<?php
/**
 * View resource page
 * 
 * @package ResourceSpace
 * @subpackage Pages
 */
include "../include/db.php";
include "../include/general.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}
include "../include/search_functions.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";

$ref=getvalescaped("ref","",true);

# Reindex the exif headers, really for debug
if (getval("exif","")!="")
	{
	include "include/image_processing.php";
	include "include/resource_functions.php";
	extract_exif_comment($ref);
	exit();
	}

# fetch the current search (for finding simlar matches)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

# next / previous resource browsing
$go=getval("go","");
if ($go!="")
	{
	$origref=$ref; # Store the reference of the resource before we move, in case we need to revert this.
	
	# Re-run the search and locate the next and previous records.
	$result=do_search($search,$restypes,$order_by,$archive,240+$offset+1);
	if (is_array($result))
		{
		# Locate this resource
		$pos=-1;
		for ($n=0;$n<count($result);$n++)
			{
			if ($result[$n]["ref"]==$ref) {$pos=$n;}
			}
		if ($pos!=-1)
			{
			if (($go=="previous") && ($pos>0)) {$ref=$result[$pos-1]["ref"];}
			if (($go=="next") && ($pos<($n-1))) {$ref=$result[$pos+1]["ref"];if (($pos+1)>=($offset+72)) {$offset=$pos+1;}} # move to next page if we've advanced far enough
			}
		else
			{
			?>
			<script type="text/javascript">
			alert("<?php echo $lang["resourcenotinresults"] ?>");
			</script>
			<?php
			}
		}
	# Check access permissions for this new resource, if an external user.
	if ($k!="" && !check_access_key($ref,$k)) {$ref=$origref;} # cancel the move.
	}


# Load resource data
$resource=get_resource_data($ref);
if ($resource===false) {exit("Resource not found.");}

# Load access level
$access=get_resource_access($ref);

# load custom access level so that we can verify if individual size restrictions
# should be overridden
# but... don't do this if user is not set - presume their access is controlled another way.
if (isset($userref))
	{
	$usercustomaccess = get_custom_access_user($ref,$userref);
	} else {
	$usercustomaccess = false;
	}


# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if ($access==2) 
		{
		exit("This is a confidential resource.");
		}
		
hook("afterpermissionscheck");
		
		
if ($pending_review_visible_to_all && isset($userref) && $resource["created_by"]!=$userref && $resource["archive"]==-1 && !checkperm("e0"))
	{
	# When users can view resources in the 'User Contributed - Pending Review' state in the main search
	# via the $pending_review_visible_to_all option, set access to restricted.
	$access=1;
	}

# If requested, refresh the collection frame (for redirects from saves)
if (getval("refreshcollectionframe","")!="")
	{
	refresh_collection_frame();
	}

# Update the hitcounts for the search keywords (if search specified)
# (important we fetch directly from $_GET and not from a cookie
$usearch=@$_GET["search"];
if ((strpos($usearch,"!")===false) && ($usearch!="")) {update_resource_keyword_hitcount($ref,$usearch);}

# Log this activity
daily_stat("Resource view",$ref);
if ($log_resource_views) {resource_log($ref,'v',0);}

if ($metadata_report && isset($exiftool_path)){
	# Include the metadata report function
	$headerinsert.="
	<script src=\"../lib/js/metadata_report.js\" type=\"text/javascript\"></script>
	";
	}
	
# Show the header/sidebar
include "../include/header.php";

# Load resource field data
$fields=get_resource_field_data($ref);

# Load edit access level (checking edit permissions - e0,e-1 etc. and also the group 'edit filter')
$edit_access=get_edit_access($ref,$resource["archive"],$fields);

?>

<!--Panel for record and details-->
<div class="RecordBox">
<div class="RecordPanel"> 

<div class="RecordHeader">
<?php if (!hook("renderinnerresourceheader")) { ?>

<div class="backtoresults">
<a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>&go=previous">&lt;&nbsp;<?php echo $lang["previousresult"]?></a>
<?php if ($k=="") { ?>
|
<a href="search.php?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&k=<?php echo $k?>"><?php echo $lang["viewallresults"]?></a>
<?php } ?>
|
<a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>&go=next"><?php echo $lang["nextresult"]?>&nbsp;&gt;</a>
</div>

<h1><?php if ($resource["archive"]==2) { ?><span class="ArchiveResourceTitle"><?php echo $lang["archivedresource"]?>:</span>&nbsp;<?php } ?><?php echo highlightkeywords(htmlspecialchars(i18n_get_translated($resource["title"])),$search)?>&nbsp;</h1>
<?php } /* End of renderinnerresourceheader hook */ ?>
</div>

<?php if (isset($resource['is_transcoding']) && $resource['is_transcoding']==1) { ?><div class="PageInformal"><?php echo $lang['resourceistranscoding']?></div><?php } ?>

<?php hook("renderbeforeresourceview"); ?>

<div class="RecordResource">
<?php if (!hook("renderinnerresourceview")) { ?>
<?php if (!hook("renderinnerresourcepreview")) { ?>
<?php

$download_multisize=true;

$flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
if (!file_exists($flvfile)) {$flvfile=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension);}
if (file_exists("../players/type" . $resource["resource_type"] . ".php"))
	{
	include "../players/type" . $resource["resource_type"] . ".php";
	}
elseif (!(isset($resource['is_transcoding']) && $resource['is_transcoding']==1) && file_exists($flvfile) && (strpos(strtolower($flvfile),".".$ffmpeg_preview_extension)!==false))
	{
	# Include the Flash player if an FLV file exists for this resource.
	$download_multisize=false;
	include "flv_play.php";
	
	# If configured, and if the resource itself is not an FLV file (in which case the FLV can already be downloaded), then allow the FLV file to be downloaded.
	if ($flv_preview_downloadable && $resource["file_extension"]!="flv") {$flv_download=true;}
	}
elseif ($resource["has_image"]==1)
	{
	$imagepath=get_resource_path($ref,true,"pre",false,$resource["preview_extension"],-1,1,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
	if (!file_exists($imagepath))
		{
		$imageurl=get_resource_path($ref,false,"thm",false,$resource["preview_extension"],-1,1,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
		}
	else
		{
		$imageurl=get_resource_path($ref,false,"pre",false,$resource["preview_extension"],-1,1,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
		}
	
	?>
	<a href="preview.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>" title="<?php echo $lang["fullscreenpreview"]?>">
	<?php
	if (file_exists($imagepath))
		{ 
		?><img src="<?php echo $imageurl?>" alt="<?php echo $lang["fullscreenpreview"]?>" class="Picture" GALLERYIMG="no" id="previewimage" /><?php 
		} 
	?></a><?php
	}
else
	{
	?>
	<img src="../gfx/<?php echo get_nopreview_icon($resource["resource_type"],$resource["file_extension"],false)?>" alt="" class="Picture" style="border:none;" id="previewimage" />
	<?php
	}

?>
<?php } /* End of renderinnerresourcepreview hook */ ?>

<?php hook("renderbeforerecorddownload"); ?>

<div class="RecordDownload" id="RecordDownload">
<div class="RecordDownloadSpace">
<?php if (!hook("renderinnerresourcedownloadspace")) { ?>
<h2><?php echo $lang["resourcetools"]?></h2>

<?php 

# Look for a viewer to handle the right hand panel. If not, display the standard photo download / file download boxes.
if (file_exists("../viewers/type" . $resource["resource_type"] . ".php"))
	{
	include "../viewers/type" . $resource["resource_type"] . ".php";
	}
elseif (hook("replacedownloadoptions"))
	{
	}
else
	{ 
	?>
<table cellpadding="0" cellspacing="0">
<tr>
<td><?php echo $lang["fileinformation"]?></td>
<td><?php echo $lang["filesize"]?></td>
<td><?php echo $lang["options"]?></td>
</tr>
<?php
$nodownloads=false;$counter=0;$fulldownload=false;
if ($resource["has_image"]==1 && $download_multisize)
	{
	# Restricted access? Show the request link.
		
	
	# List all sizes and allow the user to download them
	$sizes=get_image_sizes($ref,false,$resource["file_extension"]);
	for ($n=0;$n<count($sizes);$n++)
		{
		# DPI calculations 
		if (isset($sizes[$n]['resolution'])&& $sizes[$n]['resolution']!=0){$dpi = $sizes[$n]['resolution'];}
		elseif (isset($dpi)&& $dpi!=0){}
		else { $dpi=300; }
		if (isset($sizes[$n]['unit'])){
			if (trim(strtolower($sizes[$n]['unit']))=="inches"){$imperial_measurements=true;}
			if (trim($sizes[$n]['unit'])=="cm"){$imperial_measurements=false;}
		}
		
		if ($imperial_measurements)
			{	
			$dpi_unit="in";
			$dpi_w=round(($sizes[$n]["width"]/$dpi),1);
			$dpi_h=round(($sizes[$n]["height"]/$dpi),1);
			}
		else
			{
			$dpi_unit="cm";
			$dpi_w=round(($sizes[$n]["width"]/$dpi)*2.54,1);
			$dpi_h=round(($sizes[$n]["height"]/$dpi)*2.54,1);
			}
			
		# MP calculation
		$mp=round(($sizes[$n]["width"]*$sizes[$n]["height"])/1000000,1);
		
		# Is this the original file? Set that the user can download the original file
		# so the request box does not appear.
		$fulldownload=false;
		if ($sizes[$n]["id"]=="") {$fulldownload=true;}
		
		$counter++;
		$headline = ($sizes[$n]['id'] == '') ? $lang["original"] . " " . strtoupper($resource["file_extension"]) . " " . $lang["file"] : i18n_get_translated($sizes[$n]["name"]);
		?>
		<tr class="DownloadDBlend" id="DownloadBox<?php echo $n?>">
		<td><h2><?php echo $headline?></h2>
		<?php  if (is_numeric($sizes[$n]["width"])) { ?>
		<p><?php echo $sizes[$n]["width"]?> x <?php echo $sizes[$n]["height"]?> <?php echo $lang["pixels"]?> <?php if ($mp>=1) { ?> (<?php echo $mp?> MP)<?php } ?></p>
		<p><?php echo $dpi_w?> <?php echo $dpi_unit?> x <?php echo $dpi_h?> <?php echo $dpi_unit?> @ <?php echo $dpi?> <?php echo $lang["ppi"] ?></p></td>
		<?php } ?>
		<td><?php echo $sizes[$n]["filesize"]?></td>
		<!--<td><?php echo $sizes[$n]["filedown"]?></td>-->

		<?php

		# Should we allow this download?
		# For restricted access, only show sizes that are available for the restricted view.
		# This depends on "allow restricted download" in Downloads/Preview Sizes and, for the original file, $restricted_full_download in config.php.
		$downloadthissize=($access==0 || ($access==1 && $sizes[$n]["allow_restricted"])|| ($access==1 && $fulldownload &&$restricted_full_download));
		
		# has this user group been prohibited from this image size for this resource type?
		# if so block download unless they have been given custom access.
		if (checkperm('X'.$resource['resource_type'].'_'.$sizes[$n]['id']) && ($usercustomaccess === false || !$usercustomaccess==='0') )
			{
				$downloadthissize=false;
			}
		
		# If the download is allowed, show a download button, otherwise show a request button.
		if ($downloadthissize)
			{
			?>
			<td class="DownloadButton">
			<a href="terms.php?ref=<?php echo $ref?>&k=<?php echo $k?>&url=<?php echo urlencode("pages/download_progress.php?ref=" . $ref . "&size=" . $sizes[$n]["id"] . "&ext=" . $sizes[$n]["extension"] . "&k=" . $k . "&search=" . urlencode($search) . "&offset=" . $offset . "&archive=" . $archive . "&order_by=" . urlencode($order_by))?>"><?php echo $lang["download"]?></a>
			</td>
			<?php
			}
		elseif (checkperm("q"))
			{
			?>
			<?php if(!hook("resourcerequest")){?>
			<td class="DownloadButton"><a href="resource_request.php?ref=<?php echo $ref?>&k=<?php echo getval("k","")?>"><?php echo $lang["request"]?></a></td>
			<?php } ?>
			<?php
			}
		else
			{
			# No access to this size, and the request functionality has been disabled. Show just 'restricted'.
			?>
			<td class="DownloadButton DownloadDisabled"><?php echo $lang["access1"]?></td>
			<?php
			}
		?>
		</tr>
		<?php
		if (!hook("previewlinkbar")){
			if ($downloadthissize && $sizes[$n]["allow_preview"]==1)
				{ 
				# Add an extra line for previewing
				?> 
				<tr class="DownloadDBlend"><td><h2><?php echo $lang["preview"]?></h2><p><?php echo $lang["fullscreenpreview"]?></p></td><td><?php echo $sizes[$n]["filesize"]?></td><td class="DownloadButton">
				<a href="preview.php?ref=<?php echo $ref?>&ext=<?php echo $resource["file_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>"><?php echo $lang["preview"]?></a>
				</td>
				</tr>
				<?php
				} 
			}
		} /* end hook previewlinkbar */
	}
elseif (strlen($resource["file_extension"])>0 && !($access==1 && $restricted_full_download==false))
	{
	# Files without multiple download sizes (i.e. no alternative previews generated).
	$counter++;
	$path=get_resource_path($ref,true,"",false,$resource["file_extension"]);
	if (file_exists($path))
		{
		?>
		<tr class="DownloadDBlend">
		<td><h2><?php echo strtoupper($resource["file_extension"])?> <?php echo $lang["file"]?></h2></td>
		<td><?php echo formatfilesize(filesize($path))?></td>
		<td class="DownloadButton"><a href="terms.php?ref=<?php echo $ref?>&k=<?php echo $k?>&url=<?php echo urlencode("pages/download_progress.php?ref=" . $ref . "&ext=" . $resource["file_extension"] . "&k=" . $k . "&search=" . urlencode($search) . "&offset=" . $offset . "&archive=" . $archive . "&order_by=" . urlencode($order_by))?>">Download</a></td>
		</tr>
		<?php
		}
	} 
else
	{
	$nodownloads=true;
	}
	
if ($nodownloads || $counter==0)
	{
	# No file. Link to request form.
	?>
	<tr class="DownloadDBlend">
	<td><h2><?php echo ($counter==0)?$lang["access1"]:$lang["offlineresource"]?></h2></td>
	<td>N/A</td>

	<?php if (checkperm("q"))
		{
		?>
		<?php if(!hook("resourcerequest")){?>
		<td class="DownloadButton"><a href="resource_request.php?ref=<?php echo $ref?>&k=<?php echo $k ?>"><?php echo $lang["request"]?></a></td>
		<?php } ?>
		<?php
		}
	else
		{
		?>
		<td class="DownloadButton DownloadDisabled"><?php echo $lang["access1"]?></td>
		<?php
		}
	?>
	</tr>
	<?php
	}
	
if (isset($flv_download) && $flv_download)
	{
	# Allow the FLV preview to be downloaded. $flv_download is set when showing the FLV preview video above.
	?>
	<tr class="DownloadDBlend">
	<td><h2>FLV <?php echo $lang["file"]?></h2></td>
	<td><?php echo formatfilesize(filesize($flvfile))?></td>
	<td class="DownloadButton"><a href="terms.php?ref=<?php echo $ref?>&k=<?php echo $k?>&url=<?php echo urlencode("pages/download_progress.php?ref=" . $ref . "&ext=flv&size=pre&k=" . $k . "&search=" . urlencode($search) . "&offset=" . $offset . "&archive=" . $archive . "&order_by=" . urlencode($order_by))?>">Download</a></td>
	</tr>
	<?php
	}
	
# Alternative files listing
if ($access==0) # open access only (not restricted)
	{
	$altfiles=get_alternative_files($ref);
	for ($n=0;$n<count($altfiles);$n++)
		{
		if ($n==0)
			{
			?>
			<tr>
			<td colspan="3"><?php echo $lang["alternativefiles"]?></td>
			</tr>
			<?php
			}	
		$alt_thm="";$alt_pre="";
		if ($alternative_file_previews)
			{
			$alt_thm_file=get_resource_path($ref,true,"col",false,"jpg",-1,1,false,"",$altfiles[$n]["ref"]);
			if (file_exists($alt_thm_file))
				{
				# Get web path for thumb (pass creation date to help cache refresh)
				$alt_thm=get_resource_path($ref,false,"col",false,"jpg",-1,1,false,$altfiles[$n]["creation_date"],$altfiles[$n]["ref"]);
				}
			$alt_pre_file=get_resource_path($ref,true,"pre",false,"jpg",-1,1,false,"",$altfiles[$n]["ref"]);
			if (file_exists($alt_pre_file))
				{
				# Get web path for preview (pass creation date to help cache refresh)
				$alt_pre=get_resource_path($ref,false,"pre",false,"jpg",-1,1,false,$altfiles[$n]["creation_date"],$altfiles[$n]["ref"]);
				}
			}
		?>
		<tr class="DownloadDBlend" <?php if ($alt_pre!="") { ?>onMouseOver="orig_preview=$('previewimage').src;$('previewimage').src='<?php echo $alt_pre ?>';" onMouseOut="$('previewimage').src=orig_preview;"<?php } ?>>
		<td>
		<?php if ($alt_thm!="") { ?><a href="preview.php?ref=<?php echo $ref?>&alternative=<?php echo $altfiles[$n]["ref"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>"><img src="<?php echo $alt_thm?>" class="AltThumb"></a><?php } ?>
		<h2><?php echo htmlspecialchars($altfiles[$n]["name"])?></h2>
		<!--<p><?php echo strtoupper($altfiles[$n]["file_extension"])?> <?php echo $lang["file"]?></p>-->
		<p><?php echo htmlspecialchars($altfiles[$n]["description"])?></p>
		</td>
		<td><?php echo formatfilesize($altfiles[$n]["file_size"])?></td>
		<?php if ($access==0){?>
		<td class="DownloadButton"><a href="terms.php?ref=<?php echo $ref?>&k=<?php echo $k?>&url=<?php echo urlencode("pages/download_progress.php?ref=" . $ref . "&ext=" . $altfiles[$n]["file_extension"] . "&k=" . $k . "&alternative=" . $altfiles[$n]["ref"] . "&search=" . urlencode($search) . "&offset=" . $offset . "&archive=" . $archive . "&order_by=" . urlencode($order_by))?>">Download</a></td>
		<?php } else { ?>
		<td class="DownloadButton DownloadDisabled"><?php echo $lang["access1"]?></td>
		<?php } ?>
		</tr>
		<?php	
		}
	}
# --- end of alternative files listing

if ($mp3_player){
	//check for mp3 file and allow optional player
	$mp3path=get_resource_path($ref,false,"",false,"mp3");
	$mp3realpath=get_resource_path($ref,true,"",false,"mp3");
	
	if (file_exists($mp3realpath)){
		include "mp3_play.php";
	}
}

?>



</table>
<?php } ?>
<br />
<ul>
<?php 



# ----------------------------- Resource Actions -------------------------------------
hook ("resourceactions") ?>
<?php if ($k=="") { ?>
<?php if (!hook("replaceresourceactions")) {?>
	<?php if (!checkperm("b")) { ?><li><?php echo add_to_collection_link($ref,$search)?>&gt; <?php echo $lang["addtocollection"]?></a></li><?php } ?>
	<?php if ($allow_share && ($access==0 || ($access==1 && $restricted_share))) { ?>
		<li><a href="resource_email.php?ref=<?php echo $ref?>" target="main">&gt; <?php echo $lang["emailresource"]?></a></li>
		<?php if (!$disable_link_in_view) { ?><li><a target="_top" href="<?php echo $baseurl?>/?r=<?php echo $ref?>">&gt; <?php echo $lang["link"]?></a></li><?php }} ?>
	<?php if ($edit_access) { ?>
		<li><a href="edit.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>">&gt; 
			<?php echo $lang["edit"]?></a>
	<?php if (!checkperm("D") and !(isset($allow_resource_deletion) && !$allow_resource_deletion)){?>&nbsp;&nbsp;<a href="delete.php?ref=<?php echo $ref?>">&gt; <?php echo $lang["delete"]?></a><?php } ?></li><?php } ?>
	<?php if (checkperm("e" . $resource["archive"])) { ?><li><a href="log.php?ref=<?php echo $ref?>">&gt; <?php echo $lang["log"]?></a></li><?php } ?>
<?php } /* End replaceresourceactions */ 
		hook("afterresourceactions");
?>
<?php } /* End if ($k!="")*/ ?>
<?php } /* End of renderinnerresourcedownloadspace hook */ ?>
</ul>
<div class="clearerleft"> </div>

<?php
if (!hook("replaceuserratingsbox")){
# Include user rating box, if enabled and the user is not external.
if ($user_rating && $k=="") { include "../include/user_rating.php"; }
} /* end hook replaceuserratingsbox */


?>


</div>
<?php } /* End of renderinnerresourceview hook */ ?>
</div>

<?php hook("renderbeforeresourcedetails"); ?>

<div class="Title"><?php echo $lang["resourcedetails"]?></div>

<?php
$extra="";

#  -----------------------------  Draw tabs ---------------------------
$tabname="";
$tabcount=0;
if (count($fields)>0 && $fields[0]["tab_name"]!="")
	{ ?>
	<div class="TabBar">
	<?php
	$extra="";
	$tabname="";
	$tabcount=0;
	for ($n=0;$n<count($fields);$n++)
		{
		$value=$fields[$n]["value"];

		# draw new tab?
		if (($tabname!=$fields[$n]["tab_name"]) && ($value!="") && ($value!=",") && ($fields[$n]["display_field"]==1))
			{
			?><div id="tabswitch<?php echo $tabcount?>" class="Tab<?php if ($tabcount==0) { ?> TabSelected<?php } ?>"><a href="#" onclick="SelectTab(<?php echo $tabcount?>);return false;"><?php echo i18n_get_translated($fields[$n]["tab_name"])?></a></div><?php
			$tabcount++;
			$tabname=$fields[$n]["tab_name"];
			}
		}
	?>
	</div>
	<script type="text/javascript">
	function SelectTab(tab)
		{
		// Deselect all tabs
		<?php for ($n=0;$n<$tabcount;$n++) { ?>
		document.getElementById("tab<?php echo $n?>").style.display="none";
		document.getElementById("tabswitch<?php echo $n?>").className="Tab";
		<?php } ?>
		document.getElementById("tab" + tab).style.display="block";
		document.getElementById("tabswitch" + tab).className="Tab TabSelected";
		}
	</script>
	<?php
	}
	
	
	
?>

<div id="tab0" class="TabbedPanel<?php if ($tabcount>0) { ?> StyledTabbedPanel<?php } ?>">
<div class="clearerleft"> </div>
<div>
<?php 
#  ----------------------------- Draw standard fields ------------------------
?>
<?php if ($show_resourceid) { ?><div class="itemNarrow"><h3><?php echo $lang["resourceid"]?></h3><p><?php echo $ref?></p></div><?php } ?>
<?php if ($show_access_field) { ?><div class="itemNarrow"><h3><?php echo $lang["access"]?></h3><p><?php echo @$lang["access" . $resource["access"]]?></p></div><?php } ?>
<?php if ($show_hitcount){ ?><div class="itemNarrow"><h3><?php echo $resource_hit_count_on_downloads?$lang["downloads"]:$lang["hitcount"]?></h3><p><?php echo $resource["hit_count"]+$resource["new_hit_count"]?></p></div><?php } ?>
<?php hook("extrafields");?>
<?php
# contributed by field
$udata=get_user($resource["created_by"]);
if ($udata!==false)
	{
	?>
<?php if ($show_contributed_by){?>	<div class="itemNarrow"><h3><?php echo $lang["contributedby"]?></h3><p><?php if (checkperm("u")) { ?><a href="team/team_user_edit.php?ref=<?php echo $udata["ref"]?>"><?php } ?><?php echo highlightkeywords($udata["fullname"],$search)?><?php if (checkperm("u")) { ?></a><?php } ?></p></div><?php } ?>
	<?php
	}


# Show field data
$tabname="";
$tabcount=0;
$fieldcount=0;
$extra="";
for ($n=0;$n<count($fields);$n++)
	{
	$value=$fields[$n]["value"];
	
	# Handle expiry fields
	if ($fields[$n]["type"]==6 && $value!="" && $value<=date("Y-m-d") && $show_expiry_warning) 
		{
		$extra.="<div class=\"RecordStory\"> <h1>" . $lang["warningexpired"] . "</h1><p>" . $lang["warningexpiredtext"] . "</p><p id=\"WarningOK\"><a href=\"#\" onClick=\"document.getElementById('RecordDownload').style.display='block';document.getElementById('WarningOK').style.display='none';\">" . $lang["warningexpiredok"] . "</a></p></div><style>#RecordDownload {display:none;}</style>";
		}
	
	
	if (($value!="") && ($value!=",") && ($fields[$n]["display_field"]==1))
		{
		$title=htmlspecialchars(str_replace("Keywords - ","",i18n_get_translated($fields[$n]["title"])));
		if ($fields[$n]["type"]==4 || $fields[$n]["type"]==6) {$value=NiceDate($value,false,true);}

		# Value formatting
		$value=i18n_get_translated($value);
		if (($fields[$n]["type"]==2) || ($fields[$n]["type"]==3) || ($fields[$n]["type"]==7)) {$value=TidyList($value);}
		$value_unformatted=$value; # store unformatted value for replacement also
		$value=nl2br(htmlspecialchars($value));
		
		# draw new tab panel?
		if (($tabname!=$fields[$n]["tab_name"]) && ($fieldcount>0))
			{
			$tabcount++;
			# Also display the custom formatted data $extra at the bottom of this tab panel.
			?><div class="clearerleft"> </div><?php echo $extra?></div></div><div class="TabbedPanel StyledTabbedPanel" style="display:none;" id="tab<?php echo $tabcount?>"><div><?php	
			$extra="";
			}
		$tabname=$fields[$n]["tab_name"];
		$fieldcount++;		

		if (trim($fields[$n]["display_template"])!="")
			{
			# Process the value using a plugin
			$plugin="../plugins/value_filter_" . $fields[$n]["name"] . ".php";
			if (file_exists($plugin)) {include $plugin;}
			
			# Highlight keywords
			$value=highlightkeywords($value,$search,$fields[$n]["partial_index"],$fields[$n]["name"],$fields[$n]["keywords_index"]);

			# Use a display template to render this field
			$template=$fields[$n]["display_template"];
			$template=str_replace("[title]",$title,$template);
			$template=str_replace("[value]",$value,$template);
			$template=str_replace("[value_unformatted]",$value_unformatted,$template);
			$template=str_replace("[ref]",$ref,$template);
			$extra.=$template;
			}
		else
			{
			#There is a value in this field, but we also need to check again for a current-language value after the i18n_get_translated() function was called, to avoid drawing empty fields
			if ($value!=""){
				# Draw this field normally.

				# Extra word wrapping to break really large words (e.g. URLs)
				$value=wordwrap($value,20,"<br />",true);
				
				# Highlight keywords
				$value=highlightkeywords($value,$search,$fields[$n]["partial_index"],$fields[$n]["name"],$fields[$n]["keywords_index"]);
				?><div class="itemNarrow"><h3><?php echo $title?></h3><p><?php echo $value?></p></div><?php
				}
			}
		}
	}
?><?php hook("extrafields2");?><div class="clearerleft"></div>
<?php echo $extra?>
</div>
</div>
<!-- end of tabbed panel-->
</div></div>
<div class="PanelShadow"></div>
</div>


<?php 
// include optional ajax metadata report
if ($metadata_report && isset($exiftool_path) && $k==""){
    if (($restricted_metadata_report && checkperm("a"))||(!$restricted_metadata_report)) { ?>
        <div class="RecordBox">
        <div class="RecordPanel">  
        <div class="Title"><?php echo $lang['metadata-report']?></div>
        <div id="metadata_report"><a onclick="metadataReport(<?php echo $ref?>);return false;" class="itemNarrow" href="#"><?php echo $lang['viewreport'];?></a><br></div>
        </div>
        <div class="PanelShadow"></div>
        </div>

    <?php } ?>
<?php } ?>
<?php 
$gps_field = sql_value('SELECT ref as value from resource_type_field '. 
                       'where name="geolocation" AND (resource_type="'.$resource['resource_type'].'" OR resource_type="0")','');
if (!$disable_geocoding && isset($gmaps_apikey) && $gps_field!=''){ ?>
    <!-- Begin Geolocation Section -->
    <div class="RecordBox">
    <div class="RecordPanel">
    <div class="Title"><?php echo $lang['location-title']; ?></div>
    <?php 
    
        $ll_field = get_data_by_field($ref, $gps_field);
        if ($ll_field!=''){
            $lat_long = explode(',', get_data_by_field($ref,$gps_field));
        ?>
            <?php if ($edit_access) { ?>
            <ul class="HorizontalNav"><li><a href="geo_edit.php?ref=<?php echo $ref; ?>"><?php echo $lang['location-edit']; ?></a></li></ul><?php } ?>
            <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $gmaps_apikey; ?>&sensor=false"
                    type="text/javascript"></script>
            <script type="text/javascript">
                function geo_loc_initialize() {
                  if (GBrowserIsCompatible()) {
                    var map = new GMap2(document.getElementById("map_canvas"));
                    map.setCenter(new GLatLng(<?php echo $lat_long[0];?>, <?php echo $lat_long[1]; ?>), 13);
                    map.setUIToDefault();
                    map.addOverlay(new GMarker(new GLatLng(<?php echo $lat_long[0]; ?>, <?php echo $lat_long[1];?>)));
                  }
                }
                Event.observe(window, 'load', geo_loc_initialize);
                Event.observe(window, 'unload', GUnload);
            </script>
            <div id="map_canvas" style="width: *; height: 300px; display:block; float:none;" class="Picture" ></div>
    <?php } else {?>
        <a href="geo_edit.php?ref=<?php echo $ref; ?>">&gt; <?php echo $lang['location-add'];?></a>
    <?php }?>
    </div>
    <div class="PanelShadow"></div>
    </div>
    <!-- End Geolocation Section -->
<?php } ?>

<?php hook("customrelations"); //For future template/spawned relations in Web to Print plugin ?>

<?php
# -------- Related Resources (must be able to search for this to work)
if (checkperm("s") && ($k=="")) {
$result=do_search("!related" . $ref);
if (count($result)>0) 
	{
	# -------- Related Resources by File Extension
	if($sort_relations_by_filetype){	
		#build array of related resources' file extensions
		for ($n=0;$n<count($result);$n++){
			$related_file_extension=$result[$n]["file_extension"];
			$related_file_extensions[]=$related_file_extension;
			}
		#reduce extensions array to unique values
		$related_file_extensions=array_unique($related_file_extensions);
		$count_extensions=0;
		foreach($related_file_extensions as $rext){
		?><!--Panel for related resources-->
		<div class="RecordBox">
		<div class="RecordPanel">  

		<div class="RecordResouce">
		<div class="Title"><?php echo $lang["relatedresources"]?> - <?php echo strtoupper($rext);?></div>
		<?php
		# loop and display the results by file extension
		for ($n=0;$n<count($result);$n++)			
			{
			if ($result[$n]["file_extension"]==$rext){
				$rref=$result[$n]["ref"];
				?>
				<!--Resource Panel-->
				<div class="CollectionPanelShell">
				<table border="0" class="CollectionResourceAlign"><tr><td>
				<a target="main" href="view.php?ref=<?php echo $rref?>&search=<?php echo urlencode("!related" . $ref)?>"><?php if ($result[$n]["has_image"]==1) { ?><img border=0 src="<?php echo get_resource_path($rref,false,"col",false,$result[$n]["preview_extension"],-1,1,checkperm("w"))?>" class="CollectImageBorder"/><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true)?>"/><?php } ?></a></td>
				</tr></table>
				<div class="CollectionPanelInfo"><a target="main" href="view.php?ref=<?php echo $rref?>"><?php echo tidy_trim(i18n_get_translated($result[$n]["title"]),25)?></a>&nbsp;</div>
				</div>
				<?php		
				}
			}
		?>
		<div class="clearerleft"> </div>
		<?php $count_extensions++; if ($count_extensions==count($related_file_extensions)){?><a href="search.php?search=<?php echo urlencode("!related" . $ref) ?>"><?php echo $lang["clicktoviewasresultset"]?></a><?php }?>
		</div>
		</div>
		<div class="PanelShadow"></div>
		</div><?php
		} #end of display loop by resource extension
	} #end of IF sorted relations
	
	
	# -------- Related Resources (Default)
	else { 
		 ?><!--Panel for related resources-->
		<div class="RecordBox">
		<div class="RecordPanel">  

		<div class="RecordResouce">
		<div class="Title"><?php echo $lang["relatedresources"]?></div>
		<?php
    	# loop and display the results
    	for ($n=0;$n<count($result);$n++)            
        	{
        	$rref=$result[$n]["ref"];
        	?>
        	<!--Resource Panel-->
        	<div class="CollectionPanelShell">
            <table border="0" class="CollectionResourceAlign"><tr><td>
            <a target="main" href="view.php?ref=<?php echo $rref?>&search=<?php echo urlencode("!related" . $ref)?>"><?php if ($result[$n]["has_image"]==1) { ?><img border=0 src="<?php echo get_resource_path($rref,false,"col",false,$result[$n]["preview_extension"],-1,1,checkperm("w"))?>" class="CollectImageBorder"/><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true)?>"/><?php } ?></a></td>
            </tr></table>
            <div class="CollectionPanelInfo"><a target="main" href="view.php?ref=<?php echo $rref?>"><?php echo tidy_trim(i18n_get_translated($result[$n]["title"]),15)?></a>&nbsp;</div>
        </div>
        <?php        
        }
    ?>
    <div class="clearerleft"> </div>
        <a href="search.php?search=<?php echo urlencode("!related" . $ref) ?>"><?php echo $lang["clicktoviewasresultset"]?></a>

    </div>
    </div>
    <div class="PanelShadow"></div>
    </div><?php
		}# end related resources display
	} 
	# -------- End Related Resources
	
	

if ($show_related_themes==true ){
# -------- Public Collections / Themes
$result=get_themes_by_resource($ref);
if (count($result)>0) 
	{
	?><!--Panel for related themes / collections -->
	<div class="RecordBox">
	<div class="RecordPanel">  
	
	<div class="RecordResouce BasicsBox">
	<div class="Title"><?php echo $lang["collectionsthemes"]?></div>
	<div class="VerticalNav">
	<ul>
	<?php
		# loop and display the results
		for ($n=0;$n<count($result);$n++)			
			{
			?>
			<li><a href="search.php?search=!collection<?php echo $result[$n]["ref"]?>"><?php echo (strlen($result[$n]["theme"])>0)?htmlspecialchars(str_replace("*","",$result[$n]["theme"]) . " / "):$lang["public"] . " : " . htmlspecialchars($result[$n]["fullname"] . " / ")?><?php echo htmlspecialchars($result[$n]["name"])?></a></li>
			<?php		
			}
		?>
	</ul>
	</div>
	</div>
	</div>
	<div class="PanelShadow"></div>
	</div><?php
	}} 
?>



<?php if ($enable_find_similar) { ?>
<!--Panel for search for similar resources-->
<div class="RecordBox">
<div class="RecordPanel"> 


<div class="RecordResouce">
<div class="Title"><?php echo $lang["searchforsimilarresources"]?></div>
<?php if ($resource["has_image"]==1) { ?>

<!--
<p>Find resources with a <a href="search.php?search=<?php echo urlencode("!rgb:" . $resource["image_red"] . "," . $resource["image_green"] . "," . $resource["image_blue"])?>">similar colour theme</a>.</p>
<p>Find resources with a <a href="search.php?search=<?php echo urlencode("!colourkey" . $resource["colour_key"]) ?>">similar colour theme (2)</a>.</p>
-->

<?php } ?>
<script type="text/javascript">
function UpdateResultCount()
	{
	// set the target of the form to be the result count iframe and submit
	document.getElementById("findsimilar").target="resultcount";
	document.getElementById("countonly").value="yes";
	document.getElementById("findsimilar").submit();
	document.getElementById("findsimilar").target="";
	document.getElementById("countonly").value="";
	}
</script>

<form method="post" action="find_similar.php" id="findsimilar">
<input type="hidden" name="resource_type" value="<?php echo $resource["resource_type"]?>">
<input type="hidden" name="countonly" id="countonly" value="">
<?php
$keywords=get_resource_top_keywords($ref,30);
$searchwords=split_keywords($search);
for ($n=0;$n<count($keywords);$n++)
	{
	?>
	<div class="SearchSimilar"><input type=checkbox name="keyword_<?php echo urlencode($keywords[$n])?>" value="yes"
	<?php if (in_array($keywords[$n],$searchwords)) {?>checked<?php } ?> onClick="UpdateResultCount();">&nbsp;<?php echo $keywords[$n]?></div>
	<?php
	}
?>
<div class="clearerleft"> </div>
<br />
<input name="search" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" id="dosearch"/>
<iframe src="blank.html" frameborder=0 scrolling=no width=1 height=1 style="visibility:hidden;" name="resultcount" id="resultcount"></iframe>
</form>
<div class="clearerleft"> </div>
</div>
</div>
<div class="PanelShadow"></div>
</div></div>
<?php } ?>



<?php } # end of block that requires search permissions?>

<?php
include "../include/footer.php";
?>
