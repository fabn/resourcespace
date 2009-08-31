<?php
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

$ref=getvalescaped("ref","");
$copycollectionremoveall=getvalescaped("copycollectionremoveall","");

# Fetch collection data
$collection=get_collection($ref);if ($collection===false) {exit("Collection not found.");}


# Collection copy functionality
$copy=getval("copy","");
if ($copy!="")
	{
	copy_collection($copy,$ref,$copycollectionremoveall!="");
	refresh_collection_frame();
	}

if (getval("name","")!="")
	{
	# Save collection data
	save_collection($ref);
	if (getval("redirect","")!="")
		{
		if ((getval("theme","")!="") || (getval("newtheme","")!=""))
			{
			redirect ("pages/themes.php?manage=true");
			}
		else
			{
			redirect ("pages/collection_manage.php?reload=true");
			}
		}
	else
		{
		# No redirect, we stay on this page. Reload the collection info.
		$collection=get_collection($ref);
		}
	refresh_collection_frame();
	}

	
include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["editcollection"]?></h1>
<p><?php echo text("introtext")?></p>
<form method=post id="collectionform">
<input type=hidden name=redirect id=redirect value=yes>
<input type=hidden name=ref value="<?php echo $ref?>">

<div class="Question">
<label for="name"><?php echo $lang["name"]?></label><input type=text class="stdwidth" name="name" id="name" value="<?php echo $collection["name"]?>" maxlength="100" <?php if ($collection["cant_delete"]==1) { ?>readonly=true<?php } ?>>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="keywords"><?php echo $lang["relatedkeywords"]?></label><textarea class="stdwidth" rows="3" name="keywords" id="keywords" <?php if ($collection["cant_delete"]==1) { ?>readonly=true<?php } ?>><?php echo htmlspecialchars($collection["keywords"])?></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["id"]?></label><div class="Fixed"><?php echo $collection["ref"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="public"><?php echo $lang["access"]?></label>
<?php if ($collection["cant_delete"]==1) { 
# This is a user's My Collection, which cannot be made public or turned into a theme. Display a warning.
?>
<input type="hidden" id="public" name="public" value="0">
<div class="Fixed"><?php echo $lang["mycollection_notpublic"] ?></div>
<?php } else { ?>
<select id="public" name="public" class="shrtwidth" onchange="document.getElementById('redirect').value='';document.getElementById('collectionform').submit();">
<option value="0" <?php if ($collection["public"]!=1) {?>selected<?php } ?>><?php echo $lang["private"]?></option>
<?php if ($collection["cant_delete"]!=1 && ($enable_public_collections || checkperm("h"))) { ?><option value="1" <?php if ($collection["public"]==1) {?>selected<?php } ?>><?php echo $lang["public"]?></option><?php } ?>
</select>
<?php } ?>
<div class="clearerleft"> </div>
</div>

<?php if ($collection["public"]==0) { ?>
<?php if (!hook("replaceuserselect")){?>
<div class="Question">
<label for="users"><?php echo $lang["attachedusers"]?></label><?php $userstring=htmlspecialchars($collection["users"]); include "../include/user_select.php"; ?>
<div class="clearerleft"> </div>
</div>
<?php } /* end hook replaceuserselect */?>

<?php } else { 
if (checkperm("h") && $enable_themes) { # Only users with the 'h' permission can publish public collections as themes.

# Theme category level 1
?>
<div class="Question">
<label for="theme"><?php echo $lang["themecategory"] . (($theme_category_levels>1)?"1":"")?></label>
<select class="stdwidth" name="theme" id="theme" <?php if ($theme_category_levels>1) { ?>onchange="document.getElementById('redirect').value='';document.getElementById('collectionform').submit();"<?php } ?>><option value=""><?php echo $lang["select"]?></option>
<?php $themes=get_theme_headers(); for ($n=0;$n<count($themes);$n++) { ?>
<option <?php if ($collection["theme"]==$themes[$n]) { ?>selected<?php } ?>><?php echo $themes[$n]?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
<label><?php echo $lang["newcategoryname"]?></label>
<input type=text class=stdwidth name="newtheme" id="newtheme" value="" maxlength="100"><br/>
<div class="clearerleft"> </div>
</div>
<?php 

# Theme category level 2
if ($theme_category_levels>=2)
	{
	?>
	<div class="Question">
	<label for="theme2"><?php echo $lang["themecategory"] . " 2" ?></label>
	<select class="stdwidth" name="theme2" id="theme2" <?php if ($theme_category_levels>2) { ?>onchange="document.getElementById('redirect').value='';document.getElementById('collectionform').submit();"<?php } ?>><option value=""><?php echo $lang["select"]?></option>
	<?php $themes=get_theme_headers($collection["theme"]); for ($n=0;$n<count($themes);$n++) { ?>
	<option <?php if ($collection["theme2"]==$themes[$n]) { ?>selected<?php } ?>><?php echo $themes[$n]?></option>
	<?php } ?>
	</select>
	<div class="clearerleft"> </div>
	<label><?php echo $lang["newcategoryname"]?></label>
	<input type=text class=stdwidth name="newtheme2" id="newtheme2" value="" maxlength="100"><br/>
	<div class="clearerleft"> </div>
	</div>
	<?php
	}

# Theme category level 3
if ($theme_category_levels>=3)
	{
	?>
	<div class="Question">
	<label for="theme3"><?php echo $lang["themecategory"] . " 3"?></label>
	<select class="stdwidth" name="theme3" id="theme3"><option value=""><?php echo $lang["select"]?></option>
	<?php $themes=get_theme_headers($collection["theme"],$collection["theme2"]); for ($n=0;$n<count($themes);$n++) { ?>
	<option <?php if ($collection["theme3"]==$themes[$n]) { ?>selected<?php } ?>><?php echo $themes[$n]?></option>
	<?php } ?>
	</select>
	<div class="clearerleft"> </div>
	<label><?php echo $lang["newcategoryname"]?></label>
	<input type=text class=stdwidth name="newtheme3" id="newtheme3" value="" maxlength="100"><br/>
	<div class="clearerleft"> </div>
	</div>
	<?php
	}

} else {
?>
<input type=hidden name="theme" value="<?php echo $collection["theme"]?>">
<?php
} 
}?>
<div class="Question">
<label for="allow_changes"><?php echo $lang["allowothersaddremove"]?></label><input type=checkbox id="allow_changes" name="allow_changes" <?php if ($collection["allow_changes"]==1) { ?>checked<?php } ?>>
<div class="clearerleft"> </div>
</div>

<?php if (checkperm("e0") || checkperm("e1") || checkperm("e2")) { ?>
<div class="Question">
<label for="allow_changes"><?php echo $lang["relateallresources"]?></label><input type=checkbox id="relateall" name="relateall">
<div class="clearerleft"> </div>
</div><?php } ?>

<div class="Question">
<label for="removeall"><?php echo $lang["removeallresourcesfromcollection"]?></label><input type=checkbox id="removeall" name="removeall">
<div class="clearerleft"> </div>
</div>

<?php if (checkperm("e0") || checkperm("e1") || checkperm("e2")) { ?>
<div class="Question">
<label for="deleteall"><?php echo $lang["deleteallresourcesfromcollection"]?></label><input type=checkbox id="deleteall" name="deleteall" onClick="if (this.checked) {return confirm('<?php echo $lang["deleteallsure"]?>');}">
<div class="clearerleft"> </div>
</div><?php } ?>

<?php
if ($enable_collection_copy) 
	{
	?>
	<div class="Question">
	<label for="copy"><?php echo $lang["copyfromcollection"]?></label>
	<select name="copy" id="copy" class="stdwidth" onChange="
	var ccra =document.getElementById('copycollectionremoveall');
	if ($('copy').value!=''){ccra.style.display='block';}
	else{ccra.style.display='none';}">
	<option value=""><?php echo $lang["donotcopycollection"]?></option>
	<?php
	$list=get_user_collections($userref);
	for ($n=0;$n<count($list);$n++)
		{
		if ($ref!=$list[$n]["ref"]){?><option value="<?php echo $list[$n]["ref"]?>"><?php echo htmlspecialchars($list[$n]["name"])?></option> <?php }
		}
	?>
	</select>
	<div class="clearerleft"> </div>
	</div>
<div class="Question" id="copycollectionremoveall" style="display:none;">
<label for="copycollectionremoveall"><?php echo $lang["copycollectionremoveall"]?></label><input type=checkbox id="copycollectionremoveall" name="copycollectionremoveall" value="yes">
<div class="clearerleft"> </div>
</div>

<?php } ?>

<?php if (checkperm("e0") || checkperm("e1") || checkperm("e2")) { ?>
<!-- Archive Status -->
<div class="Question">
<label for="archive"><?php echo $lang["resetarchivestatus"]?></label>
<select class="stdwidth" name="archive" id="archive">
<option value=""><?php echo $lang["select"]?></option>
<?php for ($n=-2;$n<=2;$n++) { ?>
<?php if (checkperm("e" . $n)) { ?><option value="<?php echo $n?>"><?php echo $lang["status" . $n]?></option><?php } ?>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>
<div class="Question">
<label for="archive"><?php echo $lang["editallresources"]?></label>
<div class="Fixed">
<?php if (allow_multi_edit($ref)) { ?>
<a href="edit.php?collection=<?php echo $ref?>"><?php echo $lang["editresources"]?> &gt;</a>
<?php } else { ?><?php echo $lang["multieditnotallowed"]?><?php } ?></div>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="Question">
<label><?php echo $lang["collectionlog"]?></label>
<div class="Fixed">
<a href="collection_log.php?ref=<?php echo $ref?>"><?php echo $lang["log"]?> &gt;</a>
</div>
<div class="clearerleft"> </div>
</div>

<?php if (file_exists("plugins/collection_edit.php")) { include "plugins/collection_edit.php"; } ?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../include/footer.php";
?>
