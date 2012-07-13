<?php
#
# Annotate setup page
#

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';

// Specify the name of this plugin and the heading to display for the page.
$plugin_name = 'annotate';
$plugin_page_heading = $lang['annotate_configuration'];

// Build the $page_def array of descriptions of each configuration variable the plugin uses.

$page_def[] = config_add_text_list_input('annotate_ext_exclude', $lang['extensions_to_exclude']);
$page_def[] = config_add_multi_rtype_select('annotate_rt_exclude', $lang['resource_types_to_exclude']);
$page_def[] = config_add_single_select('annotate_font', $lang['annotate_font'], array('helvetica', 'dejavusanscondensed'), false);
$page_def[] = config_add_boolean_select('annotate_debug', $lang['annotatedebug']);
$page_def[] = config_add_boolean_select('annotate_public_view', $lang['annotate_public_view']);
$page_def[] = config_add_boolean_select('annotate_show_author', $lang['annotate_show_author']);
$page_def[] = config_add_boolean_select('annotate_pdf_output', $lang["annotate_pdf_output"]);
$page_def[] = config_add_boolean_select('annotate_pdf_output_only_annotated', $lang["annotate_pdf_output_only_annotated"]);

// Do the page generation ritual -- don't change this section.
$upload_status = config_gen_setup_post($page_def, $plugin_name);
include '../../../include/header.php';
config_gen_setup_html($page_def, $plugin_name, $upload_status, $plugin_page_heading);
include '../../../include/footer.php';
