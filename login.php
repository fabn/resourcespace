<?php
include "include/db.php";
include "include/general.php";
include "include/resource_functions.php";
include "include/collections_functions.php";

$url=getval("url","index.php");
$text=getvalescaped("text","");
$api=getval("api","");

# process log in
$error="";

# Auto logged out? Set error message.
if (getval("auto","")!="") {$error=str_replace("30",$session_length,$lang["sessionexpired"]);}

# Display a no-cookies message
if (getval("nocookies","")!="" && getval("cookiecheck","")=="") {$error=$lang["nocookies"];}

# First check that this IP address has not been locked out due to excessive attempts.
$ip=get_ip();
$lockouts=sql_value("select count(*) value from ip_lockout where ip='" . escape_check($ip) . "' and tries>='" . $max_login_attempts_per_ip . "' and date_add(last_try,interval " . $max_login_attempts_wait_minutes . " minute)>now()",0);

# Also check that the username provided has not been locked out due to excessive login attempts.
$ulockouts=sql_value("select count(*) value from user where username='" . getvalescaped("username","") . "' and login_tries>='" . $max_login_attempts_per_username . "' and date_add(login_last_try,interval " . $max_login_attempts_wait_minutes . " minute)>now()",0);

# Default the username to the stored username in the case of session expiry (if configured)
$stored_username="";
if ($login_remember_username && isset($_COOKIE["user"]))
	{
    $s=explode("|",$_COOKIE["user"]);
    $stored_username=$s[0];
	}

if ($lockouts>0 || $ulockouts>0)
	{
	$error=str_replace("?",$max_login_attempts_wait_minutes,$lang["max_login_attempts_exceeded"]);
	}

# Process the submitted login
elseif (array_key_exists("username",$_POST) && getval("langupdate","")=="")
    {
    $username=getvalescaped("username","");
    $password=getvalescaped("password","");
    
    if (strlen($password)==32 && getval("userkey","")!=md5($username . $scramble_key)) {exit("Invalid password.");} # Prevent MD5s being entered directly while still supporting direct entry of plain text passwords (for systems that were set up prior to MD5 password encryption was added). If a special key is sent, which is the md5 hash of the username and the secret scramble key, then allow a login using the MD5 password hash as the password. This is for the 'log in as this user' feature.
    
    if (strlen($password)!=32)
    	{
    	# Provided password is not a hash, so generate a hash.
    	$password_hash=md5("RS" . $username . $password);
    	}
    else
    	{
    	$password_hash=$password;
    	}
    	
    hook("externalauth","",array( $username, $password)); #Attempt external auth if configured

    $session_hash=md5($password_hash . $username . $password . date("Y-m-d"));
    if ($enable_remote_apis){$session_hash=md5($password_hash.$username.date("Y-m-d"));} // session hashes need to match if using api key, so password cannot be included here to avoid auto-logouts after a remote api call.
    
    $valid=sql_query("select ref,usergroup from user where username='$username' and (password='$password' or password='$password_hash')");
    
    if (count($valid)>=1)
        {
   	    # Account expiry
        $expires=sql_value("select account_expires value from user where username='$username' and password='$password'","");
        if ($expires!="" && $expires!="0000-00-00 00:00:00" && strtotime($expires)<=time())
       		{
       		$valid=0;$error=$lang["accountexpired"];
       		}
       else
       		{
		 	$expires=0;
        	if (getval("remember","")!="") {$expires=time()+(3600*24*100);} # remember login for 100 days

			# Store language cookie
			setcookie("language",getval("language",""),time()+(3600*24*1000));
			setcookie("language",getval("language",""),time()+(3600*24*1000),$baseurl_short . "pages/");

			# Update the user record. Set the password hash again in case a plain text password was provided.
			sql_query("update user set password='$password_hash',session='$session_hash',last_active=now(),login_tries=0,lang='".getval("language","")."' where username='$username' and (password='$password' or password='$password_hash')");

			# Log this
			$userref=$valid[0]["ref"];
			$usergroup=$valid[0]["usergroup"];
			daily_stat("User session",$userref);
			resource_log(0,'l',0);
			
			# Blank the IP address lockout counter for this IP
			sql_query("delete from ip_lockout where ip='" . escape_check($ip) . "'");

			# Set the session cookie.
	        setcookie("user",$username . "|" . $session_hash,$expires);
	        
	        # Set default resource types
	        setcookie("restypes",$default_res_types);

			# If the redirect URL is the collection frame, do not redirect to this as this will cause
			# the collection frame to appear full screen.
			if (strpos($url,"pages/collections.php")!==false) {$url="index.php";}

	        $accepted=sql_value("select accepted_terms value from user where username='$username' and (password='$password' or password='$password_hash')",0);
	        if ($api && $enable_remote_apis ){
				# send the cookie back to authenticate.php
				include_once('include/rest_utils.php');
				RestUtils::sendResponse(200, json_encode(array("cookie"=>$username . "|" . $session_hash,"expires"=>$expires)), 'application/json');  
				}
				
			if (($accepted==0) && ($terms_login) && !checkperm("p")) {redirect ("pages/terms.php?noredir=true&url=" . urlencode("pages/change_password.php"));} else {redirect($url);}
	        }
        }
    else
        {		
		if ($api && $enable_remote_apis ){
			include_once('include/rest_utils.php');
			RestUtils::sendResponse(200,  json_encode(array("cookie"=>"no")), 'application/json');  
		}
        $error=$lang["loginincorrect"];
        
        # Add / increment a lockout value for this IP
        $lockouts=sql_value("select count(*) value from ip_lockout where ip='" . escape_check($ip) . "' and tries<'" . $max_login_attempts_per_ip . "'","");
        
        if ($lockouts>0)
        	{
        	# Existing row with room to move
			$tries=sql_value("select tries value from ip_lockout where ip='" . escape_check($ip) . "'",0);
			$tries++;
			if ($tries==$max_login_attempts_per_ip)
				{
				# Show locked out message.
				$error=str_replace("?",$max_login_attempts_wait_minutes,$lang["max_login_attempts_exceeded"]);
				}
				
			# Increment
        	sql_query("update ip_lockout set last_try=now(),tries=tries+1 where ip='" . escape_check($ip) . "'");
        	}
        else
        	{
        	# New row
        	sql_query("delete from ip_lockout where ip='" . escape_check($ip) . "'");
        	sql_query("insert into ip_lockout (ip,tries,last_try) values ('" . escape_check($ip) . "',1,now())");
        	}
        	
        # Increment a lockout value for any matching username.
        $ulocks=sql_query("select ref,login_tries,login_last_try from user where username='$username'");
        if (count($ulocks)>0)
        	{
			$tries=$ulocks[0]["login_tries"];
			if ($tries=="") {$tries=1;} else {$tries++;}
			if ($tries>$max_login_attempts_per_username) {$tries=1;}
			if ($tries==$max_login_attempts_per_username)
				{
				# Show locked out message.
				$error=str_replace("?",$max_login_attempts_wait_minutes,$lang["max_login_attempts_exceeded"]);
				}
			sql_query("update user set login_tries='$tries',login_last_try=now() where username='$username'");
        	}
        	
        
        }
    }

if ((getval("logout","")!="") && array_key_exists("user",$_COOKIE))
    {
    #fetch username and update logged in status
    $s=explode("|",$_COOKIE["user"]);
    $username=mysql_escape_string($s[0]);
    sql_query("update user set logged_in=0,session='' where username='$username'");
        
    #blank cookie
    setcookie("user","",0);

    # Also blank search related cookies
    setcookie("search","");	
    setcookie("saved_offset","");	
    setcookie("saved_archive","");	

    #Do not show stored username.
    $stored_username="";
    
    unset($username);
    
    if (isset($anonymous_login))
    	{
    	# If the system is set up with anonymous access, redirect to the home page after logging out.
    	redirect("pages/home.php");
    	}
    }

if (getval("langupdate","")!="")
	{
	# Update language while remaining on this page.
	$language=getval("language","");
	setcookie("language",$language,time()+(3600*24*1000));
	setcookie("language",$language,time()+(3600*24*1000),$baseurl_short . "pages/");
	redirect("login.php?username=" . urlencode(getval("username","")));
	}




include "include/header.php";
?>

  <h1><?php echo text("welcomelogin")?></h1>
  <p><?php echo text(getvalescaped("text",""))?></p>
  <p>
  <?php if ($allow_account_request) { ?><a href="pages/user_request.php">&gt; <?php echo $lang["nopassword"]?> </a><?php } ?>
  <?php if ($allow_password_reset) { ?><br/><a href="pages/user_password.php">&gt; <?php echo $lang["forgottenpassword"]?></a><?php } ?>
  </p>
  
  
  <?php if ($error!="") { ?><div class="FormIncorrect"><?php echo $error?></div><?php } ?>
  <form target="_top" id="loginform" method="post" <?php if (!$login_autocomplete) { ?>AUTOCOMPLETE="OFF"<?php } ?>>
  <input type="hidden" name="langupdate" id="langupdate" value="">  
  <input type="hidden" name="url" value="<?php echo htmlspecialchars($url)?>">
		<div class="Question">
			<label for="username"><?php echo $lang["username"]?> </label>
			<input type="text" name="username" id="username" class="stdwidth" <?php if (!$login_autocomplete) { ?>AUTOCOMPLETE="OFF"<?php } ?> value="<?php echo htmlspecialchars(getval("username",$stored_username)) ?>" />
			<div class="clearerleft"> </div>
		</div>
		
		<div class="Question">
			<label for="pass"><?php echo $lang["password"]?> </label>
			<input type="password" name="password" id="password" class="stdwidth" <?php if (!$login_autocomplete) { ?>AUTOCOMPLETE="OFF"<?php } ?> />
			<div class="clearerleft"> </div>
		</div>
<?php if ($disable_languages==false) { ?>	
		<div class="Question">
			<label for="pass"><?php echo $lang["language"]?> </label>
			<select class="stdwidth" name="language" onChange="document.getElementById('langupdate').value='YES';document.getElementById('loginform').submit();">
			<?php reset ($languages); foreach ($languages as $key=>$value) { ?>
			<option value="<?php echo $key?>" <?php if ($language==$key) { ?>selected<?php } ?>><?php echo $value?></option>
			<?php } ?>
			</select>
			<div class="clearerleft"> </div>
		</div> 
<?php } ?>
	
		<?php if ($allow_keep_logged_in) { ?>
		<div class="Question">
			<label for="remember"><?php echo $lang["keepmeloggedin"]?></label>
			<input style="margin-top: 0.5em;" name="remember" id="remember" type="checkbox" value="yes" checked="checked">
			<div class="clearerleft"> </div>
		</div>
		<?php } ?>
		
		<div class="QuestionSubmit">
			<label for="buttons"> </label>			
			<input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["login"]?>&nbsp;&nbsp;" />
		</div>
	</form>
  <p>&nbsp;</p>

<?php

if ($stored_username!="")
	{
    # Javascript to default the focus to the password box
    ?>
    <script type="text/javascript">
	document.getElementById('password').focus();
    </script>
    <?php
	}
else
	{
    # Javascript to default the focus to the username box
    ?>
    <script type="text/javascript">
	document.getElementById('username').focus();
    </script>
    <?php
	}

include "include/footer.php";
?>
