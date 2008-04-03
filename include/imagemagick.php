<? 

 

$file=myrealpath(get_resource_path($ref,"",false,$extension)); 
# Set up ImageMagick 
putenv("MAGICK_HOME=" . $imagemagick_path); 
putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . 
$imagemagick_path . "/bin"); # Path 
sql_query("update resource set has_image=0 where ref='$ref'"); 
# FFMPEG goes here. 
# If $ffmpeg_path is set... 
# Run ffmpeg against $file, if created OK then set $file to the resulting jpeg and proceed with ImageMagick below. 
global $ffmpeg_path; 
if (isset($ffmpeg_path)) 
        { 
        $tmpfile=myrealpath(get_resource_path($ref,"tmp",true,$extension)); 
		if (file_exists($tmpfile . ".jpg")) {unlink($tmpfile . ".jpg");} 	
         $command=$ffmpeg_path . "/ffmpeg -i \"$file\" -f image2 -t 0.001 -ss 1 \"" . $tmpfile . ".jpg\""; 
        $output=shell_exec($command); 
        #exit($command . "<br>" . $output); 
        if (file_exists($tmpfile . ".jpg")) {$file=$tmpfile . ".jpg";} 
        } 
        
        
# Support text files simply by rendering them on a JPEG.
if ($extension=="txt")
	{
	$text=wordwrap(file_get_contents($file),90);
	$width=600;$height=800;
	$font="gfx/fonts/vera.ttf";
	$im=imagecreatetruecolor($width,$height);
	$col=imagecolorallocate($im,255,255,255);
	imagefilledrectangle($im,0,0,$width,$height,$col);
	$col=imagecolorallocate($im,0,0,0);
	imagettftext($im,9,0,10,25,$col,$font,$text);
    $tmpfile=myrealpath(get_resource_path($ref,"tmp2",true,$extension)); 
    imagejpeg($im,$tmpfile . ".jpg");
	$file=$tmpfile . ".jpg";
	}
        
$ps=sql_query("select * from preview_size where internal=1 or allow_preview=1"); 
for ($n=0;$n<count($ps);$n++) 
        { 
        # fetch target width and height 
        $tw=$ps[$n]["width"];$th=$ps[$n]["height"]; 
        $id=$ps[$n]["id"]; 
        $target=myrealpath(get_resource_path($ref,$ps[$n] 
["id"],false,"jpg")); 
        if (file_exists($target)) {unlink($target);} 
        $command=$imagemagick_path . "/bin/convert";
        if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}
        $command.= " \"$file\"[0] -colorspace RGB -resize " . $tw . "x" . $th . " \"$target\""; 
        $output=shell_exec($command); 
        #exit($command . "<br>" . $output); 
        if (file_exists($target) && ($id=="thm")) 
                { 
                list($sw,$sh) = @getimagesize($target); 
                # Set has image, and set the preview extension to JPG rather than PDF 
                sql_query("update resource set 
has_image=1,preview_extension='jpg',thumb_width='$sw',thumb_height='$sh' 
where ref='$ref'"); 
                } 
        } 

#Support for Sort By Color feature : extracting color data for files processed by ImageMagick:
$image = get_resource_path($ref,"thm",false);
if (file_exists($image))
	{
	$target = imagecreatefromjpeg($image);
	extract_mean_colour($target,$ref);
	imagedestroy($target); 
	}


?>
