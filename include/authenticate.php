<?php
# authenticate user based on cookie
$valid=true;
$autologgedout=false;
$nocookies=false;

if (!isset($api)){$api=false;} // $api is set above inclusion of authenticate.php in remotely accessible scripts.

if ($api && $enable_remote_apis ){
	# if using API (RSS or API), send credentials to login.php, as if normally posting, to establish login
	if (getval("key","") || (getval("username","")&& getval("password",""))){ // key is provided within the website when logged in (encrypted username and password)

        if (getval("username","")&& getval("password","")){
            $u_p_array[0]=getval("username","");$u_p_array[1]=getval("password","");
        }
        else {
            $u_p_array=decrypt_api_key(getval("key",""));
        }

        if (count($u_p_array)!=2){
			$data['cookie']="no";
			} 
		else{
			$username=$u_p_array[0];
			$password=$u_p_array[1];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("api"=>true,"username"=>$username,"password"=>$password,"userkey"=>md5($username . $scramble_key)),'','&'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_URL, $baseurl.'/login.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
            curl_setopt($ch, CURLOPT_USERAGENT, "API Client"); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $responseBody = curl_exec($ch);
            $responseInfo	= curl_getinfo($ch);
            curl_close($ch);

            //fix for installs that don't json_decode curl response properly
            $data['cookie']='no';
            if (substr($responseBody,0,9)=='{"cookie"'){
                $data=explode(":",$responseBody);
                $data['cookie']=$data[1];
                $data['cookie']=explode(",",$data['cookie']);
                $data['cookie']=$data['cookie'][0];
                $data['cookie']=str_replace('"',"",$data['cookie']);
                $data['cookie']=trim(str_replace("}",'',$data['cookie']));
            }
        }
		if (substr($data['cookie'],0,2)!="no" && $data['cookie']!=''){
		$_COOKIE['user']=$data['cookie'];
		}
        else {unset($_COOKIE['user']);}

	}
}

if (array_key_exists("user",$_COOKIE) || array_key_exists("user",$_GET) || isset($anonymous_login))
    {
    if (array_key_exists("user",$_COOKIE))
    	{
	    $s=explode("|",$_COOKIE["user"]);
        $username=mysql_escape_string($s[0]);
	    $session_hash=mysql_escape_string($s[1]);
	    }
	elseif (array_key_exists("user",$_GET))
		{
	    $s=explode("|",$_GET["user"]);
        $username=mysql_escape_string($s[0]);
	    $session_hash=mysql_escape_string($s[1]);
		}
	else
		{
		$username=$anonymous_login;
		$session_hash="";
		}

	$hashsql="and u.session='$session_hash'";
	if (isset($anonymous_login) && ($username==$anonymous_login)) {$hashsql="";} # Automatic anonymous login, do not require session hash.

    $userdata=sql_query("select u.ref, u.username, g.permissions, g.fixed_theme, g.parent, u.usergroup, u.current_collection, u.last_active, timestampdiff(second,u.last_active,now()) idle_seconds,u.email, u.password, u.fullname, g.search_filter, g.edit_filter, g.ip_restrict ip_restrict_group, g.name groupname, u.ip_restrict ip_restrict_user, resource_defaults, u.password_last_change,g.config_options,g.request_mode from user u,usergroup g where u.usergroup=g.ref and u.username='$username' $hashsql and u.approved=1 and (u.account_expires is null or u.account_expires='0000-00-00 00:00:00' or u.account_expires>now())");
    if (count($userdata)>0)
        {
        $valid=true;
        $userref=$userdata[0]["ref"];
        $username=$userdata[0]["username"];
		
		# Hook to modify user permissions
		if (hook("userpermissions")){$userdata[0]["permissions"]=hook("userpermissions");} 
		
		# Create userpermissions array for checkperm() function
		$userpermissions=array_merge(explode(",",trim($global_permissions)),explode(",",trim($userdata[0]["permissions"]))); 
	
		$usergroup=$userdata[0]["usergroup"];
		$usergroupname=$userdata[0]["groupname"];
        $usergroupparent=$userdata[0]["parent"];
        $useremail=$userdata[0]["email"];
        $userpassword=$userdata[0]["password"];
        $userfullname=$userdata[0]["fullname"];
		if (!isset($userfixedtheme)) {$userfixedtheme=$userdata[0]["fixed_theme"];} # only set if not set in config.php

        $ip_restrict_group=trim($userdata[0]["ip_restrict_group"]);
        $ip_restrict_user=trim($userdata[0]["ip_restrict_user"]);
        
        $usercollection=$userdata[0]["current_collection"];
        if ($usercollection==0 || !is_numeric($usercollection))
        	{
       		# Create a collection for this user
			global $lang;
			include_once "collections_functions.php"; # Make sure collections functions are included before create_collection
			$usercollection=create_collection($userref,$lang["mycollection"],0,1);
			# set this to be the user's current collection
			sql_query("update user set current_collection='$usercollection' where ref='$userref'");
        	}
        
        $usersearchfilter=$userdata[0]["search_filter"];
        $usereditfilter=$userdata[0]["edit_filter"];
        $userresourcedefaults=$userdata[0]["resource_defaults"];
        $userrequestmode=trim($userdata[0]["request_mode"]);
    	
        # Some alternative language choices for basket mode / e-commerce
        if ($userrequestmode==2 || $userrequestmode==3)
			{
			$lang["addtocollection"]=$lang["addtobasket"];
			$lang["action-addtocollection"]=$lang["addtobasket"];
			$lang["addtocurrentcollection"]=$lang["addtobasket"];
			$lang["requestaddedtocollection"]=$lang["buyitemaddedtocollection"];
			$lang["request"]=$lang["addtobasket"];
			
			# The request button (renamed "Buy" by the line above) should always add the item to the current collection.
			$request_adds_to_collection=true;
			}        
    
	
        # Apply config override options
        $config_options=trim($userdata[0]["config_options"]);
        if ($config_options!="") {eval($config_options);}
        

        if ($password_expiry>0 && !checkperm("p") && $allow_password_change && $pagename!="change_password" && $pagename!="index" && $pagename!="collections" && strlen(trim($userdata[0]["password_last_change"]))>0)
        	{
        	# Redirect the user to the password change page if their password has expired.
	        $last_password_change=time()-strtotime($userdata[0]["password_last_change"]);
			if ($last_password_change>($password_expiry*60*60*24))
				{
				redirect("pages/change_password.php?expired=true");
				}
        	}
        
        if (strlen(trim($userdata[0]["last_active"]))>0)
        	{
	        if ($userdata[0]["idle_seconds"]>($session_length*60))
	        	{
          	    # Last active more than $session_length mins ago?
				$al="";if (isset($anonymous_login)) {$al=$anonymous_login;}
				
				if ($session_autologout && $username!=$al) # If auto logout enabled, but this is not the anonymous user, log them out.
					{
					# Reached the end of valid session time, auto log out the user.
					
					# Remove session
					sql_query("update user set logged_in=0,session='' where ref='$userref'");
			
					# Blank cookie / var
					setcookie("user","",0);
					unset($username);
		
					if (isset($anonymous_login))
						{
						# If the system is set up with anonymous access, redirect to the home page after logging out.
						redirect("pages/home.php");
						}
					else
						{
						$valid=false;
						$autologgedout=true;
						}
					}
				else
	        		{
		        	# Session end reached, but the user may still remain logged in.
			        # This is a new 'session' for the purposes of statistics.
					daily_stat("User session",$userref);
					}
				}
			}
        }
        else {$valid=false;}
    }
else
    {
    $valid=false;
    $nocookies=true;
    
    # Set a cookie that we'll check for again on the login page after the redirection.
    # If this cookie is missing, it's assumed that cookies are switched off or blocked and a warning message is displayed.
    setcookie("cookiecheck","true",0,'/');
    }

if (!$valid && !$api)
    {
	$_SERVER['REQUEST_URI'] = ( isset($_SERVER['REQUEST_URI']) ?
	$_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER
	['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')));
    $path=$_SERVER["REQUEST_URI"];
	?>
	<script type="text/javascript">
	top.location.href="<?php echo $baseurl?>/login.php?url=<?php echo urlencode($path)?><?php if ($autologgedout) { ?>&auto=true<?php } ?><?php if ($nocookies) { ?>&nocookies=true<?php } ?>";
	</script>
	<?php
    exit();
    }
if (!$valid && $api){echo "invalid login";exit();}    

# Handle IP address restrictions
$ip=get_ip();
$ip_restrict=$ip_restrict_group;
if ($ip_restrict_user!="") {$ip_restrict=$ip_restrict_user;} # User IP restriction overrides the group-wide setting.
if ($ip_restrict!="")
	{
	# Allow multiple IP addresses to be entered, comma separated.
	$i=explode(",",$ip_restrict);
	$allow=false;

	# Loop through all provided ranges
	for ($n=0;$n<count($i);$n++)
		{
		$ip_restrict=trim($i[$n]);
		
		# Match against the IP restriction.
		$wildcard=strpos($ip_restrict,"*");

		if ($wildcard!==false)
			{
			# Wildcard
			if (substr($ip,0,$wildcard)==substr($ip_restrict,0,$wildcard)) {$allow=true;}
			}
		else
			{
			# No wildcard, straight match
			if ($ip==$ip_restrict) {$allow=true;}
			}
		}

	if (!$allow)
		{
		header("HTTP/1.0 403 Access Denied");
		exit("Access denied.");
		}
	}

#update activity table
global $pagename;
$terms="";if (($pagename!="login") && ($pagename!="terms")) {$terms=",accepted_terms=1";} # Accepted terms
if (!$api){$last_browser=mysql_escape_string(substr($_SERVER["HTTP_USER_AGENT"],0,250));}
else {$last_browser="API Client";}
sql_query("update user set last_active=now(),logged_in=1,last_ip='" . get_ip() . "',last_browser='" . $last_browser . "'$terms where ref='$userref'");

# Add group specific text (if any) when logged in.
if (hook("replacesitetextloader"))
	{
	# this hook expects $site_text to be modified and returned by the plugin	 
	$site_text=hook("replacesitetextloader");
	}
else
	{
	if (isset($usergroup))
		{
		$results=sql_query("select language,name,text from site_text where (page='$pagename' or page='all') and specific_to_group='$usergroup'");
		for ($n=0;$n<count($results);$n++) {$site_text[$results[$n]["language"] . "-" . $results[$n]["name"]]=$results[$n]["text"];}
		}
	}	/* end replacesitetextloader */

# Load group specific plugins
$active_plugins = (sql_query("SELECT name,enabled_groups FROM plugins WHERE inst_version>=0 AND length(enabled_groups)>0"));
foreach($active_plugins as $plugin)
	{
	# Check group access, only enable for global access at this point
	$s=explode(",",$plugin['enabled_groups']);
	if (in_array($usergroup,$s))
		{
		register_plugin($plugin['name']);
		}
	}


