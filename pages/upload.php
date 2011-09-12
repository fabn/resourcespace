<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";

// disable file upload progress bar for WebKit browsers (chrome and safari).
// also doesn't work on Opera. Firefox and IE should work

$checkagent= substr($_SERVER["HTTP_USER_AGENT"],0,250);
$pos = strpos($checkagent, 'AppleWebKit');
$show_progress=false;
if ($pos!==true && extension_loaded('uploadprogress')){
    $show_progress=true;
}

if ($show_progress){
// progress bar if uploadprogress pecl extension is installed
$uid = md5(uniqid(mt_rand()));
$headerinsert.="<script type=\"text/javascript\">
function showprogress(){
        var started='no';
progressbar = new Ajax.PeriodicalUpdater('progress-bar', '".$baseurl."/pages/ajax/get_progress.php',
	{frequency:1,
    parameters: { uid:'".$uid."'},
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


</script>
";
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
<?php

# Define the titles:
if ($ref!="")
	{ # Replace file
	$titleh1 = $lang["replacefile"];
	$titleh2="";
	} 
else if ($archive=="2")
	{ # Add single archived resource
	$titleh1 = $lang["newarchiveresource"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["fileupload"]), $lang["header-upload-subtitle"]);
	}
else
	{ # Add single resource
	$titleh1 = $lang["addresource"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["fileupload"]), $lang["header-upload-subtitle"]);
	}
?>
<?php hook("upload_page_top"); ?>

<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<p><?php echo $lang["intro-single_upload"] ?></p>

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

<form method="post" class="form" enctype="multipart/form-data" <?php if ($show_progress){?>onsubmit="showprogress();"<?php } ?>>

<br/>
<?php if ($status!="") { ?><?php echo $status?><?php } ?>
<div id="invalid" style="display:none;" class="FormIncorrect"><?php echo $lang['invalidextension_mustbe']." ".strtoupper(str_replace(",",", ",$allowed_extensions))?></div>
<div class="Question">

<label for="userfile"><?php echo $lang["file"] ?></label>
<?php if ($show_progress){?>
<input type="hidden" id="uid" name="UPLOAD_IDENTIFIER" value="<?php echo $uid; ?>" >
<?php }?>
<input type=file name=userfile id=userfile size="80">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox <?php if (getval("no_exif","")!=""){?>checked<?php } ?> id="no_exif" name="no_exif" value="yes">
<div class="clearerleft"> </div>
</div>


<?php if ($show_progress){?>
<div class="Question" id="meter" style="display:none;">
<label><?php echo $lang["progress"] ?></label>
<div class="Fixed">
    <div class="Fixed meter-wrap" id="meter-wrap" name="meter-wrap">
        <div class="meter-value" id="meter-value" name="meter-value" style="width: 0%;"></div>
    </div><div id="meter-text"></div>
</div><div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<?php if ($ref!="")  # Replace file -> show a cancel button
		{ ?>
		<input name="createblank" type="submit" value="&nbsp;&nbsp;<?php echo $lang['cancel'] ?>&nbsp;&nbsp;" /><?php
		}
	else # Add single (archived) resource -> step-by-step guide -> show the back button and a no upload button
		{ ?>
		<input name="back" type="button" onclick="history.back(-1)" value="&nbsp;&nbsp;<?php echo $lang["back"] ?>&nbsp;&nbsp;" />
		<input name="createblank" type="submit" value="&nbsp;&nbsp;<?php echo $lang['noupload'] ?>&nbsp;&nbsp;" /><?php
		} ?>
	<input name="save" type="submit" onclick="if (!check(this.form.userfile.value)){$('invalid').style.display='block';return false;}else {$('invalid').style.display='none';}" value="&nbsp;&nbsp;<?php echo $lang["action-upload"]?>&nbsp;&nbsp;" />
</div>

<br />
<p>&gt; <a href="upload_java.php?replace_resource=<?php echo $ref ?>"><?php echo $lang["uploadertryjava"]?></a></p>

</form>
</div>

<?php
hook("upload_page_bottom");

include "../include/footer.php";
?>
