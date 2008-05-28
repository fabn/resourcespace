<?
include "include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "include/authenticate.php";}

include "include/general.php";
include "include/search_functions.php";
include "include/resource_functions.php";

$ref=getvalescaped("ref","");

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
$offset=getvalescaped("offset",0);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0);

# next / previous resource browsing
$go=getval("go","");
if ($go!="")
	{
	# Re-run the search and locate the next and previous records.
	$result=do_search($search,$restypes,$order_by,$archive,72+$offset+1);
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
		}
	}


# Load resource data
$resource=get_resource_data($ref);

# Load access level
$access=$resource["access"];
if (checkperm("v"))
	{
	$access=0; # Permission to access all resources
	}
else
	{
	if ($k!="")
		{
		#if ($access==3) {$access=2;} # Can't support custom group permissions for non-users
		if ($access==3) {$access=0;}
		}
	elseif ($access==3)
		{
		# Load custom access level
		$access=get_custom_access($ref,$usergroup);
		}
	}

# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if ($access==2) 
		{
		exit("This is a confidential resource.");
		}

# Update the hitcounts for the search keywords (if search specified)
# (important we fetch directly from $_GET and not from a cookie
$usearch=@$_GET["search"];
if ((strpos($usearch,"!")===false) && ($usearch!="")) {update_resource_keyword_hitcount($ref,$usearch);}

# Log this activity
daily_stat("Resource view",$ref);

include "include/header.php";
?>

<!--Panel for record and details-->
<div class="RecordBox">
<div class="RecordPanel"> 

<div class="RecordHeader">
<? if (!hook("renderinnerresourceheader")) { ?>
<? if ($k=="") { ?>
<div class="backtoresults">
<a href="view.php?ref=<?=$ref?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&go=previous">&lt;&nbsp;<?=$lang["previousresult"]?></a>
|
<a href="search.php<? if (strpos($search,"!")!==false) {?>?search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?><? } ?>"><?=$lang["viewallresults"]?></a>
|
<a href="view.php?ref=<?=$ref?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&go=next"><?=$lang["nextresult"]?>&nbsp;&gt;</a>
</div>
<? } ?>
<h1><? if ($resource["archive"]==2) { ?><span class="ArchiveResourceTitle">ARCHIVE RESOURCE:</span>&nbsp;<? } ?><?=highlightkeywords(htmlspecialchars($resource["title"]),$search)?></h1>
<? } /* End of renderinnerresourceheader hook */ ?>
</div>

<? hook("renderbeforeresourceview"); ?>

<div class="RecordResouce">
<? if (!hook("renderinnerresourceview")) { ?>
<? if (!hook("renderinnerresourcepreview")) { ?>
<?
$flvfile=get_resource_path($ref,"",false,"flv");
if (file_exists("plugins/players/type" . $resource["resource_type"] . ".php"))
	{
	include "plugins/players/type" . $resource["resource_type"] . ".php";
	}
elseif (file_exists($flvfile) && (strpos(strtolower($flvfile),".flv")!==false))
	{
	# Include the Flash player if an FLV file exists for this resource.
	include "flv_play.php";
	}
elseif ($resource["has_image"]==1)
	{
	$imagepath=get_resource_path($ref,"pre",false,$resource["preview_extension"]);
	$previewpath=get_resource_path($ref,"scr",false,$resource["preview_extension"]);
	if (!file_exists($previewpath)) {$previewpath=get_resource_path($ref,"",false,$resource["preview_extension"]);}
	
	if (file_exists($previewpath) && $access==0) { ?><a href="preview.php?ref=<?=$ref?>&ext=<?=$resource["preview_extension"]?>&k=<?=$k?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>"><? }
	if (file_exists($imagepath))
		{ ?><img src="<?=$imagepath?>?nc=<?=time()?>" alt="" class="Picture" /><? } 
	else # use the thumbnail instead, the uploaded file wasn't big enough to create a preview.
		{ ?><img src="<?=get_resource_path($ref,"thm",false)?>" alt="" class="Picture" /><? }
	if (file_exists($previewpath)) { ?></a><? }
	}
else
	{
	?>
	<img src="gfx/type<?=$resource["resource_type"]?>.gif" alt="" class="Picture" style="border:none;" />
	<?
	}
?>
<? } /* End of renderinnerresourcepreview hook */ ?>

<? hook("renderbeforerecorddownload"); ?>

<div class="RecordDownload" id="RecordDownload">
<div class="RecordDownloadSpace">
<? if (!hook("renderinnerresourcedownloadspace")) { ?>
<h2><?=$lang["resourcetools"]?></h2>

<? 
# Look for a viewer to handle the right hand panel. If not, display the standard photo download / file download boxes.
if (file_exists("viewers/type" . $resource["resource_type"] . ".php"))
	{
	include "viewers/type" . $resource["resource_type"] . ".php";
	}
else
	{ ?>
<table cellpadding="0" cellspacing="0">
<tr>
<td><?=$lang["fileinformation"]?></td>
<td><?=$lang["filesize"]?></td>
<td><?=$lang["options"]?></td>
</tr>
<?
$nodownloads=false;$counter=0;
if (($resource["has_image"]==1) && (($resource["file_extension"]=="jpg") || ($resource["file_extension"]=="jpeg")))
	{
	# Work out if the user is allowed to download these images
	$download=true;
	if (checkperm("v")) {$download=true;}
	if (($k!="") && (check_access_key($ref,$k))) {$download=true;} # External users to whom the resource has been e-mailed

	$sizes=get_image_sizes($ref,false,$resource["file_extension"]);
	for ($n=0;$n<count($sizes);$n++)
		{
		# DPI calculations
		$dpi=300;
		$dpi_w=round(($sizes[$n]["width"]/$dpi)*2.54,1);
		$dpi_h=round(($sizes[$n]["height"]/$dpi)*2.54,1);

		# MP calculation
		$mp=round(($sizes[$n]["width"]*$sizes[$n]["height"])/1000000,1);
		
		$downloadthissize=$download;
		if (($access==1) && $downloadthissize)
			{
			# Additional check on restricted downloads - is this download available for restricted access?
			if ($sizes[$n]["allow_restricted"]!=1) {$downloadthissize=false;}
			}

		if (!checkperm("v") && !checkperm("g") && $downloadthissize && $k=="") 
			{
			# Restricted access if used does not have 'g' permission
			# Only allow downloads of sizes where 'allow restricted download' is set to 1.
			if (!$sizes[$n]["allow_restricted"]==1) {$downloadthissize=false;}
			}
			
		if ($downloadthissize)
			{
			$counter++;
			?>
			<tr class="DownloadDBlend" id="DownloadBox<?=$n?>">
			<td><h2><?=i18n_get_translated($sizes[$n]["name"])?></h2>
			<p><?=$sizes[$n]["width"]?> x <?=$sizes[$n]["height"]?> <?=$lang["pixels"]?> <? if ($mp>=1) { ?> (<?=$mp?> MP)<? } ?></p>
			<p><?=$dpi_w?> cm x <?=$dpi_h?> cm @ 300dpi</p></td>
			<td><?=$sizes[$n]["filesize"]?></td>
			<!--<td><?=$sizes[$n]["filedown"]?></td>-->
			<td class="DownloadButton HorizontalWhiteNav">
			<a href="terms.php?ref=<?=$ref?>&k=<?=$k?>&url=<?=urlencode("download_progress.php?ref=" . $ref . "&size=" . $sizes[$n]["id"] . "&ext=" . $resource["file_extension"] . "&k=" . $k)?>"><?=$lang["download"]?></a>
			</td></tr>
			<?
			}
		if ($downloadthissize && $sizes[$n]["allow_preview"]==1)
			{ 
		 	# Add an extra line for previewing
		 	?> 
			<tr class="DownloadDBlend"><td><h2><?=$lang["preview"]?></h2><p><?=$lang["fullscreenpreview"]?></p></td><td><?=$sizes[$n]["filesize"]?></td><td class="DownloadButton HorizontalWhiteNav">
			<a href="preview.php?ref=<?=$ref?>&ext=<?=$resource["file_extension"]?>&k=<?=$k?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>"><?=$lang["preview"]?></a>
			</td>
			</tr>
      		<?
      		} 
		}
	}
elseif (strlen($resource["file_extension"])>0 && !($access==1 && $restricted_full_download==false))
	{
	# Files without multiple download sizes (i.e. no thumbnail, or ImageMagick generated).
	$counter++;
	$path=get_resource_path($ref,"",false,$resource["file_extension"]);
	if (file_exists($path))
		{
		?>
		<tr class="DownloadDBlend">
		<td><h2><?=strtoupper($resource["file_extension"])?> <?=$lang["file"]?></h2></td>
		<td><?=formatfilesize(filesize($path))?></td>
		<td class="DownloadButton HorizontalWhiteNav"><a href="terms.php?ref=<?=$ref?>&k=<?=$k?>&url=<?=urlencode("download_progress.php?ref=" . $ref . "&ext=" . $resource["file_extension"] . "&k=" . $k)?>">Download</a></td>
		</tr>
		<?
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
	<td><h2><?=($counter==0)?$lang["access1"]:$lang["offlineresource"]?></h2></td>
	<td>N/A</td>
	<td class="DownloadButton HorizontalWhiteNav"><a href="resource_request.php?ref=<?=$ref?>"><?=$lang["request"]?></a></td>
	</tr>
	<?
	}
?>
</table>
<? } ?>
<br />
<? hook ("resourceactions") ?>
<? if ($k=="") { ?>
<a href="collections.php?add=<?=$ref?>&nc=<?=time()?>&search=<?=urlencode($search)?>" target="collections">&gt; <?=$lang["addtocollection"]?></a>&nbsp;&nbsp;&nbsp;
<? if (checkperm("g") || checkperm("v")) { ?><a href="resource_email.php?ref=<?=$ref?>" target="main">&gt; <?=$lang["emailresource"]?></a>&nbsp;&nbsp;<? } ?>
<? if (checkperm("e" . $resource["archive"])) { ?><a href="edit.php?ref=<?=$ref?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>">&gt; <?=$lang["edit"]?></a>&nbsp;&nbsp;<a href="delete.php?ref=<?=$ref?>">&gt; <?=$lang["delete"]?></a><? } ?>
&nbsp;&nbsp;<a href="log.php?ref=<?=$ref?>">&gt; <?=$lang["log"]?></a>
<? } ?>
<? } /* End of renderinnerresourcedownloadspace hook */ ?>
</div>
<? } /* End of renderinnerresourceview hook */ ?>
</div>

<? hook("renderbeforeresourcedetails"); ?>

<div class="Title"><?=$lang["resourcedetails"]?></div>

<?
$extra="";
$tabname="";
$tabcount=0;
$fields=get_resource_field_data($ref);
if (count($fields)>0 && $fields[0]["tab_name"]!="")
	{ ?>
	<div class="TabBar">
	<?
	# Draw tabs.
	$extra="";
	$tabname="";
	$tabcount=0;
	for ($n=0;$n<count($fields);$n++)
		{
		$value=$fields[$n]["value"];

		# draw new tab?
		if (($tabname!=$fields[$n]["tab_name"]) && ($value!="") && ($value!=",") && ($fields[$n]["display_field"]==1))
			{
			?><div id="tabswitch<?=$tabcount?>" class="Tab<? if ($tabcount==0) { ?> TabSelected<? } ?>"><a href="#" onclick="SelectTab(<?=$tabcount?>);return false;"><?=$fields[$n]["tab_name"]?></a></div><?
			$tabcount++;
			$tabname=$fields[$n]["tab_name"];
			}
		}
	?>
	</div>
	<script language="Javascript">
	function SelectTab(tab)
		{
		// Deselect all tabs
		<? for ($n=0;$n<$tabcount;$n++) { ?>
		document.getElementById("tab<?=$n?>").style.display="none";
		document.getElementById("tabswitch<?=$n?>").className="Tab";
		<? } ?>
		document.getElementById("tab" + tab).style.display="block";
		document.getElementById("tabswitch" + tab).className="Tab TabSelected";
		}
	</script>
	<?
	}
?>

<div id="tab0" class="TabbedPanel<? if ($tabcount>0) { ?> StyledTabbedPanel<? } ?>">
<div class="clearerleft"> </div>
<? 
# Draw standard fields
?>
<div class="itemNarrow"><h3><?=$lang["resourceid"]?></h3><p><?=$ref?></p></div>
<div class="itemNarrow"><h3><?=$lang["access"]?></h3><p><?=@$lang["access" . $resource["access"]]?></p></div>
<?
# contributed by field
$udata=get_user($resource["created_by"]);
if ($udata!==false)
	{
	?>
	<div class="itemNarrow"><h3><?=$lang["contributedby"]?></h3><p><? if (checkperm("u")) { ?><a href="team_user_edit.php?ref=<?=$udata["ref"]?>"><? } ?><?=$udata["fullname"]?><? if (checkperm("u")) { ?></a><? } ?></p></div>
	<?
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
	if ($fields[$n]["type"]==6 && $value!="" && $value<=date("Y-m-d"))
		{
		$extra.="<div class=\"RecordStory\"> <h1>" . $lang["warningexpired"] . "</h1><p>" . $lang["warningexpiredtext"] . "</p><p id=\"WarningOK\"><a href=\"#\" onClick=\"document.getElementById('RecordDownload').style.display='block';document.getElementById('WarningOK').style.display='none';\">" . $lang["warningexpiredok"] . "</a></p></div><style>#RecordDownload {display:none;}</style>";
		}
	
	
	if (($value!="") && ($value!=",") && ($fields[$n]["display_field"]==1))
		{
		$title=htmlspecialchars(str_replace("Keywords - ","",i18n_get_translated($fields[$n]["title"])));
		if ($fields[$n]["type"]==4 || $fields[$n]["type"]==6) {$value=NiceDate($value,false,true);}

		# Value formatting
		$value=i18n_get_translated($value);
		if (($fields[$n]["type"]==2) || ($fields[$n]["type"]==30)) {$value=TidyList($value);}
		$value=highlightkeywords(nl2br(htmlspecialchars($value)),$search);
		
		# draw new tab panel?
		if (($tabname!=$fields[$n]["tab_name"]) && ($fieldcount>0))
			{
			$tabcount++;
			# Also display the custom formatted data $extra at the bottom of this tab panel.
			?><div class="clearerleft"> </div><?=$extra?></div><div class="TabbedPanel StyledTabbedPanel" style="display:none;" id="tab<?=$tabcount?>"><?	
			$extra="";
			}
		$tabname=$fields[$n]["tab_name"];
		$fieldcount++;		

		if (trim($fields[$n]["display_template"])!="")
			{
			# Process the value using a plugin
			$plugin="plugins/value_filter_" . $fields[$n]["name"] . ".php";
			if (file_exists($plugin)) {include $plugin;}
			
			# Use a display template to render this field
			$template=$fields[$n]["display_template"];
			$template=str_replace("[title]",$title,$template);
			$template=str_replace("[value]",$value,$template);
			$template=str_replace("[ref]",$ref,$template);
			$extra.=$template;
			}
		else
			{
			# Draw this field normally.
			?>
			<div class="itemNarrow"><h3><?=$title?></h3><p><?=$value?></p></div>
			<?
			}
		}
	}
?>
<div class="clearerleft"> </div>

<?=$extra?>

</div><!-- end of tabbed panel-->

</div>
</div>


<div class="PanelShadow"></div>
</div>


<?
# -------- Related Resources (must be able to search for this to work)
if (checkperm("s")) {
$result=do_search("!related" . $ref);
if (count($result)>0) 
	{
	?><!--Panel for related resources-->
<div class="RecordBox">
<div class="RecordPanel">  

<div class="RecordResouce">
<div class="Title"><?=$lang["relatedresources"]?></div>
<?
	# loop and display the results
	for ($n=0;$n<count($result);$n++)			
		{
		$rref=$result[$n]["ref"];
		?>
		<!--Resource Panel-->
		<div class="CollectionPanelShell">
			<table border="0" class="CollectionResourceAlign"><tr><td>
			<a target="main" href="view.php?ref=<?=$rref?>&search=<?=urlencode("!related" . $ref)?>"><? if ($result[$n]["has_image"]==1) { ?><img border=0 src="<?=get_resource_path($rref,"col",false,$result[$n]["preview_extension"])?>" class="CollectImageBorder"/><? } else { ?><img border=0 width=56 height=75 src="gfx/type<?=$result[$n]["resource_type"]?>_col.gif"/><? } ?></a></td>
			</tr></table>
			<div class="CollectionPanelInfo"><a target="main" href="view.php?ref=<?=$rref?>"><?=tidy_trim($result[$n]["title"],25)?></a>&nbsp;</div>
		</div>
		<?		
		}
	?>
	<div class="clearerleft"> </div>
		<a href="search.php?search=<?=urlencode("!related" . $ref) ?>"><?=$lang["clicktoviewasresultset"]?></a>

	</div>
	</div>
	<div class="PanelShadow"></div>
	</div><?
	}


if ($show_related_themes==true){
# -------- Public Collections / Themes
$result=get_themes_by_resource($ref);
if (count($result)>0) 
	{
	?><!--Panel for related themes / collections -->
	<div class="RecordBox">
	<div class="RecordPanel">  
	
	<div class="RecordResouce BasicsBox">
	<div class="Title"><?=$lang["collectionsthemes"]?></div>
	<div class="VerticalNav">
	<ul>
	<?
		# loop and display the results
		for ($n=0;$n<count($result);$n++)			
			{
			?>
			<li><a href="search.php?search=!collection<?=$result[$n]["ref"]?>"><?=(strlen($result[$n]["theme"])>0)?htmlspecialchars($result[$n]["theme"] . " / "):$lang["public"] . " : " . htmlspecialchars($result[$n]["fullname"] . " / ")?><?=htmlspecialchars($result[$n]["name"])?></a></li>
			<?		
			}
		?>
	</ul>
	</div>
	</div>
	</div>
	<div class="PanelShadow"></div>
	</div><?
	}} 
?>




<!--Panel for search for similar resources-->
<div class="RecordBox">
<div class="RecordPanel"> 


<div class="RecordResouce">
<div class="Title"><?=$lang["searchforsimilarresources"]?></div>
<? if ($resource["has_image"]==1) { ?>

<!--
<p>Find resources with a <a href="search.php?search=<?=urlencode("!rgb:" . $resource["image_red"] . "," . $resource["image_green"] . "," . $resource["image_blue"])?>">similar colour theme</a>.</p>
<p>Find resources with a <a href="search.php?search=<?=urlencode("!colourkey" . $resource["colour_key"]) ?>">similar colour theme (2)</a>.</p>
-->

<? } ?>
<script language="Javascript">
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
<input type="hidden" name="resource_type" value="<?=$resource["resource_type"]?>">
<input type="hidden" name="countonly" id="countonly" value="">
<?
$keywords=get_resource_top_keywords($ref,30);
$searchwords=split_keywords($search);
for ($n=0;$n<count($keywords);$n++)
	{
	?>
	<div class="SearchSimilar"><input type=checkbox name="keyword_<?=urlencode($keywords[$n])?>" value="yes"
	<? if (in_array($keywords[$n],$searchwords)) {?>checked<?}?> onClick="UpdateResultCount();">&nbsp;<?=$keywords[$n]?></div>
	<?
	}
?>
<div class="clearerleft"> </div>
<br />
<input name="search" type="submit" value="&nbsp;&nbsp;<?=$lang["search"]?>&nbsp;&nbsp;" id="dosearch"/>
<iframe frameborder=0 scrolling=no width=1 height=1 style="visibility:hidden;" name="resultcount" id="resultcount"></iframe>
</form>
<div class="clearerleft"> </div>
</div>
</div>
<div class="PanelShadow"></div>
</div>
<? } # end of block that requires search permissions?>

<?
include "include/footer.php";
?>
