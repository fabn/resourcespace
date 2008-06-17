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
# takes any field that has a write_to entry, explodes it by commas, and tries to write the field
# to each of the Exiftool fields named in write_to
# For example, in the "write_to" box for the Caption field, you could put: Description,Caption-Abstract
# Exiftool will write your RS caption to any EXIF,IPTC,XMP fields that have one of those two names.

if (isset($exiftool_path))
	{
	if (file_exists(stripslashes($exiftool_path) . "/exiftool"))
		{
		
			$command=$exiftool_path."/exiftool ";
			$write_to=get_write_to_headers();

			for($i=0;$i< count($write_to);$i++)
				{
				$field=explode(",",$write_to[$i]['write_to']);
				foreach ($field as $field){
				$command.="-".$field."=\"".get_data_by_field($ref,$write_to[$i]['ref']) . "\" " ;}
				}
			$command.=" $path";

	$output=shell_exec($command) or die("problem writing metadata:". $output);
		}
}


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
	header("Content-Disposition: attachment; filename=" . $ref . $size . "." . $ext);
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

#Replacing Original File:
#If for some reason the downloaded files are corrupted by writing to them, the original file should still exist.
#in other words, files are only modified when they leave. The original file has a "_original" appended to it by exiftool,
#and once the modified file has been downloaded, the original file is restored:

if (file_exists($path."_original")){	
	unlink ($path);
	rename($path."_original", $path);}

exit();

?>