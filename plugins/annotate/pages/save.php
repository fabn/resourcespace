<?php

include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";
include_once "../../../include/resource_functions.php";


$ref=getvalescaped("ref","");
if ($ref==""){die("no");}

$top=getvalescaped('top','');
$left=getvalescaped('left','');
$width=getvalescaped('width','');
$height=getvalescaped('height','');
$text=getvalescaped('text','');
$text=str_replace("<br />\n"," ",$text);// remove the breaks added by get.php
	
$id=getvalescaped('id','');
$preview_width=getvalescaped('pw','');
$preview_height=getvalescaped('ph','');

$oldtext=sql_value("select note value from annotate_notes where ref='$ref' and note_id='$id'","");
if ($oldtext!=""){
	remove_keyword_mappings($ref,i18n_get_indexable($oldtext),-1,false,false,"annotation_ref",$id);
}

sql_query("delete from annotate_notes where ref='$ref' and note_id='$id'");


if (substr($text,0,strlen($username))!=$username){$text=$username.": ".$text;}

sql_query("insert into annotate_notes (ref,top_pos,left_pos,width,height,preview_width,preview_height,note,user) values ('$ref','$top','$left','$width','$height','$preview_width','$preview_height','$text','$userref') ");

$annotateid = sql_insert_id();
echo $annotateid;

$notes=sql_query("select * from annotate_notes where ref='$ref'");
sql_query("update resource set annotation_count=".count($notes)." where ref=$ref");

#Add annotation to keywords
$keywordtext = substr(strstr($text,": "),2); # don't add the username to the keywords
debug("adding annotation to resource keywords. Keywords: " . $keywordtext);

add_keyword_mappings($ref,i18n_get_indexable($keywordtext),-1,false,false,"annotation_ref",$annotateid);
#add_keyword_mappings($ref,$text,-1);
