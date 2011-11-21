<?php
/**
 * Upgrade script.
 * 
 * Manually called upon upgrading ResourceSpace to add new values to the 
 * configuration database.
 * 
 * @package ResourceSpace
 * @subpackage Pages_Misc
 * @author Brian Adams <wreality@gmail.com>
 */
define('UPGRADING', true);
$inc_files = get_included_files();
$direct = false;
if (strpos($inc_files[0], 'upgrade.php')){ 
    $direct = true;
    include_once dirname(__FILE__)."/../include/db.php"; 
    include_once dirname(__FILE__)."/../include/general.php";
}

if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
  $cli = true;
} else {
  $cli = false;
}
function default_module_config_key($module_name, $module_key, $def_value){
    global $direct, $cli;
    $output = '';
    $output .= "Checking: $module_name: $module_key ";
    if (get_module_config_key($module_name, $module_key)!==null){
        $output .= '(OK)';
    }
    else {
        set_module_config_key($module_name, $module_key, $def_value);
        $output .= '(UPDATED)';
    }
    $output .= $cli?"\r\n":'<br />';
    if ($direct) echo $output;
    
}

$dbversion = get_module_config_key('general', 'dbversion');
if ($productversion != 'SVN'){
    $matches = array();
    preg_match('/\.(?P<revision>\d+)$/', $productversion, $matches);
    $scriptversion = $matches[1];
}
else {
    $scriptversion = $productversion;
}
if (($scriptversion > $dbversion) || $dbversion=='SVN' || $scriptversion == 'SVN' || $dbversion==null ){
if ($direct){
    ?>

<h1>Upgrading to ResourceSpace version <?php echo $productversion; ?> from version <?php echo $dbversion; ?></h1>
<h2>Checking configuration keys.</h2>
<?php }
# General Module -- General module keys are presented as global variables to the 
# application.  Existing config options are entered in the database using 
# ternaries to support backwards compatability.  Future options do not need this
# check as config.default.php will be depreciated.  When possibile, use:
# get_module_config_key('<modulename>', '<keyname>') rather than adding a key to
# the general section.
default_module_config_key('general', 'applicationname', isset($applicationname)?$applicationname:'ResourceSpace');
default_module_config_key('general', 'defaultlanguage', isset($defaultlanguage)?$defaultlanguage:'en');
default_module_config_key('general', 'disable_languages', isset($disable_languages)?$disable_languages:false);
default_module_config_key('general', 'ftp_server', isset($ftp_server)?$ftp_server:"my.ftp.server");
default_module_config_key('general', 'ftp_username', isset($ftp_username)?$ftp_username:"my_username");
default_module_config_key('general', 'ftp_password', isset($ftp_password)?$ftp_password:"my_password");
default_module_config_key('general', 'email_from', isset($email_from)?$email_from:'resourcespace@my.site'); 
default_module_config_key('general', 'email_notify', isset($email_notify)?$email_notify:'resourcespace@my.site'); 
default_module_config_key('general', 'spider_password', isset($spider_password)?$spider_password:'TBTT6FD'); 
default_module_config_key('general', 'scramble_key', isset($scramble_key)?$scramble_key:'abcdef123'); 
default_module_config_key('general', 'send_statistics', isset($send_statistics)?$send_statistics:'1'); 
default_module_config_key('general', 'config_windows', isset($config_windows)?$config_windows:''); 
default_module_config_key('general', 'css_reload_key', isset($css_reload_key)?$css_reload_key:'26'); 
default_module_config_key('general', 'ftp_defaultfolder', isset($ftp_defaultfolder)?$ftp_defaultfolder:'temp/'); 
default_module_config_key('general', 'allow_password_change', isset($allow_password_change)?$allow_password_change:'1'); 
default_module_config_key('general', 'noadd', isset($noadd)?$noadd:'Array'); 
default_module_config_key('general', 'suggest_threshold', isset($suggest_threshold)?$suggest_threshold:'-1'); 
default_module_config_key('general', 'max_results', isset($max_results)?$max_results:'50000'); 
default_module_config_key('general', 'minyear', isset($minyear)?$minyear:'1980'); 
default_module_config_key('general', 'homeanim_folder', isset($homeanim_folder)?$homeanim_folder:'gfx/homeanim/gfx'); 
default_module_config_key('general', 'iptc_expectedchars', isset($iptc_expectedchars)?$iptc_expectedchars:'Ã¦Ã¸Ã¥Ã†Ã˜Ã…'); 
default_module_config_key('general', 'exif_comment', isset($exif_comment)?$exif_comment:'18'); 
default_module_config_key('general', 'exif_model', isset($exif_model)?$exif_model:'52'); 
default_module_config_key('general', 'exif_date', isset($exif_date)?$exif_date:'12'); 
default_module_config_key('general', 'metadata_report', isset($metadata_report)?$metadata_report:''); 
default_module_config_key('general', 'restricted_metadata_report', isset($restricted_metadata_report)?$restricted_metadata_report:''); 
default_module_config_key('general', 'exiftool_resolution_calc', isset($exiftool_resolution_calc)?$exiftool_resolution_calc:''); 
default_module_config_key('general', 'exiftool_remove_existing', isset($exiftool_remove_existing)?$exiftool_remove_existing:''); 
default_module_config_key('general', 'exiftool_write', isset($exiftool_write)?$exiftool_write:'1'); 
default_module_config_key('general', 'exiftool_no_process', isset($exiftool_no_process)?$exiftool_no_process:'Array'); 
default_module_config_key('general', 'filename_field', isset($filename_field)?$filename_field:'51'); 
default_module_config_key('general', 'imagemagick_preserve_profiles', isset($imagemagick_preserve_profiles)?$imagemagick_preserve_profiles:''); 
default_module_config_key('general', 'imagemagick_quality', isset($imagemagick_quality)?$imagemagick_quality:'90'); 
default_module_config_key('general', 'photoshop_thumb_extract', isset($photoshop_thumb_extract)?$photoshop_thumb_extract:''); 
default_module_config_key('general', 'cr2_thumb_extract', isset($cr2_thumb_extract)?$cr2_thumb_extract:''); 
default_module_config_key('general', 'nef_thumb_extract', isset($nef_thumb_extract)?$nef_thumb_extract:''); 
default_module_config_key('general', 'imagemagick_calculate_sizes', isset($imagemagick_calculate_sizes)?$imagemagick_calculate_sizes:''); 
default_module_config_key('general', 'pdf_pages', isset($pdf_pages)?$pdf_pages:'30'); 
default_module_config_key('general', 'ffmpeg_preview', isset($ffmpeg_preview)?$ffmpeg_preview:'1'); 
default_module_config_key('general', 'ffmpeg_preview_seconds', isset($ffmpeg_preview_seconds)?$ffmpeg_preview_seconds:'20'); 
default_module_config_key('general', 'ffmpeg_preview_extension', isset($ffmpeg_preview_extension)?$ffmpeg_preview_extension:'flv'); 
default_module_config_key('general', 'ffmpeg_preview_min_width', isset($ffmpeg_preview_min_width)?$ffmpeg_preview_min_width:'32'); 
default_module_config_key('general', 'ffmpeg_preview_min_height', isset($ffmpeg_preview_min_height)?$ffmpeg_preview_min_height:'18'); 
default_module_config_key('general', 'ffmpeg_preview_max_width', isset($ffmpeg_preview_max_width)?$ffmpeg_preview_max_width:'480'); 
default_module_config_key('general', 'ffmpeg_preview_max_height', isset($ffmpeg_preview_max_height)?$ffmpeg_preview_max_height:'270'); 
default_module_config_key('general', 'ffmpeg_preview_options', isset($ffmpeg_preview_options)?$ffmpeg_preview_options:'-f flv -ar 22050 -b 650k -ab 32 -ac 1'); 
default_module_config_key('general', 'ffmpeg_preview_force', isset($ffmpeg_preview_force)?$ffmpeg_preview_force:''); 
default_module_config_key('general', 'ffmpeg_preview_async', isset($ffmpeg_preview_async)?$ffmpeg_preview_async:''); 
default_module_config_key('general', 'allow_account_request', isset($allow_account_request)?$allow_account_request:'1'); 
default_module_config_key('general', 'allow_password_reset', isset($allow_password_reset)?$allow_password_reset:'1'); 
default_module_config_key('general', 'highlightkeywords', isset($highlightkeywords)?$highlightkeywords:'1'); 
default_module_config_key('general', 'searchbyday', isset($searchbyday)?$searchbyday:''); 
default_module_config_key('general', 'restricted_full_download', isset($restricted_full_download)?$restricted_full_download:''); 
default_module_config_key('general', 'archive_search', isset($archive_search)?$archive_search:''); 
default_module_config_key('general', 'research_request', isset($research_request)?$research_request:''); 
default_module_config_key('general', 'country_search', isset($country_search)?$country_search:''); 
default_module_config_key('general', 'resourceid_simple_search', isset($resourceid_simple_search)?$resourceid_simple_search:''); 
default_module_config_key('general', 'colour_sort', isset($colour_sort)?$colour_sort:'1'); 
default_module_config_key('general', 'title_sort', isset($title_sort)?$title_sort:''); 
default_module_config_key('general', 'country_sort', isset($country_sort)?$country_sort:''); 
default_module_config_key('general', 'original_filename_sort', isset($original_filename_sort)?$original_filename_sort:''); 
default_module_config_key('general', 'default_sort', isset($default_sort)?$default_sort:'relevance'); 
default_module_config_key('general', 'enable_themes', isset($enable_themes)?$enable_themes:'1'); 
default_module_config_key('general', 'use_theme_as_home', isset($use_theme_as_home)?$use_theme_as_home:''); 
default_module_config_key('general', 'theme_images', isset($theme_images)?$theme_images:'1'); 
default_module_config_key('general', 'theme_images_number', isset($theme_images_number)?$theme_images_number:'1'); 
default_module_config_key('general', 'theme_images_align_right', isset($theme_images_align_right)?$theme_images_align_right:''); 
default_module_config_key('general', 'theme_category_levels', isset($theme_category_levels)?$theme_category_levels:'1'); 
default_module_config_key('general', 'advancedsearch_disabled', isset($advancedsearch_disabled)?$advancedsearch_disabled:''); 
default_module_config_key('general', 'home_advancedsearch', isset($home_advancedsearch)?$home_advancedsearch:''); 
default_module_config_key('general', 'advanced_search_nav', isset($advanced_search_nav)?$advanced_search_nav:''); 
default_module_config_key('general', 'home_mycontributions', isset($home_mycontributions)?$home_mycontributions:''); 
default_module_config_key('general', 'disable_searchresults', isset($disable_searchresults)?$disable_searchresults:''); 
default_module_config_key('general', 'recent_link', isset($recent_link)?$recent_link:'1'); 
default_module_config_key('general', 'help_link', isset($help_link)?$help_link:'1'); 
default_module_config_key('general', 'search_results_link', isset($search_results_link)?$search_results_link:'1'); 
default_module_config_key('general', 'view_new_material', isset($view_new_material)?$view_new_material:''); 
default_module_config_key('general', 'mycollections_link', isset($mycollections_link)?$mycollections_link:''); 
default_module_config_key('general', 'mycontributions_link', isset($mycontributions_link)?$mycontributions_link:''); 
default_module_config_key('general', 'terms_download', isset($terms_download)?$terms_download:''); 
default_module_config_key('general', 'terms_login', isset($terms_login)?$terms_login:''); 
default_module_config_key('general', 'thumbs_default', isset($thumbs_default)?$thumbs_default:'show'); 
default_module_config_key('general', 'autoshow_thumbs', isset($autoshow_thumbs)?$autoshow_thumbs:''); 
default_module_config_key('general', 'smallthumbs', isset($smallthumbs)?$smallthumbs:'1'); 
default_module_config_key('general', 'max_collection_thumbs', isset($max_collection_thumbs)?$max_collection_thumbs:'150'); 
default_module_config_key('general', 'results_display_array', isset($results_display_array)?$results_display_array:array(24,48,72,120,240)); 
default_module_config_key('general', 'default_perpage', isset($default_perpage)?$default_perpage:'48'); 
default_module_config_key('general', 'groupuploadfolders', isset($groupuploadfolders)?$groupuploadfolders:''); 
default_module_config_key('general', 'orderbyrating', isset($orderbyrating)?$orderbyrating:''); 
default_module_config_key('general', 'zipped_collection_textfile', isset($zipped_collection_textfile)?$zipped_collection_textfile:''); 
default_module_config_key('general', 'speedtagging', isset($speedtagging)?$speedtagging:''); 
default_module_config_key('general', 'speedtaggingfield', isset($speedtaggingfield)?$speedtaggingfield:'1'); 
default_module_config_key('general', 'videotypes', isset($videotypes)?$videotypes:array(3)); 
default_module_config_key('general', 'defaulttheme', isset($defaulttheme)?$defaulttheme:'greyblu'); 
default_module_config_key('general', 'available_themes', isset($available_themes)?$available_themes:array("greyblu","black","whitegry")); 
default_module_config_key('general', 'plugins', isset($plugins)?$plugins:array()); 
default_module_config_key('general', 'infobox', isset($infobox)?$infobox:'1'); 
default_module_config_key('general', 'infobox_fields', isset($infobox_fields)?$infobox_fields:array(18,10,29,53)); 
default_module_config_key('general', 'infobox_display_resource_id', isset($infobox_display_resource_id)?$infobox_display_resource_id:'1'); 
default_module_config_key('general', 'infobox_display_resource_icon', isset($infobox_display_resource_icon)?$infobox_display_resource_icon:'1'); 
default_module_config_key('general', 'collection_reorder_caption', isset($collection_reorder_caption)?$collection_reorder_caption:''); 
default_module_config_key('general', 'email_footer', isset($email_footer)?$email_footer:''); 
default_module_config_key('general', 'contact_sheet', isset($contact_sheet)?$contact_sheet:'1'); 
default_module_config_key('general', 'contact_sheet_resource', isset($contact_sheet_resource)?$contact_sheet_resource:''); 
default_module_config_key('general', 'contact_sheet_previews', isset($contact_sheet_previews)?$contact_sheet_previews:'1'); 
default_module_config_key('general', 'contact_sheet_font', isset($contact_sheet_font)?$contact_sheet_font:'helvetica'); 
default_module_config_key('general', 'contact_sheet_unicode_filenames', isset($contact_sheet_unicode_filenames)?$contact_sheet_unicode_filenames:'1'); 
default_module_config_key('general', 'print_contact_title', isset($print_contact_title)?$print_contact_title:''); 
default_module_config_key('general', 'titlefontsize', isset($titlefontsize)?$titlefontsize:'10'); 
default_module_config_key('general', 'refnumberfontsize', isset($refnumberfontsize)?$refnumberfontsize:'8'); 
default_module_config_key('general', 'config_sheetlist_fields', isset($config_sheetlist_fields)?$config_sheetlist_fields:array(8)); 
default_module_config_key('general', 'usercontribute_swfupload', isset($usercontribute_swfupload)?$usercontribute_swfupload:'1'); 
default_module_config_key('general', 'usercontribute_javaupload', isset($usercontribute_javaupload)?$usercontribute_javaupload:''); 
default_module_config_key('general', 'show_related_themes', isset($show_related_themes)?$show_related_themes:'1'); 
default_module_config_key('general', 'disable_quoted_printable_enc', isset($disable_quoted_printable_enc)?$disable_quoted_printable_enc:''); 
default_module_config_key('general', 'basic_simple_search', isset($basic_simple_search)?$basic_simple_search:''); 
default_module_config_key('general', 'hide_main_simple_search', isset($hide_main_simple_search)?$hide_main_simple_search:''); 
default_module_config_key('general', 'home_themeheaders', isset($home_themeheaders)?$home_themeheaders:''); 
default_module_config_key('general', 'home_themes', isset($home_themes)?$home_themes:'1'); 
default_module_config_key('general', 'home_mycollections', isset($home_mycollections)?$home_mycollections:'1'); 
default_module_config_key('general', 'home_helpadvice', isset($home_helpadvice)?$home_helpadvice:'1'); 
default_module_config_key('general', 'original_filenames_when_downloading', isset($original_filenames_when_downloading)?$original_filenames_when_downloading:'1'); 
default_module_config_key('general', 'prefix_resource_id_to_filename', isset($prefix_resource_id_to_filename)?$prefix_resource_id_to_filename:'1'); 
default_module_config_key('general', 'prefix_filename_string', isset($prefix_filename_string)?$prefix_filename_string:'RS'); 
default_module_config_key('general', 'flag_new_themes', isset($flag_new_themes)?$flag_new_themes:'1'); 
default_module_config_key('general', 'file_checksums', isset($file_checksums)?$file_checksums:''); 
default_module_config_key('general', 'default_group', isset($default_group)?$default_group:'2'); 
default_module_config_key('general', 'custom_access', isset($custom_access)?$custom_access:'1'); 
default_module_config_key('general', 'config_search_for_number', isset($config_search_for_number)?$config_search_for_number:'1'); 
default_module_config_key('general', 'save_as', isset($save_as)?$save_as:''); 
default_module_config_key('general', 'allow_share', isset($allow_share)?$allow_share:'1'); 
default_module_config_key('general', 'restricted_share', isset($restricted_share)?$restricted_share:''); 
default_module_config_key('general', 'autocomplete_search', isset($autocomplete_search)?$autocomplete_search:'1'); 
default_module_config_key('general', 'autocomplete_search_items', isset($autocomplete_search_items)?$autocomplete_search_items:'15'); 
default_module_config_key('general', 'auto_order_checkbox', isset($auto_order_checkbox)?$auto_order_checkbox:'1'); 
default_module_config_key('general', 'checkbox_ordered_vertically', isset($checkbox_ordered_vertically)?$checkbox_ordered_vertically:'1'); 
default_module_config_key('general', 'enable_add_collection_on_upload', isset($enable_add_collection_on_upload)?$enable_add_collection_on_upload:'1'); 
default_module_config_key('general', 'upload_add_to_new_collection', isset($upload_add_to_new_collection)?$upload_add_to_new_collection:'1'); 
default_module_config_key('general', 'enable_copy_data_from', isset($enable_copy_data_from)?$enable_copy_data_from:'1'); 
default_module_config_key('general', 'always_record_resource_creator', isset($always_record_resource_creator)?$always_record_resource_creator:''); 
default_module_config_key('general', 'enable_related_resources', isset($enable_related_resources)?$enable_related_resources:'1'); 
default_module_config_key('general', 'allow_keep_logged_in', isset($allow_keep_logged_in)?$allow_keep_logged_in:'1'); 
default_module_config_key('general', 'show_user_contributed_resources', isset($show_user_contributed_resources)?$show_user_contributed_resources:'1'); 
default_module_config_key('general', 'contact_link', isset($contact_link)?$contact_link:'1'); 
default_module_config_key('general', 'about_link', isset($about_link)?$about_link:'1'); 
default_module_config_key('general', 'reset_date_upload_template', isset($reset_date_upload_template)?$reset_date_upload_template:'1'); 
default_module_config_key('general', 'reset_date_field', isset($reset_date_field)?$reset_date_field:'12'); 
default_module_config_key('general', 'blank_edit_template', isset($blank_edit_template)?$blank_edit_template:''); 
default_module_config_key('general', 'show_expiry_warning', isset($show_expiry_warning)?$show_expiry_warning:'1'); 
default_module_config_key('general', 'collection_resize', isset($collection_resize)?$collection_resize:''); 
default_module_config_key('general', 'enable_collection_copy', isset($enable_collection_copy)?$enable_collection_copy:'1'); 
default_module_config_key('general', 'default_res_types', isset($default_res_types)?$default_res_types:''); 
default_module_config_key('general', 'show_resourceid', isset($show_resourceid)?$show_resourceid:'1'); 
default_module_config_key('general', 'show_access_field', isset($show_access_field)?$show_access_field:'1'); 
default_module_config_key('general', 'show_contributed_by', isset($show_contributed_by)?$show_contributed_by:'1'); 
default_module_config_key('general', 'show_extension_in_search', isset($show_extension_in_search)?$show_extension_in_search:''); 
default_module_config_key('general', 'category_tree_open', isset($category_tree_open)?$category_tree_open:''); 
default_module_config_key('general', 'session_length', isset($session_length)?$session_length:'30'); 
default_module_config_key('general', 'session_autologout', isset($session_autologout)?$session_autologout:''); 
default_module_config_key('general', 'login_autocomplete', isset($login_autocomplete)?$login_autocomplete:'1'); 
default_module_config_key('general', 'login_remember_username', isset($login_remember_username)?$login_remember_username:'1'); 
default_module_config_key('general', 'password_min_length', isset($password_min_length)?$password_min_length:'7'); 
default_module_config_key('general', 'password_min_alpha', isset($password_min_alpha)?$password_min_alpha:'1'); 
default_module_config_key('general', 'password_min_numeric', isset($password_min_numeric)?$password_min_numeric:'1'); 
default_module_config_key('general', 'password_min_uppercase', isset($password_min_uppercase)?$password_min_uppercase:'0'); 
default_module_config_key('general', 'password_min_special', isset($password_min_special)?$password_min_special:'0'); 
default_module_config_key('general', 'password_expiry', isset($password_expiry)?$password_expiry:'0'); 
default_module_config_key('general', 'max_login_attempts_per_ip', isset($max_login_attempts_per_ip)?$max_login_attempts_per_ip:'20'); 
default_module_config_key('general', 'max_login_attempts_per_username', isset($max_login_attempts_per_username)?$max_login_attempts_per_username:'5'); 
default_module_config_key('general', 'max_login_attempts_wait_minutes', isset($max_login_attempts_wait_minutes)?$max_login_attempts_wait_minutes:'10'); 
default_module_config_key('general', 'imperial_measurements', isset($imperial_measurements)?$imperial_measurements:''); 
default_module_config_key('general', 'default_resource_type', isset($default_resource_type)?$default_resource_type:'1'); 
default_module_config_key('general', 'ip_forwarded_for', isset($ip_forwarded_for)?$ip_forwarded_for:''); 
default_module_config_key('general', 'extracted_text_field', isset($extracted_text_field)?$extracted_text_field:'72'); 
default_module_config_key('general', 'pending_review_visible_to_all', isset($pending_review_visible_to_all)?$pending_review_visible_to_all:''); 
default_module_config_key('general', 'user_rating', isset($user_rating)?$user_rating:''); 
default_module_config_key('general', 'enable_public_collections', isset($enable_public_collections)?$enable_public_collections:'1'); 
default_module_config_key('general', 'registration_group_select', isset($registration_group_select)?$registration_group_select:''); 
default_module_config_key('general', 'notify_user_contributed_submitted', isset($notify_user_contributed_submitted)?$notify_user_contributed_submitted:'1'); 
default_module_config_key('general', 'notify_user_contributed_unsubmitted', isset($notify_user_contributed_unsubmitted)?$notify_user_contributed_unsubmitted:''); 
default_module_config_key('general', 'feedback_resource_select', isset($feedback_resource_select)?$feedback_resource_select:''); 
default_module_config_key('general', 'log_resource_views', isset($log_resource_views)?$log_resource_views:''); 
default_module_config_key('general', 'banned_extensions', isset($banned_extensions)?$banned_extensions:array("php","cgi","pl","exe","asp","jsp")); 
default_module_config_key('general', 'show_status_and_access_on_upload', isset($show_status_and_access_on_upload)?$show_status_and_access_on_upload:''); 
default_module_config_key('general', 'php_time_limit', isset($php_time_limit)?$php_time_limit:'300'); 
default_module_config_key('general', 'flv_preview_downloadable', isset($flv_preview_downloadable)?$flv_preview_downloadable:''); 
default_module_config_key('general', 'default_user_select', isset($default_user_select)?$default_user_select:''); 
default_module_config_key('general', 'simple_search_dropdown_filtering', isset($simple_search_dropdown_filtering)?$simple_search_dropdown_filtering:''); 
default_module_config_key('general', 'search_includes_themes', isset($search_includes_themes)?$search_includes_themes:'1'); 
default_module_config_key('general', 'search_includes_public_collections', isset($search_includes_public_collections)?$search_includes_public_collections:''); 
default_module_config_key('general', 'index_collection_titles', isset($index_collection_titles)?$index_collection_titles:''); 
default_module_config_key('general', 'default_home_page', isset($default_home_page)?$default_home_page:'home.php');  
default_module_config_key('general', 'config_trimchars', isset($config_trimchars)?$config_trimchars:''); 
default_module_config_key('general', 'global_permissions', isset($global_permissions)?$global_permissions:'F51,F52'); 
default_module_config_key('general', 'user_account_auto_creation', isset($user_account_auto_creation)?$user_account_auto_creation:''); 
default_module_config_key('general', 'user_account_auto_creation_usergroup', isset($user_account_auto_creation_usergroup)?$user_account_auto_creation_usergroup:'2'); 
default_module_config_key('general', 'edit_large_preview', isset($edit_large_preview)?$edit_large_preview:''); 
default_module_config_key('general', 'order_by_resource_id', isset($order_by_resource_id)?$order_by_resource_id:''); 
default_module_config_key('general', 'enable_find_similar', isset($enable_find_similar)?$enable_find_similar:'1'); 
default_module_config_key('general', 'disable_link_in_view', isset($disable_link_in_view)?$disable_link_in_view:''); 
default_module_config_key('general', 'email_url_save_user', isset($email_url_save_user)?$email_url_save_user:''); 
default_module_config_key('general', 'disable_upload_preview', isset($disable_upload_preview)?$disable_upload_preview:''); 
default_module_config_key('general', 'disable_alternative_files', isset($disable_alternative_files)?$disable_alternative_files:''); 
default_module_config_key('general', 'hide_access_column', isset($hide_access_column)?$hide_access_column:''); 
default_module_config_key('general', 'show_edit_all_link', isset($show_edit_all_link)?$show_edit_all_link:''); 
default_module_config_key('general', 'papersize_select', isset($papersize_select)?$papersize_select:' US Letter - 8.5" x 11" US Legal - 8.5" x 14" US Tabloid - 11" x 17" A4 - 210mm x 297mm A3 - 297mm x 420mm'); 
default_module_config_key('general', 'display_collection_title', isset($display_collection_title)?$display_collection_title:''); 
default_module_config_key('general', 'bypass_share_screen', isset($bypass_share_screen)?$bypass_share_screen:''); 
default_module_config_key('general', 'collection_prefix', isset($collection_prefix)?$collection_prefix:''); 
default_module_config_key('general', 'email_multi_collections', isset($email_multi_collections)?$email_multi_collections:''); 
default_module_config_key('general', 'back_to_collections_link', isset($back_to_collections_link)?$back_to_collections_link:''); 
default_module_config_key('general', 'partial_index_min_word_length', isset($partial_index_min_word_length)?$partial_index_min_word_length:'3'); 
default_module_config_key('general', 'thumbs_display_fields', isset($thumbs_display_fields)?$thumbs_display_fields:array(3)); 
default_module_config_key('general', 'image_rotate_reverse_options', isset($image_rotate_reverse_options)?$image_rotate_reverse_options:''); 
default_module_config_key('general', 'jupload_chunk_size', isset($jupload_chunk_size)?$jupload_chunk_size:'5000000'); 
default_module_config_key('general', 'jupload_look_and_feel', isset($jupload_look_and_feel)?$jupload_look_and_feel:'java'); 
default_module_config_key('general', 'themes_in_my_collections', isset($themes_in_my_collections)?$themes_in_my_collections:''); 
default_module_config_key('general', 'top_nav_upload', isset($top_nav_upload)?$top_nav_upload:'1'); 
default_module_config_key('general', 'top_nav_upload_type', isset($top_nav_upload_type)?$top_nav_upload_type:'java'); 
default_module_config_key('general', 'allow_resource_deletion', isset($allow_resource_deletion)?$allow_resource_deletion:'1'); 
default_module_config_key('general', 'delete_requires_password', isset($delete_requires_password)?$delete_requires_password:'1'); 
default_module_config_key('general', 'process_locks_max_seconds', isset($process_locks_max_seconds)?$process_locks_max_seconds:'14400'); 
default_module_config_key('general', 'zip_contents_field_crop', isset($zip_contents_field_crop)?$zip_contents_field_crop:'1'); 
default_module_config_key('general', 'ffmpeg_supported_extensions', isset($ffmpeg_supported_extensions)?$ffmpeg_supported_extensions:'Array'); 
default_module_config_key('general', 'ffmpeg_audio_extensions', isset($ffmpeg_audio_extensions)?$ffmpeg_audio_extensions:'Array'); 
default_module_config_key('general', 'ffmpeg_audio_params', isset($ffmpeg_audio_params)?$ffmpeg_audio_params:'-acodec libmp3lame -ab 64k -ac 1'); 
default_module_config_key('general', 'no_preview_extensions', isset($no_preview_extensions)?$no_preview_extensions:'Array'); 
default_module_config_key('general', 'default_display', isset($default_display)?$default_display:'thumbs'); 
default_module_config_key('general', 'alternative_file_previews', isset($alternative_file_previews)?$alternative_file_previews:''); 
default_module_config_key('general', 'public_collections_confine_group', isset($public_collections_confine_group)?$public_collections_confine_group:''); 
default_module_config_key('general', 'public_collections_top_nav', isset($public_collections_top_nav)?$public_collections_top_nav:''); 
default_module_config_key('general', 'breadcrumbs', isset($breadcrumbs)?$breadcrumbs:''); 
default_module_config_key('general', 'multilingual_text_fields', isset($multilingual_text_fields)?$multilingual_text_fields:''); 
default_module_config_key('general', 'upload_methods', isset($upload_methods)?$upload_methods:'Array'); 
default_module_config_key('general', 'local_ftp_upload_folder', isset($local_ftp_upload_folder)?$local_ftp_upload_folder:'upload/'); 
default_module_config_key('general', 'unoconv_extensions', isset($unoconv_extensions)?$unoconv_extensions:'Array'); 
default_module_config_key('general', 'sort_relations_by_filetype', isset($sort_relations_by_filetype)?$sort_relations_by_filetype:''); 
default_module_config_key('general', 'video_playlists', isset($video_playlists)?$video_playlists:''); 
default_module_config_key('general', 'allow_save_search', isset($allow_save_search)?$allow_save_search:'1'); 
default_module_config_key('general', 'use_collection_name_in_zip_name', isset($use_collection_name_in_zip_name)?$use_collection_name_in_zip_name:''); 
default_module_config_key('general', 'use_theme_bar', isset($use_theme_bar)?$use_theme_bar:''); 
default_module_config_key('general', 'pdf_dynamic_rip', isset($pdf_dynamic_rip)?$pdf_dynamic_rip:''); 
default_module_config_key('general', 'site_text_custom_create', isset($site_text_custom_create)?$site_text_custom_create:''); 
default_module_config_key('general', 'resource_hit_count_on_downloads', isset($resource_hit_count_on_downloads)?$resource_hit_count_on_downloads:''); 
default_module_config_key('general', 'show_hitcount', isset($show_hitcount)?$show_hitcount:''); 
default_module_config_key('general', 'use_checkboxes_for_selection', isset($use_checkboxes_for_selection)?$use_checkboxes_for_selection:''); 
default_module_config_key('general', 'mp3_player', isset($mp3_player)?$mp3_player:'1'); 
default_module_config_key('general', 'config_show_performance_footer', isset($config_show_performance_footer)?$config_show_performance_footer:''); 
default_module_config_key('general', 'use_phpmailer', isset($use_phpmailer)?$use_phpmailer:''); 
default_module_config_key('general', 'enable_thumbnail_creation_on_upload', isset($enable_thumbnail_creation_on_upload)?$enable_thumbnail_creation_on_upload:'1'); 
default_module_config_key('general', 'xml_metadump', isset($xml_metadump)?$xml_metadump:'1'); 
default_module_config_key('general', 'xml_metadump_dc_map', isset($xml_metadump_dc_map)?$xml_metadump_dc_map:'Array'); 
default_module_config_key('general', 'use_plugins_manager', isset($use_plugins_manager)?$use_plugins_manager:''); 
default_module_config_key('general', 'syncdir', isset($syncdir)?$syncdir:'/path/to/static/files'); 
default_module_config_key('general', 'nogo', isset($nogo)?$nogo:'[folder1]'); 
default_module_config_key('general', 'staticsync_autotheme', isset($staticsync_autotheme)?$staticsync_autotheme:'1'); 
default_module_config_key('general', 'staticsync_extension_mapping_default', isset($staticsync_extension_mapping_default)?$staticsync_extension_mapping_default:'1'); 
default_module_config_key('general', 'staticsync_extension_mapping', isset($staticsync_extension_mapping)?$staticsync_extension_mapping:'Array'); 
default_module_config_key('general', 'staticsync_title_includes_path', isset($staticsync_title_includes_path)?$staticsync_title_includes_path:'1'); 
default_module_config_key('general', 'staticsync_ingest', isset($staticsync_ingest)?$staticsync_ingest:''); 
default_module_config_key('general', 'staticsync_alternatives_suffix', isset($staticsync_alternatives_suffix)?$staticsync_alternatives_suffix:'_alternatives'); 
default_module_config_key('general', 'data_joins', isset($data_joins)?$data_joins:'Array'); 
default_module_config_key('general', 'frameless_collections', isset($frameless_collections)?$frameless_collections:''); 
default_module_config_key('general', 'debug_log', isset($debug_log)?$debug_log:''); 
default_module_config_key('general', 'imagemagick_path', isset($imagemagick_path)?$imagemagick_path:'');
default_module_config_key('general', 'ghostscript_path', isset($ghostscript_path)?$ghostscript_path:'');
default_module_config_key('general', 'ghostscript_executable', isset($ghostscript_executable)?$ghostscript_executable:'');
default_module_config_key('general', 'ffmpeg_path', isset($ffmpeg_path)?$ffmpeg_path:'');
default_module_config_key('general', 'exiftool_path', isset($exiftool_path)?$exiftool_path:'');
default_module_config_key('general', 'antiword_path', isset($antiword_path)?$antiword_path:'');
default_module_config_key('general', 'pdftotext_path', isset($pdftotext_path)?$pdftotext_path:'');

# Geolocation Module
default_module_config_key('geolocation', 'disable_geocoding', isset($disable_geocoding)?$disable_geocoding:true);
default_module_config_key('geolocation', 'gmaps_apikey', isset($gmaps_apikey)?$gmaps_apikey:'');
set_module_config_key('general','dbversion', $scriptversion);

if ($direct) { ?> 
    Upgrade complete <br />
<?php } } else { if($direct){?>
<h1>No Upgrade Needed.</h1>
<h2>ResourceSpace version: <?php echo $productversion; ?></h2>
<?php }}?>