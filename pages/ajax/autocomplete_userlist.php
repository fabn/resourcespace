<?php
# Feeder page for userlist names

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";

$find=getvalescaped("userlist_parameter","  ");
$userlists=sql_query("select userlist_name from user_userlist where user=$userref and userlist_name like '%$find%'");

?>
<ul>
<?php
$users=get_users(0,$find);
for ($n=0;$n<count($userlists) && $n<=20;$n++)
	{
    ?><li><?php echo $userlists[$n]['userlist_name']?></li><?php	
	}
?>
</ul>
