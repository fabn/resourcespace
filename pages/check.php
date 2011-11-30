<?php
include "../include/db.php";
include "../include/general.php";
include "../include/authenticate.php"; if (!checkperm("a")) {exit("Access denied.");}
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
  <h1><?php echo $lang["installationcheck"]?></h1>
  <a href="">&gt; <?php echo $lang["repeatinstallationcheck"]?></a>
  <br/><br/>
<table class="InfoTable">
<?php
# Check PHP version
$phpversion=phpversion();
$phpinifile=php_ini_loaded_file();
if ($phpversion<'4.4') {$result=$lang["status-fail"] . ": " . str_replace("?", "4.4", $lang["shouldbeversion"]);} else {$result=$lang["status-ok"];}
?><tr><td><?php echo str_replace("?", "PHP", $lang["softwareversion"]); ?></td><td><?php echo $phpversion.'  config:'.$phpinifile;?></td><td><b><?php echo $result?></b></td></tr><?php

# Check MySQL version
if ($use_mysqli){
	$mysqlversion=mysqli_get_server_info($db);
	}
else {
	$mysqlversion=mysql_get_server_info();
	}
if ($mysqlversion<'5') {$result=$lang["status-fail"] . ": " . str_replace("?", "5", $lang["shouldbeversion"]);} else {$result=$lang["status-ok"];}
if ($use_mysqli){$encoding=mysqli_client_encoding($db);} else {$encoding=mysql_client_encoding();}
?><tr><td><?php echo str_replace("?", "MySQL", $lang["softwareversion"]); ?></td><td><?php echo $mysqlversion . " " . str_replace("%encoding", $encoding, $lang["client-encoding"]); ?></td><td><b><?php echo $result?></b></td></tr><?php

# Check GD installed
if (function_exists("gd_info"))
	{
	$gdinfo=gd_info();
	if (is_array($gdinfo))
		{
		$version=$gdinfo["GD Version"];
		$result=$lang["status-ok"];
		}
	else
		{
		$version=$lang["status-notinstalled"];
		$result=$lang["status-fail"];
		}
	}
else
	{
	$version=$lang["status-notinstalled"];
	$result=$lang["status-fail"];
	}
?><tr><td><?php echo str_replace("?", "GD", $lang["softwareversion"]); ?></td><td><?php echo $version?></td><td><b><?php echo $result?></b></td></tr><?php

# Check ini values for memory_limit, post_max_size, upload_max_filesize
$memory_limit=ini_get("memory_limit");
if (ResolveKB($memory_limit)<(200*1024)) {$result=$lang["status-warning"] . ": " . str_replace("?", "200M", $lang["shouldbeormore"]);} else {$result=$lang["status-ok"];}
?><tr><td><?php echo str_replace("?", "memory_limit", $lang["phpinivalue"]); ?></td><td><?php echo $memory_limit?></td><td><b><?php echo $result?></b></td></tr><?php

$post_max_size=ini_get("post_max_size");
if (ResolveKB($post_max_size)<(100*1024)) {$result=$lang["status-warning"] . ": " . str_replace("?", "100M", $lang["shouldbeormore"]);} else {$result=$lang["status-ok"];}
?><tr><td><?php echo str_replace("?", "post_max_size", $lang["phpinivalue"]); ?></td><td><?php echo $post_max_size?></td><td><b><?php echo $result?></b></td></tr><?php

$upload_max_filesize=ini_get("upload_max_filesize");
if (ResolveKB($upload_max_filesize)<(100*1024)) {$result=$lang["status-warning"] . ": " . str_replace("?", "100M", $lang["shouldbeormore"]);} else {$result=$lang["status-ok"];}
?><tr><td><?php echo str_replace("?", "upload_max_filesize", $lang["phpinivalue"]); ?></td><td><?php echo $upload_max_filesize?></td><td><b><?php echo $result?></b></td></tr><?php


# Check write access to filestore
$success=is_writable($storagedir);
if ($success===false) {$result=$lang["status-fail"] . ": " . $lang["nowriteaccesstofilestore"];} else {$result=$lang["status-ok"];}
?><tr><td colspan="2"><?php echo $lang["writeaccesstofilestore"] ?></td><td><b><?php echo $result?></b></td></tr>

<?php
if (in_array("transform",$plugins)){
# Check write access to homeanim
$success=is_writable(dirname(__FILE__) . "/../".$homeanim_folder);
if ($success===false) {$result=$lang["status-fail"] . ": " . $lang["nowriteaccesstohomeanim"];} else {$result=$lang["status-ok"];}
?><tr><td colspan="2"><?php echo $lang["writeaccesstohomeanim"] ?></td><td><b><?php echo $result?></b></td></tr>
<?php } 

# Check filestore folder browseability
$output=@file_get_contents($baseurl . "/filestore");
if (strpos($output,"Index of")===false)
	{
	$result=$lang["status-ok"];
	}
else
	{
	$result=$lang["status-fail"] . ": " . $lang["noblockedbrowsingoffilestore"];
	}
?><tr><td colspan="2"><?php echo $lang["blockedbrowsingoffilestore"] ?></td><td><b><?php echo $result?></b></td></tr>

<?php
$imagemagick_version="";
function CheckImagemagick()
	{
 	global $imagemagick_path, $lang;
 
 	# Check for path
 	$path=$imagemagick_path . "/convert";
	if (!file_exists($path)) {$path=$imagemagick_path . "/convert.exe";}
	if (!file_exists($path)) {return false;}
	
	# Check execution and return version
	$version=run_command($path . " -version");
	if (strpos($version,"ImageMagick")===false && strpos($version,"GraphicsMagick")===false)
		{
		return str_replace("?", "$version", $lang["executionofconvertfailed"]);
		}	
		
	# Set version
	$s=explode("\n",$version);
	global $imagemagick_version;$imagemagick_version=$s[0];
	
	return true;
	}

$ffmpeg_version="";
function CheckFfmpeg()
{
 	global $ffmpeg_path, $lang;
 	
 	# Check for path
 	$path=$ffmpeg_path . "/ffmpeg";
	if (!file_exists($path)) {$path=$ffmpeg_path . "/ffmpeg.exe";}
	if (!file_exists($path)) {return false;}
	
	# Check execution and return version
		$out = run_external(escapeshellcmd($path) . " -version", $code);
    if (isset($out[0])) {$version = $out[0];}
	if (strpos(strtolower($version),"ffmpeg")===false)
		{
		return str_replace("?", "$version", $lang["executionofconvertfailed"]);
		}	
		
	# Set version
	$s=explode("\n",$version);
	global $ffmpeg_version;$ffmpeg_version=$s[0];
	
	return true;
}
function CheckGhostscript()
{
 	global $ghostscript_path, $ghostscript_executable;
	if (file_exists($ghostscript_path . "/" . $ghostscript_executable)) return true;
	if (file_exists($ghostscript_path . "/" . $ghostscript_executable . ".exe")) return true;
	return false;
}
function CheckExiftool()
{
 	global $exiftool_path;
	if (file_exists($exiftool_path . "/exiftool")) return true;
	if (file_exists($exiftool_path . "/exiftool.exe")) return true;	
	return false;
}

# Check ImageMagick path
if (isset($imagemagick_path))
	{	 
	$result=CheckImagemagick();
	if ($result===true)
		{
		$result=$lang["status-ok"];
		}
	else
		{
		$result=$lang["status-fail"] . ": " . $result;
		}
	}
else
	{
	$result=$lang["status-notinstalled"];
	}
?><tr><td <?php if ($imagemagick_version=="") { ?>colspan="2"<?php } ?>>ImageMagick</td>
<?php if ($imagemagick_version!="") { ?><td><?php echo $imagemagick_version ?></td><?php } ?>
<td><b><?php echo $result?></b></td></tr><?php


# Check FFmpeg path
if (isset($ffmpeg_path))
	{
	if (CheckFfmpeg())
		{
		$result=$lang["status-ok"];
		}
	else
		{
		$result= $lang["status-fail"] . ": " . str_replace("?", "$ffmpeg_path/ffmpeg", $lang["softwarenotfound"]);
		}
	}
else
	{
	$result=$lang["status-notinstalled"];
	}
?><tr><td <?php if ($ffmpeg_version=="") { ?>colspan="2"<?php } ?>>FFmpeg</td>
<?php if ($ffmpeg_version!="") { ?><td><?php echo $ffmpeg_version ?></td><?php } ?>
<td><b><?php echo $result?></b></td></tr><?php


# Check Ghostscript path
if (isset($ghostscript_path))
	{
	if (CheckGhostscript())
		{
		$result=$lang["status-ok"];
		}
	else
		{
		$result= $lang["status-fail"] . ": " . str_replace("?", "$ghostscript_path/gs", $lang["softwarenotfound"]);
		}
	}
else
	{
	$result=$lang["status-notinstalled"];
	}
?><tr><td colspan="2">Ghostscript</td><td><b><?php echo $result?></b></td></tr><?php


# Check Exif function
if (function_exists('exif_read_data')) 
	{
	$result=$lang["status-ok"];
	}
else
	{
	$version=$lang["status-notinstalled"];
	$result=$lang["status-fail"];
	}
?><tr><td colspan="2"><?php echo $lang["exif_extension"]?></td><td><b><?php echo $result?></b></td></tr><?php

# Check Exiftool path
if (isset($exiftool_path))
	{
	if (CheckExiftool())
		{
		$result=$lang["status-ok"];
		}
	else
		{
		$result=$lang["status-fail"] . ": " . str_replace("?", "$exiftool_path/exiftool", $lang["softwarenotfound"]);
		}
	}
else
	{
	$result=$lang["status-notinstalled"];
	}
?><tr><td colspan="2">Exiftool</td><td><b><?php echo $result?></b></td></tr>

<?php hook("addinstallationcheck");?>

<tr>
<td><?php echo $lang["lastscheduledtaskexection"] ?></td>
<td><?php $last_cron=sql_value("select datediff(now(),value) value from sysvars where name='last_cron'",$lang["status-never"]);echo $last_cron ?></td>
<td><?php if ($last_cron>2 || $last_cron==$lang["status-never"]) { ?><b><?php echo $lang["status-warning"] ?></b><br/><?php echo $lang["executecronphp"] ?><?php } else {?><b><?php echo $lang["status-ok"] ?></b><?php } ?></td>

</tr>


</table>
</div>

<?php
include "../include/footer.php";
?>
