<?
include "include/db.php";
include "include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "include/general.php";
include "include/collections_functions.php";
include "include/resource_functions.php";
include "include/search_functions.php";

$ref=getvalescaped("ref","");
$collection=getvalescaped("collection","");

# Fetch collection data
$cinfo=get_collection($collection);if ($cinfo===false) {exit("Collection not found.");}
$commentdata=get_collection_resource_comment($ref,$collection);
$comment=$commentdata["comment"];
$rating=$commentdata["rating"];

# Check access
if (($userref!=$cinfo["user"]) && ($cinfo["allow_changes"]!=1) && (!checkperm("h"))) {exit("Access denied.");}

if (getval("save","")!="")
	{
	# Save comment
	$comment=trim(getvalescaped("comment",""));
	$rating=trim(getvalescaped("rating",""));
	save_collection_resource_comment($ref,$collection,$comment,$rating);
	redirect ("search.php?refreshcollectionframe=true&search=!collection" . $collection);
	}


include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["collectioncomments"]?></h1>
<p><?=$lang["collectioncommentsinfo"]?></p>
<form method="post">
<input type="hidden" name="ref" value="<?=$ref?>">
<input type="hidden" name="collection" value="<?=$collection?>">

<div class="Question">
<label for="name"><?=$lang["comment"]?></label><textarea class="stdwidth" style="width:450px;" rows=20 cols=80 name="comment" id="comment"><?=htmlspecialchars($comment)?></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="name"><?=$lang["rating"]?></label><select class="stdwidth" name="rating">
<option value="" <? if ($rating=="") { ?>selected<? } ?>></option>
<? for ($n=1;$n<=5;$n++) { ?>
<option value="<?=$n?>" <? if ($rating==$n) { ?>selected<? } ?>><?=str_pad("",$n,"*")?></option>
<? } ?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "include/footer.php";
?>