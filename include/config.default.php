<?php
/**
 * This file contains the default configuration settings.
 * 
 * **** DO NOT ALTER THIS FILE! ****
 * 
 * If you need to change any of the below values, copy
 * them to config.php and change them there.
 * 
 * This file will be overwritten when you upgrade and
 * ensures that any new configuration options are set to
 * a sensible default value.
 * 
 * @package ResourceSpace
 * @subpackage Configuration
 */



/* ---------------------------------------------------
BASIC PARAMETERS
------------------------------------------------------ */
$mysql_server="localhost";	# Use 'localhost' if MySQL is installed on the same server as your web server.
$mysql_username="root";		# MySQL username
$mysql_password="";			# MySQL password
$mysql_db="resourcespace";			# MySQL database name
# $mysql_charset="utf8"; # MySQL database connection charset, uncomment to use.

# The path to the MySQL client binaries - e.g. mysqldump
# (only needed if you plan to use the export tool)
$mysql_bin_path="/usr/bin"; # Note: no trailing slash

# Force MySQL Strict Mode? (regardless of existing setting) - This is useful for developers so that errors that might only occur when Strict Mode is enabled are caught. Strict Mode is enabled by default with some versions of MySQL. The typical error caused is when the empty string ('') is inserted into a numeric column when NULL should be inserted instead. With Strict Mode turned off, MySQL inserts NULL without complaining. With Strict Mode turned on, a warning/error is generated.
$mysql_force_strict_mode=false;

$secure=false; # Using HTTPS?
$development=false; # Development mode?
$baseurl="http://my.site/resourcespace"; # The 'base' web address for this installation. Note: no trailing slash
$email_from="resourcespace@my.site"; # Where e-mails appear to come from
$email_notify="resourcespace@my.site"; # Where resource/research/user requests are sent
$spider_password="TBTT6FD"; # The password required for spider.php - IMPORTANT - randomise this for each new installation. Your resources will be readable by anyone that knows this password.

# Scramble resource paths? If this is a public installation then this is a very wise idea.
# Set the scramble key to be a hard-to-guess string (similar to a password).
# To disable, set to the empty string ("").
$scramble_key="abcdef123";

# If you agree to send occasional statistics to Montala, leave this set to 'yes'.
# The following two numeric metrics alone will be sent every 7 days:
# - Number of resources
# - Number of users
# The information will only be used to provide totals on the Montala site, e.g 
# global number of installations, users and resources.
$send_statistics=true;

# Enable work-arounds required when installed on Microsoft Windows systems
$config_windows=false;

# ---- Paths to various external utilities ----

# If using ImageMagick, uncomment and set next 2 lines
# $imagemagick_path="/sw/bin";
# $ghostscript_path="/sw/bin";

# If using FFMpeg to generate video thumbs and previews, uncomment and set next line.
# $ffmpeg_path="/usr/bin";

# Install Exiftool and set this path to enable metadata-writing when resources are downloaded
# $exiftool_path="/usr/local/bin";

# Path to Antiword - for text extraction / indexing of Microsoft Word Document (.doc) files
# $antiword_path="/usr/bin";

# Path to pdftotext - part of the XPDF project, see http://www.foolabs.com/xpdf/
# Enables extraction of text from PDF files
# $pdftotext_path="/usr/bin";














/* ---------------------------------------------------
OTHER PARAMETERS

The below options customise your installation. 
You do not need to review these items immediately
but may want to review them once everything is up 
and running.
------------------------------------------------------ */


# Uncomment and set next two lines to configure storage locations (to use another server for file storage)
#$storagedir="/path/to/filestore"; # Where to put the media files. Can be absolute (/var/www/blah/blah) or relative to the installation. Note: no trailing slash
#$storageurl="http://my.storage.server/filestore"; # Where the storagedir is available. Can be absolute (http://files.example.com) or relative to the installation. Note: no trailing slash

include "version.php";
$applicationname="ResourceSpace"; # The name of your implementation / installation (e.g. 'MyCompany Resource System')

# Available languages
$defaultlanguage="en"; # default language, uses iso codes (en, es etc.)
$languages["en"]="British English";
$languages["en-US"]="American English";
$languages["ar"]="العربية";
$languages["id"]="Bahasa Indonesia"; # Indonesian
$languages["zh-CN"]="简化字"; # Simplified Chinese
$languages["da"]="Dansk"; # Danish
$languages["de"]="Deutsch"; # German
$languages["el"]="Ελληνικά"; # Greek
$languages["es"]="Español"; # Spanish
$languages["fr"]="Français"; # French
$languages["hr"]="Hrvatski Jezik"; # Croatian
$languages["it"]="Italiano"; # Italian
$languages["jp"]="日本語"; # Japanese
$languages["nl"]="Nederlands"; # Dutch
$languages["no"]="Norsk"; # Norwegian
$languages["pt"]="Português"; # Portuguese
$languages["pt-BR"]="Português do Brasil"; # Brazilian Portuguese
$languages["ru"]="Русский язык"; # Russian
$languages["sv"]="Svenska"; # Swedish


# Disable language selection options
$disable_languages=false;

# FTP settings for batch upload
# Only necessary if you plan to use the FTP upload feature.
$ftp_server="my.ftp.server";
$ftp_username="my_username";
$ftp_password="my_password";
$ftp_defaultfolder="temp/";

# Can users change passwords?
$allow_password_change=true;

# search params
# Common keywords to ignore both when searching and when indexing.
$noadd=array("", "a","the","this","then","another","is","with","in","and","where","how","on","of","to", "from", "at", "for", "-", "by", "be");
$suggest_threshold=-1; # How many results trigger the 'suggestion' feature, -1 disables the feature
$max_results=50000;
$minyear=1980; # The year of the earliest resource record, used for the date selector on the search form. Unless you are adding existing resources to the system, probably best to set this to the current year at the time of installation.

# Set folder for home images. Ex: "gfx/homeanim/mine/" 
# Files should be numbered sequentially, and will be auto-counted.
$homeanim_folder="gfx/homeanim/gfx";

# Optional 'quota size' for allocation of a set amount of disk space to this application. Value is in GB.
# Note: Unix systems only.
# $disksize=150;

# Set your time zone below (default GMT)
if (function_exists("date_default_timezone_set")) {date_default_timezone_set("GMT");}

# IPTC header - Character encoding auto-detection
# If using IPTC headers, specify any non-ascii characters used in your local language
# to aid with character encoding auto-detection.
# Several encodings will be attempted and if a character in this string is returned then this is considered
# a match.
# For English, there is no need to specify anything here (i.e. just an empty string)
# The example given is for Norwegian.
$iptc_expectedchars="æøåÆØÅ";

# Which field do we drop the EXIF data in to? (when NOT using exiftool)
# Comment out these lines to disable basic EXIF reading.
# See exiftool for more advanced EXIF/IPTC/XMP extraction.
$exif_comment=18;
$exif_model=52;
$exif_date=12;

# If exiftool is installed, you can optionally enable the metadata report available on the View page. 
# You may want to enable it on the usergroup level by overriding this config option in System Setup.
$metadata_report=false;

# Allow a link to re-extract metadata per-resource (on the View Page) to users who have edit abilities.
$allow_metadata_revert=false;

# Use Exiftool to attempt to extract specified resolution and unit information from files (ex. Adobe files) upon upload.
$exiftool_resolution_calc=false;

# Set to true to strip out existing EXIF,IPTC,XMP metadata when adding metadata to resources using exiftool.
$exiftool_remove_existing=false; 

# If Exiftool path is set, write metadata to files upon download if possible.
$exiftool_write=true;

# If Exiftool path is set, do NOT send files with the following extensions to exiftool for processing
# For example: $exiftool_no_process=array("eps","png");
$exiftool_no_process=array();

# Which field do we drop the original filename in to?
$filename_field=51;


# If using imagemagick, should colour profiles be preserved? (for larger sizes only - above 'scr')
$imagemagick_preserve_profiles=false;
$imagemagick_quality=90; # JPEG quality (0=worst quality/lowest filesize, 100=best quality/highest filesize)

# Some files can take a long time to preview, or take too long (PSD) or involve too many sofware dependencies (RAW). 
# If this is a problem, these options allow EXIFTOOL to attempt to grab a preview embedded in the file.
# (Files must be saved with Previews). If a preview image can't be extracted, RS will revert to ImageMagick.
$photoshop_thumb_extract=false;
$cr2_thumb_extract=false; 
$nef_thumb_extract=false;

# Attempt to resolve a height and width of the ImageMagick file formats at view time
# (enabling may cause a slowdown on viewing resources when large files are used)
$imagemagick_calculate_sizes=false;

# If using imagemagick for PDF, EPS and PS files, up to how many pages should be extracted for the previews?
# If this is set to more than one the user will be able to page through the PDF file.
 $pdf_pages=30;


# Create a preview video for ffmpeg compatible files? A FLV (Flash Video) file will automatically be produced for supported file types (most video types - AVI, MOV, MPEG etc.)
$ffmpeg_preview=true; 
$ffmpeg_preview_seconds=20; # how many seconds to preview
$ffmpeg_preview_extension="flv";
$ffmpeg_preview_min_width=32;
$ffmpeg_preview_min_height=18;
$ffmpeg_preview_max_width=480;
$ffmpeg_preview_max_height=270;
$ffmpeg_preview_options="-f flv -ar 22050 -b 650k -ab 32 -ac 1";

# If uploaded file is FLV, should we transcode it anyway?
$ffmpeg_preview_force=false;

# Encode preview asynchronous?
$ffmpeg_preview_async=false;

# FFMPEG - generation of alternative video file sizes/formats
# It is possible to automatically generate different file sizes and have them attached as alternative files.
# See below for examples.
# The blocks must be numbered sequentially (0, 1, 2).
# Ensure the formats you are specifiying with vcodec and acodec are supported by checking 'ffmpeg -formats'.
#
# $ffmpeg_alternatives[0]["name"]="QuickTime H.264 WVGA";
# $ffmpeg_alternatives[0]["filename"]="quicktime_h264";
# $ffmpeg_alternatives[0]["extension"]="mov";
# $ffmpeg_alternatives[0]["params"]="-vcodec h264 -s wvga -aspect 16:9 -b 2500k -deinterlace -ab 160k -acodec mp3 -ac 2";
#
# $ffmpeg_alternatives[1]["name"]="Larger FLV";
# $ffmpeg_alternatives[0]["filename"]="flash";
# $ffmpeg_alternatives[1]["extension"]="FLV";
# $ffmpeg_alternatives[1]["params"]="-s wvga -aspect 16:9 -b 2500k -deinterlace -ab 160k -acodec mp3 -ac 2";


# To be able to run certain actions asyncronus (eg. preview transcoding), define the path to php:
# $php_path="/usr/bin";

# Use qt-faststart to make mp4 previews start faster
# $qtfaststart_path="/usr/bin";
# $qtfaststart_extensions=array("mp4","m4v","mov");

# Allow users to request accounts?
$allow_account_request=true;

# Should the system allow users to request new passwords via the login screen?
$allow_password_reset=true;

# Highlight search keywords when displaying results and resources?
$highlightkeywords=true;

# Search on day in addition to month/year?
$searchbyday=false;

# Allow download of original file for resources with "Restricted" access.
# For the tailor made preview sizes / downloads, this value is set per preview size in the system setup.
$restricted_full_download=false;

# Also search the archive and display a count with every search? (performance penalty)
$archive_search=false;

# Display the Research Request functionality?
# Allows users to request resources via a form, which is e-mailed.
$research_request=false;

# Country search in the right nav? (requires a field with the short name 'country')
$country_search=false;

# Resource ID search blank in right nav? (probably only needed if $config_search_for_number is set to true) 
$resourceid_simple_search=false;

# Enable sorting resources in other ways:
$colour_sort=true;
$title_sort=false; 
$country_sort=false; // (requires that the 'country' field maps to the 'country' database column on resource)
$original_filename_sort=false; // will only work if you are not using staticsync 

# What is the default sort order?
# Options are date, colour, relevance, popularity, country
$default_sort="relevance";

# Enable themes (promoted collections intended for showcasing selected resources)
$enable_themes=true;

# Use the themes page as the home page?
$use_theme_as_home=false;

# Use the recent page as the home page?
$use_recent_as_home=false;


# Show images along with theme category headers (image selected is the most popular within the theme category)
$theme_images=true;
$theme_images_number=1; # How many to auto-select (if none chosen manually)
$theme_images_align_right=false; # Align theme images to the right on the themes page? (particularly useful when there are multiple theme images)

# How many levels of theme category to show.
# If this is set to more than one, a dropdown box will appear to allow browsing of theme sub-levels
# Maximum of 3 category levels (so with the themes themselves, a total of 4 categorisation levels for resources).
$theme_category_levels=1;

##  Advanced Search Options
##  Defaults (all false) shows advanced search in the search bar but not the home page or top navigation.
##  To disable advanced search altogether, set 
##      $advancedsearch_disabled = true;
##      $home_advancedsearch=false;
##      $advanced_search_nav=false;

#Hide advanced search on search bar
$advancedsearch_disabled = false;

# Show advanced search on the home page?
$home_advancedsearch=false;

# Display the advanced search as a 'search' link in the top navigation
$advanced_search_nav=false;



# Show My Contributions on the home page?
$home_mycontributions=false;

# Do not display 'search results' link in the top navigation
$disable_searchresults = false;

# Display a 'Recent' link in the top navigation
$recent_link=true;

# Display Help and Advice link in the top navigation
$help_link=true;

# Display Search Results link in top navigation
$search_results_link=true;

# Display 'View New Material' link in the quick search bar (same as 'Recent')
$view_new_material=false;

# Display a 'My Collections' link in the top navigation
$mycollections_link=false;

# Display a 'My Contributions' link in the top navigation for all users, even if they have team center access
# (Displays automatically for regular users)
$mycontributions_link=false;

# Require terms for download?
$terms_download=false;

# Require terms on first login?
$terms_login=false;

##  Thumbnails options

# In the collection frame, show or hide thumbnails by default? ("hide" is better if collections are not going to be heavily used).
$thumbs_default="show";
#  Automatically show thumbs when you change collection (only if default is show)
$autoshow_thumbs = false;
# Enable small thumbnails option for search screen
$smallthumbs=true;
# How many thumbnails to show in the collections frame until the frame automatically hides thumbnails.
$max_collection_thumbs=150;

# Options for number of results to display per page:
$results_display_array=array(24,48,72,120,240);
# How many results per page? (default)
$default_perpage=48;

# Group based upload folders? (separate local upload folders for each group)
$groupuploadfolders=false;

# Enable order by rating? (require rating field updating to rating column)
$orderbyrating=false;

# Zip command to use to create zip archive (uncomment to enable download of collections as a zip file)
# $zipcommand="zip -j";

# Option to write a text file into zipped collections containing resource data
$zipped_collection_textfile=false;

# Enable speed tagging feature? (development)
$speedtagging=false;
$speedtaggingfield=1;

# A list of types which get the extra video icon in the search results
$videotypes=array(3);

# Sets the default colour theme (defaults to blue)
$defaulttheme="greyblu";

# Theme chips available. This makes it possible to add new themes and chips using the same structure.
# To create a new theme, you need a chip in gfx/interface, a graphics folder called gfx/<themename>,
# and a css file called css/Col-<themename>.css
# this is a basic way of adding general custom themes that do not affect SVN checkouts, 
# though css can also be added in plugins as usual.
 
$available_themes=array("greyblu","black","whitegry");

# Uncomment and set the next line to lock to one specific colour scheme (e.g. greyblu/whitegry).
# $userfixedtheme="whitegry";

# List of active plugins.
# Note that multiple plugins must be specified within array() as follows:
# $plugins=array("loader","rss","messaging","googledisplay"); 
$plugins=array();

# Uncomment and set the next line to allow anonymous access. The anonymous user will automatically be logged in
# to the account with the username specified.
# $anonymous_login="guest";

# Enable AJAX popup info box on search results.
$infobox=true;
# A list of fields to display in the info box (using the field reference number)
$infobox_fields=array(18,10,29,53);
# Display the resource ID in the info box?
$infobox_display_resource_id=true;
# Display a small resource file type icon in the info box?
$infobox_display_resource_icon=true;


# Reordering, captioning and ranking of collections
$collection_reorder_caption=false;

# Footer text applied to all e-mails (blank by default)
$email_footer="";

# Contact Sheet feature, and whether contact sheet becomes resource.
# Requires ImageMagick/Ghostscript.
$contact_sheet=true;
# Produce a separate resource file when creating contact sheets?
$contact_sheet_resource=false; 
# Ajax previews in contact sheet configuration. 
$contact_sheet_previews=true;
# Select a contact sheet font. Default choices are 
# helvetica,times (These are pdf core fonts: not embedded, very small pdf file size.)
$contact_sheet_font="helvetica";
# allow unicode filenames? (stripped out by default in tcpdf but since collection names may 
# have special characters, probably want to try this on.)
$contact_sheet_unicode_filenames=true;
# Set font sizes for contactsheet
$titlefontsize=10; // Contact Sheet Title
$refnumberfontsize=8; // This includes field text, not just ref number
# If making a contact sheet with list sheet style, use these fields in contact sheet:
$config_sheetlist_fields = array(8);
# If making a contact sheet with thumbnail sheet style, use these fields in contact sheet:
$config_sheetthumb_fields = array();
$config_sheetthumb_include_ref=true;

##  Contact Print settings - paper size options
$papersize_select = '
<option value="a4">A4 - 210mm x 297mm</option>
<option value="a3">A3 - 297mm x 420mm</option>
<option value="letter">US Letter - 8.5" x 11"</option>
<option value="legal">US Legal - 8.5" x 14"</option>
<option value="tabloid">US Tabloid - 11" x 17"</option>';

## Columns options (May want to limit options if you are adding text fields to the Thumbnail style contact sheet).
$columns_select = '
<option value=2>2</option>
<option value=3>3</option>
<option value=4 selected>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>';

# Contact Sheet Font Embedding:
# Note: this has only been developed for Linux so far. It relies on the ttf2ufm program included with tcpdf, and there is a compiled version included for windows, but it may need further dev on the paths to work. I don't think the linux compile works on the mac. -Tom G
# $ttf_file overrides $contact_sheet_font. 
# A .ttf file should be installed in the filestore/fonts directory (folder created on first use of the contact sheet), 
# $ttf_file="dejavusans.ttf"; 
# If using $ttf_file, optionally set $subsetting=true and set fontforge path to generate subset fonts on the fly (reduces PDF file size).
# $subsetting=true; 
# $fontforge_path="/usr/bin";


# Options below control the batch uploader used in user contributions. If both set to true,
# the user will be given a choice. If both set to false, the standard single upload will be used.
# Use SWFUpload (Flash) for user contributions
$usercontribute_swfupload=true;
# Use JUpload Java uploader for user contributions
$usercontribute_javaupload=false;

# Show related themes and public collections panel on Resource View page.
$show_related_themes=true;

# Multi-lingual support for e-mails. Try switching this to true if e-mail links aren't working and ASCII characters alone are required (e.g. in the US).
$disable_quoted_printable_enc=false;

# Watermarking - generate watermark images for 'internal' (thumb/preview) images.
# Groups with the 'w' permission will see these watermarks when access is 'restricted'.
# Uncomment and set to the location of a watermark graphic.
# NOTE: only available when ImageMagick is installed.
# NOTE: if set, you must be sure watermarks are generated for all images; This can be done using pages/tools/update_previews.php?previewbased=true
# NOTE: also, if set, restricted external emails will recieve watermarked versions. Restricted mails inherit the permissions of the sender, but
# if watermarks are enabled, we must assume restricted access requires the equivalent of the "w" permission
# $watermark="gfx/watermark.png";

# Simple search even more simple
# Set to 'true' to make the simple search bar more basic, with just the single search box.
$basic_simple_search=false;

# Hide the main simple search field in the searchbar (if using only simple search fields for the searchbar)
$hide_main_simple_search=false;

# Options to show/hide the link panels on the home page
$home_themeheaders=false;
$home_themes=true;
$home_mycollections=true;
$home_helpadvice=true;

#
# Custom panels for the home page.
# You can add as many panels as you like. They must be numbered sequentially starting from zero (0,1,2,3 etc.)
#
# You may want to turn off $home_themes etc. above if you want ONLY your own custom panels to appear on the home page.
#
# The below are examples.
#
# $custom_home_panels[0]["title"]="Custom Panel A";
# $custom_home_panels[0]["text"]="Custom Panel Text A";
# $custom_home_panels[0]["link"]="search.php?search=example";
#
# $custom_home_panels[1]["title"]="Custom Panel B";
# $custom_home_panels[1]["text"]="Custom Panel Text B";
# $custom_home_panels[1]["link"]="search.php?search=example";
#
# $custom_home_panels[2]["title"]="Custom Panel C";
# $custom_home_panels[2]["text"]="Custom Panel Text C";
# $custom_home_panels[2]["link"]="search.php?search=example";


# Custom top navigation links.
# You can add as many panels as you like. They must be numbered sequentially starting from zero (0,1,2,3 etc.)
# URL should be absolute, or include $baseurl as below, because a relative URL will not work from the Team Center.
# 
# $custom_top_nav[0]["title"]="Example Link A";
# $custom_top_nav[0]["link"]="$baseurl/pages/search.php?search=a";
#
# $custom_top_nav[1]["title"]="Example Link B";
# $custom_top_nav[1]["link"]="$baseurl/pages/search.php?search=b";


# Use original filename when downloading a file?
$original_filenames_when_downloading=true;

# When $original_filenames_when_downloading, should the original filename be prefixed with the resource ID?
# This ensures unique filenames when downloading multiple files.
# WARNING: if switching this off, be aware that when downloading a collection as a zip file, a file with the same name as another file in the collection will overwrite that existing file. It is therefore advisiable to leave this set to 'true'.
$prefix_resource_id_to_filename=true;

# When using $prefix_resource_id_to_filename above, what string should be used prior to the resource ID?
# This is useful to establish that a resource was downloaded from ResourceSpace and that the following number
# is a ResourceSpace resource ID.
$prefix_filename_string="RS";

# Display a 'new' flag next to new themes (added < 1 month ago)
$flag_new_themes=true;

# Create file checksums? (experimental)
$file_checksums=false;

# Default group when adding new users;
$default_group=2;

# Enable 'custom' access level?
# Allows fine-grained control over access to resources.
# You may wish to disable this if you are using metadata based access control (search filter on the user group)
$custom_access=true;

# How are numeric searches handled?
#
# If true:
# 		If the search keyword is numeric then the resource with the matching ID will be shown
# If false:
#		The search for the number provided will be performed as with any keyword. However, if a resource with a matching ID number if found then this will be shown first.
$config_search_for_number=true;

# Display the download as a 'save as' link instead of redirecting the browser to the download (which sometimes causes a security warning).
# For the Opera and Internet Explorer 7 browsers this will always be enabled regardless of the below setting as these browsers block automatic downloads by default.
$save_as=false;

# Allow resources to be e-mailed / shared (internally and externally)
$allow_share=true;

# Should those with 'restricted' access to a resource be able to share the resource?
$restricted_share=false;

# Auto-completion of search (quick search only)
# Disabled by default due to an apparent bug with the Scriptaculous code used for this (Internet Explorer only)
$autocomplete_search=true;
$autocomplete_search_items=15;

# Automatically order checkbox lists (alphabetically)
$auto_order_checkbox=true;

# Order checkbox lists vertically (as opposed to horizontally, as HTML tables normally work)
$checkbox_ordered_vertically=true;

# When batch uploading, show the 'add resources to collection' selection box
$enable_add_collection_on_upload=true;
# Batch Uploads, default is "Add to New Collection". Turn off to default to "Do not Add to Collection"
$upload_add_to_new_collection=true;

# When batch uploading, enable the 'copy resource data from existing resource' feature
$enable_copy_data_from=true;

# Always record the name of the resource creator for new records.
# If false, will only record when a resource is submitted into a provisional status.
$always_record_resource_creator = false;

# Enable the 'related resources' field when editing resources.
$enable_related_resources=true;

# Enable the 'keep me logged in at this workstation' option at the login form
# If the user then selects this, a 100 day expiry time is set on the cookie.
$allow_keep_logged_in=true;

# Show the link to 'user contributed assets' on the My Contributions page
# Allows non-admin users to see the assets they have contributed
$show_user_contributed_resources=true;

# Show the contact us link?
$contact_link=true;

# Show the about us link?
$about_link=true;

# When uploading resources (batch upload) and editing the template, should the date be reset to today's date?
# If set to false, the previously entered date is used.
$reset_date_upload_template=true;
$reset_date_field=12; # Which date field to reset? (if using multiple date fields)

# When uploading resources (batch upload) and editing the template, should all values be reset to blank every time?
$blank_edit_template=false;

# Show expiry warning when expiry date has been passed
$show_expiry_warning=true;

# Make the frameset resizeable. Useful for viewing large collections, especially if re-ordering.
$collection_resize=false;

# Make selection box in collection edit menu that allows you to select another accessible collection to base the current one upon.
# It is helpful if you would like to make variations on collections that are heavily commented upon or re-ordered.
$enable_collection_copy=true;

# Default resource types to use for searching (leave empty for all)
$default_res_types="";

# Show the Resource ID on the resource view page.
$show_resourceid=true;

# Show the access on the resource view page.
$show_access_field=true;

# Show the 'contributed by' on the resource view page.
$show_contributed_by=true;

# Show the extension after the truncated text in the search results.
$show_extension_in_search=false;

# Should the category tree field (if one exists) default to being open instead of closed?
$category_tree_open=false;

# Length of a user session. This is used for statistics (user sessions per day) and also for auto-log out if $session_autologout is set.
$session_length=30;

# Automatically log a user out at the end of a session (a period of idleness equal to $session_length above).
$session_autologout=false;

# Allow browsers to save the login information on the login form.
$login_autocomplete=true;

# Remember the username on the login screen if the previous session expired
# (logging out clears the username for security purposes)
$login_remember_username=true;

# Password standards - these must be met when a user or admin creates a new password.
$password_min_length=7; # Minimum length of password
$password_min_alpha=1; # Minimum number of alphabetical characters (a-z, A-Z) in any case
$password_min_numeric=1; # Minimum number of numeric characters (0-9)
$password_min_uppercase=0; # Minimum number of upper case alphabetical characters (A-Z)
$password_min_special=0; # Minimum number of 'special' i.e. non alphanumeric characters (!@$%& etc.)

# How often do passwords expire, in days? (set to zero for no expiry).
$password_expiry=0;

# How many failed login attempts per IP address until a temporary ban is placed on this IP
# This helps to prevent dictionary attacks.
$max_login_attempts_per_ip=20;

# How many failed login attempts per username until a temporary ban is placed on this IP
$max_login_attempts_per_username=5;

# How long the user must wait after failing the login $max_login_attempts_per_ip or $max_login_attempts_per_username times.
$max_login_attempts_wait_minutes=10;

# Use imperial instead of metric for the download size guidelines
$imperial_measurements=false;

# What is the default resource type to use for batch upload templates?
$default_resource_type=1;

# If ResourceSpace is behind a proxy, enabling this will mean the "X-Forwarded-For" Apache header is used
# for the IP address. Do not enable this if you are not using such a proxy as it will mean IP addresses can be
# easily faked.
$ip_forwarded_for=false;

# When extracting text from documents (e.g. HTML, DOC, TXT, PDF) which field is used for the actual content?
# Comment out the line to prevent extraction of text content
$extracted_text_field=72;

# Should the resources that are in the archive state "User Contributed - Pending Review" (-1) be
# visible in the main searches (as with resources in the live state)?
# The resources will not be downloadable, except to the contributer and those with edit capability to the resource.
$pending_review_visible_to_all=false;

# Enable user rating of resources
# Users can rate resources using a star ratings system on the resource view page.
# Average ratings are automatically calculated and used for the 'popularity' search ordering.
$user_rating=false;

# Enable public collections
# Public collections are collections that have been set as public by users and are searchable at the bottom
# of the themes page. Note that, if turned off, it will still be possible for administrators to set collections
# as public as this is how themes are published.
$enable_public_collections=true;

# Custom User Registration Fields
# -------------------------------
# Additional custom fields that are collected and e-mailed when new users apply for an account
# Uncomment the next line and set the field names, comma separated
#$custom_registration_fields="Phone Number,Department";
# Which of the custom fields are required?
# $custom_registration_required="Phone Number";
# You can also set that particular fields are displayed in different ways as follows:
# $custom_registration_types["Department"]=1;
# Types are as follows:
# 	1: Normal text box (default)
# 	2: Large text box
#   3: Drop down box (set options using $custom_registration_options["Field Name"]=array("Option 1","Option 2","Option 3");
#   4: HTML block, e.g. help text paragraph (set HTML usign $custom_registration_html="<b>Some HTML</b>";

# Allow user group to be selected as part of user registration?
# User groups available for user selection must be specified using the 'Allow registration selection' option on each user group
# in System Setup.
# Only useful when $user_account_auto_creation=true;
$registration_group_select=false;

# Custom Resource/Collection Request Fields
# -----------------------------------------
# Additional custom fields that are collected and e-mailed when new resources or collections are requested.
# Uncomment the next line and set the field names, comma separated
#$custom_request_fields="Phone Number,Department";
# Which of the custom fields are required?
# $custom_request_required="Phone Number";
# You can also set that particular fields are displayed in different ways as follows:
# $custom_request_types["Department"]=1;
# Types are as follows:
# 	1: Normal text box (default)
# 	2: Large text box
#   3: Drop down box (set options using $custom_request_options["Field Name"]=array("Option 1","Option 2","Option 3");
#   4: HTML block, e.g. help text paragraph (set HTML usign $custom_request_html="<b>Some HTML</b>";


# Send an e-mail to the address set at $email_notify above when user contributed
# resources are submitted (status changes from "User Contributed - Pending Submission" to "User Contributed - Pending Review").
$notify_user_contributed_submitted=true;
$notify_user_contributed_unsubmitted=false;

# When requesting feedback, allow the user to select resources (e.g. pick preferred photos from a photo shoot).
$feedback_resource_select=false;


# Uncomment and set the below value to set the maximum size of uploaded file that thumbnail/preview images will be created for.
# This is useful when dealing with very large files that may place a drain on system resources - for example 100MB+ Adobe Photoshop files will take a great deal of cpu/memory for ImageMagick to process and it may be better to skip the automatic preview in this case and add a preview JPEG manually using the "Upload a preview image" function on the resource edit page.
# The value is in MB.
# $preview_generate_max_file_size=100;

# Should resource views be logged for reporting purposes?
# Note that general daily statistics for each resource are logged anyway for the statistics graphs
# - this option relates to specific user tracking for the more detailed report.
$log_resource_views=false;

# A list of file extentions of file types that cannot be uploaded for security reasons.
# For example; uploading a PHP file may allow arbirtary execution of code, depending on server security settings.
$banned_extensions=array("php","cgi","pl","exe","asp","jsp");

# When uploading batch resources, on the edit 'template' by default the status and access fields are hidden. Set the below option to 'true' to enable these options during this process.
$show_status_and_access_on_upload=false;

# Mime types by extensions.
# used by pages/download.php to detect the mime type of the file proposed to download.
$mime_type_by_extension = array(
    'mov'   => 'video/quicktime',
    '3gp'   => 'video/3gpp',
    'mpg'   => 'video/mpeg',
    'mp4'   => 'video/mp4',
    'avi'   => 'video/msvideo',
    'mp3'   => 'audio/mpeg',
    'wav'   => 'audio/x-wav',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'gif'   => 'image/gif',
    'png'   => 'image/png',
  );

# PHP execution time limit
# Default is 5 minutes.
$php_time_limit=300;

# Should the automatically produced FLV file be available as a separate download?
$flv_preview_downloadable=false;

# What is the default value for the user select box, for example when e-mailing resources?
$default_user_select="";

# When multiple dropdowns are used on the simple search box, should selecting something from one or more dropdowns
# limit the options available in the other dropdowns automatically? This adds a performance penalty so is off by default.
$simple_search_dropdown_filtering=false;


# When searching, also include themes/public collections at the top?
$search_includes_themes=true;
$search_includes_public_collections=false;

# include keywords from collection titles when indexing collections
$index_collection_titles = false;

# Default home page (when not using themes as the home page).
# You can set other pages, for example search results, as the home page e.g.
# $default_home_page="search.php?search=example";
$default_home_page="home.php";

# Configures separators to use when splitting keywords (in other words - characters to treat as white space)
# You must reindex after altering this if you have existing data in the system (via pages/tools/reindex.php)
# 'Space' is included by default and does not need to be specified below.
$config_separators=array("/","_",".","; ","-","(",")","'","\"","\\", "?");

# trim characters - will be removed from the beginning or end of the string, but not the middle
# when indexing. Format for this argument is as described in PHP trim() documentation.
# leave blank for no extra trimming.
$config_trimchars="";

# Global permissions
# Permissions that will be prefixed to all user group permissions
# Handy for setting global options, e.g. for fields
# By default do not allow the 'original filename' and 'camera make/model' fields to be edited.
$global_permissions="F51,F52";

# User account application - auto creation
# By default this is switched off and applications for new user accounts will be sent as e-mails
# Enabling this option means user accounts will be created but will need to be approved by an administrator
# before the user can log in.
$user_account_auto_creation=false;
$user_account_auto_creation_usergroup=2; # which user group for auto-created accounts? (see also $registration_group_select - allows users to select the group themselves).

# Display a larger preview image on the edit page?
$edit_large_preview=false;

# Allow sorting by resource ID
$order_by_resource_id=false;

# Enable find similar search?
$enable_find_similar=true;

##  Hide the 'link' link on view.php (link is back to the same page)
$disable_link_in_view = false;

##  The URL that goes in the bottom of the 'new user' email (save_user function in general.php)
##  If blank, uses $baseurl 
$email_url_save_user = ""; 

# edit.php - disable links to upload preview and manage alternative files
$disable_upload_preview = false;
$disable_alternative_files = false;

#collection_manage.php - hide 'access' column
$hide_access_column = false;
#collection_manage.php - show 'edit all' link on collections
$show_edit_all_link = false;

# If displaying a collection in search.php, display collection title at top.
$display_collection_title = false;

#Bypass share.php and go straight to e-mail
$bypass_share_screen = false;

# add a prefix to all collection refs, to distinguish them from resource refs
$collection_prefix = "";

# Allow multiple collections to be e-mailed at once
$email_multi_collections = false;

#  Link back to collections from log page - if "" then link is ignored.
#  suggest 
# $back_to_collections_link = "&lt;&lt;-- Back to My Collections &lt;&lt;--";
$back_to_collections_link = "";

# For fields with partial keyword indexing enabled, this determines the minimum infix length
$partial_index_min_word_length=3;

# array of fields to display on the large thumbnail view.
$thumbs_display_fields=array(3);

# On some PHP installations, the imagerotate() function is wrong and images are rotated in the opposite direction
# to that specified in the dropdown on the edit page.
# Set this option to 'true' to rectify this.
$image_rotate_reverse_options=false;

# JUpload Chunk Size (bytes)
# The size in bytes that Jupload (Java Batch Upload) will break files into.
# JUpload chunking completely bypasses PHP's file upload limits (if chunk size is set lower than the upload limit).
$jupload_chunk_size="5000000"; # Chunk size ~5MB.

# JUpload Look and Feel
# set to "java" for java style file browser, and "system" to use look and feel of local system
$jupload_look_and_feel = "java";

# Once collections have been published as themes by default they are removed from the user's My Collections. These option leaves them in place.
$themes_in_my_collections=false;

# Show an upload link in the top navigation? (if 't' and 'c' permissions for the current user)
$top_nav_upload=true;
$top_nav_upload_type="java"; # The upload type. Options are java, swf, ftp and local

# Allow users to delete resources?
# (Can also be controlled on a more granular level with the "D" restrictive permission.)
$allow_resource_deletion = true;

# Resource deletion state
# When resources are deleted, the variable below can be set to move the resources into an alternative state instead of removing the resource and its files from the system entirely.
# 
# The resource will still be removed from any collections it has been added to.
#
# Possible options are:
#
# -2	User Contributed Pending Review (not useful unless deleting user-contributed resources)
# -1	User Contributed Pending Submission (not useful unless deleting user-contributed resources) 
# 1		Waiting to be archived
# 2 	Archived
# 3		Deleted (recommended)
# $resource_deletion_state=3;

# Does deleting resources require password entry? (single resource delete)
$delete_requires_password=true;


# Offline processes (e.g. staticsync and create_previews.php) - for process locking, how old does a lock have to be before it is ignored?
$process_locks_max_seconds=60*60*4; # 4 hours default.

# Zip files - the contents of the zip file can be imported to a text field on upload.
# Requires 'unzip' on the command path.
# If the below is not set, but unzip is available, the archive contents will be written to $extracted_text_field
#
# $zip_contents_field=18;
$zip_contents_field_crop=1; # The number of lines to remove from the top of the zip contents output (in order to remove the filename field and other unwanted header information).

# List of extensions that can be processed by ffmpeg.
# Mostly video files.
# @see http://en.wikipedia.org/wiki/List_of_file_formats#Video
$ffmpeg_supported_extensions = array(
		'aaf',
		'3gp',
		'asf',
		'avchd',
		'avi',
		'cam',
		'dat',
		'dsh',
		'flv',
		'm1v',
		'm2v',
		'mkv',
		'wrap',
		'mov',
		'mpeg',
		'mpg',
		'mpe',
		'mp4',
		'mxf',
		'nsv',
		'ogm',
		'rm',
		'ram',
		'svi',
		'smi',
		'wmv',
		'divx',
		'xvid',
	);

# A list of extensions which will be ported to mp3 format for preview.
# Note that if an mp3 file is uploaded, the original mp3 file will be used for preview.
$ffmpeg_audio_extensions = array(
	'wav',
	'ogg',
	'aiff',
	'au',
	'cdda',
	'm4a',
	'wma',
	'mp2',
	'aac',
	'ra',
	'rm',
	'gsm'
	);
	
# The audio settings for mp3 previews
$ffmpeg_audio_params = "-acodec libmp3lame -ab 64k -ac 1"; # Default to 64Kbps mono

# A list of file extensions for files which will not have previews automatically generated. This is to work around a problem with colour profiles whereby an image file is produced but is not a valid file format.
$no_preview_extensions=array("icm","icc");

# If set, send a notification when resources expire to this e-mail address.
# This requires batch/expiry_notification.php to be executed periodically via a cron job or similar.
# $expiry_notification_mail="myaddress@mydomain.example";

# What is the default display mode for search results? (smallthumbs/thumbs/list)
$default_display="thumbs";

# Generate thumbs/previews for alternative files?
$alternative_file_previews=true;

# For alternative file previews... enable a thumbnail mouseover to see the preview image?
$alternative_file_previews_mouseover=false;

# Confine public collections display to the collections posted by the user's own group, sibling groups, parent group and children groups.
# All collections can be accessed via a new 'view all' link.
$public_collections_confine_group=false;

# Show public collections in the top nav?
$public_collections_top_nav=false;

# Display theme categories as links, and themes on separate pages?
$themes_category_split_pages=false;

# How many characters to trim the title to on search results
$search_results_title_trim=40;

# Uncomment the below to set a vertical height for the title on the large thumbnail view.
# You may need to tweak $search_results_title_wordwrap below.
# $search_result_title_height=26;

# Force breaking up of very large titles so they wrap to multiple lines (useful when using multi line titles with $search_result_title_height above)
$search_results_title_wordwrap=20;





# ------------------------------------------------------------------------------------------------------------------
# StaticSync (staticsync.php)
# The ability to synchronise ResourceSpace with a separate and stand-alone filestore.
# ------------------------------------------------------------------------------------------------------------------
$syncdir="/path/to/static/files"; # The sync folder
$nogo="[folder1]"; # A list of folders to ignore within the sign folder.
$staticsync_autotheme=true; # Automatically create themes based on the first and second levels of the sync folder structure.
# Mapping extensions to resource types for sync'd files
# Format: staticsync_extension_mapping[resource_type]=array("extension 1","extension 2");
$staticsync_extension_mapping_default=1;
$staticsync_extension_mapping[3]=array("mov","3gp","avi","mpg","mp4","flv"); # Video
$staticsync_extension_mapping[4]=array("flv");
# Uncomment and set the next line to specify a category tree field to use to store the retieved path information for each file. The tree structure will be automatically modified as necessary to match the folder strucutre within the sync folder.
# $staticsync_mapped_category_tree=50;
# Should the generated resource title include the sync folder path?
$staticsync_title_includes_path=true;
# Should the sync'd resource files be 'ingested' i.e. moved into ResourceSpace's own filestore structure?
# In this scenario, the sync'd folder merely acts as an upload mechanism. If path to metadata mapping is used then this allows metadata to be extracted based on the file's location.
$staticsync_ingest=false;
#
# StaticSync Path to metadata mapping
# ------------------------
# It is possible to take path information and map selected parts of the path to metadata fields.
# For example, if you added a mapping for '/projects/' and specified that the second level should be 'extracted' means that 'ABC' would be extracted as metadata into the specified field if you added a file to '/projects/ABC/'
# Hence meaningful metadata can be specified by placing the resource files at suitable positions within the static
# folder heirarchy.
# Use the line below as an example. Repeat this for every mapping you wish to set up
#	$staticsync_mapfolders[]=array
#		(
#		"match"=>"/projects/",
#		"field"=>10,
#		"level"=>2
#		);
#
# Suffix to use for alternative files folder
# If staticsync finds a folder in the same directory as a file with the same name as a file but with this suffix appended, then files in the folder will be treated as alternative files for the give file.
# For example a folder/file structure might look like:
# /staticsync_folder/myfile.jpg
# /staticsync_folder/myfile.jpg_alternatives/alternative1.jpg
# /staticsync_folder/myfile.jpg_alternatives/alternative2.jpg
# /staticsync_folder/myfile.jpg_alternatives/alternative3.jpg
# NOTE: Alternative file processing only works when $staticsync_ingest is set to 'true'.
$staticsync_alternatives_suffix="_alternatives";

# End of StaticSync settings
# ------------------------------------------------------------------------------------------------------------------














#
# ------------------------- Development Items -------------------------
#
#
# The items below are under development. Functionality is incomplete. You are advised to leave these switched off unless you have the ability to correct any problems yourself.

# Display a breadcrumbs trail (list of pages previously visited to aid navigation)
$breadcrumbs=false;

# Enable multi-lingual free text fields
# By default, only the checkbox list/dropdown fields can be multilingual by using the special syntax when defining
# the options. However, setting the below to true means that free text fields can also be multi-lingual. Several text boxes appear when entering data so that translations can be entered.
$multilingual_text_fields=false;

# Allow to selectively disable upload methods.
# Controls are :
# - single_upload            : Enable / disable "Add Single Resource".
# - in_browser_upload        : Enable / disable "Add Resource Batch - In Browser (Flash)".
# - in_browser_upload_java   : Enable / disable "Add Resource Batch - In Browser (Java)".
# - fetch_from_ftp           : Enable / disable "Add Resource Batch - Fetch from FTP server".
# - fetch_from_local_folder  : Enable / disable "Add Resource Batch - Local upload".
$upload_methods = array(
		'single_upload' => true,
		'in_browser_upload' => true,
		'in_browser_upload_java' => true,
		'fetch_from_ftp' => true,
		'fetch_from_local_folder' => true,
	);

# Allow to change the location of the upload folder, so that it is not in the
# web visible path. Relative and abolute paths are allowed.
$local_ftp_upload_folder = 'upload/';

# Set path to Unoconv (a python-based bridge to OpenOffice) to allow document conversion to PDF.
## $unoconv_path="/usr/bin";
# Files with these extensions will be passed to unoconv (if enabled above) for conversion to PDF and auto thumb-preview generation.
# Default list taken from http://svn.rpmforge.net/svn/trunk/tools/unoconv/docs/formats.txt
$unoconv_extensions=array("doc","docx","odt","odp","html","rtf","txt","ppt","pptx","sxw","sdw","html","psw","rtf","sdw","pdb","bib","txt","ltx","sdd","sda","odg","sdc");

# Uncomment to set a point in time where collections are considered 'active' and appear in the drop-down. 
# This is based on creation date for now. Older collections are effectively 'archived', but accessible through Manage My Collections.
# You can use any English-language strings supported by php's strtotime() function.
# $active_collections="-3 months";

# Set this to true to separate related resource results into separate sections (ie. PDF, JPG)
$sort_relations_by_filetype=false;

# If collections have at least one video, enable multi-playback in the Video Playlist page. 
$video_playlists=false;

# Allow the addition of 'saved searches' to collections. 
$allow_save_search=true;

# Use the collection name in the downloaded zip filename when downloading collections as a zip file?
$use_collection_name_in_zip_name=false;

# Enable a permanently visible 'themes bar' on the left hand side of the screen for quick access to themes.
$use_theme_bar=false;

# Use pdfinfo to extract document size in order to calculate an efficient ripping resolution 
# Useful mainly if you have odd sized pdfs, as you might in the printing industry; 
# ex: you have very large PDFs, such as 50 to 200 in (will greatly decrease ripping time and avoid overload) 
# or very small, such as PDFs < 5 in (will improve quality of the scr image)
$pdf_dynamic_rip=false;

# Allow for the creation of new site text entries from Manage Content
# note: this is intended for developers who create custom pages or hooks and need to have more manageable content,
$site_text_custom_create=false;

# use hit count functionality to track downloads rather than resource views.
$resource_hit_count_on_downloads=false;
$show_hitcount=false;

# Use checkboxes for selecting resources 
$use_checkboxes_for_selection=false;

# allow player for mp3 files
# player docs at http://flash-mp3-player.net/players/maxi/
$mp3_player=true;

# Show the performance metrics in the footer (for debug)
$config_show_performance_footer=false;

$use_phpmailer=false;

# Allow to disable thumbnail generation during batch resource upload from FTP or local folder.
# In addition to this option, a multi-thread thumbnail generation script is available in the batch
# folder (create_previews.php). You can use it as a cron job, or manually.
$enable_thumbnail_creation_on_upload = true;

# Create XML metadata dump files in the resource folder?
# This ensures that your metadata is kept in a readable format next to each resource file and may help
# to avoid data obsolescence / future migration. Also, potentially a useful additional backup.
$xml_metadump=true;

# Configures mapping between metadata and Dublin Core fields, which are used in the XML metadata dump instead if a match is found.
$xml_metadump_dc_map=array
	(
	"title" => "title",
	"caption" => "description",
	"date" => "date"
	);
	
# Use Plugins Manager
$use_plugins_manager = true;

#Disable geocoding features?
$disable_geocoding = true;

# To enable Google Maps Geolocation displays, uncomment the following line and 
# add your Google Maps API Key. Requires exiftool.
# Get an api key at: http://code.google.com/apis/maps/signup.html
#$gmaps_apikey = '';


	
# Data Joins -- Developer's tool to allow joining resource field data to the search query.
# $thumbs_display_fields array items are already joined. These are fields in addition to the $thumbs_display_fields array, 
# that you want available for other, typically plugin, purposes, in order to avoid an excessive query count.
# ex. $data_joins=array(13); to add the expiry date to the general search query result.  
$data_joins=array();

# Use the new 'frameless collections' mode that uses an AJAX driven 'collection summary' box on the right hand side instead of the collection frame. May be more suitable for intranets etc. that might work better without frames.
$frameless_collections=false;

# QuickLook previews (Mac Only)
# If configured, attempt to produce a preview for files using Mac OS-X's built in QuickLook preview system which support multiple files.
# This requires 'qlpreview', available from http://www.hamsoftengineering.com/codeSharing/qlpreview/qlpreview.html
#
# IMPORTANT! Note that currently, previews will only work when previews are generated by a normal system user as an offline cron job, i.e. using batch/create_previews.php
# It seems that the '_www' user that Apache runs as does not have permission to execute some part of the QuickLook system and generic icons instead of actual document previews are produced.
# Hopefully a work-around will be found shortly. Until then, QuickLook previews will remain a 'development item'.
#
# $qlpreview_path="/usr/bin";
#
# A list of extensions that QLPreview should NOT be used for.
$qlpreview_exclude_extensions=array("tif","tiff");



# Log developer debug information to the debug log (filestore/tmp/debug.txt)?
$debug_log=false;


# Enable Metadata Templates. This should be set to the ID of the resource type that you intend to use for metadata templates.
# Metadata templates can be selected on the resource edit screen to pre-fill fields.
# The intention is that you will create a new resource type named "Metadata Template" and enter its ID below.
# This resource type can be hidden from view if necessary, using the restrictive resource type permission.
#
# Metadata template resources act a little differently in that they have editable fields for all resource types. This is so they can be used with any 
# resource type, e.g. if you complete the photo fields then these will be copied when using this template for a photo resource.
# 
# $metadata_template_resource_type=5;
#
# The ability to set that a different field should be used for 'title' for metadata templates, so that the original title field can still be used for template data
# $metadata_template_title_field=10;



?>
