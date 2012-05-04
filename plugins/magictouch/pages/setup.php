<?php
#
# Setup page for Magictouch plugin
#

// Do the include and authorization checking ritual.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';

// Specify the name of this plugin and the heading to display for the page.
$plugin_name = 'magictouch';
$plugin_page_heading = $lang['magictouch_configuration'];

// Build the $page_def array of descriptions of each configuration variable the plugin uses.
// Each element of $page_def describes one configuration variable. Each description is 
// created by one of the config_add_xxxx helper functions. See their definitions and
// descriptions in include/plugin_functions for more information.

$page_def[] = config_add_text_input('magictouch_account_id', $lang['magic_touch_key']);
$page_def[] = config_add_single_select('magictouch_secure', $lang['https'], array('https', 'http'), false);
$page_def[] = config_add_text_list_input('magictouch_ext_exclude', $lang['extensions_to_exclude']);
$page_def[] = config_add_multi_rtype_select('magictouch_rt_exclude', $lang['resource_types_to_exclude']);
$page_def[] = config_add_text_list_input('magictouch_view_page_sizes', $lang['view_page_sizes']);
$page_def[] = config_add_text_list_input('magictouch_preview_page_sizes', $lang['preview_page_sizes']);


// Do the page generation ritual.
$upload_status = config_gen_setup_post($page_def, $plugin_name);
include '../../../include/header.php';

$frontm = '';
if ($magictouch_account_id==''){
	$frontm .= $lang['get-magictouch'];
	$frontm .= '<br /><br />';
	$frontm .= $lang['configure-account-id-and-register-domain'];
	$frontm .= '<br /><br />';
}
config_gen_setup_html($page_def, $plugin_name, $upload_status, $plugin_page_heading, $frontm);
include '../../../include/footer.php';