<?php


include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";

$ref=getval("ref","");
if ($ref==""){die("no");}
$preview_width=getval("pw",0);
$preview_height=getval("ph",0);

$notes=sql_query("select * from annotate_notes where ref='$ref'");
sql_query("update resource set annotation_count=".count($notes)." where ref=$ref");
// check if display size is different from original preview size, and if so, modify coordinates


$json="[";
$notes_array=array();
for ($x=0;$x<count($notes);$x++){
	

		
			$ratio=$preview_width/$notes[$x]['preview_width'];
			
		$notes[$x]['width']=$ratio*$notes[$x]['width'];
		$notes[$x]['height']=$ratio*$notes[$x]['height'];
		$notes[$x]['top_pos']=$ratio*$notes[$x]['top_pos'];
		$notes[$x]['left_pos']=$ratio*$notes[$x]['left_pos'];
		$notes[$x]['note'] = str_replace(array(chr(13), chr(10)), '<br />', $notes[$x]['note']);

	
	if ($x>0){$json.=",";}
	$json.="{";
	$json.='"top":'.$notes[$x]['top_pos'].', ';
	$json.='"left":'.$notes[$x]['left_pos'].', ';
	$json.='"width":'.$notes[$x]['width'].', ';
	$json.='"height":'.$notes[$x]['height'].', ';
	$json.='"text":"'.str_replace('"','\"',$notes[$x]['note']).'", ';
	$json.='"id":"'.$notes[$x]['note_id'].'", ';
	if ($notes[$x]['user']==$userref){
	$json.='"editable":true';
	} else {
	$json.='"editable":false';	
}
	$json.="}";
}
$json.="]";
echo $json;

