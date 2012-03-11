<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
$ref=getvalescaped("ref","",true);
$status="";

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

$maxsize="200000000"; #200MB
#ini_set("upload_max_filesize","200M");
#echo "Max size = " . ini_get("upload_max_filesize");

#handle posts
if (array_key_exists("userfile",$_FILES))
    {
	$status=upload_preview($ref);
	redirect("pages/edit.php?refreshcollectionframe=true&ref=" . $ref."&search=".urlencode($search)."&offset=".$offset."&order_by=".$order_by."&sort=".$sort."&archive=".$archive);
    }
    
include "../include/header.php";
?>

<div class="BasicsBox"> 
<h2>&nbsp;</h2>
<h1><?php echo $lang["uploadpreview"]?></h1>
<p><?php echo text("introtext")?></p>
<script language="JavaScript">
// Check allowed extensions:
function check(filename) {
	var allowedExtensions='jpg'.toLowerCase();	
	if (allowedExtensions.length==0){return true;}
	var ext = filename.substr(filename.lastIndexOf('.'));
	ext =ext.substr(1).toLowerCase();
	if (allowedExtensions.indexOf(ext)==-1){ return false;} else {return true;}
}
</script>
<form method="post" class="form" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxsize?>">

<br/>
<?php if ($status!="") { ?><?php echo $status?><?php } ?>
<div id="invalid" style="display:none;" class="FormIncorrect"><?php echo str_replace_formatted_placeholder("%extensions", "JPG", $lang['invalidextension_mustbe-extensions']); ?></div>
<div class="Question">
<label for="userfile"><?php echo $lang["clickbrowsetolocate"]?></label>
<input type=file name=userfile id=userfile>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" onclick="if (!check(this.form.userfile.value)){$('invalid').style.display='block';return false;}else {$('invalid').style.display='none';}" value="&nbsp;&nbsp;<?php echo $lang["upload_file"]?>&nbsp;&nbsp;" />
</div>

<p><a href="edit.php?ref=<?php echo $ref?>">&gt; <?php echo $lang["backtoeditresource"]?></a></p>

</form>
</div>

<?php
include "../include/footer.php";
?>
