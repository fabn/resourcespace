<?php
include "../include/db.php";
include "../include/authenticate.php"; if (! (checkperm("c") || checkperm("d"))) {exit ("Permission denied.");}
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

if (extension_loaded("uploadprogress")){
	$headerinsert.="<script type=\"text/javascript\" src=\"".$baseurl."/lib/js/jquery-periodical-updater.js\"></script>";
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

if ($show_progress){
// progress bar if uploadprogress pecl extension is installed
$uid = md5(uniqid(mt_rand()));?>

<script type="text/javascript">
function showprogress(){
	
	var progressbar = jQuery.PeriodicalUpdater('<?php echo $baseurl?>/pages/ajax/get_progress.php?uid=<?php echo $uid?>',{
	   method: 'post',          // method; get or post
        data: '',          
        minTimeout: 500,       // starting value for the timeout in milliseconds
        maxTimeout: 1000,       // maximum length of time between requests
        multiplier: 1.5,          // the amount to expand the timeout by if the response hasn't changed (up to maxTimeout)
        type: 'text'           // response type - text, xml, json, etc.  
       
	},function(remoteData, success, xhr, handle) {
            var status=remoteData; console.log('start');
            jQuery('#meter').style.display='block';
            if (status<=100 && started=='yes'){      
				console.log(status);
				jQuery('#meter-value').animate({width:status+'%',duration:.2});
                jQuery('#meter-text').innerHTML=status+'%'; 
            }
            if (status==100 && started=='yes'){
                jQuery('#meter-text').innerHTML='<?php echo $lang['pleasewait']?>';
            }
            if (status<100){      
                started='yes';
            }                         
            });

}

</script>
<?php } ?>
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
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["upload_file"]), $lang["header-upload-subtitle"]);
	}
else
	{ # Add single resource
	$titleh1 = $lang["addresource"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["upload_file"]), $lang["header-upload-subtitle"]);
	}
?>
<?php hook("upload_page_top"); ?>
<?php if ($ref!=""){?><p>
	<a href="edit.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoeditresource"]?></a><br / >
	<a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a>
</p>
<?php } ?>
<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<?php if ($ref!=""){
	$resource=get_resource_data($ref);?>
	<?php if ($replace_file_resource_preview){ 
		$imgpath=get_resource_path($resource['ref'],true,"col",false);
		if (file_exists($imgpath)){ ?><img src="<?php echo get_resource_path($resource['ref'],false,"col",false);?>"/><?php } 
	} ?>
	<?php if ($replace_file_resource_title){ 
		echo "<h2>" . i18n_get_translated($resource['field'.$view_title_field]) . "</h2><br/>";
	}
}?>
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

<form method="post" class="form" enctype="multipart/form-data">

<br/>
<?php if ($status!="") { ?><?php echo $status?><?php } ?>
<div id="invalid" style="display:none;" class="FormIncorrect"><?php echo str_replace_formatted_placeholder("%extensions", str_replace(",",", ",$allowed_extensions), $lang['invalidextension_mustbe-extensions'])?></div>
<div class="Question">

<label for="userfile"><?php echo $lang["file"] ?></label>
<?php if ($show_progress){?>
<input type="hidden" id="uid" name="UPLOAD_IDENTIFIER" value="<?php echo $uid; ?>" >
<?php }?>
<input type=file name=userfile id=userfile size="80">
<div class="clearerleft"> </div>
</div>

<?php /* Show the import embedded metadata checkbox when uploading a missing file or replacing a file.
In the other upload workflows this checkbox is shown in a previous page. */
if (getvalescaped("upload_a_file","")!="" || getvalescaped("replace_file","")!="")
    { ?>
    <div class="Question">
    <label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox <?php if (getval("no_exif","")!=""){?>checked<?php } ?> id="no_exif" name="no_exif" value="yes">
    <div class="clearerleft"> </div>
    </div>
    <?php
    } ?>

<?php if ($show_progress){?>
<div class="Question" id="meter">
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
	<input name="save" type="submit" onclick="<?php if ($show_progress){?>showprogress();<?php } ?> if (!check(this.form.userfile.value)){jQuery('#invalid').fadeIn();return false;}else {jQuery('#invalid').fadeOut();}" value="&nbsp;&nbsp;<?php echo $lang["action-upload"]?>&nbsp;&nbsp;" />
</div>

<?php if (!$hide_uploadertryother) { ?>
<br />
<p>&gt; <a href="upload_java.php?replace_resource=<?php echo $ref ?>"><?php echo $lang["uploadertryjava"]?></a></p>
<?php } ?>

</form>
</div>

<?php
hook("upload_page_bottom");

include "../include/footer.php";
?>
