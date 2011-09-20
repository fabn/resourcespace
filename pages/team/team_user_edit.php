<?php
/**
 * User edit form display page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../include/general.php";

$ref=getvalescaped("ref","",true);

if ((getval("save","")!="") || (getval("suggest","")!=""))
	{
	# Save user data
	$result=save_user($ref);
	if ($result===false)
		{
		$error=$lang["useralreadyexists"];
		}
	elseif ($result!==true)
		{
		$error=$result;
		}
	else
		{
		if (getval("save","")!="") {$backurl=getval("backurl","team_user.php?nc=" . time());redirect ("pages/team/" . $backurl);}
		}
	}

# Fetch user data
$user=get_user($ref);
if (($user["usergroup"]==3) && ($usergroup!=3)) {exit("Permission denied.");}

include "../../include/header.php";


# Log in as this user?
if (getval("loginas","")!="")
	{
	# Log in as this user
	# A user key must be generated to enable login using the MD5 hash as the password.
	?>
	<form method="post" action="../../login.php" id="autologin" target="_top">
	<input type="hidden" name="username" value="<?php echo $user["username"]?>">
	<input type="hidden" name="password" value="<?php echo $user["password"]?>">
	<input type="hidden" name="userkey" value="<?php echo md5($user["username"] . $scramble_key)?>">
	<noscript><input type="submit" value="<?php echo $lang["login"]?>"></noscript>
	</form>
	<script type="text/javascript">
	document.getElementById("autologin").submit();
	</script>
	<?php
	exit();
	}

?>
<div class="BasicsBox">
<h1><?php echo $lang["edituser"]?></h1>
<?php if (isset($error)) { ?><div class="FormError">!! <?php echo $error?> !!</div><?php } ?>

<form method=post>
<input type=hidden name=ref value="<?php echo $ref?>">
<input type=hidden name=backurl value="<?php echo getval("backurl","team_user.php?nc=" . time())?>">

<div class="Question"><label><?php echo $lang["username"]?></label><input name="username" type="text" class="stdwidth" value="<?php echo $user["username"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["password"]?></label><input name="password" type="text" class="stdwidth" value="<?php echo (strlen($user["password"])==32)?$lang["hidden"]:$user["password"]?>">&nbsp;<input type=submit name="suggest" value="<?php echo $lang["suggest"]?>" /><div class="clearerleft"> </div></div>

<?php if (!hook("replacefullname")){?>
<div class="Question"><label><?php echo $lang["fullname"]?></label><input name="fullname" type="text" class="stdwidth" value="<?php echo $user["fullname"]?>"><div class="clearerleft"> </div></div>
<?php } ?>

<div class="Question"><label><?php echo $lang["group"]?></label>
<select class="stdwidth" name="usergroup">
<?php $groups=get_usergroups(true);
for ($n=0;$n<count($groups);$n++)
	{
	if (($groups[$n]["ref"]==3) && ($usergroup!=3))
		{
		#Do not show
		}
	else
		{
		?>
		<option value="<?php echo $groups[$n]["ref"]?>" <?php if ($user["usergroup"]==$groups[$n]["ref"]) {?>selected<?php } ?>><?php echo $groups[$n]["name"]?></option>	
		<?php
		}
	}
?>
</select>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["emailaddress"]?></label><input name="email" type="text" class="stdwidth" value="<?php echo $user["email"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["accountexpiresoptional"]?><br/><?php echo $lang["format"]?>: YYYY-MM-DD</label><input name="account_expires" type="text" class="stdwidth" value="<?php echo $user["account_expires"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["ipaddressrestriction"]?><br/><?php echo $lang["wildcardpermittedeg"]?> 194.128.*</label><input name="ip_restrict" type="text" class="stdwidth" value="<?php echo $user["ip_restrict"]?>"><div class="clearerleft"> </div></div>

<?php hook("additionaluserfields");?>

<div class="Question"><label><?php echo $lang["comments"]?></label><textarea name="comments" class="stdwidth" rows=5 cols=50><?php echo htmlspecialchars($user["comments"])?></textarea><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["created"]?></label>
<div class="Fixed"><?php echo nicedate($user["created"],true) ?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["lastactive"]?></label>
<div class="Fixed"><?php echo nicedate($user["last_active"],true) ?></div>
<div class="clearerleft"> </div></div>


<div class="Question"><label><?php echo $lang["lastbrowser"]?></label>
<div class="Fixed"><?php echo resolve_user_agent($user["last_browser"],true)?></div>
<div class="clearerleft"> </div></div>




<?php 
# Only allow sending of password when this is not an MD5 string (i.e. only when first created or 'Suggest' is used).
?>
<div class="Question"><label><?php echo $lang["ticktoemail"]?></label>
<?php if (strlen($user["password"])!=32) { ?>
<input name="emailme" type="checkbox" value="yes" <?php if ($user["approved"]==0) { ?>checked<?php } ?>>
<?php } else { ?>
<div class="Fixed"><?php echo $lang["cannotemailpassword"]?></div>
<?php } ?>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["approved"]?></label><input name="approved" type="checkbox"  value="yes" <?php if ($user["approved"]==1) { ?>checked<?php } ?>>
<?php if ($user["approved"]==0) { ?><div class="FormError">!! <?php echo $lang["ticktoapproveuser"]?> !!</div><?php } ?>

<div class="clearerleft"> </div></div>



<div class="Question"><label><?php echo $lang["ticktodelete"]?></label><input name="deleteme" type="checkbox"  value="yes"><div class="clearerleft"> </div></div>

<?php if ($user["approved"]==1) { ?>
<div class="Question"><label><?php echo $lang["login"]?></label>
<div class="Fixed"><a href="team_user_edit.php?ref=<?php echo $ref?>&loginas=true">&gt;&nbsp;<?php echo $lang["clicktologinasthisuser"]?></a></div>
<div class="clearerleft"> </div></div>
<?php } ?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>
