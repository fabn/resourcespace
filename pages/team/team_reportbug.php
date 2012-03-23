<?php
/**
 * Redirect to MantisBT to file a bug report and prepopulate fields on the bug
 * report page.
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 * @author Brian Adams <wreality@gmail.com>
 */
include "../../include/db.php";
/**
 * Only accessable to users with 'a' permission.
 */
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "../../include/general.php";

# Error message
$errortext = getval('errortext', '');

# ResourceSpace Build
$build = '';
if ($productversion == 'SVN'){
    $p_version = 'Trunk (SVN)';
    //Try to run svn info to determine revision number
    $out = array();
    exec('svn info ../../', $out);
    foreach($out as $outline){
        $matches = array();
        if (preg_match('/^revision: (\d+)/i', $outline, $matches)!=0){
            $build = $matches[1];
        }
    }
}
else {
    #Extract build number from productversion
    if (preg_match('/^(\d+)\.(\d+)\.(\d+)/', $productversion, $matches)!=0){
        $build = $matches[3];
    }
}

# ResourceSpace version
$p_version = $productversion == 'SVN'?'Trunk (SVN)':$productversion;

# Browser User-Agent
$custom_field_2 = $_SERVER['HTTP_USER_AGENT'];

# ImageMagick version
$custom_field_4 = "N/A"; # Should not be translated as this information is sent to the bug tracker.
if (isset($imagemagick_path)){
   $out = array();
   exec($imagemagick_path.'/convert -v', $out);
   if (isset($out[0])) {$custom_field_4 = $out[0];}
}

# ExifTool version
$exiftool_fullpath = get_utility_path("exiftool");
if ($exiftool_fullpath==false)
    {
    $custom_field_5 = "N/A"; # Should not be translated as this information is sent to the bug tracker.
    }
else
    {
    $version = run_command($exiftool_fullpath . ' -ver');
    # Set version
    $s=explode("\n",$version);
    $custom_field_5 = $s[0];
    }

# FFmpeg version
$ffmpeg_fullpath = get_utility_path("ffmpeg");
if ($ffmpeg_fullpath==false)
    {
    $custom_field_6 = "N/A"; # Should not be translated as this information is sent to the bug tracker.
    }
else
    {
    $version = run_command($ffmpeg_fullpath . " -version");
    if (strpos(strtolower($version),"ffmpeg")===false)
        {
        return str_replace("?", "$version", $lang["executionofconvertfailed"]);
        }
    # Set version
    $s=explode("\n",$version);
    $custom_field_6 = $s[0];
    }

# Server Platform
$serverversion = $_SERVER['SERVER_SOFTWARE'];

# PHP version
$custom_field_3 = phpversion();


if (isset($_REQUEST['submit']))
    {
    header ("Location: " . 
        'http://bugs.resourcespace.org/bug_report_advanced_page.php?'.
        "platform=$serverversion&".
        "product_version=$p_version&".
        "custom_field_2=$custom_field_2&".
        "custom_field_4=$custom_field_4&".
        "custom_field_6=$custom_field_6&".
        "custom_field_5=$custom_field_5&".
        "custom_field_3=$custom_field_3&".
        "build=$build&".
        "additional_info=$errortext");
    }
else
    {
    include ("../../include/header.php"); ?>
    <div class="BasicsBox"> 
        <h1><?php echo $lang["reportbug"]?></h1>
        <p><?php echo $lang['reportbug-detail']?></p>
        <table class="InfoTable">
        <tr><td><?php echo str_replace("?", "ResourceSpace", $lang["softwareversion"]); ?></td><td><?php echo $p_version?></td></tr>
        <tr><td><?php echo str_replace("?", "ResourceSpace", $lang["softwarebuild"]); ?></td><td><?php echo $build?></td></tr>
        <tr><td><?php echo $lang['serverplatform']; ?></td><td><?php echo $serverversion?></td></tr>
        <tr><td><?php echo str_replace("?", "PHP", $lang["softwareversion"]); ?></td><td><?php echo $custom_field_3?></td></tr>
        <tr><td><?php echo str_replace("?", "ExifTool", $lang["softwareversion"]); ?></td><td><?php echo $custom_field_5?></td></tr>
        <tr><td><?php echo str_replace("?", "FFmpeg", $lang["softwareversion"]); ?></td><td><?php echo $custom_field_6?></td></tr>
        <tr><td><?php echo str_replace("?", "ImageMagick", $lang["softwareversion"]); ?></td><td><?php echo $custom_field_4?></td></tr>
        <tr><td><?php echo $lang["browseruseragent"]; ?></td><td><?php echo $custom_field_2?></td></tr>
        </table>
        <br /><p><b><a href="http://bugs.resourcespace.org/login_page.php" target="_blank"><?php echo $lang['reportbug-login']?></a></b></p>
        <form method="post"><input type="submit" name="submit" value="<?php echo $lang["reportbug-preparebutton"] ?>"/></form>
    </div>
    <?php include ("../../include/footer.php");
    }
