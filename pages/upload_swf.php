<?
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";
$status="";

$collection_add=getvalescaped("collection_add","");

#handle posts
if (array_key_exists("Filedata",$_FILES))
    {
	
    if (getval("replace","")=="")
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
	
		$status=upload_file($ref);
		
		$thumb=get_resource_path($ref,true,"col",false);
		if (file_exists($thumb))
			{
			echo get_resource_path($ref,false,"col",false);
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
				$status=upload_file($ref); # Upload to the specified ref.
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
    
$headerinsert="
<script type=\"text/javascript\" src=\"../lib/swfupload/swfupload.js?<?=$css_reload_key?>\"></script>
<script type=\"text/javascript\" src=\"../lib/swfupload/handlers.js?<?=$css_reload_key?>\"></script>
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
	
	background-image: url(swfupload/cancelbutton.gif);
	background-repeat: no-repeat;
	background-position: -14px 0px;
	float: right;
}
a.progressCancel:hover 
{
	background-position: 0px 0px;
}
</style>

<script type="text/javascript">

var swfu; 

window.onload =  function()
	{

	swfu = new SWFUpload({
		upload_url : "<?=$baseurl?>/pages/upload_swf.php?replace=<?=getval("replace","")?>&collection_add=<?=$collection_add?>&user=<?=urlencode($_COOKIE["user"])?>",
		flash_url : "<?=$baseurl?>/lib/swfupload/swfupload.swf",
		

				// File Upload Settings

				file_size_limit : "2000000",
				file_upload_limit : "0",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				
				button_placeholder_id : "btnBrowse",
				button_image_url : "<?=$baseurl?>/lib/swfupload/XPButtonNoText_160x22.png",
				button_width : 160,
				button_height : 22,
				button_text : "<span class=\"button\"><?=$lang["selectfiles"]?></span>",
				button_text_style : ".button { margin: auto; text-align: center; font-weight: bold; font-family: Helvetica, Arial, sans-serif; font-size: 12px; }",
				button_text_top_padding : 1,

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
		
		
		
	}); 


	};

</script>

<div class="BasicsBox" id="uploadbox"> 
<h2>&nbsp;</h2>
<h1><?=(getval("replace","")!="")?$lang["replaceresourcebatch"]:$lang["fileupload"]?></h1>
<p><?=text("introtext")?></p>

<br/>
<? if ($status!="") { ?><?=$status?><? } ?>
</td></tr>


	<div style="margin: 0px 10px;">
		<div>
			<form>
				<span id="btnBrowse"></span>
			</form>
		</div>
		<div id="divFileProgressContainer" style="height: 75px;"></div>
		<div id="thumbnails"></div>
	</div>

</div>

<?
include "../include/footer.php";
?>