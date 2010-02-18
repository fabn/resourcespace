<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";
include "../include/search_functions.php";
include "../include/image_processing.php";

# Editing resource or collection of resources (multiple)?
$ref=getvalescaped("ref","",true);

# Fetch search details (for next/back browsing and forwarding of search params)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);
$errors=array(); # The results of the save operation (e.g. required field messages)

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

# next / previous resource browsing
$go=getval("go","");
if ($go!="")
	{
	# Re-run the search and locate the next and previous records.
	$result=do_search($search,$restypes,$order_by,$archive,240+$offset+1);
	if (is_array($result))
		{
		# Locate this resource
		$pos=-1;
		for ($n=0;$n<count($result);$n++)
			{
			if ($result[$n]["ref"]==$ref) {$pos=$n;}
			}
		if ($pos!=-1)
			{
			if (($go=="previous") && ($pos>0)) {$ref=$result[$pos-1]["ref"];}
			if (($go=="next") && ($pos<($n-1))) {$ref=$result[$pos+1]["ref"];if (($pos+1)>=($offset+72)) {$offset=$pos+1;}} # move to next page if we've advanced far enough
			}
		else
			{
			?>
			<script type="text/javascript">
			alert("<?php echo $lang["resourcenotinresults"] ?>");
			</script>
			<?php
			}
		}
	}

$collection=getvalescaped("collection","",true);
if ($collection!="") 
	{
	# If editing multiple items, use the first resource as the template
	$multiple=true;
	$items=get_collection_resources($collection);
	if (count($items)==0) {exit("You cannot edit an empty collection.");}
	$ref=$items[0];
	}
else
	{
	$multiple=false;
	}

if (getval("regenexif","")!="")
	{
	extract_exif_comment($ref);
	}


# Fetch resource data.
$resource=get_resource_data($ref);

# Not allowed to edit this resource?
if (!get_edit_access($ref,$resource["archive"])) {exit ("Permission denied.");}

if (getval("regen","")!="")
	{
	create_previews($ref,false,$resource["file_extension"]);
	}
	
# Establish if this is a metadata template resource, so we can switch off certain unnecessary features
$is_template=(isset($metadata_template_resource_type) && $resource["resource_type"]==$metadata_template_resource_type);
	

if (getval("submitted","")!="" && getval("resetform","")=="" && getval("copyfromsubmit","")=="")
	{
	# save data
	if (!$multiple)
		{

		# Batch upload - change resource type
		$resource_type=getvalescaped("resource_type","");
		if ($resource_type!="")
			{
			update_resource_type($ref,$resource_type);
			$resource=get_resource_data($ref,false); # Reload resource data.
			}		

		$save_errors=save_resource_data($ref,$multiple);
		$no_exif=getval("no_exif","");
		
		if (($save_errors===true || $is_template)&&(getval("tweak","")==""))
			{
			if ($ref>0)
				{
				# Log this			
				daily_stat("Resource edit",$ref);
				redirect("pages/view.php?ref=" . $ref . "&search=" . urlencode($search) . "&offset=" . $offset . "&order_by=" . $order_by . "&sort=".$sort."&archive=" . $archive . "&refreshcollectionframe=true");
				}
			else
				{
				if (getval("swf","")!="") // Test if in browser flash upload
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/upload_swf.php?collection_add=" . getval("collection_add","")."&entercolname=".getval("entercolname","")."&resource_type=".$resource_type . "&no_exif=" . $no_exif);}
					}
				elseif (getval("java","")!="") // Test if in browser java upload
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/upload_java.php?collection_add=" . getval("collection_add","")."&entercolname=".getval("entercolname","")."&resource_type=".$resource_type . "&no_exif=" . $no_exif);}
					}
				elseif (getval("local","")!="") // Test if fetching resource from local upload folder.
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/team/team_batch_select.php?use_local=yes&resource_type=".$resource_type . "&no_exif=" . $no_exif);}
					}
				else
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/team/team_batch.php?resource_type=".$resource_type . "&no_exif=" . $no_exif);}
					}
				}
			}
		elseif (getval("save","")!="")
			{
			?>
			<script type="text/javascript">
			alert('<?php echo addslashes($lang["requiredfields"]) ?>');
			</script>
			<?php
			}
		}
	else
		{
		# Save multiple resources
		save_resource_data_multi($collection);
		if(!hook("redirectaftermultisave")){
			redirect("pages/search.php?refreshcollectionframe=true&search=!collection" . $collection);
			}
		}
	}

if (getval("tweak","")!="")
	{
	$tweak=getval("tweak","");
	switch($tweak)
		{
		case "rotateclock":
		tweak_preview_images($ref,270,0,$resource["preview_extension"]);
		break;
		case "rotateanti":
		tweak_preview_images($ref,90,0,$resource["preview_extension"]);
		break;
		case "gammaplus":
		tweak_preview_images($ref,0,1.3,$resource["preview_extension"]);
		break;
		case "gammaminus":
		tweak_preview_images($ref,0,0.7,$resource["preview_extension"]);
		break;
		case "restore":
		create_previews($ref,false,$resource["file_extension"]);
		break;
		}

	# Reload resource data.
	$resource=get_resource_data($ref,false);
	}
	
# Simulate reupload (preserving filename and thumbs, but otherwise resetting metadata).
if (getval("exif","")!="")
	{
	upload_file($ref,$no_exif=false,true);
	}	

# If requested, refresh the collection frame (for redirects from saves)
if (getval("refreshcollectionframe","")!="")
	{
	refresh_collection_frame();
	}

include "../include/header.php";
?>
<script type="text/javascript">
function ShowHelp(field)
	{
	// Show the help box if available.
	if (document.getElementById('help_' + field))
		{
		Effect.Appear('help_' + field, { duration: 0.5 });
		}
	}
function HideHelp(field)
	{
	// Hide the help box if available.
	if (document.getElementById('help_' + field))
		{
		document.getElementById('help_' + field).style.display='none';
		}
	}
</script>

<div class="BasicsBox"> 

<form method="post" id="mainform">
<input type="hidden" name="submitted" value="true">
<?php 
if ($multiple) { ?>
<h1><?php echo $lang["editmultipleresources"]?></h1>
<p><?php echo count($items)?> <?php echo $lang["resourcesselected"]?>. <?php echo text("multiple")?></p>

<?php } elseif ($ref>0) { ?>
<p><a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresourceview"]?></a></p>

<h1><?php echo $lang["editresource"]?></h1>
<?php if (!$multiple) { 
# Resource next / back browsing.
?>
<div class="TopInpageNav">
<a href="edit.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&go=previous">&lt;&nbsp;<?php echo $lang["previousresult"]?></a>
|
<a href="search.php<?php if (strpos($search,"!")!==false) {?>?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?><?php } ?>"><?php echo $lang["viewallresults"]?></a>
|
<a href="edit.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&go=next"><?php echo $lang["nextresult"]?>&nbsp;&gt;</a>
</div>
<?php } ?>


<div class="Question" style="border-top:none;">
<label><?php echo $lang["resourceid"]?></label>
<div class="Fixed"><?php echo $ref?></div>
<div class="clearerleft"> </div>
</div>

<?php if (!$is_template && !checkperm("F*")) { ?>
<div class="Question">
<label><?php echo $lang["file"]?></label>
<div class="Fixed">
<?php
if ($resource["has_image"]==1)
	{
	?><img align="top" src="<?php echo get_resource_path($ref,false,($edit_large_preview?"pre":"thm"),false,$resource["preview_extension"],-1,1,checkperm("w"))?>" class="ImageBorder" style="margin-right:10px;"/><br />
	<?php
	}
else
	{
	# Show the no-preview icon
	?>
	<img src="../gfx/<?php echo get_nopreview_icon($resource["resource_type"],$resource["file_extension"],true)?>" />
	<br />
	<?
	}
if ($resource["file_extension"]!="") { ?><strong><?php echo strtoupper($resource["file_extension"] . " " . $lang["file"]) . " (" . formatfilesize(@filesize(get_resource_path($ref,true,"",false,$resource["file_extension"]))) . ")" ?></strong><br /><?php } ?>

	<?php if ($resource["has_image"]!=1) { ?>
	<a href="upload.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["uploadafile"]?></a>
	<?php } else { ?>
	<a href="upload.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["replacefile"]?></a>
	<?php } ?>
	<?php if (! $disable_upload_preview) { ?><br />
	<a href="upload_preview.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["uploadpreview"]?></a><?php } ?>
	<?php if (! $disable_alternative_files) { ?><br />
	<a href="alternative_files.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["managealternativefiles"]?></a><?php } ?>
	<?php if ($allow_metadata_revert){?><br />
	<a href="edit.php?ref=<?php echo $ref?>&exif=true&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" onClick="return confirm('<?php echo $lang["confirm-revertmetadata"]?>');">&gt; 
	<?php echo $lang["action-revertmetadata"]?></a><?php } ?>
</div>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<?php if (!checkperm("F*")) { ?>
<div class="Question">
<label><?php echo $lang["imagecorrection"]?><br/><?php echo $lang["previewthumbonly"]?></label><select class="stdwidth" name="tweak" id="tweak" onChange="document.getElementById('mainform').submit();">
<option value=""><?php echo $lang["select"]?></option>
<?php if ($resource["has_image"]==1) { ?>
<?php
# On some PHP installations, the imagerotate() function is wrong and images are turned incorrectly.
# A local configuration setting allows this to be rectified
if (!$image_rotate_reverse_options)
	{
	?>
	<option value="rotateclock"><?php echo $lang["rotateclockwise"]?></option>
	<option value="rotateanti"><?php echo $lang["rotateanticlockwise"]?></option>
	<?php
	}
else
	{
	?>
	<option value="rotateanti"><?php echo $lang["rotateclockwise"]?></option>
	<option value="rotateclock"><?php echo $lang["rotateanticlockwise"]?></option>
	<?php
	}
?>
<option value="gammaplus"><?php echo $lang["increasegamma"]?></option>
<option value="gammaminus"><?php echo $lang["decreasegamma"]?></option>
<option value="restore"><?php echo $lang["recreatepreviews"]?></option>
<?php } else { ?>
<option value="restore"><?php echo $lang["retrypreviews"]?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } ?>


<?php } else { # For batch uploads, specify default content (writes to resource with ID [negative user ref]) ?>
<h1><?php echo $lang["specifydefaultcontent"]?></h1>
<p><?php echo text("batch")?></p>

<?php if ($ref<0) { 
	# User edit template. Show the save / clear buttons at the top too, to avoid unnecessary scrolling.
	?>
	<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<input name="resetform" type="submit" value="<?php echo $lang["clearform"]?>" />&nbsp;
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["next"]?>&nbsp;&nbsp;" /><br><br>
	<div class="clearerleft"> </div>
	</div>
	<?php
	}
?>

<div class="Question">
<label for="resourcetype"><?php echo $lang["resourcetype"]?></label>
<select name="resource_type" id="resourcetype" class="stdwidth" onChange="document.getElementById('mainform').submit();">
<?php
$types=get_resource_types();
for ($n=0;$n<count($types);$n++)
	{
	?><option value="<?php echo $types[$n]["ref"]?>" <?php if ($resource["resource_type"]==$types[$n]["ref"]) {?>selected<?php } ?>><?php echo $types[$n]["name"]?></option><?php
	}
?></select>
<div class="clearerleft"> </div>
</div>

<?php
if (getval("swf","")!="" || getval("java","")!="") { 

# Batch uploads (SWF/Java) - also ask which collection to add the resource to.
if ($enable_add_collection_on_upload) 
	{
	?>
	<div class="Question">
	<label for="collection_add"><?php echo $lang["addtocollection"]?></label>
	<select name="collection_add" id="collection_add" class="stdwidth"   onchange="if($(this).value==-1){$('collectionname').style.display='block';} else {$('collectionname').style.display='none';}">
	<option value="-1" <?php if ($upload_add_to_new_collection){ ?>selected <?php }?>>(<?php echo $lang["createnewcollection"]?>)</option>
	<option value="" <?php if (!$upload_add_to_new_collection){ ?>selected <?php }?>><?php echo $lang["batchdonotaddcollection"]?></option>
	<?php
	$list=get_user_collections($userref);
	$currentfound=false;
	for ($n=0;$n<count($list);$n++)
		{
		if ($list[$n]["ref"]==$usercollection) {$currentfound=true;}
		?>
		<option value="<?php echo $list[$n]["ref"]?>"><?php echo htmlspecialchars($list[$n]["name"])?></option>
		<?php
		}
	if (!$currentfound)
		{
		# The user's current collection has not been found in their list of collections (perhaps they have selected a theme to edit). Display this as a separate item.
		$cc=get_collection($usercollection);
		if ($cc!==false)
			{
			?>
			<option value="<?php echo $usercollection?>"><?php echo htmlspecialchars($cc["name"])?></option>
			<?php
			}
		}
	?>
	</select>
	<div class="clearerleft"> </div>
	<div name="collectionname" id="collectionname" <?php if ($upload_add_to_new_collection){ ?> style="display:block;"<?php } else { ?> style="display:none;"<?php } ?>>
	<label for="collection_add"><?php echo $lang["collectionname"]?></label>
	<input type=text id="entercolname" name="entercolname" class="stdwidth">
	</div>
	</div>
	<?php 
}
?>


<?php } ?>

<div class="Question">
<label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox id="no_exif" name="no_exif" value="yes" <?php if (getval("no_exif","")!="") { ?>checked<?php } ?>>
<div class="clearerleft"> </div>
</div>

<?php } ?>

<?php
$lastrt=-1;

# "copy data from" feature
if ($enable_copy_data_from && !$multiple && !checkperm("F*"))
	{ 
	?>
	<div class="Question">
	<label for="copyfrom"><?php echo $lang["batchcopyfrom"]?></label>
	<input class="stdwidth" type="text" name="copyfrom" id="copyfrom" value="" style="width:80px;">
	<input type="submit" name="copyfromsubmit" value="<?php echo $lang["copy"]?>">
	</div>
	<?php
	}

if (isset($metadata_template_resource_type) && !$multiple && !checkperm("F*"))
	{
	# Show metadata templates here
	?>
	<div class="Question">
	<label for="metadatatemplate"><?php echo $lang["usemetadatatemplate"]?></label>
	<select name="metadatatemplate" class="stdwidth" style="width:310px;">
	<option value=""><?php echo (getval("metadatatemplate","")=="")?$lang["select"]:$lang["undometadatatemplate"] ?></option>
	<?php
	$templates=get_metadata_templates();
	foreach ($templates as $template)
		{
		?>
		<option value="<?php echo $template["ref"] ?>"><?php echo $template["field$metadata_template_title_field"] ?></option>
		<?php	
		}
	?>
	</select>
	<input type="submit" name="copyfromsubmit" value="<?php echo $lang["action-select"]?>">
	</div>
	<?php
	}

$use=$ref;

# Resource aliasing.
# 'Copy from' or 'Metadata template' been supplied? Load data from this resource instead.
$originalref=$use;
if (getval("copyfrom","")!="") {$use=getvalescaped("copyfrom","");}
if (getval("metadatatemplate","")!="") {$use=getvalescaped("metadatatemplate","");}

?>
<br /><br /><h1><?php echo $lang["resourcemetadata"]?></h1>
<?php

$fields=get_resource_field_data($use,$multiple,true,$originalref);
for ($n=0;$n<count($fields);$n++)
	{
	# Should this field be displayed?
	if (!
		(
			# Field is an archive only field
			(($resource["archive"]==0) && ($fields[$n]["resource_type"]==999))
		||
			# Field has write access denied
			(checkperm("F*") && !checkperm("F-" . $fields[$n]["ref"]))
		||			
			checkperm("F" . $fields[$n]["ref"])
		))
		
		{

	$name="field_" . $fields[$n]["ref"];
	$value=$fields[$n]["value"];
	
	if ($multilingual_text_fields)
		{
		# Multilingual text fields - find all translations and display the translation for the current language.
		$translations=i18n_get_translations($value);
		if (array_key_exists($language,$translations)) {$value=$translations[$language];} else {$value="";}
		}
	
	if ($multiple) {$value="";} # Blank the value for multi-edits.
	
	if (($fields[$n]["resource_type"]!=$lastrt)&& ($lastrt!=-1))
		{
		?><br><h1><?php echo get_resource_type_name($fields[$n]["resource_type"])?> <?php echo $lang["properties"]?></h1><?php
		}
	$lastrt=$fields[$n]["resource_type"];
	
	# Blank form if 'reset form' has been clicked.
	if (getval("resetform","")!="") {$value="";}

	# If config option $blank_edit_template is set, always show a blank form for user edit templates.
	if ($ref<0 && $blank_edit_template && getval("submitted","")=="") {$value="";}

	?>
	<?php if ($multiple) { # Multiple items, a toggle checkbox appears which activates the question
	?><div><input name="editthis_<?php echo $name?>" id="editthis_<?php echo $n?>" type="checkbox" value="yes" onClick="var q=document.getElementById('question_<?php echo $n?>');var m=document.getElementById('modeselect_<?php echo $n?>');var f=document.getElementById('findreplace_<?php echo $n?>');if (this.checked) {q.style.display='block';m.style.display='block';} else {q.style.display='none';m.style.display='none';f.style.display='none';document.getElementById('modeselectinput_<?php echo $n?>').selectedIndex=0;}">&nbsp;<label for="editthis<?php echo $n?>"><?php echo htmlspecialchars(i18n_get_translated($fields[$n]["title"]))?></label></div><?php } ?>

	<?php
	if ($multiple)
		{
		# When editing multiple, give option to select Replace All Text or Find and Replace
		?>
		<div class="Question" id="modeselect_<?php echo $n?>" style="display:none;padding-bottom:0;margin-bottom:0;">
		<label><?php echo $lang["editmode"]?></label>
		<select id="modeselectinput_<?php echo $n?>" name="modeselect_<?php echo $fields[$n]["ref"]?>" class="stdwidth" onChange="var fr=document.getElementById('findreplace_<?php echo $n?>');var q=document.getElementById('question_<?php echo $n?>');if (this.value=='FR') {fr.style.display='block';q.style.display='none';} else {fr.style.display='none';q.style.display='block';}">
		<option value="RT"><?php echo $lang["replacealltext"]?></option>
		<?php if ($fields[$n]["type"]==0 || $fields[$n]["type"]==1 || $fields[$n]["type"]==5) { 
		# Find and replace appies to text boxes only.
		?>
		<option value="FR"><?php echo $lang["findandreplace"]?></option>
		<?php } ?>
		<?php if ($fields[$n]["type"]==0 || $fields[$n]["type"]==1 || $fields[$n]["type"]==5 || $fields[$n]["type"]==2 || $fields[$n]["type"]==3) { 
		# Append applies to text boxes, checkboxes and dropdowns only.
		?>
		<option value="AP"><?php echo $lang["appendtext"]?></option>
		<option value="RM"><?php echo $lang["removetext"]?></option>
		<?php } ?>
		</select>
		</div>
		
		<div class="Question" id="findreplace_<?php echo $n?>" style="display:none;border-top:none;">
		<label>&nbsp;</label>
		<?php echo $lang["find"]?> <input type="text" name="find_<?php echo $fields[$n]["ref"]?>" class="shrtwidth">
		<?php echo $lang["andreplacewith"]?> <input type="text" name="replace_<?php echo $fields[$n]["ref"]?>" class="shrtwidth">
		</div>
		<?php
		}
	?>

	<div class="Question" id="question_<?php echo $n?>" <?php if ($multiple) {?>style="display:none;border-top:none;"<?php } ?>>
	<label for="<?php echo $name?>"><?php if (!$multiple) {?><?php echo htmlspecialchars(i18n_get_translated($fields[$n]["title"]))?> <?php if (!$is_template && $fields[$n]["required"]==1) { ?><sup>*</sup><?php } ?><?php } ?></label>
	<?php

	# Define some Javascript for help actions (applies to all fields)
	$help_js="onBlur=\"HideHelp(" . $fields[$n]["ref"] . ");return false;\" onFocus=\"ShowHelp(" . $fields[$n]["ref"] . ");return false;\"";
	
	#hook to modify field type in special case. Returning zero (to get a standard text box) doesn't work, so return 1 for type 0, 2 for type 1, etc.
	$modified_field_type="";
	$modified_field_type=(hook("modifyfieldtype"));
	if ($modified_field_type){$fields[$n]["type"]=$modified_field_type-1;}
	
	switch ($fields[$n]["type"]) {
		case 0: # -------- Plain text entry
		?><input class="stdwidth" type=text name="<?php echo $name?>" id="<?php echo $name?>" value="<?php echo htmlspecialchars($value)?>" <?php echo $help_js; ?>><?php
		break;
	
		case 1: # -------- Text area entry
		?><textarea class="stdwidth" rows=6 cols=50 name="<?php echo $name?>" id="<?php echo $name?>" <?php echo $help_js; ?>><?php echo htmlspecialchars($value)?></textarea><?php
		break;
		
		case 5: # -------- Larger text area entry
		?><textarea class="stdwidth" rows=20 cols=80 name="<?php echo $name?>" id="<?php echo $name?>" <?php echo $help_js; ?>><?php echo htmlspecialchars($value)?></textarea><?php
		break;
		
		case 2: # -------- Check box list

		# Translate all options
		$options=trim_array(explode(",",$fields[$n]["options"]));
		$option_trans=array();
		$option_trans_simple=array();
		for ($m=0;$m<count($options);$m++)
			{
			$trans=i18n_get_translated($options[$m]);
			$option_trans[$options[$m]]=$trans;
			$option_trans_simple[]=$trans;
			}

		if ($auto_order_checkbox) {asort($option_trans);}
		$options=array_keys($option_trans); # Set the options array to the keys, so it is now effectively sorted by translated string	
			
		$set=trim_array(explode(",",$value));
		$wrap=0;
		$l=average_length($option_trans_simple);
		$cols=10;
		if ($l>5)  {$cols=6;}
		if ($l>10) {$cols=4;}
		if ($l>15) {$cols=3;}
		if ($l>25) {$cols=2;}
		
		$height=ceil(count($options)/$cols);
		
		global $checkbox_ordered_vertically;
		if ($checkbox_ordered_vertically)
			{
			# ---------------- Vertical Ordering (only if configured) -----------
			?><table cellpadding=2 cellspacing=0><tr><?php
			for ($y=0;$y<$height;$y++)
				{
				for ($x=0;$x<$cols;$x++)
					{
					# Work out which option to fetch.
					$o=($x*$height)+$y;
					if ($o<count($options))
						{
						$option=$options[$o];
						$trans=$option_trans[$option];

						$name=$fields[$n]["ref"] . "_" . urlencode($option);
						if ($option!="")
							{
							?>
							<td width="1"><input type="checkbox" name="<?php echo $name?>" value="yes" <?php if (in_array($option,$set)) {?>checked<?php } ?> /></td><td><?php echo htmlspecialchars($trans)?>&nbsp;</td>
							<?php
							}
						}
					}
				?></tr><tr><?php
				}
			?></tr></table><?php
			}
		else
			{				
			# ---------------- Horizontal Ordering (Standard) ---------------------				
			?>
			<table cellpadding=2 cellspacing=0><tr>
			<?php
	
			foreach ($option_trans as $option=>$trans)
				{
				$name=$fields[$n]["ref"] . "_" . urlencode($option);
				$wrap++;if ($wrap>$cols) {$wrap=1;?></tr><tr><?php }
				?>
				<td width="1"><input type="checkbox" name="<?php echo $name?>" value="yes" <?php if (in_array($option,$set)) {?>checked<?php } ?> /></td><td><?php echo htmlspecialchars($trans)?>&nbsp;</td>
				<?php
				}
			?></tr></table><?php
			}
		break;

		case 3: # -------- Drop down list
		# Translate all options
		$options=trim_array(explode(",",$fields[$n]["options"]));
		$option_trans=array();
		for ($m=0;$m<count($options);$m++)
			{
			$option_trans[$options[$m]]=i18n_get_translated($options[$m]);
			}
		if ($auto_order_checkbox) {asort($option_trans);}	
		
		# This hook will need modifying as the options list no longer works this way. Options are now translated first so the sort order is correct. ~DH
		#$adjusted_editdropdownoptions=hook("adjusteditdropdownoptions");
		#if ($adjusted_editdropdownoptions){$options=$adjusted_editdropdownoptions;}

		if (substr($value,0,1) == ',') { $value = substr($value,1); }	// strip the leading comma if it exists	

		?><select class="stdwidth" name="<?php echo $name?>" id="<?php echo $name?>" <?php echo $help_js; ?>>
		<option value=""></option>
		<?php
		foreach ($option_trans as $option=>$trans)
			{
			if (trim($option)!="")
				{
				?>
				<option value="<?php echo htmlspecialchars(trim($option))?>" <?php if (trim($option)==trim($value)) {?>selected<?php } ?>><?php echo htmlspecialchars(trim($trans))?></option>
				<?php
				}
			}
		?></select><?php
		break;
		
		
		case 4: # -------- Date selector
		case 6: # Also includes expiry date

		# Start with a null date
		$dy="";$dm="";$dd=""; 
		$dh="";$di="";
		
		if (($ref<0 || $value=="") && $reset_date_upload_template && $reset_date_field==$fields[$n]["ref"])
			{
			# Upload template: always reset to today's date (if configured).
			$dy=date("Y");$dm=date("m");$dd=date("d");
			$dh=date("H");$di=date("i");
			}
		elseif ($value!="")
        	{
            #fetch the date parts from the value
            $sd=explode(" ",$value);
            if (count($sd)>=2)
            	{
            	# Attempt to extract hours and minutes from second part.
            	$st=explode(":",$sd[1]);
            	if (count($st>=2))
            		{
            		$dh=$st[0];
            		$di=$st[1];
            		}
            	}
            $value=$sd[0];
            $sd=explode("-",$value);
            if (count($sd)>=3)
            	{
	            $dy=intval($sd[0]);$dm=intval($sd[1]);$dd=intval($sd[2]);
	            }
            }
        ?>
        <select name="<?php echo $name?>-d"><option value=""><?php echo $lang["day"]?></option>
        <?php for ($m=1;$m<=31;$m++) {?><option <?php if($m==$dd){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
        </select>
            
        <select name="<?php echo $name?>-m"><option value=""><?php echo $lang["month"]?></option>
        <?php for ($m=1;$m<=12;$m++) {?><option <?php if($m==$dm){echo " selected";}?> value="<?php echo sprintf("%02d",$m)?>"><?php echo $lang["months"][$m-1]?></option><?php } ?>
        </select>
           
        <input type=text size=5 name="<?php echo $name?>-y" value="<?php echo $dy?>">
        
        <!-- Time (optional) -->
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
        <select name="<?php echo $name?>-h"><option value=""><?php echo $lang["hour-abbreviated"]?></option>
        <?php for ($m=0;$m<=23;$m++) {?><option <?php if($m==$dh && $dh!=""){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
        </select>
        
        <select name="<?php echo $name?>-i"><option value=""><?php echo $lang["minute-abbreviated"]?></option>
        <?php for ($m=0;$m<=59;$m++) {?><option <?php if($m==$di && $di!=""){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
        </select>
        
        
        <?php
		break;
		
		
		case 7: # ----- Category Tree
		$options=$fields[$n]["options"];
		include "../include/category_tree.php";
		break;
		}
		?>
		
		<?php
		# Display any error messages from previous save
		if (array_key_exists($fields[$n]["ref"],$errors))
			{
			?>
			<div class="FormError">!! <?php echo $errors[$fields[$n]["ref"]]?> !!</div>
			<?php
			}

		if (trim($fields[$n]["help_text"]!=""))
			{
			# Show inline help for this field.
			# For certain field types that have no obvious focus, the help always appears.
			?>
			<div class="FormHelp" style="padding:0;<?php if (!in_array($fields[$n]["type"],array(2,6,7))) { ?> display:none;<?php } else { ?> clear:left;<?php } ?>" id="help_<?php echo $fields[$n]["ref"]?>"><div class="FormHelpInner"><?php echo nl2br(trim(htmlspecialchars(i18n_get_translated($fields[$n]["help_text"]))))?></div></div>
			<?php
			}

		# If enabled, include code to produce extra fields to allow multilingual free text to be entered.
		if ($multilingual_text_fields && ($fields[$n]["type"]==0 || $fields[$n]["type"]==1 || $fields[$n]["type"]==5))
			{
			include "../include/multilingual_fields.php";
			}
		?>			
		<div class="clearerleft"> </div>
		</div>
		<?php
		}
	}


if (!checkperm("F*")) # Only display status/relationships if full write access field access has been granted.
{
?>
<?php if(!hook("replacestatusandrelationshipsheader")){?>
<?php if ($ref>=0) { ?><br><h1><?php echo $lang["statusandrelationships"]?></h1><?php } ?>
<?php } /* end hook replacestatusandrelationshipsheader */ ?>

	<!-- Archive Status -->
	<?php if ($ref<0 && $show_status_and_access_on_upload==false) { 
		# Hide the dropdown, and set the default status depending on user permissions.
		$mode=0;
		if (checkperm("e-1")) {$mode=-1;}
		if (checkperm("e-2")) {$mode=-2;}
		if (checkperm("e2")) {$mode=2;}
		if (checkperm("e0")) {$mode=0;}
		
		$modified_defaultstatus=(hook("modifydefaultstatusmode"));
		if ($modified_defaultstatus){$mode=$modified_defaultstatus;}
		
		#if (checkperm("e0") && checkperm("e-2")) {$mode=-2;}
		?>
		<input type=hidden name="archive" value="<?php echo $mode?>">
		<?php
		}
	else { ?>
	<?php if(!hook("replacestatusselector")){?>
	<?php if ($multiple) { ?><div id="editmultiple_status"><input name="editthis_status" id="editthis_status" value="yes" type="checkbox" onClick="var q=document.getElementById('question_status');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label id="editthis_status_label" for="editthis<?php echo $n?>"><?php echo $lang["status"]?></label></div><?php } ?>
	<div class="Question" id="question_status" <?php if ($multiple) {?>style="display:none;"<?php } ?>>
	<label for="archive"><?php echo $lang["status"]?></label>
	<select class="stdwidth" name="archive" id="archive">
	<?php for ($n=-2;$n<=3;$n++) { ?>
	<?php if (checkperm("e" . $n)) { ?><option value="<?php echo $n?>" <?php if ($resource["archive"]==$n) { ?>selected<?php } ?>><?php echo $lang["status" . $n]?></option><?php } ?>
	<?php } ?>
	</select>
	<div class="clearerleft"> </div>
	</div>
	<?php } ?>
	<?php } /* end hook replacestatusselector */?>

<?php if (!hook("replaceaccessselector")){ ?>	
	<!-- Access -->
	<?php if ($ref<0 && $show_status_and_access_on_upload==false) { 
		# Do not show for user editable templates.
		?>
		<input type=hidden name="access" value="<?php echo $resource["access"]?>">
		<?php
		}
	else { ?>
	<?php if ($multiple) { ?><div><input name="editthis_access" id="editthis_access" value="yes" type="checkbox" onClick="var q=document.getElementById('question_access');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editthis<?php echo $n?>"><?php echo $lang["access"]?></label></div><?php } ?>
	<div class="Question" id="question_access" <?php if ($multiple) {?>style="display:none;"<?php } ?>>
	<label for="archive"><?php echo $lang["access"]?></label>
	<select class="stdwidth" name="access" id="access" onChange="var c=document.getElementById('custom_access');if (this.value==3) {c.style.display='block';} else {c.style.display='none';}">
	
	<?php for ($n=0;$n<=($custom_access?3:2);$n++) { ?>
	<?php if ($n==2 && checkperm("v")){?><option value="<?php echo $n?>" <?php if ($resource["access"]==$n) { ?>selected<?php } ?>><?php echo $lang["access" . $n]?></option><?php } 
	else if ($n!=2){ ?><option value="<?php echo $n?>" <?php if ($resource["access"]==$n) { ?>selected<?php } ?>><?php echo $lang["access" . $n]?></option><?php } ?>
	<?php } ?>
	</select>
	<div class="clearerleft"> </div>
	<table id="custom_access" cellpadding=3 cellspacing=3 style="padding-left:150px;<?php if (!$custom_access || $resource["access"]!=3) { ?>display:none;<?php } ?>">
	<?php
	$groups=get_resource_custom_access($ref);
	for ($n=0;$n<count($groups);$n++)
		{
		$access=2;$editable=true;
		if ($groups[$n]["access"]!="") {$access=$groups[$n]["access"];}
		$perms=explode(",",$groups[$n]["permissions"]);
		if (in_array("v",$perms)) {$access=0;$editable=false;}
		?>
		<tr>
		<td valign=middle nowrap><?php echo htmlspecialchars($groups[$n]["name"])?>&nbsp;&nbsp;</td>
		<td width=10 valign=middle><input type=radio name="custom_<?php echo $groups[$n]["ref"]?>" value="0" <?php if (!$editable) { ?>disabled<?php } ?> <?php if ($access==0) { ?>checked<?php } ?>></td>
		<td align=left valign=middle><?php echo $lang["access0"]?></td>
		<td width=10 valign=middle><input type=radio name="custom_<?php echo $groups[$n]["ref"]?>" value="1" <?php if (!$editable) { ?>disabled<?php } ?> <?php if ($access==1) { ?>checked<?php } ?>></td>
		<td align=left valign=middle><?php echo $lang["access1"]?></td>
		<?php if (checkperm("v")){?><td width=10 valign=middle><input type=radio name="custom_<?php echo $groups[$n]["ref"]?>" value="2" <?php if (!$editable) { ?>disabled<?php } ?> <?php if ($access==2) { ?>checked<?php } ?>></td>
		<td align=left valign=middle><?php echo $lang["access2"]?></td><?php } ?>
		</tr>
		<?php
		}
	?></table>
	<div class="clearerleft"> </div>
	</div>
	<?php } ?>
	<?php } /* end Hook replaceaccessselector */ ?>

	<!-- Related Resources -->
	<?php if ($enable_related_resources) { ?>
	<?php if ($multiple) { ?><div><input name="editthis_related" id="editthis_related" value="yes" type="checkbox" onClick="var q=document.getElementById('question_related');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editthis<?php echo $n?>"><?php echo $lang["relatedresources"]?></label></div><?php } ?>
	<div class="Question" id="question_related" <?php if ($multiple) {?>style="display:none;"<?php } ?>>
	<label for="related"><?php echo $lang["relatedresources"]?></label>
	<textarea class="stdwidth" rows=3 cols=50 name="related" id="related"><?php echo ((getval("resetform","")!="")?"":join(", ",get_related_resources($ref)))?></textarea>
	<div class="clearerleft"> </div>
	</div>
	<?php } 
}
?>
	
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<input name="resetform" type="submit" value="<?php echo $lang["clearform"]?>" />&nbsp;
	<input <?php if ($multiple) { ?>onclick="return confirm('<?php echo $lang["confirmeditall"]?>');"<?php } ?> name="save" type="submit" value="&nbsp;&nbsp;<?php echo ($ref>0)?$lang["save"]:$lang["next"]?>&nbsp;&nbsp;" /><br><br>
	<div class="clearerleft"> </div>
	</div>
	
</form>
<?php if (!$is_template) { ?><p><sup>*</sup> <?php echo $lang["requiredfield"]?></p><?php } ?>
</div>

<!--<p><a href="view.php?ref=<?php echo $ref?>">Back to view</a></p>-->
<?php
include "../include/footer.php";
?>
