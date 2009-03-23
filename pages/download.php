<?php
include "../include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}

include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","");
$size=getvalescaped("size","");
$ext=getvalescaped("ext","");
$alternative=getvalescaped("alternative",-1);
$page=getvalescaped("page",1);

# Permissions check
if (!resource_download_allowed($ref,$size))
	{
	# This download is not allowed. How did the user get here?
	exit("Permission denied");
	}

/**
 * Returns filename component of path
 * This version is UTF-8 proof.
 * Thanks to nasretdinov at gmail dot com
 * http://fr2.php.net/manual/en/function.basename.php#85369
 * 
 * @param string $file A path.
 * @access public
 * @return string Returns the base name of the given path.
 */
function mb_basename($file)
	{
	$exploded_path = explode('/',$file);
	return end($exploded_path);
	} // mb_basename()

/**
 * Remove the extension part of a filename.
 * Thanks to phparadise
 * http://fundisom.com/phparadise/php/file_handling/strip_file_extension
 * 
 * @param string $name A file name.
 * @access public
 * @return string Return the file name without the extension part.
 */
function strip_extension($name)
	{
	$ext = strrchr($name, '.');
	if($ext !== false)
		{
		$name = substr($name, 0, -strlen($ext));
		}
	return $name;
	} // strip_extension()

# If no extension was provided, we fallback to JPG.
if ($ext=="") {$ext="jpg";}

$noattach=getval("noattach","");
$path=get_resource_path($ref,true,$size,false,$ext,-1,$page,($size=="scr" && checkperm("w") && $alternative==-1),"",$alternative);

if (!file_exists($path)) {$path=get_resource_path($ref,true,"",false,$ext,-1,$page,false,"",$alternative);}

if (!file_exists($path))
	{
	# Return icon for file (for previews)
	$info=get_resource_data($ref);
	$path="../gfx/type" . $info["resource_type"] . ".gif";
	$ext="gif";
	}

# writing RS metadata to files: exiftool
if ($noattach=="") # Only for downloads (not previews)
	{
	$tmpfile=write_metadata($path,$ref);
	if ($tmpfile!==false && file_exists($tmpfile)){$path=$tmpfile;}
	}
	
$filesize=filesize($path);
header("Content-Length: " . $filesize);

# Log this activity (download only, not preview)
if ($noattach=="")
	{
	daily_stat("Resource download",$ref);
	resource_log($ref,'d',0);
	
	# We compute a file name for the download.
	$filename=$ref . $size . ($alternative>0?"_" . $alternative:"") . "." . $ext;
	
	if ($original_filenames_when_downloading)
		{
		# Use the original filename.
		if ($alternative>0)
			{
			# Fetch from the resource_alt_files alternatives table (this is an alternative file)
			$origfile=get_alternative_file($ref,$alternative);$origfile=$origfile["file_name"];
			}
		else
			{
			# Fetch from the standard table.
			$origfile=get_resource_data($ref);$origfile=$origfile["file_path"];
			}
		if (strlen($origfile)>0)
			{
			#do an extra check to see if the original filename might have uppercase extension that can be preserved.	
			$pathparts=pathinfo($origfile);
			if (strtolower($pathparts['extension'])==$ext){$ext=$pathparts['extension'];}	
				
			# Use the original filename if one has been set.
			# Strip any path information (e.g. if the staticsync.php is used).
			$filename = sprintf('%s.%s', strip_extension(mb_basename($origfile)), $ext);
			if ($prefix_resource_id_to_filename) { $filename = $prefix_filename_string . $ref . "_" . $filename; }
			}
		}
	
	# We use quotes around the filename to handle filenames with spaces.
	header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
	}

# We assign a default mime-type, in case we can find the one associated to the file extension.
$mime="application/octet-stream";

# For online previews, set the mime type.
# We only need to add the types we'll be using for previews here, not all supported file types.
# Mime types are defined in configuration. This allow to easily add new mime types when needed.
#
# Note : Videos... we should re-encode to a single type for video previews at some point (flash file?)
# For now, support the basic types as direct in-browser previews of the source file. DH 20071117
if ($noattach!="")
	{
	if (isset($mime_type_by_extension[$ext]))
		{
		$mime = $mime_type_by_extension[$ext];
		}
	}

# We declare the downloaded content mime type.
header("Content-Type: $mime");

set_time_limit(0);

#echo file_get_contents($path);
# The above required that the downloaded file was read into PHP's memory space first.
# Perhaps this is not the case for readfile().
readfile($path);

#Deleting Exiftool temp File:
if ($noattach=="") # Only for downloads (not previews)
	{
	if (file_exists($tmpfile)){delete_exif_tmpfile($tmpfile);}
	}

exit();

