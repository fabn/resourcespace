<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
$ref=getvalescaped("ref","");
$status="";

$maxsize="200000000"; #200MB
#ini_set("upload_max_filesize","200M");
#echo "Max size = " . ini_get("upload_max_filesize");

#handle posts
if (array_key_exists("userfile",$_FILES))
    {
	$status=upload_preview($ref);
	redirect("pages/edit.php?refreshcollectionframe=true&ref=" . $ref);
    }
    
include "../include/header.php";
?>

<div class="BasicsBox"> 
<h2>&nbsp;</h2>
<h1><?php echo $lang["uploadpreview"]?></h1>
<p><?php echo text("introtext")?></p>

<form method="post" class="form" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxsize?>">

<br/>
<?php if ($status!="") { ?><?php echo $status?><?php } ?>

<div class="Question">
<label for="userfile"><?php echo $lang["clickbrowsetolocate"]?></label>
<input type=file name=userfile id=userfile>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["fileupload"]?>&nbsp;&nbsp;" />
</div>

<p><a href="edit.php?ref=<?php echo $ref?>">&gt; <?php echo $lang["backtoeditresource"]?></a></p>

</form>
</div>

<?php
include "../include/footer.php";
?>
