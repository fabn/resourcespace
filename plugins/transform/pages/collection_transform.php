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

	echo "<h1>" . $lang['batchtransform'] . "</h1>";
	echo "<p><strong>WARNING: executing this command will permanently change resources. Use caution!</strong></p>";
?>


	<form name='batchtransform' action='collection_rotate.php'>
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

?>
