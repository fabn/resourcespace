<?
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
	refresh_collection_frame();
	}

	
include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["editcollection"]?></h1>
<p><?=text("introtext")?></p>
<form method=post id="collectionform">
<input type=hidden name=redirect id=redirect value=yes>
<input type=hidden name=ref value="<?=$ref?>">

<div class="Question">
<label for="name"><?=$lang["name"]?></label><input type=text class="stdwidth" name="name" id="name" value="<?=$collection["name"]?>" maxlength="100" <? if ($collection["cant_delete"]==1) { ?>readonly=true<? } ?>>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?=$lang["id"]?></label><div class="Fixed"><?=$collection["ref"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="public"><?=$lang["access"]?></label><select id="public" name="public" class="shrtwidth" onchange="document.getElementById('redirect').value='';document.getElementById('collectionform').submit();">
<option value="0" <? if ($collection["public"]==0) {?>selected<?}?>><?=$lang["private"]?></option>
<? if ($enable_public_collections || checkperm("h")) { ?><option value="1" <? if ($collection["public"]==1) {?>selected<?}?>><?=$lang["public"]?></option><? } ?>
</select>
<div class="clearerleft"> </div>
</div>

<? if ($collection["public"]==0) { ?>
<div class="Question">
<label for="users"><?=$lang["attachedusers"]?></label><? $userstring=htmlspecialchars($collection["users"]); include "../include/user_select.php"; ?>
<div class="clearerleft"> </div>
</div>

<? } else { 
if (checkperm("h") && $enable_themes) { # Only users with the 'h' permission can publish public collections as themes.

# Theme category level 1
?>
<div class="Question">
<label for="theme"><?=$lang["themecategory"] . (($theme_category_levels>1)?"1":"")?></label>
<select class="stdwidth" name="theme" id="theme" <? if ($theme_category_levels>1) { ?>onchange="document.getElementById('redirect').value='';document.getElementById('collectionform').submit();"<? } ?>><option value=""><?=$lang["select"]?></option>
<? $themes=get_theme_headers(); for ($n=0;$n<count($themes);$n++) { ?>
<option <? if ($collection["theme"]==$themes[$n]) { ?>selected<? } ?>><?=$themes[$n]?></option>
<? } ?>
</select>
<div class="clearerleft"> </div>
<label><?=$lang["newcategoryname"]?></label>
<input type=text class=stdwidth name="newtheme" id="newtheme" value="" maxlength="100"><br/>
<div class="clearerleft"> </div>
</div>
<? 

# Theme category level 2
if ($theme_category_levels>=2)
	{
	?>
	<div class="Question">
	<label for="theme2"><?=$lang["themecategory"] . " 2" ?></label>
	<select class="stdwidth" name="theme2" id="theme2" <? if ($theme_category_levels>2) { ?>onchange="document.getElementById('redirect').value='';document.getElementById('collectionform').submit();"<? } ?>><option value=""><?=$lang["select"]?></option>
	<? $themes=get_theme_headers($collection["theme"]); for ($n=0;$n<count($themes);$n++) { ?>
	<option <? if ($collection["theme2"]==$themes[$n]) { ?>selected<? } ?>><?=$themes[$n]?></option>
	<? } ?>
	</select>
	<div class="clearerleft"> </div>
	<label><?=$lang["newcategoryname"]?></label>
	<input type=text class=stdwidth name="newtheme2" id="newtheme2" value="" maxlength="100"><br/>
	<div class="clearerleft"> </div>
	</div>
	<?
	}

# Theme category level 3
if ($theme_category_levels>=3)
	{
	?>
	<div class="Question">
	<label for="theme3"><?=$lang["themecategory"] . " 3"?></label>
	<select class="stdwidth" name="theme3" id="theme3"><option value=""><?=$lang["select"]?></option>
	<? $themes=get_theme_headers($collection["theme"],$collection["theme2"]); for ($n=0;$n<count($themes);$n++) { ?>
	<option <? if ($collection["theme3"]==$themes[$n]) { ?>selected<? } ?>><?=$themes[$n]?></option>
	<? } ?>
	</select>
	<div class="clearerleft"> </div>
	<label><?=$lang["newcategoryname"]?></label>
	<input type=text class=stdwidth name="newtheme3" id="newtheme3" value="" maxlength="100"><br/>
	<div class="clearerleft"> </div>
	</div>
	<?
	}

} else {
?>
<input type=hidden name="theme" value="<?=$collection["theme"]?>">
<?
} 
}?>
<div class="Question">
<label for="allow_changes"><?=$lang["allowothersaddremove"]?></label><input type=checkbox id="allow_changes" name="allow_changes" <? if ($collection["allow_changes"]==1) { ?>checked<? } ?>>
<div class="clearerleft"> </div>
</div>

<? if (checkperm("e0") || checkperm("e1") || checkperm("e2")) { ?>
<div class="Question">
<label for="allow_changes"><?=$lang["relateallresources"]?></label><input type=checkbox id="relateall" name="relateall">
<div class="clearerleft"> </div>
</div><? } ?>

<div class="Question">
<label for="removeall"><?=$lang["removeallresourcesfromcollection"]?></label><input type=checkbox id="removeall" name="removeall">
<div class="clearerleft"> </div>
</div>

<? if (checkperm("e0") || checkperm("e1") || checkperm("e2")) { ?>
<div class="Question">
<label for="deleteall"><?=$lang["deleteallresourcesfromcollection"]?></label><input type=checkbox id="deleteall" name="deleteall" onClick="if (this.checked) {return confirm('<?=$lang["deleteallsure"]?>');}">
<div class="clearerleft"> </div>
</div><? } ?>

<?
if ($enable_collection_copy) 
	{
	?>
	<div class="Question">
	<label for="copy"><?=$lang["copyfromcollection"]?></label>
	<select name="copy" id="copy" class="stdwidth" onChange="
	var ccra =document.getElementById('copycollectionremoveall');
	if ($('copy').value!=''){ccra.style.display='block';}
	else{ccra.style.display='none';}">
	<option value=""><?=$lang["donotcopycollection"]?></option>
	<?
	$list=get_user_collections($userref);
	for ($n=0;$n<count($list);$n++)
		{
		if ($ref!=$list[$n]["ref"]){?><option value="<?=$list[$n]["ref"]?>"><?=htmlspecialchars($list[$n]["name"])?></option> <? }
		}
	?>
	</select>
	<div class="clearerleft"> </div>
	</div>
<div class="Question" id="copycollectionremoveall" style="display:none;">
<label for="copycollectionremoveall"><?=$lang["copycollectionremoveall"]?></label><input type=checkbox id="copycollectionremoveall" name="copycollectionremoveall" value="yes">
<div class="clearerleft"> </div>
</div>

<? } ?>

<? if (checkperm("e0") || checkperm("e1") || checkperm("e2")) { ?>
<!-- Archive Status -->
<div class="Question">
<label for="archive"><?=$lang["resetarchivestatus"]?></label>
<select class="stdwidth" name="archive" id="archive">
<option value=""><?=$lang["select"]?></option>
<? for ($n=-2;$n<=2;$n++) { ?>
<? if (checkperm("e" . $n)) { ?><option value="<?=$n?>"><?=$lang["status" . $n]?></option><? } ?>
<? } ?>
</select>
<div class="clearerleft"> </div>
</div>
<div class="Question">
<label for="archive"><?=$lang["editallresources"]?></label>
<div class="Fixed">
<? if (allow_multi_edit($ref)) { ?>
<a href="edit.php?collection=<?=$ref?>"><?=$lang["editresources"]?> &gt;</a>
<? } else { ?><?=$lang["multieditnotallowed"]?><? } ?></div>
<div class="clearerleft"> </div>
</div>
<? } ?>

<? if (file_exists("plugins/collection_edit.php")) { include "plugins/collection_edit.php"; } ?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "../include/footer.php";
?>