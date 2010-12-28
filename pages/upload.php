<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";

if (extension_loaded('uploadprogress')){
// progress bar if uploadprogress pecl extension is installed
$uid = md5(uniqid(mt_rand()));
$headerinsert.="<script type=\"text/javascript\">
function showprogress(){
        var started='no';
progressbar = new Ajax.PeriodicalUpdater('progress-bar', 'ajax/get_progress.php?uid=".$uid."',
	{frequency:1,
	onSuccess: function(response) {
            var status=response.responseText;
            $('meter').style.display='block';
            if (status<=100 && started=='yes'){      
                $('meter-value').morph('width:'+status+'%',{duration:.2});
                $('meter-text').innerHTML=status+'%'; 
            }
            if (status==100 && started=='yes'){
                $('meter-text').innerHTML='".$lang['pleasewait']."';
            }
            if (status<100){      
                started='yes';
            }                         
            }
	 });
}

</script>";
}

$ref=getvalescaped("ref","",true);
$resource_type=getvalescaped("resource_type","");
$status="";
if ($ref!=""){
$allowed_extensions=get_allowed_extensions($ref);
}
if ($resource_type!=""){
    $allowed_extensions=get_allowed_extensions_by_type($resource_type);
}
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

if (getval("createblank","")!=""){
    if ($ref==""){
        $ref=copy_resource(0-$userref);
    }
    redirect("pages/edit.php?refreshcollectionframe=true&ref=" . $ref."&search=".urlencode($search)."&offset=".$offset."&order_by=".$order_by."&sort=".$sort."&archive=".$archive);
}

#handle posts
if (array_key_exists("userfile",$_FILES))
    {
	if(verify_extension($_FILES['userfile']['name'],$allowed_extensions))
		{
        if ($ref==""){
            $ref=copy_resource(0-$userref);
        }

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
<?php if (extension_loaded('uploadprogress')){?>
<input type="hidden" id="uid" name="UPLOAD_IDENTIFIER" value="<?php echo $uid; ?>" >
<?php }?>
<input type=file name=userfile id=userfile>
<div class="clearerleft"> </div>
</div>

<?php if (extension_loaded('uploadprogress')){?>
<div class="Question" id="meter" style="display:none;">
<label>Progress</label>
<div class="Fixed">
    <div class="Fixed meter-wrap" id="meter-wrap" name="meter-wrap" style="width:290px;border:1px solid #fff;">
        <div class="meter-value" id="meter-value" name="meter-value" style="width: 0%;"></div>
    </div><div id="meter-text"></div>
</div><div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="Question">
<label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox <?php if (getval("no_exif","")!=""){?>checked<?php } ?> id="no_exif" name="no_exif" value="yes">
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
<input name="createblank" type="submit" value="&nbsp;&nbsp;<?php if ($ref!=""){echo $lang['cancel'];}else{echo $lang['noupload'];}?>&nbsp;&nbsp;" />
<input name="save" type="submit" onclick="showprogress();if (!check(this.form.userfile.value)){$('invalid').style.display='block';return false;}else {$('invalid').style.display='none';}" value="&nbsp;&nbsp;<?php echo $lang["fileupload"]?>&nbsp;&nbsp;" />
</div>

<p><a href="edit.php?ref=<?php echo $ref?>">&gt; <?php echo $lang["backtoeditresource"]?></a></p>

</form>
</div>

<?php
hook("upload_page_bottom");

include "../include/footer.php";
?>
