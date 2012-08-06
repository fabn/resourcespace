<?php
# update the user select element

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
include "../../include/resource_functions.php";

$userstring=getvalescaped("userstring","");
?>

<?php $user_userlists=sql_query("select * from user_userlist where user='".escape_check($userref)."'");?>

<option value=""><?php echo $lang['loadasaveduserlist']?></option>
<?php
if (count($user_userlists)>0){

foreach ($user_userlists as $user_userlist){?>
	<option id="<?php echo $user_userlist['ref']?>" value="<?php echo $user_userlist['userlist_string']?>" <?php if ($userstring==$user_userlist['userlist_string']){?>selected<?php } ?>><?php echo $user_userlist['userlist_name']?></option>
<?php } 
 
} ?>

