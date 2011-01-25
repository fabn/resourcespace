<?php
include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection","",true),$k))) {include "../include/authenticate.php";}
include "../include/search_functions.php";
include "../include/resource_functions.php";

$collection=getvalescaped("collection","",true);
$size=getvalescaped("size","");
$submitted=getvalescaped("submitted","");
$includetext=getvalescaped("text","false");
$collectiondata=get_collection($collection);

# initiate text file
if (($zipped_collection_textfile==true)&&($includetext=="true")) { 
    $text = $collectiondata['name'] . "\r\n" . 
    $lang["downloaded"] . " " . nicedate(date("Y-m-d H:i:s"), true, true) . "\r\n\r\n" .
    $lang["contents"] . ":\r\n\r\n";
}

# get collection
$result=do_search("!collection" . $collection);

$modified_result=hook("modifycollectiondownload");
if (is_array($modified_result)){$result=$modified_result;}

#this array will store all the available downloads.
$available_sizes=array();

#build the available sizes array
for ($n=0;$n<count($result);$n++)
	{
	$ref=$result[$n]["ref"];
	# Load access level (0,1,2) for this resource
	$access=get_resource_access($result[$n]);
	
	# get all possible sizes for this resource
	$sizes=get_all_image_sizes(false,$access>=1);

	#check availability of original file 
	$p=get_resource_path($ref,true,"",false,$result[$n]["file_extension"]);
	if (file_exists($p) && (($access==0) || ($access==1 && $restricted_full_download)))
		{
		$available_sizes['original'][]=$ref;
		}
	
	# check for the availability of each size and load it to the available_sizes array
	foreach ($sizes as $sizeinfo)
		{
		$size_id=$sizeinfo['id'];
		# get file extension from database or use jpg.
		$pextension = $size == 'original' ? $result[$n]["file_extension"] : 'jpg';
		$p=get_resource_path($ref,true,$size_id,false,$pextension);
		if (file_exists($p)) $available_sizes[$size_id][]=$ref;
		
		}
	}
	
#print_r($available_sizes);
$used_resources=array();
if ($submitted != "")
	{
	$path="";
	$deletion_array=array();
	
	# No temporary folder? Create one
	if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
	
	# Build a list of files to download
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=get_resource_access($result[$n]);
		$use_watermark=check_use_watermark();
		
		# Only download resources with proper access level
		if ($access==0 || $access=1)
			{
			$usesize=$size;
			$pextension = ($size == 'original') ? $result[$n]["file_extension"] : 'jpg';
			($size == 'original') ? $usesize="" : $usesize=$usesize;
			$p=get_resource_path($ref,true,$usesize,false,$pextension,-1,1,$use_watermark);

			# Check file exists and, if restricted access, that the user has access to the requested size.
			if ((file_exists($p) && $access==0) || 
				(file_exists($p) && $access==1 && 
					(image_size_restricted_access($size) || ($usesize='' && $restricted_full_download))))
				{
				
				$used_resources[]=$ref;
				# when writing metadata, we take an extra security measure by copying the files to tmp
				$tmpfile=write_metadata($p,$ref);
				if($tmpfile!==false && file_exists($tmpfile)){$p=$tmpfile;}		
	
				# if the tmpfile is made, from here on we are working with that. 
				
				# If using original filenames when downloading, copy the file to new location so the name is included.
				if ($original_filenames_when_downloading)	
					{
					if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
					# Retrieve the original file name		

					$filename=get_data_by_field($ref,$filename_field);	

					if (strlen($filename)>0)
						{
						# Only perform the copy if an original filename is set.

						# now you've got original filename, but it may have an extension in a different letter case. 
						# The system needs to replace the extension to change it to jpg if necessary, but if the original file
						# is being downloaded, and it originally used a different case, then it should not come from the file_extension, 
						# but rather from the original filename itself.
						
						# do an extra check to see if the original filename might have uppercase extension that can be preserved.	
						# also, set extension to "" if the original filename didn't have an extension (exiftool identification of filetypes)
						$pathparts=pathinfo($filename);
						if (isset($pathparts['extension'])){
						if (strtolower($pathparts['extension'])==$pextension){$pextension=$pathparts['extension'];}	
						} else {$pextension="";}	
						if ($usesize!=""){$append="-".$usesize;}else {$append="";}

						$basename_minus_extension=remove_extension($pathparts['basename']);
						$filename=$basename_minus_extension.$append.".".$pextension;

						if ($prefix_resource_id_to_filename) {$filename=$prefix_filename_string . $ref . "_" . $filename;}
						
						$fs=explode("/",$filename);$filename=$fs[count($fs)-1];
						
						# Copy to a new location
						$newpath=$storagedir . "/tmp/" . $filename;
						copy($p,$newpath);
						
						# Add the temporary file to the post-zip deletion list.
						$deletion_array[]=$newpath;
						
						# Set p so now we are working with this new file
						$p=$newpath;
						}
					}
				
				#Add resource data/collection_resource data to text file
				if (($zipped_collection_textfile==true)&&($includetext=="true")){ 
					if ($size==""){$sizetext="";}else{$sizetext="-".$size;}
					$fields=get_resource_field_data($ref);
					$commentdata=get_collection_resource_comment($ref,$collection);
					if (count($fields)>0){ 
					$text.= $lang["resourceid"] . ": " . $ref . ($sizetext=="" ? "" :" " . $sizetext) . "\r\n-----------------------------------------------------------------\r\n";
						for ($i=0;$i<count($fields);$i++){
							$value=$fields[$i]["value"];
							$title=str_replace("Keywords - ","",$fields[$i]["title"]);
							if ((trim($value)!="")&&(trim($value)!=",")){$text.= wordwrap("* " . $title . ": " . $value . "\r\n", 65);}
						}
					if(trim($commentdata['comment'])!=""){$text.= wordwrap($lang["comment"] . ": " . $commentdata['comment'] . "\r\n", 65);}	
					if(trim($commentdata['rating'])!=""){$text.= wordwrap($lang["rating"] . ": " . $commentdata['rating'] . "\r\n", 65);}	
					$text.= "-----------------------------------------------------------------\r\n\r\n";	
					}
				}
				
				$path.=$p . "\r\n";	
				
				# build an array of paths so we can clean up any exiftool-modified files.
				
				if($tmpfile!==false && file_exists($tmpfile)){$deletion_array[]=$tmpfile;}
				daily_stat("Resource download",$ref);
				resource_log($ref,'d',0);
				
				# update hit count if tracking downloads only
				if ($resource_hit_count_on_downloads) { 
				# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability).
				sql_query("update resource set new_hit_count=greatest(hit_count,new_hit_count)+1 where ref='$ref'");
				} 
				
				}
			}
		}
	if ($path=="") {exit("Nothing to download.");}	
	
	
    # append summary notes about the completeness of the package, write the text file, add to zip, and schedule for deletion 	
    if (($zipped_collection_textfile==true)&&($includetext=="true")){
        $qty_sizes = count($available_sizes[$size]);
        $qty_total = count($result);
        $text.= $lang["status-note"] . ": " . $qty_sizes . " " . $lang["of"] . " " . $qty_total . " ";
        switch ($qty_total) {
        case 0:
            $text.= $lang["resource-0"] . " ";
            break;
        case 1:
            $text.= $lang["resource-1"] . " ";
            break;
        default:
            $text.= $lang["resource-2"] . " ";
            break;
        }

        switch ($qty_sizes) {
        case 0:
            $text.= $lang["were_available-0"] . " ";
            break;
        case 1:
            $text.= $lang["were_available-1"] . " ";
            break;
        default:
            $text.= $lang["were_available-2"] . " ";
            break;
        }
        $text.= $lang["forthispackage"] . ".\r\n\r\n";
    
        foreach ($result as $resource) {
            if (!in_array($resource['ref'],$used_resources)) {
                $text.= $lang["didnotinclude"] . ": " . $resource['ref'] . "\r\n\r\n";
            }
        }

        $textfile = $storagedir . "/tmp/" . $collection . "-" . safe_file_name($collectiondata['name']) . $sizetext . ".txt";
        $fh = fopen($textfile, 'w') or die("can't open file");
        fwrite($fh, $text);
        fclose($fh);

        $path.=$textfile . "\r\n";	
        $deletion_array[]=$textfile;	
    }

	# Create and send the zipfile
	$file = $lang["collectionidprefix"] . $collection . "-" . $size . ".zip";	
		
	# Write command parameters to file.
	$cmdfile = $storagedir . "/tmp/zipcmd" . $collection . "-" . $size . ".txt";
	$fh = fopen($cmdfile, 'w') or die("can't open file");
	fwrite($fh, $path);
	fclose($fh);
		
	# Execute the zip command.
	if ($config_windows)
		{
		# Use the Zzip format to specify the external file
		exec("$zipcommand " . $storagedir . "/tmp/" . $file . " @" . $cmdfile);
		}
	else
		{
		# UNIX et al.
		# Pipe the file containing the filenames to zip
		exec("$zipcommand " . $storagedir . "/tmp/" . $file . " -@ < " . $cmdfile);
		}
	
	# Zip done, add the 
	$deletion_array[]=$cmdfile;

	# Remove temporary files.
	foreach($deletion_array as $tmpfile) {delete_exif_tmpfile($tmpfile);}
	
	# Get the file size of the zip.
	$filesize=@filesize($storagedir . "/tmp/" . $file);
	
	if ($use_collection_name_in_zip_name)
		{
		# Use collection name (if configured)
		$filename = $lang["collectionidprefix"] . $collection . "-" . safe_file_name($collectiondata['name']) . "-" . $size . ".zip";
		}
	else
		{
		# Do not include the collection name in the filename (default)
		$filename= $lang["collectionidprefix"] . $collection . "-" . $size . ".zip";
		}
		
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Content-Type: application/zip");
	header("Content-Length: " . $filesize);
	
	set_time_limit(0);
	readfile($storagedir . "/tmp/" . $file);
	
	# Remove zip file.
	unlink($storagedir . "/tmp/" . $file);
	exit();	
	}
include "../include/header.php";

?>
<div class="BasicsBox">
<h1><?php echo $lang["downloadzip"]?></h1>

<form method=post>
<input type=hidden name="collection" value="<?php echo $collection?>">

<?php 
hook("collectiondownloadmessage");
?>

<div class="Question">
<label for="downloadsize"><?php echo $lang["downloadsize"]?></label>
<div class="tickset">
<?php

$maxaccess=collection_max_access($collection);
$sizes=get_all_image_sizes(false,$maxaccess>=1);

$available_sizes=array_reverse($available_sizes,true);

# analyze available sizes and present options
?><select name="size" class="stdwidth" id="downloadsize"><?php

if (array_key_exists('original',$available_sizes)) {
    ?><option value="original"><?php
    $qty_originals = count($available_sizes['original']);
    echo $lang['original'] . " (" . $qty_originals . " " . $lang["of"] . " " . count($result) . " ";
    switch ($qty_originals) {
    case 0:
        echo $lang["are_available-0"];
        break;
    case 1:
        echo $lang["are_available-1"];
        break;
    default:
        echo $lang["are_available-2"];
        break;
    }
    echo ")";
    ?></option><?php
} ?>

<?php

foreach ($available_sizes as $key=>$value) {
    foreach($sizes as $size){if ($size['id']==$key) {$sizename=$size['name'];}}
	if ($key!='original') {
	    ?><option value="<?php echo $key?>"><?php
	    $qty_values = count($value);
        echo $sizename . " (" . $qty_values . " " . $lang["of"] . " " . count($result) . " ";
        switch ($qty_values) {
        case 0:
            echo $lang["are_available-0"];
            break;
        case 1:
            echo $lang["are_available-1"];
            break;
        default:
            echo $lang["are_available-2"];
            break;
        }
        echo ")";
        ?></option><?php
    }
} ?></select>

<div class="clearerleft"> </div></div>
<div class="clearerleft"> </div></div>

<?php 

if ($zipped_collection_textfile=="true") { ?>
<div class="Question">
<label for="text"><?php echo $lang["zippedcollectiontextfile"]?></label>
<select name="text" class="shrtwidth" id="text">
<option value="true"><?php echo $lang["yes"]?></option>
<option value="false"><?php echo $lang["no"]?></option>
</select>
<div class="clearerleft"> </div><br>
<?php } ?>
<div class="Inline"><input name="submitted" type="submit" value="&nbsp;&nbsp;<?php echo $lang["download"]?>&nbsp;&nbsp;" /></div>
</div>
<div class="clearerleft"> </div>
</div>
</form>

</div>
<?php 
include "../include/footer.php";
?>

