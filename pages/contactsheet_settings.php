<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include("../include/collections_functions.php");

$collection=getvalescaped("c","");
$collectiondata= get_collection($collection);


# Include a couple functions for the Ajax contactsheet update
$headerinsert.="
<script src=\"../lib/js/contactsheet.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
contactsheet_previewimage_prefix = '".addslashes($storageurl)."';
</script>
<script type=\"text/javascript\">
function revertToPage1(){
$('previewpage').options.length=0;$('previewpage').options[1]=new Option(1,1,selected,selected);$('previewpage').value=1;$('previewPageOptions').style.display='none';
}
</script>
";

?><?php
$bodyattribs="onload=\"previewContactSheet();\"";
include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["contactsheetconfiguration"]?></h1>

<p><?php echo $lang["contactsheetintrotext"]?></p>

<!-- this is the container for some Ajax fun. The image will go here...-->
<?php if ($contact_sheet_previews==true){?><div style="float:right;padding:15px 30px 15px 0;height:300px;"><img id="previewimage" name="previewimage"/></div><?php } ?>

<!-- each time the form is modified, the variables are sent to contactsheet.php with preview=true
 contactsheet.php makes just the first page of the pdf (with col size images) 
 and then thumbnails it for the ajax request. This creates a very small but helpful 
 preview image that can be judged before initiating a download of sometimes several MB.-->
<form method=post name="contactsheetform" id="contactsheetform" action="ajax/contactsheet.php" >
<input type=hidden name="c" value="<?php echo $collection?>">

<!--<div name="error" id="error"></div>-->

<div class="Question">
<label><?php echo $lang["collectionname"]?></label><div class="Fixed"><?php echo $collectiondata['name']?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["display"]?></label>
<select class="shrtwidth" name="sheetstyle" id="sheetstyle" onChange="
	if ($('sheetstyle').value=='list')
		{
		$('ThumbnailOptions').style.display='none';
		}
	else
		{
		$('ThumbnailOptions').style.display='block';
		}
	revertToPage1();	
		">
<option value="thumbnails"><?php echo $lang["thumbnails"]?></option>
<option value="list"><?php echo $lang["list"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["size"]?></label>
<select class="shrtwidth" name="size" id="size" onChange="revertToPage1();"><?php echo $papersize_select ?>
</select>
<div class="clearerleft"> </div>
</div>

<?php if ($contactsheet_sorting){ 
$all_field_info=get_fields_for_search_display(array_unique(array_merge($thumbs_display_fields,$list_display_fields,$config_sheetlist_fields,$config_sheetthumb_fields)));
?>
<div class="Question">
<label><?php echo $lang["sortorder"]?></label>
<select class="shrtwidth" name="orderby" id="orderby">
<option value="relevance"><?php echo $lang["relevance"]?></option>
<option value="date"><?php echo $lang["date"]?></option>
<option value="colour"><?php echo $lang["colour"]?></option>
<option value="resourceid"><?php echo $lang["resourceid"]?></option>
<?php 
foreach ($all_field_info as $sortable_field)
	{ 
	?><option value="<?php echo $sortable_field['ref']?>"><?php echo $sortable_field["title"]?></option><?php
	}	
?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<div id="ThumbnailOptions" class="Question">
<label><?php echo $lang["columns"]?></label>
<select class="shrtwidth" name="columns" id="ThumbnailOptions" onChange="revertToPage1();"> 
<?php echo $columns_select ?>
</select>
</div>

<div class="Question">
<label><?php echo $lang["orientation"]?></label>
<select class="shrtwidth" name="orientation" id="orientation" onChange="revertToPage1();">
<option value="portrait"><?php echo $lang["portrait"]?></option>
<option value="landscape"><?php echo $lang["landscape"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div name="previewPageOptions" id="previewPageOptions" class="Question" style="display:none">
<label><?php echo $lang['previewpage']?></label>
<select class="shrtwidth" name="previewpage" id="previewpage" onChange="previewContactSheet();">
</select>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>	
<?php if ($contact_sheet_previews==true){?> <input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["action-preview"]?>&nbsp;&nbsp;" onClick="previewContactSheet();"/><?php } ?>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../include/footer.php";
?>
