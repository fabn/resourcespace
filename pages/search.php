<?php
include "../include/db.php";
include "../include/general.php";
include "../include/resource_functions.php"; //for checking scr access
include "../include/search_functions.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(str_replace("!collection","",getvalescaped("search","")),$k))) {include "../include/authenticate.php";}

 # Disable info box for external users.
if ($k!="") {$infobox=false;}
else {
       #note current user collection for add/remove links
       $user=get_user($userref);$usercollection=$user['current_collection'];
}

$search=getvalescaped("search","");

# Append extra search parameters from the quick search.
if (!is_numeric($search)) # Don't do this when the search query is numeric, as users typically expect numeric searches to return the resource with that ID and ignore country/date filters.
	{
	// For the simple search fields, collect from the GET request and assemble into the search string.
	reset ($_GET);
	foreach ($_GET as $key=>$value)
		{
		$value=trim($value);
		if ($value!="" && substr($key,0,6)=="field_")
			{
			if (strpos($key,"_year")!==false)
				{
				# Date field
				
				# Construct the date from the supplied dropdown values
				$key_month=str_replace("_year","_month",$key);
				if (getval($key_month,"")!="") {$value.="-" . getval($key_month,"");}

				$key_day=str_replace("_year","_day",$key);
				if (getval($key_day,"")!="") {$value.="-" . getval($key_day,"");}
				
				$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . str_replace("_year","",substr($key,6)) . ":" . $value;
				}
			elseif (strpos($key,"_month")===false && strpos($key,"_day")===false)
				{
				# Standard field
				$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . substr($key,6) . ":" . $value;
				}
			}
		}
	
	$year=getvalescaped("year","");
	if ($year!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "year:" . $year;}
	$month=getvalescaped("month","");
	if ($month!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "month:" . $month;}
	$day=getvalescaped("day","");
	if ($day!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "day:" . $day;}
	}
	
hook("searchstringprocessing");


# Fetch and set the values
if (strpos($search,"!")===false) {setcookie("search",$search);} # store the search in a cookie if not a special search
$offset=getvalescaped("offset",0);if (strpos($search,"!")===false) {setcookie("saved_offset",$offset);}
if ((!is_numeric($offset)) || ($offset<0)) {$offset=0;}
$order_by=getvalescaped("order_by",$default_sort);if (strpos($search,"!")===false) {setcookie("saved_order_by",$order_by);}
$display=getvalescaped("display","thumbs");setcookie("display",$display);
$per_page=getvalescaped("per_page",$default_perpage);setcookie("per_page",$per_page);
$archive=getvalescaped("archive",0);if (strpos($search,"!")===false) {setcookie("saved_archive",$archive);}
$jumpcount=0;


## If displaying a collection
# Enable/disable the reordering feature. Just for collections for now.
$allow_reorder=false;
# display collection title if option set.
$collection_title = "";

if (substr($search,0,11)=="!collection")
	{
	$collection=substr($search,11);
	$collectiondata=get_collection($collection);
	if ($collection_reorder_caption)
		{
	# Check to see if this user can edit (and therefore reorder) this resource
		if (($userref==$collectiondata["user"]) || ($collectiondata["allow_changes"]==1) || (checkperm("h")))
			{
			$allow_reorder=true;
			}
		}

	if ($display_collection_title)
		{
		$collection_title = '<br /></div><div align="left"><h1>'.$collectiondata ["name"].'</h1>';
		}
	}

# get current collection resources to pre-fill checkboxes
if ($use_checkboxes_for_selection){
$collectionresources=get_collection_resources($usercollection);
}

# fetch resource types from query string and generate a resource types cookie
if (getval("resetrestypes","")=="")
	{
	$restypes=getvalescaped("restypes","");
	}
else
	{
	$restypes="";
	reset($_GET);foreach ($_GET as $key=>$value)
		{
		if (substr($key,0,8)=="resource") {if ($restypes!="") {$restypes.=",";} $restypes.=substr($key,8);}
		}
	setcookie("restypes",$restypes);
	
	# This is a new search, log this activity
	if ($archive==2) {daily_stat("Archive search",0);} else {daily_stat("Search",0);}
	}
	
# If returning to an old search, restore the page/order by
if (!array_key_exists("search",$_GET))
	{
	$offset=getvalescaped("saved_offset",0);setcookie("saved_offset",$offset);
	$order_by=getvalescaped("saved_order_by","relevance");setcookie("saved_order_by",$order_by);
	$archive=getvalescaped("saved_archive",0);setcookie("saved_archive",$archive);
	}
	
# If requested, refresh the collection frame (for redirects from saves)
if (getval("refreshcollectionframe","")!="")
	{
	refresh_collection_frame();
	}

# Include javascript for infobox panels.
$headerinsert.="
<script src=\"../lib/js/infobox.js\" type=\"text/javascript\"></script>
";

if ($infobox)
	$bodyattribs="OnMouseMove='InfoBoxMM(event);'";

# Include function for reordering
if ($allow_reorder)
	{
	$url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&archive=" . $archive . "&offset=" . $offset;
	?>
	<script type="text/javascript">
	function ReorderResources(id1,id2)
		{
		document.location='<?php echo $url?>&reorder=' + id1 + '-' + id2;
		}
	</script>
	<?php
	
	# Also check for the parameter and reorder as necessary.
	$reorder=getvalescaped("reorder","");
	if ($reorder!="")
		{
		$r=explode("-",$reorder);
		swap_collection_order(substr($r[0],13),$r[1],substr($search,11));
		refresh_collection_frame();
		}
	}

# Initialise the results references array (used later for search suggestions)
$refs=array();

# Special query? Ignore restypes
if (strpos($search,"!")!==false) {$restypes="";}

# Do the search!
$result=do_search($search,$restypes,$order_by,$archive,$per_page+$offset);

# Special case: numeric searches (resource ID) and one result: redirect immediately to the resource view.
if (is_numeric($search) && is_array($result) && count($result)==1)
	{
	redirect("pages/view.php?ref=" . $result[0]["ref"] . "&search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&offset=" . urlencode($offset) . "&archive=" . $archive . "&k=" . $k);
	}
	

# Include the page header to and render the search results
include "../include/header.php";

if (is_array($result))
	{
	$url="search.php?search=" . urlencode($search) . "&order_by=" . $order_by . "&offset=" . $offset . "&archive=" . $archive;
	?>
	<div class="TopInpageNav">
	<div class="InpageNavLeftBlock"><?php echo $lang["youfound"]?>:<br /><span class="Selected"><?php echo number_format(count($result))?><?php echo (count($result)==$max_results)?"+":""?></span> <?php echo $lang["youfoundresources"]?></div>
	<div class="InpageNavLeftBlock"><?php echo $lang["display"]?>:<br />
	<?php if ($display=="thumbs") { ?><span class="Selected"><?php echo $lang["largethumbs"]?></span><?php } else { ?><a href="<?php echo $url?>&display=thumbs&k=<?php echo $k?>"><?php echo $lang["largethumbs"]?></a><?php } ?>&nbsp;|&nbsp; 
		<?php if ($smallthumbs==true) { ?>		
	<?php if ($display=="smallthumbs") { ?><span class="Selected"><?php echo $lang["smallthumbs"]?></span><?php } else { ?><a href="<?php echo $url?>&display=smallthumbs&k=<?php echo $k?>"><?php echo $lang["smallthumbs"]?></a><?php } ?>&nbsp; |&nbsp;<?php } ?>
	<?php if ($display=="list") { ?><span class="Selected"><?php echo $lang["list"]?></span><?php } else { ?><a href="<?php echo $url?>&display=list&k=<?php echo $k?>"><?php echo $lang["list"]?></a><?php } ?> <?php hook("adddisplaymode"); ?> </div>
	<?php
	
	# order by
	#if (strpos($search,"!")===false)
	if (true) # Ordering enabled for collections/themes too now at the request of N Ward / Oxfam
		{
		$rel=$lang["relevance"];
		if (strpos($search,"!")!==false) {$rel=$lang["asadded"];}
		?>
		<div class="InpageNavLeftBlock "><?php echo $lang["sortorder"]?>:<br /><?php if ($order_by=="relevance") {?><span class="Selected"><?php echo $rel?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=relevance&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $rel?></a><?php } ?>
		
		<?php if ($title_sort) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="title") {?><span class="Selected"><?php echo $lang["resourcetitle"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=title&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["resourcetitle"]?></a><?php } ?>
		<?php } ?>
		
		<?php if ($original_filename_sort) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="file_path") {?><span class="Selected"><?php echo $lang["filename"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=file_path&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["filename"]?></a><?php } ?>
		<?php } ?>
		
		&nbsp;|&nbsp;
		<?php if ($order_by=="popularity") {?><span class="Selected"><?php echo $lang["popularity"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=popularity&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["popularity"]?></a><?php } ?>
		
		<?php if ($orderbyrating) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="rating") {?><span class="Selected"><?php echo $lang["rating"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=rating&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["rating"]?></a><?php } ?>
		<?php } ?>
		
		&nbsp;|&nbsp;
		<?php if ($order_by=="date") {?><span class="Selected"><?php echo $lang["date"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=date&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["date"]?></a><?php } ?>
		
		<?php if ($colour_sort) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="colour") {?><span class="Selected"><?php echo $lang["colour"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=colour&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["colour"]?></a><?php } ?>
		<?php } ?>
		
		<?php if ($country_sort) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="country") {?><span class="Selected"><?php echo $lang["country"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=country&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["country"]?></a><?php } ?>
		<?php } ?>
		
		<?php if ($order_by_resource_id) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="resourceid") {?><span class="Selected"><?php echo $lang["resourceid"]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=resourceid&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["resourceid"]?></a><?php } ?>
		<?php } ?>
		
		</div>
		<div class="InpageNavLeftBlock"><?php echo $lang["resultsdisplay"]?>:<br />
		<?php 
		for($n=0;$n<count($results_display_array);$n++){?>
		<?php if ($per_page==$results_display_array[$n]){?><span class="Selected"><?php echo $results_display_array[$n]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>&per_page=<?php echo $results_display_array[$n]?>"><?php echo $results_display_array[$n]?></a><?php } ?><?php if ($n>-1&&$n<count($results_display_array)-1){?>&nbsp;|<?php } ?>
		<?php } ?>
		</div>
		
		<?php
		}
		
	$results=count($result);
	$totalpages=ceil($results/$per_page);
	if ($offset>$results) {$offset=0;}
	$curpage=floor($offset/$per_page)+1;
	$url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&archive=" . $archive . "&k=" . $k;	

	pager();
	$draw_pager=true;
	?><?php echo $collection_title ?>
	</div>
	
	<?php		
	hook("beforesearchresults");
	
	if ($display=="list")
		{
		?>
		<!--list-->
		<div class="Listview">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">

		<!--Title row-->
		<?php if(!hook("replacelistviewtitlerow")){?>	
		<tr class="ListviewTitleStyle">
		<?php if ($use_checkboxes_for_selection){?><td></td><?php } ?>
		<td><?php echo $lang["titleandcountry"]?></td>
		<td>&nbsp;</td>
		<td><?php echo $lang["id"]?></td>
		<td><?php echo $lang["type"]?></td>
		<td><?php echo $lang["date"]?> </td>
		<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
		</tr>
		<?php } ?> <!--end hook replace listviewtitlerow-->
		<?php
		}
		
	# Include public collections and themes in the main search, if configured.		
	if (($search_includes_themes || $search_includes_public_collections) && $search!="" && substr($search,0,1)!="!" && $offset==0)
		{
		include "../include/search_public.php";
		}
	
	# work out common keywords among the results
	if ((count($result)>$suggest_threshold) && (strpos($search,"!")===false) && ($suggest_threshold!=-1))
		{
		for ($n=0;$n<count($result);$n++)
			{
			if ($result[$n]["ref"]) {$refs[]=$result[$n]["ref"];} # add this to a list of results, for query refining later
			}
		$suggest=suggest_refinement($refs,$search);
		if (count($suggest)>0)
			{
			?><p><?php echo $lang["torefineyourresults"]?>: <?php
			for ($n=0;$n<count($suggest);$n++)
				{
				if ($n>0) {echo ", ";}
				?><a  href="search.php?search=<?php echo  urlencode(strip_tags($suggest[$n])) ?>"><?php echo stripslashes($suggest[$n])?></a><?php
				}
			?></p><?php
			}
		}
		
	$rtypes=array();
	$types=get_resource_types();
	for ($n=0;$n<count($types);$n++) {$rtypes[$types[$n]["ref"]]=$types[$n]["name"];}

	# loop and display the results
	for ($n=$offset;(($n<count($result)) && ($n<($offset+$per_page)));$n++)			
		{
		$ref=$result[$n]["ref"];
		$GLOBALS['get_resource_data_cache'][$ref] = $result[$n];
		$url="view.php?ref=" . $ref . "&search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&offset=" . urlencode($offset) . "&archive=" . $archive . "&k=" . $k; ?>
		
			<?php	
			if ($display=="thumbs") { #Thumbnails view
			?>
		 
<?php if (!hook("renderresultthumb")) { ?>

<!--Resource Panel-->
	<div class="ResourcePanelShell" id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanel">
	
<?php if (!hook("renderimagethumb")) { ?>			
	
	<table border="0" class="ResourceAlign<?php if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideo<?php } ?>"><tr><td>
	<a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["title"])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img width="<?php echo $result[$n]["thumb_width"]?>" height="<?php echo $result[$n]["thumb_height"]?>" src="<?php echo get_resource_path($ref,false,"thm",false,$result[$n]["preview_extension"],-1,1,false,$result[$n]["file_modified"])?>" class="ImageBorder"
	<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
	/><?php } else { ?><img border=0 src="../gfx/type<?php echo $result[$n]["resource_type"]?>.gif" 
	<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
	/><?php } ?></a>
		</td>
		</tr></table>
<?php } ?> <!-- END HOOK Renderimagethumb-->	
		
<?php if (!hook("rendertitlethumb")) { ?>			

		<div class="ResourcePanelInfo"><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["title"])))?>"<?php } ?>><?php echo highlightkeywords(htmlspecialchars(tidy_trim(i18n_get_translated($result[$n]["title"]),32)),$search)?><?php if ($show_extension_in_search) { ?><?php echo " [" . strtoupper($result[$n]["file_extension"] . "]")?><?php } ?></a>&nbsp;</div>

<?php } ?> <!-- END HOOK Rendertitlethumb -->			
		
		<div class="ResourcePanelCountry"><?php if (!$allow_reorder) { # Do not display the country if reordering (to create more room) ?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($result[$n]["country"])),10),$search)?><?php } ?>&nbsp;</div>	
				
		<?php if( resource_download_allowed($ref,"scr")){?><span class="IconPreview"><a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span><?php } ?>
		
		<?php if (!checkperm("b") && $k=="") { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12"/></a></span>
		<?php } ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="") { ?>
		<?php if ($search=="!collection".$usercollection){?><span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
		
		<?php if ($allow_share && $k=="") { ?><span class="IconEmail"><a href="resource_email.php?ref=<?php echo $ref?>" title="<?php echo $lang["emailresource"]?>"><img src="../gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span><?php } ?>
		<?php if ($result[$n]["rating"]>0) { ?><div class="IconStar"></div><?php } ?>
		<?php if ($collection_reorder_caption && $allow_reorder) { ?>
		<span class="IconComment"><a href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo substr($search,11)?>" title="<?php echo $lang["addorviewcomments"]?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>			
		<div class="IconReorder" onMouseDown="InfoBoxWaiting=false;"> </div>
		<?php } ?>
		<div class="clearer"></div>
		<?php if ($use_checkboxes_for_selection){?><input type="checkbox" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onClick="if ($('check<?php echo $ref?>').checked){ <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if ($('check<?php echo $ref?>').checked==false){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"><?php } ?>
	</div>
<div class="PanelShadow"></div>
</div>
<?php if ($allow_reorder) { 
# Javascript drag/drop enabling.
?>
<script type="text/javascript">
new Draggable('ResourceShell<?php echo $ref?>',{handle: 'IconReorder', revert: true});
Droppables.add('ResourceShell<?php echo $ref?>',{accept: 'ResourcePanelShell', onDrop: function(element) {ReorderResources(element.id,<?php echo $ref?>);}, hoverclass: 'ReorderHover'});
</script>
<?php } ?> 
<?php } ?>

		<?php 
		} elseif ($display == "smallthumbs") { #Small Thumbs view
		?>

<div class="ResourcePanelShellSmall" id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanelSmall">	
		<table border="0" class="ResourceAlignSmall"><tr><td>
		<a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["title"])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img  src="<?php echo get_resource_path($ref,false,"col",false,$result[$n]["preview_extension"],-1,1,false,$result[$n]["file_modified"])?>" class="ImageBorder"
		<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
		/><?php } else { ?><img border=0 src="../gfx/type<?php echo $result[$n]["resource_type"]?>_col.gif"
		<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
		/><?php } ?></a>
		</td>
		</tr></table>
		<div class="ResourcePanelCountry">
		<?php if( resource_download_allowed($ref,"scr")){?>
		<span class="IconPreview">
		<a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span><?php } ?>
		
		<?php if (!checkperm("b") && $k=="") { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="") { ?>
		<?php if ($search=="!collection".$usercollection){?>
		<span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
	
		</div>
<div class="clearer"></div></div>	
<div class="PanelShadow"></div></div>
		 
		<?php
		} else if ($display=="list") { # List view
		?>
		<?php if (!hook("replacelistitem")) {?>
		<!--List Item-->
		<tr>
		<?php if ($use_checkboxes_for_selection){?><td><input type="checkbox" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onClick="if ($('check<?php echo $ref?>').checked){ <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if ($('check<?php echo $ref?>').checked==false){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"></td><?php } ?>
		
		<td nowrap><div class="ListTitle"><a <?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?> href="<?php echo $url?>"><?php echo highlightkeywords(tidy_trim(i18n_get_translated($result[$n]["title"]),45) . 
		
		((strlen(trim($result[$n]["country"]))>1)?(", " . tidy_trim(TidyList(i18n_get_translated($result[$n]["country"])),25)):"") .
		($show_extension_in_search?" [" . strtoupper($result[$n]["file_extension"]) . "]":"")
		,$search) ?></a></div></td>
		<td><?php if ($result[$n]["rating"]>0) { ?><div class="IconStar"> </div><?php } else { ?>&nbsp;<?php } ?></td>
		<td><?php echo $result[$n]["ref"]?></td>
		<td><?php if (array_key_exists($result[$n]["resource_type"],$rtypes)) { ?><?php echo i18n_get_translated($rtypes[$result[$n]["resource_type"]])?><?php } ?></td>
		<td><?php echo nicedate($result[$n]["creation_date"],false,true)?></td>
		<td><div class="ListTools"><a <?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);"onMouseOut="InfoBoxSetResource(0);"<?php } ?> href="<?php echo $url?>">&gt;&nbsp;<?php echo $lang["action-view"]?></a> &nbsp;<?php

		if (!checkperm("b")&& $k=="") { ?>
		<?php echo add_to_collection_link($ref,$search)?>&gt;&nbsp;<?php echo $lang["action-addtocollection"]?></a> &nbsp;
		<?php } ?>

		<?php if ($allow_share && $k=="") { ?><a href="resource_email.php?ref=<?php echo $ref?>">&gt;&nbsp;<?php echo $lang["action-email"]?></a><?php } ?></div></td>
		
		
		</tr>
		<?php } ?><!--end hook replacelistitem--> 
		<?php
		}
	
	hook("customdisplaymode");
	
		}
		
	if ($display=="list")
		{
		?>
		</table>
		</div>
		<?php
		}
	
	if ($display!="list")
		{
		?>
		<!--Key to Panel-->
		<div class="BottomInpageKey"> 
			<?php echo $lang["key"]?>:
			<?php if ($display=="thumbs") { ?>
				
				<?php if ($orderbyrating) { ?><div class="KeyStar"><?php echo $lang["verybestresources"]?></div><?php } ?>
				<?php if ($allow_reorder) { ?><div class="KeyReorder"><?php echo $lang["reorderresources"]?></div><?php } ?>
				<div class="KeyComment"><?php echo $lang["addorviewcomments"]?></div>
				<?php if ($allow_share) { ?><div class="KeyEmail"><?php echo $lang["emailresource"]?></div><?php } ?>
			<?php } ?>
			
			<?php if (!checkperm("b")) { ?><div class="KeyCollect"><?php echo $lang["addtocurrentcollection"]?></div><?php } ?>
			<div class="KeyPreview"><?php echo $lang["fullscreenpreview"]?></div>
		</div>
		<?php
		}
	}
else
	{
	?>
	<div class="BasicsBox"> 
	  <div class="NoFind">
		<p><?php echo $lang["searchnomatches"]?></p>
		<?php if ($result!="")
		{
		?>
		<p><?php echo $lang["try"]?>: <a href="search.php?search=<?php echo urlencode(strip_tags($result))?>"><?php echo stripslashes($result)?></a></p>
		<?php
		}
		else
		{
		?>
		<p><?php if (strpos($search,"country:")!==false) { ?><p><?php echo $lang["tryselectingallcountries"]?> <?php } 
		elseif (strpos($search,"year:")!==false) { ?><p><?php echo $lang["tryselectinganyyear"]?> <?php } 
		elseif (strpos($search,"month:")!==false) { ?><p><?php echo $lang["tryselectinganymonth"]?> <?php } 
		else 		{?><?php echo $lang["trybeinglessspecific"]?><?php } ?> <?php echo $lang["enteringfewerkeywords"]?></p>
		<?php
		}
	  ?>
	  </div>
	</div>
	<?php
	}
?>
<!--Bottom Navigation - Archive, Saved Search plus Collection-->
<div class="BottomInpageNav">
<?php if (($archive==0) && (strpos($search,"!")===false) && $archive_search) { 
	$arcresults=do_search($search,$restypes,$order_by,2,0);
	if (is_array($arcresults)) {$arcresults=count($arcresults);} else {$arcresults=0;}
	if ($arcresults>0) 
		{
		?>
		<div class="InpageNavLeftBlock"><a href="search.php?search=<?php echo urlencode($search)?>&archive=2">&gt;&nbsp;<?php echo $lang["view"]?> <span class="Selected"><?php echo number_format($arcresults)?></span> <?php echo ($arcresults==1)?$lang["match"]:$lang["matches"]?> <?php echo $lang["inthearchive"]?></a></div>
		<?php 
		}
	else
		{
		?>
		<div class="InpageNavLeftBlock">&gt;&nbsp;<?php echo $lang["nomatchesinthearchive"]?></div>
		<?php 
		}
	} ?>
	<?php if (!checkperm("b") && $k=="") { ?>
	<?php if($allow_save_search) { ?><div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?php echo urlencode($search)?>&restypes=<?php echo urlencode($restypes)?>&archive=<?php echo $archive?>" target="collections">&gt;&nbsp;<?php echo $lang["savethissearchtocollection"]?></a></div><?php } ?>
	<div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?php echo urlencode($search)?>&restypes=<?php echo urlencode($restypes)?>&archive=<?php echo $archive?>&mode=resources" target="collections">&gt;&nbsp;<?php echo $lang["savesearchitemstocollection"]?></a></div>
	<?php } ?>
	
	<?php hook("resultsbottomtoolbar"); ?>
	
	<?php 
	$url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&archive=" . $archive . "&k=" . $k;	

	if (isset($draw_pager)) {pager(false);} ?>
</div>	
<?php	


# Add the infobox.
?>
<div id="InfoBox"><div id="InfoBoxInner"> </div></div>
<?php
include "../include/footer.php";
?>
