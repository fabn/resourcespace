<?php
# English
# Language File for ResourceSpace
# -------
# Note: when translating to a new language, preserve the original case if possible.

# User group names (for the default user groups)
$lang["usergroup-administrators"]="Administrators";
$lang["usergroup-general_users"]="General Users";
$lang["usergroup-super_admin"]="Super Admin";
$lang["usergroup-archivists"]="Archivists";
$lang["usergroup-restricted_user_-_requests_emailed"]="Restricted User - Requests Emailed";
$lang["usergroup-restricted_user_-_requests_managed"]="Restricted User - Requests Managed";
$lang["usergroup-restricted_user_-_payment_immediate"]="Restricted User - Payment Immediate";
$lang["usergroup-restricted_user_-_payment_invoice"]="Restricted User - Payment Invoice";

# Resource type names (for the default resource types)
$lang["resourcetype-photo"]="Photo";
$lang["resourcetype-document"]="Document";
$lang["resourcetype-video"]="Video";
$lang["resourcetype-audio"]="Audio";
$lang["resourcetype-global_fields"]="Global Fields";
$lang["resourcetype-archive_only"]="Archive Only";

# Image size names (for the default image sizes)
$lang["imagesize-thumbnail"]="Thumbnail";
$lang["imagesize-preview"]="Preview";
$lang["imagesize-screen"]="Screen";
$lang["imagesize-low_resolution_print"]="Low resolution print";
$lang["imagesize-high_resolution_print"]="High resolution print";
$lang["imagesize-collection"]="Collection";

# Field titles (for the default fields)
$lang["fieldtitle-keywords"]="Keywords";
$lang["fieldtitle-country"]="Country";
$lang["fieldtitle-title"]="Title";
$lang["fieldtitle-story_extract"]=$lang["storyextract"]="Story Extract";
$lang["fieldtitle-credit"]="Credit";
$lang["fieldtitle-date"]=$lang["date"]="Date";
$lang["fieldtitle-expiry_date"]="Expiry Date";
$lang["fieldtitle-caption"]="Caption";
$lang["fieldtitle-notes"]="Notes";
$lang["fieldtitle-named_persons"]="Named Person(s)";
$lang["fieldtitle-camera_make_and_model"]="Camera Make / Model";
$lang["fieldtitle-original_filename"]="Original Filename";
$lang["fieldtitle-video_contents_list"]="Video Contents List";
$lang["fieldtitle-source"]="Source";
$lang["fieldtitle-website"]="Website";
$lang["fieldtitle-artist"]="Artist";
$lang["fieldtitle-album"]="Album";
$lang["fieldtitle-track"]="Track";
$lang["fieldtitle-year"]="Year";
$lang["fieldtitle-genre"]="Genre";
$lang["fieldtitle-duration"]="Duration";
$lang["fieldtitle-channel_mode"]="Channel Mode";
$lang["fieldtitle-sample_rate"]="Sample Rate";
$lang["fieldtitle-audio_bitrate"]="Audio Bitrate";
$lang["fieldtitle-frame_rate"]="Frame Rate";
$lang["fieldtitle-video_bitrate"]="Video Bitrate";
$lang["fieldtitle-aspect_ratio"]="Aspect Ratio";
$lang["fieldtitle-video_size"]="Video Size";
$lang["fieldtitle-image_size"]="Image Size";
$lang["fieldtitle-extracted_text"]="Extracted Text";
$lang["fieldtitle-file_size"]=$lang["filesize"]="File Size";
$lang["fieldtitle-category"]="Category";
$lang["fieldtitle-subject"]="Subject";
$lang["fieldtitle-author"]="Author";

# Field types
$lang["fieldtype-text_box_single_line"]="Text Box (single line)";
$lang["fieldtype-text_box_multi-line"]="Text Box (multi-line)";
$lang["fieldtype-text_box_large_multi-line"]="Text Box (large multi-line)";
$lang["fieldtype-check_box_list"]="Check box list";
$lang["fieldtype-drop_down_list"]="Drop down list";
$lang["fieldtype-date_and_time"]="Date / Time";
$lang["fieldtype-expiry_date"]="Expiry Date";
$lang["fieldtype-category_tree"]="Category Tree";

# Property labels (for the default properties)
$lang["documentation-permissions"]="See <a href=../../documentation/permissions.txt target=_blank>the permissions help text file</a> for further information on permissions.";
$lang["property-reference"]="Reference";
$lang["property-name"]="Name";
$lang["property-permissions"]="Permissions";
$lang["information-permissions"]="NOTE: Global permissions from config may also be in effect";
$lang["property-fixed_theme"]="Fixed theme";
$lang["property-parent"]="Parent";
$lang["property-search_filter"]="Search filter";
$lang["property-edit_filter"]="Edit filter";
$lang["property-resource_defaults"]="Resource defaults";
$lang["property-override_config_options"]="Override config options";
$lang["property-email_welcome_message"]="Email welcome message";
$lang["information-ip_address_restriction"]="Wildcards are supported for IP address restrictions, e.g. 128.124.*";
$lang["property-ip_address_restriction"]="IP address restriction";
$lang["property-request_mode"]="Request mode";
$lang["property-allow_registration_selection"]="Allow registration selection";

$lang["property-resource_type_id"]="Resource type id";
$lang["information-allowed_extensions"]="If set, only files with the specified extensions are allowed upon upload to this type, e.g. jpg,gif";
$lang["property-allowed_extensions"]="Allowed extensions";

$lang["property-field_id"]="Field id";
$lang["property-title"]="Title";
$lang["property-resource_type"]="Resource Type";
$lang["property-field_type"]="Field Type";
$lang["information-options"]="<br /><b>Please note</b> - it is better to use the Manage Field Options function from the Manage Resources section of the Team Home to edit field options as existing data is automatically migrated when options are renamed.";
$lang["property-options"]="Options";
$lang["property-required"]="Required";
$lang["property-order_by"]="Order by";
$lang["property-index_this_field"]="Index this field";
$lang["information-enable_partial_indexing"]="Partial keyword indexing (prefix+infix indexing) should be used sparingly as it will significantly increase the index size. See the wiki for details.";
$lang["property-enable_partial_indexing"]="Enable partial indexing";
$lang["information-shorthand_name"]="Important: Shorthand name must be set for the field to appear on Advanced Search. It must contain only lowercase alphabetical characters - no spaces, numbers or symbols.";
$lang["property-shorthand_name"]="Shorthand name";
$lang["property-display_field"]="Display field";
$lang["property-enable_advanced_search"]="Enable advanced search";
$lang["property-enable_simple_search"]="Enable simple search";
$lang["property-use_for_find_similar_searching"]="Use for find similar searching";
$lang["property-iptc_equiv"]="Iptc equiv";
$lang["property-display_template"]="Display template";
$lang["property-value_filter"]="Value filter";
$lang["property-tab_name"]="Tab name";
$lang["property-smart_theme_name"]="Smart theme name";
$lang["property-exiftool_field"]="Exiftool field";
$lang["property-exiftool_filter"]="Exiftool filter";
$lang["property-help_text"]="Help text";
$lang["information-display_as_dropdown"]="Checkbox lists and dropdown boxes: display as a dropdown box on the advanced search? (the default is to display both as checkbox lists on the advanced search page to enable OR functionality)";
$lang["property-display_as_dropdown"]="Display as dropdown";
$lang["property-external_user_access"]="External user access";
$lang["property-autocomplete_macro"]="Autocomplete macro";
$lang["property-hide_when_uploading"]="Hide when uploading";

$lang["property-query"]="Query";

$lang["information-id"]="Note: 'Id' below MUST be set to a three character unique code";
$lang["property-id"]="Id";
$lang["property-width"]="Width";
$lang["property-height"]="Height";
$lang["property-pad_to_size"]="Pad to size";
$lang["property-internal"]="Internal";
$lang["property-allow_preview"]="Allow preview";
$lang["property-allow_restricted_download"]="Allow restricted download";

$lang["property-total_resources"]="Total resources";
$lang["property-total_keywords"]="Total keywords";
$lang["property-resource_keyword_relationships"]="Resource keyword relationships";
$lang["property-total_collections"]="Total collections";
$lang["property-collection_resource_relationships"]="Collection resource relationships";
$lang["property-total_users"]="Total users";


# Top navigation bar (also reused for page titles)
$lang["logout"]="Log Out";
$lang["contactus"]="Contact Us";
# next line
$lang["home"]="Home";
$lang["searchresults"]="Search Results";
$lang["themes"]="Themes";
$lang["mycollections"]="My Collections";
$lang["myrequests"]="My Requests";
$lang["collections"]="Collections";
$lang["mycontributions"]="My Contributions";
$lang["researchrequest"]="Research Request";
$lang["helpandadvice"]="Help & Advice";
$lang["teamcentre"]="Team Centre";
# footer link
$lang["aboutus"]="About Us";
$lang["interface"]="Interface";

# Search bar
$lang["simplesearch"]="Simple Search";
$lang["searchbutton"]="Search";
$lang["clearbutton"]="Clear";
$lang["bycountry"]="By Country";
$lang["bydate"]="By Date";
$lang["anyyear"]="Any year";
$lang["anymonth"]="Any month";
$lang["anyday"]="Any day";
$lang["anycountry"]="Any country";
$lang["resultsdisplay"]="Results Display";
$lang["xlthumbs"]="X-Large";
$lang["largethumbs"]="Large";
$lang["smallthumbs"]="Small";
$lang["list"]="List";
$lang["perpage"]="per page";

$lang["gotoadvancedsearch"]="Go to Advanced Search";
$lang["viewnewmaterial"]="View New Material";
$lang["researchrequestservice"]="Research Request Service";

# Team Centre
$lang["manageresources"]="Manage Resources";
$lang["overquota"]="Over disk space quota; cannot add resources";
$lang["managearchiveresources"]="Manage Archive Resources";
$lang["managethemes"]="Manage Themes";
$lang["manageresearchrequests"]="Manage Research Requests";
$lang["manageusers"]="Manage Users";
$lang["managecontent"]="Manage Content";
$lang["viewstatistics"]="View Statistics";
$lang["viewreports"]="View Reports";
$lang["viewreport"]="View Report";
$lang["treeobjecttype-report"]=$lang["report"]="Report";
$lang["sendbulkmail"]="Send Bulk Mail";
$lang["systemsetup"]="System Setup";
$lang["usersonline"]="Users currently online (idle time minutes)";
$lang["diskusage"]="Disk usage";
$lang["available"]="available";
$lang["used"]="used";
$lang["free"]="free";
$lang["editresearch"]="Edit Research";
$lang["editproperties"]="Edit Properties";
$lang["selectfiles"]="Select Files";
$lang["searchcontent"]="Search content";
$lang["ticktodeletehelp"]="Tick to delete this section";
$lang["createnewhelp"]="Create a new help section";
$lang["searchcontenteg"]="(page, name, text)";
$lang["copyresource"]="Copy Resource";
$lang["resourceidnotfound"]="The resource ID was not found";
$lang["inclusive"]="(inclusive)";
$lang["pluginssetup"]="Manage Plugins";
$lang["pluginmanager"]="Plugin Manager";
$lang["users"]="users";


# Team Centre - Bulk E-mails
$lang["emailrecipients"]="E-mail Recipient(s)";
$lang["emailsubject"]="E-mail Subject";
$lang["emailtext"]="E-mail Text";
$lang["emailhtml"]="Enable HTML support - mail body must use HTML formatting";
$lang["send"]="Send";
$lang["emailsent"]="The e-mail has been sent.";
$lang["mustspecifyoneuser"]="You must specify at least one user";
$lang["couldnotmatchusers"]="Could not match all the usernames, or usernames were duplicated";

# Team Centre - User management
$lang["comments"]="Comments";

# Team Centre - Resource management
$lang["viewuserpending"]="View User Contributed Resources Pending Review";
$lang["userpending"]="User Contributed Resources Pending Review";
$lang["viewuserpendingsubmission"]="View User Contributed Resources Pending Submission";
$lang["userpendingsubmission"]="User Contributed Resources Pending Submission";
$lang["searcharchivedresources"]="Search Archived Resources";
$lang["viewresourcespendingarchive"]="View Resources Pending Archive";
$lang["resourcespendingarchive"]="Resources Pending Archive";
$lang["uploadresourcebatch"]="Upload Resource Batch";
$lang["uploadinprogress"]="Upload and Resize in Progress";
$lang["transferringfiles"]="Transferring files, please wait.";
$lang["donotmoveaway"]="IMPORTANT: Do not navigate away from this page until the upload has completed!";
$lang["pleaseselectfiles"]="Please select one or more files to upload.";
$lang["resizingimage"]="Resizing image";
$lang["uploaded"]="Uploaded";
$lang["andresized"]="and resized";
$lang["uploadfailedfor"]="Upload failed for"; # E.g. upload failed for abc123.jpg
$lang["uploadcomplete"]="Upload complete.";
$lang["resourcesuploadedok"]="resources uploaded OK"; # E.g. 17 resources uploaded OK
$lang["failed"]="failed";
$lang["clickviewnewmaterial"]="Click 'View New Material' to see uploaded resources.";
$lang["specifyftpserver"]="Specify Remote FTP Server";
$lang["ftpserver"]="FTP Server";
$lang["ftpusername"]="FTP Username";
$lang["ftppassword"]="FTP Password";
$lang["ftpfolder"]="FTP Folder";
$lang["connect"]="Connect";
$lang["uselocalupload"]="OR: Use local 'upload' folder instead of remote FTP server";

# User contributions
$lang["contributenewresource"]="Contribute New Resource";
$lang["viewcontributedps"]="View My Contributions - Pending Submission";
$lang["viewcontributedpr"]="View My Contributions - Pending Resource Team Review";
$lang["viewcontributedsubittedl"]="View My Contributions - Active";
$lang["contributedps"]="My Contributions - Pending Submission";
$lang["contributedpr"]="My Contributions - Pending Resource Team Review";
$lang["contributedsubittedl"]="My Contributions - Active";

# Collections
$lang["editcollection"]="Edit Collection";
$lang["editcollectionresources"]="Edit Collection Previews...";
$lang["access"]="Access";
$lang["private"]="Private";
$lang["public"]="Public";
$lang["attachedusers"]="Attached Users";
$lang["themecategory"]="Theme Category";
$lang["theme"]="Theme";
$lang["newcategoryname"]="OR: Enter a new theme category name...";
$lang["allowothersaddremove"]="Allow other users to add/remove resources";
$lang["resetarchivestatus"]="Reset archive status for all resources in collection";
$lang["editallresources"]="Edit all resources in collection";
$lang["editresources"]="Edit Resources";
$lang["multieditnotallowed"]="Mult-edit not allowed - all the resources are not in the same status or of the same type.";
$lang["emailcollection"]="E-mail Collection";
$lang["collectionname"]="Collection Name";
$lang["collectionid"]="Collection ID";
$lang["collectionidprefix"]="Col_ID";
$lang["emailtousers"]="E-mail to users<br><br><b>For existing users</b> start typing the user's name to search, click the user when found and then click plus<br><br><b>For non-registered users</b> type the e-mail address then click plus";
$lang["removecollectionareyousure"]="Are you sure you wish to remove this collection from your list?";
$lang["managemycollections"]="Manage 'My Collections'";
$lang["createnewcollection"]="Create New Collection";
$lang["findpubliccollection"]="Public Collections";
$lang["searchpubliccollections"]="Search Public Collections";
$lang["addtomycollections"]="Add to my collections";
$lang["action-addtocollection"]="Add to collection";
$lang["action-removefromcollection"]="Remove from collection";
$lang["addtocollection"]="Add to collection";
$lang["cantmodifycollection"]="You can't modify this collection.";
$lang["currentcollection"]="Current Collection";
$lang["viewcollection"]="View collection";
$lang["viewall"]="View All";
$lang["action-editall"]="Edit All";
$lang["hidethumbnails"]="Hide Thumbs";
$lang["showthumbnails"]="Show Thumbs";
$lang["contactsheet"]="Contact Sheet";
$lang["mycollection"]="My Collection";
$lang["editresearchrequests"]="Edit research requests";
$lang["research"]="Research";
$lang["savedsearch"]="Saved Search";
$lang["mustspecifyoneusername"]="You must specify at least one username";
$lang["couldnotmatchallusernames"]="Could not match all the usernames";
$lang["emailcollectionmessage"]="has e-mailed you a collection of resources from $applicationname which has been added to your 'My Collections' page."; # suffixed to user name e.g. "Fred has e-mailed you a collection..."
$lang["emailcollectionmessageexternal"]="has e-mailed you a collection of resources from $applicationname."; # suffixed to user name e.g. "Fred has e-mailed you a collection..."
$lang["clicklinkviewcollection"]="Click the link below to view the collection.";
$lang["zippedcollectiontextfile"]="Include text file with resource/collection data.";
$lang["copycollectionremoveall"]="Remove all resources before copying";
$lang["purgeanddelete"]="Purge";
$lang["purgecollectionareyousure"]="Are you sure you want to remove this collection AND DELETE all resources in it?";
$lang["collectionsdeleteempty"]="Delete Empty Collections";
$lang["collectionsdeleteemptyareyousure"]="Are you sure you want to delete all of your own empty collections?";
$lang["collectionsnothemeselected"]="You must select or enter a theme category name.";
$lang["downloaded"]="Downloaded";
$lang["contents"]="Contents";
$lang["forthispackage"]="for this package";
$lang["didnotinclude"]="Did not include";
$lang["selectcollection"]="Select Collection";
$lang["total"]="Total";
$lang["ownedbyyou"]="owned by you";

# Resource create / edit / view
$lang["createnewresource"]="Create New Resource";
$lang["treeobjecttype-resource_type"]=$lang["resourcetype"]="Resource Type";
$lang["resourcetypes"]="Resource Types";
$lang["deleteresource"]="Delete Resource";
$lang["downloadresource"]="Download Resource";
$lang["rightclicktodownload"]="Right click this link and choose 'Save Target As' to download your resource..."; # For Opera/IE browsers only
$lang["downloadinprogress"]="Download In Progress";
$lang["editmultipleresources"]="Edit Multiple Resources";
$lang["editresource"]="Edit Resource";
$lang["resources_selected-1"]="1 resource selected"; # 1 resource selected
$lang["resources_selected-2"]="%number resources selected"; # e.g. 17 resources selected
$lang["image"]="Image";
$lang["previewimage"]="Preview Image";
$lang["file"]="File";
$lang["upload"]="Upload";
$lang["action-upload"]="Upload";
$lang["uploadafile"]="Upload a file";
$lang["replacefile"]="Replace file";
$lang["imagecorrection"]="Edit Preview Images";
$lang["previewthumbonly"]="(preview / thumbnail only)";
$lang["rotateclockwise"]="Rotate clockwise";
$lang["rotateanticlockwise"]="Rotate anti-clockwise";
$lang["increasegamma"]="Brighten previews";
$lang["decreasegamma"]="Darken previews";
$lang["restoreoriginal"]="Restore original";
$lang["recreatepreviews"]="Recreate previews";
$lang["retrypreviews"]="Retry preview creation";
$lang["specifydefaultcontent"]="Specify Default Content For New Resources";
$lang["properties"]="Properties";
$lang["relatedresources"]="Related Resources";
$lang["indexedsearchable"]="Indexed, searchable fields";
$lang["clearform"]="Clear Form";
$lang["similarresources"]="similar resources"; # e.g. 17 similar resources
$lang["similarresource"]="similar resource"; # e.g. 1 similar resource
$lang["nosimilarresources"]="No similar resources";
$lang["emailresource"]="E-mail";
$lang["resourcetitle"]="Resource Title";
$lang["requestresource"]="Request Resource";
$lang["action-viewmatchingresources"]="View matching resources";
$lang["nomatchingresources"]="No matching resources";
$lang["matchingresources"]="matching resources"; # e.g. 17 matching resources
$lang["advancedsearch"]="Advanced Search";
$lang["archiveonlysearch"]="Archive Only Search";
$lang["allfields"]="All Fields";
$lang["typespecific"]="Type Specific";
$lang["youfound"]="You found"; # e.g. you found 17 resources
$lang["youfoundresources"]="resources"; # e.g. you found 17 resources
$lang["youfoundresource"]="resource"; # e.g. you found 1 resource
$lang["display"]="Display"; # e.g. Display: thumbnails / list
$lang["sortorder"]="Sort Order";
$lang["relevance"]="Relevance";
$lang["asadded"]="As added";
$lang["popularity"]="Popularity";
$lang["rating"]="Rating";
$lang["colour"]="Colour";
$lang["jumptopage"]="Jump to page";
$lang["jump"]="Jump";
$lang["titleandcountry"]="Title / Country";
$lang["torefineyourresults"]="To refine your results, try";
$lang["verybestresources"]="The very best resources";
$lang["addtocurrentcollection"]="Add to current collection";
$lang["addresource"]="Add Single Resource";
$lang["addresourcebatch"]="Add Resource Batch";
$lang["fileupload"]="File Upload";
$lang["clickbrowsetolocate"]="Click browse to locate a file";
$lang["resourcetools"]="Resource Tools";
$lang["fileinformation"]="File Information";
$lang["options"]="Options";
$lang["previousresult"]="Previous Result";
$lang["viewallresults"]="View All Results";
$lang["nextresult"]="Next Result";
$lang["pixels"]="pixels";
$lang["download"]="Download";
$lang["preview"]="Preview";
$lang["fullscreenpreview"]="Full screen preview";
$lang["originalfileoftype"]="Original ? File"; # ? will be replaced, e.g. "Original PDF File"
$lang["fileoftype"]="? File"; # ? will be replaced, e.g. "MP4 File"
$lang["log"]="Log";
$lang["resourcedetails"]="Resource Details";
$lang["offlineresource"]="Offline Resource";
$lang["request"]="Request";
$lang["searchforsimilarresources"]="Search for similar resources";
$lang["clicktoviewasresultset"]="Click to view these resources as a result set";
$lang["searchnomatches"]="Your search did not match any resources.";
$lang["try"]="Try";
$lang["tryselectingallcountries"]="Try selecting <b>all</b> in the countries box, or";
$lang["tryselectinganyyear"]="Try selecting <b>any year</b> in the year box, or";
$lang["tryselectinganymonth"]="Try selecting <b>any month</b> in the month box, or";
$lang["trybeinglessspecific"]="Try being less specific by";
$lang["enteringfewerkeywords"]="entering fewer search keywords."; # Suffixed to any of the above 4 items e.g. "Try being less specific by entering fewer search keywords"
$lang["match"]="match";
$lang["matches"]="matches";
$lang["inthearchive"]="in the archive";
$lang["nomatchesinthearchive"]="No matches in the archive";
$lang["savethissearchtocollection"]="Save search query to collection";
$lang["mustspecifyonekeyword"]="You must specify at least one search keyword.";
$lang["hasemailedyouaresource"]="has e-mailed you a resource."; # Suffixed to user name, e.g. Fred has e-mailed you a resource
$lang["clicktoviewresource"]="Click the link below to view the resource.";
$lang["statuscode"]="Status Code";

# Resource log - actions
$lang["resourcelog"]="Resource Log";
$lang["log-u"]="Uploaded file";
$lang["log-c"]="Created resource";
$lang["log-d"]="Downloaded file";
$lang["log-e"]="Edited resource field";
$lang["log-m"]="Edited resource field (multi-edit)";
$lang["log-E"]="Shared Resource via e-mail to ";//  + notes field
$lang["log-v"]="Viewed resource";
$lang["log-x"]="Deleted resource";
$lang["log-l"]="Logged in"; # For user entries only.
$lang["log-t"]="Transformed file";
$lang["log-s"]="Change status";
$lang["log-a"]="Change access";
$lang["log-r"]="Reverted metadata";

$lang["backtoresourceview"]="Back to resource view";

# Resource status
$lang["status"]="Status";
$lang["status-2"]="Pending Submission";
$lang["status-1"]="Pending Review";
$lang["status0"]="Active";
$lang["status1"]="Waiting to be archived";
$lang["status2"]="Archived";
$lang["status3"]="Deleted";

# Charts
$lang["activity"]="Activity";
$lang["summary"]="summary";
$lang["mostinaday"]="Most in a day";
$lang["totalfortheyear"]="Total for the year";
$lang["totalforthemonth"]="Total for the month";
$lang["dailyaverage"]="Daily average for active days";
$lang["nodata"]="No data for this period.";
$lang["max"]="Max"; # i.e. maximum
$lang["statisticsfor"]="Statistics for"; # e.g. Statistics for 2007
$lang["printallforyear"]="Print all statistics for this year";

# Log in / user account
$lang["nopassword"]="Click here to apply for an account";
$lang["forgottenpassword"]="Click here if you have forgotten your password";
$lang["keepmeloggedin"]="Keep me logged in at this workstation";
$lang["columnheader-username"]=$lang["username"]="Username";
$lang["password"]="Password";
$lang["login"]="Log in";
$lang["loginincorrect"]="Sorry, your login details were incorrect.<br /><br />If you have forgotten your password,<br />use the link above to request a new one.";
$lang["accountexpired"]="Your account has expired. Please contact the resources team.";
$lang["useralreadyexists"]="An account with that e-mail or username already exists, changes not saved";
$lang["useremailalreadyexists"]="An account with that e-mail already exists.";
$lang["ticktoemail"]="E-mail this user their username and new password";
$lang["ticktodelete"]="Tick to delete this user";
$lang["edituser"]="Edit User";
$lang["columnheader-full_name"]=$lang["fullname"]="Full Name";
$lang["email"]="E-mail";
$lang["columnheader-e-mail_address"]=$lang["emailaddress"]="E-mail Address";
$lang["suggest"]="Suggest";
$lang["accountexpiresoptional"]="Account Expires (optional)";
$lang["lastactive"]="Last Active";
$lang["lastbrowser"]="Last Browser";
$lang["searchusers"]="Search Users";
$lang["createuserwithusername"]="Create user with username...";
$lang["emailnotfound"]="The e-mail address specified could not be found";
$lang["yourname"]="Your Full Name";
$lang["youremailaddress"]="Your E-mail Address";
$lang["sendreminder"]="Send Reminder";
$lang["sendnewpassword"]="Send New Password";
$lang["requestuserlogin"]="Request User Login";

# Research request
$lang["nameofproject"]="Name of Project";
$lang["descriptionofproject"]="Description of Project";
$lang["descriptionofprojecteg"]="(eg. Audience / Style / Subject / Geographical focus)";
$lang["deadline"]="Deadline";
$lang["nodeadline"]="No deadline";
$lang["noprojectname"]="You must specify a project name";
$lang["noprojectdescription"]="You must specify a project description";
$lang["contacttelephone"]="Contact Telephone";
$lang["finaluse"]="Final Use";
$lang["finaluseeg"]="(eg. Powerpoint / Leaflet / Poster)";
$lang["noresourcesrequired"]="Number of resources required for final product?";
$lang["shaperequired"]="Shape of images required";
$lang["portrait"]="Portrait";
$lang["landscape"]="Landscape";
$lang["square"]="Square";
$lang["either"]="Either";
$lang["sendrequest"]="Send Request";
$lang["editresearchrequest"]="Edit Research Request";
$lang["requeststatus0"]=$lang["unassigned"]="Unassigned";
$lang["requeststatus1"]="In Progress";
$lang["requeststatus2"]="Complete";
$lang["copyexistingresources"]="Copy the resources in an existing collection to this research brief";
$lang["deletethisrequest"]="Tick to delete this request";
$lang["requestedby"]="Requested by";
$lang["requesteditems"]="Requested items";
$lang["assignedtoteammember"]="Assigned to team member";
$lang["typecollectionid"]="(Type collection ID below)";
$lang["researchid"]="Research ID";
$lang["assignedto"]="Assigned to";
$lang["createresearchforuser"]="Create research request for user";
$lang["searchresearchrequests"]="Search Research Requests";
$lang["requestasuser"]="Request as user";
$lang["haspostedresearchrequest"]="has posted a research request"; # username is suffixed to this
$lang["newresearchrequestwaiting"]="New Research Request Waiting";
$lang["researchrequestassignedmessage"]="Your research request has been assigned to a member of the team. Once we've completed the research you'll receive an e-mail with a link to all the resources that we recommend.";
$lang["researchrequestassigned"]="Research Request Assigned";
$lang["researchrequestcompletemessage"]="Your research request is complete and has been added to your 'My Collections' page.";
$lang["researchrequestcomplete"]="Research Request Completed";


# Misc / global
$lang["selectgroupuser"]="Select group/user...";
$lang["select"]="Select...";
$lang["add"]="Add";
$lang["create"]="Create";
$lang["treeobjecttype-group"]=$lang["group"]="Group";
$lang["confirmaddgroup"]="Are you sure you want to add all the members in this group?";
$lang["backtoteamhome"]="Back to team centre home";
$lang["columnheader-resource_id"]=$lang["resourceid"]="Resource ID";
$lang["id"]="ID";
$lang["todate"]="To Date";
$lang["fromdate"]="From Date";
$lang["day"]="Day";
$lang["month"]="Month";
$lang["year"]="Year";
$lang["hour-abbreviated"]="HH";
$lang["minute-abbreviated"]="MM";
$lang["itemstitle"]="Items";
$lang["tools"]="Tools";
$lang["created"]="Created";
$lang["user"]="User";
$lang["owner"]="Owner";
$lang["message"]="Message";
$lang["name"]="Name";
$lang["action"]="Action";
$lang["treeobjecttype-field"]=$lang["field"]="Field";
$lang["save"]="Save";
$lang["revert"]="Revert";
$lang["cancel"]="Cancel";
$lang["view"]="View";
$lang["type"]="Type";
$lang["text"]="Text";
$lang["yes"]="Yes";
$lang["no"]="No";
$lang["key"]="Key"; # e.g. explanation of icons on search page
$lang["languageselection"]="Language Selection";
$lang["language"]="Language";
$lang["changeyourpassword"]="Change Your Password";
$lang["yourpassword"]="Your Password";
$lang["newpassword"]="New Password";
$lang["newpasswordretype"]="New password (retype)";
$lang["passwordnotvalid"]="This is not a valid password";
$lang["passwordnotmatch"]="The entered passwords did not match";
$lang["wrongpassword"]="Incorrect password, please try again";
$lang["action-view"]="View";
$lang["action-preview"]="Preview";
$lang["action-viewmatchingresources"]="View Matching Resources";
$lang["action-expand"]="Expand";
$lang["action-select"]="Select";
$lang["action-download"]="Download";
$lang["action-email"]="E-mail";
$lang["action-edit"]="Edit";
$lang["action-delete"]="Delete";
$lang["action-deletecollection"]="Delete Collection";
$lang["action-revertmetadata"]="Revert Metadata";
$lang["confirm-revertmetadata"]="Are you sure you want to re-extract the original metadata from this file? This action will simulate a re-upload of the file, and you will lose any altered metadata.";
$lang["action-remove"]="Remove";
$lang["complete"]="Complete";
$lang["backtohome"]="Back to the home page";
$lang["backtohelphome"]="Back to help home";
$lang["backtosearch"]="Back to my search results";
$lang["backtoview"]="Resource View";
$lang["backtoeditresource"]="Back to edit resource";
$lang["backtouser"]="Back to user login";
$lang["termsandconditions"]="Terms and Conditions";
$lang["iaccept"]="I Accept";
$lang["contributedby"]="Contributed by";
$lang["format"]="Format";
$lang["notavailableshort"]="N/A";
$lang["allmonths"]="All months";
$lang["allgroups"]="All groups";
$lang["status-ok"]="OK";
$lang["status-fail"]="FAIL";
$lang["status-warning"]="WARNING";
$lang["status-notinstalled"]="Not installed";
$lang["status-never"]="Never";
$lang["softwareversion"]="? version"; # E.g. "PHP version"
$lang["softwarebuild"]="? Build"; # E.g. "ResourceSpace Build"
$lang["softwarenotfound"]="'?'  not found"; # ? will be replaced.
$lang["client-encoding"]="(client-encoding: %encoding)"; # %encoding will be replaced, e.g. client-encoding: utf8
$lang["browseruseragent"]="Browser User-Agent";
$lang['serverplatform']="Server Platform";
$lang["are_available-0"]="are available";
$lang["are_available-1"]="is available";
$lang["are_available-2"]="are available";
$lang["were_available-0"]="were available";
$lang["were_available-1"]="was available";
$lang["were_available-2"]="were available";
$lang["resource-0"]="resources";
$lang["resource-1"]="resource";
$lang["resource-2"]="resources";
$lang["status-note"]="NOTE";
$lang["action-changelanguage"]="Change Language";

# Pager
$lang["next"]="Next";
$lang["previous"]="Previous";
$lang["page"]="Page";
$lang["of"]="of"; # e.g. page 1 of 2
$lang["items"]="items"; # e.g. 17 items
$lang["item"]="item"; # e.g. 1 item

# Statistics
$lang["stat-addpubliccollection"]="Add public collection";
$lang["stat-addresourcetocollection"]="Add resources to collection";
$lang["stat-addsavedsearchtocollection"]="Add saved search to collection";
$lang["stat-addsavedsearchitemstocollection"]="Add saved search items to collection";
$lang["stat-advancedsearch"]="Advanced search";
$lang["stat-archivesearch"]="Archive search";
$lang["stat-assignedresearchrequest"]="Assigned research request";
$lang["stat-createresource"]="Create resource";
$lang["stat-e-mailedcollection"]="E-mailed collection";
$lang["stat-e-mailedresource"]="E-mailed resource";
$lang["stat-keywordaddedtoresource"]="Keyword added to resource";
$lang["stat-keywordusage"]="Keyword usage";
$lang["stat-newcollection"]="New collection";
$lang["stat-newresearchrequest"]="New research request";
$lang["stat-printstory"]="Print story";
$lang["stat-processedresearchrequest"]="Processed search request";
$lang["stat-resourcedownload"]="Resource download";
$lang["stat-resourceedit"]="Resource edit";
$lang["stat-resourceupload"]="Resource upload";
$lang["stat-resourceview"]="Resource view";
$lang["stat-search"]="Search";
$lang["stat-usersession"]="User session";
$lang["stat-addedsmartcollection"]="Added Smart Collection";

# Access
$lang["access0"]="Open";
$lang["access1"]="Restricted";
$lang["access2"]="Confidential";
$lang["access3"]="Custom";
$lang["statusandrelationships"]="Status and Relationships";

# Lists
$lang["months"]=array("January","February","March","April","May","June","July","August","September","October","November","December");

# New for 1.3
$lang["savesearchitemstocollection"]="Save search items to collection";
$lang["removeallresourcesfromcollection"]="Remove all resources from this collection";
$lang["deleteallresourcesfromcollection"]="Delete all resources in this collection";
$lang["deleteallsure"]="Are you sure you wish to DELETE these resources? This will delete the resources themselves, not just remove them from this collection.";
$lang["batchdonotaddcollection"]="(do not add to a collection)";
$lang["collectionsthemes"]="Related themes and public collections";
$lang["recent"]="Recent";
$lang["batchcopyfrom"]="Copy the data below from resource with ID";
$lang["copy"]="Copy";
$lang["zipall"]="Zip all";
$lang["downloadzip"]="Download collection as a zip file";
$lang["downloadsize"]="Download size";
$lang["tagging"]="Tagging";
$lang["speedtagging"]="Speed Tagging";
$lang["existingkeywords"]="Existing Keywords:";
$lang["extrakeywords"]="Extra Keywords";
$lang["leaderboard"]="Leaderboard";
$lang["confirmeditall"]="Are you sure you wish to save? This will overwrite the existing values(s) for the selected field(s) for all the resources in your current collection.";
$lang["confirmsubmitall"]="Are you sure you wish to submit all for review? This will overwrite the existing values(s) for the selected field(s) for all the resources in your current collection and submit them all for review.";
$lang["confirmunsubmitall"]="Are you sure you wish to unsubmit all from the review process? This will overwrite the existing values(s) for the selected field(s) for all the resources in your current collection and unsubmit them all from review.";
$lang["confirmpublishall"]="Are you sure you wish to publish? This will overwrite the existing values(s) for the selected field(s) for all the resources in your current collection and publish them all for public viewing";
$lang["confirmunpublishall"]="Are you sure you wish to unpublish these resources? This will overwrite the existing values(s) for the selected field(s) for all the resources in your current collection and remove them from public viewing";
$lang["collectiondeleteconfirm"]="Are you sure you wish to delete this collection?";
$lang["hidden"]="(hidden)";
$lang["requestnewpassword"]="Request New Password";

# New for 1.4
$lang["reorderresources"]="Reorder resources within collection (hold and drag)";
$lang["addorviewcomments"]="Add or view comments";
$lang["collectioncomments"]="Collection Comments";
$lang["collectioncommentsinfo"]="Add a comment to this collection for this resource. This will only apply to this collection.";
$lang["comment"]="Comment";
$lang["warningexpired"]="Resource Expired";
$lang["warningexpiredtext"]="Warning! This resource has exceeded the expiry date. You must click the link below to enable the download functionality.";
$lang["warningexpiredok"]="&gt; Enable resource download";
$lang["userrequestcomment"]="Comment";
$lang["addresourcebatchbrowser"]="Add Resource Batch - In Browser (Flash)";
$lang["addresourcebatchbrowserjava"]="Add Resource Batch - In Browser (Java - recommended)";

$lang["addresourcebatchftp"]="Add Resource Batch - Fetch from FTP server";
$lang["replaceresourcebatch"]="Replace Resource Batch";
$lang["editmode"]="Edit Mode";
$lang["replacealltext"]="Replace All Text / Option(s)";
$lang["findandreplace"]="Find And Replace";
$lang["appendtext"]="Append Text / Option(s)";
$lang["removetext"]="Remove Text / Option(s)";
$lang["find"]="Find";
$lang["andreplacewith"]="...and replace with...";
$lang["relateallresources"]="Relate all resources in this collection";

# New for 1.5
$lang["columns"]="Columns";
$lang["contactsheetconfiguration"]="Contact Sheet Configuration";
$lang["thumbnails"]="Thumbnails";
$lang["contactsheetintrotext"]="Please select the sheet size and the number of columns for your contact sheet.";
$lang["size"]="Size";
$lang["orientation"]="Orientation";
$lang["requiredfield"]="This is a required field";
$lang["requiredfields"]="Some required fields were not completed. Please review the form and try again";
$lang["viewduplicates"]="View Duplicate Resources";
$lang["duplicateresources"]="Duplicate Resources";
$lang["userlog"]="User Log";
$lang["ipaddressrestriction"]="IP address restriction (optional)";
$lang["wildcardpermittedeg"]="Wildcard permitted e.g.";

# New for 1.6
$lang["collection_download_original"]="Original file";
$lang["newflag"]="NEW!";
$lang["link"]="Link";
$lang["uploadpreview"]="Upload a preview image only";
$lang["starttypingusername"]="(start typing username / full name / group name)";
$lang["requestfeedback"]="Request feedback<br />(you will be e-mailed the response)";
$lang["sendfeedback"]="Send feedback";
$lang["feedbacknocomments"]="You have not left any comments for the resources in the collection.<br />Click the speech bubble next to each resource to add comments.";
$lang["collectionfeedback"]="Collection Feedback";
$lang["collectionfeedbackemail"]="You have received the following feedback:";
$lang["feedbacksent"]="Your feedback has been sent.";
$lang["newarchiveresource"]="Add Single Archived Resource";
$lang["nocategoriesselected"]="No categories selected";
$lang["showhidetree"]="Show/hide tree";
$lang["clearall"]="Clear all";
$lang["clearcategoriesareyousure"]="Are you sure you wish to clear all selected options?";
$lang["share"]="Share";
$lang["sharecollection"]="Share Collection";
$lang["sharecollection-name"]="Share Collection - %collectionname"; # %collectionname will be replaced, e.g. Share Collection - Cars
$lang["generateurl"]="Generate URL";
$lang["generateurlinternal"]="The below URL will work for existing users only.";
$lang["generateurlexternal"]="The below URL will work for everyone and does not require a login.";
$lang["archive"]="Archive";
$lang["collectionviewhover"]="Click to see the resources in this collection";
$lang["collectioncontacthover"]="Create a contact sheet with the resources in this collection";
$lang["original"]="Original";

$lang["password_not_min_length"]="The password must be at least ? characters in length";
$lang["password_not_min_alpha"]="The password must have at least ? alphabetical (a-z, A-Z) characters";
$lang["password_not_min_uppercase"]="The password must have at least ? upper case (A-Z) characters";
$lang["password_not_min_numeric"]="The password must have at least ? numeric (0-9) characters";
$lang["password_not_min_special"]="The password must have at least ? non alpha-numeric characters (!@$%&* etc.)";
$lang["password_matches_existing"]="The entered password is the same as your existing password";
$lang["password_expired"]="Your password has expired and you must now enter a new password";
$lang["max_login_attempts_exceeded"]="You have exceeded the maximum number of login attempts. You must now wait ? minutes before you can attempt to log in again.";

$lang["newlogindetails"]="Please find your new login details below."; # For new password mail
$lang["youraccountdetails"]="Your Account Details"; # Subject of mail sent to user on user details save

$lang["copyfromcollection"]="Copy from collection";
$lang["donotcopycollection"]="Do not copy from a collection";

$lang["resourcesincollection"]="resources in this collection"; # E.g. 3 resources in this collection
$lang["removefromcurrentcollection"]="Remove from current collection";
$lang["showtranslations"]="+ Show translations";
$lang["hidetranslations"]="- Hide translations";
$lang["archivedresource"]="Archived Resource";

$lang["managerelatedkeywords"]="Manage Related Keywords";
$lang["keyword"]="Keyword";
$lang["relatedkeywords"]="Related Keywords";
$lang["matchingrelatedkeywords"]="Matching Related Keywords";
$lang["newkeywordrelationship"]="Create new relationship for keyword...";
$lang["searchkeyword"]="Search keyword";

$lang["exportdata"]="Export Data";
$lang["exporttype"]="Export Type";

$lang["managealternativefiles"]="Manage alternative files";
$lang["managealternativefilestitle"]="Manage Alternative Files";
$lang["alternativefiles"]="Alternative Files";
$lang["filetype"]="File Type";
$lang["filedeleteconfirm"]="Are you sure you wish to delete this file?";
$lang["addalternativefile"]="Add alternative file";
$lang["editalternativefile"]="Edit Alternative File";
$lang["description"]="Description";
$lang["notuploaded"]="Not uploaded";
$lang["uploadreplacementfile"]="Upload replacement file";
$lang["backtomanagealternativefiles"]="Back to manage alternative files";


$lang["resourceistranscoding"]="Resource is currently being transcoded";
$lang["cantdeletewhiletranscoding"]="You can't delete resources while they are transcoding";

$lang["maxcollectionthumbsreached"]="There are too many resources in this collection to display thumbnails. Thumbnails will now be hidden.";

$lang["ratethisresource"]="How do you rate this resource?";
$lang["ratingthankyou"]="Thank you for your rating.";
$lang["ratings"]="ratings";
$lang["rating_lowercase"]="rating";
$lang["cannotemailpassword"]="You cannot e-mail the user their existing password as it is not stored (a cryptographic hash is stored instead).<br /><br />You must use the 'Suggest' button above which will generate a new password and enable the e-mail function.";

$lang["userrequestnotification1"]="The User Login Request form has been completed with the following details:";
$lang["userrequestnotification2"]="If this is a valid request, please visit the system at the URL below and create an account for this user.";
$lang["ipaddress"]="IP Address";
$lang["userresourcessubmitted"]="The following user contributed resources have been submitted for review:";
$lang["userresourcesunsubmitted"]="The following user contributed resources have been unsubmitted, and no longer require review:";
$lang["viewalluserpending"]="View all user contributed resources pending review:";

# New for 1.7
$lang["installationcheck"]="Installation Check";
$lang["managefieldoptions"]="Manage Field Options";
$lang["matchingresourcesheading"]="Matching Resources";
$lang["backtofieldlist"]="Back to field list";
$lang["rename"]="Rename";
$lang["showalllanguages"]="Show all languages";
$lang["hidealllanguages"]="Hide all languages";
$lang["clicktologinasthisuser"]="Click to log in as this user";
$lang["addkeyword"]="Add keyword";
$lang["selectedresources"]="Selected resources";

$lang["internalusersharing"]="Internal User Sharing";
$lang["externalusersharing"]="External User Sharing";
$lang["accesskey"]="Access Key";
$lang["sharedby"]="Shared By";
$lang["sharedwith"]="Shared With";
$lang["lastupdated"]="Last Updated";
$lang["lastused"]="Last Used";
$lang["noattachedusers"]="No attached users.";
$lang["confirmdeleteaccess"]="Are you sure you wish to delete this access key? Users that have been given access using this key will no longer be able to access this collection.";
$lang["noexternalsharing"]="No external sharing.";
$lang["sharedcollectionaddwarning"]="Warning: This collection has been shared with external users. The resource you have added has now been made available to these users. Click 'share' to manage the external access for this collection.";
$lang["addresourcebatchlocalfolder"]="Add Resource Batch - Fetch from local upload folder";

# Setup Script
$lang["setup-alreadyconfigured"]="Your ResourceSpace installation is already configured.  To reconfigure, you may delete <pre>include/config.php</pre> and point your browser to this page again.";
$lang["setup-successheader"]="Congratulations!";
$lang["setup-successdetails"]="Your initial ResourceSpace setup is complete.  Be sure to check out 'include/default.config.php' for more configuration options.";
$lang["setup-successnextsteps"]="Next steps:";
$lang["setup-successremovewrite"]="You can now remove write access to 'include/'.";
$lang["setup-visitwiki"]='Visit the <a href="http://wiki.resourcespace.org/index.php/Main_Page">ResourceSpace Documentation Wiki</a> for more information about customizing your installation.';
$lang["setup-checkconfigwrite"]="Write access to config directory:";
$lang["setup-checkstoragewrite"]="Write access to storage directory:";
$lang["setup-welcome"]="Welcome to ResourceSpace";
$lang["setup-introtext"]="Thanks for choosing ResourceSpace.  This configuration script will help you setup ResourceSpace.  This process only needs to be completed once.";
$lang["setup-checkerrors"]="Pre-configuration errors were detected.<br />  Please resolve these errors and return to this page to continue.";
$lang["setup-errorheader"]="There were errors detected in your configuration.  See below for detailed error messages.";
$lang["setup-warnheader"]="Some of your settings generated warning messages.  See below for details.  This doesn't necessarily mean there is a problem with your configuration.";
$lang["setup-basicsettings"]="Basic Settings";
$lang["setup-basicsettingsdetails"]="These settings provide the basic setup for your ResourceSpace installation.  Required items are marked with a <strong>*</strong>";
$lang["setup-dbaseconfig"]="Database Configuration";
$lang["setup-mysqlerror"]="There was an error with your MySQL settings:";
$lang["setup-mysqlerrorversion"]="MySQL version should be 5 or greater.";
$lang["setup-mysqlerrorserver"]="Unable to reach server.";
$lang["setup-mysqlerrorlogin"]="Login failed. (Check username and password.)";
$lang["setup-mysqlerrordbase"]="Unable to access database.";
$lang["setup-mysqlerrorperns"]="Check user permissions.  Unable to create tables.";
$lang["setup-mysqltestfailed"]="Test failed (unable to verify MySQL)";
$lang["setup-mysqlserver"]="MySQL Server:";
$lang["setup-mysqlusername"]="MySQL Username:";
$lang["setup-mysqlpassword"]="MySQL Password:";
$lang["setup-mysqldb"]="MySQL Database:";
$lang["setup-mysqlbinpath"]="MySQL Binary Path:";
$lang["setup-generalsettings"]="General Settings";
$lang["setup-baseurl"]="Base URL:";
$lang["setup-emailfrom"]="Email From Address:";
$lang["setup-emailnotify"]="Email Notify:";
$lang["setup-spiderpassword"]="Spider Password:";
$lang["setup-scramblekey"]="Scramble Key:";
$lang["setup-apiscramblekey"]="API Scramble Key:";
$lang["setup-paths"]="Paths";
$lang["setup-pathsdetail"]="For each path, enter the path without a trailing slash to each binary.  To disable a binary, leave the path blank.  Any auto-detected paths have already been filled in.";
$lang["setup-applicationname"]="Application Name:";
$lang["setup-basicsettingsfooter"]="NOTE: The only <strong>required</strong> settings are on this page.  If you're not interested in checking out the advanced options, you may click below to begin the installation process.";
$lang["setup-if_mysqlserver"]='The IP address or <abbr title="Fully Qualified Domain Name">FQDN</abbr> of your MySQL server installation.  If MySql is installed on the same server as your web server, use "localhost".';
$lang["setup-if_mysqlusername"]="The username used to connect to your MySQL server.  This user must have rights to create tables in the database named below.";
$lang["setup-if_mysqlpassword"]="The password for the MySQL username entered above.";
$lang["setup-if_mysqldb"]="The Name of the MySQL database RS will use. (This database must exist.)";
$lang["setup-if_mysqlbinpath"]="The path to the MySQL client binaries - e.g. mysqldump. NOTE: This is only needed if you plan to use the export tool.";
$lang["setup-if_baseurl"]="The 'base' web address for this installation.  NOTE: No trailing slash.";
$lang["setup-if_emailfrom"]="The address that emails from RS appear to come from.";
$lang["setup-if_emailnotify"]="The email address to which resource/user/research requests are sent.";
$lang["setup-if_spiderpassword"]="The spider password is a required field.";
$lang["setup-if_scramblekey"]="To enable scrambling, set the scramble key to be a hard-to-guess string (similar to a password).  If this is a public installation then this is a very wise idea.  Leave this field blank to disable resource path scrambling. This field has already been randomised for you, but you can change it to match an existing installation, if necessary.";
$lang["setup-if_apiscramblekey"]="Set the api scramble key to be a hard-to-guess string (similar to a password).  If you plan to use APIs then this is a very wise idea.";
$lang["setup-if_applicationname"]="The name of your implementation / installation (e.g. 'MyCompany Resource System').";
$lang["setup-err_mysqlbinpath"]="Unable to verify path.  Leave blank to disable.";
$lang["setup-err_baseurl"]="Base URL is a required field.";
$lang["setup-err_baseurlverify"]="Base URL does not seem to be correct (could not load license.txt).";
$lang["setup-err_spiderpassword"]="The password required for spider.php.  IMPORTANT: Randomise this for each new installation. Your resources will be readable by anyone that knows this password.  This field has already been randomised for you, but you can change it to match an existing installation, if necessary.";
$lang["setup-err_scramblekey"]="If this is a public installation, setting the scramble key is recommended.";
$lang["setup-err_apiscramblekey"]="If this is a public installation, setting the api scramble key is recommended.";
$lang["setup-err_path"]="Unable to verify location of";
$lang["setup-emailerr"]="Not a valid email address.";
$lang["setup-rs_initial_configuration"]="ResourceSpace: Initial Configuration";
$lang["setup-include_not_writable"]="'/include' not writable. Only required during setup.";
$lang["setup-override_location_in_advanced"]="Override location in 'Advanced Settings'.";
$lang["setup-advancedsettings"]="Advanced Settings";
$lang["setup-binpath"]="%bin Path"; #%bin will be replaced, e.g. "Imagemagick Path"
$lang["setup-begin_installation"]="Begin Installation!";
$lang["setup-generaloptions"]="General Options";
$lang["setup-allow_password_change"]="Allow password change?";
$lang["setup-enable_remote_apis"]="Enable Remote APIs?";
$lang["setup-if_allowpasswordchange"]="Allow end users to change their passwords.";
$lang["setup-if_enableremoteapis"]="Allow remote access to API plugins.";
$lang["setup-allow_account_requests"]="Allow users to request accounts?";
$lang["setup-display_research_request"]="Display the Research Request functionality?";
$lang["setup-if_displayresearchrequest"]="Allows users to request resources via a form, which is e-mailed.";
$lang["setup-themes_as_home"]="Use the themes page as the home page?";
$lang["setup-remote_storage_locations"]="Remote Storage Locations";
$lang["setup-use_remote_storage"]="Use remote storage?";
$lang["setup-if_useremotestorage"]="Check this box to configure remote storage locations for RS. (To use another server for filestore.)";
$lang["setup-storage_directory"]="Storage Directory";
$lang["setup-if_storagedirectory"]="Where to put the media files. Can be absolute (/var/www/blah/blah) or relative to the installation. NOTE: No trailing slash.";
$lang["setup-storage_url"]="Storage URL";
$lang["setup-if_storageurl"]="Where the storagedir is available. Can be absolute (http://files.example.com) or relative to the installation. NOTE: No trailing slash.";
$lang["setup-ftp_settings"]="FTP Settings";
$lang["setup-if_ftpserver"]="Only necessary if you plan to use the FTP upload feature.";
$lang["setup-login_to"]="Login to";
$lang["setup-configuration_file_output"]="Configuration File Ouput";

# Collection log - actions
$lang["collectionlog"]="Collection Log";
$lang["collectionlog-r"]="Removed resource";
$lang["collectionlog-R"]="Removed all resources";
$lang["collectionlog-D"]="Deleted all resources";
$lang["collectionlog-d"]="Deleted resource"; // this shows external deletion of any resources related to the collection.
$lang["collectionlog-a"]="Added resource";
$lang["collectionlog-c"]="Added resource (copied)";
$lang["collectionlog-m"]="Added resource comment";
$lang["collectionlog-*"]="Added resource rating";
$lang["collectionlog-S"]="Shared collection with "; //  + notes field
$lang["collectionlog-E"]="E-mailed collection to ";//  + notes field
$lang["collectionlog-s"]="Shared resource with ";//  + notes field
$lang["collectionlog-T"]="Stopped sharing collection with ";//  + notes field
$lang["collectionlog-t"]="Stopped access to resource by ";//  + notes field
$lang["collectionlog-X"]="Collection deleted";


$lang["viewuncollectedresources"]="View Resources Not Used in Collections";

# Collection requesting
$lang["requestcollection"]="Request Collection";

# Metadata report
$lang["metadata-report"]="Metadata Report";

# Video Playlist
$lang["videoplaylist"]="Video Playlist";

$lang["restrictedsharecollection"]="You have restricted access to one or more of the resources in this collection and therefore sharing is prohibited.";

$lang["collection"]="Collection";
$lang["idecline"]="I Decline"; # For terms and conditions

$lang["mycollection_notpublic"]="You cannot make your 'My Collection' into a public collection or theme. Please create a new collection for this purpose.";

$lang["resourcemetadata"]="Resource Metadata";

$lang["selectgenerateurlexternal"]="To create a URL that will work for external users (people that do not have a login) please choose the access level you wish to grant to the resources.";

$lang["externalselectresourceaccess"]="If you are e-mailing external users, please select the level of access you would like to grant to this resource.";

$lang["externalselectresourceexpires"]="If you are e-mailing external users, please select an expiry date for the generated URL.";

$lang["externalshareexpired"]="Sorry, this share has expired and is no longer available.";

$lang["expires"]="Expires";
$lang["never"]="Never";

$lang["approved"]="Approved";
$lang["notapproved"]="Not approved";

$lang["userrequestnotification3"]="If this is a valid request, click the link below to review the details and approve the user account.";

$lang["ticktoapproveuser"]="You must tick the box to approve this user if you wish to enable this account";

$lang["managerequestsorders"]="Manage Requests / Orders";
$lang["editrequestorder"]="Edit Request / Order";
$lang["requestorderid"]="Request / Order ID";
$lang["viewrequesturl"]="To view this request, click the link below:";
$lang["requestreason"]="Reason for request";

$lang["resourcerequeststatus0"]="Pending";
$lang["resourcerequeststatus1"]="Approved";
$lang["resourcerequeststatus2"]="Declined";

$lang["ppi"]="PPI"; # (Pixels Per Inch - used on the resource download options list).

$lang["useasthemethumbnail"]="Use this resource as a theme category thumbnail?";
$lang["sessionexpired"]="You have been automatically logged out because you were inactive for more than 30 minutes. Please enter your login details to continue.";

$lang["resourcenotinresults"]="The current resource is no longer within your active search results so next/previous navigation is not possible.";
$lang["publishstatus"]="Save with Publish Status:";
$lang["addnewcontent"]="New content (Page,Name)";
$lang["hitcount"]="Hit Count";
$lang["downloads"]="Downloads";

$lang["addremove"]="";

##  Translations for standard log entries
$lang["all_users"]="all users";
$lang["new_resource"]="new resource";

$lang["invalidextension_mustbe"]="Invalid extension, must be";
$lang["allowedextensions"]="Allowed Extensions";

$lang["alternativebatchupload"]="Batch upload alternative files (Java)";

$lang["confirmdeletefieldoption"]="Are you sure you wish to DELETE this field option?";

$lang["cannotshareemptycollection"]="This collection is empty and cannot be shared.";

$lang["requestall"]="Request all";
$lang["requesttype-email_only"]=$lang["resourcerequesttype0"]="Email Only";
$lang["requesttype-managed"]=$lang["resourcerequesttype1"]="Managed Request";
$lang["requesttype-payment_-_immediate"]=$lang["resourcerequesttype2"]="Payment - Immediate";
$lang["requesttype-payment_-_invoice"]=$lang["resourcerequesttype3"]="Payment - Invoice";

$lang["requestapprovedmail"]="Your request has been approved. Click the link below to view and download the requested resources.";
$lang["requestdeclinedmail"]="Sorry, your request for the resources in the collection below has been declined.";

$lang["resourceexpirymail"]="The following resources have expired:";
$lang["resourceexpiry"]="Resource Expiry";

$lang["requestapprovedexpires"]="Your access to these resources will expire on";

$lang["pleasewaitsmall"]="(please wait)";
$lang["removethisfilter"]="(remove this filter)";

$lang["no_exif"]="Do not import embedded EXIF/IPTC/XMP metadata for this upload";
$lang["difference"]="Difference";
$lang["viewdeletedresources"]="View Deleted Resources";
$lang["finaldeletion"]="This resources is already in the 'deleted' state. This action will completely remove the resource from the system.";

$lang["nocookies"]="A cookie could not be set correctly. Please make sure you have cookies enabled in your browser settings.";

$lang["selectedresourceslightroom"]="Selected resources (Lightroom compatible list):";

# Plugins Manager
$lang['plugins-noneinstalled'] = "No plugins currently activated.";
$lang['plugins-noneavailable'] = "No plugins currently available.";
$lang['plugins-availableheader'] = 'Available Plugins';
$lang['plugins-installedheader'] = 'Currently Activated Plugins';
$lang['plugins-author'] = 'Author';
$lang['plugins-version'] = 'Version';
$lang['plugins-instversion'] = 'Installed Version';
$lang['plugins-uploadheader'] = 'Upload Plugin';
$lang['plugins-uploadtext'] = 'Select a .rsp file to install.';
$lang['plugins-deactivate'] = 'Deactivate';
$lang['plugins-moreinfo'] = 'More Info';
$lang['plugins-activate'] = 'Activate';
$lang['plugins-purge'] = 'Purge Configuration';
$lang['plugins-rejmultpath'] = 'Archive contains multiple paths. (Security Risk)';
$lang['plugins-rejrootpath'] = 'Archive contains absolute paths. (Security Risk)';
$lang['plugins-rejparentpath'] = 'Archive contain parent paths (../). (Security Risk)';
$lang['plugins-rejmetadata'] = 'Archive description file not found.';
$lang['plugins-rejarchprob'] = 'There was a problem extracting the archive:';
$lang['plugins-rejfileprob'] = 'Uploaded plugin must be a .rsp file.';
$lang['plugins-rejremedy'] = 'If you trust this plugin you can install it manually by expanding the archive into your plugins directory.';
$lang['plugins-uploadsuccess'] = 'Plugin uploaded succesfully.';
$lang['plugins-headertext'] = 'Plugins extend the functionality of ResourceSpace.';
$lang['plugins-legacyinst'] = 'Activated via config.php';
$lang['plugins-uploadbutton'] = 'Upload Plugin';

#Location Data
$lang['location-title'] = 'Location Data';
$lang['location-add'] = 'Add Location';
$lang['location-edit'] = 'Edit Location';
$lang['location-details'] = 'Double-click map to place pin.  Once the pin has been placed, you can drag the pin to refine the location.';
$lang['location-noneselected']="No Location Selected";
$lang['location'] = 'Location';

$lang["publiccollections"]="Public Collections";
$lang["viewmygroupsonly"]="View my groups only";
$lang["usemetadatatemplate"]="Use metadata template";
$lang["undometadatatemplate"]="(undo template selection)";

$lang["accountemailalreadyexists"]="An account with that e-mail address already exists";

$lang["backtothemes"]="Back to themes";
$lang["downloadreport"]="Download Report";

#Bug Report Page
$lang['reportbug']="Prepare bug report for ResourceSpace team";
$lang['reportbug-detail']="The following information has been compiled for inclusion in the bug report.  You'll be able to change all values before submitting a report.";
$lang['reportbug-login']="NOTE: Click here to login to the bug tracker BEFORE clicking prepare.";
$lang['reportbug-preparebutton']="Prepare Bug Report";

$lang["enterantispamcode"]="<strong>Anti-Spam</strong> <sup>*</sup><br /> Please enter the following code:";

$lang["groupaccess"]="Group Access";
$lang["plugin-groupsallaccess"]="This plugin is activated for all groups";
$lang["plugin-groupsspecific"]="This plugin is activated for the selected groups only";


$lang["associatedcollections"]="Associated Collections";
$lang["emailfromuser"]="Send the e-mail from ";
$lang["emailfromsystem"]="If unchecked, email will be sent from the system address: ";



$lang["previewpage"]="Preview Page";
$lang["nodownloads"]="No Downloads";
$lang["uncollectedresources"]="Resources Not Used in Collections";
$lang["nowritewillbeattempted"]="No write will be attempted";
$lang["notallfileformatsarewritable"]="Not all file formats are writable by exiftool";
$lang["filetypenotsupported"]="%filetype filetype not supported";  # %filetype will be replaced, e.g. JPG filetype not supported
$lang["exiftoolprocessingdisabledforfiletype"]="Exiftool processing disabled for file type %filetype"; # %filetype will be replaced, e.g. Exiftool processing disabled for file type JPG
$lang["nometadatareport"]="No Metadata Report";
$lang["metadatawritewillbeattempted"]="Metadata write will be attempted.";
$lang["embeddedvalue"]="Embedded Value";
$lang["exiftooltag"]="Exiftool Tag";
$lang["error"]="Error";
$lang["exiftoolnotfound"]="Could not find Exiftool";

$lang["indicateusage"]="Please describe your planned use for this resource.";
$lang["usage"]="Usage";
$lang["indicateusagemedium"]="Usage Medium";
$lang["usageincorrect"]="You must describe the planned usage and select a medium";

$lang["savesearchassmartcollection"]="Save Search as Smart Collection";
$lang["smartcollection"]="Smart Collection";


$lang["uploadertryflash"]="If you are having problems with this uploader, try the <strong>Flash uploader</strong>.";
$lang["uploadertryjava"]="If you are having problems with this uploader, or if <strong>uploading large files</strong>, try the <strong>Java uploader</strong>.";
$lang["getjava"]="To ensure that you have the latest Java software on your system, visit the Java website.";
$lang["getflash"]="To ensure that you have the latest Flash player on your system, visit the Flash website.";

$lang["all"]="All";
$lang["backtoresults"]="Back to results";

$lang["preview_all"]="Preview All";

$lang["usagehistory"]="Usage History";
$lang["usagebreakdown"]="Usage Breakdown";
$lang["usagetotal"]="Total Downloads";
$lang["usagetotalno"]="Total number of downloads";
$lang["ok"]="OK";

$lang["random"]="Random";
$lang["userratingstatsforresource"]="User Rating Stats for Resource";
$lang["average"]="Average";
$lang["popupblocked"]="The popup has been blocked by your browser.";
$lang["closethiswindow"]="Close this window";

$lang["requestaddedtocollection"]="This resource has been added to your current collection. You can request the items in your collection by clicking \'Request All\' on the collection bar below.";

# E-commerce text
$lang["buynow"]="Buy Now";
$lang["yourbasket"]="Your Basket";
$lang["addtobasket"]="Add to basket";
$lang["yourbasketcontains"]="Your basket contains ? items.";
$lang["yourbasketisempty"]="Your basket is empty.";
$lang["buy"]="Buy";
$lang["buyitemaddedtocollection"]="This resource has been added to your basket. You can purchase all the items in your basket by clicking \'Buy Now\' below.";
$lang["buynowintro"]="Please select the sizes you require.";
$lang["nodownloadsavailable"]="Sorry, there are no downloads available for this resource.";
$lang["proceedtocheckout"]="Proceed to checkout";
$lang["totalprice"]="Total price";
$lang["price"]="Price";
$lang["waitingforpaymentauthorisation"]="Sorry, we have not yet received the payment authorisation. Please wait a few moments then click 'reload' below.";
$lang["reload"]="Reload";
$lang["downloadpurchaseitems"]="Download Purchased Items";
$lang["downloadpurchaseitemsnow"]="Please use the links below to download your purchased items immediately.<br><br>Do not navigate away from this page until you have downloaded all the items.";
$lang["alternatetype"]="Alternative type";


$lang["subcategories"]="Subcategories";
$lang["back"]="Back";

$lang["pleasewait"]="Please wait...";

$lang["autorotate"]="Autorotate images?";

# Reports
# Report names (for the default reports)
$lang["report-keywords_used_in_resource_edits"]="Keywords used in resource edits";
$lang["report-keywords_used_in_searches"]="Keywords used in searches";
$lang["report-resource_download_summary"]="Resource download summary";
$lang["report-resource_views"]="Resource views";
$lang["report-resources_sent_via_e-mail"]="Resources sent via e-mail";
$lang["report-resources_added_to_collection"]="Resources added to collection";
$lang["report-resources_created"]="Resources created";
$lang["report-resources_with_zero_downloads"]="Resources with zero downloads";
$lang["report-resources_with_zero_views"]="Resources with zero views";
$lang["report-resource_downloads_by_group"]="Resource downloads by group";
$lang["report-resource_download_detail"]="Resource download detail";
$lang["report-user_details_including_group_allocation"]="User details including group allocation";

#Column headers (for the default reports)
$lang["columnheader-keyword"]="Keyword";
$lang["columnheader-entered_count"]="Entered Count";
$lang["columnheader-searches"]="Searches";
$lang["columnheader-date_and_time"]="Date / Time";
$lang["columnheader-downloaded_by_user"]="Downloaded By User";
$lang["columnheader-user_group"]="User Group";
$lang["columnheader-resource_title"]="Resource Title";
$lang["columnheader-title"]="Title";
$lang["columnheader-downloads"]="Downloads";
$lang["columnheader-group_name"]="Group Name";
$lang["columnheader-resource_downloads"]="Resource Downloads";
$lang["columnheader-views"]="Views";
$lang["columnheader-added"]="Added";
$lang["columnheader-creation_date"]="Creation Date";
$lang["columnheader-sent"]="Sent";
$lang["columnheader-last_seen"]="Last Seen";

$lang["period"]="Period";
$lang["lastndays"]="Last ? days"; # ? is replaced by the system with the number of days, for example "Last 100 days".
$lang["specificdays"]="Specific number of days";
$lang["specificdaterange"]="Specific date range";
$lang["to"]="to";

$lang["emailperiodically"]="Create new periodic e-mail";
$lang["emaileveryndays"]="E-mail me this report every ? days";
$lang["newemailreportcreated"]="A new periodic e-mail has been created. You can cancel this using the link at the bottom of the e-mail.";
$lang["unsubscribereport"]="To unsubscribe from this report, click the link below:";
$lang["unsubscribed"]="Unsubscribed";
$lang["youhaveunsubscribedreport"]="You have been unsubscribed from the periodic report e-mail.";
$lang["sendingreportto"]="Sending report to";
$lang["reportempty"]="No matching data was found for the selected report and period.";

$lang["purchaseonaccount"]="Add To Account";
$lang["areyousurepayaccount"]="Are you sure you wish to add this purchase to your account?";
$lang["accountholderpayment"]="Account Holder Payment";
$lang["subtotal"]="Subtotal";
$lang["discountsapplied"]="Discounts applied";
$lang["log-p"]="Purchased resource";
$lang["viauser"]="via user";
$lang["close"]="Close";

# Installation Check
$lang["repeatinstallationcheck"]="Repeat Installation Check";
$lang["shouldbeversion"]="should be ? or greater"; # E.g. "should be 4.4 or greater"
$lang["phpinivalue"]="PHP.INI value for '?'"; # E.g. "PHP.INI value for 'memory_limit'"
$lang["writeaccesstofilestore"]="Write access to $storagedir";
$lang["nowriteaccesstofilestore"]="$storagedir not writable";
$lang["writeaccesstohomeanim"]="Write access to $homeanim_folder";
$lang["nowriteaccesstohomeanim"]="$homeanim_folder not writable. Open permissions to enable home animation cropping feature in the transform plugin.";
$lang["blockedbrowsingoffilestore"]="Blocked browsing of 'filestore' directory";
$lang["noblockedbrowsingoffilestore"]="filestore folder appears to be browseable; remove 'Indexes' from Apache 'Options' list.";
$lang["executionofconvertfailed"]="Execution failed; unexpected output when executing convert command. Output was '?'.<br>If on Windows and using IIS 6, access must be granted for command line execution. Refer to installation instructions in the wiki."; # ? will be replaced.
$lang["exif_extension"]="EXIF extension";
$lang["lastscheduledtaskexection"]="Last scheduled task execution (days)";
$lang["executecronphp"]="Relevance matching will not be effective and periodic e-mail reports will not be sent. Ensure <a href='../batch/cron.php'>batch/cron.php</a> is executed at least once daily via a cron job or similar.";
$lang["shouldbeormore"]="should be ? or greater"; # E.g. should be 200M or greater

$lang["generateexternalurl"]="Generate External URL";

$lang["starsminsearch"]="Stars (Minimum)";
$lang["anynumberofstars"]="Any Number of Stars";

$lang["noupload"]="No Upload";

# System Setup
# System Setup Tree Nodes (for the default setup tree)
$lang["treenode-root"]="Root";
$lang["treenode-group_management"]="Group Management";
$lang["treenode-new_group"]="New Group";
$lang["treenode-new_subgroup"]="New Subgroup";
$lang["treenode-resource_types_and_fields"]="Resource Types / Fields";
$lang["treenode-new_resource_type"]="New Resource Type";
$lang["treenode-new_field"]="New Field";
$lang["treenode-reports"]="Reports";
$lang["treenode-new_report"]="New Report";
$lang["treenode-downloads_and_preview_sizes"]="Downloads / Preview Sizes";
$lang["treenode-new_download_and_preview_size"]="New Download / Preview Size";
$lang["treenode-database_statistics"]="Database Statistics";
$lang["treenode-permissions_search"]="Permissions Search";
$lang["treenode-no_name"]="(no name)";

$lang["treeobjecttype-preview_size"]="Preview Size";

$lang["permissions"]="Permissions";

# System Setup File Editor
$lang["configdefault-title"]="(copy and paste options from here)";
$lang["config-title"]="(BE CAREFUL not to make syntax errors. If you break this file, you must fix server-side!)";

# System Setup Properties Pane
$lang["file_too_large"]="File too large";
$lang["field_updated"]="Field updated";
$lang["zoom"]="Zoom";
$lang["deletion_instruction"]="Leave blank and save to delete the file";
$lang["upload_file"]="Upload file";
$lang["item_deleted"]="Item deleted";
$lang["viewing_version_created_by"]="Viewing version created by";
$lang["on_date"]="on";
$lang["launchpermissionsmanager"]="Launch Permissions Manager";
$lang["confirm-deletion"]="Are you sure?";

# Permissions Manager
$lang["permissionsmanager"]="Permissions Manager";
$lang["backtogroupmanagement"]="Back to group management";
$lang["searching_and_access"]="Searching / Access";
$lang["metadatafields"]="Metadata Fields";
$lang["resource_creation_and_management"]="Resource Creation / Management";
$lang["themes_and_collections"]="Themes / Collections";
$lang["administration"]="Administration";
$lang["other"]="Other";
$lang["custompermissions"]="Custom Permissions";
$lang["searchcapability"]="Search capability";
$lang["access_to_restricted_and_confidential_resources"]="Can download restricted resources and view confidential resources<br>(normally admin only)";
$lang["restrict_access_to_all_available_resources"]="Restrict access to all available resources";
$lang["can_make_resource_requests"]="Can make resource requests";
$lang["show_watermarked_previews_and_thumbnails"]="Show watermarked previews/thumbnails";
$lang["can_see_all_fields"]="Can see all fields";
$lang["can_see_field"]="Can see field";
$lang["can_edit_all_fields"]="Can edit all fields<br>(for editable resources)";
$lang["can_edit_field"]="Can edit field";
$lang["can_see_resource_type"]="Can see resource type";
$lang["restricted_access_only_to_resource_type"]="Restricted access only to resource type";
$lang["edit_access_to_workflow_state"]="Edit access to workflow state";
$lang["can_create_resources_and_upload_files-admins"]="Can create resources / upload files<br>(admin users; resources go to 'Active' state)";
$lang["can_create_resources_and_upload_files-general_users"]="Can create resources / upload files<br>(normal users; resources go to 'Pending Submission' state via My Contributions)";
$lang["can_delete_resources"]="Can delete resources<br>(to which the user has write access)";
$lang["can_manage_archive_resources"]="Can manage archive resources";
$lang["can_tag_resources_using_speed_tagging"]="Can tag resources using 'Speed Tagging'<br>(if enabled in the configuration)";
$lang["enable_bottom_collection_bar"]="Enable bottom collection bar ('Lightbox')";
$lang["can_publish_collections_as_themes"]="Can publish collections as themes";
$lang["can_see_all_theme_categories"]="Can see all theme categories";
$lang["can_see_theme_category"]="Can see theme category";
$lang["display_only_resources_within_accessible_themes"]="When searching, display only resources that exist within themes to which the user has access";
$lang["can_access_team_centre"]="Can access the Team Centre area";
$lang["can_manage_research_requests"]="Can manage research requests";
$lang["can_manage_resource_requests"]="Can manage resource requests";
$lang["can_manage_content"]="Can manage content (intro/help text)";
$lang["can_bulk-mail_users"]="Can bulk-mail users";
$lang["can_manage_users"]="Can manage users";
$lang["can_manage_keywords"]="Can manage keywords";
$lang["can_access_system_setup"]="Can access the System Setup area";
$lang["can_change_own_password"]="Can change own account password";
$lang["can_manage_users_in_children_groups"]="Can manage users in children groups to the user's group only";
$lang["can_email_resources_to_own_and_children_and_parent_groups"]="Can email resources to users in the user's own group, children groups and parent group only";

$lang["nodownloadcollection"]="You do not have access to download any of the resources in this collection.";

$lang["progress"]="Progress";
$lang["ticktodeletethisresearchrequest"]="Tick to delete this request";

# SWFUpload
$lang["queued_too_many_files"]="You have attempted to queue too many files.";
$lang["creatingthumbnail"]="Creating thumbnail...";
$lang["uploading"]="Uploading...";
$lang["thumbnailcreated"]="Thumbnail Created.";
$lang["done"]="Done.";
$lang["stopped"]="Stopped."; 

$lang["latlong"]="Lat / Long";
$lang["geographicsearch"]="Geographic search";

$lang["geographicsearch_help"]="Drag to select a search area.";

$lang["purge"]="Purge";
$lang["purgeuserstitle"]="Purge Users";
$lang["purgeusers"]="Purge users";
$lang["purgeuserscommand"]="Delete users accounts that have not been active in the last % months, but were created before this period.";
$lang["purgeusersconfirm"]="This will delete % user accounts. Are you sure?";
$lang["pleaseenteravalidnumber"]="Please enter a valid number";
$lang["purgeusersnousers"]="There are no users to purge.";

$lang["editallresourcetypewarning"]="Warning: changing the resource type will delete any resource type specific metadata currently stored for the selected resources.";

$lang["geodragmode"]="Drag mode";
$lang["geodragmodearea"]="selection";
$lang["geodragmodepan"]="pan";

$lang["substituted_original"] = "substituted original";
$lang["use_original_if_size"] = "Use original if selected size is unavailable?";

$lang["originals-available-0"] = "available"; # 0 (originals) available
$lang["originals-available-1"] = "available"; # 1 (original) available
$lang["originals-available-2"] = "available"; # 2+ (originals) available

$lang["inch-short"] = "in";
$lang["centimetre-short"] = "cm";
$lang["megapixel-short"]="MP";
$lang["at-resolution"] = "@"; # E.g. 5.9 in x 4.4 in @ 144 PPI

$lang["deletedresource"] = "Deleted Resource";
$lang["deletedresources"] = "Deleted Resources";
$lang["action-delete_permanently"] = "Delete permanently";

$lang["horizontal"] = "Horizontal";
$lang["vertical"] = "Vertical";

$lang["cc-emailaddress"] = "CC %emailaddress"; # %emailaddress will be replaced, e.g. CC [your email address]

$lang["sort"] = "Sort";
$lang["sortcollection"] = "Sort Collection";
$lang["emptycollection"] = "Remove Resources";
$lang["emptycollectionareyousure"]="Are you sure you want to remove all resources from this collection?";

$lang["error-cannoteditemptycollection"]="You cannot edit an empty collection.";
$lang["error-permissiondenied"]="Permission denied.";
$lang["error-collectionnotfound"]="Collection not found.";

$lang["header-upload-subtitle"] = "Step %number: %subtitle"; # %number, %subtitle will be replaced, e.g. Step 1: Specify Default Content For New Resources
$lang["local_upload_path"] = "Local Upload Folder";
$lang["ftp_upload_path"] = "FTP Folder";
$lang["foldercontent"] = "Folder Content";
$lang["intro-local_upload"] = "Select one or more files from the local upload folder and click <b>Upload</b>. Once the files are uploaded they can be deleted from the upload folder.";
$lang["intro-ftp_upload"] = "Select one or more files from the FTP folder and click <b>Upload</b>.";
$lang["intro-java_upload"] = "Click <b>Browse</b> to locate one or more files and then click <b>Upload</b>.";
$lang["intro-swf_upload"] = "Click <b>Upload</b> to locate and upload one or more files. Hold down the Shift key to select multiple files.";
$lang["intro-single_upload"] = "Click <b>Browse</b> to locate a file and then click <b>Upload</b>.";
$lang["intro-batch_edit"] = "Please specify the default upload settings and the default values for the metadata of the resources you are about to upload.";

$lang["collections-1"] = "(<strong>1</strong> Collection)";
$lang["collections-2"] = "(<strong>%number</strong> Collections)"; # %number will be replaced, e.g. 3 Collections
$lang["total-collections-0"] = "<strong>Total: 0</strong> Collections";
$lang["total-collections-1"] = "<strong>Total: 1</strong> Collection";
$lang["total-collections-2"] = "<strong>Total: %number</strong> Collections"; # %number will be replaced, e.g. Total: 5 Collections
$lang["owned_by_you-0"] = "(<strong>0</strong> owned by you)";
$lang["owned_by_you-1"] = "(<strong>1</strong> owned by you)";
$lang["owned_by_you-2"] = "(<strong>%mynumber</strong> owned by you)"; # %mynumber will be replaced, e.g. (2 owned by you)

$lang["listresources"]= "Resources:";
$lang["action-log"]="View Log";

$lang["saveuserlist"]="Save this list";
$lang["deleteuserlist"]="Delete this list";
$lang["typeauserlistname"]="Type a Userlist name...";
$lang["loadasaveduserlist"]="Load a Saved Userlist";

$lang["searchbypage"]="Search Page";
$lang["searchbyname"]="Search Name";
$lang["searchbytext"]="Search Text";
$lang["saveandreturntolist"]="Save and Return to List";
$lang["backtomanagecontent"]="Back to Manage Content";
$lang["editcontent"]="Edit Content";

$lang["confirmcollectiondownload"]="Please wait while we create a zip archive. This might take a while, depending on the total size of your Resources.";

$lang["starttypingkeyword"]="Start typing keyword...";
$lang["createnewentryfor"]="Create new entry for";
$lang["confirmcreatenewentryfor"]="Are you sure you wish to create a new keyword list entry for '%%'?";

$lang["editresourcepreviews"]="Edit Resource Previews";
