<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; 
include "../../../include/general.php";
include("../../../include/resource_functions.php");

$ref=getvalescaped("ref","");
$resource=get_resource_data($ref);
# Include a couple functions for the Ajax contactsheet update
$headerinsert.="
<script src=\"../lib/js/annotate_pdf_preview.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
annotate_previewimage_prefix = '".addslashes($storageurl)."';
</script>
<script type=\"text/javascript\">
function revertToPage1(){
$('previewpage').options.length=0;$('previewpage').options[1]=new Option(1,1,selected,selected);$('previewpage').value=1;$('previewPageOptions').style.display='none';
}
</script>
";?>

<?php
$bodyattribs="onload=\"previewAnnotations();\"";
include "../../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["annotatepdfconfig"]?></h1>

<p><?php echo $lang["annotatepdfintrotext"]?></p>

<div style="float:right;padding:15px 30px 15px 0;height:300px;"><img id="previewimage" name="previewimage"/></div>

<form method=post name="annotateform" id="annotateform" action="annotate_pdf_gen.php" >
<input type=hidden name="ref" value="<?php echo $ref?>">

<!--<div name="error" id="error"></div>-->

<div class="Question">
<label><?php echo $lang["resourcetitle"]?></label><div class="Fixed"><?php echo $resource['field'.$view_title_field]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["size"]?></label>
<select class="shrtwidth" name="size" id="size" onChange="revertToPage1();"><?php echo $papersize_select ?>
</select>
<div class="clearerleft"> </div>
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
<select class="shrtwidth" name="previewpage" id="previewpage" onChange="previewAnnotations();">
</select>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>	
<input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["preview"]?>&nbsp;&nbsp;" onClick="previewAnnotations();"/>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../../include/footer.php";
?>
