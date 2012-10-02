<?php
include "../include/db.php";
include "../include/authenticate.php"; if (! (checkperm("c") || checkperm("d"))) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";
$status="";
$resource_type=getvalescaped("resource_type","");
$collection_add=getvalescaped("collection_add","");
$collectionname=getvalescaped("entercolname","");
$search=getvalescaped("search","");
$offset=getvalescaped("offset","",true);
$order_by=getvalescaped("order_by","");
$archive=getvalescaped("archive","",true);

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

$allowed_extensions="";
if ($resource_type!="") {$allowed_extensions=get_allowed_extensions_by_type($resource_type);}

$alternative = getvalescaped("alternative",""); # Batch upload alternative files (Java)
$replace = getvalescaped("replace",""); # Replace Resource Batch

$replace_resource=getvalescaped("replace_resource",""); # Option to replace existing resource file

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
if ($_FILES)
	{
	/**
	 * upload.php
	 *
	 * Copyright 2009, Moxiecode Systems AB
	 * Released under GPL License.
	 *
	 * License: http://www.plupload.com/license
	 * Contributing: http://www.plupload.com/contributing
	 */

	// HTTP headers for no cache etc
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	// Settings
	#$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
	$targetDir = get_temp_dir() . DIRECTORY_SEPARATOR . "plupload" . DIRECTORY_SEPARATOR . $session_hash;

	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds

	// 5 minutes execution time
	@set_time_limit(5 * 60);

	// Uncomment this one to fake upload time
	// usleep(5000);

	// Get parameters
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
	$plfilename = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

	// Clean the filename for security reasons
	$plfilename = preg_replace('/[^\w\._]+/', '_', $plfilename);

	// Make sure the fileName is unique but only if chunking is disabled
	if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $plfilename)) {
		$ext = strrpos($plfilename, '.');
		$plfilename_a = substr($plfilename, 0, $ext);
		$plfilename_b = substr($plfilename, $ext);

		$count = 1;
		while (file_exists($targetDir . DIRECTORY_SEPARATOR . $plfilename_a . '_' . $count . $plfilename_b))
			$count++;

		$plfilename = $plfilename_a . '_' . $count . $plfilename_b;
	}

	$plfilepath = $targetDir . DIRECTORY_SEPARATOR . $plfilename;

	// Create target dir
	if (!file_exists($targetDir))
		@mkdir($targetDir,0777,true);

	// Remove old temp files	
	if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
		while (($file = readdir($dir)) !== false) {
			$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

			// Remove temp file if it is older than the max age and is not the current file
			if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$plfilepath}.part")) {
				@unlink($tmpfilePath);
			}
		}

		closedir($dir);
	} else
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		

	// Look for the content type header
	if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
		$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

	if (isset($_SERVER["CONTENT_TYPE"]))
		$contentType = $_SERVER["CONTENT_TYPE"];

	// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
	if (strpos($contentType, "multipart") !== false) {
		if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
			// Open temp file
			$out = fopen("{$plfilepath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen($_FILES['file']['tmp_name'], "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				fclose($in);
				fclose($out);
				@unlink($_FILES['file']['tmp_name']);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	} else {
		// Open temp file
		$out = fopen("{$plfilepath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen("php://input", "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

			fclose($in);
			fclose($out);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}

	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off 
		rename("{$plfilepath}.part", $plfilepath);
	
	
		# Additional ResourceSpace upload code
		
		$plupload_upload_location=$plfilepath;
		if(!hook("initialuploadprocessing"))
			{			
			if ($alternative!="")
				{
				# Upload an alternative file (JUpload only)

				# Add a new alternative file
				$aref=add_alternative_file($alternative,$plfilename);
				
				# Work out the extension
				$extension=explode(".",$plfilepath); $extension=trim(strtolower($extension[count($extension)-1]));

				# Find the path for this resource.
				$path=get_resource_path($alternative, true, "", true, $extension, -1, 1, false, "", $aref);
				
				# Move the sent file to the alternative file location
				
				# PLUpload - file was sent chunked and reassembled - use the reassembled file location
				$result=rename($plfilepath, $path);

				if ($result===false)
					{
					exit("ERROR: File upload error. Please check the size of the file you are trying to upload.");
					}

				chmod($path,0777);
				$file_size = @filesize_unlimited($path);
				
				# Save alternative file data.
				sql_query("update resource_alt_files set file_name='" . escape_check($plfilename) . "',file_extension='" . escape_check($extension) . "',file_size='" . $file_size . "',creation_date=now() where resource='$alternative' and ref='$aref'");
				
				if ($alternative_file_previews_batch)
					{
					create_previews($alternative,false,$extension,false,false,$aref);
					}
				
				echo "SUCCESS";
				exit();
				}
			if ($replace=="" && $replace_resource=="")
				{
				# Standard upload of a new resource

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
				echo "SUCCESS: " . $ref;
				exit();
				}
			elseif ($replace=="" && $replace_resource!="")
				{
				# Replacing an existing resource file
				$status=upload_file($replace_resource,(getval("no_exif","")!=""),false,(getval('autorotate','')!=''));

				echo "SUCCESS: $replace_resource";
				exit();
				}
			else
				{
				# Overwrite an existing resource using the number from the filename.
				
				# Extract the number from the filename
				$plfilename=strtolower(str_replace(" ","_",$plfilename));
				$s=explode(".",$plfilename);
				if (count($s)==2) # does the filename follow the format xxxxx.xxx?
					{
					$ref=trim($s[0]);
					if (is_numeric($ref)) # is the first part of the filename numeric?
						{
						$status=upload_file($ref,(getval("no_exif","")!=""),false,(getval('autorotate','')!='')); # Upload to the specified ref.
						}
					}

				echo "SUCCESS: " . $ref;
				exit();
				}
			}		
		}
		
		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
		

    }
$headerinsert.= "
<link href=\"../lib/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css?" . $css_reload_key . "\" rel=\"stylesheet\" type=\"text/css\" media=\"screen,projection,print\"  />
<script type=\"text/javascript\" src=\"http://bp.yahooapis.com/2.4.21/browserplus-min.js?" . $css_reload_key . "\"></script>
<script type=\"text/javascript\" src=\"../lib/plupload/plupload.full.js?" . $css_reload_key . "\"></script>
<script type=\"text/javascript\" src=\"../lib/plupload/jquery.plupload.queue/jquery.plupload.queue.js?" . $css_reload_key . "\"></script>
";

include "../include/header.php";
?>


<script type="text/javascript">

jQuery(document).ready(function () { 

	
	jQuery("#pluploader").pluploadQueue({
		// General settings
		runtimes : '<?php echo $plupload_runtimes ?>',
		url: 'upload_plupload.php?replace=<?php echo $replace ?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($username."|".$session_hash)?>&resource_type=<?php echo $resource_type?>&no_exif=<?php echo getval("no_exif","")?>&autorotate=<?php echo getval("autorotate","")?>&replace_resource=<?php echo $replace_resource?>',
		chunk_size : '5mb',	
		multiple_queues: true,

		<?php if ($replace_resource > 0){?>
		multi_selection:false,
		<?php }
		if ($allowed_extensions!=""){
			// Specify what files can be browsed for
			$allowed_extensions=str_replace(", ",",",$allowed_extensions);
			$allowedlist=explode(",",trim($allowed_extensions));
			sort($allowedlist);
			$allowed_extensions=implode(",",$allowedlist);
			?>
			filters : [
				{title: "<?php echo $lang["allowedextensions"] ?>",extensions : '<?php echo $allowed_extensions ?>'}
			],<?php 
			} ?>

		// Flash settings
        flash_swf_url: '../lib/plupload/plupload.flash.swf',

		// Silverlight settings
		silverlight_xap_url : '../lib/plupload/plupload.silverlight.xap'

	});


    var uploader = jQuery('#pluploader').pluploadQueue();



	//Show link to java if chunking not supported 
	if(!uploader.features.chunks){jQuery('#plupload_support').slideDown();}

	<?php if ($plupload_autostart){?>
			uploader.bind('FilesAdded', function(up, files) {
				uploader.start();
			}); 
	<?php	}

	 if ($replace_resource > 0){?>
                	uploader.bind('FilesAdded', function(up, files) {
                        	if (uploader.files.length >= 1) {
                                	jQuery('#pluploader_browse').hide("slow");
                        	}
                	});
	<?php }
	else { ?>
	 	//Show diff instructions if supports drag and drop
		if(!uploader.files.length && uploader.features.dragdrop && uploader.settings.dragdrop)	{jQuery('#plupload_instructions').html('<?php echo $lang["intro-plupload_dragdrop"] ?>');}
	<?php }

	if ($usercollection==$collection_add) { ?>uploader.bind('FileUploaded', function(up, file) {top.collections.location.href="<?php echo $baseurl . '/pages/collections.php?nc=' . time() ?>"});
		<?php } ?>

	//add flag so that upload_plupload.php can tell if this is the last file.
	uploader.bind('BeforeUpload', function(up, files) {
		if( (uploader.total.uploaded) == uploader.files.length-1)
					{
					uploader.settings.url = uploader.settings.url + '&lastqueued=true';
					}

	});

	//Change URL if exif box status changes
	jQuery('#no_exif').live('change', function(){
    		if(jQuery(this).is(':checked')){
			uploader.settings.url ='upload_plupload.php?replace=<?php echo $replace ?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($username."|".$session_hash)?>&resource_type=<?php echo $resource_type?>&autorotate=<?php echo getval("autorotate","")?>&replace_resource=<?php echo $replace_resource?>&no_exif=true';
    		}
		else {
			uploader.settings.url ='upload_plupload.php?replace=<?php echo $replace ?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($username."|".$session_hash)?>&resource_type=<?php echo $resource_type?>&autorotate=<?php echo getval("autorotate","")?>&replace_resource=<?php echo $replace_resource?>&no_exif=false';
		}
	});


	<?php if ($plupload_clearqueue){?>
		//remove the completed files once complete 
		uploader.bind('UploadComplete', function(up, files) {
					jQuery('.plupload_done').slideUp('2000', function() {
						uploader.splice();
					});
		});
	<?php } ?>
	
	// Client side form validation
	jQuery('form.pluploadform').submit(function(e) {
		
        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form.pluploadform')[0].submit();
                }
            });
                
            uploader.start();
        } else {
            alert('You must queue at least one file.');
        }

        return false;
    });
		
});
	
		
</script>

<?php
	# Add language support if available
	if (file_exists("../lib/plupload/i18n/" . $language . ".js"))
		{
		echo "<script type=\"text/javascript\" src=\"../lib/plupload/i18n/" . $language . ".js?" . $css_reload_key . "\"></script>";
		}
		?>
		
<div class="BasicsBox" >

<?php if ($alternative!=""){?><p> <a href="edit.php?ref=<?php echo $alternative?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo 
$order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoeditresource"]?></a><br / >
<a href="view.php?ref=<?php echo $alternative?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresourceview"]?></a></p><?php } ?>

<?php if ($replace_resource!=""){?><p> <a href="edit.php?ref=<?php echo $replace_resource?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo 
$order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoeditresource"]?></a><br / >
<a href="view.php?ref=<?php echo $replace_resource ?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresourceview"]?></a></p><?php } ?>

<?php if ($alternative!=""){$resource=get_resource_data($alternative);
	if ($alternative_file_resource_preview){ 
		$imgpath=get_resource_path($resource['ref'],true,"col",false);
		if (file_exists($imgpath)){ ?><img src="<?php echo get_resource_path($resource['ref'],false,"col",false);?>"/><?php }
	}
	if ($alternative_file_resource_title){ 
		echo "<h2>".$resource['field'.$view_title_field]."</h2><br/>";
	}
}

# Define the titles:
if ($replace!="") 
	{
	# Replace Resource Batch
	$titleh1 = $lang["replaceresourcebatch"];
	$titleh2 = "";
	$intro = $lang["intro-plupload_upload-replace_resource"];
	}
elseif ($replace_resource!="")
	{
	# Replace file
	$titleh1 = $lang["replacefile"];
	$titleh2 = "";
	$intro = $lang["intro-plupload_upload-replace_resource"];
	}
elseif ($alternative!="")
	{
	# Batch upload alternative files 
	$titleh1 = $lang["alternativebatchupload"];
	$titleh2 = "";
	$intro = $lang["intro-plupload"];
	}
else
	{
	# Add Resource Batch - In Browser 
	$titleh1 = $lang["addresourcebatchbrowser"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["upload_files"]), $lang["header-upload-subtitle"]);
	$intro = $lang["intro-plupload"];
	}	

?>
<?php hook("upload_page_top"); ?>

<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<div id="plupload_instructions"><p><?php echo $intro ?></p></div>

<?php if ($allowed_extensions!=""){
    $allowed_extensions=str_replace(", ",",",$allowed_extensions);
    $list=explode(",",trim($allowed_extensions));
    sort($list);
    $allowed_extensions=implode(",",$list);
    ?><p><?php echo str_replace_formatted_placeholder("%extensions", str_replace(",",", ",$allowed_extensions), $lang['allowedextensions-extensions'])?></p><?php } ?>

<?php /* Show the import embedded metadata checkbox when uploading a missing file or replacing a file.
In the other upload workflows this checkbox is shown in a previous page. */
if (getvalescaped("upload_a_file","")!="" || getvalescaped("replace_resource","")!=""  || getvalescaped("replace","")!="")
	{ ?>
		<label for="no_exif"><?php echo $lang["no_exif"]?></label><input type=checkbox <?php if (getval("no_exif","")!=""){?>checked<?php } ?> id="no_exif" name="no_exif" value="yes">
		<div class="clearerleft"> </div>
	<?php
	} ?>

<br>

<?php if ($status!="") { ?><?php echo $status?><?php } ?>

<form class="pluploadform" action="upload_plupload.php">
	<div id="pluploader" style="height: 330px;">
	</div>
</form>

<div id="plupload_support" style="display:none">
	<p><?php echo $lang["pluploader_warning"]; ?></p>
	<div id="silverlight" ><p><a href="http://www.microsoft.com/getsilverlight" target="new" > &gt; <?php echo $lang["getsilverlight"] ?></a></p></div>
	<div id="browserplus" ><p><a href="http://browserplus.yahoo.com" target="new" > &gt; <?php echo $lang["getbrowserplus"] ?></a></p></div>
</div>
	<p><a href="upload_java.php?resource_type=<?php echo getvalescaped("resource_type",""); ?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add;?>&entercolname=<?php echo $collectionname;?>&replace=<?php echo urlencode($replace); ?>&no_exif=<?php echo urlencode(getvalescaped("no_exif","")); ?>&autorotate=<?php echo urlencode(getvalescaped('autorotate','')); ?>&replace_resource=<?php echo $replace_resource?>"> &gt; <?php echo $lang["uploadertryjava"]; ?></a></p>
	<p><a href="upload_swf.php?resource_type=<?php echo getvalescaped("resource_type",""); ?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add;?>&entercolname=<?php echo $collectionname;?>&replace=<?php echo urlencode($replace); ?>&no_exif=<?php echo urlencode(getvalescaped("no_exif","")); ?>&autorotate=<?php echo urlencode(getvalescaped('autorotate','')); ?>&replace_resource=<?php echo $replace_resource?>"> &gt; <?php echo $lang["uploadertryflash"]; ?></a></p>



</div>

<?php

hook("upload_page_bottom");

include "../include/footer.php";
?>
