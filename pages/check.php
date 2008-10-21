<?
include "../include/db.php";
include "../include/general.php";
include "../include/header.php";

# A simple script to check the ResourceSpace hosting environment supports our needs.

function ResolveKB($value)
	{
	$value=trim(strtoupper($value));
	if (substr($value,-1,1)=="K")
		{
		return substr($value,0,strlen($value)-1);
		}
	if (substr($value,-1,1)=="M")
		{
		return substr($value,0,strlen($value)-1) * 1024;
		}
	if (substr($value,-1,1)=="G")
		{
		return substr($value,0,strlen($value)-1) * 1024 * 1024;
		}
	return $value;
	}

?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>Installation Check</h1>
  
<table class="InfoTable">
<?
# Check PHP version
$phpversion=phpversion();
if ($phpversion<'4.4') {$result="FAIL: should be 4.4 or greater";} else {$result="OK";}
?><tr><td>PHP version</td><td><?=$phpversion?></td><td><b><?=$result?></b></td></tr><?

# Check MySQL version
$mysqlversion=mysql_get_server_info();
if ($mysqlversion<'5') {$result="FAIL: should be 5 or greater";} else {$result="OK";}
?><tr><td>MySQL version</td><td><?=$mysqlversion?></td><td><b><?=$result?></b></td></tr><?

# Check GD installed
$gdinfo=gd_info();
if (is_array($gdinfo))
	{
	$version=$gdinfo["GD Version"];
	$result="OK";
	}
else
	{
	$version="Not installed.";
	$result="FAIL";
	}
?><tr><td>GD version</td><td><?=$version?></td><td><b><?=$result?></b></td></tr><?

# Check ini values for memory_limit, post_max_size, upload_max_filesize
$memory_limit=ini_get("memory_limit");
if (ResolveKB($memory_limit)<(200*1024)) {$result="WARNING: should be 200M or greater";} else {$result="OK";}
?><tr><td>PHP.INI value for 'memory_limit'</td><td><?=$memory_limit?></td><td><b><?=$result?></b></td></tr><?

$post_max_size=ini_get("post_max_size");
if (ResolveKB($post_max_size)<(100*1024)) {$result="WARNING: should be 100M or greater";} else {$result="OK";}
?><tr><td>PHP.INI value for 'post_max_size'</td><td><?=$post_max_size?></td><td><b><?=$result?></b></td></tr><?

$upload_max_filesize=ini_get("upload_max_filesize");
if (ResolveKB($upload_max_filesize)<(100*1024)) {$result="WARNING: should be 100M or greater";} else {$result="OK";}
?><tr><td>PHP.INI value for 'upload_max_filesize'</td><td><?=$upload_max_filesize?></td><td><b><?=$result?></b></td></tr><?


# Check write access to $storagedir
$success=is_writable($storagedir);
if ($success===false) {$result="FAIL: $storagedir not writable";} else {$result="OK";}
?><tr><td colspan="2">Write access to '$storagedir' directory</td><td><b><?=$result?></b></td></tr><?

function CheckImagemagick()
{
 	global $imagemagick_path;
	if (file_exists($imagemagick_path . "/convert")) return true;
	if (file_exists($imagemagick_path . "/convert.exe")) return true;	
	return false;
}
function CheckFfmpeg()
{
 	global $ffmpeg_path;
	if (file_exists($ffmpeg_path . "/ffmpeg")) return true;
	if (file_exists($ffmpeg_path . "/ffmpeg.exe")) return true;	
	return false;
}
function CheckGhostscript()
{
 	global $ghostscript_path;
	if (file_exists($ghostscript_path . "/gs")) return true;
	if (file_exists($ghostscript_path . "/gs.exe")) return true;	
	return false;
}

# Check ImageMagick path
if (isset($imagemagick_path))
	{	 
	if (CheckImagemagick())
		{
		$result="OK";
		}
	else
		{
		$result="FAIL: '$imagemagick_path/convert' not found";
		}
	}
else
	{
	$result="(not installed)";
	}
?><tr><td colspan="2">ImageMagick</td><td><b><?=$result?></b></td></tr><?


# Check FFmpeg path
if (isset($ffmpeg_path))
	{
	if (CheckFfmpeg())
		{
		$result="OK";
		}
	else
		{
		$result="FAIL: '$ffmpeg_path/ffmpeg' not found";
		}
	}
else
	{
	$result="(not installed)";
	}
?><tr><td colspan="2">FFmpeg</td><td><b><?=$result?></b></td></tr><?


# Check Ghostscript path
if (isset($ghostscript_path))
	{
	if (CheckGhostscript())
		{
		$result="OK";
		}
	else
		{
		$result="FAIL: '$ghostscript_path/gs' not found";
		}
	}
else
	{
	$result="(not installed)";
	}
?><tr><td colspan="2">Ghostscript</td><td><b><?=$result?></b></td></tr><?


# Check Exif function
if (function_exists('exif_read_data')) 
	{
	$result="OK";
	}
else
	{
	$version="Not installed.";
	$result="FAIL";
	}
?><tr><td colspan="2">EXIF extension installed</td><td><b><?=$result?></b></td></tr><?


# Check Exiftool path
if (isset($exiftool_path))
	{
	if (file_exists(stripslashes($exiftool_path) . "/exiftool"))
		{
		$result="OK";
		}
	else
		{
		$result="FAIL: '$exiftool_path/exiftool' not found";
		}
	}
else
	{
	$result="(not installed)";
	}
?><tr><td colspan="2">Exiftool</td><td><b><?=$result?></b></td></tr><?


?>
</table>
	
</div>

<?
include "../include/footer.php";
?>