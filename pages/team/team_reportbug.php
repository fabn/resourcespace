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

$errortext = getval('errortext', '');
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
$p_version = $productversion == 'SVN'?'Trunk (SVN)':$productversion;
$custom_field_2 = $_SERVER['HTTP_USER_AGENT'];
$custom_field_4 = '';
if (isset($imagemagick_path)){
   $out = array();
   exec($imagemagick_path.'/convert -v', $out);
   $custom_field_4 = $out[0];
}
$custom_field_5 = '';
if (isset($exiftool_path)){
    $out = array();
    exec($exiftool_path.'/exiftool -ver', $out);
    $custom_field_5 = $out[0];
}
$custom_field_6 = '';
if (isset($ffmpeg_path)){
    $out = array();
    exec($ffmpeg_path.'/ffmpeg -version', $out);
    $custom_field_6 = $out[0];
}

$serverversion = $_SERVER['SERVER_SOFTWARE'];
$custom_field_3 = phpversion();
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
         
