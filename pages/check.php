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
?><tr><td colspan="2"><?php echo $lang["writeaccesstofilestore"] ?></td><td><b><?php echo $result?></b></td></tr><?php

# Check write access to homeanim (if transform plugin is installed)
if (in_array("transform",$plugins)){
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
?><tr><td colspan="2"><?php echo $lang["blockedbrowsingoffilestore"] ?></td><td><b><?php echo $result?></b></td></tr><?php

# Check ImageMagick/GraphicsMagick
display_utility_status("im-convert");

# Check FFmpeg
display_utility_status("ffmpeg");

# Check Ghostscript
display_utility_status("ghostscript");

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

# Check ExifTool
display_utility_status("exiftool");

# Check archiver
if ($collection_download || isset($zipcommand)) # Only check if it is going to be used.
    {
    $archiver_fullpath = get_utility_path("archiver", $path);

    if ($path==null && !isset($zipcommand))
        {
        $result = $lang["status-notinstalled"];
        }
    elseif ($collection_download && $archiver_fullpath!=false)
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
        $result = $lang["status-fail"] . ": " . str_replace("?", $path, $lang["softwarenotfound"]);
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

function display_utility_status($utilityname)
    {
    global $lang;
    $utility = get_utility_version($utilityname);

    if ($utility["success"]==true)
        {
        $result = $lang["status-ok"];
        }
    else
        {
        $result = $utility["error"];
        }

    ?><tr><td <?php if ($utility["success"]==false) { ?>colspan="2"<?php } ?>><?php echo $utility["name"] ?></td>
    <?php if ($utility["success"]==true) { ?><td><?php echo $utility["version"] ?></td><?php } ?>
    <td><b><?php echo $result?></b></td></tr><?php
    }

function get_utility_displayname($utilityname)
    {

    # Define the display name of a utility.
    switch (strtolower($utilityname))
        {
        case "im-convert":
           return "ImageMagick/GraphicsMagick";
           break;
        case "ghostscript":
            return "Ghostscript";
            break;
        case "ffmpeg":
            return "FFmpeg";
            break;
        case "exiftool":
            return "ExifTool";
            break;
        case "antiword":
            return "Antiword";
            break;
        case "pdftotext":
            return "pdftotext";
            break;
        case "blender":
            return "Blender";
            break;
        case "archiver":
            return "Archiver";
            break;
        default:
            return $utilityname;
        }
    }

function get_utility_version($utilityname)
    {
    global $lang;

    # Get utility path.
    $utility_fullpath = get_utility_path($utilityname, $path);

    # Get utility display name.
    $name = get_utility_displayname($utilityname);

    # Check path.
    if ($path==null)
        {
        # There was no complete path to check - the utility is not installed.
        $error_msg = $lang["status-notinstalled"];
        return array("name" => $name, "version" => "", "success" => false, "error" => $error_msg);
        }
    if ($utility_fullpath==false)
        {
        # There was a path but it was incorrect - the utility couldn't be found.
        $error_msg = $lang["status-fail"] . ":<br>" . str_replace("?", $path, $lang["softwarenotfound"]);
        return array("name" => $name, "version" => "", "success" => false, "error" => $error_msg);
        }

    # Look up the argument to use to get the version.
    switch (strtolower($utilityname))
        {
        case "exiftool":
            $version_argument = "-ver";
            break;
        default:
            $version_argument = "-version";
        }

    # Check execution and find out version.
    $version_command = $utility_fullpath . " " . $version_argument;
    $version = run_command($version_command);

    switch (strtolower($utilityname))
        {
        case "im-convert":
           if (strpos($version, "ImageMagick")!==false) {$name = "ImageMagick";}
           if (strpos($version, "GraphicsMagick")!==false) {$name = "GraphicsMagick";}
           if ($name=="ImageMagick" || $name=="GraphicsMagick") {$expected = true;}
           else {$expected = false;}
           break;
        case "ghostscript":
            if (strpos(strtolower($version), "ghostscript")===false) {$expected = false;}
            else {$expected = true;}
            break;
        case "ffmpeg":
            if (strpos(strtolower($version), "ffmpeg")===false) {$expected = false;}
            else {$expected = true;}
            break;
        case "exiftool":
            if (preg_match("/^([0-9]+)+\.([0-9]+)$/", $version)==false) {$expected = false;} # E.g. 8.84
            else {$expected = true;}
            break;
        }

    if ($expected==false)
        {
        # There was a correct path but the version check failed - unexpected output when executing the command.
        $error_msg = $lang["status-fail"] . ":<br>" . str_replace(array("%command", "%output"), array($version_command, $version), $lang["execution_failed"]);
        return array("name" => $name, "version" => "", "success" => false, "error" => $error_msg);
        }
    else    
        {
        # There was a working path and the output was the expected - the version is returned.
        $s = explode("\n", $version);
        return array("name" => $name, "version" => $s[0], "success" => true, "error" => "");
        }
    }
?>
