<?php

include(dirname(__FILE__)."/../../include/db.php");
include(dirname(__FILE__)."/../../include/general.php");
include(dirname(__FILE__)."/../../include/search_functions.php");
include(dirname(__FILE__)."/../../include/resource_functions.php");
$api=true;

include(dirname(__FILE__)."/../../include/authenticate.php");

// required: check that this plugin is available to the user
if (!in_array("api_alt_file",$plugins)){die("no access");}

$resource=getval("resource","");
$alt_file=getval("alt_file","");

if ($api_alt_file['signed']){

// test signature? get query string minus leading ? and skey parameter
$test_query="";
parse_str($_SERVER["QUERY_STRING"],$parsed);
foreach ($parsed as $parsed_parameter=>$value){
    if ($parsed_parameter!="skey"){
        $test_query.=$parsed_parameter.'='.$value."&";
    }
    }
$test_query=rtrim($test_query,"&");

    // get hashkey that should have been used to create a signature.
    $hashkey=md5($api_scramble_key.getval("key",""));

    // generate the signature required to match against given skey to continue
    $keytotest = md5($hashkey.$test_query);

    if ($keytotest <> getval('skey','')){
		header("HTTP/1.0 403 Forbidden.");
		echo "HTTP/1.0 403 Forbidden. Invalid Signature";
		exit;
	}
}


// if only a resource is specified, return all alt files
if ($alt_file=="" && $resource!=""){$results=get_alternative_files($resource);}

// if a specific alt file and resource are specified, return a single result
if ($alt_file!="" && $resource!=""){$results[0]=get_alternative_file($resource,$alt_file);}


for ($n=0;$n<count($results);$n++){
    $thepath = get_resource_path($resource,true,'',false,$results[$n]['file_extension'],-1,1,false,"",$results[$n]["ref"]);
        if (file_exists($thepath)){
            $results[$n]['file_path']=$thepath;
        }

if (getval("previewsize","")!=""){
$thepath = get_resource_path($resource,true,getval("previewsize",""),false,'jpg',-1,1,false,"",$results[$n]["ref"]);
         if (file_exists($thepath)&& count ($results[$n])!=1){
            $results[$n]['preview']=$thepath;
        }
    }
}

if (!is_array($results)|| (isset ($results[0])&&count($results[0])==1)){$results=array();}

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
            echo xml_entities($value);
            echo '</'.$resultitem.'>';
        }
        echo '</resource>';
    }
    echo '</results>';
}

else echo json_encode($results); // echo json without headers by default




