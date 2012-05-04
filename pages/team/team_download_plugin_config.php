<?php
/**
 *  Download plugin config file in json form as <plugin-name>.rsc
 *
 *	Invoke by doing
 *
 *		pages/team/team_download_plugin_config.php?pin=<plugin-name>
 *
 *		where <plugin-name> is the name of the plugin
 *
 */
include '../../include/db.php';
include '../../include/authenticate.php'; 
if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}

$name=getvalescaped('pin','');
if ($name=='') {exit($lang['error-permissiondenied']);}

@ob_end_clean(); //turn off output buffering

// required for IE, otherwise Content-Disposition may be ignored
if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
 
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $name . '.rsc"');
header('Content-Transfer-Encoding: binary');
 
 // Make the download non-cacheable
header('Cache-control: private');
header('Pragma: private');
header('Expires: Thu, 01 Dec 1994 16:00:00 GMT');

// Dump file identifier
echo chr(0xEF) . chr(0xBB) . chr(0xBF) . '{"ResourceSpacePlugin":"' . $name . "\"}\n";
// Dump the config
$mysql_vq = $mysql_verbatim_queries;
$mysql_verbatim_queries = true;
$config = sql_value("SELECT config_json as value from plugins where name='$name'",'');
$mysql_verbatim_queries = $mysql_vq;
if (!isset($mysql_charset))
    {
    $config = iconv('ISO-8859-1', 'UTF-8', $config);
    }
echo $config;
exit();

?>