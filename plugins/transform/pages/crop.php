<?php

include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";
include_once "../../../include/resource_functions.php";
include_once "../../../include/image_processing.php";
include_once "../include/config.default.php";
if (file_exists("../include/config.php")){
	include_once("../include/config.php");
}

include_once "../include/transform_functions.php";


// verify that the requested ResourceID is numeric.
$ref = $_REQUEST['ref'];
if (!is_numeric($ref)){ echo "Error: non numeric ref."; exit; }


# Load download access level
$access=get_resource_access($ref);

// if they can't download this resource, they shouldn't be doing this
if ($access!=0){
	include "../../../include/header.php";
	echo "Permission denied.";
	include "../../../include/footer.php";
	exit;
}

# Load edit access level
$edit_access=get_edit_access($ref);

// generate a preview image for the operation if it doesn't already exist
if (!file_exists("../../../filestore/tmp/transform_plugin/pre_$ref.jpg")){
	generate_transform_preview($ref) or die("Error generating transform preview.");
}


# Locate imagemagick.
if (!isset($imagemagick_path)){
	echo "Error: ImageMagick must be configured for crop functionality. Please contact your system administrator.";
	exit;
}
$command=$imagemagick_path . "/bin/convert";
if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility.'");}	


// retrieve file extensions
$orig_ext = sql_value("select file_extension value from resource where ref = '$ref'",'');
$preview_ext = sql_value("select preview_extension value from resource where ref = '$ref'",'');

// retrieve image paths for preview image and original file
//$previewpath = get_resource_path($ref,true,$cropper_cropsize,false,$preview_ext);
$previewpath = "$storagedir/tmp/transform_plugin/pre_$ref.jpg";
$originalpath= get_resource_path($ref,true,'',false,$orig_ext);


// retrieve image sizes for original image and preview used for cropping
$cropsizes = getimagesize($previewpath);
$origsizes = getimagesize($originalpath);
$cropwidth = $cropsizes[0];
$cropheight = $cropsizes[1];
$origwidth = $origsizes[0];
$origheight = $origsizes[1];


// if we've been told to do something
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'docrop'){

$width = $_REQUEST['width'];
$height = $_REQUEST['height'];
$xcoord = $_REQUEST['xcoord'];
$ycoord = $_REQUEST['ycoord'];
$description = $_REQUEST['description'];
$cropsize = $_REQUEST['cropsize'];
$new_width = $_REQUEST['new_width'];
$new_height = $_REQUEST['new_height'];

if (isset($_REQUEST['filename']) && $cropper_custom_filename){
	$filename = $_REQUEST['filename'];
} else {
	$filename = '';
}

// verify that all crop parameters are numeric - just a precaution
if (!is_numeric($width) || !is_numeric($height) || !is_numeric($xcoord) || !is_numeric($ycoord)){
	// should never happen, but if it does, could be bad news
	echo $lang['nonnumericcrop'];
	exit;
}

if ($cropper_debug){
	error_log("origwidth: $origwidth, width: $width / origheight = $origheight, height = $height, $xcoord / $ycoord");
}

if ($width == 0 && $height == 0 && ($new_width > 0||$new_height > 0)) {
	// the user did not indicate a crop. presumably they are scaling
	$verb = $lang['scaled'];
	$crop_necessary = false;
} else {
	$crop_necessary = true;
	$verb = $lang['cropped'];
	// now we need to mathematically convert to the original size
	$finalxcoord = round ((($origwidth  * $xcoord)/$cropwidth),0);
	$finalycoord = round ((($origheight * $ycoord)/$cropheight),0);
	$finalwidth  = round ((($origwidth  * $width)/$cropwidth),0);
	$finalheight = round ((($origheight * $height)/$cropheight),0);
}

// determine output format
// prefer what the user requested. If nothing, look for configured default. If nothing, use same as original
if (isset($_REQUEST['new_ext']) && strlen($_REQUEST['new_ext']) == 3){
	// is this an allowed extension?
	$new_ext = strtolower($_REQUEST['new_ext']);
	if (!in_array(strtoupper($new_ext),$cropper_formatarray)){
		$new_ext = strtolower($orig_ext);
	}
} elseif (isset($cropper_default_target_format)) {
	$new_ext = strtolower($cropper_default_target_format);
} else {
	$new_ext = strtolower($orig_ext);
}

if ( $cropper_custom_filename && strlen($filename) > 0){
	$mytitle = $filename;
} else{
	$mytitle = mysql_real_escape_string("$verb " . strtoupper($new_ext) . ' ' . $lang['file']);
}

$mydesc = mysql_real_escape_string($description);

# Is this a download only?
$download=(getval("download","")!="");

if (!$download)
	{
	$newfile=add_alternative_file($ref,$mytitle,$mydesc);
	$newpath = get_resource_path($ref, true, "", true, $new_ext, -1, 1, false, "", $newfile);
	}
else
	{
	$tmpdir = "$storagedir/tmp";
	$newpath = "$tmpdir/transform_plugin/download_$ref." . $new_ext;
	}
	
$command .= " '$originalpath' ";

if ($crop_necessary){
	$command .= " -crop " . $finalwidth . "x" . $finalheight . "+" . $finalxcoord . "+$finalycoord +repage ";
}


// did the user request a width? If so, tack that on
if (is_numeric($new_width)||is_numeric($new_height)){
	
	
	$scalewidth = is_numeric($new_width)?true:false;
	$scaleheight = is_numeric($new_height)?true:false;
	
	if (!$cropper_allow_scale_up){
		// sanity checks
		// don't allow a specified size larger than the natural crop size
		// or the original size of the image
		if ($crop_necessary) {
			$checkwidth = $finalwidth;
			$checkheight = $finalheight;
		} else {
			$checkwidth = $orig_width;
			$checkheight = $orig_height;
		}
		
		if (is_numeric($new_width) && $new_width > $checkwidth){
			// if the requested width is greater than the original or natural size, ignore
			$new_width = '';
			$scalewidth = false;
		}
	
		if (is_numeric($new_height) && $new_height > $checkheight){
			// if the requested height is greater than original or natural size, ignore
			$new_height = '';
			$scaleheight = false;
		}
	
	}

	if ($scalewidth || $scaleheight){
		// add scaling command
		// note that there is a minor issue here: may be rounding
		// errors when the crop box is scaled up from preview size to original	 size
		// if so and the resulting match doesn't quite match the required width and 
		// height, there may be a tiny amount of distortion introduced as the
		// program scales up or down by a few pixels. This should be
		// imperceptible, but perhaps worth revisiting at some point.
		
		$command .= " -scale $new_width";
		
		if ($new_height > 0){
			$command .= "x$new_height";
		}
		
		$command .= " ";
	}
	
}

$command .= " '$newpath'";

if ($cropper_debug && !$download){
	error_log($command);
	if (isset($_REQUEST['showcommand'])){
		echo "$command";
		delete_alternative_file($ref,$newfile);
		exit;
	}
}

// fixme -- do we need to trap for errors from imagemagick?
$shell_result = shell_exec($command);
if ($cropper_debug){
	error_log("SHELL RESULT: $shell_result");
}

// generate previews if needed
global $alternative_file_previews;
if ($alternative_file_previews && !$download)
	{
	create_previews($ref,false,$new_ext,false,false,$newfile);
	}

// get final pixel dimensions of resulting file
$newfilesize = filesize($newpath);
$newfiledimensions = getimagesize($newpath);
$newfilewidth = $newfiledimensions[0];
$newfileheight = $newfiledimensions[1];

// strip of any extensions from the filename, since we'll provide that
if(preg_match("/(.*)\.\w\w\w\\$/",$filename,$matches)){
	$filename = $matches[1];
}

// avoid bad characters in filenames
$filename = preg_replace("/[^A-Za-z0-9_\- ]/",'',$filename);
//$filename = str_replace(' ','_',trim($filename));

// if there is not a filename, create one
if ( $cropper_custom_filename && strlen($filename) > 0){
	$filename = "$filename";
} else {
	if ($download)
		{
		$filename=$ref . "_" . strtolower($lang['transform']);
		}
	else
		{
		$filename = "alt_$newfile";
		}
}

$filename = mysql_real_escape_string($filename);

$lcext = strtolower($new_ext);

$mpcalc = round(($newfilewidth*$newfileheight)/1000000,1);

// don't show  a megapixel count if it rounded down to 0
if ($mpcalc > 0){
	$mptext = " ($mpcalc MP)";
} else {
	$mptext = '';
}

if (strlen($mydesc) > 0){ $deschyphen = ' - '; } else { $deschyphen = ''; }
	
// update final information on alt file
if (!$download)
	{
	$result = sql_query("update resource_alt_files set file_name='{$filename}.".$lcext."',file_extension='$lcext',file_size = '$newfilesize',  description = concat(description,'" . $deschyphen . $newfilewidth . " x " . 		$newfileheight . " pixels $mptext') where ref='$newfile'");

	resource_log($ref,'a','',"$new_ext " . strtolower($verb) . " to $newfilewidth x $newfileheight");
	}

if ($download)
	{
	# Output file, delete file and exit
	header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
	header("Content-Type: application/octet-stream");

	set_time_limit(0);
	readfile($newpath);
	unlink($newpath);
	exit();
	}


// send user back to view page
header("Location:../../../pages/view.php?ref=$ref\n\n");
exit;

} else {

// get resource info
$resource = get_resource_data($ref);

// retrieve path to image and figure out size we're using
if ($resource["has_image"]==1)
        {
		$imageurl = "../../../filestore/tmp/transform_plugin/pre_$ref.jpg";
		$imagepath = $imageurl;

        	//$imagepath=get_resource_path($ref,true,$cropper_cropsize,false,$resource["preview_extension"],-1,1);
        	
        	if (!file_exists($imagepath)){
				echo $lang['noimagefound'];
				exit;
			}
        	//$imageurl=get_resource_path($ref,false,$cropper_cropsize,false,$resource["preview_extension"],-1,1);
        	
			$origpresizes = getimagesize($imagepath);
			$origprewidth = $origpresizes[0];
			$origpreheight = $origpresizes[1];
        
		} else {
		
			echo $lang['noimagefound'];
			exit;
		}


	
include "../../../include/header.php";


?>
<script type="text/javascript" src="../../../lib/js/prototype.js" language="javascript"></script>
<script type="text/javascript" src="../../../lib/js/scriptaculous.js?load=builder,dragdrop" language="javascript"></script>
<script type="text/javascript" src="../lib/jsCropperUI/cropper.js" language="javascript"></script>

<h1><?php echo $lang['transformimage'] ?></h1>
<p><?php echo $lang['transformblurb']; ?></p>
<?php



	if (file_exists($imagepath))
                {
                ?>
<div id='cropimgdiv' style='float:left' onmouseover='unfocus_widths();' ><img src="<?php echo $imageurl?>" id='cropimage' /></div>
<?php
                }
        ?>
<script type="text/javascript" language="javascript">
	Event.observe( window, 'load', function() {
		CropManager.attachCropper()
		
		
	} );

	function onEndCrop( coords, dimensions ) {
	document.dimensionsform.xcoord.value=coords.x1;
	document.dimensionsform.ycoord.value=coords.y1;
	document.dimensionsform.width.value=dimensions.width;
	document.dimensionsform.height.value=dimensions.height;
	}
	

		/**
		 * A little manager that allows us to reset the options dynamically
		 */
		var CropManager = {
			/**
			 * Holds the current Cropper.Img object
			 * @var obj
			 */
			curCrop: null,
			
			/**
			 * Gets a min/max parameter from the form 
			 * 
			 * @access private
			 * @param string Form element ID
			 * @return int
			 */
			getParam: function( name ) {
				var val = $F( name );
				return parseInt( val );
			},
									
			/** 
			 * Attaches/resets the image cropper
			 *
			 * @access private
			 * @param obj Event object
			 * @return void
			 */
			attachCropper: function( e ) {
				document.dimensionsform.lastWidthSetting.value = document.getElementById('new_width').value;
				document.dimensionsform.lastHeightSetting.value = document.getElementById('new_height').value;
		
				if( this.curCrop == null ) {
					this.curCrop = new Cropper.Img( 
						'cropimage', 
						{ 
							ratioDim: {x: this.getParam( 'new_width'), y: this.getParam( 'new_height') },
							onEndCrop: onEndCrop 
						} 
					);
				} else {
					this.removeCropper();
					this.curCrop = new Cropper.Img( 
						'cropimage', 
						{ 
							ratioDim: {x: this.getParam( 'new_width'), y: this.getParam( 'new_height') },
							onEndCrop: onEndCrop 
						} 
					);
				}
				if( e != null ) Event.stop( e );
			},
			
			/**
			 * Removes the cropper
			 *
			 * @access public
			 * @return void
			 */
			removeCropper: function() {
				if( this.curCrop != null ) {
					this.curCrop.remove();
					this.curCrop = null;
				}
			},
			
			/**
			 * Resets the cropper, either re-setting or re-applying
			 *
			 * @access public
			 * @return void
			 */
			resetCropper: function() {
				this.attachCropper();
			}
		};	
	
		
		
		
		function unfocus_widths(){
			document.getElementById('new_width').blur();
			document.getElementById('new_height').blur();
		}
		
		function evaluate_values(){
			// do we need to redraw the cropper?
			if (
				(document.getElementById('new_width').value == document.getElementById('lastWidthSetting').value && document.getElementById('new_height').value == document.getElementById('lastHeightSetting').value) 
				|| (document.getElementById('lastWidthSetting').value == '' && document.getElementById('new_width').value == '') 
				|| (document.getElementById('lastHeightSetting').value == '' && document.getElementById('new_height').value == '') 
				)
			{
				return true;
			} else {
			
				CropManager.attachCropper();
				return true;
			}
			
		
		}
		
		function validate_transform(theform){
			// make sure that this is a reasonable transformation before we submit the form.
			// fixme - could add more sophisticated validation here
			
			if (theform.xcoord.value == 0 && theform.ycoord.value == 0 && theform.new_width.value == '' && theform.new_height.value == ''){
				alert('<?php echo addslashes($lang['errormustchoosecropscale']); ?>');
				return false;
			}
			
			<?php if (!$cropper_allow_scale_up) { ?>
				if (Number(theform.new_width.value) > Number(theform.origwidth.value) || Number(theform.new_height.value) > Number(theform.origheight.value)){
					alert('<?php echo addslashes($lang['errorspecifiedbiggerthanoriginal']); ?>');
					return false;
				}
			<?php } ?>
			return true;
		
		}
		
	
</script>
<div id="cropbox"  style='float:left; margin-left:20px'>
  <form name='dimensionsform' id="dimensionsForm" onsubmit='return validate_transform(this);'>
    <input type='hidden' name='action' value='docrop' />
    <input type='hidden' name='xcoord' id='xcoord' value='0' />
    <input type='hidden' name='ycoord' id='ycoord' value='0' />
    <input type='hidden' name='width' id='width' value='<?php echo $origprewidth ?>' />
    <input type='hidden' name='height' id='height'  value='<?php echo $origpreheight ?>' />
    <input type='hidden' name='ref' id='ref' value='<?php echo $ref; ?>' />
    <input type='hidden' name='cropsize' id='cropsize' value='<?php echo $cropper_cropsize; ?>' />
    <input type='hidden' name='lastWidthSetting' id='lastWidthSetting' value='' />
    <input type='hidden' name='lastHeightSetting' id='lastHeightSetting' value='' />
    <input type='hidden' name='origwidth' id='origwidth'  value='<?php echo $origwidth ?>' />
    <input type='hidden' name='origheight' id='origheight'  value='<?php echo $origheight ?>' />
    <table>
      <tr>
        <td style='text-align:right'><?php echo $lang["width"]; ?>: </td>
        <td><input type='text' name='new_width' id='new_width' value='' size='4'  onblur='evaluate_values()' />
          px </td>
        <td style='text-align:right'><?php echo $lang["height"]; ?></td>
        <td><input type='text' name='new_height'  id='new_height' value='' size='4'  onblur='evaluate_values()' />
          px </td>
      </tr>
      <?php if ($cropper_custom_filename){ ?>
      <tr>
        <td style='text-align:right'><?php echo $lang["newfilename"]; ?>: </td>
        <td colspan='3'><input type='text' name='filename' value='' size='30'/></td>
      </tr>
      <?php } ?>
      <tr>
        <td><?php echo $lang["description"]; ?>: </td>
        <td colspan='3' style='text-align:right'><input type='text' name='description' value='' size='30'/></td>
      </tr>
      <tr>
        <td style='text-align:right'><?php echo $lang['type']; ?>: </td>
        <td colspan='3'><?php
			
			if ($cropper_force_original_format){
				echo "<input type='hidden' name='new_ext' value='";
				echo strtoupper($orig_ext) . "' />" . strtoupper($orig_ext);
			} else {
			?>
          <select name='new_ext'>
            <?php 
				foreach ($cropper_formatarray as $theformat){
					echo "<option value='$theformat'";
					
					if (strtolower($theformat) == strtolower($orig_ext)) {
						echo " selected";
					}
					echo ">$theformat</option>\n";
				}
				?>
          </select>
          <?php } // end of if force_original_format ?></td>
      </tr>
    </table>
    <?php
if ($cropper_debug){
	echo "<input type='checkbox'  name='showcommand' value='1'>Debug IM Command</checkbox>";
}
?>
    <p style='text-align:right;margin-top:15px;'>
      <input type='button' value="<?php echo $lang['cancel']; ?>" onclick="javascript:window.location='../../../pages/view.php?ref=<?php echo $ref ?>';" />
      <input type='submit' name='download' value="<?php echo $lang['download']; ?>" />
      <?php if ($edit_access) { ?><input type='submit' name='submit' value="<?php echo $lang['savealternative']; ?>" /><?php } ?>
    </p>
  </form>
  <p>
    <?php

		# MP calculation
		$mp=round(($origwidth*$origheight)/1000000,1);
		if ($mp > 0){
			$orig_mptext = "($mp MP)";
		} else {
			$orig_mptext = '';
		}
		
		echo $lang['original'] . ' ' . $lang['size'];
		echo ": $origwidth x $origheight ";
		echo $lang['pixels'];
		echo " $orig_mptext";

		?>
  </p>
</div>


<?php


include "../../../include/footer.php";

} // end of if action docrop

?>
