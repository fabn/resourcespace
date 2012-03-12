<?php


include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";
include_once "../../../include/resource_functions.php";

$ref=getvalescaped("ref","");
if ($ref==""){die("no");}
$id=getvalescaped('id','');
$text=getvalescaped('text','');
$notes=sql_query("delete from annotate_notes where ref='$ref' and note_id='$id'");
$notes=sql_query("select * from annotate_notes where ref='$ref'");
sql_query("update resource set annotation_count=".count($notes)." where ref=$ref");

#remove annotation from keywords
debug("Annotation: deleting keyword: " . $text . " from resource id: " . $ref);
remove_keyword_mappings($ref,$text,-1,false,false,"annotation_ref",$id);
