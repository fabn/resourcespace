<?php

/**
 * Performs the login using the global $username, and $password. Since the "externalauth" hook
 * is allowed to change the credentials later on, the $password_hash needs to be global as well.
 *
 * @return array Containing the login details ('valid' determines whether or not the login succeeded).
 */
function perform_login()
	{
	global $api, $scramble_key, $enable_remote_apis, $lang, $max_login_attempts_wait_minutes, $max_login_attempts_per_ip, $max_login_attempts_per_username, $global_cookies, $username, $password, $password_hash;
	
    if (!$api && strlen($password)==32 && getval("userkey","")!=md5($username . $scramble_key))
		{
		exit("Invalid password."); # Prevent MD5s being entered directly while still supporting direct entry of plain text passwords (for systems that were set up prior to MD5 password encryption was added). If a special key is sent, which is the md5 hash of the username and the secret scramble key, then allow a login using the MD5 password hash as the password. This is for the 'log in as this user' feature.
		}

	if (strlen($password)!=32)
		{
		# Provided password is not a hash, so generate a hash.
		$password_hash=md5("RS" . $username . $password);
		}
	else
		{
		$password_hash=$password;
		}

	$ip=get_ip();

	# This may change the $username, $password, and $password_hash
    hook("externalauth","",array($username, $password)); #Attempt external auth if configured

	$session_hash=md5($password_hash . $username . $password . date("Y-m-d"));
	if ($enable_remote_apis){$session_hash=md5($password_hash.$username.date("Y-m-d"));} // no longer necessary to omit password in this hash for api support

	$valid=sql_query("select ref,usergroup from user where lower(username)='".escape_check($username)."' and (password='".escape_check($password)."' or password='".escape_check($password_hash)."')");

	# Prepare result array
	$result=array();
	$result['valid']=false;

	if (count($valid)>=1)
		{
		# Account expiry
		$expires=sql_value("select account_expires value from user where username='".escape_check($username)."' and password='".escape_check($password)."'","");
		if ($expires!="" && $expires!="0000-00-00 00:00:00" && strtotime($expires)<=time())
			{
			$result['error']=$lang["accountexpired"];
			return $result;
			}

		$result['valid']=true;
		$result['session_hash']=$session_hash;
		$result['password_hash']=$password_hash;

		# Update the user record. Set the password hash again in case a plain text password was provided.
		sql_query("update user set password='".escape_check($password_hash)."',session='".escape_check($session_hash)."',last_active=now(),login_tries=0,lang='".getvalescaped("language","")."' where lower(username)='".escape_check($username)."' and (password='".escape_check($password)."' or password='".escape_check($password_hash)."')");

		# Log this
		$userref=$valid[0]["ref"];
		$usergroup=$valid[0]["usergroup"];
		daily_stat("User session",$userref);
		sql_query("insert into resource_log(date,user,resource,type) values (now()," . (($userref!="")?"'$userref'":"null") . ",0,'l')");

		# Blank the IP address lockout counter for this IP
		sql_query("delete from ip_lockout where ip='" . escape_check($ip) . "'");

		return $result;
		}

	# Invalid login
	$result['error']=$lang["loginincorrect"];

  hook("loginincorrect");

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
			$result['error']=str_replace("?",$max_login_attempts_wait_minutes,$lang["max_login_attempts_exceeded"]);
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
	$ulocks=sql_query("select ref,login_tries,login_last_try from user where username='".escape_check($username)."'");
	if (count($ulocks)>0)
		{
		$tries=$ulocks[0]["login_tries"];
		if ($tries=="") {$tries=1;} else {$tries++;}
		if ($tries>$max_login_attempts_per_username) {$tries=1;}
		if ($tries==$max_login_attempts_per_username)
			{
			# Show locked out message.
			$result['error']=str_replace("?",$max_login_attempts_wait_minutes,$lang["max_login_attempts_exceeded"]);
			}
		sql_query("update user set login_tries='$tries',login_last_try=now() where username='$username'");
		}

	return $result;
	}

function build_user_cookie($username, $session_hash)
	{
	return $username . '|' . $session_hash;
	}
?>
