<?php


include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";

$ref=getval("ref","");
if ($ref==""){die("no");}

$top=getval('top','');
$left=getval('left','');
$width=getval('width','');
$height=getval('height','');
$text=getvalescaped('text','');
$id=getval('id','');
$preview_width=getval('pw','');
$preview_height=getval('ph','');


sql_query("delete from annotate_notes where ref='$ref' and note_id='$id'");

if (substr($text,0,strlen($username))!=$username){$text=$username.": ".$text;}

sql_query("insert into annotate_notes (ref,top_pos,left_pos,width,height,preview_width,preview_height,note,user) values ('$ref','$top','$left','$width','$height','$preview_width','$preview_height','$text','$userref') ");

echo mysql_insert_id();

$notes=sql_query("select * from annotate_notes where ref='$ref'");
sql_query("update resource set annotation_count=".count($notes)." where ref=$ref");
