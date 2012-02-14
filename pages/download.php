<?php
include "../include/db.php";
include "../include/general.php";
include "../include/resource_functions.php";

if(strlen(getval('direct',''))>0){$direct = true;} else { $direct = false;}

# if direct downloading without authentication is enabled, skip the authentication step entirely
if (!($direct_download_noauth && $direct)){
	# External access support (authenticate only if no key provided, or if invalid access key provided)
	$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref","",true),$k))) {include "../include/authenticate.php";}
}

$ref=getvalescaped("ref","",true);
$size=getvalescaped("size","");
$ext=getvalescaped("ext","");
$alternative=getvalescaped("alternative",-1);
$page=getvalescaped("page",1);
$usage=getval("usage","-1");
$usagecomment=getval("usagecomment","");


$resource_data=get_resource_data($ref);

if ($direct_download_noauth && $direct){
	# if this is a direct download and direct downloads w/o authentication are enabled, allow regardless of permissions
	$allowed = true;
} else {
	# Permissions check
	$allowed=resource_download_allowed($ref,$size,$resource_data["resource_type"]);
}

if (!$allowed)
	{
	# This download is not allowed. How did the user get here?
	exit("Permission denied");
	}

# additional access check, as the resource download may be allowed, but access restriction should force watermark.	
$access=get_resource_access($ref);	
$use_watermark=check_use_watermark($ref);

# If no extension was provided, we fallback to JPG.
if ($ext=="") {$ext="jpg";}

$noattach=getval("noattach","");
$path=get_resource_path($ref,true,$size,false,$ext,-1,$page,$use_watermark && $alternative==-1,"",$alternative);

if (!file_exists($path)) {$path=get_resource_path($ref,true,"",false,$ext,-1,$page,false,"",$alternative);}

if (!file_exists($path) && $noattach!="")
	{
	# Return icon for file (for previews)
	$info=get_resource_data($ref);
	$path="../gfx/" . get_nopreview_icon($info["resource_type"],$ext,"thm");
	}

# writing RS metadata to files: exiftool
if ($noattach=="" && $alternative==-1) # Only for downloads (not previews)
	{
	$tmpfile=write_metadata($path,$ref);
	if ($tmpfile!==false && file_exists($tmpfile)){$path=$tmpfile;}
	}
	
$filesize=filesize_unlimited($path);
header("Content-Length: " . $filesize);

# Log this activity (download only, not preview)
if ($noattach=="")
	{
	daily_stat("Resource download",$ref);
	resource_log($ref,'d',0,$usagecomment,"","",$usage);
	
        hook('moredlactions');

	# update hit count if tracking downloads only
	if ($resource_hit_count_on_downloads) { 
		# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability).
		sql_query("update resource set new_hit_count=greatest(hit_count,new_hit_count)+1 where ref='$ref'");
	} 
	
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
				
			# Fetch from field data or standard table		

			$origfile=get_data_by_field($ref,$filename_field);	
				
			}
		if (strlen($origfile)>0)
			{
			# do an extra check to see if the original filename might have uppercase extension that can be preserved.	
			$pathparts=pathinfo($origfile);
			if (isset($pathparts['extension'])){
				if (strtolower($pathparts['extension'])==$ext){$ext=$pathparts['extension'];}	
			} 
			
			# Use the original filename if one has been set.
			# Strip any path information (e.g. if the staticsync.php is used).
			# append preview size to base name if not the original
			if ($size!=""){$filename=strip_extension(mb_basename($origfile))."-".$size.".".$ext;}
			else {$filename = strip_extension(mb_basename($origfile)).".".$ext;}
			
			if ($prefix_resource_id_to_filename) { $filename = $prefix_filename_string . $ref . "_" . $filename; }
			}
		}
	
	# Remove critical characters from filename
	$filename = preg_replace('/:/', '_', $filename);

        hook("downloadfilename");
	
	if (!$direct){		
		# We use quotes around the filename to handle filenames with spaces.
		header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
	}
	}

# We assign a default mime-type, in case we can find the one associated to the file extension.
$mime="application/octet-stream";


if ($noattach=="")
	{		
	# Get mime type via exiftool if possible
	if (isset($exiftool_path)){
		if (file_exists(stripslashes($exiftool_path) . "/exiftool") || file_exists(stripslashes($exiftool_path) . "/exiftool.exe"))
			{	
			$command=$exiftool_path."/exiftool -s -s -s -t -mimetype " . escapeshellarg($path);
			$mime=run_command($command);
			}	
	}	
	# Override or correct for lack of exiftool with config mappings	
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

# Old method
#readfile($path);

# New method
$blocksize = (2 << 20); //2M chunks
$sent = 0;
$handle = fopen($path, "r");

// Now we need to loop through the file and echo out chunks of file data
while($sent < $filesize)
	{
	echo fread($handle, $blocksize);
	$sent += $blocksize;
	}




#Deleting Exiftool temp File:
if ($noattach=="" && $alternative==-1) # Only for downloads (not previews)
	{
	if (file_exists($tmpfile)){delete_exif_tmpfile($tmpfile);}
	}

exit();

