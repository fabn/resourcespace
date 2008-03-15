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
		}

	$hashsql="and u.session='$session_hash'";
	if (isset($anonymous_login) && ($username==$anonymous_login)) {$hashsql="";} # Automatic anonymous login, do not require session hash.
	
    $userdata=sql_query("select u.ref,u.username,g.permissions,g.fixed_theme,g.parent,u.usergroup,u.current_collection,u.last_active,u.email,u.password,u.fullname,g.search_filter from user u,usergroup g where u.usergroup=g.ref and u.username='$username' $hashsql and (u.account_expires is null or u.account_expires='0000-00-00 00:00:00' or u.account_expires>now())");
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
        
        $usercollection=$userdata[0]["current_collection"];
        $usersearchfilter=$userdata[0]["search_filter"];
        
        if (strlen(trim($userdata[0]["last_active"]))>0)
        	{
	        $last_active=time()-strtotime($userdata[0]["last_active"]);
	        if ($last_active>(30*60)) # Last active more than 30 mins ago? This is a new session.
	        	{
	        	#Log this
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

    $path=$_SERVER["REQUEST_URI"];
    $path=explode("/",$path);
    $path=$path[count($path)-1];
	?>
	<script>
	top.location.href="<?=$baseurl?>/login.php?url=<?=urlencode($path)?>";
	</script>
	<?
    exit();
    }

#update activity table
global $pagename;
$terms="";if (($pagename!="login") && ($pagename!="terms")) {$terms=",accepted_terms=1";} # Accepted terms
sql_query("update user set last_active=now(),logged_in=1,last_ip='" . $_SERVER["REMOTE_ADDR"] . "',last_browser='" . mysql_escape_string(substr($_SERVER["HTTP_USER_AGENT"],0,100)) . "'$terms where ref='$userref'");

# Add group specific text (if any) when logged in.
if (isset($usergroup))
	{
	$results=sql_query("select language,name,text from site_text where (page='$pagename' or page='all') and specific_to_group='$usergroup'");
	for ($n=0;$n<count($results);$n++) {$site_text[$results[$n]["language"] . "-" . $results[$n]["name"]]=$results[$n]["text"];}
	}

?>
