<?php 
#
# This file contains the integration code with ImageMagick
# It also contains integration code for those types that we need ImageMagick to be able to process
# for example types that use GhostScript or FFmpeg.
#

global $imagemagick_path,$imagemagick_preserve_profiles,$imagemagick_quality,$pdf_pages,$antiword_path,$unoconv_path;

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
	$indd_thumb = extract_indd_thumb ($file);
	if ($indd_thumb!="no")
		{
		base64_to_jpeg( $indd_thumb, $target);
		if (file_exists($target)){$newfile = $target;}
		}
		
	hook("indesign");	
	}
	
	
/* ----------------------------------------
	Try PhotoshopThumbnail
   ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
if ($extension=="psd")
	{
	global $photoshop_thumb_extract;
	if ($photoshop_thumb_extract)
		{
		global $exiftool_path;
		if (isset($exiftool_path))
			{
			shell_exec($exiftool_path.'/exiftool -b -PhotoshopThumbnail '.$file.' > '.$target);
			}
		if (file_exists($target))
			{
			#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in imagemagick_preview.php
			if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
			}
		}
	}
	
/* ----------------------------------------
	Try CR2 preview extraction via exiftool
   ----------------------------------------
*/

if ($extension=="cr2")
	{
	global $cr2_thumb_extract;
	if ($cr2_thumb_extract)
		{
		global $exiftool_path;
		if (isset($exiftool_path))
			{
			shell_exec($exiftool_path.'/exiftool -b -previewimage '.$file.' > '.$target);
			}
		if (file_exists($target))
			{
			#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in imagemagick_preview.php
			if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
			}
		}
	}	
	
/* ----------------------------------------
	Try NEF preview extraction via exiftool
   ----------------------------------------
*/

if ($extension=="nef")
	{
	global $nef_thumb_extract;
	if ($nef_thumb_extract)
		{
		global $exiftool_path;
		if (isset($exiftool_path))
			{
			shell_exec($exiftool_path.'/exiftool -b -jpgfromraw '.$file.' > '.$target);
			}
		if (file_exists($target))
			{
			#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in imagemagick_preview.php
			if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
			}
		}
	}		
	
	
	
/* ----------------------------------------
	Unoconv is a python-based utility to run files through OpenOffice. It is available in Ubuntu.
	This adds conversion of office docs to PDF format and adds them as alternative files
	One could also see the potential to base previews on the PDFs for paging and better quality for most of these formats.
   ----------------------------------------
*/
if (($extension=="doc"||$extension=="docx"||$extension=="odt"||$extension=="odp"||$extension=="html"||$extension=="rtf"||$extension=="txt"||$extension=="ppt") && isset($unoconv_path))
	{
	$unocommand=$unoconv_path . "/unoconv";
	if (!file_exists($unocommand)) {exit("Unoconv executable not found at '$unoconv_path'");}

	shell_exec($unocommand . " --format=pdf \"" . $file . "\"");
	$path_parts=pathinfo($file);
	$pdffile=$path_parts['dirname']."/".$path_parts['filename'].".pdf";
	if (file_exists($pdffile))
		{
		$alt_ref=add_alternative_file($ref,"PDF version");
		$alt_path=get_resource_path($ref,true,"",false,"pdf",-1,1,false,"",$alt_ref);	
	    copy($pdffile,$alt_path);unlink($pdffile);
	    sql_query("update resource_alt_files set file_name='$ref-converted.pdf',description='PDF version',file_extension='pdf',file_size='".filesize($alt_path)."' where resource=$ref and ref=$alt_ref");
	    $extension="pdf";
	    $file=$alt_path;
		}
	}	

/* ----------------------------------------
	Try OpenDocument Format
   ----------------------------------------
*/
if (($extension=="odt") || ($extension=="ott") || ($extension=="odg") || ($extension=="otg") || ($extension=="odp") || ($extension=="otp") || ($extension=="ods") || ($extension=="ots") || ($extension=="odf") || ($extension=="otf") || ($extension=="odm") || ($extension=="oth"))

	{
shell_exec("unzip -p $file \"Thumbnails/thumbnail.png\" > $target");
$odcommand=$command . " \"$target\"[0]  \"$target\""; 
				$output=shell_exec($odcommand); if(file_exists($target)){$newfile = $target;}
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
	if (file_exists($target."0001.jpg"))
		{
		copy($target."0001.jpg","$target");
		unlink($target."0001.jpg");
		$newfile = $target;
		}
	}



/* ----------------------------------------
	Microsoft Word previews using Antiword
	(note: this is very basic)
   ----------------------------------------
*/
if ($extension=="doc" && isset($antiword_path) && isset($ghostscript_path))
	{
	$command=$antiword_path . "/antiword";
	if (!file_exists($command)) {$command=$antiword_path . "\antiword.exe";}
	if (!file_exists($command)) {exit("Antiword executable not found at '$antiword_path'");}
	shell_exec($command . " -p a4 \"" . $file . "\" > \"" . $target . ".ps" . "\"");
	if (file_exists($target . ".ps"))
		{
		# Postscript file exists
		
		# Locate ghostscript command
		$gscommand= $ghostscript_path. "/gs";
	    if (!file_exists($gscommand)) {$gscommand= $ghostscript_path. "\gs.exe";}
        if (!file_exists($gscommand)) {exit("Could not find GhostScript 'gs' utility.'");}	
		
		$gscommand = $gscommand . " -dBATCH -dNOPAUSE -sDEVICE=jpeg -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=1 -dLastPage=1 -dUseCropBox -dEPSCrop " . escapeshellarg($target . ".ps");
		$output=shell_exec($gscommand); 

		if (file_exists($target))
			{
			# A JPEG was created. Set as the file to process.
			$newfile=$target;
			}
		}
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
	$font=dirname(__FILE__). "/../gfx/fonts/vera.ttf";
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
$ffmpeg_path.="/ffmpeg";
if (!file_exists($ffmpeg_path)) {$ffmpeg_path.=".exe";}

if (isset($ffmpeg_path) && file_exists($ffmpeg_path) && !isset($newfile)) 
        {
        $ffmpeg_path=escapeshellarg($ffmpeg_path);
        	
        # A work-around for Windows systems. Prefixing the command prevents a problem
        # with double quotes.
        global $config_windows;
        if ($config_windows)
        	{
		    $ffmpeg_path = "cd & " . $ffmpeg_path;
        	}
        
        $snapshottime = 1;
        $out = shell_exec($ffmpeg_path." -i " . escapeshellarg($file) . " 2>&1");
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
        
        $output=shell_exec($ffmpeg_path . " -i " . escapeshellarg($file) . " -f image2 -vframes 1 -ss ".$snapshottime." " . escapeshellarg($target)); 

        if (file_exists($target)) 
            {
            $newfile=$target;
            global $ffmpeg_preview,$ffmpeg_preview_seconds,$ffmpeg_preview_extension,$ffmpeg_preview_options;
            global $ffmpeg_preview_min_width,$ffmpeg_preview_min_height,$ffmpeg_preview_max_width,$ffmpeg_preview_max_height;
            global $php_path, $ffmpeg_preview_async, $ffmpeg_preview_force;

            if ($ffmpeg_preview && ($extension!=$ffmpeg_preview_extension || $ffmpeg_preview_force) )
                {
                	if ($ffmpeg_preview_async && $php_path && file_exists($php_path . "/php"))
	                	{
	                	global $scramble_key;
	                	exec($php_path . "/php " . dirname(__FILE__)."/imagemagick_preview.php " . 
	                		escapeshellarg($scramble_key) . " " . 
	                		escapeshellarg($ref) . " " . 
	                		escapeshellarg($file) . " " . 
	                		escapeshellarg($target) . " " . 
	                		escapeshellarg($previewonly) . " " . 
	                		"&> /dev/null &");
	                	}
                	else 
	                	{
	                	include(dirname(__FILE__)."/imagemagick_preview.php");
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
			
			$gscommand2 = $gscommand . " -dBATCH -r150 -dNOPAUSE -sDEVICE=jpeg -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " -dUseCropBox -dEPSCrop " . escapeshellarg($file);
 			$output=shell_exec($gscommand2); 
	
			# Set that this is the file to be used.
			if (file_exists($target) && $n==1)
				{
				$newfile=$target;
				}
			
			# resize directly to the screen size (no other sizes needed)
			if (file_exists($target))
				{
				$command2=$command . " " . $prefix . escapeshellarg($target) . "[0] -quality $imagemagick_quality -resize 850x850 " . escapeshellarg($target); 
				$output=shell_exec($command2); 
				
				# Add a watermarked image too?
				global $watermark;
    			if (isset($watermark))
    				{
					$path=get_resource_path($ref,true,$size,false,"",-1,$n,true);
					if (file_exists($path)) {unlink($path);}
    				$watermarkreal=dirname(__FILE__). "/../" . $watermark;
    				
				    $command2 = $command . " \"$target\"[0] $profile -quality $imagemagick_quality -resize 800x800 -tile " . escapeshellarg($watermarkreal) . " -draw \"rectangle 0,0 800,800\" " . escapeshellarg($path); 
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
