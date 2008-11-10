<?
include "../include/db.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection",""),$k))) {include "../include/authenticate.php";}
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

$collection=getvalescaped("collection","");
$errors="";
$done=false;

# Fetch collection data
$cinfo=get_collection($collection);if ($cinfo===false) {exit("Collection not found.");}

# Check access
if (!$cinfo["request_feedback"]) {exit("Access denied.");}

# Check that comments have been added.
$comments=get_collection_comments($collection);
if (count($comments)==0 && $feedback_resource_select==false) {$errors=$lang["feedbacknocomments"];}

if (getval("save","")!="")
	{
	# Save comment
	$comment=trim(getvalescaped("comment",""));
	send_collection_feedback($collection,$comment);

	# Stay on this page for external access users (no access to search)
	refresh_collection_frame();
	$done=true;
	}

include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["sendfeedback"]?></h1>
<? if ($done) { ?><p><?=$lang["feedbacksent"]?></p><? } else { ?>

<form method="post">
<input type="hidden" name="k" value="<?=$k?>">
<input type="hidden" name="collection" value="<?=$collection?>">

<? if ($feedback_resource_select)
	{
	?><h2><?=$lang["selectedresources"]?>:</h2><?
	# Show thumbnails and allow the user to select resources.
	$result=do_search("!collection" . $collection);
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		?>	
		<!--Resource Panel-->
		<div class="ResourcePanelShell" id="ResourceShell<?=$ref?>">
		<div class="ResourcePanel">
		
		<table border="0" class="ResourceAlign<? if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideo<? } ?>"><tr><td>
		<? if ($result[$n]["has_image"]==1) { ?><img width="<?=$result[$n]["thumb_width"]?>" height="<?=$result[$n]["thumb_height"]?>" src="<?=get_resource_path($ref,false,"thm",false,$result[$n]["preview_extension"],-1,1,checkperm("w"),$result[$n]["file_modified"])?>" class="ImageBorder"
		<? } else { ?>		<img border=0 src="../gfx/type<?=$result[$n]["resource_type"]?>.gif" /><? } ?>
		</td>
		</tr></table>
		<span class="ResourceSelect"><input type="checkbox" name="select_<?=$ref?>" value="yes"></span>

		<div class="ResourcePanelInfo"><?=htmlspecialchars(tidy_trim (i18n_get_translated ($result[$n]["title"]),32))?>&nbsp;</div>
			
		<div class="clearer"> </div>
		</div>
		<div class="PanelShadow"></div>
		</div>
		
		<?
		}
	?><div class="clearer"> </div> <?
	}
?>

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
include "../include/footer.php";
?>