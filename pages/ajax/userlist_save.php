<?php
# Feeder page for AJAX user/group search for the user selection include file

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";

$user=getvalescaped("user","");
$userstring=getvalescaped("userstring","");
$userlistname=getvalescaped("userlistname","");
$delete=getval("delete","");

if ($delete!=""){
	$userlistref=getvalescaped("userlistref","");
	sql_query("delete from user_userlist where ref='$userlistref'");
}

if ($userstring!="" && $userstring!=$lang['typeauserlistname'] && $userlistname!=""){

sql_query("delete from user_userlist where user=$user and userlist_name='$userlistname'");
sql_query("insert into user_userlist (user,userlist_name,userlist_string) values ($user,'$userlistname','$userstring')");

}


