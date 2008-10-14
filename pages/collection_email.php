<?
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";

$ref=getvalescaped("ref","");
# Fetch collection data
$collection=get_collection($ref);if ($collection===false) {exit("Collection not found.");}

$errors="";
if (getval("save","")!="")
	{
	# Email / share collection
	# Build a new list and insert
	$users=getvalescaped("users","");
	$message=getvalescaped("message","");
	$feedback=getvalescaped("request_feedback","");	if ($feedback=="") {$feedback=false;} else {$feedback=true;}
	$errors=email_collection($ref,$collection["name"],$userfullname,$users,$message,$feedback);
	if ($errors=="")
		{
		# Log this			
		daily_stat("E-mailed collection",$ref);
		redirect("pages/done.php?text=collection_email");
		}
	}

include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["emailcollection"]?></h1>

<p><?=text("introtext")?></p>

<form method=post id="collectionform">
<input type=hidden name=redirect id=redirect value=yes>
<input type=hidden name=ref value="<?=$ref?>">

<div class="Question">
<label><?=$lang["collectionname"]?></label><div class="Fixed"><?=$collection["name"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?=$lang["collectionid"]?></label><div class="Fixed"><?=$collection["ref"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="message"><?=$lang["message"]?></label><textarea class="stdwidth" rows=6 cols=50 name="message" id="message"></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="users"><?=$lang["emailtousers"]?></label><? $userstring=getval("users","");include "../include/user_select.php"; ?>
<div class="clearerleft"> </div>
<? if ($errors!="") { ?><div class="FormError">!! <?=$errors?> !!</div><? } ?>
</div>

<? if ($collection["user"]==$userref) { # Collection owner can request feedback.
?>
<div class="Question">
<label for="request_feedback"><?=$lang["requestfeedback"]?></label><input type=checkbox id="request_feedback" name="request_feedback" value="yes">
<div class="clearerleft"> </div>
</div>
<? } ?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["emailcollection"]?>&nbsp;&nbsp;" />
</div>

</form>
</div>

<?		
include "../include/footer.php";
?>