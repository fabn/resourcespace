<? 

$file=myrealpath(get_resource_path($ref,"",false,$extension)); 


# Set up ImageMagick 
putenv("MAGICK_HOME=" . $imagemagick_path); 
putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . 
$imagemagick_path . "/bin"); # Path 
sql_query("update resource set has_image=0 where ref='$ref'"); 


# Set up target file
$target=myrealpath(get_resource_path($ref,"",false,"jpg")); 
if (file_exists($target)) {unlink($target);}

hook("metadata");

/* ----------------------------------------
	Try InDesign
   ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
if ($extension=="indd")
	{
	$indd_thumb = extract_indd_thumb ($file);
	if ($indd_thumb!="no")
		{
		base64_to_jpeg( $indd_thumb, $target);
		if (file_exists($target)){$newfile = $target;}
		}
	hook("indesign");	
	}


/* ----------------------------------------
	Try text file to JPG conversion
   ----------------------------------------
*/
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
    imagejpeg($im,$target);
	$newfile=$target;
	}


/* ----------------------------------------
	Try FFMPEG for video files
   ----------------------------------------
*/
global $ffmpeg_path; 
if (isset($ffmpeg_path) && !isset($newfile)) 
        { 
         $command=$ffmpeg_path . "/ffmpeg -i \"$file\" -f image2 -t 0.001 -ss 1 \"" . $target . "\""; 
        $output=shell_exec($command); 
        #exit($command . "<br>" . $output); 
        if (file_exists($target)) {$newfile=$target;} 
        } 


/* ----------------------------------------
	Try ImageMagick
   ----------------------------------------
*/
if (!isset($newfile))
	{
	# Locate imagemagick.
    $command=$imagemagick_path . "/bin/convert";
    if (!file_exists($command)) {$command=$imagemagick_path . "/convert.exe";}
    if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
    if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}	

    $prefix="";
        
    # CR2 files need a cr2: prefix
    if ($extension=="cr2") {$prefix="cr2:";}
        
    $command.= " " . $prefix . "\"$file\"[0] +profile icc -colorspace RGB -resize 800x800 \"$target\""; 
    $output=shell_exec($command); 
    if (file_exists($target))
    	{
    	$newfile=$target;
    	}
	}
	
	
# If a file has been created, generate previews just as if a JPG was uploaded.
if (isset($newfile))
	{
	create_previews($ref,false,"jpg");
	}
?>
