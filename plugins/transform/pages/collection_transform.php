<?php
/*
include_once "../include/config.default.php";
if (file_exists("../include/config.php")){
	include_once("../include/config.php");
}
*/
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

	echo "<h1>" . $lang['batchtransform'] . "</h1>";
	echo "<p>". $lang['batchtransform-introtext'] . "</p>";
?>


	<form name='batchtransform' action='collection_transform.php'>
	<input type='hidden' name='doit' value='1' />
	<input type='hidden' name='collection' value='<?php echo $collection ?>' />
	
	<?php echo $lang['rotation']; ?>:<br />
	<select name='rotation'>
		<option value='90'><?php echo $lang['rotation90']; ?></option>
		<option value='180'><?php echo $lang['rotation180']; ?></option>
		<option value='270'><?php echo $lang['rotation270']; ?></option>
	</select>
	<br /><br />
	<input type="submit" value="<?php echo htmlspecialchars($lang['transform']) ?>" />
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
	echo $lang['error-crop-imagemagick-not-configured'];
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
	echo $lang['no_resources_found'];
} else {
	echo "<h2>" . str_replace("%col", $collection, $lang['batch_transforming_collection']) . "</h2>\n";
	flush();
	foreach($resources as $resource){
		echo "<hr /><h4>$resource</h4>";
		flush();
		$edit_access=get_edit_access($resource);
		if (!$edit_access){
			echo " " . $lang['not-transformed'];
			$failcount++;
		} else {

			$orig_ext = sql_value("select file_extension value from resource where ref = '$resource'",'');
			$new_ext = $orig_ext; // eventually we'll allow them to change the format. But for now, always the same.	

			$path = get_resource_path($resource,true,'',false,$new_ext);
			// strategy = we will transform to new path, check file, then replace the original.
			$newpath = $path."_btr.$new_ext";

			$command = $basecommand .  "\"$path\"";

			$command .= " -delete 1--1 -flatten "; // make sure we're only operating on first layer; fixes embedded preview weirdness

			if ($rotation > 0){
				$command .= " -rotate $rotation ";
			}
			$command .= " \"$newpath\"";
			//echo "   $command<br>";
			$shell_result = run_command($command);
			if (file_exists($newpath) && filesize($newpath) > 0){
				// success!
				if (!rename($newpath,$path)){
					echo " " . str_replace("%res", $resource, $lang['error-unable-to-rename']) . "<br />\n";
					$failcount++;
				} else {
					create_previews($resource,false,$new_ext);


					// get final pixel dimensions of resulting file
					$newfilesize = filesize($path);
					$newfiledimensions = getimagesize($path);
					$newfilewidth = $newfiledimensions[0];
					$newfileheight = $newfiledimensions[1];
					
					# delete existing resource_dimensions
    					sql_query("delete from resource_dimensions where resource='$resource'");
    					sql_query("insert into resource_dimensions (resource, width, height, file_size) values ('$resource', '$newfilewidth', '$newfileheight', '$newfilesize')");

					resource_log($resource,'t','','batch transform');
					echo "<img src='" . get_resource_path($resource,false,"thm",false,'jpg',-1,1) . "' /><br />\n";
					echo " " . $lang['success'] . "<br />\n";
					$successcount++;
				}
				
			} else {
				echo " " . str_replace("%res", $resource, $lang['error-transform-failed']) . "<br />\n";
				$failcount++;
			}
		}
	flush();

	}
}


if ($successcount > 0){
	collection_log($collection,'b',''," ($successcount)");
}

echo "<hr /><h3>" . $lang['summary'] . "</h3>\n";
$qty_total = count($resources);
switch ($qty_total)
    {
    case 1:
        echo $lang['resources_in_collection-1'];
        break;
    default:
        echo str_replace("%qty", $qty_total, $lang['resources_in_collection-2']);
        break;
    }
echo "<br />";
switch ($successcount)
    {
    case 0:
        echo $lang['resources_transformed_successfully-0'];
        break;
    case 1:
        echo $lang['resources_transformed_successfully-1'];
        break;
    default:
        echo str_replace("%qty", $successcount, $lang['resources_transformed_successfully-2']);
        break;
    }
echo "<br />";
switch ($failcount)
    {
    case 0:
        break;
    case 1:
        echo $lang['errors-1'];
        break;
    default:
        echo str_replace("%qty", $failcount, $lang['errors-2']);
        break;
    }

include "../../../include/footer.php";

?>
