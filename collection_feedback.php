<?
include "include/db.php";
include "include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection",""),$k))) {include "include/authenticate.php";}
include "include/general.php";
include "include/resource_functions.php";
include "include/search_functions.php";

$collection=getvalescaped("collection","");
$errors="";
$done=false;

# Fetch collection data
$cinfo=get_collection($collection);if ($cinfo===false) {exit("Collection not found.");}

# Check access
if (!$cinfo["request_feedback"]) {exit("Access denied.");}

# Check that comments have been added.
$comments=get_collection_comments($collection);
if (count($comments)==0) {$errors=$lang["feedbacknocomments"];}

if (getval("save","")!="")
	{
	# Save comment
	$comment=trim(getvalescaped("comment",""));
	send_collection_feedback($collection,$comment);

	# Stay on this page for external access users (no access to search)
	refresh_collection_frame();
	$done=true;
	}

include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["sendfeedback"]?></h1>
<? if ($done) { ?><p><?=$lang["feedbacksent"]?></p><? } else { ?>

<form method="post">
<input type="hidden" name="k" value="<?=$k?>">
<input type="hidden" name="collection" value="<?=$collection?>">

<div class="Question">
<? if ($errors!="") { ?><div class="FormError">!! <?=$errors?> !!</div><? } ?>
<label for="name"><?=$lang["message"]?></label><textarea class="stdwidth" style="width:450px;" rows=20 cols=80 name="comment" id="comment"></textarea>
<div class="clearerleft"> </div>
</div>


<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["send"]?>&nbsp;&nbsp;" />
</div>
</form>
<? } ?>
</div>

<?		
include "include/footer.php";
?>