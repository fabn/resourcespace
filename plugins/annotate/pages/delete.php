<?php


include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";

$ref=getval("ref","");
if ($ref==""){die("no");}
$id=getval('id','');
$notes=sql_query("delete from annotate_notes where ref='$ref' and note_id='$id'");
$notes=sql_query("select * from annotate_notes where ref='$ref'");
sql_query("update resource set annotation_count=".count($notes)." where ref=$ref");
