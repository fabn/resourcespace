<?php

function generate_transform_preview($ref){
	global $storagedir;	
        global $imagemagick_path;

	$tmpdir = get_temp_dir();

        // get imagemagick path
        $command=$imagemagick_path . "/bin/convert";
        if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
        if (!file_exists($command)) {$command=$imagemagick_path . "\convert.exe";}
        if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility.'");}

        $orig_ext = sql_value("select file_extension value from resource where ref = '$ref'",'');
        $originalpath= get_resource_path($ref,true,'',false,$orig_ext);

	# Since this check is in get_temp_dir() omit: if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);}
	if(!is_dir(get_temp_dir() . "/transform_plugin")){mkdir(get_temp_dir() . "/transform_plugin",0777);}

        $command .= " \"$originalpath\" +matte -flatten -colorspace RGB -geometry 450 \"$tmpdir/transform_plugin/pre_$ref.jpg\"";
        shell_exec($command);
	

	// while we're here, clean up any old files still hanging around
	$dp = opendir(get_temp_dir() . "/transform_plugin");
	while ($file = readdir($dp)) {
		if ($file <> '.' && $file <> '..'){
			if ((filemtime(get_temp_dir() . "/transform_plugin/$file")) < (strtotime('-2 days'))) {
				unlink(get_temp_dir() . "/transform_plugin/$file");
			}
		}
	}
	closedir($dp);

        return true;
  
}



?>
