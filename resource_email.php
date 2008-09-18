<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("g") && !checkperm("v")) {exit ("Permission denied.");} # Cannot e-mail if can't see hi-res images. To avoid loophole whereby users could email resources to an external address, and hence download hi-res versions.
include "include/general.php";
include "include/resource_functions.php";

$ref=getvalescaped("ref","");
# Fetch resource data
$resource=get_resource_data($ref);if ($resource===false) {exit("Resource not found.");}

$errors="";
if (getval("save","")!="")
	{
	# Email resource
	# Build a new list and insert
	$users=getvalescaped("users","");
	$message=getvalescaped("message","");
	$errors=email_resource($ref,$resource["title"],$userfullname,$users,$message);
	if ($errors=="")
		{
		# Log this			
		daily_stat("E-mailed resource",$ref);

		redirect("done.php?text=resource_email");
		}
	}

include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["emailresource"]?></h1>

<p><?=text("introtext")?></p>

<form method=post id="resourceform">
<input type=hidden name=ref value="<?=$ref?>">

<div class="Question">
<label><?=$lang["resourcetitle"]?></label><div class="Fixed"><?=$resource["title"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?=$lang["resourceid"]?></label><div class="Fixed"><?=$resource["ref"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="message"><?=$lang["message"]?></label><textarea class="stdwidth" rows=6 cols=50 name="message" id="message"></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="users"><?=$lang["emailtousers"]?></label><? include "include/user_select.php"; ?>
<div class="clearerleft"> </div>
<? if ($errors!="") { ?><div class="FormError">!! <?=$errors?> !!</div><? } ?>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["emailresource"]?>&nbsp;&nbsp;" />
</div>

</form>
</div>

<?		
include "include/footer.php";
?>