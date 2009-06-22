<?php

# This script is useful if you've added an exiftool field mapping and would like to update a single RS field with the original file information 
# for all your resources.

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*40);

# ex. 
# $fieldref=29;

$fieldref="";


if ($fieldref==""){die ("Please add a fieldref parameter, which is the ref number of the field that you would like exiftool to extract from.");}

$fieldref_info= sql_query("select exiftool_field,title,resource_type from resource_type_field where ref='$fieldref'");

$title=$fieldref_info[0]["title"];
$exiftool_tag=$fieldref_info[0]["exiftool_field"];
$field_resource_type=$fieldref_info[0]["resource_type"];

if ($exiftool_tag==""){ die ("Please add an exiftool mapping to your $title Field");}


echo "Updating RS Field $fieldref - $title, with exiftool extraction of: $exiftool_tag<br><br>";

if($field_resource_type==0){
$rd=sql_query("select ref,file_extension from resource where has_image=1");
} else {
$rd=sql_query("select ref,file_extension from resource where has_image=1 and resource_type=$field_resource_type");
}	

for ($n=0;$n<count($rd);$n++)
	{
	$ref=$rd[$n]['ref'];
	$extension=$rd[$n]['file_extension'];
	
	$image=get_resource_path($ref,true,"",false,$extension);
	if (!file_exists($image)) {return false;}
		
	$resource=get_resource_data($ref);
			
	$command=$exiftool_path."/exiftool -s -s -s -".$exiftool_tag." ". escapeshellarg($image);
	
	$value = iptc_return_utf8(trim(shell_exec($command)));	
	#if ($value!=""){
		update_field($ref,$fieldref,$value);
		echo ("<br>Updated Resource $ref <br> -Exiftool found \"$value\" embedded in the -$exiftool_tag tag and applied it to ResourceSpace Field $fieldref<br><br>");
	#	}
	}
echo "...done.";


