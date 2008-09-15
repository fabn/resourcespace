<?

$mysql_server="localhost";	# Use 'localhost' if MySQL is installed on the same server as your web server.
$mysql_username="root";		# MySQL username
$mysql_password="";			# MySQL password
$mysql_db="resourcespace";			# MySQL database name

$secure=false; # Using HTTPS?
$development=false; # Development mode?
$baseurl="http://my.site/resourcespace"; # The 'base' web address for this installation. Note: no trailing slash
$email_from="resourcespace@my.site"; # Where e-mails appear to come from
$email_notify="resourcespace@my.site"; # Where resource/research/user requests are sent
$spider_password="TBTT6FD"; # The password required for spider.php - IMPORTANT - randomise this for each new installation. Your resources will be readable by anyone that knows this password.

include "version.php";
$applicationname="ResourceSpace"; # The name of your implementation / installation (e.g. 'MyCompany Resource System')

# Available languages
$defaultlanguage="en"; # default language, uses iso codes (en, es etc.)
$languages["en"]="British English";
$languages["us"]="American English";
$languages["de"]="Deutsch";
$languages["es"]="Español";
$languages["fr"]="Français";
$languages["it"]="Italiano";
$languages["nl"]="Nederlands";
$languages["no"]="Norsk";
$languages["pt"]="Português";

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

# Scramble resource paths? If this is a public installation then this is a very wise idea.
# Set the scramble key to be a hard-to-guess string (similar to a password).
# To disable, set to the empty string ("").
$scramble_key="abcdef123";

# search params
# Common keywords to ignore both when searching and when indexing.
$noadd=array("", "a","the","this","then","another","is","with","in","and","where","how","on","of","to", "from", "at", "for", "-", "by", "be");
$suggest_threshold=-1; # How many results trigger the 'suggestion' feature, -1 disables the feature
$max_results=50000;
$minyear=1980; # The year of the earliest resource record, used for the date selector on the search form. Unless you are adding existing resources to the system, probably best to set this to the current year at the time of installation.

# Set folder for home images. Ex: "gfx/homeanim/mine/" 
# Files should be numbered sequentially, and will be auto-counted.
$homeanim_folder="gfx/homeanim/gfx/";

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

# Which field do we drop the EXIF data in to?
$exif_comment=18;
$exif_model=52;
$exif_date=12;

# Which field do we drop the original filename in to?
$filename_field=51;

# If using imagemagick, uncomment and set next 2 lines
# $imagemagick_path="/sw/bin";
# $ghostscript_path="/sw/bin";
# If using imagemagick, should colour profiles be preserved? (for larger sizes only - above 'scr')
$imagemagick_preserve_profiles=false;
$imagemagick_quality=90; # JPEG quality (0=worst quality/lowest filesize, 100=best quality/highest filesize)

# If using imagemagick for PDF, EPS and PS files, up to how many pages should be extracted for the previews?
# If this is set to more than one the user will be able to page through the PDF file.
 $pdf_pages=30;

# If using ffmpeg, uncomment and set next 2 lines.
# $ffmpeg_path="/Applications/ffmpegX.app/Contents/Resources";

# Create a preview video for ffmpeg compatible files? A FLV (Flash Video) file will automatically be produced for supported file types (most video types - AVI, MOV, MPEG etc.)
$ffmpeg_preview=true; 
$ffmpeg_preview_seconds=20; # how many seconds to preview


# Allow users to request accounts?
$allow_account_request=true;

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
$research_request=true;

# Country search in the right nav? (requires a field with the short name 'country')
$country_search=false;

# Country sort in the search results? (requires that the 'country' field maps to the 'country' database column on resource)
$country_sort=false;

# Enable themes (promoted collections intended for showcasing selected resources)
$enable_themes=true;

# Use the themes page as the home page?
$use_theme_as_home=false;

# Show images along with theme category headers (image selected is the most popular within the theme category)
$theme_images=true;

# How many levels of theme category to show.
# If this is set to more than one, a dropdown box will appear to allow browsing of theme sub-levels
# Maximum of 3 category levels (so with the themes themselves, a total of 4 categorisation levels for resources).
$theme_category_levels=1;

# Display a 'Recent' link in the top navigation (goes to View New Material)
# Removes the 'view new material' link from the simple search bar.
$recent_link=true;

# Display a 'My Collections' link in the top navigation
$mycollections_link=false;

# Require terms for download?
$terms_download=false;

# Require terms on first login?
$terms_login=false;

# In the collection frame, show or hide thumbnails by default? ("hide" is better if collections are not going to be heavily used).
$thumbs_default="show";



# How many results per page? (default)
$default_perpage=48;

# for sync
$syncdir="/path/to/static/files";
$nogo="[folder1]";
$type=1;

# Group based upload folders? (separate local upload folders for each group)
$groupuploadfolders=false;

# Enable order by rating? (require rating field updating to rating column)
$orderbyrating=false;

# Zip command to use to create zip archive (uncomment to enable download of collections as a zip file)
# $zipcommand="zip -j";

# Enable speed tagging feature? (development)
$speedtagging=false;
$speedtaggingfield=1;

# A list of types which get the extra video icon in the search results
$videotypes=array(3);

# Sets the default colour theme (defaults to blue)
$defaulttheme="greyblu";

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

# Reordering, captioning and ranking of collections
$collection_reorder_caption=false;

# Footer text applied to all e-mails (blank by default)
$emailfooter="";

# Contact Sheet feature, and whether contact sheet becomes resource.
# Requires ImageMagick/Ghostscript.
$contact_sheet=true;
# Produce a separate resource file when creating contact sheets?
$contact_sheet_resource=false; 
# Ajax previews in contact sheet configuration. 
$contact_sheet_previews=true;

# If making a contact sheet with list sheet style, use these fields in contact sheet:
$config_sheetlist_fields = array(8);

# Use SWFUpload (in browser batch upload) for user contributions as opposed to standard single uploads
$usercontribute_swfupload=true;

# Show related themes and public collections panel on Resource View page.
$show_related_themes=true;

# Multi-lingual support for e-mails. Try switching this to true if e-mail links aren't working and ASCII characters alone are required (e.g. in the US).
$disable_quoted_printable_enc=false;

# Enable small thumbnails option
$smallthumbs=true;

# Install Exiftool and set this path to experiment with metadata-writing
# $exiftool_path="/usr/local/bin";
$exiftool_remove_existing=false; # Set to true to strip out existing EXIF,IPTC,XMP metadata when adding metadata to resources.

# Watermarking - generate watermark images for 'internal' (thumb/preview) images.
# Groups with the 'w' permission will see these watermarks.
# Uncomment and set to the location of a watermark graphic.
# NOTE: only available when ImageMagick is installed.
# $watermark="gfx/watermark.png";

# Simple search even more simple
# Set to 'true' to make the simple search bar more basic, with just the single search box.
$basic_simple_search=false;

# Options to show/hide the box-links on the home page
$home_themeheaders=false;
$home_themes=true;
$home_mycollections=true;
$home_helpadvice=true;

# Use original filename when downloading a file?
$original_filenames_when_downloading=false;

# Display a 'new' flag next to new themes (added < 1 month ago)
$flag_new_themes=true;

# Create file checksums? (experimental)
$file_checksums=false;

# Display the advanced search as a 'search' link in the top navigation
$advanced_search_nav=false;

# Default group when adding new users;
$default_group=2;

# Enable 'custom' access level?
# Allows fine-grained control over access to resources.
# You may wish to disable this if you are using metadata based access control (search filter on the user group)
$custom_access=true;

# If true: if search keyword is numeric then search for resource id
$config_search_for_number=true;

# Display the download as a 'save as' link instead of redirecting the browser to the download (which sometimes causes a security warning).
# For the Opera browser this will always be enabled regardless of the below setting as Opera does not warn about failed downloads (so the download looks broken).
$save_as=false;


#
# ----------------- Development Items ---------------------
#
# The items below are under development. Functionality is incomplete. You are advised to leave these switched off.

# Display a breadcrumbs trail (list of pages previously visited to aid navigation)
$breadcrumbs=false;




?>