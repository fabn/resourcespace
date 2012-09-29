<?php

function HookPosixldapauthAllExternalauth($uname, $pword)
{
	include_once "include/collections_functions.php";

	include_once "plugins/posixldapauth/config/config.default.php";
	if (file_exists("plugins/posixldapauth/config/config.php"))
	{
        	include_once("plugins/posixldapauth/config/config.php");
	}
	include_once "plugins/posixldapauth/hooks/ldap_class.php";
	global $username;
	global $password;
	global $password_hash,$use_plugins_manager,$ldapauth;
	$debugMode = false;
        
	if ($use_plugins_manager==true)
	{
		$ldapauth = get_plugin_config("posixldapauth");

		if ($ldapauth==null || $ldapauth['enable']==false) 
		{
			return false;
		}
	}
		
	if ($uname != "" && $pword != "") 
	{
		// pass the config to the class
		$ldapConf['host'] = $ldapauth['ldapserver'];
		$ldapConf['basedn'] = $ldapauth['basedn'];
		$objLdapAuth = new ldapAuth($ldapConf);	
		// connect to the ldap
		if ($objLdapAuth->connect())
		{
			
			// see if we can bind with the username and password.
			if($objLdapAuth->auth($uname,$pword,$ldapauth['ldaptype'],$ldapauth['ldapusercontainer']))
			{
				if ($debugMode)
				{
					echo "all.php: line 43 : auth to ldap server is successful \r\n";
				}
				$auth = true;
				// get the user info etc	
				$userDetails = $objLdapAuth->getUserDetails($uname);
				//print_r($userDetails);
				if ($debugMode)
				{
					echo "all.php: line 50 : cn=" . $userDetails["cn"] . "\r\n";
					echo "all.php: line 50 : dn=" . $userDetails["dn"] . "\r\n"; 	
				}
				
				$user_cn = $userDetails["cn"];
				$user_dn = $userDetails["dn"];
				
				/* 	Now we have the user details, we need to figure out if the user exists in the 
					RS database allready, in which case we'll update the passsword, or if it's
					a new user and create users is set, then we create a new user.
					
					Maybe w should also check groups as well? So if group membership has changed the user will be updated!
				*/
				
				$uexists=sql_query('select ref from user where username="'.$uname.$ldapauth['usersuffix'].'"');
				if (count($uexists)>=1) 
				{
					// if we get here, the user has already been added to RS.
					$username=$uname.$ldapauth['usersuffix'];
					$password_hash= md5("RS".$username.$password);
					sql_query('update user set password="'.$password_hash.'" where username="'.$username.'"');
					//          $password=sql_value('select password value from user where username="'.$uname.$ldapauth['usersuffix'].'"',"");
					return true;
				}
				elseif ($ldapauth['createusers']) 
				{
					
					// else, is we have specified to create users from the LDAP, we need to get info about the user
					// to add them to resource space.
					$nuser = array();
					// Start Populating User Fields from LDAP
					$nuser['username']=$uname.$ldapauth['usersuffix'];
					$nuser['fullname']=$user_cn;
					if (isset($userDetails["mail"]))
					{
						$nuser['email']=$userDetails["mail"];
					} else {
						$nuser['email']="$uname@mail";
					}
					$nuser['password']=md5("RS". $nuser['username'].$password);
					
					// Set a var so that we can keep track of the group level as we scan the access groups.
					$currentGroupLevel = 0;
					
				
					
					if ($ldapauth['groupbased'])
					{
						//echo "group based";
						// set match to false as default"
						$match = false;						
						/* 	At this point we want to do a switch on the type of directory we are authenticing against
							so that we can use group matching for the different types of directory layout:
							ie, AD uses memberof, OD doesn't!
							We also need to check for higher numbered groups, ie if a user is amember of staff, and of admin users,
							we need to give them the highest access!
						*/
						//switch ($ldapauth['ldaptype'])
						//{
						//	case 0:
								// Open Directory!
								// set the uid, ie the username...
								$objLdapAuth->userName = $uname;
									
								// now we cycle through the config array to check groups!
								foreach ($ldapauth['groupmap'] as $ldapGrpName => $arrLdapGrp)
								{
									// check to see if we are allowing users in this group to log in?
									if ($arrLdapGrp['enabled'])
									{
										// get the group name and check group membership	
										if ($objLdapAuth->checkGroupByName($ldapGrpName,$ldapauth['ldaptype']))
										{
											if ( $match )
											{
												if ($currentGroupLevel < $arrLdapGrp['rsGroup'])
												{
													$nuser['usergroup'] = $arrLdapGrp['rsGroup'];
													$currentGroupLevel = $arrLdapGrp['rsGroup'];
												}
											} else {	
												$match = true;
											
												$nuser['usergroup'] = $arrLdapGrp['rsGroup'];
												$currentGroupLevel = $arrLdapGrp['rsGroup'];
											} 
										}
									}	
								}
								//break;
							//case 1:
								// Active Directory - memberof?
								/* These are the steps we need to take:
								1. Connect
								2. Bind using the supplied credentials - or maybe we don't as the user will have bound!
								3. Get the users info and check 'member of' field
								4. compare to enabled groups.
								*/
						/*		
								break;
							case 2:
								// Novell 
								break;
						}*/
						
						// if we haven't managed to find a group match that is allowed to log into RS, then
						// we return false!	- we ned to modify this to use the group set if group based is not enabled!
						if (!($match)) return false;
						// Create the user
						$ref=new_user($nuser['username']);
						if (!$ref) return false; # Shouldn't ever get here.  Something strange happened
						
						// Update with information from LDAP
						sql_query('update user set password="'.$nuser['password'].
							'", fullname="'.$nuser['fullname'].'", email="'.$nuser['email'].'", usergroup="'.
							$nuser['usergroup'].'", comments="Auto create from LDAP" where ref="'.$ref.'"');
							
						$username=$nuser['username'];
						$password=$nuser['password'];


						// now unbind
						$objLdapAuth->unBind();	
						return true;
					}
				}				
			} else {					
				// username / password is wrong!
				return false;
			}		
		}	
		return false;		
	}
}
