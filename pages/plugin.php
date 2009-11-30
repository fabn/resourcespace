<?php
/***
 * plugin.php - Maps requests to plugin pages to requested plugin.
 * 
 * @package ResourceSpace
 * @subpackage Plugins
 *
 ***/

# Define this page as an acceptable entry point.
define('RESOURCESPACE', true);

include '../include/db.php';
include '../include/general.php';

$query = explode('&', $_SERVER['QUERY_STRING']);
$plugin_query = explode('/', $query[0]);

if (!is_plugin_activated(mysql_real_escape_string($plugin_query[0]))){
    die ('Plugin does not exist or is not enabled');
}
if (isset($plugin_query[1])){
    if(preg_match('/[\/]/', $plugin_query[1])) die ('Invalid plugin page.');
    $page_path = "../plugins/{$plugin_query[0]}/pages/{$plugin_query[1]}.php";
    if(file_exists($page_path)){
        include $page_path;
    }
    else {
        die ('Plugin page not found.');
    }
}