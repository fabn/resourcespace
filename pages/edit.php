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
	if (count($items)==0) {
		$error=$lang['error-cannoteditemptycollection'];
		include "../include/header.php";
		error_alert($error);
	}
	
	# check editability
	if (!allow_multi_edit($collection)){
		$error=$lang['error-permissiondenied'];
		include "../include/header.php";
		error_alert($error);
	}
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
if (!get_edit_access($ref,$resource["archive"])) {
		$error=$lang['error-permissiondenied'];
		include "../include/header.php";
		error_alert($error);}

if (getval("regen","")!="")
	{
	create_previews($ref,false,$resource["file_extension"]);
	}
	
# Establish if this is a metadata template resource, so we can switch off certain unnecessary features
$is_template=(isset($metadata_template_resource_type) && $resource["resource_type"]==$metadata_template_resource_type);
	

if (getval("tweak","")=="" && getval("submitted","")!="" && getval("resetform","")=="" && getval("copyfromsubmit","")=="")
	{
	hook("editbeforesave");			

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
		$autorotate = getval("autorotate","");

		if ($upload_collection_name_required){
			if (getvalescaped("entercolname","")=="" && getval("collection_add","")==-1){ 
				if (!is_array($save_errors)){$save_errors=array();}	
				$save_errors['collectionname']=$lang["requiredfield"];
			}
		}		
		
		if (($save_errors===true || $is_template)&&(getval("tweak","")==""))
			{
			if ($ref>0 && getval("save","")!="")
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
					if (getval("save","")!="") {redirect("pages/upload_swf.php?collection_add=" . getval("collection_add","")."&entercolname=".urlencode(getvalescaped("entercolname",""))."&resource_type=".$resource_type . "&no_exif=" . $no_exif . "&autorotate=" . $autorotate . "&themestring=" . urlencode(getval('themestring','')) . "&public=" . getval('public',''));}
					}
				elseif (getval("java","")!="") // Test if in browser java upload
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/upload_java.php?collection_add=" . getval("collection_add","")."&entercolname=".urlencode(getvalescaped("entercolname",""))."&resource_type=".$resource_type . "&no_exif=" . $no_exif . "&autorotate=" . $autorotate . "&themestring=" . urlencode(getval('themestring','')) . "&public=" . getval('public',''));}
					}
				elseif (getval("local","")!="") // Test if fetching resource from local upload folder.
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/team/team_batch_select.php?use_local=yes&collection_add=" . getval("collection_add","")."&entercolname=".urlencode(getvalescaped("entercolname",""))."&resource_type=".$resource_type . "&no_exif=" . $no_exif . "&autorotate=" . $autorotate);}
					}
                elseif (getval("single","")!="") // Test if single upload (archived or not).
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/upload.php?resource_type=".$resource_type . "&no_exif=" . $no_exif . "&autorotate=" . $autorotate . "&archive=" . $archive);}
					}    
				else // Hence fetching from ftp.
					{
					# Save button pressed? Move to next step.
					if (getval("save","")!="") {redirect("pages/team/team_batch.php?collection_add=" . getval("collection_add","")."&entercolname=".urlencode(getvalescaped("entercolname","")). "&resource_type=".$resource_type . "&no_exif=" . $no_exif . "&autorotate=" . $autorotate);}
					}
				}
			}
		elseif (getval("save","")!="")
			{
			$show_error=true;
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
		refresh_collection_frame();
		break;
		}

	# Reload resource data.
	$resource=get_resource_data($ref,false);
	}
	
# Simulate reupload (preserving filename and thumbs, but otherwise resetting metadata).
if (getval("exif","")!="")
	{
	upload_file($ref,$no_exif=false,true);
	resource_log($ref,"r","");
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
<p><?php $qty = count($items);
echo ($qty==1 ? $lang["resources_selected-1"] : str_replace("%number", $qty, $lang["resources_selected-2"])) . ". ";
# The script doesn't allow editing of empty collections, no need to handle that case here.
echo text("multiple"); ?></p>

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


<div class="Question" id="resource_ref_div" style="border-top:none;">
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
	<?php
	}
if ($resource["file_extension"]!="") { ?><strong><?php echo str_replace("?",strtoupper($resource["file_extension"]),$lang["fileoftype"]) . " (" . formatfilesize(@filesize(get_resource_path($ref,true,"",false,$resource["file_extension"]))) . ")" ?></strong><br /><?php } ?>

	<?php if ($resource["has_image"]!=1) { ?>
	<a href="upload.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["uploadafile"]?></a>
	<?php } else { ?>
	<a href="upload.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["replacefile"]?></a>
	<?php hook("afterreplacefile"); ?>
	<?php } ?>
	<?php if (! $disable_upload_preview) { ?><br />
	<a href="upload_preview.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["uploadpreview"]?></a><?php } ?>
	<?php if (! $disable_alternative_files) { ?><br />
	<a href="alternative_files.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["managealternativefiles"]?></a><?php } ?>
	<?php if ($allow_metadata_revert){?><br />
	<a href="edit.php?ref=<?php echo $ref?>&exif=true&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" onClick="return confirm('<?php echo $lang["confirm-revertmetadata"]?>');">&gt; 
	<?php echo $lang["action-revertmetadata"]?></a><?php } ?>
	<?php hook("afterfileoptions"); ?>
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


<?php } else { # Add new material(s), specify default content (writes to resource with ID [negative user ref])

# Define the title h1:
if (getval("java","")!="") {$titleh1 = $lang["addresourcebatchbrowserjava"];} # Add Resource Batch - In Browser (Java)
elseif (getval("swf","")!="") {$titleh1 = $lang["addresourcebatchbrowser"];} # Add Resource Batch - In Browser (Flash)
elseif (getval("single","")!="")
	{
	if (getval("archive","")=="2")
		{
		$titleh1 = $lang["newarchiveresource"]; # Add Single Archived Resource
		}
	else
		{
		$titleh1 = $lang["addresource"]; # Add Single Resource
		}
	}
elseif (getval("local","")!="") {$titleh1 = $lang["addresourcebatchlocalfolder"];} # Add Resource Batch - Fetch from local upload folder
else $titleh1 = $lang["addresourcebatchftp"]; # Add Resource Batch - Fetch from FTP server

# Define the subtitle h2:
$titleh2 = str_replace(array("%number","%subtitle"), array("1", $lang["specifydefaultcontent"]), $lang["header-upload-subtitle"]);
?>

<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<p><?php echo $lang["intro-batch_edit"] ?></p>

<?php if ($ref<0) { 
	# User edit template. Show the save / clear buttons at the top too, to avoid unnecessary scrolling.
	?>
	<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<input name="resetform" type="submit" value="<?php echo $lang["clearbutton"]?>" />&nbsp;
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["next"]?>&nbsp;&nbsp;" /><br><br>
	<div class="clearerleft"> </div>
	</div>
	<?php
	}
?>

<?php if ($metadata_read){?>
<div class="Question">
<label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox id="no_exif" name="no_exif" value="yes" <?php if (getval("no_exif","")!="") { ?>checked<?php } ?>>
<div class="clearerleft"> </div>
</div>
<?php } else { ?>
<input type=hidden id="no_exif" name="no_exif" value="no">
<?php } ?>

<?php if($camera_autorotation){ ?>
<div class="Question">
<label for="autorotate"><?php echo $lang["autorotate"]?></label><input type=checkbox id="autorotate" name="autorotate" value="yes" <?php
if ($camera_autorotation_checked) {echo ' checked';}?>>
<div class="clearerleft"> </div>
</div>
<?php } // end if camera autorotation ?>


<?php } ?>

<?php if (!$multiple){?>
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
<?php } else {
# Multiple method of changing resource type.
 ?>
<h1><?php echo $lang["resourcetype"] ?></h1>
<div><input name="editresourcetype" id="editresourcetype" type="checkbox" value="yes" onClick="var q=document.getElementById('editresourcetype_question');if (this.checked) {q.style.display='block';alert('<?php echo $lang["editallresourcetypewarning"] ?>');} else {q.style.display='none';}">&nbsp;<label for="editresourcetype"><?php echo $lang["resourcetype"] ?></label></div>
<div class="Question" style="display:none;" id="editresourcetype_question">
<label for="resourcetype"><?php echo $lang["resourcetype"]?></label>
<select name="resource_type" id="resourcetype" class="stdwidth">
<?php
$types=get_resource_types();
for ($n=0;$n<count($types);$n++)
	{
	?><option value="<?php echo $types[$n]["ref"]?>" <?php if ($resource["resource_type"]==$types[$n]["ref"]) {?>selected<?php } ?>><?php echo $types[$n]["name"]?></option><?php
	}
?></select>
<div class="clearerleft"> </div>
</div>
<?php } ?>


<?php
if ($ref<=0 && getval("single","")=="") { 

# Batch uploads (java/swf/ftp/local) - also ask which collection to add the resource to.
if ($enable_add_collection_on_upload) 
	{
	?>
	<div class="Question">
	<label for="collection_add"><?php echo $lang["addtocollection"]?></label>
	<select name="collection_add" id="collection_add" class="stdwidth"   onchange="if($(this).value==-1){$('collectioninfo').style.display='block';} else {$('collectioninfo').style.display='none';}">
	<?php if ($upload_add_to_new_collection_opt) { ?><option value="-1" <?php if ($upload_add_to_new_collection){ ?>selected <?php }?>>(<?php echo $lang["createnewcollection"]?>)</option><?php } ?>
	<?php if ($upload_do_not_add_to_new_collection_opt) { ?><option value="" <?php if (!$upload_add_to_new_collection){ ?>selected <?php }?>><?php echo $lang["batchdonotaddcollection"]?></option><?php } ?>
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
	<div name="collectioninfo" id="collectioninfo">
	<div name="collectionname" id="collectionname" <?php if ($upload_add_to_new_collection && $upload_add_to_new_collection_opt){ ?> style="display:block;"<?php } else { ?> style="display:none;"<?php } ?>>
	<label for="collection_add"><?php echo $lang["collectionname"]?><?php if ($upload_collection_name_required){?><sup>*</sup><?php } ?></label>
	<input type=text id="entercolname" name="entercolname" class="stdwidth" value='<?php echo htmlentities(stripslashes(getval("entercolname","")), ENT_QUOTES);?>'> 
	</div>
	
	<?php if ($enable_public_collection_on_upload && ($enable_public_collections || checkperm('h')) && !checkperm('b')) { ?>
	<label for="public"><?php echo $lang["access"]?></label>
	<select id="public" name="public" class="shrtwidth"  <?php
		if (checkperm('h')){ // if the user can add to a theme, include the code to toggle the theme selector
		?>
			onchange="if($(this).value==1){$('themeselect').style.display='block';resetThemeLevels();} else {$('themeselect').style.display='none'; clearThemeLevels();}"
		<?php 
		} ?>
	?>>
	<option value="0" selected><?php echo $lang["private"]?></option>
	<option value="1"><?php echo $lang["public"]?></option>
	</select>
	<div class="clearerleft"> </div>
	
	<?php 
	if (checkperm('h')){ 
	// if the user can add to a theme, include the theme selector
	?>
		<!-- select theme if collection is public -->
		<script type="text/javascript" src="../lib/js/update_theme_levels.js"></script>
		<input type="hidden" name="themestring" id="themestring" value="" />
		<div id='themeselect' class='themeselect' style="display:none">
			<?php 
				include_once("ajax/themelevel_add.php"); 
			?>
		</div>
		<!-- end select theme -->
		
		</div>
		
<?php 	
		} // end if checkperm h 
	} // end if public collections enabled
} // end enable_add_collection_on_upload
?>
	</div> <!-- end collectioninfo -->


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

$fields=get_resource_field_data($use,$multiple,true,$originalref);

# if this is a metadata template, set the metadata template title field at the top
if (isset($metadata_template_resource_type)&&(isset($metadata_template_title_field)) && $resource["resource_type"]==$metadata_template_resource_type){
	# recreate fields array, first with metadata template field
	$x=0;
	for ($n=0;$n<count($fields);$n++){
		if ($fields[$n]["resource_type"]==$metadata_template_resource_type){
			$newfields[$x]=$fields[$n];
			$x++;
		}
	}
	# then add the others
	for ($n=0;$n<count($fields);$n++){
		if ($fields[$n]["resource_type"]!=$metadata_template_resource_type){
			$newfields[$x]=$fields[$n];
			$x++;
		}
	}
	$fields=$newfields;
}

?>
<br /><br /><h1><?php echo $lang["resourcemetadata"]?></h1>
<?php
for ($n=0;$n<count($fields);$n++)
	{
	# Should this field be displayed?
	if (!
		(
			# Field is an archive only field
			(($resource["archive"]==0) && ($fields[$n]["resource_type"]==999))
		||
			# Field has write access denied
			(checkperm("F*") && !checkperm("F-" . $fields[$n]["ref"])
			&& !($ref<0 && checkperm("P" . $fields[$n]["ref"])) # Upload only field
			)
		||			
			checkperm("F" . $fields[$n]["ref"])
		||
			($ref<0 && $fields[$n]["hide_when_uploading"] && $fields[$n]["required"]==0)		))
		
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
	?><div><input name="editthis_<?php echo $name?>" id="editthis_<?php echo $n?>" type="checkbox" value="yes" onClick="var q=document.getElementById('question_<?php echo $n?>');var m=document.getElementById('modeselect_<?php echo $n?>');var f=document.getElementById('findreplace_<?php echo $n?>');if (this.checked) {q.style.display='block';m.style.display='block';} else {q.style.display='none';m.style.display='none';f.style.display='none';document.getElementById('modeselectinput_<?php echo $n?>').selectedIndex=0;}">&nbsp;<label for="editthis<?php echo $n?>"><?php echo htmlspecialchars($fields[$n]["title"])?></label></div><?php } ?>

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
	<label for="<?php echo $name?>"><?php if (!$multiple) {?><?php echo htmlspecialchars($fields[$n]["title"])?> <?php if (!$is_template && $fields[$n]["required"]==1) { ?><sup>*</sup><?php } ?><?php } ?></label>
	<?php

	# Define some Javascript for help actions (applies to all fields)
	$help_js="onBlur=\"HideHelp(" . $fields[$n]["ref"] . ");return false;\" onFocus=\"ShowHelp(" . $fields[$n]["ref"] . ");return false;\"";
	
	#hook to modify field type in special case. Returning zero (to get a standard text box) doesn't work, so return 1 for type 0, 2 for type 1, etc.
	$modified_field_type="";
	$modified_field_type=(hook("modifyfieldtype"));
	if ($modified_field_type){$fields[$n]["type"]=$modified_field_type-1;}


	# ----------------------------  Show field -----------------------------------
	$type=$fields[$n]["type"];
	if ($type=="") {$type=0;} # Default to text type.
	$field=$fields[$n];
	include "edit_fields/" . $type . ".php";
	# ----------------------------------------------------------------------------


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


# User upload forms. Work out the correct archive status.
if ($ref<0 && $show_status_and_access_on_upload==false) { 
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

if (!checkperm("F*")) # Only display status/relationships if full write access field access has been granted.
{
?>
<?php if(!hook("replacestatusandrelationshipsheader")){?>
<?php if ($ref>=0) { ?><br><h1><?php echo $lang["statusandrelationships"]?></h1><?php } ?>
<?php } /* end hook replacestatusandrelationshipsheader */ ?>

	<!-- Archive Status -->
	<?php if ($ref<0 && $show_status_and_access_on_upload==false)
		{
        // for Team Center uploads directly to archive status:
		if ($archive==2){ ?><input type=hidden name="archive" id="archive" value="2"><?php }
		}
	else { ?>
	<?php if(!hook("replacestatusselector")){?>
	<?php if ($multiple) { ?><div id="editmultiple_status"><input name="editthis_status" id="editthis_status" value="yes" type="checkbox" onClick="var q=document.getElementById('question_status');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label id="editthis_status_label" for="editthis<?php echo $n?>"><?php echo $lang["status"]?></label></div><?php } ?>
	<div class="Question" id="question_status" <?php if ($multiple) {?>style="display:none;"<?php } ?>>
	<label for="archive"><?php echo $lang["status"]?></label>
	<select class="stdwidth" name="archive" id="archive">
	<?php for ($n=-2;$n<=3;$n++) { ?>
	<?php if (checkperm("e" . $n)) { ?><option value="<?php echo $n?>" <?php if ($resource["archive"]==$n  || $archive==$n) { ?>selected<?php } ?>><?php echo $lang["status" . $n]?></option><?php } ?>
	<?php } ?>
	</select>
	<div class="clearerleft"> </div>
	</div>
	<?php } ?>
	<?php } /* end hook replacestatusselector */?>

<?php hook("beforeaccessselector"); ?>
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

if ($multiple && !$disable_geocoding)
	{
	# Multiple method of changing location.
	 ?>
	<br /><h1><?php echo $lang["location-title"] ?></h1>
	<div><input name="editlocation" id="editlocation" type="checkbox" value="yes" onClick="var q=document.getElementById('editlocation_question');if (this.checked) {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editlocation"><?php echo $lang["location"] ?></label></div>
	<div class="Question" style="display:none;" id="editlocation_question">
	<label for="location"><?php echo $lang["latlong"]?></label>
	<input type="text" name="location" id="location" class="stdwidth">
	<div class="clearerleft"> </div>
	</div>
	<div><input name="editmapzoom" id="editmapzoom" type="checkbox" value="yes" onClick="var q=document.getElementById('editmapzoom_question');if (this.checked) {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editmapzoom"><?php echo $lang["mapzoom"] ?></label></div>
	<div class="Question" style="display:none;" id="editmapzoom_question">
	<label for="mapzoom"><?php echo $lang["mapzoom"]?></label>
	<select name="mapzoom" id="mapzoom">
		<option value=""><?php echo $lang["select"]?></option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
		<option value="13">13</option>
		<option value="14">14</option>
		<option value="15">15</option>
		<option value="16">16</option>
		<option value="17">17</option>
		<option value="18">18</option>
	</select>
	<div class="clearerleft"> </div>
	</div>
	<?php
	} 
?>
	
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<input name="resetform" type="submit" value="<?php echo $lang["clearbutton"]?>" />&nbsp;
	<input <?php if ($multiple) { ?>onclick="return confirm('<?php echo $lang["confirmeditall"]?>');"<?php } ?> name="save" type="submit" value="&nbsp;&nbsp;<?php echo ($ref>0)?$lang["save"]:$lang["next"]?>&nbsp;&nbsp;" /><br><br>
	<div class="clearerleft"> </div>
	</div>
	
</form>
<?php if (!$is_template) { ?><p><sup>*</sup> <?php echo $lang["requiredfield"]?></p><?php } ?>
</div>

<?php if (isset($show_error)){?>
    <script type="text/javascript">
    alert('<?php echo addslashes($lang["requiredfields"]) ?>');
    </script><?php
    }
?>
<!--<p><a href="view.php?ref=<?php echo $ref?>">Back to view</a></p>-->
<?php
include "../include/footer.php";
?>
