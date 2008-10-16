<?

# Image processing functions
# Functions to allow upload and resizing of images

if (!function_exists("upload_file")){
function upload_file($ref)
	{
	# Process file upload for resource $ref
	
	# Work out which file has been posted (switch is necessary for SWFUpload)
	if (isset($_FILES['userfile'])) {$processfile=$_FILES['userfile'];} else {$processfile=$_FILES['Filedata'];}
	
    $filename=strtolower(str_replace(" ","_",$processfile['name']));
    
    # Work out extension
    $extension=explode(".",$filename);$extension=trim(strtolower($extension[count($extension)-1]));
    
    $status="Please provide a file name.";
    $filepath=get_resource_path($ref,true,"",true,$extension);

    if ($filename!="")
    	{
	    $result=move_uploaded_file($processfile['tmp_name'], $filepath);
    	if ($result==false)
       	 	{
       	 	$status="File upload error. Please check the size of the file you are trying to upload.";
       	 	}
     	else
     		{
     		chmod($filepath,0777);
			$status="Your file has been uploaded.";
    	 	}
    	}
    	
    # Store extension in the database
    sql_query("update resource set file_extension='$extension',preview_extension='$extension' where ref='$ref'");

	# get file metadata 
    global $exiftool_path;
    extract_exif_comment($ref,$extension);

	# Store original filename in field, if set
	global $filename_field;
	if (isset($filename_field))
		{
		update_field($ref,$filename_field,$filename);
		}
    
    # Clear any existing FLV file or multi-page previews.
	global $pdf_pages;
	for ($n=2;$n<=$pdf_pages;$n++)
		{
		# Remove preview page.
		$path=get_resource_path($ref,true,"scr",false,"jpg",-1,$n,false);
		if (file_exists($path)) {unlink($path);}
		# Also try the watermarked version.
		$path=get_resource_path($ref,true,"scr",false,"jpg",-1,$n,true);
		if (file_exists($path)) {unlink($path);}
		}
	# Remove any FLV video preview (except if the actual resource is an FLV file).
	if ($extension!="flv")
		{
		$path=get_resource_path($ref,true,"",false,"flv");
		if (file_exists($path)) {unlink($path);}
		}
    
	# Create previews
	create_previews($ref,false,$extension);

    return $status;
    }}
	
function extract_exif_comment($ref,$extension)
	{
	# Extract the EXIF comment from either the ImageDescription field or the UserComment
	# Also parse IPTC headers and insert
	
	# EXIF headers

	$image=get_resource_path($ref,true,"",false,$extension);
	if (!file_exists($image)) {return false;}

global $exiftool_path,$exif_comment;
if (isset($exiftool_path))
	{
	if (file_exists(stripslashes($exiftool_path) . "/exiftool"))
		{
			
			$read_from=get_exiftool_fields();
			for($i=0;$i< count($read_from);$i++)
				{
				$command=$exiftool_path."/exiftool -p ";
				$field=explode(",",$read_from[$i]['exiftool_field']);
				foreach ($field as $field){
				$command.="'$".$field."' -f -m -ScanforXMP -fast $image" ;}
				$metadata=shell_exec($command);
				if (trim($metadata)!="-"){update_field($ref,$read_from[$i]['ref'],iptc_return_utf8($metadata));}
				}
		}
	}
elseif (isset($exif_comment))
{
	$data=@exif_read_data($image);

	if ($data!==false)
		{
		$comment="";
		#echo "<pre>EXIF\n";print_r($data);exit();

		if (isset($data["ImageDescription"])) {$comment=$data["ImageDescription"];}
		if (($comment=="") && (isset($data["COMPUTED"]["UserComment"]))) {$comment=$data["COMPUTED"]["UserComment"];}
		if ($comment!="")
			{
			# Convert to UTF-8
			$comment=iptc_return_utf8($comment);
			
			# Save comment
			global $exif_comment;
			update_field($ref,$exif_comment,$comment);
			}
		if (isset($data["Model"]))
			{
			# Save camera make/model
			global $exif_model;
			update_field($ref,$exif_model,$data["Model"]);
			}
		if (isset($data["DateTimeOriginal"]))
			{
			# Save camera date/time
			global $exif_date;
			$date=$data["DateTimeOriginal"];
			# Reformat date to ISO standard
			$date=substr($date,0,4) . "-" . substr($date,5,2) . "-" . substr($date,8);
			update_field($ref,$exif_date,$date);
			}
		}
		
	# Try IPTC headers
	$size = getimagesize($image, $info);
	if (isset($info["APP13"]))
		{
		$iptc = iptcparse($info["APP13"]);
		#echo "<pre>IPTC\n";print_r($iptc);exit();

		# Look for iptc fields, and insert.
		$fields=sql_query("select * from resource_type_field where length(iptc_equiv)>0");
		for ($n=0;$n<count($fields);$n++)
			{
			$iptc_equiv=$fields[$n]["iptc_equiv"];
			if (isset($iptc[$iptc_equiv][0]))
				{
				# Found the field
				if (count($iptc[$iptc_equiv])>1)
					{
					# Multiple values (keywords)
					$value="";
					for ($m=0;$m<count($iptc[$iptc_equiv]);$m++)
						{
						if ($m>0) {$value.=", ";}
						$value.=$iptc[$iptc_equiv][$m];
						}
					}
				else
					{
					$value=$iptc[$iptc_equiv][0];
					}
					
				$value=iptc_return_utf8($value);
				
				# Date parsing
				if ($fields[$n]["type"]==4)
					{
					$value=substr($value,0,4) . "-" . substr($value,4,2) . "-" . substr($value,6,2);
					}
				
				if (trim($value)!="") {update_field($ref,$fields[$n]["ref"],$value);}
				}			
			}
		}
	}
	}

function iptc_return_utf8($text)
	{
	# For the given $text, return the utf-8 equiv.
	# Used for iptc headers to auto-detect the character encoding.
	global $iptc_expectedchars;
	
	# No inconv library? Return text as-is
	if (!function_exists("iconv")) {return $text;}
	
	$try=array("UTF-8","ISO-8859-1","Macintosh","Windows-1252");
	for ($n=0;$n<count($try);$n++)
		{
		if ($try[$n]=="UTF-8") {$trans=$text;} else {$trans=@iconv($try[$n], "UTF-8", $text);}
		for ($m=0;$m<strlen($iptc_expectedchars);$m++)
			{
			if (strpos($trans,substr($iptc_expectedchars,$m,1))!==false) {return $trans;}
			}
		}
	return $text;
	}
	
function create_previews($ref,$thumbonly=false,$extension="jpg",$previewonly=false)
	{
	global $imagemagick_path,$ghostscript_path;

	# File checksum (experimental) - disabled for now
	# if (!$previewonly) {generate_file_checksum($ref,$extension);}
	
	if (($extension=="jpg") || ($extension=="jpeg") || ($extension=="png") || ($extension=="gif"))
	# Create image previews for built-in supported file types only (JPEG, PNG, GIF)
		{
		if (isset($imagemagick_path))
			{
			create_previews_using_im($ref,$thumbonly,$extension,$previewonly);
			}
		else
			{
			# ----------------------------------------
			# Use the GD library to perform the resize
			# ----------------------------------------


			# For resource $ref, (re)create the various preview sizes listed in the table preview_sizes
			# Only create previews where the target size IS LESS THAN OR EQUAL TO the source size.
			# Set thumbonly=true to (re)generate thumbnails only.

			if (!$previewonly)
				{
				$file=get_resource_path($ref,true,"",false,$extension);	
				}
			else
				{
				# We're generating based on a new preview (scr) image.
				$file=get_resource_path($ref,true,"tmp",false,$extension);	
				}

			$sizes="";
			if ($thumbonly) {$sizes=" where id='thm' or id='col'";}
			if ($previewonly) {$sizes=" where id='thm' or id='col' or id='pre' or id='scr'";}

			# fetch source image size, if we fail, exit this function (file not an image, or file not a valid jpg/png/gif).
			if ((list($sw,$sh) = @getimagesize($file))===false) {return false;}
		
			$ps=sql_query("select * from preview_size $sizes");
			for ($n=0;$n<count($ps);$n++)
				{
				# fetch target width and height
				$tw=$ps[$n]["width"];$th=$ps[$n]["height"];
				$id=$ps[$n]["id"];
			
				# Find the target path and delete anything that's already there.
				$path=get_resource_path($ref,true,$ps[$n]["id"],false);
				if (file_exists($path)) {unlink($path);}
				# Also try the watermarked version.
				$wpath=get_resource_path($ref,true,$ps[$n]["id"],false,"jpg",-1,1,true);
				if (file_exists($wpath)) {unlink($wpath);}

	      # only create previews where the target size IS LESS THAN OR EQUAL TO the source size.
				# or when producing a small thumbnail (to make sure we have that as a minimum)
				if (($sw>$tw) || ($sh>$th) || ($id=="thm") || ($id=="col"))
					{
					# Calculate width and height.
					if ($sw>$sh) {$ratio = ($tw / $sw);} # Landscape
					else {$ratio = ($th / $sh);} # Portrait
					$tw=floor($sw*$ratio);
					$th=floor($sh*$ratio);

					# ----------------------------------------
					# Use the GD library to perform the resize
					# ----------------------------------------
				
					$target = imagecreatetruecolor($tw,$th);
				
					if ($extension=="png")
						{
						$source = @imagecreatefrompng($file);
						if ($source===false) {return false;}
						}
					elseif ($extension=="gif")
						{
						$source = @imagecreatefromgif($file);
						if ($source===false) {return false;}
						}
					else
						{
						$source = @imagecreatefromjpeg($file);
						if ($source===false) {return false;}
						}
					
					imagecopyresampled($target,$source,0,0,0,0,$tw,$th,$sw,$sh);
					imagejpeg($target,$path,90);

					if ($ps[$n]["id"]=="thm") {extract_mean_colour($target,$ref);}
					imagedestroy($target);
					}
				elseif (($id=="pre") || ($id=="thm") || ($id=="col"))	
					{
					# If the source is smaller than the pre/thm/col, we still need these sizes; just copy the file
					copy($file,get_resource_path($ref,true,$id,false,$extension));
					if ($id=="thm") {sql_query("update resource set thumb_width='$sw',thumb_height='$sh' where ref='$ref'");}
					}
				}
			# flag database so a thumbnail appears on the site
			sql_query("update resource set has_image=1,preview_extension='jpg' where ref='$ref'");
			}
		}
	else
		{
		# Use imagemagick? (also includes ffmpeg for video handling functions)
		if (isset($imagemagick_path))
			{
      		//include "include/imagemagick.php";
      		include(dirname(__FILE__)."/imagemagick.php");
			}
		}
	return true;
	}

function create_previews_using_im($ref,$thumbonly=false,$extension="jpg",$previewonly=false)
	{
	global $imagemagick_path,$imagemagick_preserve_profiles,$imagemagick_quality;

	if (isset($imagemagick_path))
		{

		# ----------------------------------------
		# Use ImageMagick to perform the resize
		# ----------------------------------------

		# For resource $ref, (re)create the various preview sizes listed in the table preview_sizes
		# Set thumbonly=true to (re)generate thumbnails only.

		if (!$previewonly)
			{
			$file=get_resource_path($ref,true,"",false,$extension);	
			}
		else
			{
			# We're generating based on a new preview (scr) image.
			$file=get_resource_path($ref,true,"tmp",false,$extension);	
			}

		# Locate imagemagick.
		$command=$imagemagick_path . "/bin/convert";
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
		if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
		if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility.'");}	

		$prefix = '';
		# Camera RAW images need prefix
		if (preg_match('/^(dng|nef|x3f|cr2|crw|mrw|orf|raf|dcr)$/i', $extension, $rawext)) { $prefix = $rawext[0] .':'; }

		$command .= ' '. escapeshellarg($prefix.$file) .'[0] null: -flatten -quality ' . $imagemagick_quality;

		# Locate imagemagick.
		$identcommand=$imagemagick_path . "/bin/identify";
		if (!file_exists($identcommand)) {$identcommand=$imagemagick_path . "/identify";}
		if (!file_exists($identcommand)) {$identcommand=$imagemagick_path . "\identify.exe";}
		if (!file_exists($identcommand)) {exit("Could not find ImageMagick 'identify' utility.'");}	
		# Get image's dimensions.
		$identcommand .= ' -format %wx%h '. escapeshellarg($prefix . $file) .'[0]';
		$identoutput=shell_exec($identcommand);
		preg_match('/^([0-9]+)x([0-9]+)$/ims',$identoutput,$smatches);
				if ((@list(,$sw,$sh) = $smatches)===false) { return false; }

		$sizes="";
		if ($thumbonly) {$sizes=" where id='thm' or id='col'";}
		if ($previewonly) {$sizes=" where id='thm' or id='col' or id='pre' or id='scr'";}

		$ps=sql_query("select * from preview_size $sizes order by width asc, height asc");
		$highestsize = false;
		for ($n=0;$n<count($ps);$n++)
			{
			# fetch target width and height
			$tw=$ps[$n]["width"];$th=$ps[$n]["height"];
			$id=$ps[$n]["id"];

			if ((!$highestsize && !eregi("jp[e]?g", $extension)) || ($sw>$tw) || ($sh>$th) || ($id == "pre") || ($id=="thm") || ($id=="col"))
				{
				if (($sw<$tw) && ($sh<$th))
					{
					$highestsize = true;
					}
				# Find the target path and delete anything that's already there.
				$path=get_resource_path($ref,true,$ps[$n]["id"],false);
				if (file_exists($path)) {unlink($path);}
				# Also try the watermarked version.
				$wpath=get_resource_path($ref,true,$ps[$n]["id"],false,"jpg",-1,1,true);
				if (file_exists($wpath)) {unlink($wpath);}
	
				# Preserve colour profiles? (omit for smaller sizes)   
				$profile="+profile \"*\" -colorspace RGB"; # By default, strip the colour profiles ('+' is remove the profile, confusingly)
				if ($imagemagick_preserve_profiles && $id!="thm" && $id!="col" && $id!="pre" && $id!="scr") {$profile="";}

				$runcommand = $command ." $profile -resize " . $tw . "x" . $th . "\">\" $path";
				$output=shell_exec($runcommand);  

				# Add a watermarked image too?
				global $watermark;
				if (isset($watermark) && ($ps[$n]["internal"]==1 || $ps[$n]["allow_preview"]==1))
					{
					$path=get_resource_path($ref,true,$ps[$n]["id"],false,"",-1,1,true);
					if (file_exists($path)) {unlink($path);}
   					$watermarkreal=myrealpath($watermark);

					$runcommand = $command ." $profile -resize " . $tw . "x" . $th . "\">\" -tile $watermarkreal -draw \"rectangle 0,0 $tw,$th\" $path"; 
					$output=shell_exec($runcommand);  
					}
				}
			}

		# For the thumbnail image, call extract_mean_colour() to save the colour/size information
		$target=@imagecreatefromjpeg(get_resource_path($ref,true,"thm",false));
		if ($target) 
			{
			extract_mean_colour($target,$ref);
			# flag database so a thumbnail appears on the site
			sql_query("update resource set has_image=1,preview_extension='jpg' where ref='$ref'");
			}

		return true;
		}
	else
		{
		return false;
		}
	}

function extract_mean_colour($image,$ref)
	{
	# for image $image, calculate the mean colour and update this to the image_red, image_green, image_blue tables
	# in the resources table.
	# Also - we insert the height and width of the thumbnail at this stage as all information is available and we
	# are already performing an update on the resource record.
	
	$width=imagesx($image);$height=imagesy($image);
	$totalred=0;
	$totalgreen=0;
	$totalblue=0;
	$total=0;
	
	for ($y=0;$y<20;$y++)
		{
		for ($x=0;$x<20;$x++)
			{
			$rgb = imagecolorat($image, $x*($width/20), $y*($height/20));
			$red = ($rgb >> 16) & 0xFF;
			$green = ($rgb >> 8) & 0xFF;
			$blue = $rgb & 0xFF;

			# calculate deltas (remove brightness factor)
			$cmax=max($red,$green,$blue);
			$cmin=min($red,$green,$blue);if ($cmax==$cmin) {$cmax=10;$cmin=0;} # avoid division errors
			if (abs($cmax-$cmin)>=20) # ignore gray/white/black
				{
				$red=floor((($red-$cmin)/($cmax-$cmin)) * 1000);
				$green=floor((($green-$cmin)/($cmax-$cmin)) * 1000);
				$blue=floor((($blue-$cmin)/($cmax-$cmin)) * 1000);

				$total++;
				$totalred+=$red;
				$totalgreen+=$green;
				$totalblue+=$blue;
				}
			}
		}
	if ($total==0) {$total=1;}
	$totalred=floor($totalred/$total);
	$totalgreen=floor($totalgreen/$total);
	$totalblue=floor($totalblue/$total);
	
	$colkey=get_colour_key($image);
	
	sql_query("update resource set image_red='$totalred', image_green='$totalgreen', image_blue='$totalblue',colour_key='$colkey',thumb_width='$width', thumb_height='$height' where ref='$ref'");
	}

function get_colour_key($image)
	{
	# Extracts a colour key for the image, like a soundex.
	$width=imagesx($image);$height=imagesy($image);
	$colours=array(
	"K"=>array(0,0,0), 			# Black
	"W"=>array(255,255,255),	# White
	"E"=>array(200,200,200),	# Grey
	"E"=>array(140,140,140),	# Grey
	"E"=>array(100,100,100),	# Grey
	"R"=>array(255,0,0),		# Red
	"R"=>array(128,0,0),		# Dark Red
	"R"=>array(180,0,40),		# Dark Red
	"G"=>array(0,255,0),		# Green
	"G"=>array(0,128,0),		# Dark Green
	"G"=>array(80,120,90),		# Faded Green
	"G"=>array(140,170,90),		# Pale Green
	"B"=>array(0,0,255),		# Blue
	"B"=>array(0,0,128),		# Dark Blue
	"B"=>array(90,90,120),		# Dark Blue
	"B"=>array(60,60,90),		# Dark Blue
	"B"=>array(90,140,180),		# Light Blue
	"C"=>array(0,255,255),		# Cyan
	"C"=>array(0,200,200),		# Cyan
	"M"=>array(255,0,255),		# Magenta
	"Y"=>array(255,255,0),		# Yellow
	"Y"=>array(180,160,40),		# Yellow
	"Y"=>array(210,190,60),		# Yellow
	"O"=>array(255,128,0),		# Orange
	"O"=>array(200,100,60),		# Orange
	"P"=>array(255,128,128),	# Pink
	"P"=>array(200,180,170),	# Pink
	"P"=>array(200,160,130),	# Pink
	"P"=>array(190,120,110),	# Pink
	"N"=>array(110,70,50),		# Brown
	"N"=>array(180,160,130),	# Pale Brown
	"N"=>array(170,140,110),	# Pale Brown
	);
	$table=array();
	$depth=50;
	for ($y=0;$y<$depth;$y++)
		{
		for ($x=0;$x<$depth;$x++)
			{
			$rgb = imagecolorat($image, $x*($width/$depth), $y*($height/$depth));
			$red = ($rgb >> 16) & 0xFF;
			$green = ($rgb >> 8) & 0xFF;
			$blue = $rgb & 0xFF;
			# Work out which colour this is
			$bestdist=99999;$bestkey="";
			reset ($colours);
			foreach ($colours as $key=>$value)
				{
				$distance=sqrt(pow(abs($red-$value[0]),2)+pow(abs($green-$value[1]),2)+pow(abs($blue-$value[2]),2));
				if ($distance<$bestdist) {$bestdist=$distance;$bestkey=$key;}
				}
			# Add this colour to the colour table.
			if (array_key_exists($bestkey,$table)) {$table[$bestkey]++;} else {$table[$bestkey]=1;}
			}
		}
	asort($table);reset($table);$colkey="";
	foreach ($table as $key=>$value) {$colkey.=$key;}
	$colkey=substr(strrev($colkey),0,5);
	return($colkey);
	}

function tweak_preview_images($ref,$rotateangle,$gamma,$extension="jpg")
	{
	# Tweak all preview images
	# On the edit screen, preview images can be either rotated or gamma adjusted. We keep the high(original) and low resolution print versions intact as these would be adjusted professionally when in use in the target application.

	# Use the screen resolution version for processing
	$file=get_resource_path($ref,true,"scr",false,$extension);
	if (!file_exists($file)) {return false;}
	
	if ($extension=="png")
		{
	    $source = imagecreatefrompng($file);
		}
	elseif ($extension=="gif")
		{
	    $source = imagecreatefromgif($file);
		}
	else
		{
	    $source = imagecreatefromjpeg($file);
		}
		
	# Apply tweaks
	if ($rotateangle!=0)
		{
		# Use built-in function if available, else use function in this file
		if (function_exists("imagerotate"))
			{
			$source=imagerotate($source,$rotateangle,0);
			}
		else
			{
			$source=AltImageRotate($source,$rotateangle);
			}
		}
		
	if ($gamma!=0) {imagegammacorrect($source,1.0,$gamma);}

	# Save source image and fetch new dimensions
	if ($extension=="png")
		{
		imagepng($source,$file);
		}
	elseif ($extension=="gif")
		{
		imagegif($source,$file);
		}
	else
		{
		imagejpeg($source,$file,95);
		}

    list($tw,$th) = @getimagesize($file);	
    
	# Save all images
	$ps=sql_query("select * from preview_size where (internal=1 or allow_preview=1) and id<>'scr'");
	for ($n=0;$n<count($ps);$n++)
		{
		# fetch target width and height
	    $file=get_resource_path($ref,true,$ps[$n]["id"],false,$extension);		
	    list($sw,$sh) = @getimagesize($file);
	    
		if ($rotateangle!=0) {$temp=$sw;$sw=$sh;$sh=$temp;}
		
		# Rescale image
		$target = imagecreatetruecolor($sw,$sh);
		imagecopyresampled($target,$source,0,0,0,0,$sw,$sh,$tw,$th);
		if ($extension=="png")
			{
			imagepng($target,$file);
			}
		elseif ($extension=="gif")
			{
			imagegif($target,$file);
			}
		else
			{
			imagejpeg($target,$file,95);
			}
		}
	if ($rotateangle!=0)
		{
		# Swap thumb heights/widths
		$ts=sql_query("select thumb_width,thumb_height from resource where ref='$ref'");
		sql_query("update resource set thumb_width='" . $ts[0]["thumb_height"] . "',thumb_height='" . $ts[0]["thumb_width"] . "' where ref='$ref'");
		}
	}

function AltImageRotate($src_img, $angle) {

	if ($angle==270) {$angle=-90;}

    $src_x = imagesx($src_img);
    $src_y = imagesy($src_img);
    if ($angle == 90 || $angle == -90) {
        $dest_x = $src_y;
        $dest_y = $src_x;
    } else {
        $dest_x = $src_x;
        $dest_y = $src_y;
    }

    $rotate=imagecreatetruecolor($dest_x,$dest_y);
    imagealphablending($rotate, false);

    switch ($angle) {
        case 90:
            for ($y = 0; $y < ($src_y); $y++) {
                for ($x = 0; $x < ($src_x); $x++) {
                    $color = imagecolorat($src_img, $x, $y);
                    imagesetpixel($rotate, $dest_x - $y - 1, $x, $color);
                }
            }
            break;
        case -90:
            for ($y = 0; $y < ($src_y); $y++) {
                for ($x = 0; $x < ($src_x); $x++) {
                    $color = imagecolorat($src_img, $x, $y);
                    imagesetpixel($rotate, $y, $dest_y - $x - 1, $color);
                }
            }
            break;
        case 180:
            for ($y = 0; $y < ($src_y); $y++) {
                for ($x = 0; $x < ($src_x); $x++) { 
                    $color = imagecolorat($src_img, $x, $y); 
                    imagesetpixel($rotate, $dest_x - $x - 1, $dest_y - $y - 1, $color);
                }
            }
            break;
        default: $rotate = $src_img;
    };
    return $rotate;
}

function base64_to_jpeg( $imageData, $outputfile ) {

 $jpeg = fopen( $outputfile, "wb" ) or die ("can't open");
 fwrite( $jpeg, base64_decode( $imageData ) );
 fclose( $jpeg );
}

function extract_indd_thumb ($filename) {
   
    $source = file_get_contents($filename);

    $xmpdata_start = strrpos($source,"<xap:Thumbnails");
    $xmpdata_end = strrpos($source,"</xap:Thumbnails>");
    $xmplength = $xmpdata_end-$xmpdata_start;
    $xmpdata = substr($source,$xmpdata_start,$xmplength+12);
    $regexp     = "/<xapGImg:image>.+<\/xapGImg:image>/";
    preg_match ($regexp, $xmpdata, $r);
    if (isset($r['0'])){
    	$indd_thumb = strip_tags($r['0']);
    	$indd_thumb = str_replace("#xA;","",$indd_thumb);
    	return $indd_thumb;} else {return "no";}
     }
 
 
function generate_file_checksum($resource,$extension)
	{
	global $file_checksums;
	if ($file_checksums)
		{
		# Generates a unique checksum for the given file, based on the first 50K and the file size.
		$path=get_resource_path($resource,true,"",false,$extension);
		if (file_exists($path))
			{
			# Fetch the string used to generate the unique ID
			$use=filesize($path) . "_" . file_get_contents($path,null,null,0,50000);
			
			# Generate the ID and store.
			$checksum=md5($use);
			sql_query("update resource set file_checksum='" . escape_check($checksum) . "' where ref='$resource'");
			}
		}
	}

if (!function_exists("upload_preview")){
function upload_preview($ref)
	{
	# Upload a preview image only.
	$processfile=$_FILES['userfile'];
    $filename=strtolower(str_replace(" ","_",$processfile['name']));
    
    # Work out extension
    $extension=explode(".",$filename);$extension=trim(strtolower($extension[count($extension)-1]));

	# Move uploaded file into position.	
    $filepath=get_resource_path($ref,true,"tmp",true,$extension);
    $result=move_uploaded_file($processfile['tmp_name'], $filepath);
   	if ($result!=false) {chmod($filepath,0777);}
    
	# Create previews
	create_previews($ref,false,$extension,true);

	# Delete temporary file.
	unlink($filepath);

    return true;
    }}
 
?>
