<?php 
#
# This file contains the integration code with ImageMagick
# It also contains integration code for those types that we need ImageMagick to be able to process
# for example types that use GhostScript or FFmpeg.
#

global $imagemagick_path, $imagemagick_preserve_profiles, $imagemagick_quality, $pdf_pages,$antiword_path, $unoconv_path, $pdf_dynamic_rip, $ffmpeg_audio_extensions, $ffmpeg_audio_params, $qlpreview_path,$ffmpeg_path, $ffmpeg_supported_extensions, $qlpreview_exclude_extensions;
global $dUseCIEColor;

if (!$previewonly)
	{
	$file=get_resource_path($ref,true,"",false,$extension,-1,1,false,"",$alternative); 
	$target=get_resource_path($ref,true,"",false,"jpg",-1,1,false,"",$alternative); 
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
putenv("PATH=/bin:" . $ghostscript_path . ":" . $imagemagick_path . ":" . 
$imagemagick_path . "/bin"); # Path

if ($alternative==-1)
	{
	# Reset the 'has thumbnail image' status in case previewing fails with this new file. 
	sql_query("update resource set has_image=0 where ref='$ref'"); 
	}


# Set up target file
if (file_exists($target)) {unlink($target);}


# Locate imagemagick.
 $command=$imagemagick_path . "/bin/convert";
 if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
 if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
 if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility. $command'");}	

debug ("Starting preview preprocessing. File extension is $extension.");

hook("metadata");

/* ----------------------------------------
	QuickLook Previews (Mac only)
	For everything except Audio/Video files, attempt to generate a QuickLook preview first.
   ----------------------------------------
*/
if (isset($qlpreview_path) && !in_array($extension, $qlpreview_exclude_extensions) && !in_array($extension, $ffmpeg_supported_extensions) && !in_array($extension, $ffmpeg_audio_extensions))
	{
	$qlpreview_command=$qlpreview_path."/qlpreview -generatePreviewOnly yes -imageType jpg -maxWidth 800 -maxHeight 800 -asIcon no -preferFileIcon no -inPath " . escapeshellarg($file) . " -outPath " . escapeshellarg($target);
	debug("qlpreview command: " . $qlpreview_command);
	$output=shell_exec($qlpreview_command);
	#sleep(4); # Delay to allow processing
	if (file_exists($target)){$newfile = $target;debug("qlpreview success!");}	
	}


/* ----------------------------------------
	Try InDesign - for non-exiftool - CS4 not supported
   ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
global $exiftool_path;
if (!isset($exiftool_path)){
	if ($extension=="indd" && !isset($newfile))
		{
		$indd_thumb = extract_indd_thumb ($file);
		if ($indd_thumb!="no")
			{
			base64_to_jpeg( $indd_thumb, $target);
			if (file_exists($target)){$newfile = $target;}
			}
		hook("indesign");	
		}
	}
	
	
/* ----------------------------------------
       Try InDesignThumbnail - exiftool
  ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
# Thanks to Jeff Harmon for this code

if ($extension=="indd" && !isset($newfile))
       {
       global $exiftool_path;
       if (isset($exiftool_path))
               {
               shell_exec($exiftool_path.'/exiftool -b -thumbnailimage '.$file.' > '.$target);
               }
       if (file_exists($target))
               {
               #if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
               if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
               }		
		hook("indesign");	
       }


/* ----------------------------------------
	Try InDesign - for CS5 (page previews)
   ----------------------------------------
*/
global $exiftool_path;
if (isset($exiftool_path))
	{
	if ($extension=="indd" && !isset($newfile))
		{
		$indd_thumbs = extract_indd_pages ($file);

		if (is_array($indd_thumbs))
			{
			$n=0;	
			foreach ($indd_thumbs as $indd_thumb){
				base64_to_jpeg( $indd_thumb, $target."_".$n);
				$n++;
				}
			$pagescommand="";
			for ($x=0;$x<$n;$x++){
				$pagescommand.=" ".$target."_".$x;
				}
			// process jpgs as a pdf so the existing pdf paging code can be used.	
			$file=get_resource_path($ref,true,"",false,"pdf");		
			$jpg2pdfcommand=$command . " ".$pagescommand." " . $file; 
			$output=shell_exec($jpg2pdfcommand); 
			for ($x=0;$x<$n;$x++){
				unlink($target."_".$x);
				}
			$extension="pdf";
			$dUseCIEColor=false;
			$n=0;	
			$x=0;
		}
	}	
}
	
/* ----------------------------------------
	Try PhotoshopThumbnail
   ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
if ($extension=="psd" && !isset($newfile))
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
			#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
			if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
			}
		}
	}
	
/* ----------------------------------------
	Photoshop Transparency Checkerboard
   ----------------------------------------
*/
# composite checkerboard for PSD transparency. Not applicable to $photoshop_thumb_extract.
global $psd_transparency_checkerboard;
if ($extension=="psd" && !isset($newfile) && $psd_transparency_checkerboard)
	{
	global $imagemagick_path;
	$wait=shell_exec($imagemagick_path."/composite  -compose Dst_Over -tile pattern:checkerboard ".$file." ".$target);
	if (file_exists($target)){
		$newfile=$target;
	}
		
}	
		
	
	
/* ----------------------------------------
	Try SWF
   ----------------------------------------
*/
# Note: gnash-dump must be compiled on the server. http://www.xmission.com/~ink/gnash/gnash-dump/README.txt
# Ubuntu: ./configure --prefix=/usr/local/gnash-dump --enable-renderer=agg \
# --enable-gui=gtk,dump --disable-kparts --disable-nsapi --disable-menus
# several dependencies will also be necessary, according to ./configure

if ($extension=="swf" && !isset($newfile))
	{
	global $dump_gnash_path;
	if (isset($dump_gnash_path))
		{
		shell_exec($dump_gnash_path.'/dump-gnash -t 1 --screenshot 5 --screenshot-file '.$target.' '.$file);
		}
	if (file_exists($target))
		{
		#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
		if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
		}
		
	}	
	
/* ----------------------------------------
	Try CR2 preview extraction via exiftool
   ----------------------------------------
*/

if (($extension=="cr2" || $extension=="nef" || $extension=="dng") && !isset($newfile))
	{
	global $cr2_thumb_extract;
	global $nef_thumb_extract;
	global $dng_thumb_extract;
	
	global $exiftool_path;
	if (($extension=="cr2" && $cr2_thumb_extract) || ($extension=="nef" && $nef_thumb_extract) || ($extension=="dng" && $dng_thumb_extract))
		{
		if (isset($exiftool_path))
			{	
			// previews are stored in a couple places, and some nef files have large previews in -otherimage
			if ($extension=="nef"){$bin_tag=" -otherimage ";}
			if ($extension=="cr2"||$extension=="dng"){$bin_tag=" -previewimage ";}
			// attempt
			$wait=shell_exec($exiftool_path.'/exiftool -b '.$bin_tag.' '.$file.' > '.$target);

			// check for nef -otherimage failure
			if ($extension=="nef"&&!filesize($target)>0)
				{
				unlink($target);	
				$bin_tag=" -previewimage ";
				//2nd attempt
				$wait=shell_exec($exiftool_path.'/exiftool -b '.$bin_tag.' '.$file.' > '.$target);
				}
				
			// NOTE: in case of failures, other suboptimal possibilities 
			// may be explored in the future such as -thumbnailimage and -jpgfromraw, like this:
			// //check for failure
			//if (!filesize($target)>0)
				//{
				//unlink($target);	
				//$bin_tag=" -thumbnailimage ";
				//attempt
				//$wait=shell_exec($exiftool_path.'/exiftool -b '.$bin_tag.' '.$file.' > '.$target);
				//}
			
			if (filesize($target)>0)
				{
				$orientation=get_image_orientation($file);
				if ($orientation!=0)
					{
					$command=$imagemagick_path . "/mogrify";
					$command .= ' -rotate +' .$orientation.' '. $target ;
					$wait=shell_exec($command);	
					//imagemagick is much faster than this:
					//$source = imagecreatefromjpeg($target);
					//$source=AltImageRotate($source,$orientation);
					//imagejpeg($source,$target,95);
					}
				$newfile = $target;
				}
			else
				{
				unlink($target);
				}	
			}
		}
	}	

/* ---------------------------------------- 
        Try Apple iWork Formats 
        The following are to generate previews for the Apple iWork files such 
as Apple Pages, Apple Keynote, and Apple Numbers. 
   ---------------------------------------- 
*/ 
if ( (($extension=="pages") || ($extension=="numbers") || ($extension=="key")) && !isset($newfile)) 
	{ 
    shell_exec("unzip -p $file \"QuickLook/Thumbnail.jpg\" > $target"); 
	$newfile = $target; 
	} 	
		
	
/* ----------------------------------------
	Unoconv is a python-based utility to run files through OpenOffice. It is available in Ubuntu.
	This adds conversion of office docs to PDF format and adds them as alternative files
	One could also see the potential to base previews on the PDFs for paging and better quality for most of these formats.
   ----------------------------------------
*/
global $unoconv_extensions;
if (in_array($extension,$unoconv_extensions) && isset($unoconv_path) && !isset($newfile))
	{
	$unocommand=$unoconv_path . "/unoconv";
	if (!file_exists($unocommand)) {exit("Unoconv executable not found at '$unoconv_path'");}
	
	shell_exec($unocommand . " --format=pdf \"" . $file . "\"");
	$path_parts=pathinfo($file);
	$basename_minus_extension=remove_extension($path_parts['basename']);
	$pdffile=$path_parts['dirname']."/".$basename_minus_extension.".pdf";
	if (file_exists($pdffile))
		{
		# Attach this PDF file as an alternative download.
		sql_query("delete from resource_alt_files where resource = '".$ref."' and unoconv='1'");	
		$alt_ref=add_alternative_file($ref,"PDF version");
		$alt_path=get_resource_path($ref,true,"",false,"pdf",-1,1,false,"",$alt_ref);	
	    copy($pdffile,$alt_path);unlink($pdffile);
	    sql_query("update resource_alt_files set file_name='$ref-converted.pdf',description='generated by Open Office',file_extension='pdf',file_size='".filesize($alt_path)."',unoconv='1' where resource='$ref' and ref='$alt_ref'");

		# Set vars so we continue generating thumbs/previews as if this is a PDF file
	    $extension="pdf";
	    $file=$alt_path;
		}
	}
    
/* ----------------------------------------
	Calibre E-book processing
   ----------------------------------------
*/
global $calibre_extensions;
global $calibre_path;
if (in_array($extension,$calibre_extensions) && isset($calibre_path) && !isset($newfile))
	{
	$calibrecommand=$calibre_path . "/ebook-convert";
	if (!file_exists($calibrecommand)) {exit("Calibre executable not found at '$calibre_path'");}
	
	$path_parts=pathinfo($file);
	$basename_minus_extension=remove_extension($path_parts['basename']);
	$pdffile=$path_parts['dirname']."/".$basename_minus_extension.".pdf";

	$wait=shell_exec("xvfb-run ". $calibrecommand . " " . $file . " " .$pdffile." --output-profile nook") ;

    if (file_exists($pdffile))
		{
		# Attach this PDF file as an alternative download.
		sql_query("delete from resource_alt_files where resource = '".$ref."' and unoconv='1'");	
		$alt_ref=add_alternative_file($ref,"PDF version");
		$alt_path=get_resource_path($ref,true,"",false,"pdf",-1,1,false,"",$alt_ref);	
	    copy($pdffile,$alt_path);unlink($pdffile);
	    sql_query("update resource_alt_files set file_name='$ref-converted.pdf',description='generated by Open Office',file_extension='pdf',file_size='".filesize($alt_path)."',unoconv='1' where resource='$ref' and ref='$alt_ref'");

		# Set vars so we continue generating thumbs/previews as if this is a PDF file
	    $extension="pdf";
	    $file=$alt_path;
		}
	}	
    

/* ----------------------------------------
	Try OpenDocument Format
   ----------------------------------------
*/
if ((($extension=="odt") || ($extension=="ott") || ($extension=="odg") || ($extension=="otg") || ($extension=="odp") || ($extension=="otp") || ($extension=="ods") || ($extension=="ots") || ($extension=="odf") || ($extension=="otf") || ($extension=="odm") || ($extension=="oth")) && !isset($newfile))

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
if ((($extension=="docx") || ($extension=="xlsx") || ($extension=="pptx") || ($extension=="xps")) && !isset($newfile))
	{
	shell_exec("unzip -p $file \"docProps/thumbnail.jpeg\" > $target");$newfile = $target;
	}



/* ----------------------------------------
	Try Blender 3D. This runs Blender on the command line to render the first frame of the file.
   ----------------------------------------
*/

if ($extension=="blend" && !isset($newfile))
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
if ($extension=="doc" && isset($antiword_path) && isset($ghostscript_path) && !isset($newfile))
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
		
		$gscommand = $gscommand . " -dBATCH -dNOPAUSE -sDEVICE=jpeg -r150 -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=1 -dLastPage=1 -dEPSCrop " . escapeshellarg($target . ".ps");
		$output=shell_exec($gscommand); 

		if (file_exists($target))
			{
			# A JPEG was created. Set as the file to process.
			$newfile=$target;
			}
		}
	}

/* ----------------------------------------
	Try MP3 preview extraction via exiftool
   ----------------------------------------
*/

if ($extension=="mp3" && !isset($newfile))
	{
	global $exiftool_path;
	if (isset($exiftool_path))
		{
		shell_exec($exiftool_path.'/exiftool -b -picture '.$file.' > '.$target);
		}
	if (file_exists($target))
		{
		#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
		if (filesize($target)>0){$newfile = $target;}else{unlink($target);}
		}
	}


/* ----------------------------------------
	Try text file to JPG conversion
   ----------------------------------------
*/
# Support text files simply by rendering them on a JPEG.
if ($extension=="txt" && !isset($newfile))
	{
	$text=wordwrap(file_get_contents($file),90);
	$width=650;$height=850;
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
if (isset($ffmpeg_path))
	{
	$ffmpeg_path_working=$ffmpeg_path . "/ffmpeg";
	if (!file_exists($ffmpeg_path_working)) {$ffmpeg_path_working.=".exe";}
	}

if (isset($ffmpeg_path) && file_exists($ffmpeg_path_working) && !isset($newfile) && in_array($extension, $ffmpeg_supported_extensions))
        {
        $ffmpeg_path_working=escapeshellarg($ffmpeg_path_working);
        	
        # A work-around for Windows systems. Prefixing the command prevents a problem
        # with double quotes.
        global $config_windows;
        if ($config_windows)
        	{
		    $ffmpeg_path_working = "cd & " . $ffmpeg_path_working;
        	}
        
        $snapshottime = 1;
        $out = shell_exec($ffmpeg_path_working." -i " . escapeshellarg($file) . " 2>&1");
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
        
        $output=shell_exec($ffmpeg_path_working . " -i " . escapeshellarg($file) . " -f image2 -vframes 1 -ss ".$snapshottime." " . escapeshellarg($target)); 

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
	                	exec($php_path . "/php " . dirname(__FILE__)."/ffmpeg_processing.php " . 
	                		escapeshellarg($scramble_key) . " " . 
	                		escapeshellarg($ref) . " " . 
	                		escapeshellarg($file) . " " . 
	                		escapeshellarg($target) . " " . 
	                		escapeshellarg($previewonly) . " " . 
	                		"&> /dev/null &");
	                	}
                	else 
	                	{
	                	include (dirname(__FILE__)."/ffmpeg_processing.php");
	                	}
                }
            } 
        } 


/* ----------------------------------------
	Try FFMPEG for audio files
   ----------------------------------------
*/
if (isset($ffmpeg_path) && file_exists($ffmpeg_path_working) && in_array($extension, $ffmpeg_audio_extensions)&& !isset($newfile))
	{
	$ffmpeg_path_working=escapeshellarg($ffmpeg_path_working);
	
	# A work-around for Windows systems. Prefixing the command prevents a problem
	# with double quotes.
	global $config_windows;
	if ($config_windows)
		{
	    $ffmpeg_path_working = "cd & " . $ffmpeg_path_working;
		}
	
	# Produce the MP3 preview.
	$mp3file=get_resource_path($ref,true,"",false,"mp3"); 
	$output=shell_exec($ffmpeg_path_working . " -i " . escapeshellarg($file) . " " . $ffmpeg_audio_params . " " . escapeshellarg($mp3file)); 
	}



/* ----------------------------------------
	Try ImageMagick
   ----------------------------------------
*/
if ((!isset($newfile)) && (!in_array($extension, $ffmpeg_audio_extensions)))
	{
    $prefix="";

	# Preserve colour profiles?    
	$profile="+profile icc -colorspace RGB"; # By default, strip the colour profiles ('+' is remove the profile, confusingly)
    if ($imagemagick_preserve_profiles) {$profile="";}
    
    # CR2 files need a cr2: prefix
    if ($extension=="cr2") {$prefix="cr2:";}

	$photoshop_eps = false;
	global $photoshop_eps_miff;  
	if ($photoshop_eps_miff){
		
		# Recognize Photoshop EPS(F) pixel data files
		if ($extension=="eps")
		{
		$eps_file = fopen($file, 'r');
		$i = 0;
		while (!$photoshop_eps && ($eps_line = fgets($eps_file)) && ($i < 100))
		{
		if (@eregi("%%BoundingBox: [0-9]+ [0-9]+ ([0-9]+) ([0-9]+)", $eps_line, $regs))
			{
			$eps_bbox_x = $regs[1];
			$eps_bbox_y = $regs[2];
			}
		if (@eregi("%ImageData: ([0-9]+) ([0-9]+)", $eps_line, $regs))
			{
			$eps_data_x = $regs[1];
			$eps_data_y = $regs[2];
			}
		if (@eregi("%BeginPhotoshop:",$eps_line))
			{
			$photoshop_eps = true;
			}
		$i++;
		}
		if ($photoshop_eps)
			{
			$eps_density_x = $eps_data_x / $eps_bbox_x * 72;
			$eps_density_y = $eps_data_y / $eps_bbox_y * 72;
			$eps_target=get_resource_path($ref,true,"",false,"miff");
			$nfcommand = $command . ' -compress zip -colorspace RGB -quality 100 -density ' . sprintf("%.1f", $eps_density_x ). 'x' . sprintf("%.1f", $eps_density_y) . ' ' . escapeshellarg($file) . '[0] ' . escapeshellarg($eps_target);
			shell_exec($nfcommand);
			if (file_exists($eps_target))
			{
			#  create_previews_using_im($ref,false,'miff',$previewonly);
			$extension = 'miff';
			}
			}
		}
	}

   if (($extension=="pdf") || (($extension=="eps") && !$photoshop_eps) || ($extension=="ai") || ($extension=="ps")) 
    	{
    	debug("PDF multi page preview generation starting");
    	
   	  # For EPS/PS/PDF files, use GS directly and allow multiple pages.
	# EPS files are always single pages:
	if ($extension=="eps") {$pdf_pages=1;}
	if ($extension=="ai") {$pdf_pages=1;}
	if ($extension=="ps") {$pdf_pages=1;}
	# Locate ghostscript command
	$gscommand= $ghostscript_path. "/gs";
	if (!file_exists($gscommand)) {$gscommand= $ghostscript_path. "\gs.exe";}
        if (!file_exists($gscommand)) {exit("Could not find GhostScript 'gs' utility.'");}	
		
	$resolution=150;

        if ($pdf_dynamic_rip) {
		/* We want to rip at ~150 dpi by default because it provides decent 
		* quality previews and speed in the end. It is not always efficient to just 
		* rip at 150, though, because for very large pages, a lot of pixels 
		* get wasted when we resize to 850 pixels. Also, if the page size is 
		* quite small, ripping at 150 may not provide enough quality for the 
		* scr size preview. So, use PDFinfo to calculate a rip resolution 
		* that will give us a source bitmap of approximately 1600 pixels.
		*/

			$pdfinfocommand="pdfinfo ".escapeshellarg($file);
			$pdfinfo=shell_exec($pdfinfocommand);
			$pdfinfo=explode("\n",$pdfinfo);
			$pdfinfo=preg_grep("/Page size/",$pdfinfo);
			sort($pdfinfo);
			#die(print_r($pdfinfo));
			if (isset($pdfinfo[0])){
				$pdfinfo=$pdfinfo[0];
				}
			else {
				$pdfinfo="";
				}
			if ($pdfinfo!=""){	
				$pdfinfo=str_replace("Page size:","",$pdfinfo);
				$pdfinfo=str_replace("pts","",$pdfinfo);
				$pdfinfo=str_replace(" x","",$pdfinfo);
				$pdfinfo=explode(" ",trim($pdfinfo));
				if($pdfinfo[0]>$pdfinfo[1]){
					$pdf_max_dim=$pdfinfo[0];
					}
				else{
					$pdf_max_dim=$pdfinfo[1];
					}
				$resolution=ceil(1768/($pdf_max_dim/72));
				}
			}
		
	# Create multiple pages.
	for ($n=1;$n<=$pdf_pages;$n++)
		{
		# Set up target file
		$size="";if ($n>1) {$size="scr";} # Use screen size for other pages.
		$target=get_resource_path($ref,true,$size,false,"jpg",-1,$n,false,"",$alternative); 
		if (file_exists($target)) {unlink($target);}

		if ($dUseCIEColor){$dUseCIEColor=" -dUseCIEColor ";} else {$dUseCIEColor="";}
		$gscommand2 = $gscommand . " -dBATCH -r".$resolution." ".$dUseCIEColor." -dNOPAUSE -sDEVICE=jpeg -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " -dEPSCrop " . escapeshellarg($file);
 		$output=shell_exec($gscommand2); 

    	debug("PDF multi page preview: page $n, executing " . $gscommand2);

	
		# Set that this is the file to be used.
		if (file_exists($target) && $n==1)
			{
			$newfile=$target;
	    	debug("Page $n generated successfully");
			}
			
		# resize directly to the screen size (no other sizes needed)
		 if (file_exists($target)&& $n!=1)
			{
			$command2=$command . " " . $prefix . escapeshellarg($target) . "[0] -quality $imagemagick_quality -resize 850x850 " . escapeshellarg($target); 
			$output=shell_exec($command2); 
				
			# Add a watermarked image too?
			global $watermark;
    			if (isset($watermark) && $alternative==-1)
    				{
				$path=get_resource_path($ref,true,$size,false,"",-1,$n,true,"",$alternative);
				if (file_exists($path)) {unlink($path);}
    				$watermarkreal=dirname(__FILE__). "/../" . $watermark;
    				
				$command2 = $command . " \"$target\"[0] $profile -quality $imagemagick_quality -resize 800x800 -tile " . escapeshellarg($watermarkreal) . " -draw \"rectangle 0,0 800,800\" " . escapeshellarg($path); 
					$output=shell_exec($command2); 
				}
				
			}
		
		# Splitting of PDF files to multiple resources
		global $pdf_split_pages_to_resources;
		if (file_exists($target) && $pdf_split_pages_to_resources)
			{
			# Create a new resource based upon the metadata/type of the current resource.
			$copy=copy_resource($ref);
						
			# Find out the path to the original file.
			$copy_path=get_resource_path($copy,true,"",true,"pdf");
			
			# Extract this one page to a new resource.
			$gscommand2 = $gscommand . " -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($copy_path) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " " . escapeshellarg($file);
	 		$output=shell_exec($gscommand2); 
 		
 			# Update the file extension
 			sql_query("update resource set file_extension='pdf' where ref='$copy'");
 		
 			# Create preview for the page.
 			$pdf_split_pages_to_resources=false; # So we don't get stuck in a loop creating split pages for the single page PDFs.
 			create_previews($copy,false,"pdf");
 			$pdf_split_pages_to_resources=true;
			}
		
			
		}
	}
    else
    	{
    	# Not a PDF file, so single extraction only.
			create_previews_using_im($ref,false,$extension,$previewonly,false,$alternative);
			}
	}
	
	
# Handle alternative image file generation.
global $image_alternatives;
if (isset($image_alternatives))
	{
	for($n=0;$n<count($image_alternatives);$n++)
		{
		$exts=explode(",",$image_alternatives[$n]["source_extensions"]);
		if (in_array($extension,$exts))
			{
			
			# Remove any existing alternative file(s) with this name.
			$existing=sql_query("select ref from resource_alt_files where resource='$ref' and name='" . escape_check($image_alternatives[$n]["name"]) . "'");
			for ($m=0;$m<count($existing);$m++)
				{
				delete_alternative_file($ref,$existing[$m]["ref"]);
				}
				
			# Create the alternative file.
			$aref=add_alternative_file($ref,$image_alternatives[$n]["name"]);
			$apath=get_resource_path($ref,true,"",true,$image_alternatives[$n]["target_extension"],-1,1,false,"",$aref);
			
			# Process the image
			$shell_exec_cmd = $command . " " . $image_alternatives[$n]["params"] . " " . escapeshellarg($file) . " " . escapeshellarg($apath);
			$output=shell_exec($shell_exec_cmd);
	
			if (file_exists($apath))
				{
				# Update the database with the new file details.
				$file_size=filesize($apath);
				sql_query("update resource_alt_files set file_name='" . escape_check($image_alternatives[$n]["filename"] . "." . $image_alternatives[$n]["target_extension"]) . "',file_extension='" . escape_check($image_alternatives[$n]["target_extension"]) . "',file_size='" . $file_size . "',creation_date=now() where ref='$aref'");
				}
			}
		}
	}

	
	

# If a file has been created, generate previews just as if a JPG was uploaded.
if (isset($newfile))
	{
	create_previews($ref,false,"jpg",$previewonly,false,$alternative);	
	}

?>
