<?
$file=realpath(get_resource_path($ref,"",false,$extension));	

# Set up ImageMagick
putenv("MAGICK_HOME=" . $imagemagick_path);
putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib");
putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . $imagemagick_path . "/bin"); # Path

sql_query("update resource set has_image=0 where ref='$ref'");

# FFMPEG goes here.
# If $ffmpeg_path is set...
# Run ffmpeg against $file, if created OK then set $file to the resulting jpeg and proceed with ImageMagick below.
global $ffmpeg_path;
if (isset($ffmpeg_path))
	{
	$command=$ffmpeg_path . "/ffmpeg -i '$file' -f image2 -t 0.001 -ss 3 '" . $file . ".jpg'";
	$output=shell_exec($command);
	#exit($command . "<br>" . $output);
	if (file_exists($file . ".jpg")) {$file=$file . ".jpg";}
	}
	
$ps=sql_query("select * from preview_size where internal=1 or allow_preview=1");
for ($n=0;$n<count($ps);$n++)
	{
	# fetch target width and height
	$tw=$ps[$n]["width"];$th=$ps[$n]["height"];
	$id=$ps[$n]["id"];
	$target=realpath(get_resource_path($ref,$ps[$n]["id"],false,"jpg"));

	if (file_exists($target)) {unlink($target);}

	$command="convert '$file'[0] -resize " . $tw . "x" . $th . " '$target'"; 

	$output=shell_exec($command);
	#exit($output);
	if (file_exists($target) && ($id=="thm"))
		{
		list($sw,$sh) = @getimagesize($target);
		# Set has image, and set the preview extension to JPG rather than PDF
		sql_query("update resource set has_image=1,preview_extension='jpg',thumb_width='$sw',thumb_height='$sh' where ref='$ref'");
		}
	}

?>