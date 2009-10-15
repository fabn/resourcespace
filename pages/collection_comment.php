<?php
include "../include/db.php";
include "../include/general.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref","",true),$k))) {include "../include/authenticate.php";}
include "../include/collections_functions.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

$ref=getvalescaped("ref","",true);
$collection=getvalescaped("collection","",true);

# Fetch collection data
$cinfo=get_collection($collection);if ($cinfo===false) {exit("Collection not found.");}
$commentdata=get_collection_resource_comment($ref,$collection);
$comment=$commentdata["comment"];
$rating=$commentdata["rating"];

# Check access
if (!$cinfo["request_feedback"] && ($userref!=$cinfo["user"]) && ($cinfo["allow_changes"]!=1) && (!checkperm("h"))) {exit("Access denied.");}

if (getval("save","")!="")
	{
	# Save comment
	$comment=trim(getvalescaped("comment",""));
	$rating=trim(getvalescaped("rating",""));
	save_collection_resource_comment($ref,$collection,$comment,$rating);
	if ($k=="")
		{
		redirect ("pages/search.php?refreshcollectionframe=true&search=!collection" . $collection);
		}
	else
		{
		# Stay on this page for external access users (no access to search)
		refresh_collection_frame();
		}
	}


include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["collectioncomments"]?></h1>
<p><?php echo $lang["collectioncommentsinfo"]?></p>
<?php 
$imagepath = get_resource_path($ref,true,"col",false,"jpg");
$imageurl = get_resource_path($ref,false,"col",false,"jpg");
if (file_exists($imagepath)){?>
<div class="Question">
<label for="image"><?php echo $lang["preview"]?></label><img src="<?php echo $imageurl?>?nc=<?php echo time()?>" alt="" class="Picture" />
<div class="clearerleft"> </div>
</div>
<?php } ?>

<?php if (!hook("replacecollectioncommentform")) { ?>

<form method="post">
<input type="hidden" name="ref" value="<?php echo $ref?>">
<input type="hidden" name="k" value="<?php echo $k?>">
<input type="hidden" name="collection" value="<?php echo $collection?>">

<div class="Question">
<label for="name"><?php echo $lang["comment"]?></label><textarea class="stdwidth" style="width:450px;" rows=20 cols=80 name="comment" id="comment"><?php echo htmlspecialchars($comment)?></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="name"><?php echo $lang["rating"]?></label><select class="stdwidth" name="rating">
<option value="" <?php if ($rating=="") { ?>selected<?php } ?>></option>
<?php for ($n=1;$n<=5;$n++) { ?>
<option value="<?php echo $n?>" <?php if ($rating==$n) { ?>selected<?php } ?>><?php echo str_pad("",$n,"*")?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>

<?php if (checkperm("h") && $cinfo["theme"]!="") { ?>
<div class="Question">
<label for="use_as_theme_thumbnail"><?php echo $lang["useasthemethumbnail"]?></label>
<input name="use_as_theme_thumbnail" id="use_as_theme_thumbnail" type="checkbox" value="yes" <?php if ($commentdata["use_as_theme_thumbnail"]==1) { ?>checked<?php } ?>>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>

<?php } ?> <!--End Replacecollectioncommentform hook-->

</div>

<?php		
include "../include/footer.php";
?>