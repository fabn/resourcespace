<?php
/**
 * Image processing functions
 * 
 * Functions to allow upload and resizing of images.
 * 
 * @package ResourceSpace
 * @subpackage Includes
 * @todo Document
 */

if (!function_exists("upload_file")){
function upload_file($ref,$no_exif=false,$revert=false)
	{
	# revert is mainly for metadata reversion, removing all metadata and simulating a reupload of the file from scratch.
	
	hook ("removeannotations");
	
	# Process file upload for resource $ref
	if ($revert==true){
		global $filename_field;
		$original_filename=get_data_by_field($ref,$filename_field);
		sql_query("delete from resource_data where resource=$ref");
		sql_query("delete from resource_keyword where resource=$ref");
		#clear 'joined' display fields which are based on metadata that is being deleted in a revert (original filename is reinserted later)
		$display_fields=get_resource_table_joins();
		$clear_fields="";
		for ($x=0;$x<count($display_fields);$x++){ 
			$clear_fields.="field".$display_fields[$x]."=''";
			if ($x<count($display_fields)-1){$clear_fields.=",";}
			}	
		sql_query("update resource set ".$clear_fields." where ref=$ref");
		#also add the ref back into keywords:
		add_keyword_mappings($ref, $ref , -1);
		$extension=sql_value("select file_extension value from resource where ref=$ref","");
		$filename=get_resource_path($ref,true,"",false,$extension);
		$processfile['tmp_name']=$filename; }
	else{
		# Work out which file has been posted (switch is necessary for SWFUpload)
		if (isset($_FILES['userfile'])) {$processfile=$_FILES['userfile'];} else {$processfile=$_FILES['Filedata'];}
		$filename=$processfile['name'];
	}

    # Work out extension
	if (!isset($extension)){
		global $exiftool_path;
		# first try to get it from the filename
		$extension=explode(".",$filename);
		if(count($extension)>1){
			$extension=trim(strtolower($extension[count($extension)-1]));
			} 
		# if not, try exiftool	
		else if (isset($exiftool_path) && file_exists(stripslashes($exiftool_path) . "/exiftool"))
			{
			$file_type_by_exiftool=shell_exec($exiftool_path."/exiftool -filetype -s -s -s ".escapeshellarg($processfile['tmp_name']));
			if (strlen($file_type_by_exiftool)>0){$extension=str_replace(" ","_",trim(strtolower($file_type_by_exiftool)));$filename=$filename;}else{return false;}
			}
		# if no clue of extension by now, return false		
		else {return false;}	
	}
	
    # Banned extension?
    global $banned_extensions;
    if (in_array($extension,$banned_extensions)) {return false;}
    
    $status="Please provide a file name.";
    $filepath=get_resource_path($ref,true,"",true,$extension);

	if (!$revert){ 
    # Remove existing file, if present
    $old_extension=sql_value("select file_extension value from resource where ref='$ref'","");
    if ($old_extension!="")	
    	{
    	$old_path=get_resource_path($ref,true,"",true,$old_extension);
    	if (file_exists($old_path)) {unlink($old_path);}
    	}
	}	

	if (!$revert){
    if ($filename!="")
    	{
    	global $jupload_alternative_upload_location;
    	if (isset($jupload_alternative_upload_location))
    		{
    		# JUpload - file was sent chunked and reassembled - use the reassembled file location
		    $result=rename($jupload_alternative_upload_location, $filepath);
    		}
		else
			{
			# Standard upload.
			if (!$revert){
		    $result=move_uploaded_file($processfile['tmp_name'], $filepath);
			} else {$result=true;}
		}
			
    	if ($result==false)
       	 	{
       	 	$status="File upload error. Please check the size of the file you are trying to upload.";
       	 	return false;
       	 	}
     	else
     		{
     		chmod($filepath,0777);
			$status="Your file has been uploaded.";
    	 	}
    	}
    }	
    
	# Store extension in the database and update file modified time.
	if ($revert){$has_image="";} else {$has_image=",has_image=0";}
    sql_query("update resource set file_extension='$extension',preview_extension='jpg',file_modified=now() $has_image where ref='$ref'");

	# delete existing resource_dimensions
    sql_query("delete from resource_dimensions where resource='$ref'");
	# get file metadata 
    global $exiftool_path;
    if (!$no_exif) {extract_exif_comment($ref,$extension);}

	# extract text from documents (e.g. PDF, DOC).
	global $extracted_text_field;
	if (isset($extracted_text_field) && !$no_exif) {extract_text($ref,$extension);}

	# Store original filename in field, if set
	global $filename_field;
	if (isset($filename_field))
		{
		if (!$revert){
			update_field($ref,$filename_field,$filename);
			}
		else {
			update_field($ref,$filename_field,$original_filename);
			}		
		}
    
   if (!$revert){
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
	global $ffmpeg_preview_extension;
	if ($extension!=$ffmpeg_preview_extension)
		{
		$path=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension);
		if (file_exists($path)) {unlink($path);}
		}
	# Remove any FLV preview-only file
	$path=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
	if (file_exists($path)) {unlink($path);}

	
	# Remove any MP3 (except if the actual resource is an MP3 file).
	if ($extension!="mp3")
		{
		$path=get_resource_path($ref,true,"",false,"mp3");
		if (file_exists($path)) {unlink($path);}
		}	
    
	# Create previews
		global $enable_thumbnail_creation_on_upload;
		if ($enable_thumbnail_creation_on_upload)
			{ 
			create_previews($ref,false,$extension);
			}
		else
			{
			# Offline thumbnail generation is being used. Set 'has_image' to zero so the offline create_previews.php script picks this up.
			sql_query("update resource set has_image=0 where ref='$ref'");
			}
		}
		
    return $status;
    }}
	
function extract_exif_comment($ref,$extension="")
	{
	# Extract the EXIF comment from either the ImageDescription field or the UserComment
	# Also parse IPTC headers and insert
	# EXIF headers

	$image=get_resource_path($ref,true,"",false,$extension);
	if (!file_exists($image)) {return false;}
	
	hook("pdfsearch");

global $exiftool_path,$exif_comment,$exiftool_no_process,$exiftool_resolution_calc, $disable_geocoding;
if (isset($exiftool_path) && !in_array($extension,$exiftool_no_process))
	{
	if (file_exists(stripslashes($exiftool_path) . "/exiftool") || file_exists(stripslashes($exiftool_path) . "/exiftool.exe"))
			{	
			$resource=get_resource_data($ref);
			
			hook("beforeexiftoolextraction");
			
			if ($exiftool_resolution_calc)
				{
				# see if we can use exiftool to get resolution/units, and dimensions here.
				# Dimensions are normally extracted once from the view page, but for the original file, it should be done here if possible,
				# and exiftool can provide more data. 
			
				$command=$exiftool_path."/exiftool -s -s -s -t -composite:imagesize -xresolution -resolutionunit " . escapeshellarg($image);
				$dimensions_resolution_unit=explode("\t",shell_exec($command));
				# if dimensions resolution and unit could be extracted, add them to the database.
				# they can be used in view.php to give more accurate data.
				if (count($dimensions_resolution_unit)==3)
					{
					$dru=$dimensions_resolution_unit;
					$filesize=filesize($image); 
					$wh=explode("x",$dru[0]);
					$width=$wh[0];
					$height=$wh[1];
					$resolution=$dru[1];
					$unit=$dru[2];
					sql_query("insert into resource_dimensions (resource, width, height, resolution, unit, file_size) values ('$ref', '$width', '$height', '$resolution', '$unit', '$filesize')");  
					}
				}
			
			$read_from=get_exiftool_fields($resource['resource_type']);

			# run exiftool to get all the valid fields. Use -s -s option so that
			# the command result isn't printed in columns, which will help in parsing
			# We then split the lines in the result into an array
			$command=$exiftool_path."/exiftool -s -s -f -m -d \"%Y-%m-%d %H:%M:%S\" -G " . escapeshellarg($image);
			$metalines = explode("\n", shell_exec($command));

			$metadata = array(); # an associative array to hold metadata field/value pairs
			
			# go through each line and split field/value using the first
			# occurrance of ": ".  The keys in the associative array is converted
			# into uppercase for easier lookup later
			foreach($metalines as $metaline)
				{
				# Use stripos() if available, but support earlier PHP versions if not.
				if (function_exists("stripos"))
					{
					$pos=stripos($metaline, ": ");
					}
				else
					{
					$pos=strpos($metaline, ": ");
					}
	
				if ($pos) #get position of first ": ", return false if not exist
					{
					# add to the associative array, also clean up leading/trailing space & single quote (on windows sometimes)
					
					# Extract group name and tag name.
					$s=explode("]",substr($metaline, 0, $pos));
					if (count($s)>1 && strlen($s[0])>1)
						{
						# Extract value
						$value=trim(substr($metaline,$pos+2));
						
						# Extract group name and tag name
						$groupname=strtoupper(substr($s[0],1));
						$tagname=strtoupper(trim($s[1]));
						
						# Store both tag data under both tagname and groupname:tagname, to support both formats when mapping fields. 
						$metadata[$tagname] = $value;
						$metadata[$groupname . ":" . $tagname] = $value;
						debug("Exiftool: extracted field '$groupname:$tagname', value is '$value'");
						}
					}
				}

		// We try to fetch the original filename from database.
		$resources = sql_query("SELECT resource.file_path FROM resource WHERE resource.ref = " . $ref);

		if($resources)
			{
			$resource = $resources[0];
			if($resource['file_path'])
				{
				$metadata['FILENAME'] = mb_basename($resource['file_path']);
				}
			}

		if (isset($metadata['FILENAME'])) {$metadata['STRIPPEDFILENAME'] = strip_extension($metadata['FILENAME']);}
		if (!$disable_geocoding && isset($metadata['GPSLATITUDE'])){
			
			# Set vars
            $dec_long=0;$dec_lat=0;

            #Convert latititude to decimal.
            if (preg_match("/^(?<degrees>\d?[\.\d]?) deg (?<minutes>\d?[\.\d]?)' (?<seconds>\d?[\.\d]+)\"/", $metadata['GPSLATITUDE'], $latitude)){
                $dec_lat = $latitude['degrees'] + $latitude['minutes']/60 + $latitude['seconds']/(60*60);
            }
            if (preg_match("/^(?<degrees>\d?[\.\d]?) deg (?<minutes>\d?[\.\d]?)' (?<seconds>\d?[\.\d]+)\"/", $metadata['GPSLONGITUDE'], $longitude)){
                $dec_long = $longitude['degrees'] + $longitude['minutes']/60 + $longitude['seconds']/(60*60);           
            }
            
            if (substr($metadata['GPSLATITUDEREF'],0,1)=='S')
                $dec_lat = -1 * $dec_lat;
            if (substr($metadata['GPSLONGITUDEREF'],0,1)=='W') # Support iPhone 3GS which uses 'West' not 'W'.
                $dec_long = -1 * $dec_long;
            $gps_field_ref = sql_value('SELECT ref as value FROM resource_type_field WHERE name="geolocation"', '');
            if ($gps_field_ref!='' && $dec_long!=0 && $dec_lat!=0){
                update_field($ref, $gps_field_ref, $dec_lat.','.$dec_long);
            }
        }
		# now we lookup fields from the database to see if a corresponding value
		# exists in the uploaded file
		for($i=0;$i< count($read_from);$i++)
			{
			$field=explode(",",$read_from[$i]['exiftool_field']);
			foreach ($field as $subfield)
				{
				$subfield = strtoupper($subfield); // convert to upper case for easier comparision
				
				if (in_array($subfield, array_keys($metadata)) && $metadata[$subfield] != "-" && trim($metadata[$subfield])!="")
					{
					$read=true;
					$value=$metadata[$subfield];
					
					# Dropdown box or checkbox list?
					if ($read_from[$i]["type"]==2 || $read_from[$i]["type"]==3)
						{
						# Check that the value is one of the options and only insert if it is an exact match.
	
						# The use of safe_file_name and strtolower ensures matching takes place on alphanumeric characters only and ignores case.
						
						# First fetch all options in all languages
						$options=trim_array(explode(",",strtolower(i18n_get_indexable($read_from[$i]["options"]))));
						for ($n=0;$n<count($options);$n++)	{$options[$n]=safe_file_name($options[$n]);}

						# If not in the options list, do not read this value
						$s=trim_array(explode(",",$value));
						$value=""; # blank value
						for ($n=0;$n<count($s);$n++)
							{
							if (trim($s[0])!="" && (in_array(safe_file_name(strtolower($s[$n])),$options))) {$value.="," . $s[$n];} 							
							}
						#echo($read_from[$i]["ref"] . " = " . $value . "<br>");
						}
					
					# Read the data.				
					if ($read) {
						$plugin="../plugins/exiftool_filter_" . $read_from[$i]['name'] . ".php";
						if (!file_exists($plugin)){$plugin="../../plugins/exiftool_filter_" . $read_from[$i]['name'] . ".php";}
						if (file_exists($plugin)) {include $plugin;}
						update_field($ref,$read_from[$i]['ref'],iptc_return_utf8($value));}
					}
				}
			}

		}

	}
elseif (isset($exif_comment))
{
	#
	# Exiftool is not installed. As a fallback we grab some predefined basic fields using the PHP function
	# exif_read_data()
	#
	
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
			$date=substr($date,0,4) . "-" . substr($date,5,2) . "-" . substr($date,8,11);
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
	
	# Update the XML metadata dump file.
	update_xml_metadump($ref);
	
	# Auto fill any blank fields.
	autocomplete_blank_fields($ref);
	
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
	
function create_previews($ref,$thumbonly=false,$extension="jpg",$previewonly=false,$previewbased=false,$alternative=-1)
	{
	global $imagemagick_path,$ghostscript_path,$preview_generate_max_file_size;

	# Debug
	debug("create_previews(ref=$ref,thumbonly=$thumbonly,extension=$extension,previewonly=$previewonly,previewbased=$previewbased,alternative=$alternative)");

	# File checksum (experimental) - disabled for now
	if (!$previewonly) {generate_file_checksum($ref,$extension);}

	# first reset preview tweaks to 0
	sql_query("update resource set preview_tweaks = '0|1' where ref = '$ref'");

	# pages/tools/update_previews.php?previewbased=true
	# use previewbased to avoid touching original files (to preserve manually-uploaded preview images
	# when regenerating previews (i.e. for watermarks)
	if($previewbased)
		{
		$file=get_resource_path($ref,true,"lpr",false,"jpg",-1,1,false,"",$alternative);	
		if (!file_exists($file))
			{
			$file=get_resource_path($ref,true,"scr",false,"jpg",-1,1,false,"",$alternative);		
			if (!file_exists($file))
				{
				$file=get_resource_path($ref,true,"pre",false,"jpg",-1,1,false,"",$alternative);		
				}
			}
		}
	else if (!$previewonly)
		{
		$file=get_resource_path($ref,true,"",false,$extension,-1,1,false,"",$alternative);
		}
	else
		{
		# We're generating based on a new preview (scr) image.
		$file=get_resource_path($ref,true,"tmp",false,"jpg");	
		}
	
	# Debug
	debug("File source is $file");
	
	# Make sure the file exists
	if (!file_exists($file)) {return false;}
	
	# If configured, make sure the file is within the size limit for preview generation
	if (isset($preview_generate_max_file_size))
		{
		$filesize=filesize($file)/(1024*1024);# Get filesize in MB
		if ($filesize>$preview_generate_max_file_size) {return false;}
		}
		
	if (($extension=="jpg") || ($extension=="jpeg") || ($extension=="png") || ($extension=="gif"))
	# Create image previews for built-in supported file types only (JPEG, PNG, GIF)
		{
		if (isset($imagemagick_path))
			{
			create_previews_using_im($ref,$thumbonly,$extension,$previewonly,$previewbased,$alternative);
			}
		else
			{
			# ----------------------------------------
			# Use the GD library to perform the resize
			# ----------------------------------------


			# For resource $ref, (re)create the various preview sizes listed in the table preview_sizes
			# Only create previews where the target size IS LESS THAN OR EQUAL TO the source size.
			# Set thumbonly=true to (re)generate thumbnails only.

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
			
				# Find the target path 
				$path=get_resource_path($ref,true,$ps[$n]["id"],false,"jpg",-1,1,false,"",$alternative);
				if (file_exists($path) && !$previewbased) {unlink($path);}
				# Also try the watermarked version.
				$wpath=get_resource_path($ref,true,$ps[$n]["id"],false,"jpg",-1,1,true,"",$alternative);
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
					copy($file,get_resource_path($ref,true,$id,false,$extension,-1,1,false,"",$alternative));
					if ($id=="thm") {
						sql_query("update resource set thumb_width='$sw',thumb_height='$sh' where ref='$ref'");
						}
					}
				}
			# flag database so a thumbnail appears on the site
			if ($alternative==-1) # not for alternatives
				{
				sql_query("update resource set has_image=1,preview_extension='jpg',file_modified=now() where ref='$ref'");
				}
			}
		}
	else
		{
		# If using ImageMagick, call preview_preprocessing.php which makes use of ImageMagick and other tools
		# to attempt to extract a preview.
		global $no_preview_extensions;
		if (isset($imagemagick_path) && !in_array(strtolower($extension),$no_preview_extensions))
			{
      		include(dirname(__FILE__)."/preview_preprocessing.php");
			}
		}
	return true;
	}

function create_previews_using_im($ref,$thumbonly=false,$extension="jpg",$previewonly=false,$previewbased=false,$alternative=-1)
	{
	global $imagemagick_path,$imagemagick_preserve_profiles,$imagemagick_quality;

	debug("create_previews_using_im(ref=$ref,thumbonly=$thumbonly,extension=$extension,previewonly=$previewonly,previewbased=$previewbased,alternative=$alternative)");

	if (isset($imagemagick_path))
		{

		# ----------------------------------------
		# Use ImageMagick to perform the resize
		# ----------------------------------------

		# For resource $ref, (re)create the various preview sizes listed in the table preview_sizes
		# Set thumbonly=true to (re)generate thumbnails only.
		if($previewbased)
			{
			$file=get_resource_path($ref,true,"lpr",false,"jpg",-1,1,false,"",$alternative);	
			if (!file_exists($file))
				{
				$file=get_resource_path($ref,true,"scr",false,"jpg",-1,1,false,"",$alternative);		
				if (!file_exists($file))
					{
					$file=get_resource_path($ref,true,"pre",false,"jpg",-1,1,false,"",$alternative);		
					}
				}
			}
		else if (!$previewonly)
			{
			$file=get_resource_path($ref,true,"",false,$extension,-1,1,false,"",$alternative);	
			}
		else
			{
			# We're generating based on a new preview (scr) image.
			$file=get_resource_path($ref,true,"tmp",false,"jpg");	
			}

		$hpr_path=get_resource_path($ref,true,"hpr",false,"jpg",-1,1,false,"",$alternative);	
		if (file_exists($hpr_path) && !$previewbased) {unlink($hpr_path);}	
		$lpr_path=get_resource_path($ref,true,"lpr",false,"jpg",-1,1,false,"",$alternative);	
		if (file_exists($lpr_path) && !$previewbased) {unlink($lpr_path);}	
		$scr_path=get_resource_path($ref,true,"scr",false,"jpg",-1,1,false,"",$alternative);	
		if (file_exists($scr_path) && !$previewbased) {unlink($scr_path);}
		$scr_wm_path=get_resource_path($ref,true,"scr",false,"jpg",-1,1,true,"",$alternative);	
		if (file_exists($scr_wm_path) && !$previewbased) {unlink($scr_wm_path);}
		
		$prefix = '';
		# Camera RAW images need prefix
		if (preg_match('/^(dng|nef|x3f|cr2|crw|mrw|orf|raf|dcr)$/i', $extension, $rawext)) { $prefix = $rawext[0] .':'; }

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

		$ps=sql_query("select * from preview_size $sizes order by width desc, height desc");
		for ($n=0;$n<count($ps);$n++)
			{
			# If we've already made the LPR or SCR then use those for the remaining previews.
			# As we start with the large and move to the small, this will speed things up.
			if(file_exists($lpr_path)){$file=$lpr_path;}
			if(file_exists($scr_path)){$file=$scr_path;}
			
			# Locate imagemagick.
			$command=$imagemagick_path . "/bin/convert";
			if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
			if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
			if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility.'");}	
			
			if( $prefix == "cr2:" || $prefix == "nef:" ) {
			    $flatten = "";
			} else {
			    $flatten = "-flatten";
			}

			$command .= ' '. escapeshellarg($file) .'[0] ' . $flatten . ' -quality ' . $imagemagick_quality;
			
			# fetch target width and height
			$tw=$ps[$n]["width"];$th=$ps[$n]["height"];
			$id=$ps[$n]["id"];

			# Debug
			debug("Contemplating " . $ps[$n]["id"] . " (sw=$sw, tw=$tw, sh=$sh, th=$th, extension=$extension)");

			# Always make a screen size for non-JPEG extensions regardless of actual image size
			# This is because the original file itself is not suitable for full screen preview, as it is with JPEG files.
			#
			# Always make preview sizes for smaller file sizes.
			#
			# Always make pre/thm/col sizes regardless of source image size.
			if (($id == "hpr" && !($extension=="jpg" || $extension=="jpeg")) || ($id == "scr" && !($extension=="jpg" || $extension=="jpeg")) || ($sw>$tw) || ($sh>$th) || ($id == "pre") || ($id=="thm") || ($id=="col"))
				{
					
				# Find the target path
				$path=get_resource_path($ref,true,$ps[$n]["id"],false,"jpg",-1,1,false,"",$alternative);
				
				# Debug
				debug("Generating preview size " . $ps[$n]["id"] . " to " . $path);

				# Delete any file at the target path.				
				if (file_exists($path)){unlink($path);}

				# Also try the watermarked version.
				$wpath=get_resource_path($ref,true,$ps[$n]["id"],false,"jpg",-1,1,true,"",$alternative);
				if (file_exists($wpath)){unlink($wpath);}
	
				# Preserve colour profiles? (omit for smaller sizes)   
				$profile="+profile \"*\" -colorspace RGB"; # By default, strip the colour profiles ('+' is remove the profile, confusingly)
				if ($imagemagick_preserve_profiles && $id!="thm" && $id!="col" && $id!="pre" && $id!="scr") {$profile="";}

				$runcommand = $command ." +matte $profile -resize " . $tw . "x" . $th . "\">\" ".escapeshellarg($path);
				$output=shell_exec($runcommand);  
				# echo $runcommand."<br>";
				# Add a watermarked image too?
				global $watermark;
				if (isset($watermark) && ($ps[$n]["internal"]==1 || $ps[$n]["allow_preview"]==1))
					{
					$path=get_resource_path($ref,true,$ps[$n]["id"],false,"",-1,1,true);
					if (file_exists($path)) {unlink($path);}
					
					$watermarkreal=dirname(__FILE__) ."/../" . $watermark;
					
					$runcommand = $command ." +matte $profile -resize " . $tw . "x" . $th . "\">\" -tile ".escapeshellarg($watermarkreal)." -draw \"rectangle 0,0 $tw,$th\" ".escapeshellarg($path); 
					
					#die($runcommand);
					$output=shell_exec($runcommand); 
					# echo $runcommand;
					}
				}
			}
		# For the thumbnail image, call extract_mean_colour() to save the colour/size information
		$target=@imagecreatefromjpeg(get_resource_path($ref,true,"thm",false,"jpg",-1,1,false,"",$alternative));
		if ($target && $alternative==-1) # Do not run for alternative uploads 
			{
			extract_mean_colour($target,$ref);
			# flag database so a thumbnail appears on the site
			sql_query("update resource set has_image=1,preview_extension='jpg',file_modified=now() where ref='$ref'");
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

	global $portrait_landscape_field,$lang;
	if (isset($portrait_landscape_field))
		{
		# Write 'Portrait' or 'Landscape' to the appropriate field.
		if ($width>=$height) {$portland=$lang["landscape"];} else {$portland=$lang["portrait"];}
		update_field($ref,$portrait_landscape_field,$portland);
		}

	
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
	if (!file_exists($file)) {
	# Some images may be too small to have a scr.  Try pre:
	$file=get_resource_path($ref,true,"pre",false,$extension);}
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
		
		global $portrait_landscape_field,$lang;
		if (isset($portrait_landscape_field))
			{
			# Write 'Portrait' or 'Landscape' to the appropriate field.
			if ($ts[0]["thumb_height"]>=$ts[0]["thumb_width"]) {$portland=$lang["landscape"];} else {$portland=$lang["portrait"];}
			update_field($ref,$portrait_landscape_field,$portland);
			}
		
		}
	# Update the modified date to force the browser to reload the new thumbs.
	sql_query("update resource set file_modified=now() where ref='$ref'");
	
	# record what was done so that we can reconstruct later if needed
	# current format is rotation|gamma. Additional could be tacked on if more manipulation options are added
	$current_preview_tweak = sql_value("select preview_tweaks value from resource where ref = '$ref'","");
	if (strlen($current_preview_tweak) == 0)
		{
			$oldrotate = 0;
			$oldgamma = 1;
		} else {
			list($oldrotate,$oldgamma) = explode('|',$current_preview_tweak);
		}
		$newrotate = $oldrotate + $rotateangle;
		if ($newrotate > 360){
			$newrotate = $newrotate - 360;
		}elseif ($newrotate < 0){
			$newrotate = 360 + $newrotate;
		}elseif ($newrotate == 360){
			$newrotate = 0;
		}
		if ($gamma > 0){
			$newgamma = $oldgamma +  $gamma -1;
		} else {
			$newgamma = $oldgamma;
		}
		sql_query("update resource set preview_tweaks = '$newrotate|$newgamma' where ref = $ref");
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
	// not used
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
     
function extract_indd_pages ($filename) {
	global $exiftool_path;
	shell_exec($exiftool_path.'/exiftool -b '.$filename.' > '.$filename.'metadata');
    $source = file_get_contents($filename.'metadata');
    $xmpdata = $source;
    $regexp     = "/<xmpGImg:image>.+<\/xmpGImg:image>/";
    preg_match_all ($regexp, $xmpdata, $r);
    $indd_thumbs=array();
    if (isset($r[0]) && count($r[0])>0){
		$n=0;
		foreach ($r[0] as $image){
    	$indd_thumbs[$n] = strip_tags($image);
    	$indd_thumbs[$n] = str_replace("#xA;","",$indd_thumbs[$n]);
		$n++;
		}
		$n=0;
		unlink($filename.'metadata');
    	return ($indd_thumbs);} 
     }     
 
 
function generate_file_checksum($resource,$extension,$anyway=false)
	{
	global $file_checksums;
        global $file_checksums_fullfile;
	global $file_checksums_offline;
	$generated = false;

	if (($file_checksums && !$file_checksums_offline)||$anyway) // do it if file checksums are turned on, or if requestor said do it anyway
		{
		# Generates a unique checksum for the given file, based on the first 50K and the file size.

		$path=get_resource_path($resource,true,"",false,$extension);
		if (file_exists($path))
			{

                        # Generate the ID
                        if ($file_checksums_fullfile){
                            # Fetch the string used to generate the unique ID
                            $use=filesize($path) . "_" . file_get_contents($path,null,null,0,50000);
                            $checksum=md5($use);
                        } else {
                            $checksum=md5_file($path);
                        }

                        # Generate store.
			sql_query("update resource set file_checksum='" . escape_check($checksum) . "' where ref='$resource'");
			$generated = true;
			}
		}

		if ($generated){
			return true;
		} else {
			# if we didn't generate a new file checksum, clear any existing one so that it will not be incorrect
			# The lack of checksum will also be used as the trigger for the offline process
			clear_file_checksum($resource);
			return false;
		}
	}

function clear_file_checksum($resource){
    if (strlen($resource) > 0 && is_numeric($resource)){
    	sql_query("update resource set file_checksum='' where ref='$resource'");
    	return true;
    } else {
	return false;
    }
}

if (!function_exists("upload_preview")){
function upload_preview($ref)
	{
		
	hook ("removeannotations");		
		
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

	# Delete temporary file, if not transcoding.
	if(!sql_value("SELECT is_transcoding value FROM resource WHERE ref = '".escape_check($ref)."'", false))
		{
		unlink($filepath);
		}

    return true;
    }}
 
function extract_text($ref,$extension)
	{
	# Extract text from the resource and save to the configured field.
	global $extracted_text_field,$antiword_path,$pdftotext_path,$zip_contents_field;
	$text="";
	$path=get_resource_path($ref,true,"",false,$extension);
	
	# Microsoft Word extraction using AntiWord.
	if ($extension=="doc" && isset($antiword_path))
		{
		$command=$antiword_path . "/antiword";
		if (!file_exists($command)) {$command=$antiword_path . "\antiword.exe";}
		if (!file_exists($command)) {exit("Antiword executable not found at '$antiword_path'");}
		$text=shell_exec($command . " -m UTF-8 \"" . $path . "\"");
		}
	
       # Microsoft OfficeOpen (docx,xlsx) extraction
       # This is not perfect and needs some work, but does at least extract indexable content.
       if ($extension=="docx"||$extension=="xlsx")
		{	
		$path=escapeshellarg($path);
		
		 # DOCX files are zip files and the content is in word/document.xml.
               # We extract this then remove tags.
               switch($extension){
               case "xlsx":
               $text=shell_exec("unzip -p $path \"xl/sharedStrings.xml\"");
               break;

               case "docx":
               $text=shell_exec("unzip -p $path \"word/document.xml\"");
               break;
               }
               
		# Remove tags, but add newlines as appropriate (without this, separate text blocks are joined together with no spaces).
		$text=str_replace("<","\n<",$text);
		$text=trim(strip_tags($text));
		while (strpos($text,"\n\n")!==false) {$text=str_replace("\n\n","\n",$text);} # condense multiple line breaks
		}

	# OpenOffice Text (ODT)
	if ($extension=="odt"||$extension=="ods"||$extension=="odp")
		{	
		$path=escapeshellarg($path);
		
		# ODT files are zip files and the content is in content.xml.
		# We extract this then remove tags.
		$text=shell_exec("unzip -p $path \"content.xml\"");

		# Remove tags, but add newlines as appropriate (without this, separate text blocks are joined together with no spaces).
		$text=str_replace("<","\n<",$text);
		$text=trim(strip_tags($text));
		while (strpos($text,"\n\n")!==false) {$text=str_replace("\n\n","\n",$text);} # condense multiple line breaks
		}
	
	# PDF extraction using pdftotext (part of the XPDF project)
	if ($extension=="pdf" || $extension=="ai" && isset($pdftotext_path))
		{
		$command=$pdftotext_path . "/pdftotext";
		if (!file_exists($command)) {$command=$pdftotext_path . "\pdftotext.exe";}
		if (!file_exists($command)) {exit("pdftotext executable not found at '$pdftotext_path'");}
		$text=shell_exec($command . " -enc UTF-8 \"" . $path . "\" -");
		}
	
	# HTML extraction
	if ($extension=="html" || $extension=="htm")
		{
		$text=strip_tags(file_get_contents($path));
		}

	# TXT extraction
	if ($extension=="txt")
		{
		$text=file_get_contents($path);
		}

	if ($extension=="zip")
		{
		# Zip files - map the field
		$path=escapeshellarg($path);
		$text=shell_exec("unzip -l $path");
		
		global $zip_contents_field_crop;
		if ($zip_contents_field_crop>0)
			{
			# Remove the first few lines according to $zip_contents_field_crop in config.
			$text=explode("\n",$text);
			for ($n=0;$n<count($zip_contents_field_crop);$n++) {array_shift($text);}
			$text=join("\n",$text);
			}
		
		if (isset($zip_contents_field))
			{
			$extracted_text_field=$zip_contents_field;
			}
		}
	
	
	
		
	# Save the extracted text.
	if ($text!="")
		{
		# Save text
		update_field($ref,$extracted_text_field,$text);
		
		# Update XML metadata dump file.
		update_xml_metadump($ref);
		}
	
	}
	

?>
