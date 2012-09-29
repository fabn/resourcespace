<?php
 // we get a list of groups from the LDAP;
 include_once ("../hooks/ldap_class.php");
  
$ldapConf['host'] = $_GET['server'];
$ldapConf['basedn'] = $_GET['basedn'];
	
$objLDAP = new ldapAuth($ldapConf);

$returnMessage = array();
$errmsg = false;
$status = true;

if ($objLDAP->connect())
{
	$returnMessage['Connection Test'] = "Passed";
	// we need to check for the kind of LDAP we are talking to here!
	if ($ldapauth['ldaptype'] == 1 )
	{
		// we need to bind!
		if (!$objLDAP->auth($ldapauth['rootdn'],$ldapauth['rootpass'],1,$ldapauth['addomain']))
		{
			$returnMessage["auth"] = "Could not bind to AD, please check credentials";
			$errmsg = true;
			$status = false;
		} else {
			$returnMessage["AD Bind"] = "Passed";
		}
	}
	
	if (!$errmsg)
	{
		// get the groups
		error_log( " ldapauth:setup.php line 94 GOT TO THE GROUP SELECT ");
		$ldapGroupList = $objLDAP->listGroups($_GET['type'],$_GET['groupcont']);
		if (is_array($ldapGroupList)) 
		{
			$returnMessage["Group check"] = "Passed";
			
		} else {
			$returnMessage["Group check"] = $ldapGroupList;
			$status = false;
		}
	}
	
			
} else {
	$returnMessage['Connection Test'] =  "Connection to LDAP Server failed";	
	$status = false;
}
if ($status)
{
	$returnMessage['Status'] = "Tests passed, please save your settings and then return to set group mapping.";	
} else {
	$returnMessage['Status'] = "Tests failed, please check your settings and test again.";
}

print_r($returnMessage);


?>