<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
$ref=getvalescaped("ref","",true);
$resource_type=getvalescaped("resource_type","");
$status="";
if ($ref!=""){
$allowed_extensions=get_allowed_extensions($ref);
} else {$allowed_extensions="";}
# fetch the current search 
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

#handle posts
if (array_key_exists("userfile",$_FILES))
    {
    if ($ref==""){
      $ref=copy_resource(0-$userref);
    }
	if(verify_extension($_FILES['userfile']['name'],$allowed_extensions))
		{	
		# Log this			
		daily_stat("Resource upload",$ref);
		resource_log($ref,"u",0);

		$status=upload_file($ref,(getval("no_exif","")!=""),false,(getval("autorotate","")!=""));
		redirect("pages/edit.php?refreshcollectionframe=true&ref=" . $ref."&search=".urlencode($search)."&offset=".$offset."&order_by=".$order_by."&sort=".$sort."&archive=".$archive);
		}	
	}


include "../include/header.php";
?>
<div id="test"></div>
<div class="BasicsBox"> 
<h2>&nbsp;</h2>
<h1><?php echo $lang["fileupload"]?></h1>
<p><?php echo text("introtext")?></p>
<script language="JavaScript">
// Check allowed extensions:
function check(filename) {
	var allowedExtensions='<?php echo $allowed_extensions?>'.toLowerCase();	
	if (allowedExtensions.length==0){return true;}
	var ext = filename.substr(filename.lastIndexOf('.'));
	ext =ext.substr(1).toLowerCase();
	if (allowedExtensions.indexOf(ext)==-1){ return false;} else {return true;}
}
</script>

<form method="post" class="form" enctype="multipart/form-data">

<br/>
<?php if ($status!="") { ?><?php echo $status?><?php } ?>
<div id="invalid" style="display:none;" class="FormIncorrect"><?php echo $lang['invalidextension_mustbe']." ".$allowed_extensions?></div>
<div class="Question">
<label for="userfile"><?php echo $lang["clickbrowsetolocate"]?></label>
<input type=file name=userfile id=userfile>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox id="no_exif" name="no_exif" value="yes">
<div class="clearerleft"> </div>
</div>

<?php if($camera_autorotation){ ?>
<div class="Question">
<label for="autorotate"><?php echo $lang["autorotate"]?></label><input type=checkbox id="autorotate" name="autorotate" value="yes" <?php if ($camera_autorotation_checked) {echo ' checked';}?>>
<div class="clearerleft"> </div>
</div>
<?php } // end if camera autorotation ?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" onclick="if (!check(this.form.userfile.value)){$('invalid').style.display='block';return false;}else {$('invalid').style.display='none';}" value="&nbsp;&nbsp;<?php echo $lang["fileupload"]?>&nbsp;&nbsp;" />
</div>

<p><a href="edit.php?ref=<?php echo $ref?>">&gt; <?php echo $lang["backtoeditresource"]?></a></p>

</form>
</div>

<?php
hook("upload_page_bottom");

include "../include/footer.php";
?>
