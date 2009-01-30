<?php
error_reporting(1);
if (file_exists("../config.php"))
	die(1);
if ($_REQUEST['mysqlserver']==''){
	echo '202';
	exit();
}
if ($_REQUEST['mysqlusername']==''){
	echo '201';
	exit();
}
if ((isset($_REQUEST['mysqlserver']))&&(isset($_REQUEST['mysqlusername']))&&(isset($_REQUEST['mysqlpassword']))){
	if (mysql_connect(filter_var($_REQUEST['mysqlserver'],FILTER_SANITIZE_STRING),filter_var($_REQUEST['mysqlusername'],FILTER_SANITIZE_STRING),filter_var($_REQUEST['mysqlpassword'],FILTER_SANITIZE_STRING))==FALSE){
		if(mysql_errno()==1045){
			echo '201';
		}
		else {
			echo '202';
		}
	}
	else{
		if(mysql_select_db(filter_var($_REQUEST['mysqldb']))){
			echo '200';
		}
		else {
			echo '203';
		}
	}
	
}
?>	
	