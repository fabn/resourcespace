<?php

include "../../include/db.php";
include "../../include/general.php";
include "../../include/resource_functions.php";

$uniqid=getvalescaped("id","");

$progress_file=get_temp_dir(false,$uniqid) . "/progress_file.txt";

if (!file_exists($progress_file)){
	touch($progress_file);
}


$content= file_get_contents($progress_file);
if ($content==""){echo $lang['preparingzip'];}

else if ($content=="zipping"){
	$files=scandir(get_temp_dir(false,$uniqid));
		foreach ($files as $file){
			if (strpos($file,"zip.zip")!==false){
				echo "zipping ".formatfilesize(filesize(get_temp_dir(false,$uniqid)."/".$file));
			}
		}
	}

else {
	ob_start();echo $content;ob_flush();exit();} // echo whatever the script has placed here.
