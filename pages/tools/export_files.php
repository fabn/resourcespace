<?php
#
#
# Quick 'n' dirty script to dump all (undeleted) files with original filenames 
# (metadata unmodifed by RS!) to an external folder (ex. a hard drive)
# Copies all files to the $drive_path, and reports the progress on screen and in a log file
# Alt files go into a folder called original_filename_alt
#

//You must open permissions on the output folder. 
//Afterwards, You may have to mount it with -o uid=1000,gid=1000 to fix access
$drive_path="/home/tom/Desktop/out/";

// set to true to process file with Exiftool against resource_data before copying
$write_metadata=false;

// You may also need to modify some php limits here 
set_time_limit(60*60*5);


include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";

// create a text file for the report
$fp = fopen($drive_path.'export_files_script_report.txt', 'w');
$fpa= fopen($drive_path.'export_alt_files_script_report.txt', 'w');

//function to find duplicate filenames in array
function findDuplicates($data,$dupval) {
$nb= 0;
foreach($data as $key => $val) {if ($val==$dupval) {$nb++;}}
return $nb;
}

// get all resources in the DB omitting those in Deleted state.
$resources=sql_query("select ref,file_extension from resource where ref>0 and archive != 3");

// set up an array to store the filenames as they are found (to analyze dupes)
$filenames=array();

//loop:
foreach($resources as $resource){

  // use extension to get scrambled path 
  $extension = $resource['file_extension'];
  $scrambled=get_resource_path($resource['ref'],true,"",false,$extension);
  
  // get original file name
  $filename=get_data_by_field($resource['ref'],$filename_field);
  
  // save original file name (may be appended to indicate dupe)
  $orig_filename=$filename;
  
  // check if a file has already been processed with this name
  if (in_array($filename,$filenames)){
     // if so, append a dupe tag
     $path_parts=pathinfo($filename);
     if (isset($path_parts['extension'])&& isset($path_parts['filename'])){
        $filename_ext=$path_parts['extension'];
        $filename_wo=$path_parts['filename'];
        $x=findDuplicates($filenames,$filename);
        $filename=$filename_wo."_DUPE".$x.".".$filename_ext;
        fwrite($fp, 'DUPE: ');echo "DUPE: ";
      }
   }
     
   //add original file name to array
   $filenames[]=$orig_filename;
     
   //copy file if both paths exist
   if (file_exists($scrambled)&&$filename!=""){
      echo "copying ".$drive_path.$filename; 
      fwrite($fp, "copying ".$drive_path.$filename."\n");
      // allow to re-run script without re-copying files
      if(!file_exists($drive_path.$filename))
        {
        // optionally write metadata    
        if ($write_metadata){
            $tmpfile=write_metadata($scrambled,$resource['ref']);
            if ($tmpfile!==false && file_exists($tmpfile)){$scrambled=$tmpfile;}
            }
        copy($scrambled,$drive_path.$filename);
        if ($write_metadata && $tmpfile!==false && file_exists($tmpfile)){
            unlink($tmpfile);
            }
        }
      echo "<br />";
   // if file exists but no filename exists, export ref   
   } else if (file_exists($scrambled)){
      echo "No filename: copying ref ".$resource['ref']; 
      fwrite($fp, "No filename: copying ref ".$resource['ref']."\n");
      if(!file_exists($drive_path.$resource['ref']))
        {copy($scrambled,$drive_path.$resource['ref']);}
      echo "<br />";
   // or just note the lack of file
   } else if (!file_exists($scrambled)){
      echo "No file to copy: ref ".$resource['ref']; 
      fwrite($fp, "No file to copy: ref ".$resource['ref']."\n");
      echo "<br />";
   }
   
   // NEW FOR ALT FILES:
   $alt_files=sql_query("select * from resource_alt_files where resource=".$resource['ref']);
   if (count($alt_files)>0){
	   // make folder of the original_filename_alts (if not there already)
	   if (!file_exists($drive_path.$filename."_alts")){
		   mkdir($drive_path.$filename."_alts");
		   chmod($drive_path.$filename."_alts",0777);
		   }
	   foreach ($alt_files as $alt){
		   $scrambled_alt=get_resource_path($resource['ref'],true,"",false,$alt['file_extension'],-1,1,false,"",$alt['ref']);
		   if (file_exists($scrambled_alt)){
			   // allow to re-run script without re-copying files
			   if (!file_exists($drive_path.$filename."_alts/".$alt['file_name'])){
				   copy($scrambled_alt,$drive_path.$filename."_alts/".$alt['file_name']);
			       }
			   }
	       fwrite($fpa, $filename."-- copying alt: ".$alt['file_name']."\n");
	       echo ("&nbsp;&nbsp;<b>".$filename."-- copying alt: ".$alt['file_name']."</b>"); 
	       echo "<br />";
	  }
	} 
	
   // unset all vars in the loop to avoid unintended reuse.
   unset($scrambled);
   unset($filename);
   unset($x);
   unset($filename_ext);
   unset($filename_wo);
   unset($extension);
   unset($orig_filename);
   flush();
  }

fclose($fp);
fclose($fpa);
	
