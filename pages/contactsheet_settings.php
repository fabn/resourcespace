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
";

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

<div class="Question">
<label><?php echo $lang["collectionname"]?></label><div class="Fixed"><?php echo $collectiondata['name']?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["display"]?></label>
<select class="shrtwidth" name="sheetstyle" id="sheetstyle" onChange="
	if ($('sheetstyle').value=='list')
		{
		Effect.DropOut('ThumbnailOptions',{duration:0.5});
		}
	else
		{
		Effect.Appear('ThumbnailOptions',{duration:0.5});
		}"">
<option value="thumbnails"><?php echo $lang["thumbnails"]?></option>
<option value="list"><?php echo $lang["list"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["size"]?></label>
<select class="shrtwidth" name="size" id="size"><?php echo $papersize_select ?>
</select>
<div class="clearerleft"> </div>
</div>

<div id="ThumbnailOptions" class="Question">
<label><?php echo $lang["columns"]?></label>
<select class="shrtwidth" name="columns" id="ThumbnailOptions"">
<option value=2>2</option>
<option value=3>3</option>
<option value=4 selected>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>
</select>
</div>

<div class="Question">
<label><?php echo $lang["orientation"]?></label>
<select class="shrtwidth" name="orientation" id="orientation">
<option value="portrait"><?php echo $lang["portrait"]?></option>
<option value="landscape"><?php echo $lang["landscape"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>	
<?php if ($contact_sheet_previews==true){?> <input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["preview"]?>&nbsp;&nbsp;" onClick="previewContactSheet();"/><?php } ?>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../include/footer.php";
?>