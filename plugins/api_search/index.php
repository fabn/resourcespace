<?php

include(dirname(__FILE__)."/../../include/db.php");
include(dirname(__FILE__)."/../../include/general.php");
include(dirname(__FILE__)."/../../include/search_functions.php");

$api=true;

include(dirname(__FILE__)."/../../include/authenticate.php");

// required: check that this plugin is available to the user
if (!in_array("api_search",$plugins)){die("no access");}

$search=getval("search","");
$restypes=getval("restypes","");
$order_by=getval("order_by","relevance");
$sort=getval("sort","desc");
$archive=getval("archive",0);
$starsearch=getval("starsearch","");

$help=getval("help","");
if ($help!=""){
header('Content-type: text/plain');
echo file_get_contents("readme.txt");
die();
}

$results=do_search($search,$restypes,$order_by,$archive,-1,$sort,false,$starsearch);

if (getval("previewsize","")!=""){
for($n=0;$n<count($results);$n++){
    $results[$n]['preview']=get_resource_path($results[$n]['ref'],false,getval("previewsize",""),false,"jpg",-1,1,false,"",-1);
    }
}

if (!is_array($results)){$results=array();}


if (getval("content","")=="json"){
header('Content-type: application/json');
echo json_encode($results);
}

else if (getval("content","")=="xml"){
    header('Content-type: application/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?><results>';
    foreach ($results as $result){
        echo '<resource>';
        foreach ($result as $resultitem=>$value){
            echo '<'.$resultitem.'>';
            echo $value;
            echo '</'.$resultitem.'>';
        }
        echo '</resource>';
    }
    echo '</results>';
}

else echo json_encode($results); // echo json without headers by default




