<?php
#
# Setup page for transform plugin
#

// Do the include and authorization checking ritual.
include '../../../include/db.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}
include '../../../include/general.php';

// Specify the name of this plugin, the heading to display for the page.
$plugin_name = 'transform';
$page_heading = $lang['transform_configuration'];

// Build the $page_def array of descriptions of each configuration variable the plugin uses.
#$page_def[] = config_add_text_input('cropper_default_target_format', 'Default Target Format');
$page_def[] = config_add_boolean_select('cropper_debug', $lang['cropper_debug']);
$page_def[] = config_add_text_list_input('cropper_formatarray', $lang['output_formats']);
$page_def[] = config_add_text_list_input('cropper_allowed_extensions', $lang['input_formats']);
#$page_def[] = config_add_text_input('cropper_default_target_format', 'Default Target Format');
#$page_def[] = config_add_boolean_select('cropper_cropsize', 'cropper_cropsize');
$page_def[] = config_add_boolean_select('cropper_custom_filename', $lang['custom_filename']);
#$page_def[] = config_add_boolean_select('cropper_use_filename_as_title', 'Use Filename as Title');
#$page_def[] = config_add_boolean_select('cropper_allow_scale_up', 'cropper_allow_scale_up');
$page_def[] = config_add_boolean_select('cropper_rotation', $lang['allow_rotation']);
$page_def[] = config_add_boolean_select('cropper_transform_original', $lang['allow_transform_original']);
$page_def[] = config_add_boolean_select('cropper_use_repage', $lang['use_repage']);
#$page_def[] = config_add_boolean_select('cropper_jpeg_rgb', 'cropper_jpeg_rgb');
$page_def[] = config_add_boolean_select('cropper_enable_batch', $lang['enable_batch_transform']);
// Commented out lines above that either don't seem to work or I'm unsure how to test

// Do the page generation ritual
$upload_status = config_gen_setup_post($page_def, $plugin_name);
include '../../../include/header.php';
config_gen_setup_html($page_def, $plugin_name, $upload_status, $page_heading);
include '../../../include/footer.php';
