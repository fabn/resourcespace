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

$allowed_extensions="";
if ($resource_type!="") {$allowed_extensions=get_allowed_extensions_by_type($resource_type);}

$alternative=getval("alternative",""); # Upload alternative resources

# Create a new collection?
if ($collection_add==-1)
	{
	# The user has chosen Create New Collection from the dropdown.
	$collection_add=create_collection($userref,$lang["upload"] . " " . date("ymdHis"));
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
		if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);} # check tmp dir exists
		$chunkpath=$storagedir . "/tmp/jupload_chunk_part_" . $userref . ".tmp";
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
		$assembledpath=$storagedir . "/tmp/jupload_chunk_assembled_" . $userref . ".tmp";
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
			$f=fopen($assembledpath,"a"); # Open assembled file for appending.
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
		
		echo "SUCCESS";
		exit();
		}
    if (getval("replace","")=="")
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
	
		$status=upload_file($ref,true);
		
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
				$status=upload_file($ref); # Upload to the specified ref.
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

<div class="BasicsBox" id="uploadbox"> 
<h2>&nbsp;</h2>
<h1><?php echo (getval("replace","")!="")?$lang["replaceresourcebatch"]:$lang["fileupload"]?></h1>
<p><?php echo text("introtext")?></p>
<?php if ($allowed_extensions!=""){?><p><?php echo $lang['allowedextensions'].": ".$allowed_extensions?></p><?php } ?>

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
            <param name="postURL" value="upload_java.php?replace=<?php echo getval("replace","")?>&alternative=<?php echo $alternative ?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($_COOKIE["user"])?>&resource_type=<?php echo $resource_type?>" />
            <param name="allowedFileExtensions" value="<?php echo $allowed?>">
            <param name="nbFilesPerRequest" value="1">
            <param name="allowHttpPersistent" value="false">
            <param name="debugLevel" value="0">
            <param name="showLogWindow" value="false">
            <param name="lang" value="<?php echo $language?>">
            <param name="maxChunkSize" value="<?php echo $jupload_chunk_size ?>">
            
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


</div>

<?php
include "../include/footer.php";
?>
