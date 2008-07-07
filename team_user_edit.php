<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "include/general.php";

$ref=getvalescaped("ref","");

if ((getval("save","")!="") || (getval("suggest","")!=""))
	{
	# Save user data
	if (save_user($ref)===false)
		{
		$error=$lang["useralreadyexists"];
		}
	else
		{
		if (getval("save","")!="") {$backurl=getval("backurl","team_user.php?nc=" . time());redirect ($backurl);}
		}
	}

# Fetch user data
$user=get_user($ref);
if (($user["usergroup"]==3) && ($usergroup!=3)) {exit("Permission denied.");}


include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["edituser"]?></h1>
<? if (isset($error)) { ?><div class="FormError">!! <?=$error?> !!</div><? } ?>

<form method=post>
<input type=hidden name=ref value="<?=$ref?>">
<input type=hidden name=backurl value="<?=getval("backurl","team_user.php?nc=" . time())?>">

<div class="Question"><label><?=$lang["username"]?></label><input name="username" type="text" class="stdwidth" value="<?=$user["username"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["password"]?></label><input name="password" type="text" class="stdwidth" value="<?=(strlen($user["password"])==32)?$lang["hidden"]:$user["password"]?>">&nbsp;<input type=submit name="suggest" value="<?=$lang["suggest"]?>" /><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["fullname"]?></label><input name="fullname" type="text" class="stdwidth" value="<?=$user["fullname"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["group"]?></label>
<select class="stdwidth" name="usergroup">
<? $groups=get_usergroups(true);
for ($n=0;$n<count($groups);$n++)
	{
	if (($groups[$n]["ref"]==3) && ($usergroup!=3))
		{
		#Do not show
		}
	else
		{
		?>
		<option value="<?=$groups[$n]["ref"]?>" <? if ($user["usergroup"]==$groups[$n]["ref"]) {?>selected<?}?>><?=$groups[$n]["name"]?></option>	
		<?
		}
	}
?>
</select>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["emailaddress"]?></label><input name="email" type="text" class="stdwidth" value="<?=$user["email"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["accountexpiresoptional"]?><br/><?=$lang["format"]?>: YYYY-MM-DD</label><input name="account_expires" type="text" class="stdwidth" value="<?=$user["account_expires"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["ipaddressrestriction"]?><br/><?=$lang["wildcardpermittedeg"]?> 194.128.*</label><input name="ip_restrict" type="text" class="stdwidth" value="<?=$user["ip_restrict"]?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["comments"]?></label><textarea name="comments" class="stdwidth" rows=5 cols=50><?=htmlspecialchars($user["comments"])?></textarea><div class="clearerleft"> </div></div>

<? 
# Only allow sending of password when this is not an MD5 string (i.e. only when first created).
if (strlen($user["password"])!=32) { ?><div class="Question"><label><?=$lang["ticktoemail"]?></label><input name="emailme" type="checkbox" value="yes"><div class="clearerleft"> </div></div><? } ?>

<div class="Question"><label><?=$lang["ticktodelete"]?></label><input name="deleteme" type="checkbox"  value="yes"><div class="clearerleft"> </div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "include/footer.php";
?>