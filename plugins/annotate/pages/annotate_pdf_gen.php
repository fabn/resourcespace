<?php

// this program creates a new PDF document with annotations

include('../../../include/db.php');
include('../../../include/general.php');
include('../../../include/authenticate.php');
include('../include/general.php');

$ref=getvalescaped("ref","");
$size=getvalescaped("size","letter");
$color=getvalescaped("color","yellow");

$cleartmp=getvalescaped("cleartmp",""); 
if ($cleartmp!=""){echo getvalescaped("uniqid","");clear_annotate_temp($ref,getvalescaped("uniqid",""));exit("cleared");}

if(getvalescaped("preview","")!=""){$preview=true;} else {$preview=false;}

$previewpage=getvalescaped("previewpage",1);

if (substr($ref,0,1)=="C"){
	$is_collection=true;
	$ref=substr($ref,1); 
	$result=create_annotated_pdf($ref,true,$size,true,$preview);
} 
else { 
	$is_collection=false;
	$result=create_annotated_pdf($ref,false,$size,true,$preview);
}







