<?php 
include "../../include/db.php";
include "../../include/authenticate.php"; 
include "../../include/general.php"; 
$ref=getval("ref","");
$ext=getval("ext","");

$image=get_resource_path($ref,true,"",false,$ext);
	if (!file_exists($image)) {return "error";}

global $exiftool_path;
	if (file_exists(stripslashes($exiftool_path) . "/exiftool") || file_exists(stripslashes($exiftool_path) . "/exiftool.exe"))
			{
            $command=$exiftool_path."/exiftool -h " . escapeshellarg($image);
            $report= shell_exec($command);?>
				
				All metadata in the Original <?php echo strtoupper($ext)?> File.<br><br>
				<?php echo $report;               
         }
         
	 else {echo "Could not find Exiftool";}
