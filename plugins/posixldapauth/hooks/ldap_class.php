<?php
/**
* This class is used to Authenticate and get details from an LDAP server, specifically Apple OSX 10.5 Server<br>
* @version 0.1.0
* @author D.J.White <djwhite@mac.com>
* @copyright 2009 D.J.White
* @package SysProfUtils
*/
/**
* Main LDAP Auth Class
*/
class ldapAuth
{
	/**
	* @var array stores the ldap config
	*/
	private $ldapconfig = array();
	/**
	* @var string stores the connection details
	*/
	public $ldapconn;		//Connection		
	/**
	* @var string Search Result
	*/
	public $r;				// Search Result;
	/**
	* @var string User Name
	*/
	public $userName;		// username
	/**
	* @var string User Password
	*/
	public $ldappass;		// password
	/**
	* @var string unique RDN
	*/
	public $ldaprdn;		// rdn;
	
	/** 	
	* This constructs the object, and gets the config
	* @access public
	*/
	function __construct($ldapconfig)
	{
		//include ("config.php");
		$this->ldapconfig = $ldapconfig;
	}
	
	/** 	
	* Attempts to connect to the LDAP Server
	* Returns 1 if successful, 0 on failure
	* @return bool
	* @access public
	*/
	function connect()
	{
		$this->ldapconn = ldap_connect($this->ldapconfig['host'])
	    	or die("Could not connect to LDAP server.");
		ldap_set_option($this->ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		return 1;
	}
	
	
	/** 	
	* unbinds from the LDAP Server
	* @access public
	*/
	function unBind()
	{
		ldap_unbind($this->ldapconn);	
	}
	
	/** 	
	* This attempts to authenticate the user against the LDAP<br>
	* Returns 1 if successful, 0 on failure
	* @param string username Required: User Name
	* @param string pass Required: User's Password
	* @return bool
	* @access public
	*/
	function auth($username,$pass,$ldapType,$userContainer)
	{
		$this->userName = $username;
		$this->ldappass = $pass;
		
		if ($ldapType == 1)
		{
			// Active Directory, format is user@domain
			$this->ldaprdn = $username;// ."@".$userContainer;	
			
		} else {
			// OD - requires full DN.
			$this->ldaprdn = "uid=" . $this->userName  .",".$userContainer  .",". $this->ldapconfig['basedn'];
		}
		
		if (@ldap_bind($this->ldapconn, $this->ldaprdn, $this->ldappass)) {
			// now check if this is AD, and if so, set the DN correctly!
			if ($ldapType == 1)
			{
				// get the shortname from the username (ie user@domain becomes user)
				$usercn = stristr($username,"@",true);
				// set the search filter * attributes we want
				$filter="(samaccountname=".$usercn.")";
				$attributes=array("dn","cn");
				
				// search
				if (!($search = ldap_search($this->ldapconn, $this->ldapconfig['basedn'], $filter,$attributes))) {
				     die("Unable to search ldap server");
				}	
				// get the info
				$number_returned = ldap_count_entries($this->ldapconn, $search);
				$info = ldap_get_entries($this->ldapconn, $search);
				
				// set the rdn
				$this->ldaprdn = $info[0]["dn"];
			}
	        return 1;
	    } else {
	        return 0;
	    }
		
	}
	
	
	
	/** 	
	* This gets the basic info for a user from the LDAP<br>
	* Returns an array of the user details, or empty if the user is not found.
	* @param string username Required: username
	* @return array
	* @access public
	*/
	function getUserDetails($username)
	{
		$this->userName = $username;
		
		// Removed the RDN Setup as this should be done by the  auth function... ie we check auth before we get user details!
		//$this->ldaprdn = "cn=users," . $this->ldapconfig['basedn'];
		//$this->ldaprdn = "uid=" . $this->userName . ",cn=users," . $this->ldapconfig['basedn'];
		//$filter = "(cn=".$this->userName.")";
		$filter="(objectclass=*)";
		$retArr = array("sn", "givenname", "mail","cn");
		//echo $this->ldaprdn;
		$res = ldap_search($this->ldapconn,$this->ldaprdn,$filter,$retArr);
		$info = ldap_get_entries($this->ldapconn, $res);
		//print_r( $info );
		$retVar = array();
		
		// build the return values
		if (isset($info[0]['mail'][0])) { $retVar['mail'] = $info[0]['mail'][0]; }
		if (isset($info[0]['sn'][0])) { $retVar['sn'] = $info[0]['sn'][0]; }
		if (isset($info[0]['cn'][0])) { $retVar['cn'] = $info[0]['cn'][0]; }
		if (isset($info[0]['givenname'][0])) { $retVar['givenname'] = $info[0]['givenname'][0]; }
		if (isset($info[0]['dn'])) { $retVar['dn'] = $info[0]['dn']; }
		
		return $retVar;
	}
	
	/** 	
	* This checks to see if the user is an a particular group by gidNumber<br>
	* Returns 1 if successful, 0 on failure
	* @param string groupId Required: gidNumber
	* @return bool
	* @access public
	*/
	function checkGroup($groupId)
	{
		//echo $this->ldapconfig['basedn'];		
		$found = false;
		
	
		$dn = "cn=groups," . $this->ldapconfig['basedn'];
		$gid = "(gidnumber=" . $groupId . ")";
		$res = ldap_search($this->ldapconn,$dn,$gid,array("memberuid"));
		$info = ldap_get_entries($this->ldapconn, $res); 
		//print_r($info);
		$x =  count($info[0]['memberuid']) - 1;
		for ($l = 0; $l < $x; $l++)
		{
			if ($info[0]['memberuid'][$l] == $this->userName)
			{
				$found = true;
			} 
		}
		
		if ($found)
		{
			return 1;
		} else {
			return 0;
		}
	}	
	
	/** 	
	* This checks to see if the user is an a particular group by group short name<br>
	* Returns 1 if successful, 0 on failure
	* @param string groupId Required: groupName
	* @param string ldapType : Type of Directory, 1= Active Directory
	* @return bool
	* @access public
	*/
	
	function checkGroupByName($groupName, $ldapType=0)
	{
		$found = false;
		
		$gid = "(cn=" . $groupName . ")";
		
		
		// check to see what type of directory we are using, and set parameters accordingly.
		// $memField $ userField allow us to reference different variables in this class.
		// the reason for this is that AD returns the Full DN for each user, and OD returns the shortname.
		if ($ldapType == 1)
		{
			// set the parameters for AD
			$attributes = array ("member");
			$dn = $this->ldapconfig['basedn'];
			$memField = "member";
			$userField = "ldaprdn";
			
		} else {
			// Set for LDAP
			$attributes = array ("memberuid");
			$dn = "cn=groups," . $this->ldapconfig['basedn'];
			$memField = "memberuid";
			$userField = "userName";
		}
		
		// search for the group
		if (!($search = ldap_search($this->ldapconn, $this->ldapconfig['basedn'], $gid ,$attributes))) {
		     die("Unable to search ldap server");
		}
		$info = ldap_get_entries($this->ldapconn, $search);
		
		
		// cycle through the group memebers to see if we can find the user.
		foreach ($info[0][$memField] as $member)
		{
			if ($member == $this->$userField) { $found = true; }
				
		}
		
		if ($found)
		{
			return 1;
		} else {
			return 0;
		}
	}
	
	/** 	
	* This gets a list of all the groups in the LDAP
	* Returns a list of the groups that contain the group short name and the group number
	* @return array
	* @access public
	
	*/
	function listGroups($ldapType = 0,$groupContainer="",$testMode = 0)
	{
		/*this works with both LDAP and AD, and does a switch on DirectoryType
			type 
			1 = AD
			anything else uses ldap!
			
			As the AD requires authenticated binding, we expect the bind to allready have happened!
		*/
		
		
		// set the required parameters for each directory type:
		if ($ldapType == 1)
		{
			$attributes = array("cn","dn");
			$dn = $this->ldapconfig['basedn'];
			$filter = "(&(objectCategory=group))";
		} else {
			$attributes = array("cn","gidnumber");
			if ($groupContainer != "") 
			{
				error_log( " ldapauth:ldap_clas.php:listGroups() line 279 - Changed group DN to: ".$groupContainer);
				$dn = $groupContainer;
			} else {
				$dn = "cn=groups," . $this->ldapconfig['basedn'];
			}
			$filter = "cn=*";
		}
		error_log( " ldapauth:ldap_clas.php:listGroups() line 286 - ldap_search ( ". $dn . "," . $filter . ")");
		//print_r($this->ldapconfig);
		
		if (!($sr = ldap_search($this->ldapconn, $dn, $filter,$attributes)))
		{
				return ("ldap_search($this->ldapconn, $dn, $filter,$attributes)) failed, please check settings");
		}
		
		error_log( " ldapauth:ldap_clas.php:listGroups() line 292 - attempting to get entries");
		if (!$info = ldap_get_entries($this->ldapconn, $sr))
		{
			return ("ldap_get_entries($this->ldapconn, $sr)) failed");	
		}
		
		if (!$info["count"])
		{
			return ("ldap search successfuil, but 0 groups found");	
		}
		
		error_log( " ldapauth:ldap_clas.php:listGroups() line 295 - ".$info["count"]." entries returned");
		
		// create an array to return of each entry.
		for ($i=0; $i < $info["count"]; $i++) 
		{
			// map the attributes to the return array.
	    	foreach ($attributes as $rField)
	    	{
		    	if (isset($info[$i][$rField]))
		    	{
			    	$retGroups[$i][$rField] =  $info[$i][$rField][0];
		    	}
	    	}
		}
		return $retGroups;
		
	}
	
}
?>