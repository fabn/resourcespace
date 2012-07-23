<?php 

# Replacement collection download functionality - as collections can now contain both local and remote files.

$inside_plugin=true;

include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection","",true),$k))) {include "../../../include/authenticate.php";}
include "../../../include/search_functions.php";
include "../../../include/resource_functions.php";

$collection=getvalescaped("collection","",true);
$size=getvalescaped("size","");
$submitted=getvalescaped("submitted","");
$includetext=getvalescaped("text","false");
$useoriginal=getvalescaped("use_original","no");
$collectiondata=get_collection($collection);
$settings_id=getvalescaped("settings","");

$archiver_fullpath = get_utility_path("archiver");

if (!isset($zipcommand))
    {
    if (!$collection_download) {exit($lang["download-of-collections-not-enabled"]);}
    if ($archiver_fullpath==false) {exit($lang["archiver-utility-not-found"]);}
    if (!isset($collection_download_settings)) {exit($lang["collection_download_settings-not-defined"]);}
    else if (!is_array($collection_download_settings)) {exit($lang["collection_download_settings-not-an-array"]);}
    if (!isset($archiver_listfile_argument)) {exit($lang["listfile-argument-not-defined"]);}
    }
$archiver = $collection_download && ($archiver_fullpath!=false) && (isset($archiver_listfile_argument)) && (isset($collection_download_settings) ? is_array($collection_download_settings) : false);

# initiate text file
if (($zipped_collection_textfile==true)&&($includetext=="true")) { 
    $text = i18n_get_translated($collectiondata['name']) . "\r\n" .
    $lang["downloaded"] . " " . nicedate(date("Y-m-d H:i:s"), true, true) . "\r\n\r\n" .
    $lang["contents"] . ":\r\n\r\n";
}

# get collection
$result=do_search("!collection" . $collection);

$modified_result=hook("modifycollectiondownload");
if (is_array($modified_result)){$result=$modified_result;}

#this array will store all the available downloads.
$available_sizes=array();

$sizes=get_all_image_sizes(false);
foreach ($sizes as $sizeinfo)
	{
	$size_id=$sizeinfo['id'];
	}


$used_resources=array();
$subbed_original_resources = array();
if ($submitted != "")
	{
	$path="";
	$deletion_array=array();

	# Build a list of files to download
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=get_resource_access($result[$n]);
		$use_watermark=check_use_watermark();

		# Only download resources with proper access level
		if ($access==0 || $access=1)
			{
			$usesize=$size;
			$pextension = ($size == 'original') ? $result[$n]["file_extension"] : 'jpg';
			($size == 'original') ? $usesize="" : $usesize=$usesize;
			$p=get_resource_path($ref,true,$usesize,false,$pextension,-1,1,$use_watermark);

			if ((!file_exists($p)) && $useoriginal == 'yes' && resource_download_allowed($ref,'',$result[$n]['resource_type'])){
				// this size doesn't exist, so we'll try using the original instead
				$p=get_resource_path($ref,true,'',false,$result[$n]['file_extension'],-1,1,$use_watermark);
				$pextension = $result[$n]['file_extension'];
				$subbed_original_resources[] = $ref;
				$subbed_original = true;
			} else {
				$subbed_original = false;
			}

			# Check file exists and, if restricted access, that the user has access to the requested size.
			if (((file_exists($p) && $access==0) || 
				(file_exists($p) && $access==1 && 
					(image_size_restricted_access($size) || ($usesize='' && $restricted_full_download))) 
					
					) && resource_download_allowed($ref,$usesize,$result[$n]['resource_type']))
				{
				
				$used_resources[]=$ref;
				# when writing metadata, we take an extra security measure by copying the files to tmp
				$tmpfile=write_metadata($p,$ref);
				if($tmpfile!==false && file_exists($tmpfile)){$p=$tmpfile;}		
	
				# if the tmpfile is made, from here on we are working with that. 
				
				# If using original filenames when downloading, copy the file to new location so the name is included.
				if ($original_filenames_when_downloading)	
					{
					# Retrieve the original file name		
					$filename=get_data_by_field($ref,$filename_field);	

					if (strlen($filename)>0)
						{
						# Only perform the copy if an original filename is set.

						# now you've got original filename, but it may have an extension in a different letter case. 
						# The system needs to replace the extension to change it to jpg if necessary, but if the original file
						# is being downloaded, and it originally used a different case, then it should not come from the file_extension, 
						# but rather from the original filename itself.
						
						# do an extra check to see if the original filename might have uppercase extension that can be preserved.	
						# also, set extension to "" if the original filename didn't have an extension (exiftool identification of filetypes)
						$pathparts=pathinfo($filename);
						if (isset($pathparts['extension'])){
						if (strtolower($pathparts['extension'])==$pextension){$pextension=$pathparts['extension'];}	
						} else {$pextension="jpg";}	
						if ($usesize!=""&&!$subbed_original){$append="-".$usesize;}else {$append="";}
						$basename_minus_extension=remove_extension($pathparts['basename']);
						$filename=$basename_minus_extension.$append.".".$pextension;

						if ($prefix_resource_id_to_filename) {$filename=$prefix_filename_string . $ref . "_" . $filename;}
						
						$fs=explode("/",$filename);$filename=$fs[count($fs)-1];

                        # Convert $filename to the charset used on the server.
                        if (!isset($server_charset)) {$to_charset = 'UTF-8';}
                        else
                            {
                            if ($server_charset!="") {$to_charset = $server_charset;}
                            else {$to_charset = 'UTF-8';}
                            }
                        $filename = mb_convert_encoding($filename, $to_charset, 'UTF-8');

                        # Copy to a new location
                        $newpath = get_temp_dir() . "/" . $filename;
                        copy($p, $newpath);

						# Add the temporary file to the post-archiving deletion list.
						$deletion_array[]=$newpath;
						
						# Set p so now we are working with this new file
						$p=$newpath;
						}
					}
				
				#Add resource data/collection_resource data to text file
				if (($zipped_collection_textfile==true)&&($includetext=="true")){ 
					if ($size==""){$sizetext="";}else{$sizetext="-".$size;}
					if ($subbed_original) { $sizetext = ' (' . $lang['substituted_original'] . ')'; }
					$fields=get_resource_field_data($ref);
					$commentdata=get_collection_resource_comment($ref,$collection);
					if (count($fields)>0){ 
					$text.= $lang["resourceid"] . ": " . $ref . ($sizetext=="" ? "" :" " . $sizetext) . "\r\n-----------------------------------------------------------------\r\n";
						for ($i=0;$i<count($fields);$i++){
							$value=$fields[$i]["value"];
							$title=str_replace("Keywords - ","",$fields[$i]["title"]);
							if ((trim($value)!="")&&(trim($value)!=",")){$text.= wordwrap("* " . $title . ": " . i18n_get_translated($value) . "\r\n", 65);}
						}
					if(trim($commentdata['comment'])!=""){$text.= wordwrap($lang["comment"] . ": " . $commentdata['comment'] . "\r\n", 65);}	
					if(trim($commentdata['rating'])!=""){$text.= wordwrap($lang["rating"] . ": " . $commentdata['rating'] . "\r\n", 65);}	
					$text.= "-----------------------------------------------------------------\r\n\r\n";	
					}
				}
				
				$path.=$p . "\r\n";	
				
				# build an array of paths so we can clean up any exiftool-modified files.
				
				if($tmpfile!==false && file_exists($tmpfile)){$deletion_array[]=$tmpfile;}
				daily_stat("Resource download",$ref);
				resource_log($ref,'d',0);
				
				# update hit count if tracking downloads only
				if ($resource_hit_count_on_downloads) { 
				# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability).
				sql_query("update resource set new_hit_count=greatest(hit_count,new_hit_count)+1 where ref='$ref'");
				} 
				
				}
			}
		}

	# Download and add external resources
	$xt_resources=sql_query("select * from resourceconnect_collection_resources where collection='" . $collection . "'");
	foreach ($xt_resources as $xt_resource)
		{
		# Work out download URL
		$url=$xt_resource["url"];		
		$url=str_replace("view.php","download.php",$url);
		$url.="&size=" . $size;
		
		$tmp_file=get_temp_dir() . "/XT_" . $collection . "_" . $xt_resource["ref"] . ".jpg";
		file_put_contents($tmp_file,file_get_contents($url));
		
		$deletion_array[]=$tmp_file; # Add to deletion array for post-download cleanup
		$path.=$tmp_file . "\r\n";	# Add to download path
		}

    if ($path=="") {print_r($result);exit($lang["nothing_to_download"]);}	



    # Define the archive file.
    if ($archiver)
        {
        $file = $lang["collectionidprefix"] . $collection . "-" . $size . "." . $collection_download_settings[$settings_id]["extension"];
        }
    else
        {
        $file = $lang["collectionidprefix"] . $collection . "-" . $size . ".zip";
        }

	# Write command parameters to file.
	$cmdfile = get_temp_dir() . "/zipcmd" . $collection . "-" . $size . ".txt";
	$fh = fopen($cmdfile, 'w') or die("can't open file");
	fwrite($fh, $path);
	fclose($fh);

    # Execute the archiver command.
    # If $collection_download is true the $collection_download_settings are used if defined, else the legacy $zipcommand is used.
    if ($archiver)
        {
        exec($archiver_fullpath . " " . $collection_download_settings[$settings_id]["arguments"] . " " . escapeshellarg(get_temp_dir() . "/" . $file) . " " . $archiver_listfile_argument . escapeshellarg($cmdfile));
        }
    else
        {
        if ($config_windows)
            # Add the command file, containing the filenames, as an argument.
            {
            exec("$zipcommand " . escapeshellarg(get_temp_dir() . "/" . $file) . " @" . escapeshellarg($cmdfile));
            }
        else
            {
            # Pipe the command file, containing the filenames, to the executable.
            exec("$zipcommand " . escapeshellarg(get_temp_dir() . "/" . $file) . " -@ < " . escapeshellarg($cmdfile));
            }
        }

    # Archive created, schedule the command file for deletion.
	$deletion_array[]=$cmdfile;

	# Remove temporary files.
	foreach($deletion_array as $tmpfile) {delete_exif_tmpfile($tmpfile);}

    # Get the file size of the archive.
    $filesize = @filesize_unlimited(get_temp_dir() . "/" . $file);

    if ($use_collection_name_in_zip_name)
        {
        # Use collection name (if configured)
        if ($archiver)
            {
            $filename = $lang["collectionidprefix"] . $collection . "-" . safe_file_name(i18n_get_translated($collectiondata['name'])) . "-" . $size . "." . $collection_download_settings[$settings_id]["extension"];
            }
        else
            {
            $filename = $lang["collectionidprefix"] . $collection . "-" . safe_file_name(i18n_get_translated($collectiondata['name'])) . "-" . $size . ".zip";
            }
        }
    else
        {
        # Do not include the collection name in the filename (default)
        if ($archiver)
            {
            $filename = $lang["collectionidprefix"] . $collection . "-" . $size . "." . $collection_download_settings[$settings_id]["extension"];
            }
        else
            {
            $filename = $lang["collectionidprefix"] . $collection . "-" . $size . ".zip";
            }
        }

	header("Content-Disposition: attachment; filename=" . $filename);
    if ($archiver) {header("Content-Type: " . $collection_download_settings[$settings_id]["mime"]);}
    else {header("Content-Type: application/zip");}
	header("Content-Length: " . $filesize);

	set_time_limit(0);
	readfile(get_temp_dir() . "/" . $file);

    # Remove archive.
	unlink(get_temp_dir() . "/" . $file);
	exit();	
	}
include "../../../include/header.php";

?>
<div class="BasicsBox">
<h1><?php echo $lang["downloadzip"]?></h1>

<form id='myform' action="collection_download.php?submitted=true" method=post>
<input type=hidden name="collection" value="<?php echo $collection?>">
<input type=hidden name="k" value="<?php echo $k?>">

<?php 
hook("collectiondownloadmessage");
?>

<div class="Question">
<label for="downloadsize"><?php echo $lang["downloadsize"]?></label>

<?php

$maxaccess=collection_max_access($collection);

# analyze available sizes and present options
?><select name="size" class="stdwidth" id="downloadsize">

<?php
foreach($sizes as $size)
	{
	$sizename=$size['name'];
    ?><option value="<?php echo $size['id']?>"><?php echo $sizename; ?></option><?php
    }
?></select>


<div class="clearerleft"> </div></div>


<?php 

if ($zipped_collection_textfile=="true") { ?>
<div class="Question">
<label for="text"><?php echo $lang["zippedcollectiontextfile"]?></label>
<select name="text" class="shrtwidth" id="text">
<option value="true"><?php echo $lang["yes"]?></option>
<option value="false"><?php echo $lang["no"]?></option>
</select>
<div class="clearerleft"> </div></div><?php
}

# Archiver settings
if ($archiver)
    { ?>
    <div class="Question">
    <label for="archivetype"><?php echo $lang["archivesettings"]?></label>
    <div class="tickset">
    <select name="settings" class="stdwidth" id="archivesettings"><?php
    foreach ($collection_download_settings as $key=>$value)
        { ?>
        <option value="<?php echo $key ?>"><?php echo lang_or_i18n_get_translated($value["name"],"archive-") ?></option><?php
        } ?>
    </select>
    <div class="clearerleft"> </div></div><br>
    </div><?php
    } ?>

<div class="QuestionSubmit"> 
<label for="download"> </label>
<input type="button" onclick="if (confirm('<?php echo $lang['confirmcollectiondownload']?>')){$('progress').innerHTML='<strong><br /><br /><?php echo $lang['pleasewait'];?></strong>';$('myform').submit();}" value="&nbsp;&nbsp;<?php echo $lang["action-download"]?>&nbsp;&nbsp;" /></div>

<div class="clearerleft"> </div>
<div id="progress"></div>

</form>

</div>
<?php 
include "../../../include/footer.php";
?>

