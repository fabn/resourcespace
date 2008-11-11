<?
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
	
	# Build a list of files to download
	$result=do_search("!collection" . $collection);
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=$result[$n]["access"];
		if (checkperm("v") || ($k!=""))
			{
			$access=0; # Permission to access all resources
			}
		else
			{
			if ($access==3)
				{
				# Load custom access level
				$access=get_custom_access($ref,$usergroup);
				}
			}
			
		# Only download resources with proper access level
		if ($access==0)
			{
			$p=get_resource_path($ref,true,$size,false,$result[$n]["file_extension"]);
			if (!file_exists($p))
				{
				# If the file doesn't exist for this size, then the original file must be in the requested size.
				# Try again with the size omitted to get the original.
				$p=get_resource_path($ref,true,"",false,$result[$n]["file_extension"]);
				}
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
					# Retrieve the original file name (strip the path if it's present due to staticsync.php)
					$filename=get_resource_data($ref);
					$filename=$filename["file_path"];
					
					# Replace (instead of appending) original extension with extension of the actual file that is sent
					preg_match('/\.[^\.]+$/', $p, $pext);
					$filename=preg_replace('/\.[^\.]+$/', $pext[0], $filename);

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
				
				$path.=" \"" . $p . "\"";	
				
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

	$path.=" \"" . $textfile . "\"";	
	$deletion_array[]=$textfile;	
	}

	# Create and send the zipfile
	if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
	
	$file="collection_" . $collection . "_" . $size . ".zip";
	exec("$zipcommand " . $storagedir . "/tmp/" . $file . $path );
	$filesize=filesize($storagedir . "/tmp/" . $file);
	
	header("Content-Disposition: attachment; filename=" . $file);
	header("Content-Type: application/zip");
	header("Content-Length: " . $filesize);
	
	set_time_limit(0);
	readfile($storagedir . "/tmp/" . $file);
	
	unlink($storagedir . "/tmp/" . $file);
	foreach($deletion_array as $tmpfile) {delete_exif_tmpfile($tmpfile);}
	exit();
	}
include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["downloadzip"]?></h1>

<form method=post>
<input type=hidden name="collection" value="<?=$collection?>">

<div class="Question">
<label for="downloadsize"><?=$lang["downloadsize"]?></label>
<div class="tickset">
<select name="size" class="shrtwidth" id="downloadsize">
<?
$sizes=get_all_image_sizes();
$sizes[]=array("id" => "", "name" => $lang["collection_download_original"]);
for ($n=0;$n<count($sizes);$n++)
	{
	?><option value="<?=$sizes[$n]["id"]?>"><?=i18n_get_translated($sizes[$n]["name"])?></option><?
	}
?></select>
<div class="clearerleft"> </div></div>
<div class="clearerleft"> </div></div>

<? if ($zipped_collection_textfile=="true") { ?>
<div class="Question">
<label for="text"><?=$lang["zippedcollectiontextfile"]?></label>
<select name="text" class="shrtwidth" id="text">
<option value="true"><?=$lang["yes"]?></option>
<option value="false"><?=$lang["no"]?></option>
</select>
<div class="clearerleft"> </div><br>
<? } ?>
<div class="Inline"><input name="submitted" type="submit" value="&nbsp;&nbsp;<?=$lang["download"]?>&nbsp;&nbsp;" /></div>
</div>
<div class="clearerleft"> </div>
</div>
</form>

</div>
<?
include "../include/footer.php";
?>

