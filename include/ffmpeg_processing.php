<?php

if (!defined("RUNNING_ASYNC")) {define("RUNNING_ASYNC", !isset($ffmpeg_preview));}

$ffmpeg_path_working=$ffmpeg_path . "/ffmpeg";
if (!file_exists($ffmpeg_path_working)) {$ffmpeg_path_working.=".exe";}
$ffmpeg_path_working=escapeshellarg($ffmpeg_path_working);

if (RUNNING_ASYNC)
	{
	require dirname(__FILE__)."/db.php";
	require dirname(__FILE__)."/general.php";
	
	if (empty($_SERVER['argv'][1]) || $scramble_key!==$_SERVER['argv'][1]) {exit("Incorrect scramble_key");}
	
	if (empty($_SERVER['argv'][2])) {exit("Ref param missing");}
	$ref=$_SERVER['argv'][2];
	
	if (empty($_SERVER['argv'][3])) {exit("File param missing");}
	$file=$_SERVER['argv'][3];
	
	if (empty($_SERVER['argv'][4])) {exit("Target param missing");}
	$target=$_SERVER['argv'][4];
	
	if (!isset($_SERVER['argv'][5])) {exit("Previewonly param missing");}
	$previewonly=$_SERVER['argv'][5];
	
	# A work-around for Windows systems. Prefixing the command prevents a problem
	# with double quotes.
    global $config_windows;
    if ($config_windows)
       	{
	    $ffmpeg_path_working = "cd & " . $ffmpeg_path_working;
       	}
	
	sql_query("UPDATE resource SET is_transcoding = 1 WHERE ref = '".escape_check($ref)."'");
	}
else 
	{
	global $qtfaststart_path, $qtfaststart_extensions;
	}
	
# Increase timelimit
set_time_limit(0);

# Create a preview video (FLV)
$targetfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension); 

$snapshotsize=getimagesize($target);
$width=$snapshotsize[0];
$height=$snapshotsize[1];
$sourcewidth=$width;
$sourceheight=$height;

if($height<$ffmpeg_preview_min_height)
	{
	$height=$ffmpeg_preview_min_height;
	}

if($width<$ffmpeg_preview_min_width)
	{
	$width=$ffmpeg_preview_min_width;
	}

if($height>$ffmpeg_preview_max_height)
	{
	$width=ceil($width*($ffmpeg_preview_max_height/$height));
	$height=$ffmpeg_preview_max_height;
	}
	
if($width>$ffmpeg_preview_max_width)
	{
	$height=ceil($height*($ffmpeg_preview_max_width/$width));
	$width=$ffmpeg_preview_max_width;
	}

# Frame size must be a multiple of two
if ($width % 2){$width++;}
if ($height % 2) {$height++;}

$shell_exec_cmd = $ffmpeg_path_working . " -y -i " . escapeshellarg($file) . " $ffmpeg_preview_options -s {$width}x{$height} -t $ffmpeg_preview_seconds " . escapeshellarg($targetfile);
$output=shell_exec($shell_exec_cmd);

if (!file_exists($targetfile))
    {
    error_log("FFmpeg failed: ".$shell_exec_cmd);
    }

if($qtfaststart_path && file_exists($qtfaststart_path . "/qt-faststart") && in_array($ffmpeg_preview_extension, $qtfaststart_extensions) )
    {
	$targetfiletmp=$targetfile.".tmp";
	rename($targetfile, $targetfiletmp);
    $output=shell_exec($qtfaststart_path . "/qt-faststart " . escapeshellarg($targetfiletmp) . " " . escapeshellarg($targetfile));
    unlink($targetfiletmp);
    }

# Handle alternative files.
global $ffmpeg_alternatives;
if (isset($ffmpeg_alternatives))
	{
	for($n=0;$n<count($ffmpeg_alternatives);$n++)
		{
		$generate=true;
		if (isset($ffmpeg_alternatives[$n]["lines_min"]))
			{
			# If this alternative size is larger than the source, do not generate.
			if ($ffmpeg_alternatives[$n]["lines_min"]>$sourceheight)
				{
				$generate=false;
				}
			
			}
					
		if ($generate) # OK to generate this alternative?
			{
			# Remove any existing alternative file(s) with this name.
			$existing=sql_query("select ref from resource_alt_files where resource='$ref' and name='" . escape_check($ffmpeg_alternatives[$n]["name"]) . "'");
			for ($m=0;$m<count($existing);$m++)
				{
				delete_alternative_file($ref,$existing[$m]["ref"]);
				}
				
			# Create the alternative file.
			$aref=add_alternative_file($ref,$ffmpeg_alternatives[$n]["name"]);
			$apath=get_resource_path($ref,true,"",true,$ffmpeg_alternatives[$n]["extension"],-1,1,false,"",$aref);
			
			# Process the video 
			$shell_exec_cmd = $ffmpeg_path_working . " -y -i " . escapeshellarg($file) . " " . $ffmpeg_alternatives[$n]["params"] . " " . escapeshellarg($apath);
			$output=shell_exec($shell_exec_cmd);
	
			if (file_exists($apath))
				{
				# Update the database with the new file details.
				$file_size=filesize($apath);
				sql_query("update resource_alt_files set file_name='" . escape_check($ffmpeg_alternatives[$n]["filename"] . "." . $ffmpeg_alternatives[$n]["extension"]) . "',file_extension='" . escape_check($ffmpeg_alternatives[$n]["extension"]) . "',file_size='" . $file_size . "',creation_date=now() where ref='$aref'");
				}
			}
		}
	}



if (!mysql_ping())
	{
	mysql_connect($mysql_server,$mysql_username,$mysql_password,true);
	mysql_select_db($mysql_db);
	// If $mysql_charset is defined, we use it
	// else, we use the default charset for mysql connection.
	if(isset($mysql_charset))
		{
		if($mysql_charset)
			{
			mysql_set_charset($mysql_charset);
			}
		}
	}

if (RUNNING_ASYNC)
	{
	sql_query("UPDATE resource SET is_transcoding = 0 WHERE ref = '".escape_check($ref)."'");
	
	if ($previewonly)
		{
		unlink($file);
		}
	}

