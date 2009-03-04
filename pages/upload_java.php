<?php

include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";
$status="";

$collection_add=getvalescaped("collection_add","");

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


<div class="BasicsBox" id="uploadbox"> 
<h2>&nbsp;</h2>
<h1><?php echo (getval("replace","")!="")?$lang["replaceresourcebatch"]:$lang["fileupload"]?></h1>
<p><?php echo text("introtext")?></p>


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
            <param name="postURL" value="upload_java.php?replace=<?php echo getval("replace","")?>&collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($_COOKIE["user"])?>" />
            
            <param name="nbFilesPerRequest" value="1">
            <param name="debugLevel" value="0">
            <param name="showLogWindow" value="false">
            
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