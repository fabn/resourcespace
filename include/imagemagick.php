<? 
#
# This file contains the integration code with ImageMagick
# It also contains integration code for those types that we need ImageMagick to be able to process
# for example types that use GhostScript or FFmpeg.
#

global $imagemagick_path,$imagemagick_preserve_profiles,$imagemagick_quality,$pdf_pages;

if (!$previewonly)
	{
	$file=get_resource_path($ref,true,"",false,$extension); 
	$target=get_resource_path($ref,true,"",false,"jpg"); 
	}
else
	{
	# Use temporary preview source/destination - user has uploaded a file intended to replace the previews only.
	$file=get_resource_path($ref,true,"tmp",false,$extension);
	$target=get_resource_path($ref,true,"tmp",false,"jpg");
	}
	
# Set up ImageMagick 
putenv("MAGICK_HOME=" . $imagemagick_path); 
putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . 
$imagemagick_path . "/bin"); # Path 
sql_query("update resource set has_image=0 where ref='$ref'"); 


# Set up target file
if (file_exists($target)) {unlink($target);}


# Locate imagemagick.
 $command=$imagemagick_path . "/bin/convert";
 if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
 if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
 if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility. $command'");}	


hook("metadata");

/* ----------------------------------------
	Try InDesign
   ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
if ($extension=="indd")
	{
#This is how to use exiftool to extract InDesign thumbnails:
	global $exiftool_path;
	if (isset($exiftool_path))
	{
	shell_exec($exiftool_path.'/exiftool -ScanforXMP -f -ThumbnailsImage -b '.$file.' > '.$target);
	}
#Or the old way:	
 	else {
	$indd_thumb = extract_indd_thumb ($file);
	if ($indd_thumb!="no")
		{
		base64_to_jpeg( $indd_thumb, $target);
		if (file_exists($target)){$newfile = $target;}
		}
		}
		
	hook("indesign");	
	}

/* ----------------------------------------
	Try OpenDocument Format
   ----------------------------------------
*/
if (($extension=="odt") || ($extension=="ott") || ($extension=="odg") || ($extension=="otg") || ($extension=="odp") || ($extension=="otp") || ($extension=="ods") || ($extension=="ots") || ($extension=="odf") || ($extension=="otf") || ($extension=="odm") || ($extension=="oth"))

	{
shell_exec("unzip -p $file \"Thumbnails/thumbnail.png\" > $target");
$odcommand=$command . " \"$target\"[0]  \"$target\""; 
				$output=shell_exec($odcommand); 
	}



/* ----------------------------------------
	Try Microsoft OfficeOpenXML Format
	Also try Micrsoft XPS... the sample document I've seen uses the same path for the preview, 
	so it will likely work in most cases, but I think the specs allow it to go anywhere.
   ----------------------------------------
*/
if (($extension=="docx") || ($extension=="xlsx") || ($extension=="pptx") || ($extension=="xps"))

	{
shell_exec("unzip -p $file \"docProps/thumbnail.jpeg\" > $target");$newfile = $target;
	}


/* ----------------------------------------
	Try Blender 3D. This runs Blender on the command line to render the first frame of the file.
   ----------------------------------------
*/

if ($extension=="blend")

	{
$blendercommand="blender";	
if (!file_exists($blendercommand)) {$blendercommand="/Applications/blender.app/Contents/MacOS/blender";}
if (!file_exists($blendercommand)) {exit("Could not find blender application. '$blendercommand'");}	
shell_exec($blendercommand. " -b $file -F JPEG -o $target -f 1");
if (file_exists($target."0001.jpg")){
copy($target."0001.jpg","$target");
$newfile = $target;}
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
        	
        $snapshottime = 1;
        
        $out = shell_exec($ffmpeg_path."/ffmpeg -i '$file' 2>&1");
        if(preg_match("/Duration: (\d+):(\d+):(\d+)\.\d+, start/", $out, $match))
        	{
			$duration = $match[1]*3600+$match[2]*60+$match[3];
			if($duration>10)
				{
				$snapshottime = 5;
				}
			elseif($snapshottime > 2)
				{
				$snapshottime = floor($duration / 10);
				}
			}
		
		if ($extension=="mxf")
			{ $snapshottime = 0; }
        
        $output=shell_exec($ffmpeg_path . "/ffmpeg -i \"$file\" -f image2 -vframes 1 -ss ".$snapshottime." \"$target\""); 
        #exit($command . "<br>" . $output); 
        if (file_exists($target)) 
            {
            $newfile=$target;
            global $ffmpeg_preview,$ffmpeg_preview_seconds,$ffmpeg_preview_extension,$ffmpeg_preview_options;
            global $ffmpeg_preview_min_width,$ffmpeg_preview_min_height,$ffmpeg_preview_max_width,$ffmpeg_preview_max_height;
            global $qtfaststart_path,$qtfaststart_extensions;

            if ($ffmpeg_preview)
                {
                # Create a preview video (FLV)
                $targetfile=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension); 
                
                $snapshotsize = getimagesize($target);
                $width = $snapshotsize[0];
                $height = $snapshotsize[1];
				
				if($height<$ffmpeg_preview_min_height)
					{
					$height = $ffmpeg_preview_min_height;
					}
				
				if($width<$ffmpeg_preview_min_width)
					{
					$width = $ffmpeg_preview_min_width;
					}
				
				if($height>$ffmpeg_preview_max_height)
					{
					$width = ceil($width*($ffmpeg_preview_max_height/$height));
					$height = $ffmpeg_preview_max_height;
					}
					
				if($width>$ffmpeg_preview_max_width)
					{
					$height = ceil($height*($ffmpeg_preview_max_width/$width));
					$width = $ffmpeg_preview_max_width;
					}
				
                $output=shell_exec($ffmpeg_path . "/ffmpeg -y -i \"$file\" $ffmpeg_preview_options -s {$width}x{$height} -t $ffmpeg_preview_seconds  \"$targetfile\"");
                
                if($qtfaststart_path && file_exists($qtfaststart_path . "/qt-faststart") && in_array($ffmpeg_preview_extension, $qtfaststart_extensions) )
                    {
                	$targetfiletmp=$targetfile.".tmp";
                	rename($targetfile, $targetfiletmp);
	                $output=shell_exec($qtfaststart_path . "/qt-faststart \"$targetfiletmp\" \"$targetfile\"");
	                unlink($targetfiletmp);
                    }
                }
            } 
        } 


/* ----------------------------------------
	Try ImageMagick
   ----------------------------------------
*/
if (!isset($newfile))
	{

    $prefix="";

	# Preserve colour profiles?    
	$profile="+profile icc -colorspace RGB"; # By default, strip the colour profiles ('+' is remove the profile, confusingly)
    if ($imagemagick_preserve_profiles) {$profile="";}
    
    # CR2 files need a cr2: prefix
    if ($extension=="cr2") {$prefix="cr2:";}
    
   if (($extension=="pdf") || ($extension=="eps") || ($extension=="ai") || ($extension=="ps")) 
    	{
   	    # For EPS/PS/PDF files, use GS directly and allow multiple pages.
		# EPS files are always single pages:
		if ($extension=="eps") {$pdf_pages=1;}
		if ($extension=="ai") {$pdf_pages=1;}
		if ($extension=="ps") {$pdf_pages=1;}
		# Locate ghostscript command
		$gscommand= $ghostscript_path. "/gs";
	    if (!file_exists($gscommand)) {$gscommand= $ghostscript_path. "\gs.exe";}
        if (!file_exists($gscommand)) {exit("Could not find GhostScript 'gs' utility.'");}	

        
		# Create multiple pages.
		for ($n=1;$n<=$pdf_pages;$n++)
			{
			# Set up target file
			$size="";if ($n>1) {$size="scr";} # Use screen size for other pages.
			$target=get_resource_path($ref,true,$size,false,"jpg",-1,$n); 
			if (file_exists($target)) {unlink($target);}
			
			$gscommand2 = $gscommand . " -dBATCH -dNOPAUSE -sDEVICE=jpeg -sOutputFile=\"$target\" -dFirstPage=" . $n . " -dLastPage=" . $n . " -dUseCropBox -dEPSCrop \"$file\"";
 			$output=shell_exec($gscommand2); 
	
			# Set that this is the file to be used.
			if (file_exists($target) && $n==1)
				{
				$newfile=$target;
				}
			
			# For files other than page 1, resize directly to the screen size (no other sizes needed)
			if (file_exists($target) && $n>1)
				{
				$command2=$command . " " . $prefix . "\"$target\"[0] -quality $imagemagick_quality -resize 800x800 \"$target\""; 
				$output=shell_exec($command2); 
				
				# Add a watermarked image too?
				global $watermark;
    			if (isset($watermark))
    				{
					$path=get_resource_path($ref,true,$size,false,"",-1,$n,true);
					if (file_exists($path)) {unlink($path);}
    				$watermarkreal=myrealpath($watermark);
    				
				    $command2 = $command . " \"$target\"[0] $profile -quality $imagemagick_quality -resize 800x800 -tile $watermarkreal -draw \"rectangle 0,0 800,800\" \"$path\""; 
					$output=shell_exec($command2); 
					}
				
				}
			}
		}
    else
    	{
    	# Not a PDF file, so single extraction only.
			create_previews_using_im($ref,false,$extension,$previewonly);
			}
	}
	

# If a file has been created, generate previews just as if a JPG was uploaded.
if (isset($newfile))
	{
	create_previews($ref,false,"jpg",$previewonly);	
	}
if ($extension=="ai"){extract_exif_comment($ref,"jpg");}
?>