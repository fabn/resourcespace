<?php 
include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection","",true),$k))) {include "../include/authenticate.php";}
include "../include/search_functions.php";
include "../include/resource_functions.php";

$uniqid="";$id="";
$collection=getvalescaped("collection","",true);
$size=getvalescaped("size","");
$submitted=getvalescaped("submitted","");
$includetext=getvalescaped("text","false");
$useoriginal=getvalescaped("use_original","no");
$collectiondata=get_collection($collection);
$settings_id=getvalescaped("settings","");
$uniqid=getval("id",uniqid("Col".$collection."-"));
function findDuplicates($data,$dupval) {
$nb= 0;
foreach($data as $key => $val) {if ($val==$dupval) {$nb++;}}
return $nb;
}

if ($use_zip_extension){
	// set the time limit to unlimited, default 300 is not sufficient here.
	set_time_limit(0);
	$headerinsert="<script type=\"text/javascript\" src=\"".$baseurl."/lib/js/jquery-periodical-updater.js\"></script>";
}

function update_zip_progress_file($note){
	global $progress_file;
	$fp = fopen($progress_file, 'w');		
	$filedata=$note;
	fwrite($fp, $filedata);
	fclose($fp);
}

$archiver_fullpath = get_utility_path("archiver");

if (!isset($zipcommand) && !$use_zip_extension)
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

#build the available sizes array
for ($n=0;$n<count($result);$n++)
	{
	$ref=$result[$n]["ref"];
	# Load access level (0,1,2) for this resource
	$access=get_resource_access($result[$n]);
	
	# get all possible sizes for this resource
	$sizes=get_all_image_sizes(false,$access>=1);

	#check availability of original file 
	$p=get_resource_path($ref,true,"",false,$result[$n]["file_extension"]);
	if (file_exists($p) && (($access==0) || ($access==1 && $restricted_full_download)) && resource_download_allowed($ref,'',$result[$n]['resource_type']))
		{
		$available_sizes['original'][]=$ref;
		} 
	
	# check for the availability of each size and load it to the available_sizes array
	foreach ($sizes as $sizeinfo)
		{
		$size_id=$sizeinfo['id'];
		# get file extension from database or use jpg.
		$pextension = $size == 'original' ? $result[$n]["file_extension"] : 'jpg';
		$p=get_resource_path($ref,true,$size_id,false,$pextension);
		if (file_exists($p) && resource_download_allowed($ref,$size_id,$result[$n]['resource_type'])) $available_sizes[$size_id][]=$ref;
		
		}
	}

	
#print_r($available_sizes);
$used_resources=array();
$subbed_original_resources = array();
if ($submitted != "")
	{
    # Define the archive file.
	if ($use_zip_extension){
		$id=getvalescaped("id","");
		$progress_file=get_temp_dir(false,$id) . "/progress_file.txt";
		$zipfile = get_temp_dir(false,$id)."/zip.zip";
		$zip = new ZipArchive();
		$zip->open($zipfile, ZIPARCHIVE::CREATE);
	}
    else if ($archiver)
        {
        $zipfile = get_temp_dir(false,$id)."/".$lang["collectionidprefix"] . $collection . "-" . $size . "." . $collection_download_settings[$settings_id]["extension"];
        }
    else
        {
        $zipfile = get_temp_dir(false,$id)."/".$lang["collectionidprefix"] . $collection . "-" . $size . ".zip";
        }
    
	$path="";
	$deletion_array=array();
	// set up an array to store the filenames as they are found (to analyze dupes)
	$filenames=array();
	
	# Build a list of files to download
	for ($n=0;$n<count($result);$n++)
		{
		$copy=false; 
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
				$tmpfile=write_metadata($p,$ref,$id); // copies file
				if($tmpfile!==false && file_exists($tmpfile)){
					$p=$tmpfile; // file already in tmp, just rename it
				}	
				else {
					$copy=true; // copy the file from filestore rather than renaming
				}
	
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
						
						// check if a file has already been processed with this name
						$orig_filename=$filename;
						if (in_array($filename,$filenames)){
							// if so, append a dupe tag
							$path_parts=pathinfo($filename);
							if (isset($path_parts['extension'])&& isset($path_parts['filename'])){
								$filename_ext=$path_parts['extension'];
								$filename_wo=$path_parts['filename'];
								$x=findDuplicates($filenames,$filename);
								$filename=$filename_wo."_dupe".$x.".".$filename_ext;
							}
						}
						//add original file name to array
						$filenames[]=$orig_filename;
						
                        # Copy to tmp (if exiftool failed) or rename this file
                        # this is for extra efficiency to reduce copying and disk usage
                        
                        if (!$use_zip_extension){
							// the copy or rename to the filename is not necessary using the zip extension since the archived filename can be specified.
							$newpath = get_temp_dir(false,$id) . "/" . $filename;
							if (!$copy){rename($p, $newpath);} else {copy($p,$newpath);}
							# Add the temporary file to the post-archiving deletion list.
							$deletion_array[]=$newpath;
							
							# Set p so now we are working with this new file
							$p=$newpath;
							}

						}
					}
				
				#Add resource data/collection_resource data to text file
				if (($zipped_collection_textfile==true)&&($includetext=="true")){ 
					if ($size==""){$sizetext="";}else{$sizetext="-".$size;}
					if ($subbed_original) { $sizetext = '(' . $lang['substituted_original'] . ')'; }
					$fields=get_resource_field_data($ref);
					$commentdata=get_collection_resource_comment($ref,$collection);
					if (count($fields)>0){ 
					$text.= ($sizetext=="" ? "" : $sizetext) ." ". $filename. "\r\n-----------------------------------------------------------------\r\n";
					$text.= $lang["resourceid"] . ": " . $ref . "\r\n";
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
				if ($use_zip_extension){
					$zip->addFile($p,$filename);
					update_zip_progress_file("file ".$zip->numFiles);
				}
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
    if ($path=="") {exit($lang["nothing_to_download"]);}	


    # Append summary notes about the completeness of the package, write the text file, add to archive, and schedule for deletion
    if (($zipped_collection_textfile==true)&&($includetext=="true")){
        $qty_sizes = count($available_sizes[$size]);
        $qty_total = count($result);
        $text.= $lang["status-note"] . ": " . $qty_sizes . " " . $lang["of"] . " " . $qty_total . " ";
        switch ($qty_total) {
        case 0:
            $text.= $lang["resource-0"] . " ";
            break;
        case 1:
            $text.= $lang["resource-1"] . " ";
            break;
        default:
            $text.= $lang["resource-2"] . " ";
            break;
        }

        switch ($qty_sizes) {
        case 0:
            $text.= $lang["were_available-0"] . " ";
            break;
        case 1:
            $text.= $lang["were_available-1"] . " ";
            break;
        default:
            $text.= $lang["were_available-2"] . " ";
            break;
        }
        $text.= $lang["forthispackage"] . ".\r\n\r\n";
    
        foreach ($result as $resource) {
	    if (in_array($resource['ref'],$subbed_original_resources)){
		$text.= $lang["didnotinclude"] . ": " . $resource['ref'];
		$text.= " (".$lang["substituted_original"] . ")";
		$text.= "\r\n";
	    } elseif (!in_array($resource['ref'],$used_resources)) {
                $text.= $lang["didnotinclude"] . ": " . $resource['ref'];
		$text.= "\r\n";
            }
        }

        $textfile = get_temp_dir(false,$id) . "/". $collection . "-" . safe_file_name(i18n_get_translated($collectiondata['name'])) . $sizetext . ".txt";
        $fh = fopen($textfile, 'w') or die("can't open file");
        fwrite($fh, $text);
        fclose($fh);
		if ($use_zip_extension){
			$zip->addFile($textfile,$collection . "-" . safe_file_name(i18n_get_translated($collectiondata['name'])) . $sizetext . ".txt");
        } else {
			$path.=$textfile . "\r\n";	
        }
        $deletion_array[]=$textfile;	
    }


	# Write command parameters to file.
	//update_progress_file("writing zip command");	
	if (!$use_zip_extension){
		$cmdfile = get_temp_dir(false,$id) . "/zipcmd" . $collection . "-" . $size . ".txt";
		$fh = fopen($cmdfile, 'w') or die("can't open file");
		fwrite($fh, $path);
		fclose($fh);
	}

    # Execute the archiver command.
    # If $collection_download is true the $collection_download_settings are used if defined, else the legacy $zipcommand is used.
    if ($use_zip_extension){
		update_zip_progress_file("zipping");
		$wait=$zip->close();
		update_zip_progress_file("complete");
		sleep(1);
	}
    else if ($archiver)
        {
        run_command($archiver_fullpath . " " . $collection_download_settings[$settings_id]["arguments"] . " " . escapeshellarg($zipfile) . " " . $archiver_listfile_argument . escapeshellarg($cmdfile));
        }
    else if (!$use_zip_extension)
        {
        if ($config_windows)
            # Add the command file, containing the filenames, as an argument.
            {
            exec("$zipcommand " . escapeshellarg($zipfile) . " @" . escapeshellarg($cmdfile));
            }
        else
            {
            # Pipe the command file, containing the filenames, to the executable.
            exec("$zipcommand " . escapeshellarg($zipfile) . " -@ < " . escapeshellarg($cmdfile));
            }
        }

    # Archive created, schedule the command file for deletion.
	if (!$use_zip_extension){
		$deletion_array[]=$cmdfile;
	}
	
	# Remove temporary files.
	foreach($deletion_array as $tmpfile) {
		delete_exif_tmpfile($tmpfile);
	}

    # Get the file size of the archive.
    $filesize = @filesize_unlimited($zipfile);

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
    else {
	header("Content-Type: application/zip");}
	if ($use_zip_extension){header("Content-Transfer-Encoding: binary");}
	header("Content-Length: " . $filesize);

	ignore_user_abort(true); // collection download has a problem with leaving junk files when this script is aborted client side. This seems to fix that by letting the process run its course.
	set_time_limit(0);
	readfile($zipfile);

	

    # Remove archive.
	unlink($zipfile);
	
	if ($use_zip_extension){
				
				unlink($progress_file);
				rmdir(get_temp_dir(false,$id));
	}
	
	
	exit();	
	}
include "../include/header.php";

?>
<div class="BasicsBox">
<h1><?php echo $lang["downloadzip"]?></h1>
<?php if ($use_zip_extension){?>
<script>
function ajax_download()
	{	
	document.getElementById('downloadbuttondiv').style.display='none';	
	document.getElementById('progress').innerHTML='<br /><br /><?php echo $lang["collectiondownloadinprogress"];?>';
	document.getElementById('progress3').style.display='none';
	document.getElementById('progressdiv').style.display='block';
	var ifrm = document.getElementById('downloadiframe');
	
    ifrm.src = "collection_download.php?submitted=true&"+jQuery('#myform').serialize();
    
	progress= jQuery("progress3").PeriodicalUpdater("ajax/collection_download_progress.php?id=<?php echo $uniqid?>", {
        method: 'post',          // method; get or post
        data: '',               //  e.g. {name: "John", greeting: "hello"}
        minTimeout: 500,       // starting value for the timeout in milliseconds
        maxTimeout: 2000,       // maximum length of time between requests
        multiplier: 1.5,          // the amount to expand the timeout by if the response hasn't changed (up to maxTimeout)
        type: 'text'           // response type - text, xml, json, etc.  
       

    }, function(remoteData, success, xhr, handle) {
         if (remoteData.indexOf("file")!=-1){
					var numfiles=remoteData.replace("file ","");
					if (numfiles==1){
						var message=numfiles+' <?php echo $lang['fileaddedtozip']?>';
					} else { 
						var message=numfiles+' <?php echo $lang['filesaddedtozip']?>';
					}	 
					var status=(numfiles/<?php echo count($result)?>*100)+"%";
					console.log(status);
					document.getElementById('progress2').innerHTML=message;
				}
				else if (remoteData=="complete"){ 
				   document.getElementById('progress2').innerHTML="<?php echo $lang['zipcomplete']?>";
                   document.getElementById('progress').style.display="none";
                   progress.stop();    
                }  
                else {
					// fix zip message or allow any
					console.log(remoteData);
					document.getElementById('progress2').innerHTML=remoteData.replace("zipping","<?php echo $lang['zipping']?>");
                }
     
    });
		
}


        


</script>
<?php } ?>

<?php if (!$use_zip_extension){?>
	<form id='myform' action="collection_download.php?submitted=true" method=post>
<?php } else { ?>
	<form id='myform'>
<?php } ?>

<input type=hidden name="collection" value="<?php echo $collection?>">
<input type=hidden name="k" value="<?php echo $k?>">

<?php if ($use_zip_extension){?>
	<input type=hidden name="id" value="<?php echo $uniqid?>">
	<iframe id="downloadiframe" <?php if (!$debug_direct_download){?>style="display:none;"<?php } ?>></iframe>
<?php } ?>

<?php 
hook("collectiondownloadmessage");
?>

<div class="Question">
<label for="downloadsize"><?php echo $lang["downloadsize"]?></label>
<div class="tickset">
<?php

$maxaccess=collection_max_access($collection);
$sizes=get_all_image_sizes(false,$maxaccess>=1);

$available_sizes=array_reverse($available_sizes,true);

# analyze available sizes and present options
?><select name="size" class="stdwidth" id="downloadsize"><?php

if (array_key_exists('original',$available_sizes)) {
    ?><option value="original"><?php
    $qty_originals = count($available_sizes['original']);
    echo $lang['original'] . " (" . $qty_originals . " " . $lang["of"] . " " . count($result) . " ";
    switch ($qty_originals) {
    case 0:
        echo $lang["are_available-0"];
        break;
    case 1:
        echo $lang["are_available-1"];
        break;
    default:
        echo $lang["are_available-2"];
        break;
    }
    echo ")";
    ?></option><?php
} ?>

<?php

foreach ($available_sizes as $key=>$value) {
    foreach($sizes as $size){if ($size['id']==$key) {$sizename=$size['name'];}}
	if ($key!='original') {
	    ?><option value="<?php echo $key?>"><?php
	    $qty_values = count($value);
        echo $sizename . " (" . $qty_values . " " . $lang["of"] . " " . count($result) . " ";
        switch ($qty_values) {
        case 0:
            echo $lang["are_available-0"];
            break;
        case 1:
            echo $lang["are_available-1"];
            break;
        default:
            echo $lang["are_available-2"];
            break;
        }
        echo ")";
        ?></option><?php
    }
} ?></select>

<div class="clearerleft"> </div></div>
<div class="clearerleft"> </div></div>

<div class="Question">
<label for="use_original"><?php echo $lang['use_original_if_size']; ?> <br /><?php
        if (isset($qty_originals))
            {
            switch ($qty_originals)
                {
                case 1:
                    echo "(" . $qty_originals . " " . $lang["originals-available-1"] . ")";
                    break;
                default:
                    echo "(" . $qty_originals . " " . $lang["originals-available-2"] . ")";
                    break;
                }
            }
        else
            {
            echo "(0 " . $lang["originals-available-0"] . ")";
            }
        ?></label><input type=checkbox id="use_original" name="use_original" value="yes" >
<div class="clearerleft"> </div></div>

<?php 

if ($zipped_collection_textfile=="true") { ?>
<div class="Question">
<label for="text"><?php echo $lang["zippedcollectiontextfile"]?></label>
<select name="text" class="shrtwidth" id="text">
<option value="true"><?php echo $lang["yes"]?></option>
<option value="false"><?php echo $lang["no"]?></option>
</select>
<div class="clearerleft"></div>
</div>

<?php
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
    <div class="clearerleft"></div></div><br>
    </div><?php
    } ?>

<div class="QuestionSubmit" id="downloadbuttondiv"> 
<label for="download"> </label>
<?php if (!$use_zip_extension) { ?>
<input type="button" onclick="if (confirm('<?php echo $lang['confirmcollectiondownload'] ?>')) {jQuery('#progress').html('<strong><br /><br /><?php echo $lang['pleasewait'];?></strong>');jQuery('#myform').submit();}" value="&nbsp;&nbsp;<?php echo $lang["action-download"]?>&nbsp;&nbsp;" />
<?php } else { ?>
<input type="button" onclick="ajax_download();" value="&nbsp;&nbsp;<?php echo $lang["action-download"]?>&nbsp;&nbsp;" />
<?php } ?>
<div class="clearerleft"> </div>
</div>

<div id="progress"></div>

<?php if ($use_zip_extension){?>
<div class="Question" id="progressdiv" style="display:none;border-top:none;"> 
<label><?php echo $lang['progress']?></label>
<div class="Fixed" id="progress3" ></div>
<div class="Fixed" id="progress2" ></div>


<div class="clearerleft"></div></div>
<?php } ?>
</form>



</div>
<?php 
include "../include/footer.php";
?>

