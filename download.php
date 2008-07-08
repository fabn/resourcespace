<?
include "include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "include/authenticate.php";}

include "include/general.php";
include "include/resource_functions.php";

$ref=getvalescaped("ref","");
$size=getvalescaped("size","");
$ext=getvalescaped("ext","");
$page=getvalescaped("page",1);
if ($ext=="") {$ext="jpg";}

$noattach=getval("noattach","");
$path=get_resource_path($ref,$size,false,$ext,-1,$page,($size=="scr" && checkperm("w")));
if (!file_exists($path)) {$path=get_resource_path($ref,"",false,$ext,-1,$page);}

if (!file_exists($path))
	{
	# Return icon for file (for previews)
	$info=get_resource_data($ref);
	$path="gfx/type" . $info["resource_type"] . ".gif";
	$ext="gif";
	}

# writing RS metadata to files: exiftool

$tmpfile=write_metadata($path,$ref);
if (file_exists($tmpfile)){$path=$tmpfile;}

$filesize=filesize($path);
header("Content-Length: " . $filesize);

# Log this activity (download only, not preview)
if ($noattach=="")
	{
	daily_stat("Resource download",$ref);
	resource_log($ref,'d',0);
	}

if ($noattach=="") 
	{
	$filename=$ref . $size . "." . $ext;
	
	if ($original_filenames_when_downloading)
		{
		# Use the original filename.
		$origfile=get_resource_data($ref);$origfile=$origfile["file_path"];
		if ($origfile!="")
			{
			# Use the original filename if one has been set.
			# Strip any path information (e.g. if the staticsync.php is used).
			$fs=explode("/",$origfile);$filename=$fs[count($fs)-1];
			}
		}
		
	header("Content-Disposition: attachment; filename=" . $filename);
	header("Content-Type: application/octet-stream");
	}
else
	{
	$mime="application/octet-stream";
	
	# For online previews, set the mime type.
	# We only need to add the types we'll be using for previews here, not all supported file types.

	# Videos... we should re-encode to a single type for video previews at some point (flash file?)
	# For now, support the basic types as direct in-browser previews of the source file. DH 20071117
	if ($ext=="mov") {$mime="video/quicktime";}
	if ($ext=="3gp") {$mime="video/3gpp";}
	if ($ext=="mpg") {$mime="video/mpeg";}
	if ($ext=="mp4") {$mime="video/mp4";}
	if ($ext=="avi") {$mime="video/msvideo";}
	
	# Audio files
	if ($ext=="mp3") {$mime="audio/mpeg";}
	if ($ext=="wav") {$mime="audio/x-wav";}
	
	
	if (($ext=="jpg") || ($ext=="jpeg")) {$mime="image/jpeg";}
	if ($ext=="gif") {$mime="image/gif";}
	if ($ext=="png") {$mime="image/png";}	

	header("Content-Type: $mime");
	}
set_time_limit(0);

echo file_get_contents($path);

#Deleting Exiftool temp File:

if (file_exists($tmpfile)){delete_exif_tmpfile($tmpfile);}
exit();

?>