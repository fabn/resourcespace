<?php
include_once "../include/config.default.php";
if (file_exists("../include/config.php")){
	include_once("../include/config.php");
}
include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";
include_once "../../../include/resource_functions.php";
include_once "../../../include/collections_functions.php";
include_once "../../../include/image_processing.php";

include_once "../include/transform_functions.php";

include "../../../include/header.php";


// verify that the requested CollectionID is numeric.
$collection = getvalescaped('collection','');
if (!is_numeric($collection)){ echo "Error: non numeric collection ID."; exit; }

$doit = getvalescaped('doit',0);

if ($doit == 0){
	// user has not confirmed operation. So make them do that first

	echo "Please select your batch transform operation. </p><p><strong>WARNING: executing this command will permanently change resources. Use caution!</strong></p>";
?>


	<form name='batchtransform' action='collection_rotate.php'>
	<input type='hidden' name='doit' value='1' />
	<input type='hidden' name='collection' value='<?php echo $collection ?>' />
	
	Rotation:
	<select name='rotation'>
		<option value='90'>90</option>
		<option value='180'>180</option>
		<option value='270'>270</option>
	</select>
	<input type='submit' value='Transform' />
	</form>


<?php	

	include "../../../include/footer.php";

	exit;
}





// get parameters. For now, only rotation is supported
$rotation = getvalescaped('rotation',0);
if (!is_numeric($rotation) || $rotation > 360){
	$rotation = 0; // only allow numeric values
}


# Locate imagemagick.
if (!isset($imagemagick_path)){
	echo "Error: ImageMagick must be configured for crop functionality. Please contact your system administrator.";
	exit;
}
$basecommand=$imagemagick_path . "/bin/convert";
if (!file_exists($basecommand)) {$basecommand=$imagemagick_path . "/convert";}
if (!file_exists($basecommand)) {$basecommand=$imagemagick_path . "\convert.exe";}
if (!file_exists($basecommand)) {exit("Could not find ImageMagick 'convert' utility.'");}	

$successcount = 0;
$failcount = 0;


// retrieve a list of all resources in the collection:
$resources = sql_array("select resource value from collection_resource where collection = '$collection'");
if (count($resources) == 0){
	echo "no resources found";
} else {
	echo "<h2>Batch transforming collection $collection</h2>\n";
	foreach($resources as $resource){
		echo "<hr /><h4>$resource</h4>";
		$edit_access=get_edit_access($resource);
		if (!$edit_access){
			echo " was not transformed: Access Denied.";
			$failcount++;
		} else {

			$orig_ext = sql_value("select file_extension value from resource where ref = '$resource'",'');
			$new_ext = $orig_ext; // eventually we'll allow them to change the format. But for now, always the same.	

			$path = get_resource_path($resource,true,'',false,$new_ext);
			// strategy = we will transform to new path, check file, then replace the original.
			$newpath = $path."_btr.$new_ext";

			$command = $basecommand .  ' ' . $path;

			$command .= " -delete 1--1 -flatten "; // make sure we're only operating on first layer; fixes embedded preview weirdness

			if ($rotation > 0){
				$command .= " -rotate $rotation ";
			}
			$command .= " $newpath";
			//echo "   $command<br>";
			$shell_result = shell_exec($command);
			if (file_exists($newpath) && filesize($newpath) > 0){
				// success!
				if (!rename($newpath,$path)){
					echo "   Error: unable to rename transformed file for resource $resource. <br />\n";
					$failcount++;
				} else {
					// FIXME - check if we need to update dimensions or other metadata
					create_previews($resource,false,$new_ext);
					resource_log($resource,'t','','batch transform');
					echo "<img src='" . get_resource_path($resource,false,"thm",false,'jpg',-1,1) . "' /><br />\n";
					echo "   SUCCESS!<br />\n";
					$successcount++;
				}
				
			} else {
				echo "   Error: Transform of resource $resource failed. <br />\n";
				$failcount++;
			}
		}

	}
}


if ($successcount > 0){
	collection_log($collection,'b',''," ($successcount)");
}

echo "<hr /><h3>Summary</h3>\n";
echo count($resources) . " resources in collection.<br />";
echo $successcount . " resources transformed successfully.<br />";
if ($failcount > 0){
	echo "$failcount errors.<br />";
}

include "../../../include/footer.php";









/*


# Load download access level
//$access=get_resource_access($ref);

// are they requesting to change the original?
//if (isset($_REQUEST['mode']) && strtolower($_REQUEST['mode']) == 'original'){
//    $original = true;
//} else {
//    $original = false;
//}



// if they can't download this resource, they shouldn't be doing this
// also, if they are trying to modify the original but don't have edit access
// they should never get these errors, because the links shouldn't show up if no perms
//if ($access!=0 || ($original && !$edit_access)){
//	include "../../../include/header.php";
//	echo "Permission denied.";
//	include "../../../include/footer.php";
//	exit;
/}





if (isset($_REQUEST['rotation']) && is_numeric($_REQUEST['rotation']) && $_REQUEST['rotation'] > 0 && $_REQUEST['rotation'] < 360){
    $rotation = $_REQUEST['rotation'];
}else{
    $rotation = 0;
}


if ($rotation > 0){
    $command .= " -rotate $rotation ";
}


if ($rotation > 0){
    // assume we should reset exif orientation flag since they have rotated to another orientation
    $command .= " -orient undefined ";
}

$command .= " \"xxxx\"";

//$shell_result = shell_exec($command);
echo $command;

// generate previews if needed
global $alternative_file_previews;
if ($alternative_file_previews && !$download && !$original && getval("slideshow","")=="")
	{
	create_previews($ref,false,$new_ext,false,false,$newfile);
	}

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
	if ($download || getval("slideshow","")!="")
		{
		$filename=$ref . "_" . strtolower($lang['transformed']);
		}
	elseif ($original)
		{
                // fixme
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
	

    $origalttitle = $lang['priorversion'];
    $origaltdesc = $lang['replaced'] . " " . strftime("%Y-%m-%d, %H:%M");
    $origfilename = sql_value("select value from resource_data left join resource_type_field on resource_data.resource_type_field = resource_type_field.ref where resource = '$ref' and name = 'original_filename'",$ref . "_original.$orig_ext");
    $origalt  = add_alternative_file($ref,$origalttitle,$origaltdesc);
    $origaltpath = get_resource_path($ref, true, "", true, $orig_ext, -1, 1, false, "", $origalt);
    $mporig =  round(($origwidth*$origheight)/1000000,2);
    $filesizeorig = filesize($originalpath);
    rename($originalpath,$origaltpath);
    $result = sql_query("update resource_alt_files set file_name='{$origfilename}',file_extension='$orig_ext',file_size = '$filesizeorig' where ref='$origalt'");
    $neworigpath = get_resource_path($ref,true,'',false,$new_ext);
    rename($newpath,$neworigpath);
    $result = sql_query("update resource set file_extension = '$new_ext' where ref = '$ref' limit 1"); // update extension
    resource_log($ref,'t','','original transformed');
    create_previews($ref, false, $orig_ext, false, false, $origalt);
    create_previews($ref,false,$new_ext);

    # delete existing resource_dimensions
    sql_query("delete from resource_dimensions where resource='$ref'");
    sql_query("insert into resource_dimensions (resource, width, height, file_size) values ('$ref', '$newfilewidth', '$newfileheight', '$newfilesize')");

    # call remove annotations, since they will not apply to transformed
    hook("removeannotations");

    // remove the cached transform preview, since it will no longer be accurate
    if (file_exists(get_temp_dir() . "/transform_plugin/pre_$ref.jpg")){
	unlink(get_temp_dir() . "/transform_plugin/pre_$ref.jpg");
    }

//    header("Location:../../../pages/view.php?ref=$ref\n\n");
    exit;

*/

?>
