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
$archive=getval("archive",0);
$fetchrows=getval("fetchrows",-1);
$sort=getval("sort","desc");
$starsearch=getval("starsearch","");

$results=do_search($search,$restypes,$order_by,$archive,$fetchrows,$sort,false,$starsearch);

if (getval("previewsize","")!=""){
for($n=0;$n<count($results);$n++){
    $results[$n]['preview']=get_resource_path($results[$n]['ref'],false,getval("previewsize",""),false,"jpg",-1,1,false,"",-1);
    }
}

$json=json_encode($results);
if (getval("content","")=="json"){
header('Content-type: application/json');
}

echo $json;
