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
?><tr><td><?php echo str_replace("?", "PHP", $lang["softwareversion"]); ?></td><td><?php echo $phpversion.'&ensp;&ensp;'.$lang["config"].': '.$phpinifile;?></td><td><b><?php echo $result?></b></td></tr><?php

# Check MySQL version
if ($use_mysqli){
	$mysqlversion=mysqli_get_server_info($db);
	}
else {
	$mysqlversion=mysql_get_server_info();
	}
if ($mysqlversion<'5') {$result=$lang["status-fail"] . ": " . str_replace("?", "5", $lang["shouldbeversion"]);} else {$result=$lang["status-ok"];}
if ($use_mysqli){$encoding=mysqli_client_encoding($db);} else {$encoding=mysql_client_encoding();}
?><tr><td><?php echo str_replace("?", "MySQL", $lang["softwareversion"]); ?></td><td><?php echo $mysqlversion . "&ensp;&ensp;" . str_replace("%encoding", $encoding, $lang["client-encoding"]); ?></td><td><b><?php echo $result?></b></td></tr><?php

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

function get_ffmpeg_version()
    {
    global $lang;

    # Check for path
    $ffmpeg_fullpath = get_utility_path("ffmpeg");
    if ($ffmpeg_fullpath==false) {return false;}
    else
        {
        # Check execution and return version
        $version = run_command($ffmpeg_fullpath . " -version");
        if (strpos(strtolower($version),"ffmpeg")===false)
            {
            return str_replace("?", "$version", $lang["executionofconvertfailed"]);
            }

        # Set version
        $s=explode("\n",$version);
        return $ffmpeg_version=$s[0];
        }
    }

$ghostscript_tested="";
function CheckGhostscript()
	{
 	global $ghostscript_path, $ghostscript_executable, $ghostscript_tested;
 	$ghostscript_tested = $ghostscript_path . '/' . $ghostscript_executable;
	if (!file_exists($ghostscript_tested))
		{
		$ghostscript_tested=$ghostscript_path. '\\' . $ghostscript_executable . '.exe';
		if (!file_exists($ghostscript_tested))
			return false;
		}

	$version=run_command('"'.$ghostscript_tested.'"' . " -version");
	if (strpos($version, "Ghostscript")===false)
		return false;

	return true;
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
$ffmpeg_version = get_ffmpeg_version();
if (!isset($ffmpeg_path))
    {
    $result=$lang["status-notinstalled"];
    }
else
    {
    if ($ffmpeg_version!=false)
        {
        $result=$lang["status-ok"];
        }
    else
        {
        if (strtolower(substr(PHP_OS, 0, 3)) === 'win')
            {
            # On a Windows server.
            $result=$lang["status-fail"] . ":<br>" . str_replace("?", $ffmpeg_path . "\\ffmpeg.exe", $lang["softwarenotfound"]);
            }
        else
            {
            # Not on a Windows server.
            $result=$lang["status-fail"] . ": " . str_replace("?", stripslashes($ffmpeg_path) . "/ffmpeg", $lang["softwarenotfound"]);
            }
        }
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
		$result= $lang["status-fail"] . ": " . str_replace("?", $ghostscript_tested, $lang["softwarenotfound"]);
		}
	}
else
	{
	$result=$lang["status-notinstalled"];
	}
?><tr><td colspan="2">Ghostscript</td><td><b><?php echo $result?></b></td></tr><?php


# Check Exif extension
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
if (!isset($exiftool_path))
	{
	$result=$lang["status-notinstalled"];
	}
else
	{
	if (get_utility_path("exiftool")!=false)
		{
		$result=$lang["status-ok"];
		}
	else
		{
		if (strtolower(substr(PHP_OS, 0, 3)) === 'win')
			{
			# On a Windows server.
			$result=$lang["status-fail"] . ":<br>" . str_replace("?", $exiftool_path . "\\exiftool.exe", $lang["softwarenotfound"]);
			}
		else
			{
			# Not on a Windows server.
			$result=$lang["status-fail"] . ": " . str_replace("?", stripslashes($exiftool_path) . "/exiftool", $lang["softwarenotfound"]);
			}
		}
	}

?><tr><td colspan="2">Exiftool</td><td><b><?php echo $result?></b></td></tr><?php

# Check archiver path
if ($collection_download || isset($zipcommand)) # Only check if it is going to be used.
    {
    if (!(isset($archiver_path) && isset($archiver_executable)) && !isset($zipcommand))
        {
        $result = $lang["status-notinstalled"];
        }
    elseif ($collection_download && (get_utility_path("archiver")!=false))
        {
        $result = $lang["status-ok"];
        if (isset($zipcommand)) {$result.= "<br/>" . $lang["zipcommand_overridden"];}
        }
    elseif (isset($zipcommand))
        {
        $result = $lang["status-warning"] . ": " . $lang["zipcommand_deprecated"];
        }
    else
        {
        if ($config_windows)
            {
            # On a Windows server.
            $result = $lang["status-fail"] . ": " . str_replace("?", $archiver_path . "\\" . $archiver_executable, $lang["softwarenotfound"]);
            }
        else
            {
            $result = $lang["status-fail"] . ": " . str_replace("?", stripslashes($archiver_path) . "/" . $archiver_executable, $lang["softwarenotfound"]);
            }
        }
    ?><tr><td colspan="2"><?php echo $lang["archiver_utility"] ?></td><td><b><?php echo $result?></b></td></tr><?php
    }

hook("addinstallationcheck");?>

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
