<?php

define("RUNNING_ASYNC", !isset($ffmpeg_preview));

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
	
	$ffmpeg_path.="/ffmpeg";
	if (!file_exists($ffmpeg_path)) {$ffmpeg_path.=".exe";}
	$ffmpeg_path=escapeshellarg($ffmpeg_path);
	
	# A work-around for Windows systems. Prefixing the command prevents a problem
	# with double quotes.
    global $config_windows;
    if ($config_windows)
       	{
	    $ffmpeg_path = "cd & " . $ffmpeg_path;
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

$shell_exec_cmd = $ffmpeg_path . " -y -i " . escapeshellarg($file) . " $ffmpeg_preview_options -s {$width}x{$height} -t $ffmpeg_preview_seconds " . escapeshellarg($targetfile);
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

