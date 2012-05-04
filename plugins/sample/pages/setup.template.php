<?php
#
# Setup page template
#
# You can 'crib' from this code to easily create a setup.php file for a new plugin.
#

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';

// Specify the name of this plugin, the heading to display for the page and the 
// optional introductory text. Set $page_intro to "" for no intro text
// Change to match your plugin.
$plugin_name = 'plugin_name_goes_here';                        // Usually a string literal
$page_heading = $lang['plugin_name_plugin_heading_goes_here']; // Usually a $lang[] string
$page_intro = '<p>html_content_goes_here</p>';                 // Usually either a $lang[] string or ''

// Build the $page_def array of descriptions of each configuration variable the plugin uses.
// Each element of $page_def describes one configuration variable. Each description is 
// created by one of the config_add_xxxx helper functions. See their definitions and
// descriptions in include/plugin_functions for more information.

$page_def[] = config_add_xxxx(...);
$page_def[] = config_add_yyyy(....);
...
// Do the page generation ritual -- don't change this section.
$upload_status = config_gen_setup_post($page_def, $plugin_name);
include '../../../include/header.php';
config_gen_setup_html($page_def, $plugin_name, $upload_status, $page_heading, $page_intro);
include '../../../include/footer.php';