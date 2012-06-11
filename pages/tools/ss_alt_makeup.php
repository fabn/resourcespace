<?php
// ss_alt_makeup.php / by David Dwiggins, ddwiggins@historicnewengland.org, 6/11/2012
//
// this script is designed to work with staticsync_alt.php
// normally, files with certain suffixes (defined in $staticsync_alt_suffix_array)
// will be attached as alternates when staticsync_alt runs. However, occasionally, some may be missed
// or you may wish to add them later.
// this script uses the filenames and path information stored in the resource metadata to match up and
// attach these alternate files after the fact.
// Note that this requires that you have previously set up $staticsync_mapped_category_tree to tell
// where the file came from.
//
// This script has been designed to work with staticsync_alt in ingest mode. It has not been tested against 
// other configurations. Use at your own risk.
// 

include dirname(__FILE__) . "/../../include/db.php";
include dirname(__FILE__) . "/../../include/general.php";
include dirname(__FILE__) . "/../../include/resource_functions.php";
include dirname(__FILE__) . "/../../include/image_processing.php";

if (!(isset($staticsync_mapped_category_tree) && is_numeric($staticsync_mapped_category_tree))){
	echo "Error: This script requires use of a mapped category tree for staticsync.\n";
	exit;
}

if (file_exists(dirname(__FILE__) . "/staticsync_local_functions.php")){
        include(dirname(__FILE__) . "/staticsync_local_functions.php");
}

if ($staticsync_ingest){
        echo date('Y-m-d H:i:s    ');
        echo "Staticsync is running in ingest mode.\n";
} else {
        echo date('Y-m-d H:i:s    ');
        echo "Staticsync is running in sync mode.\n";
}

$staticsync_alt_suffix_array = array('_alt','_verso','_DNG','_VERSO','_ALT','_dng','_orig','_ORIG','_tp','_TP','_tpv','_TPV','_cov','_COV','_ex','_EX','_scr','_SCR');
$staticsync_alt_suffixes = true;
$numeric_alt_suffixes = 8;
$file_minimum_age = 120; // don't touch files that aren't at least this many seconds old


if ($numeric_alt_suffixes > 0){
        // add numeric suffixes to alt suffix list if we've been told to do that.
        $newsuffixarray = array();
        foreach ($staticsync_alt_suffix_array as $thesuffix){
                array_push($newsuffixarray,$thesuffix);
                for ($i = 1; $i < $numeric_alt_suffixes; $i++){
                        array_push($newsuffixarray,$thesuffix.$i);
                }
        }
        $staticsync_alt_suffix_array = $newsuffixarray;
}



echo date('Y-m-d H:i:s    ');
echo "Looking for orphaned alternate files\n";

echo date('Y-m-d H:i:s    ');
echo "syncdir is $syncdir\n";

$files = dir_tree($syncdir);

//print_r($staticsync_alt_suffix_array);

foreach ($files as $thefile){

	if (is_dir($thefile)|| preg_match("/^\..*/",basename($thefile))){continue;}; // ignore directories and hidden files
	//echo "$thefile\n";
	
	foreach ($staticsync_alt_suffix_array as $suffix){
		if (preg_match("/^" . preg_quote($syncdir,"/") . "\/(.*)\/([^\/]+)($suffix)\.([^.]+)\$/",$thefile,$matches)){
			echo date('Y-m-d H:i:s    ');
			echo "Processing $thefile\n";
			//print_r($matches);

			$filename_field = 51;
			$searchpath = addslashes(str_replace('/','~',$matches[1]));
			$filename = $matches['2'];

			$sql = "select rdf.resource value from resource_data rdp 
			left join resource_data rdf on rdp.resource = rdf.resource and rdf.resource_type_field = $filename_field
			where rdp.resource_type_field = $staticsync_mapped_category_tree and rdp.value like 
			'%,$searchpath' and rdf.value like '$filename%' limit 1";

			$resource = sql_value($sql,'0');

			$ext = $matches[4];
			$thisfilesuffix = $matches[3];


			if ($resource > 0){
				echo date('Y-m-d H:i:s    ');

				echo "attaching to resource $resource!\n";
                                                            echo date('Y-m-d H:i:s    ');
                                                            echo "Attaching $thefile as alternative.\n";
                                                            $filetype=filetype($thefile);
                                                            # Create alternative file
                                                            global $lang;

                                                                if (preg_match("/^_VERSO[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Verso";
                                                                } elseif(preg_match("/^_DNG[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "DNG";
                                                                } elseif(preg_match("/^_ORIG[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Original Scan";
                                                                } elseif(preg_match("/^_TPV[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Title Page Verso";
                                                                } elseif(preg_match("/^_TP[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Title Page";
                                                                } elseif(preg_match("/^_COV[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Cover";
                                                                } elseif(preg_match("/^_SCR[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Inscription";
                                                                } elseif(preg_match("/^_EX[0-9]*/i",$thisfilesuffix)){
                                                                        $alt_title = "Enclosure";
                                                                } else {
                                                                        $alt_title = $filename;
                                                                }

                                                            $aref = add_alternative_file($resource, $alt_title, strtoupper($ext) . " " . $lang["file"], $thefile, $ext, filesize_unlimited($thefile));

                                                            $path=get_resource_path($resource, true, "", true, $ext, -1, 1, false, "", $aref);
                                                            rename ($thefile,$path); # Move alternative file

                                                            global $alternative_file_previews;
                                                            if ($alternative_file_previews)
                                                                    {
                                                                    create_previews($resource,false,$ext,false,false,$aref);
                                                                    }


			} else {
				echo date('Y-m-d H:i:s    ');
				echo "matching resource not found.\n";
			}

		}
	}
}



function dir_tree($dir) {
   $path = '';
   $stack[] = $dir;
   while ($stack) {
       $thisdir = array_pop($stack);
       if ($dircont = scandir($thisdir)) {
           $i=0;
           while (isset($dircont[$i])) {
               if ($dircont[$i] !== '.' && $dircont[$i] !== '..') {
                   $current_file = "{$thisdir}/{$dircont[$i]}";
                   if (is_file($current_file)) {
                       $path[] = "{$thisdir}/{$dircont[$i]}";
                   } elseif (is_dir($current_file)) {
                        $path[] = "{$thisdir}/{$dircont[$i]}";
                       $stack[] = $current_file;
                   }
               }
               $i++;
           }
       }
   }
   return $path;
}

?>
