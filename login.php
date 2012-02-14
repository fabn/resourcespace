<?php
include "include/db.php";
include "include/general.php";
include "include/resource_functions.php";
include "include/collections_functions.php";
include "include/login_functions.php";

$url=getval("url","index.php");
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

	$result=perform_login();
	if ($result['valid'])
		{
	 	$expires=0;
       	if (getval("remember","")!="") {$expires=time()+(3600*24*100);} # remember login for 100 days

		# Store language cookie
		if ($global_cookies)
			{
			setcookie("language",getval("language",""),time()+(3600*24*1000),"/");
            }
		else
			{
			setcookie("language",getval("language",""),time()+(3600*24*1000));
			setcookie("language",getval("language",""),time()+(3600*24*1000),$baseurl_short . "pages/");
            }

		# Set the session cookie.
		if ($global_cookies){$cookie_path="/";setcookie("user","",1);} 
		else {$cookie_path="";setcookie("user","",1,"/");}
			
		setcookie("user",$username . "|" . $result['session_hash'],$expires,$cookie_path);

        # Set default resource types
        setcookie("restypes",$default_res_types);

		# If the redirect URL is the collection frame, do not redirect to this as this will cause
		# the collection frame to appear full screen.
		if (strpos($url,"pages/collections.php")!==false) {$url="index.php";}

        $accepted=sql_value("select accepted_terms value from user where username='$username' and (password='$password' or password='".$result['password_hash']."')",0);
		if (($accepted==0) && ($terms_login) && !checkperm("p")) {redirect ("pages/terms.php?noredir=true&url=" . urlencode("pages/change_password.php"));} else {redirect($url);}
        }
    else
        {
		$error=$result['error'];
                hook("dispcreateacct");
        }
    }

if ((getval("logout","")!="") && array_key_exists("user",$_COOKIE))
    {
    #fetch username and update logged in status
    $s=explode("|",$_COOKIE["user"]);
    $username=escape_check($s[0]);
    sql_query("update user set logged_in=0,session='' where username='$username'");
        
    #blank cookie
    if ($global_cookies){
        setcookie("user","",0,"/");
    }
    else {
        setcookie("user","",0);
        }

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
    if ($global_cookies){
        setcookie("language",getval("language",""),time()+(3600*24*1000),"/");
    }
    else {
        setcookie("language",getval("language",""),time()+(3600*24*1000));
        setcookie("language",getval("language",""),time()+(3600*24*1000),$baseurl_short . "pages/");
        }
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
