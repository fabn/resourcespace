<?
# authenticate user based on cookie
$valid=true;

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
		$basic_simple_search=true; # Always use the basic simple search for anonymous users to save screen space (the login box will appear on the right hand side).
		}

	$hashsql="and u.session='$session_hash'";
	if (isset($anonymous_login) && ($username==$anonymous_login)) {$hashsql="";} # Automatic anonymous login, do not require session hash.

    $userdata=sql_query("select u.ref,u.username,g.permissions,g.fixed_theme,g.parent,u.usergroup,u.current_collection,u.last_active,u.email,u.password,u.fullname,g.search_filter, g.ip_restrict ip_restrict_group, u.ip_restrict ip_restrict_user, resource_defaults, u.password_last_change,g.config_options from user u,usergroup g where u.usergroup=g.ref and u.username='$username' $hashsql and (u.account_expires is null or u.account_expires='0000-00-00 00:00:00' or u.account_expires>now())");
    if (count($userdata)>0)
        {
        $valid=true;
        $userref=$userdata[0]["ref"];
        $username=$userdata[0]["username"];
        $userpermissions=split(",",$userdata[0]["permissions"]); #create userpermissions array for checkperm() function
        $usergroup=$userdata[0]["usergroup"];
        $usergroupparent=$userdata[0]["parent"];
        $useremail=$userdata[0]["email"];
        $userpassword=$userdata[0]["password"];
        $userfullname=$userdata[0]["fullname"];
        if (!isset($userfixedtheme)) {$userfixedtheme=$userdata[0]["fixed_theme"];} # only set if not set in config.php
        
        $ip_restrict_group=trim($userdata[0]["ip_restrict_group"]);
        $ip_restrict_user=trim($userdata[0]["ip_restrict_user"]);
        
        $usercollection=$userdata[0]["current_collection"];
        $usersearchfilter=$userdata[0]["search_filter"];
        $userresourcedefaults=$userdata[0]["resource_defaults"];
        
        # Apply config override options
        $config_options=trim($userdata[0]["config_options"]);
        if ($config_options!="") {eval($config_options);}
        
        if ($password_expiry>0 && $pagename!="change_password" && strlen(trim($userdata[0]["password_last_change"]))>0)
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
	        $last_active=time()-strtotime($userdata[0]["last_active"]);
	        if ($last_active>($session_length*60))
	        	{
          	    # Last active more than $session_length mins ago?
				$al="";if (isset($anonymous_login)) {$al=$anonymous_login;}
				
				if ($session_autologout && $username!=$al) # If auto logout enabled, but this is not the anonymous user, log them out.
					{
					# Reached the end of valid session time, auto log out the user.
					
					# Remove session
					sql_query("update user set logged_in=0,session='' where ref='$userref'");
			
					# Blank cookie / var
					setcookie("user","",0,$baseurl_short);
					unset($username);
		
					if (isset($anonymous_login))
						{
						# If the system is set up with anonymous access, redirect to the home page after logging out.
						redirect("pages/home.php");
						}
					else
						{
						redirect("pages/login.php");
						}
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
        else {$valid=false;}
    }
else
    {
    $valid=false;
    }
  
if (!$valid)
    {
	$_SERVER['REQUEST_URI'] = ( isset($_SERVER['REQUEST_URI']) ?
	$_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER
	['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')));
    $path=$_SERVER["REQUEST_URI"];
	?>
	<script>
	top.location.href="<?=$baseurl?>/pages/login.php?url=<?=urlencode($path)?>";
	</script>
	<?
    exit();
    }

# Handle IP address restrictions
$ip=get_ip();
$ip_restrict=$ip_restrict_group;
if ($ip_restrict_user!="") {$ip_restrict=$ip_restrict_user;} # User IP restriction overrides the group-wide setting.
if ($ip_restrict!="")
	{
	# Match against the IP restriction.
	$wildcard=strpos($ip_restrict,"*");
	$allow=true;
	if ($wildcard!==false)
		{
		# Wildcard
		if (substr($ip,0,$wildcard)!=substr($ip_restrict,0,$wildcard)) {$allow=false;}
		}
	else
		{
		# No wildcard, straight match
		if ($ip!=$ip_restrict) {$allow=false;}
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
sql_query("update user set last_active=now(),logged_in=1,last_ip='" . get_ip() . "',last_browser='" . mysql_escape_string(substr($_SERVER["HTTP_USER_AGENT"],0,100)) . "'$terms where ref='$userref'");

# Add group specific text (if any) when logged in.
if (isset($usergroup))
	{
	$results=sql_query("select language,name,text from site_text where (page='$pagename' or page='all') and specific_to_group='$usergroup'");
	for ($n=0;$n<count($results);$n++) {$site_text[$results[$n]["language"] . "-" . $results[$n]["name"]]=$results[$n]["text"];}
	}



?>
