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

$search=getvalescaped("search","");
$offset=getvalescaped("offset","",true);
$order_by=getvalescaped("order_by","");
$archive=getvalescaped("archive","",true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}

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
if (array_key_exists("File0",$_FILES))
    {
	$_FILES["Filedata"]=$_FILES["File0"];


	# ------------------------JUpload chunking support ----------------------------------------
	$jupart=getval("jupart","");
	if ($jupart!="")
		{
		# The sent file is a chunk.
		# JUpload passes two values when sending chunks:
		# - jupart - the part number
		# - jufinal - is this the final part? (0=no, 1=yes)
		
		# Move the chunk to a temporary location.
		$uploadedchunk=$_FILES["Filedata"]['tmp_name'];
		# Since this check is done in get_temp_dir(), omit: if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);} # check tmp dir exists
		$chunkpath=get_temp_dir() . "/jupload_chunk_part_" . $userref . ".tmp";
		if (file_exists($chunkpath)) {unlink ($chunkpath);} # remove any existing chunk file
		$result=move_uploaded_file($uploadedchunk, $chunkpath);
		
		if ($result===false)
			{
			# Upload failed?
			echo "ERROR: PHP move_uploaded_file() failed.";
			exit();
			}
   		chmod($chunkpath,0777); # Make the chunk writeable.

		# Add this to the assembled file.
		$assembledpath=get_temp_dir() . "/jupload_chunk_assembled_" . $userref . ".tmp";
		if ($jupart==1)
			{
			# First part - simply move the chunk to the assembled file location
			
			# Drop any existing file
			if (file_exists($assembledpath)) {unlink ($assembledpath);}
			rename($chunkpath,$assembledpath);
			}
		else
			{
			# Subsequent parts - append the chunk to the main file location
			$f=fopen($assembledpath,"a"); #�Open assembled file for appending.
			fwrite($f,file_get_contents($chunkpath));
			fclose($f);
			unlink($chunkpath); # Delete the temporary chunk file.
			}
			
		if (getval("jufinal","")==0)
			{
			# We are still waiting for the rest of the file. Return success message but do not process yet.
			echo "SUCCESS";
			exit();
			}
		else
			{
			# This is the last chunk.
			# Proceed with processing as normal
			$jupload_alternative_upload_location=$assembledpath;
			}
		}
	# ------------------------ End of chunking support ----------------------------------------
	
	if ($alternative!="")
		{
		# Upload an alternative file (JUpload only)

		# Add a new alternative file
		$filename=$_FILES["Filedata"]['name'];
		$aref=add_alternative_file($alternative,$filename);
		
		# Work out the extension
		$extension=explode(".",$filename); $extension=trim(strtolower($extension[count($extension)-1]));

		# Find the path for this resource.
    	$path=get_resource_path($alternative, true, "", true, $extension, -1, 1, false, "", $aref);
    	
    	# Move the sent file to the alternative file location
    	if (isset($jupload_alternative_upload_location))
    		{
    		# JUpload - file was sent chunked and reassembled - use the reassembled file location
		    $result=rename($jupload_alternative_upload_location, $path);
    		}
		else
			{
			# Standard upload.
		    $result=move_uploaded_file($_FILES["Filedata"]['tmp_name'], $path);
			}

		if ($result===false)
			{
			exit("ERROR: File upload error. Please check the size of the file you are trying to upload.");
			}

		chmod($path,0777);
		$file_size=@filesize($path);
		
		# Save alternative file data.
		sql_query("update resource_alt_files set file_name='" . escape_check($filename) . "',file_extension='" . escape_check($extension) . "',file_size='" . $file_size . "',creation_date=now() where resource='$alternative' and ref='$aref'");
		
		if ($alternative_file_previews_batch)
			{
			create_previews($alternative,false,$extension,false,false,$aref);
			update_disk_usage($ref);
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
		
		echo "SUCCESS";
		exit();
		}
	elseif ($replace=="" && $replace_resource!="")
		{
		# Replacing an existing resource file
		$status=upload_file($replace_resource,(getval("no_exif","")!=""),false,(getval('autorotate','')!=''));

		echo "SUCCESS";
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

		echo "SUCCESS";
		exit();
		}
    }
    
include "../include/header.php";
?>
<?php 
# generate AllowedFileExtensions parameter
$allowed="";
if ($allowed_extensions!=""){ $extensions=explode(",",$allowed_extensions); 
foreach ($extensions as $allowed_extension){
	$allowed.=$allowed_extension."/";
	}	
} 


?>

<?php if ($upload_java_popup && getval("replace","")=="" && getval("alternative","")==""){?>
<script type="text/javascript">

function popUp(URL) {
var date  = new Date();
var id=date.getTime();
var test=window.open(URL,id,'toolbar=no,scrollbars=no,location=no,status=no,menubar=no,resizable=no,width=690,height=560');

if (test==null || typeof(test)=="undefined") {
		alert ('<?php echo $lang['popupblocked']?>');
	} else {
		window.location.href="search.php";
	}
}
popUp('upload_java_popup.php?collection_add=<?php echo $collection_add?>&resource_type=<?php echo $resource_type?>&no_exif=<?php echo urlencode(getvalescaped("no_exif",""))?>&autorotate=<?php echo urlencode(getvalescaped("autorotate",""))?>');

</script>
<?php }?>
<div class="BasicsBox" id="uploadbox"> 
<?php if ($alternative!=""){?><p>
<a href="edit.php?ref=<?php echo $alternative?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoeditresource"]?></a><br / >
<a href="view.php?ref=<?php echo $alternative?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresourceview"]?></a></p><?php } ?>
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
	}
elseif ($replace_resource!="")
	{
	# Replace file
	$titleh1 = $lang["replacefile"];
	$titleh2 = "";
	}
elseif ($alternative!="")
	{
	# Batch upload alternative files (Java)
	$titleh1 = $lang["alternativebatchupload"];
	$titleh2 = "";
	}
else
	{
	# # Add Resource Batch - In Browser (Java - recommended)
	$titleh1 = $lang["addresourcebatchbrowserjava"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["fileupload"]), $lang["header-upload-subtitle"]);
	}
?>
<?php hook("upload_page_top"); ?>

<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<?php
# --------- Quota check -------------
if (overquota())
	{
	?>
	<p><strong><?php echo $lang["manageresources-overquota"] ?></strong></p>
	</div>
	<?php
	include "../include/footer.php";
	exit();
	}
?>

<p><?php echo $lang["intro-java_upload"] ?></p>

<?php if ($allowed_extensions!=""){
    $allowed_extensions=str_replace(", ",",",$allowed_extensions);
    $list=explode(",",trim($allowed_extensions));
    sort($list);
    $allowed_extensions=implode(",",$list);
    ?><p><?php echo $lang['allowedextensions'].": ". strtoupper(str_replace(",",", ",$allowed_extensions))?></p><?php } ?>

<!---------------------------------------------------------------------------------------------------------
-------------------     A SIMPLE AND STANDARD APPLET TAG, to call the JUpload applet  --------------------- 
----------------------------------------------------------------------------------------------------------->
        <applet
	            code="wjhk.jupload2.JUploadApplet"
	            name="JUpload"
	            archive="../lib/jupload/wjhk.jupload.jar"
	            width="640"
	            height="300"
	            mayscript
	            alt="The java pugin must be installed.">
            <!-- param name="CODE"    value="wjhk.jupload2.JUploadApplet" / -->
            <!-- param name="ARCHIVE" value="wjhk.jupload.jar" / -->
            <!-- param name="type"    value="application/x-java-applet;version=1.5" /  -->
            <param name="postURL" value="upload_java.php?replace=<?php echo $replace ?>&replace_resource=<?php echo $replace_resource ?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($_COOKIE["user"])?>&resource_type=<?php echo $resource_type?>&no_exif=<?php echo getval("no_exif","")?>&autorotate=<?php echo getval("autorotate","")?>" />
            <param name="allowedFileExtensions" value="<?php echo $allowed?>">
            <param name="nbFilesPerRequest" value="1">
            <param name="allowHttpPersistent" value="false">
            <param name="debugLevel" value="0">
            <param name="showLogWindow" value="false">
            <param name="lang" value="<?php echo $language?>">
            <param name="maxChunkSize" value="<?php echo $jupload_chunk_size ?>">
         <?php if (isset($jupload_look_and_feel)){ ?>
	    <param name="lookAndFeel" value="<?php echo $jupload_look_and_feel ?>">
	<?php } ?>
            
            <?php if (!$frameless_collections) { 
            # If not using frameless collections, refresh the bottom frame after upload.
            ?>
            <param name="afterUploadTarget" value="collections">
            <param name="afterUploadURL" value="collections.php">
            <?php } ?>

            Java 1.5 or higher plugin required. 
        </applet>
<!-- --------------------------------------------------------------------------------------------------------
----------------------------------     END OF THE APPLET TAG    ---------------------------------------------
---------------------------------------------------------------------------------------------------------- -->
<?php if ($alternative=="" && $replace=="")
	{ # Only show the back button in the step-by-step guide of Add Resource Batch - In Browser (Java - recommended)
	?>
	<div style="margin: 10px 0px;">
		<form>
			<input name="back" type="button" onclick="window.history.go(-1)" value="&nbsp;&nbsp;<?php echo $lang["back"] ?>&nbsp;&nbsp;" />
		</form>
	</div>
	<?php
	}
?>

<?php if ($alternative=="") { ?>
<p><a href="upload_swf.php?resource_type=<?php echo getvalescaped("resource_type",""); ?>&collection_add=<?php echo $collection_add;?>&entercolname=<?php echo$collectionname;?>&replace=<?php echo urlencode(getvalescaped("replace","")); ?>
&no_exif=<?php echo urlencode(getvalescaped("no_exif","")); ?>&autorotate=<?php echo urlencode(getvalescaped("autorotate","")); ?>">&gt; <?php echo $lang["uploadertryflash"]; ?></a></p>
<?php } ?>

<p><a target="_blank" href="http://www.java.com/getjava">&gt; <?php echo $lang["getjava"] ?></a></p>

</div>

<?php

hook("upload_page_bottom");

include "../include/footer.php";
?>
