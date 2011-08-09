<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";

// take an existing resource and copy it to an alternate file on another resource

// ref is the ref we want to add the alternate
$ref=getvalescaped("ref","",true);

// alternate is the resource we're copying from
$alt=getvalescaped("alt","",true);

if ($ref > 0 && $alt > 0) { 

	// make sure these are actually valid resources
	$rscount = sql_value("select count(*) value from resource where ref in ($ref,$alt)",0);
	if ($rscount == 2){

		# Fetch resource data.
		$refdata=get_resource_data($ref);
		$altdata=get_resource_data($alt);

		#  only do this if editing is allowed for both resources
		if ((!checkperm("e" . $refdata["archive"])) && ($ref>0)) {exit ("Permission denied.");}
		if ((!checkperm("e" . $altdata["archive"])) && ($alt>0)) {exit ("Permission denied.");}

		$delete = getval('delete',0);
		if ($delete != '1'){
			$delete = 0;
		}

		// we're in business - do a copy
		if (alt_from_resource($alt,$ref,'',$delete)){
			header("Location:view.php?ref=$ref\n\n");
			exit;
		} else {
			echo "Something bad happened.";
		}
	} else {
		echo "Error: source or target resource not found.";
	}


} else {
	// show a form to set up a copy
	include "../include/header.php";
	echo "<form>Source resource: <input type='text' name='alt' />";
	echo "<br />Target resource: <input type='text' name='ref' />";
	// fixme - don't display delete option if they don't have permission to do so
	echo "<br /><input type='checkbox' name='delete' value='1'>Delete original after copy</input> (Warning: this will delete the resource, all its metadata, and any alternative files. Use with caution!)"; 
	echo "<br /><input type='submit' /></form>";
	include "../include/footer.php";


}

function alt_from_resource($source,$target,$name='',$delete=false){
	// Copy a resource as an alt file of another resource
	// alt is the source resource, $ref is the target resource that will get the new alternate
	global $view_title_field;
	$srcdata=get_resource_data($source);
	$srcext = $srcdata['file_extension'];
	$srcpath = get_resource_path($source,true,"",false,$srcext);
	if ($name == ''){
		$name = sql_value("select value from resource_data where resource_type_field = '$view_title_field' and resource = '$source'",'Untitled');
	}

	$description = '';
	if (!file_exists($srcpath)){
		echo "ERROR: File not found.";
		return false;
	} else {

		$file_size = filesize($srcpath);
		$altid = add_alternative_file($target,$name,$description="",$file_name="",$file_extension="",$file_size,$alt_type='');
		$newpath = get_resource_path($target,true,"",true,$srcext,-1,1,false,'',$altid);
		copy($srcpath,$newpath);
		# Preview creation for alternative files (enabled via config)
                global $alternative_file_previews;
                if ($alternative_file_previews){
			create_previews($target,false,$srcext,false,false,$altid);
               	}
		if ($delete){
			// we are supposed to delete the original resource when we're done
			# Not allowed to edit this resource? They shouldn't have been able to get here.
			if ((!get_edit_access($source,$srcdata["archive"]))||checkperm('D')) {
				exit ("Permission denied.");
			} else {
				delete_resource($source);
			}
		}
		return true;

	}
}

?>
