<?php
/**
 * User purge form display page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../include/general.php";

$months=getval("months","");
if ($months!="")
	{
	if (!is_numeric($months) || $months<0) {$error=$lang["pleaseenteravalidnumber"];}
	else
		{
		$condition="(created is null or created<date_sub(now(), interval $months month)) and 
						  (last_active is null or last_active<date_sub(now(), interval $months month))";
		$count=sql_value("select count(*) value from user where $condition",0);
		}
	}
	
if (isset($condition) && getval("purge2","")!="")
	{
	sql_query("delete from user where $condition");
	redirect("pages/team/team_user.php");
	}

include "../../include/header.php";

?>
<div class="BasicsBox">
<h1><?php echo $lang["purgeusers"]?></h1>
<?php if (isset($error)) { ?><div class="FormError">!! <?php echo $error?> !!</div><?php } ?>

<form method=post action="team_user_purge.php">

<?php if (isset($count) && $count==0) { ?>

<p><?php echo $lang["purgeusersnousers"] ?></p>

<?php } elseif (isset($count)) { ?>

<p><?php echo str_replace("%",$count,$lang["purgeusersconfirm"]) ?>
<br /><br />
<input type="hidden" name="months" value="<?php echo $months ?>">
<input name="purge2" type="submit" value="&nbsp;&nbsp;<?php echo $lang["purge"]?>&nbsp;&nbsp;" />
</p>
<?php $users=sql_query("select * from user where $condition"); ?>
<table class="InfoTable">
	<tr>
		<td><strong><?php echo $lang["username"] ?></strong></td>
		<td><strong><?php echo $lang["fullname"] ?></strong></td>
		<td><strong><?php echo $lang["email"] ?></strong></td>
		<td><strong><?php echo $lang["created"] ?></strong></td>
		<td><strong><?php echo $lang["lastactive"] ?></strong></td>
	</tr>
<?php foreach ($users as $user) 
	{
	?><tr>
		<td><?php echo $user["username"] ?></td>
		<td><?php echo $user["fullname"] ?></td>
		<td><?php echo $user["email"] ?></td>
		<td><?php echo nicedate($user["created"]) ?></td>
		<td><?php echo nicedate($user["last_active"]) ?></td>
	</tr><?php
	}
?>
</table>


<?php } else { ?>

<p><?php echo str_replace("%","<input type=text name=months value=12>",$lang["purgeuserscommand"]) ?>
<br /><br />
<input name="purge1" type="submit" value="&nbsp;&nbsp;<?php echo $lang["purge"]?>&nbsp;&nbsp;" />
</p>
<?php } ?>


</form>
</div>

<?php		
include "../../include/footer.php";
?>
