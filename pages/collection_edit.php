<?php
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/resource_functions.php";
include "../include/search_functions.php"; 

$ref=getvalescaped("ref","",true);
$copycollectionremoveall=getvalescaped("copycollectionremoveall","");

# Fetch collection data
$collection=get_collection($ref);if ($collection===false) {
	$error=$lang['error-collectionnotfound'];
	include "../include/header.php";
	error_alert($error);
	}
$resources=do_search("!collection".$ref);
$colcount=count($resources);

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
		if (getval("addlevel","")=="yes"){
			redirect ("pages/collection_edit.php?ref=".$ref."&addlevel=yes");
			}		
		else if ((getval("theme","")!="") || (getval("newtheme","")!=""))
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

<?php hook('additionalfields');?>

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
	
//////////////////////////
// find current number of themes used
$themecount=1;
foreach($collection as $key=>$value){
	if (substr($key,0,5)=="theme"){
		if ($value==""){break 1;} 
		else{
			if (substr($key,5)==""){
				$themecount=1;				
				$orig_themecount=$themecount;
				}
			else{
				$themecount=substr($key,5);
				$orig_themecount=$themecount;
				}
		}
	}
}
//echo "<br/>Current theme level:".$themecount;

// find number of theme columns
foreach($collection as $key=>$value){
	if (substr($key,0,5)=="theme"){
		$themecolumns=substr($key,5);
	}
}		
//echo "<br/>Theme levels available:".$themecolumns;	
	
if (checkperm("h") && $enable_themes) { # Only users with the 'h' permission can publish public collections as themes.


?>
<input type=hidden name="addlevel" id="addlevel" value=""/>
<?php

if (getval("addlevel","")=="yes"){$themecount++;}
$lastselected=false;
# Theme category levels
for ($i=1;$i<=$themecount;$i++){
if ($theme_category_levels>=$i)
	{
	if ($i==1){$themeindex="";}else{$themeindex=$i;}	
	$themearray=array();
	for($y=0;$y<$i-1;$y++){
		if ($y==0){
				$themearray[]=$collection["theme"];
			}
			else {
				$themearray[]=$collection["theme".($y+1)];
			}
	}	
	$themes=get_theme_headers($themearray);
	?>
	<div class="Question">
	<label for="theme<?php echo $themeindex?>"><?php echo $lang["themecategory"] . " ".$themeindex ?></label>
	<?php if (count($themes)>0){?><select class="stdwidth" name="theme<?php echo $themeindex?>" id="theme<?php echo $themeindex?>" <?php if ($theme_category_levels>=$themeindex) { ?>onchange="if (document.getElementById('theme<?php echo $themeindex?>').value!=='') {document.getElementById('addlevel').value='yes'; document.getElementById('collectionform').submit();} else {document.getElementById('redirect').value='';document.getElementById('collectionform').submit();}"<?php } ?>><option value=""><?php echo $lang["select"]?></option>
	<?php 
	for ($n=0;$n<count($themes);$n++) { ?>
	<option <?php if ($collection["theme".$themeindex]==$themes[$n]) { ?>selected<?php } ?>><?php echo $themes[$n]?></option>
	<?php if ($collection["theme".$themeindex]==$themes[$n] && $i==$orig_themecount){$lastselected=true;} else {$lastselected=false;}?>
	<?php } ?>
	</select>
	<?php if (getval("addlevel","")!="yes" && $lastselected){$themecount++;}?>
	<div class="clearerleft"> </div>
	<label><?php echo $lang["newcategoryname"]?></label>
		<?php } //end conditional selector?>
	<input type=text class="medwidth" name="newtheme<?php echo $themeindex?>" id="newtheme<?php echo $themeindex?>" value="" maxlength="100">
	<?php if ($themecount!=1){?>
	<input type=button class="medcomplementwidth" value="<?php echo $lang['save'];?>" style="display:inline;" onclick="document.getElementById('addlevel').value='yes';document.getElementById('collectionform').submit();"/>	
	<?php } ?>
	<?php if ($themecount==1){?>
	<input type=button class="medcomplementwidth" value="<?php echo $lang['add'];?>" style="display:inline;" onclick="if (document.getElementById('newtheme<?php echo $themeindex?>').value==''){alert('<?php echo $lang["collectionsnothemeselected"] ?>');return false;}document.getElementById('addlevel').value='yes';document.getElementById('collectionform').submit();"/><?php }?>
	<div class="clearerleft"> </div>
	</div>
	<?php
	}
}

} else {
	// in case a user can edit collections but doesn't have themes enabled, preserve them
	for ($i=1;$i<=$themecount;$i++){
		if ($theme_category_levels>=$i)	{
			if ($i==1){$themeindex="";}else{$themeindex=$i;}	
			?>
			<input type=hidden name="theme<?php echo $themeindex?>" value="<?php echo $collection["theme".$themeindex]?>">
			<?php
		}
	}	
} 
}?>

<?php if (isset($collection['savedsearch'])&&$collection['savedsearch']==null){
	# disallowing share breaks smart collections 
	?>
<div class="Question">
<label for="allow_changes"><?php echo $lang["allowothersaddremove"]?></label><input type=checkbox id="allow_changes" name="allow_changes" <?php if ($collection["allow_changes"]==1) { ?>checked<?php } ?>>
<div class="clearerleft"> </div>
</div>
<?php } else { 
	# allow changes by default
	?><input type=hidden id="allow_changes" name="allow_changes" value="checked">
<?php } ?>

<?php if ((checkperm("e0") || checkperm("e1") || checkperm("e2")) && $colcount>1) { ?>
<div class="Question">
<label for="allow_changes"><?php echo $lang["relateallresources"]?></label><input type=checkbox id="relateall" name="relateall">
<div class="clearerleft"> </div>
</div><?php } ?>

<?php if ($colcount!=0 && $collection['savedsearch']==''){?>
<div class="Question">
<label for="removeall"><?php echo $lang["removeallresourcesfromcollection"]?></label><input type=checkbox id="removeall" name="removeall">
<div class="clearerleft"> </div>
</div>
<?php } ?>

<?php if ((checkperm("e0") || checkperm("e1") || checkperm("e2")) && !checkperm("D") && $colcount!=0) { ?>
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
