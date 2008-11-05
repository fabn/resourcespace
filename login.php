<?
include "include/db.php";
include "include/general.php";

$url=getval("url","index.php");

# process log in
$error="";

# First check that this IP address has not been locked out due to excessive attempts.
$ip=get_ip();
$lockouts=sql_value("select count(*) value from ip_lockout where ip='" . escape_check($ip) . "' and tries>='" . $max_login_attempts_per_ip . "' and date_add(last_try,interval " . $max_login_attempts_wait_minutes . " minute)>now()",0);

# Also check that the username provided has not been locked out due to excessive login attempts.
$ulockouts=sql_value("select count(*) value from user where username='" . getvalescaped("username","") . "' and login_tries>='" . $max_login_attempts_per_username . "' and date_add(login_last_try,interval " . $max_login_attempts_wait_minutes . " minute)>now()",0);

if ($lockouts>0 || $ulockouts>0)
	{
	$error=str_replace("?",$max_login_attempts_wait_minutes,$lang["max_login_attempts_exceeded"]);
	}

# Process the submitted login
elseif (array_key_exists("username",$_POST))
    {
    $username=getvalescaped("username","");
    $password=getvalescaped("password","");
    
    if (strlen($password)==32) {exit("Invalid password.");} # Prevent MD5s being entered directly.
    
    $password_hash=md5("RS" . $username . $password);
    $session_hash=md5($password_hash . $username . $password . date("Y-m-d"));
    
    $valid=sql_query("select ref from user where username='$username' and (password='$password' or password='$password_hash')");
    
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

			# Update the user record. Set the password hash again in case a plain text password was provided.
			sql_query("update user set password='$password_hash',session='$session_hash',last_active=now(),login_tries=0 where username='$username' and (password='$password' or password='$password_hash')");

			# Log this
			daily_stat("User session",$valid[0]["ref"]);

			# Blank the IP address lockout counter for this IP
			sql_query("delete from ip_lockout where ip='" . escape_check($ip) . "'");

			# Set the session cookie.
	        setcookie("user",$username . "|" . $session_hash,$expires);
	        
	        # Set default resource types
	        setcookie("restypes",$default_res_types);

	        $accepted=sql_value("select accepted_terms value from user where username='$username' and (password='$password' or password='$password_hash')",0);
	        if (($accepted==0) && ($terms_login) && !checkperm("p")) {redirect ("pages/terms.php?url=" . urlencode("pages/change_password.php"));} else {redirect($url);}
	        }
        }
    else
        {
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
    
    unset($username);
    
    if (isset($anonymous_login))
    	{
    	# If the system is set up with anonymous access, redirect to the home page after logging out.
    	redirect("pages/home.php");
    	}
    }

include "include/header.php";
?>

  <h1><?=text("welcomelogin")?></h1>
  <p><? if ($allow_account_request) { ?><a href="user_request.php">&gt; <?=$lang["nopassword"]?> </a><br/><? } ?>
  <a href="user_password.php">&gt; <?=$lang["forgottenpassword"]?></a></p>
  <? if ($error!="") { ?><div class="FormIncorrect"><?=$error?></div><? } ?>
  <form id="form1" method="post" <? if (!$login_autocomplete) { ?>AUTOCOMPLETE="OFF"<? } ?>>
  <input type=hidden name=url value="<?=$url?>">
		<div class="Question">
			<label for="name"><?=$lang["username"]?> </label>
			<input type="text" name="username" id="name" class="stdwidth" <? if (!$login_autocomplete) { ?>AUTOCOMPLETE="OFF"<? } ?> />
			<div class="clearerleft"> </div>
		</div>
		
		<div class="Question">
			<label for="pass"><?=$lang["password"]?> </label>
			<input type="password" name="password" id="name" class="stdwidth" <? if (!$login_autocomplete) { ?>AUTOCOMPLETE="OFF"<? } ?> />
			<div class="clearerleft"> </div>
		</div>
<? if ($disable_languages==false) { ?>	
		<div class="Question">
			<label for="pass"><?=$lang["language"]?> </label>
			<select class="stdwidth" name="language">
			<? reset ($languages); foreach ($languages as $key=>$value) { ?>
			<option value="<?=$key?>" <? if ($language==$key) { ?>selected<? } ?>><?=$value?></option>
			<? } ?>
			</select>
			<div class="clearerleft"> </div>
		</div> 
<? } ?>
	
		<? if ($allow_keep_logged_in) { ?>
		<div class="Question">
			<label for="remember"><?=$lang["keepmeloggedin"]?></label>
			<input valign=bottom name="remember" id="remember" type="checkbox" value="yes" checked>
			<div class="clearerleft"> </div>
		</div>
		<? } ?>
		
		<div class="QuestionSubmit">
			<label for="buttons"> </label>			
			<input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["login"]?>&nbsp;&nbsp;" />
		</div>
	</form>
  <p>&nbsp;</p>

<?
include "include/footer.php";
?>
