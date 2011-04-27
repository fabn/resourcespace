<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";
$status="";
$resource_type=getvalescaped("resource_type","");
$collection_add=getvalescaped("collection_add","");
$collectionname=getvalescaped("entercolname","");

$allowed_extensions="";
if ($resource_type!="") {$allowed_extensions=get_allowed_extensions_by_type($resource_type);}

$replace = getvalescaped("replace",""); # Replace Resource Batch

# Create a new collection?
if ($collection_add==-1)
	{
	# The user has chosen Create New Collection from the dropdown.
	if ($collectionname==""){$collectionname=$lang["upload"] . " " . date("ymdHis");}
	$collection_add=create_collection($userref,$collectionname);
	if (getval("public",'0') == 1)
		{
		collection_set_public($collection_add);
		}
	if (strlen(getval("themestring",'')) > 0)
		{
		$themearr = explode('||',getval("themestring",''));
		collection_set_themes($collection_add,$themearr);
		}
	}
if ($collection_add!="")
	{
	# Switch to the selected collection (existing or newly created) and refresh the frame.
 	set_user_collection($userref,$collection_add);
 	refresh_collection_frame($collection_add);
 	}	

#handle posts
if (array_key_exists("Filedata",$_FILES))
    {
	
    if ($replace=="")
    	{
		# New resource
	
		
		$ref=copy_resource(0-$userref); # Copy from user template
		
		# Add to collection?
		if ($collection_add!="")
			{
			add_resource_to_collection($ref,$collection_add);
			}
			
		# Log this			
		daily_stat("Resource upload",$ref);
		resource_log($ref,"u",0);
	
		$status=upload_file($ref,(getval("no_exif","")!=""),false,(getval('autorotate','')!=''));
		
		$thumb=get_resource_path($ref,true,"col",false);
		if (file_exists($thumb))
			{
			echo get_resource_path($ref,false,"col",false);
			}
		else
			{
			echo "../gfx/type1_col.gif";
			}
		exit();
		}
	else
		{
    	# Overwrite an existing resource using the number from the filename.
		
		# Extract the number from the filename
	    $filename=strtolower(str_replace(" ","_",$_FILES['Filedata']['name']));
		$s=explode(".",$filename);
		if (count($s)==2) # does the filename follow the format xxxxx.xxx?
			{
			$ref=trim($s[0]);
			if (is_numeric($ref)) # is the first part of the filename numeric?
				{
				$status=upload_file($ref,(getval("no_exif","")!=""),false,(getval('autorotate','')!='')); # Upload to the specified ref.
				}
			}
		$thumb=get_resource_path($ref,true,"col",false);
		if (file_exists($thumb))
			{
			echo get_resource_path($ref,false,"col",false);
			}
		exit();
		}
    }
    
$headerinsert.= "
<script type=\"text/javascript\" src=\"../lib/swfupload/swfupload.js?" . $css_reload_key . "\"></script>
<script type=\"text/javascript\" src=\"../lib/swfupload/handlers.js?" . $css_reload_key . "\"></script>
<script type=\"text/javascript\">
queued_too_many_files = '" . preg_replace("/\r?\n/", "\\n", addslashes($lang["queued_too_many_files"])) . "';
creatingthumbnail = '" . preg_replace("/\r?\n/", "\\n", addslashes($lang["creatingthumbnail"])) . "';
uploading = '" . preg_replace("/\r?\n/", "\\n", addslashes($lang["uploading"])) . "';
thumbnailcreated = '" . preg_replace("/\r?\n/", "\\n", addslashes($lang["thumbnailcreated"])) . "';
done = '" . preg_replace("/\r?\n/", "\\n", addslashes($lang["done"])) . "';
stopped = '" . preg_replace("/\r?\n/", "\\n", addslashes($lang["stopped"])) . "';
</script>
";

include "../include/header.php";
?>
<style>
.progressWrapper {
	width: 357px;
	overflow: hidden;
}
.progressContainer {
	margin: 5px;
	padding: 4px;
	
	border: solid 1px #E8E8E8;
	background-color: #F7F7F7;
	
	overflow: hidden;
}
.red /* Error */
{
	border: solid 1px #B50000;
	background-color: #FFEBEB;
}
.green /* Current */ 
{
	border: solid 1px #DDF0DD;
	background-color: #EBFFEB;
}
.blue /* Complete */
{
	border: solid 1px #CEE2F2;
	background-color: #F0F5FF;
}

.progressName {
	font-size: 8pt;
	font-weight: bold;
	color: #555555;
	
	width: 323px;
	height: 14px;
	text-align: left;
	white-space: nowrap;
	overflow: hidden;
}
.progressBarInProgress,
.progressBarComplete,
.progressBarError {
	font-size: 0px;
	width: 0%;
	height: 2px;
	background-color: blue;
	margin-top: 2px;
}
.progressBarComplete {
	width: 100%;
	background-color: green;
	visibility: hidden;
}
.progressBarError {
	width: 100%;
	background-color: red;
	visibility: hidden;
}
.progressBarStatus {
	margin-top: 2px;
	width: 337px;
	font-size: 7pt;
	font-family: Verdana;
	text-align: left;
	white-space: nowrap;
}
a.progressCancel,
a.progressCancel:link,
a.progressCancel:active,
a.progressCancel:visited,
a.progressCancel:hover
{
	font-size: 0px;
	display: block;
	height: 14px;
	width: 14px;
	
	background-image: url(../lib/swfupload/cancelbutton.gif);
	background-repeat: no-repeat;
	background-position: -14px 0px;
	float: right;
}
a.progressCancel:hover 
{
	background-position: 0px 0px;
}
</style>
<?php 
# Generate file_types parameter for swfupload
$allowed="";
if ($allowed_extensions!=""){ $extensions=explode(",",$allowed_extensions); 
foreach ($extensions as $allowed_extension){
	$allowed.="*.".$allowed_extension.";";
	}	
} 
if ($allowed==""){$allowed="*.*";}

?>
<script type="text/javascript">

var swfu; 

window.onload =  function()
	{

	swfu = new SWFUpload({
		upload_url : "<?php echo $baseurl?>/pages/upload_swf.php?replace=<?php echo $replace ?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($_COOKIE["user"])?>&resource_type=<?php echo $resource_type?>&no_exif=<?php echo getval("no_exif","") ?>&autorotate=<?php echo getval('autorotate','') ?>",
		flash_url : "<?php echo $baseurl?>/lib/swfupload/swfupload.swf",
		

				// File Upload Settings

				file_size_limit : "2000000",
				file_upload_limit : "0",
				file_types : "<?php echo $allowed?>",
				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccessWrapper,
				upload_complete_handler : uploadComplete,
				
				button_placeholder_id : "btnBrowse",
				button_image_url : "<?php echo $baseurl?>/lib/swfupload/XPButtonNoText_160x22.png",
				button_width : 160,
				button_height : 22,
				button_text : "<span class=\"button\"><?php echo $lang["action-upload"] . "..." ?></span>",
				button_text_style : ".button { margin: auto; text-align: center; font-weight: bold; font-family: Helvetica, Arial, sans-serif; font-size: 12px; }",
				button_text_top_padding : 1,

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
		
		
		
	}); 


	};

function uploadSuccessWrapper(file,server)
	{
	<?php if ($usercollection==$collection_add) { ?>
	top.collections.location.href="<?php echo $baseurl ?>/pages/collections.php?nc=<?php echo time() ?>";
	<?php } ?>
	uploadSuccess(file,server);
	}
function debug()
	{
	// SWF needs this here for some reason.
	}

</script>

<div class="BasicsBox" id="uploadbox"> 
<?php

# Define the titles:
if ($replace!="") 
	{
	# Replace Resource Batch
	$titleh1 = $lang["replaceresourcebatch"];
	$titleh2 = "";
	}
else
	{
	# Add Resource Batch - In Browser (Flash)
	$titleh1 = $lang["addresourcebatchbrowser"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["fileupload"]), $lang["header-upload-subtitle"]);
	}
?>

<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<p><?php echo $lang["intro-swf_upload"] ?></p>

<?php if ($allowed_extensions!=""){
    $allowed_extensions=str_replace(", ",",",$allowed_extensions);
    $list=explode(",",trim($allowed_extensions));
    sort($list);
    $allowed_extensions=implode(",",$list);
    ?><p><?php echo $lang['allowedextensions'].": ". strtoupper(str_replace(",",", ",$allowed_extensions));?></p><?php } ?>

<br/>
<?php if ($status!="") { ?><?php echo $status?><?php } ?>


	<div style="margin: 0px 10px;">
		<div>
			<form>
				<?php if ($replace=="")
					{ # Only show the back button in the step-by-step guide of Add Resource Batch - In Browser (Flash)
					?><input name="back" type="button" onclick="window.history.go(-1)" value="&nbsp;&nbsp;<?php echo $lang["back"]?>&nbsp;&nbsp;" /><?php
					}
				?><span id="btnBrowse"></span>
			</form>
		</div>
		<div id="divFileProgressContainer" style="height: 75px;"></div>
		<div id="thumbnails"></div>
	</div>

<p><a href="upload_java.php?resource_type=<?php echo getvalescaped("resource_type",""); ?>&collection_add=<?php echo $collection_add;?>&entercolname=<?php echo$collectionname;?>&replace=<?php echo urlencode($replace); ?>&no_exif=<?php echo urlencode(getvalescaped("no_exif","")); ?>&autorotate=<?php echo urlencode(getvalescaped('autorotate','')); ?>">&gt; <?php echo $lang["uploadertryjava"]; ?></a></p>

<p><a target="_blank" href="http://get.adobe.com/flashplayer/">&gt; <?php echo $lang["getflash"] ?></a></p>

</div>

<?php

hook("upload_page_bottom");

include "../include/footer.php";
?>
