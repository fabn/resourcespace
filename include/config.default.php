<?

$mysql_server="localhost";	# Use 'localhost' if MySQL is installed on the same server as your web server.
$mysql_username="root";		# MySQL username
$mysql_password="";			# MySQL password
$mysql_db="resourcespace";			# MySQL database name

$secure=false; # Using HTTPS?
$development=false; # Development mode?
$baseurl="http://my.site.net/resourcespace"; # The 'base' web address for this installation. Note: no trailing slash
$email_from="resourcespace@my.site.net"; # Where e-mails appear to come from
$email_notify="resourcespace@my.site.net"; # Where resource/research/user requests are sent
$spider_password="TBTT6FD"; # The password required for spider.php - IMPORTANT - randomise this for each new installation. Your resources will be readable by anyone that knows this password.

include "version.php";
$applicationname="ResourceSpace"; # The name of your implementation / installation (e.g. 'MyCompany Resource System')

# Available languages
$defaultlanguage="en"; # default language, uses iso codes (en, es etc.)
$languages["en"]="English";
$languages["de"]="Deutsch";
$languages["es"]="Español";
$languages["fr"]="Français";
$languages["nl"]="Nederlands";
$languages["no"]="Norsk";
$languages["pt"]="Português";
$languages["it"]="Italiano";

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
$homeimages=3; # How many images are on the homepage slideshow?

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

# If using ffmpeg, uncomment and set next 2 lines.
# $ffmpeg_path="/Applications/ffmpegX.app/Contents/Resources";

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

# Use the themes page as the home page?
$use_theme_as_home=false;

# Display a 'Recent' link in the top navigation (goes to View New Material)
$recent_link=true;

# Display a 'My Collections' link in the top navigation
$mycollections_link=false;

# Require terms for download?
$terms_download=false;

# Require terms on first login?
$terms_login=false;

# In the collection frame, show or hide thumbnails by default? ("hide" is better if collections are not going to be heavily used).
$thumbs_default="show";

# Show images along with theme category headers (image selected is the most popular within the theme category)
$theme_images=true;

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

# Enable AJAX popup info box on search results.
$infobox=true;

# Zip command to use to create zip archive (uncomment to enable download of collections as a zip file)
# $zipcommand="zip -j";

# Enable speed tagging feature? (development)
$speedtagging=false;
$speedtaggingfield=1;

# A list of types which get the extra video icon in the search results
$videotypes=array(3);

# Uncomment and set the next line to lock to one specific colour scheme (e.g. greyblu/whitegry).
# $userfixedtheme="whitegry";

# List of active plugins.
$plugins=array("");

# Uncomment and set the next line to allow anonymous access. The anonymous user will automatically be logged in
# to the account with the username specified.
# $anonymous_login="guest";

# Enable AJAX popup info box on search results.
$infobox=true;
$infobox_fields=array(18,10,29,53);

# Reordering, captioning and ranking of collections
$collection_reorder_caption=false;

# Footer text applied to all e-mails (blank by default)
$emailfooter="";

# Contact Sheet feature, and whether contact sheet becomes resource.
$contact_sheet=true;
$contact_sheet_resource=false;

# Use SWFUpload (in browser batch upload) for user contributions as opposed to standard single uploads
$usercontribute_swfupload=true;


?>
