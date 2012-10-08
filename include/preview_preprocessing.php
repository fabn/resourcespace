<?php 
#
# This file contains the integration code with ImageMagick
# It also contains integration code for those types that we need ImageMagick to be able to process
# for example types that use GhostScript or FFmpeg.
#

global $imagemagick_path, $imagemagick_preserve_profiles, $imagemagick_quality, $ghostscript_path, $pdf_pages, $antiword_path, $unoconv_path, $pdf_dynamic_rip, $ffmpeg_audio_extensions, $ffmpeg_audio_params, $qlpreview_path,$ffmpeg_supported_extensions, $qlpreview_exclude_extensions;
global $dUseCIEColor;

# Locate utilities
$exiftool_fullpath = get_utility_path("exiftool");
$ghostscript_fullpath = get_utility_path("ghostscript");

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
putenv("PATH=/bin:" . $ghostscript_path . ":" . $imagemagick_path); # Path

if ($alternative==-1)
	{
	# Reset the 'has thumbnail image' status in case previewing fails with this new file. 
	sql_query("update resource set has_image=0 where ref='$ref'"); 
	}


# Set up target file
if(!hook("previewpskipdel")):
if (file_exists($target)) {unlink($target);}
endif;

# Locate imagemagick.
$convert_fullpath = get_utility_path("im-convert");
if ($convert_fullpath==false) {exit("Could not find ImageMagick 'convert' utility at location '$imagemagick_path'");}

debug ("Starting preview preprocessing. File extension is $extension.");

hook("metadata");

/* ----------------------------------------
	Plugin-added preview support
   ----------------------------------------
*/

$preview_preprocessing_results=hook("previewsupport","", array( "extension" => $extension ,"file"=>$file,"target"=>$target));
if (is_array($preview_preprocessing_results)){
	if (isset($preview_preprocessing_results['file'])){
		$file=$preview_preprocessing_results['file'];
	}
	if (isset($preview_preprocessing_results['extension'])){
		$extension=$preview_preprocessing_results['extension'];
	}
}
	
/* ----------------------------------------
	QuickLook Previews (Mac only)
	For everything except Audio/Video files, attempt to generate a QuickLook preview first.
   ----------------------------------------
*/
if (isset($qlpreview_path) && !in_array($extension, $qlpreview_exclude_extensions) && !in_array($extension, $ffmpeg_supported_extensions) && !in_array($extension, $ffmpeg_audio_extensions) && !isset($newfile))
	{
	$qlpreview_command=$qlpreview_path."/qlpreview -generatePreviewOnly yes -imageType jpg -maxWidth 800 -maxHeight 800 -asIcon no -preferFileIcon no -inPath " . escapeshellarg($file) . " -outPath " . escapeshellarg($target);
	debug("qlpreview command: " . $qlpreview_command);
	$output=run_command($qlpreview_command);
	#sleep(4); # Delay to allow processing
	if (file_exists($target)){$newfile = $target;debug("qlpreview success!");}	
	}

/* ----------------------------------------
	Try InDesign - for non-exiftool - CS4 not supported
   ----------------------------------------
*/
# Note: for good results, InDesign Preferences must be set to save Preview image at Extra Large size.
if ($exiftool_fullpath==false)
	{
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
    if ($exiftool_fullpath!=false)
        {
        run_command($exiftool_fullpath.' -b -thumbnailimage '.escapeshellarg($file).' > '.$target);
        }
    if (file_exists($target))
        {
        #if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
        if (filesize_unlimited($target)>0){$newfile = $target;}else{unlink($target);}
        }
    hook("indesign");
    }


/* ----------------------------------------
	Try InDesign - for CS5 (page previews)
   ----------------------------------------
*/
if ($exiftool_fullpath!=false)
	{
	if ($extension=="indd" && !isset($newfile))
		{
		$indd_thumbs = extract_indd_pages ($file);

		if (is_array($indd_thumbs))
			{
			$n=0;	
			foreach ($indd_thumbs as $indd_thumb)
				{
				base64_to_jpeg( $indd_thumb, $target."_".$n);
				$n++;
				}
			$pagescommand="";
			for ($x=0;$x<$n;$x++)
				{
				$pagescommand.=" ".$target."_".$x;
				}
			// process jpgs as a pdf so the existing pdf paging code can be used.	
			$file=get_resource_path($ref,true,"",false,"pdf");		
			$jpg2pdfcommand = $convert_fullpath . " ".$pagescommand." " . $file; 
			$output=run_command($jpg2pdfcommand);
			for ($x=0;$x<$n;$x++)
				{
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
# Note: for good results, Photoshop Preferences must be set to save Preview image at Extra Large size.
if (($extension=="psd" || $extension=="psb") && !isset($newfile))
	{
	global $photoshop_thumb_extract;
	if ($photoshop_thumb_extract)
		{
		if ($exiftool_fullpath!=false)
			{
			run_command($exiftool_fullpath.' -b -PhotoshopThumbnail '.escapeshellarg($file).' > '.$target);
			}
		if (file_exists($target))
			{
			#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
			if (filesize_unlimited($target)>0){$newfile = $target;}else{unlink($target);}
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
    $composite_fullpath = get_utility_path("im-composite");
    $wait = run_command($composite_fullpath . " -compose Dst_Over -tile pattern:checkerboard ".escapeshellarg($file)." ".$target);

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
		run_command($dump_gnash_path.'/dump-gnash -t 1 --screenshot 5 --screenshot-file '.$target.' '.escapeshellarg($file));
		}
	if (file_exists($target))
		{
		#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
		if (filesize_unlimited($target)>0){$newfile = $target;}else{unlink($target);}
		}
		
	}	


/* ----------------------------------------
	Try RAW preview extraction via exiftool
   ----------------------------------------
*/

if (($extension=="cr2" || $extension=="nef" || $extension=="dng" || $extension=="raf" || $extension=="rw2") && !isset($newfile))
	{
	global $cr2_thumb_extract;
	global $nef_thumb_extract;
	global $dng_thumb_extract;
	global $rw2_thumb_extract;
	global $raf_thumb_extract;
	
	if (($extension=="cr2" && $cr2_thumb_extract) || ($extension=="nef" && $nef_thumb_extract) || ($extension=="dng" && $dng_thumb_extract) || ($extension=="rw2" && $rw2_thumb_extract) || ($extension=="raf" && $raf_thumb_extract))
		{
		if ($exiftool_fullpath!=false)
			{	
			// previews are stored in a couple places, and some nef files have large previews in -otherimage
			if ($extension=="rw2"){$bin_tag=" -jpgfromraw ";}
			if ($extension=="nef"){$bin_tag=" -otherimage ";}
			if ($extension=="cr2"||$extension=="dng"||$extension=="raf"){$bin_tag=" -previewimage ";}
			// attempt
			$wait=run_command($exiftool_fullpath.' -b '.$bin_tag.' '.escapeshellarg($file).' > '.$target);

			// check for nef -otherimage failure
			if ($extension=="nef"&&!filesize_unlimited($target)>0)
				{
				unlink($target);	
				$bin_tag=" -previewimage ";
				//2nd attempt
				$wait=run_command($exiftool_fullpath.' -b '.$bin_tag.' '.escapeshellarg($file).' > '.$target);
				}
				
			// NOTE: in case of failures, other suboptimal possibilities 
			// may be explored in the future such as -thumbnailimage and -jpgfromraw, like this:
			// //check for failure
			//if (!filesize_unlimited($target)>0)
				//{
				//unlink($target);	
				//$bin_tag=" -thumbnailimage ";
				//attempt
				//$wait=run_command($exiftool_fullpath.' -b '.$bin_tag.' '.$file.' > '.$target);
				//}
			
			if (filesize_unlimited($target)>0)
				{
				$orientation=get_image_orientation($file);
				if ($orientation!=0)
					{
                    $mogrify_fullpath = get_utility_path("im-mogrify");
                    if ($mogrify_fullpath!=false)
                        {
                        $command = $mogrify_fullpath . ' -rotate +' . $orientation .' '. $target;
                        $wait = run_command($command);
                        }
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
    run_command("unzip -p ".escapeshellarg($file)." \"QuickLook/Thumbnail.jpg\" > $target");
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
	
	run_command($unocommand . " --format=pdf " . escapeshellarg($file));
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
	    sql_query("update resource_alt_files set file_name='$ref-converted.pdf',description='generated by Open Office',file_extension='pdf',file_size='".filesize_unlimited($alt_path)."',unoconv='1' where resource='$ref' and ref='$alt_ref'");

		# Set vars so we continue generating thumbs/previews as if this is a PDF file
	    $extension="pdf";
	    $file=$alt_path;
	    extract_text($ref,$extension,$alt_path);
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

	$wait=run_command("xvfb-run ". $calibrecommand . " " . escapeshellarg($file) . " " .$pdffile." ") ;

    if (file_exists($pdffile))
		{
		# Attach this PDF file as an alternative download.
		sql_query("delete from resource_alt_files where resource = '".$ref."' and unoconv='1'");	
		$alt_ref=add_alternative_file($ref,"PDF version");
		$alt_path=get_resource_path($ref,true,"",false,"pdf",-1,1,false,"",$alt_ref);	
	    copy($pdffile,$alt_path);unlink($pdffile);
	    sql_query("update resource_alt_files set file_name='$ref-converted.pdf',description='generated by Open Office',file_extension='pdf',file_size='".filesize_unlimited($alt_path)."',unoconv='1' where resource='$ref' and ref='$alt_ref'");

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
    run_command("unzip -p ".escapeshellarg($file)." \"Thumbnails/thumbnail.png\" > $target");
    $odcommand = $convert_fullpath . " \"$target\"[0]  \"$target\""; 
    $output = run_command($odcommand);
    if(file_exists($target)){$newfile = $target;}
    }


/* ----------------------------------------
	Try Microsoft OfficeOpenXML Format
	Also try Micrsoft XPS... the sample document I've seen uses the same path for the preview, 
	so it will likely work in most cases, but I think the specs allow it to go anywhere.
   ----------------------------------------
*/
if ((($extension=="docx") || ($extension=="xlsx") || ($extension=="pptx") || ($extension=="xps")) && !isset($newfile))
	{
	run_command("unzip -p ".escapeshellarg($file)." \"docProps/thumbnail.jpeg\" > $target");$newfile = $target;
	}



/* ----------------------------------------
	Try Blender 3D. This runs Blender on the command line to render the first frame of the file.
   ----------------------------------------
*/

if ($extension=="blend" && !isset($newfile))
	{
    global $blender_path;
	$blendercommand=$blender_path;	
	if (!file_exists($blendercommand)|| is_dir($blendercommand)) {$blendercommand=$blender_path . "/blender";}
	if (!file_exists($blendercommand)) {$blendercommand=$blender_path . "\blender.exe";}
	if (!file_exists($blendercommand)) {exit("Could not find blender application. '$blendercommand'");}	
	$error=run_command($blendercommand. " -b ".escapeshellarg($file)." -F JPEG -o $target -f 1");

    if (file_exists($target."0001"))
		{
		copy($target."0001","$target");
		unlink($target."0001");
		$newfile = $target;
		}
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
	run_command($command . " -p a4 " . escapeshellarg($file) . " > \"" . $target . ".ps" . "\"");
	if (file_exists($target . ".ps"))
		{
		# Postscript file exists

        $gscommand = $ghostscript_fullpath . " -dBATCH -dNOPAUSE -sDEVICE=jpeg -r150 -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=1 -dLastPage=1 -dEPSCrop " . escapeshellarg($target . ".ps");
        $output = run_command($gscommand);

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
	if ($exiftool_fullpath!=false)
		{
		run_command($exiftool_fullpath.' -b -picture '.escapeshellarg($file).' > '.$target);
		}
	if (file_exists($target))
		{
		#if the file contains an image, use it; if it's blank, it needs to be erased because it will cause an error in ffmpeg_processing.php
		if (filesize_unlimited($target)>0){$newfile = $target;}else{unlink($target);}
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
$ffmpeg_fullpath = get_utility_path("ffmpeg");

if (($ffmpeg_fullpath!=false) && !isset($newfile) && in_array($extension, $ffmpeg_supported_extensions))
        {
        $snapshottime = 1;
        $out = run_command($ffmpeg_fullpath . " -i " . escapeshellarg($file) . " 2>&1");
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

        if(!hook("previewpskipthumb")): 
        $output = run_command($ffmpeg_fullpath . " -i " . escapeshellarg($file) . " -f image2 -vframes 1 -ss ".$snapshottime." " . escapeshellarg($target));
        endif;

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
if (($ffmpeg_fullpath!=false) && in_array($extension, $ffmpeg_audio_extensions)&& !isset($newfile))
	{
	# Produce the MP3 preview.
	$mp3file = get_resource_path($ref,true,"",false,"mp3"); 
	$output = run_command($ffmpeg_fullpath . " -y -i " . escapeshellarg($file) . " " . $ffmpeg_audio_params . " " . escapeshellarg($mp3file));
	}


/* ----------------------------------------
	Try ImageMagick
   ----------------------------------------
*/
if ((!isset($newfile)) && (!in_array($extension, $ffmpeg_audio_extensions)))
	{
    $prefix="";

	# Preserve colour profiles?    
	$profile="+profile icc -colorspace sRGB"; # By default, strip the colour profiles ('+' is remove the profile, confusingly)
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
			$nfcommand = $convert_fullpath . ' -compress zip -colorspace sRGB -quality 100 -density ' . sprintf("%.1f", $eps_density_x ). 'x' . sprintf("%.1f", $eps_density_y) . ' ' . escapeshellarg($file) . '[0] ' . escapeshellarg($eps_target);
			run_command($nfcommand);
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
	$resolution=150;
	$scr_size=sql_query("select width,height from preview_size where id='scr'");
	$scr_width=$scr_size[0]['width'];
	$scr_height=$scr_size[0]['height'];
	
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
			$pdfinfo=run_command($pdfinfocommand);
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
				$resolution=ceil((max($scr_width,$scr_height)*2)/($pdf_max_dim/72));
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
		$gscommand2 = $ghostscript_fullpath . " -dBATCH -r".$resolution." ".$dUseCIEColor." -dNOPAUSE -sDEVICE=jpeg -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " -dEPSCrop " . escapeshellarg($file);
 		$output=run_command($gscommand2);

    	debug("PDF multi page preview: page $n, executing " . $gscommand2);

	
		# Set that this is the file to be used.
		if (file_exists($target) && $n==1)
			{
			$newfile=$target;$pagecount=$n;
	    	debug("Page $n generated successfully");
			}
			
		# resize directly to the screen size (no other sizes needed)
		 if (file_exists($target)&& $n!=1)
			{
			$command2 = $convert_fullpath . " " . $prefix . escapeshellarg($target) . "[0] -quality $imagemagick_quality -resize ".$scr_width."x".$scr_height . " ".escapeshellarg($target); 
			$output=run_command($command2); $pagecount=$n;
				
			# Add a watermarked image too?
			global $watermark;
    			if (isset($watermark) && $alternative==-1)
    				{
				$path=get_resource_path($ref,true,$size,false,"",-1,$n,true,"",$alternative);
				if (file_exists($path)) {unlink($path);}
    				$watermarkreal=dirname(__FILE__). "/../" . $watermark;
    				
				$command2 = $convert_fullpath . " \"$target\"[0] $profile -quality $imagemagick_quality -resize ".$scr_width."x".$scr_height. " -tile " . escapeshellarg($watermarkreal) . " -draw \"rectangle 0,0 $scr_width,$scr_height\" " . escapeshellarg($path); 
					$output=run_command($command2);
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
			$gscommand2 = $ghostscript_fullpath . " -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($copy_path) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " " . escapeshellarg($file);
	 		$output=run_command($gscommand2);
 		
 			# Update the file extension
 			sql_query("update resource set file_extension='pdf' where ref='$copy'");
 		
 			# Create preview for the page.
 			$pdf_split_pages_to_resources=false; # So we don't get stuck in a loop creating split pages for the single page PDFs.
 			create_previews($copy,false,"pdf");
 			$pdf_split_pages_to_resources=true;
			}
			
		}
        // set page number
        if (isset($pagecount) && $alternative!=-1){
            sql_query("update resource_alt_files set page_count=$pagecount where ref=$alternative");
            }
        else if (isset($pagecount)){
            sql_query("update resource_dimensions set page_count=$pagecount where resource=$ref");
            }
	}
    else
    	{
    	# Not a PDF file, so single extraction only.
			create_previews_using_im($ref,false,$extension,$previewonly,false,$alternative);
			}
	}
	
# If a file has been created, generate previews just as if a JPG was uploaded.
if (isset($newfile))
	{
	create_previews($ref,false,"jpg",$previewonly,false,$alternative);	
	}

?>
