<?php

function ip_matches_regexp($ip, $ip_restrict)
	{
	if (substr($ip_restrict, 0, 1)!='!')
		return ip_matches($ip, $ip_restrict);

	return @preg_match('/'.substr($ip_restrict, 1).'/su', $ip);
	}

function HookAuto_loginAllProvideusercredentials()
	{
	global $username, $hashsql, $session_hash;

	if (array_key_exists("user",$_COOKIE) || array_key_exists("user",$_GET))
		return false;

	$results=sql_query('select username, ip_restrict from user where auto_login_enabled=1 and ip_restrict is not null');
	$ip=get_ip();
	foreach ($results as $result)
		{
		if (ip_matches_regexp($ip, $result['ip_restrict']))
			{
			$username=$result['username'];
			$hashsql='';
			$session_hash='';
			return true;
			}
		}

	return false;
	}

function HookAuto_loginAllIprestrict()
	{
	global $allow, $ip, $ip_restrict;
	if (substr($ip_restrict, 0, 1)!='!')
		return false;

	$allow=ip_matches_regexp($ip, $ip_restrict);
	return true;
	}

?>
