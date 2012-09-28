<?php
#
#
# Quick 'n' dirty script to update all filesizes (if affected by du inconsistency)
# This puts values gained from filesize_unlimited into resource_dimensions and resource_alt_files
# also updates disk usage in case cron job isn't running.



include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";

set_time_limit(0);

# get all resources in the DB
$resources=sql_query("select ref,field".$view_title_field.",file_extension from resource where ref>0 order by ref DESC");

//loop:
foreach($resources as $resource){
   $resource_path=get_resource_path($resource['ref'],true,"",false,$resource['file_extension']);
   if (file_exists($resource_path)) {
	    $filesize=filesize_unlimited($resource_path);
		
		sql_query("update resource_dimensions set file_size=$filesize where resource='".$resource['ref']."'");
		echo ("Ref: ".$resource['ref']." - ".$resource['field'.$view_title_field]." - updating resource_dimensions file_size column - ".formatfilesize($filesize)); 
	    echo "<br />";
   
   }
	
   $alt_files=sql_query("select file_extension,file_name,ref from resource_alt_files where resource=".$resource['ref']);
   if (count($alt_files)>0){
	   foreach ($alt_files as $alt){
		   $alt_path=get_resource_path($resource['ref'],true,"",false,$alt['file_extension'],-1,1,false,"",$alt['ref']);
		   if (file_exists($alt_path)){
			   // allow to re-run script without re-copying files
			   $filesize=filesize_unlimited($alt_path);
			   sql_query("update resource_alt_files set file_size=$filesize where resource='".$resource['ref']."' and ref='".$alt['ref']."'");
		       echo ("&nbsp;&nbsp;&nbsp;&nbsp;ALT - ".$alt['file_name']." - updating alt file size - ".formatfilesize($filesize)); 
	           echo "<br />";
	       }   
	  }
	} 

  update_disk_usage($resource['ref']); 
  echo ("updating disk usage"); 
  echo "<br />"; echo "<br />";
  flush();ob_flush();
 }

