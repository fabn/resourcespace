<?php

include(dirname(__FILE__)."/../../include/db.php");
include(dirname(__FILE__)."/../../include/general.php");
include(dirname(__FILE__)."/../../include/search_functions.php");
include(dirname(__FILE__)."/../../include/resource_functions.php");
$api=true;

include(dirname(__FILE__)."/../../include/authenticate.php");

// required: check that this plugin is available to the user
if (!in_array("api_search",$plugins)){die("no access");}

$search=getval("search","");
$search=refine_searchstring($search);
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


if ($api_search['signed']){

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


$results=do_search($search,$restypes,$order_by,$archive,-1,$sort,false,$starsearch);

if (!is_array($results)){$results=array();}

if (getval("previewsize","")!=""){
    for($n=0;$n<count($results);$n++){
        $access=get_resource_access($results[$n]);
        $use_watermark=check_use_watermark();
        $filepath=get_resource_path($results[$n]['ref'],true,getval('previewsize',''),false,'jpg',-1,1,$use_watermark,'',-1);
        $previewpath=get_resource_path($results[$n]['ref'],false,getval("previewsize",""),false,"jpg",-1,1,$use_watermark,"",-1);
        if (file_exists($filepath)){
            $results[$n]['preview']=$previewpath;
        }
        else {
            $previewpath=explode('filestore/',$previewpath);
            $previewpath=$previewpath[0]."gfx/";
            $file=$previewpath.get_nopreview_icon($results[$n]["resource_type"],$results[$n]["file_extension"],false,true);
            $results[$n]['preview']=$file;
        }
    }
}

// flv file and thumb if available
if (getval("flvfile","")!=""){
    for($n=0;$n<count($results);$n++){
        // flv previews
        $flvfile=get_resource_path($results[$n]['ref'],true,"pre",false,$ffmpeg_preview_extension);
        if (!file_exists($flvfile)) {$flvfile=get_resource_path($results[$n]['ref'],true,"",false,$ffmpeg_preview_extension);}
        if (!(isset($results[$n]['is_transcoding']) && $results[$n]['is_transcoding']==1) && file_exists($flvfile) && (strpos(strtolower($flvfile),".".$ffmpeg_preview_extension)!==false))
            {
            if (file_exists(get_resource_path($results[$n]['ref'],true,"pre",false,$ffmpeg_preview_extension)))
                {
                $flashpath=get_resource_path($results[$n]['ref'],false,"pre",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
                }
            else 
                {
                $flashpath=get_resource_path($results[$n]['ref'],false,"",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
                }
            $results[$n]['flvpath']=$flashpath;
            $thumb=get_resource_path($results[$n]['ref'],false,"pre",false,"jpg"); 
            $results[$n]['flvthumb']=$thumb;
        }
    }
}



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




