<?php
/*

Go from resource 0 to highest resource in system looking for directories
in the filestore without associated resource table entries. If they exist,
delete them.

*/


include dirname(__FILE__) . "/../../include/db.php";
include dirname(__FILE__) . "/../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include dirname(__FILE__) . "/../../include/general.php";
include dirname(__FILE__) . "/../../include/resource_functions.php";


$dryrun = getval('dryrun','');
if (strlen($dryrun) > 0) $dryrun = true; else $dryrun = false;

$start_id = 1;
$max_id = sql_value("select max(ref) value from resource",1);
echo "\n<pre>\n";

for ($checking=$start_id; $checking <= $max_id; $checking++){
	$thedir = dirname(get_resource_path($checking,true,'',false));
	if (!file_exists($thedir)) continue;
	$exists = sql_value("select count(ref) value from resource where ref = '$checking'",0);
	if ($exists == 0){
		// No database record for this directory!
		echo "$checking: checking $thedir\n";
		echo "    DATABASE RECORD NOT FOUND!\n";
		rrmdir($thedir);
	}

}

# recursively remove directory
function rrmdir($dir) {
    global $dryrun;
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file)) {
            rrmdir($file);
        } else {
	    if ($dryrun){
		 echo "    would be unlinking $file\n";
	    } else {
		 echo "    unlinking $file\n";
                 unlink($file);
            }
	}
    }
    if ($dryrun){
	 echo "    would be removing $dir\n";
    } else {
	 echo "    removing $dir\n";
	    rmdir($dir);
    }
}

echo "\n-----------------------------------\nRun complete.";
echo "\n-----------------------------------\n";
echo "</pre>\n";

?>
