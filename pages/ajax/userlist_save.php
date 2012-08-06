<?php
# Feeder page for AJAX user/group search for the user selection include file

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";

$user=getvalescaped("userref","");
$userstring=getvalescaped("userstring","");
$userlistname=getvalescaped("userlistname","");
$delete=getvalescaped("delete","");

if ($delete!=""){
	$userlistref=getvalescaped("userlistref","",true);
	sql_query("delete from user_userlist where ref='".escape_check($userlistref)."'");
}

if ($userstring!="" && $userstring!=$lang['typeauserlistname'] && $userlistname!=""){

sql_query("delete from user_userlist where user=".escape_check($user)." and userlist_name='".escape_check($userlistname)."'");
sql_query("insert into user_userlist (user,userlist_name,userlist_string) values ('".escape_check($user)."','".escape_check($userlistname)."','".escape_check($userstring)."')");

}


