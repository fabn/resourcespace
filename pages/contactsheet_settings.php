<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include("../include/collections_functions.php");

$collection=getvalescaped("ref","");
$collectiondata= get_collection($collection);


# Include a couple functions for the Ajax contactsheet update
$headerinsert.="
<script src=\"../lib/js/contactsheet.js\" type=\"text/javascript\"></script>
<script>
contactsheet_previewimage_prefix = '".addslashes($storageurl)."';
</script>
";

?><?php
$bodyattribs="onload=\"jQuery().rsContactSheet('preview');\"";
include "../include/header.php";
?><div class="BasicsBox" >
<div class="BasicsBox" style="float:left;margin-bottom:0;" >
<h1><?php echo $lang["contactsheetconfiguration"]?></h1>

<p><?php echo $lang["contactsheetintrotext"]?></p>

<!-- each time the form is modified, the variables are sent to contactsheet.php with preview=true
 contactsheet.php makes just the first page of the pdf (with col size images) 
 and then thumbnails it for the ajax request. This creates a very small but helpful 
 preview image that can be judged before initiating a download of sometimes several MB.-->
<form method=post name="contactsheetform" id="contactsheetform" action="ajax/contactsheet.php" >
<input type=hidden name="c" value="<?php echo $collection?>">

<!--<div name="error" id="error"></div>-->
</div><div style="clear:left;"> </div>
<div class="BasicsBox" style="width:450px;float:left;margin-top:0;" >
<div class="Question">
	<label><?php echo $lang["collectionname"]?></label><div class="Fixed"><?php echo i18n_get_translated($collectiondata['name'])?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["display"]?></label>
<select class="shrtwidth" name="sheetstyle" id="sheetstyle" onChange="
	if ($('sheetstyle').value=='list')
		{
		document.getElementById('ThumbnailOptions').style.display='none';
		}
	else
		{
		document.getElementById('ThumbnailOptions').style.display='block';
		}
	jQuery().rsContactSheet('revert');	
		">
<option value="thumbnails"><?php echo $lang["thumbnails"]?></option>
<option value="list"><?php echo $lang["list"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["size"]?></label>
<select class="shrtwidth" name="size" id="size" onChange="jQuery().rsContactSheet('revert');"><?php echo $papersize_select ?>
</select>
<div class="clearerleft"> </div>
</div>

<?php if ($contactsheet_sorting){ 
$all_field_info=get_fields_for_search_display(array_unique(array_merge($thumbs_display_fields,$list_display_fields,$config_sheetlist_fields,$config_sheetthumb_fields)));
?>
<div class="Question">
<label><?php echo $lang["sortorder"]?></label>
<select class="shrtwidth" name="orderby" id="orderby" onChange="jQuery().rsContactSheet('preview');">
<option value="relevance" selected><?php echo $lang["collection-order"]?></option>
<option value="date"><?php echo $lang["date"]?></option>
<option value="colour"><?php echo $lang["colour"]?></option>
<option value="resourceid"><?php echo $lang["resourceid"]?></option>
<?php 
foreach ($all_field_info as $sortable_field)
	{ 	
		// don't display the ones we've already covered above.
		if (!($sortable_field["title"] == $lang["date"] || $sortable_field["title"] == $lang["colour"])){
		?><option value="<?php echo $sortable_field['ref']?>"><?php echo $sortable_field["title"]?></option><?php
		}
	}	
?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["sort-type"]?></label>
<select class="shrtwidth" name="sort" id="sort" onChange="jQuery().rsContactSheet('preview');">
<option value="asc" selected><?php echo $lang["ascending"]?></option>
<option value="desc"><?php echo $lang["descending"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<?php } ?>

<div id="ThumbnailOptions" class="Question">
<label><?php echo $lang["columns"]?></label>
<select class="shrtwidth" name="columns" id="ThumbnailOptions" onChange="jQuery().rsContactSheet('revert');"> 
<?php echo $columns_select ?>
</select>
</div>

<div class="Question">
<label><?php echo $lang["orientation"]?></label>
<select class="shrtwidth" name="orientation" id="orientation" onChange="jQuery().rsContactSheet('revert');">
<option value="portrait"><?php echo $lang["portrait"]?></option>
<option value="landscape"><?php echo $lang["landscape"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div name="previewPageOptions" id="previewPageOptions" class="Question" style="display:none">
<label><?php echo $lang['previewpage']?></label>
<select class="shrtwidth" name="previewpage" id="previewpage" onChange="jQuery().rsContactSheet('preview');">
</select>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>	
<?php if ($contact_sheet_previews==true){?> <input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["action-preview"]?>&nbsp;&nbsp;" onClick="jQuery().rsContactSheet('preview');"/><?php } ?>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" />
</div>
</form>
</div></div>
<div >
	<!-- this is the container for some Ajax fun. The image will go here...-->
<?php $cs_size=explode("x",$contact_sheet_preview_size);$height=$cs_size[1];?>
<?php if ($contact_sheet_previews==true){?><div style="float:left;padding:0px -50px 15px 0;height:<?php echo $height?>px;margin-top:-15px;margin-right:-50px"><img id="previewimage" name="previewimage"/></div><?php } ?>

	</div>
	</div>
<?php		
include "../include/footer.php";
?>
