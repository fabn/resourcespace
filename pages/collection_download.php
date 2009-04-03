<?php
include "../include/db.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection",""),$k))) {include "../include/authenticate.php";}
include "../include/general.php";
include "../include/search_functions.php";
include "../include/resource_functions.php";

$collection=getvalescaped("collection","");
$size=getvalescaped("size","");
$submitted=getvalescaped("submitted","");
$includetext=getvalescaped("text","false");

# initiate text file
if (($zipped_collection_textfile==true)&&($includetext=="true")){ 
$collectiondata=get_collection($collection);
$text=$collectiondata['name']."
Downloaded ". date("D, F d, Y, H:i:s e")."\r\n
Contents:\r\n\r\n";
}

if ($submitted != "")
	{
	$path="";
	$deletion_array=array();
	
	# No temporary folder? Create one
	if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
	
	# Build a list of files to download
	$result=do_search("!collection" . $collection);
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=get_resource_access($ref);
			
		# Only download resources with proper access level
		if ($access==0 || $access=1)
			{
			if ($access==1) {$size="scr";}
			$pextension = $size == 'original' ? $result[$n]["file_extension"] : 'jpg';
			$p=get_resource_path($ref,true,$size,false,$pextension);
			$usesize=$size;
			if (!file_exists($p))
				{
				# If the file doesn't exist for this size, then the original file must be in the requested size.
				# Try again with the size omitted to get the original.
				$p=get_resource_path($ref,true,"",false,$result[$n]["file_extension"]);
				$usesize="";
				}
		
			# Check file exists and, if restricted access, that the user has access to the requested size.
			if (file_exists($p))
				{
				# when writing metadata, we take an extra security measure by copying the files to tmp

				$tmpfile=write_metadata($p,$ref);
				if($tmpfile!==false && file_exists($tmpfile)){$p=$tmpfile;}		
	
				# if the tmpfile is made, from here on we are working with that. 
				
				# If using original filenames when downloading, copy the file to new location so the name is included.
				if ($original_filenames_when_downloading)	
					{
					if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
					# Retrieve the original file name
					$filename=get_resource_data($ref);
					$filename=$filename["file_path"];
					# now you've got original filename, but it may have an extension in a different letter case. 
					# The system needs to replace the extension to change it to jpg if necessary, but if the original file
					# is being downloaded, and it originally used a different case, then it should not come from the file_extension, 
					# but rather from the original filename itself.
					
					# do an extra check to see if the original filename might have uppercase extension that can be preserved.	
					# also, set extension to "" if the original filename didn't have an extension (exiftool identification of filetypes)
					$pathparts=pathinfo($filename);
					if (isset($pathparts['extension'])){
					if (strtolower($pathparts['extension'])==$pextension){$pextension=".".$pathparts['extension'];}	
					} else {$pextension="";}	
					$filename=$pathparts['filename'].$pextension;

					if (strlen($filename)>0)
						{
						# Only perform the copy if an original filename is set.

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
					$text.= "Ref: ".$ref.$sizetext." \r\n-----------------------------------------------------------------\r\n";
						for ($i=0;$i<count($fields);$i++){
							$value=$fields[$i]["value"];
							$title=str_replace("Keywords - ","",i18n_get_translated($fields[$i]["title"]));
							if ((trim($value)!="")&&(trim($value)!=",")){$text.=wordwrap ("* ".$title.": ".$value."\r\n",65);}
						}
					if(trim($commentdata['comment'])!=""){$text.=wordwrap ("Comment: ".$commentdata['comment']."\r\n",65);}	
					if(trim($commentdata['rating'])!=""){$text.=wordwrap ("Rating: ".$commentdata['rating']."\r\n",65);}	
					$text.= "-----------------------------------------------------------------\r\n\r\n";	
					}
				}
				
				$path.=$p . "\r\n";	
				
				# build an array of paths so we can clean up any exiftool-modified files.
				
				if($tmpfile!==false && file_exists($tmpfile)){$deletion_array[]=$tmpfile;}
				daily_stat("Resource download",$ref);
				resource_log($ref,'d',0);
				}
			}
		}
	if ($path=="") {exit("Nothing to download.");}	

	# write text file, add to zip, and schedule for deletion 	
	if (($zipped_collection_textfile==true)&&($includetext=="true")){
	$textfile = $storagedir . "/tmp/".$collection."-".$collectiondata['name'].$sizetext.".txt";
	$fh = fopen($textfile, 'w') or die("can't open file");
	fwrite($fh, $text);
	fclose($fh);

	$path.=$textfile . "\r\n";	
	$deletion_array[]=$textfile;	
	}

	# Create and send the zipfile
	
	# Build a file name for the zip file.
	$file="collection_" . $collection . "_" . $size . ".zip";

	# Write command parameters to file.
	$cmdfile = $storagedir . "/tmp/zipcmd" . $collection . "_" . $size . ".txt";
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
	$filesize=filesize($storagedir . "/tmp/" . $file);
	
	header("Content-Disposition: attachment; filename=" . $file);
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

<div class="Question">
<label for="downloadsize"><?php echo $lang["downloadsize"]?></label>
<div class="tickset">
<?php

# Work out the maximum access level the user has to the resources in the collection
# If this is 'restricted' then we must restrict the download sizes available.
$maxaccess=collection_max_access($collection);

$sizes=get_all_image_sizes(false,$maxaccess>=1);
$sizes=array_reverse($sizes);

?><select name="size" class="shrtwidth" id="downloadsize">
<option value="original"><?php echo $lang['original'];?></option>
<?php
for ($n=0;$n<count($sizes);$n++)
	{
	?><option value="<?php echo $sizes[$n]["id"]?>"><?php echo i18n_get_translated($sizes[$n]["name"])?></option><?php
	} ?>
	</select>
<div class="clearerleft"> </div></div>
<div class="clearerleft"> </div></div>

<?php if ($zipped_collection_textfile=="true") { ?>
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

