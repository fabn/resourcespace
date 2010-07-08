<?php

function generate_transform_preview($ref){
	global $storagedir;	
        global $imagemagick_path;

	$tmpdir = "$storagedir/tmp";

        // get imagemagick path
        $command=$imagemagick_path . "/bin/convert";
        if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
        if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
        if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility.'");}

        $orig_ext = sql_value("select file_extension value from resource where ref = '$ref'",'');
        $originalpath= get_resource_path($ref,true,'',false,$orig_ext);

	if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);}
	if(!is_dir($storagedir."/tmp/transform_plugin")){mkdir($storagedir."/tmp/transform_plugin",0777);}

        $command .= " \"$originalpath\" +matte -flatten -colorspace RGB -geometry 450 \"$tmpdir/transform_plugin/pre_$ref.jpg\"";
        shell_exec($command);
	

	// while we're here, clean up any old files still hanging around
	$dp = opendir("$storagedir/tmp/transform_plugin");
	while ($file = readdir($dp)) {
		if ($file <> '.' && $file <> '..'){
			if ((filemtime("$storagedir/tmp/transform_plugin/$file")) < (strtotime('-2 days'))) {
				unlink("$storagedir/tmp/transform_plugin/$file");
			}
		}
	}
	closedir($dp);

        return true;
  
}



?>
