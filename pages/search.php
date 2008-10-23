<?
include "../include/db.php";
include "../include/general.php";
include "../include/search_functions.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(str_replace("!collection","",getvalescaped("search","")),$k))) {include "../include/authenticate.php";}


$search=getvalescaped("search","");

# Append extra search parameters from the quick search.
if (!is_numeric($search)) # Don't do this when the search query is numeric, as users typicall expect numeric searches to return the resource with that ID and ignore country/date filters.
	{
	$country=getvalescaped("country","");
	if ($country!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "country:" . $country;}
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
$order_by=getvalescaped("order_by","relevance");if (strpos($search,"!")===false) {setcookie("saved_order_by",$order_by);}
$display=getvalescaped("display","thumbs");setcookie("display",$display);
$per_page=getvalescaped("per_page",$default_perpage);setcookie("per_page",$per_page);
$archive=getvalescaped("archive",0);if (strpos($search,"!")===false) {setcookie("saved_archive",$archive);}
$jumpcount=0;

# Enable/disable the reordering feature. Just for collections for now.
$allow_reorder=false;
if (substr($search,0,11)=="!collection" && $collection_reorder_caption)
	{
	# Check to see if this user can edit (and therefore reorder) this resource
	$collection=substr($search,11);
	$collectiondata=get_collection($collection);
	if (($userref==$collectiondata["user"]) || ($collectiondata["allow_changes"]==1) || (checkperm("h")))
		{
		$allow_reorder=true;
		}
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
		document.location='<?=$url?>&reorder=' + id1 + '-' + id2;
		}
	</script>
	<?
	
	# Also check for the parameter and reorder as necessary.
	$reorder=getvalescaped("reorder","");
	if ($reorder!="")
		{
		$r=explode("-",$reorder);
		swap_collection_order(substr($r[0],13),$r[1],substr($search,11));
		refresh_collection_frame();
		}
	}


include "../include/header.php";


if (true) #search condition
	{
	$refs=array();
	#echo "search=$search";
	
	# Special query? Ignore restypes
	if (strpos($search,"!")!==false) {$restypes="";}
	
	# Story only? Display as list
	#if ($restypes=="2") {$display="list";}
	
	$result=do_search($search,$restypes,$order_by,$archive,$per_page+$offset);
	if (is_array($result))
		{

			
		$url="search.php?search=" . urlencode($search) . "&order_by=" . $order_by . "&offset=" . $offset . "&archive=" . $archive;
		?>
		<div class="TopInpageNav TopInpageNav">
		<div class="InpageNavLeftBlock"><?=$lang["youfound"]?>:<br /><span class="Selected"><?=number_format(count($result))?><?=(count($result)==$max_results)?"+":""?></span> <?=$lang["youfoundresources"]?></div>
		<div class="InpageNavLeftBlock"><?=$lang["display"]?>:<br />
		<? if ($display=="thumbs") { ?><span class="Selected"><?=$lang["largethumbs"]?></span><? } else { ?><a href="<?=$url?>&display=thumbs"><?=$lang["largethumbs"]?></a><? } ?>&nbsp;|&nbsp; 
			<? if ($smallthumbs==true) { ?>		
		<? if ($display=="smallthumbs") { ?><span class="Selected"><?=$lang["smallthumbs"]?></span><? } else { ?><a href="<?=$url?>&display=smallthumbs"><?=$lang["smallthumbs"]?></a><? } ?>&nbsp; |&nbsp;<? } ?>
		<? if ($display=="list") { ?><span class="Selected"><?=$lang["list"]?></span><? } else { ?><a href="<?=$url?>&display=list"><?=$lang["list"]?></a><? } ?> <? hook("adddisplaymode"); ?> </div>
		<?
		
		# order by
		#if (strpos($search,"!")===false)
		if (true) # Ordering enabled for collections/themes too now at the request of N Ward / Oxfam
			{
			$rel=$lang["relevance"];
			if (strpos($search,"!")!==false) {$rel=$lang["asadded"];}
			?>
			<div class="InpageNavLeftBlock "><?=$lang["sortorder"]?>:<br /><? if ($order_by=="relevance") {?><span class="Selected"><?=$rel?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=relevance&archive=<?=$archive?>&k=<?=$k?>"><?=$rel?></a><? } ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="popularity") {?><span class="Selected"><?=$lang["popularity"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=popularity&archive=<?=$archive?>&k=<?=$k?>"><?=$lang["popularity"]?></a><? } ?>
			
			<? if ($orderbyrating) { ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="rating") {?><span class="Selected"><?=$lang["rating"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=rating&archive=<?=$archive?>&k=<?=$k?>"><?=$lang["rating"]?></a><? } ?>
			<? } ?>
			
			&nbsp;|&nbsp;
			<? if ($order_by=="date") {?><span class="Selected"><?=$lang["date"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=date&archive=<?=$archive?>&k=<?=$k?>"><?=$lang["date"]?></a><? } ?>
			
			<? if ($colour_sort) { ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="colour") {?><span class="Selected"><?=$lang["colour"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=colour&archive=<?=$archive?>&k=<?=$k?>"><?=$lang["colour"]?></a><? } ?>
			<? } ?>
			
			<? if ($country_sort) { ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="country") {?><span class="Selected"><?=$lang["country"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=country&archive=<?=$archive?>&k=<?=$k?>"><?=$lang["country"]?></a><? } ?>
			<? } ?>
			</div>
			<?
			}
			
		$results=count($result);
	    $totalpages=ceil($results/$per_page);
	    if ($offset>$results) {$offset=0;}
    	$curpage=floor($offset/$per_page)+1;
        $url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&archive=" . $archive . "&k=" . $k;	

		pager();
		$draw_pager=true;
		?></div>
		
		<?		
		hook("beforesearchresults");
		
		if ($display=="list")
			{
			?>
			<!--list-->
			<div class="Listview">
			<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
	
			<!--Title row-->	
			<tr class="ListviewTitleStyle">
			<td><?=$lang["titleandcountry"]?></td>
			<td>&nbsp;</td>
			<td><?=$lang["id"]?></td>
			<td><?=$lang["type"]?></td>
			<td><?=$lang["date"]?> </td>
			<td><div class="ListTools"><?=$lang["tools"]?></div></td>
			</tr>
			<?
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
				?><p><?=$lang["torefineyourresults"]?>: <?
				for ($n=0;$n<count($suggest);$n++)
					{
					if ($n>0) {echo ", ";}
					?><a  href="search.php?search=<?= urlencode(strip_tags($suggest[$n])) ?>"><?=stripslashes($suggest[$n])?></a><?
					}
				?></p><?
				}
			}
		
		# Work out which resources we will be showing and pre-fetch data for those resources
		# No longer needed as we have 'resource_column' on fields that populates the main resource table for certain
		# columns
		/*
		$showrefs=array();
		for ($n=$offset;(($n<count($result)) && ($n<($offset+$page_size)));$n++)			
			{
			$showrefs[]=$result[$n]["ref"];
			}
		$resdata=get_resource_field_data_batch($showrefs);
		*/
		# Pre-fetch resource types for the list view
		
/*		
		if ($display=="list")
			{
*/			 
			$rtypes=array();
			$types=get_resource_types();
			for ($n=0;$n<count($types);$n++) {$rtypes[$types[$n]["ref"]]=$types[$n]["name"];}
/*			
			} 
*/		
		# loop and display the results
		for ($n=$offset;(($n<count($result)) && ($n<($offset+$per_page)));$n++)			
			{
			$ref=$result[$n]["ref"];
			$GLOBALS['get_resource_data_cache'][$ref] = $result[$n];
			$url="view.php?ref=" . $ref . "&search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&offset=" . urlencode($offset) . "&archive=" . $archive . "&k=" . $k; ?>
			
				<?	
				if ($display=="thumbs") { #Thumbnails view
				?>
			 
<? if (!hook("renderresultthumb")) { ?>

	<!--Resource Panel-->
		<div class="ResourcePanelShell" id="ResourceShell<?=$ref?>">
		<div class="ResourcePanel">
		
<? if (!hook("renderimagethumb")) { ?>			
		
		<table border="0" class="ResourceAlign<? if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideo<? } ?>"><tr><td>
		<a href="<?=$url?>" <? if (!$infobox) { ?>title="<?=str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["title"])))?>"<? } ?>><? if ($result[$n]["has_image"]==1) { ?><img width="<?=$result[$n]["thumb_width"]?>" height="<?=$result[$n]["thumb_height"]?>" src="<?=get_resource_path($ref,false,"thm",false,$result[$n]["preview_extension"],-1,1,checkperm("w"))?>" class="ImageBorder"
		<? if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?=$ref?>);" onMouseOut="InfoBoxSetResource(0);"<? } ?>
		/><? } else { ?><img border=0 src="../gfx/type<?=$result[$n]["resource_type"]?>.gif" 
		<? if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?=$ref?>);" onMouseOut="InfoBoxSetResource(0);"<? } ?>
		/><? } ?></a>
			</td>
			</tr></table>
<? } ?> <!-- END HOOK Renderimagethumb-->	
			
<? if (!hook("rendertitlethumb")) { ?>			

			<div class="ResourcePanelInfo"><a href="<?=$url?>" <? if (!$infobox) { ?>title="<?=str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["title"])))?>"<? } ?>><?=highlightkeywords(htmlspecialchars(tidy_trim(i18n_get_translated($result[$n]["title"]),32)),$search)?><? if ($show_extension_in_search) { ?><?=" [" . strtoupper($result[$n]["file_extension"] . "]")?><? } ?></a>&nbsp;</div>

<? } ?> <!-- END HOOK Rendertitlethumb -->			
			
			<div class="ResourcePanelCountry"><? if (!$allow_reorder) { # Do not display the country if reordering (to create more room) ?><?=highlightkeywords(tidy_trim(TidyList(i18n_get_translated($result[$n]["country"])),10),$search)?><? } ?>&nbsp;</div>				
			<span class="IconPreview"><a href="preview.php?from=search&ref=<?=$ref?>&ext=<?=$result[$n]["preview_extension"]?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>" title="<?=$lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?=$lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
			<? if (!checkperm("b")) { ?><span class="IconCollect"><?=add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span><? } ?>
			<? if (!checkperm("b") && substr($search,0,11)=="!collection") { ?>
			<span class="IconCollectOut"><?=remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
			<? } ?>
			<? if ($allow_share) { ?><span class="IconEmail"><a href="resource_email.php?ref=<?=$ref?>" title="<?=$lang["emailresource"]?>"><img src="../gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span><? } ?>
			<? if ($result[$n]["rating"]>0) { ?><div class="IconStar"></div><? } ?>
			<? if ($collection_reorder_caption && $allow_reorder) { ?>
			<span class="IconComment"><a href="collection_comment.php?ref=<?=$ref?>&collection=<?=substr($search,11)?>" title="<?=$lang["addorviewcomments"]?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>			
			<div class="IconReorder" onMouseDown="InfoBoxWaiting=false;"> </div>
			<? } ?>
			<div class="clearer"></div>
		</div>
	<div class="PanelShadow"></div>
	</div>
	<? if ($allow_reorder) { 
	# Javascript drag/drop enabling.
	?>
	<script type="text/javascript">
	new Draggable('ResourceShell<?=$ref?>',{handle: 'IconReorder', revert: true});
	Droppables.add('ResourceShell<?=$ref?>',{accept: 'ResourcePanelShell', onDrop: function(element) {ReorderResources(element.id,<?=$ref?>);}, hoverclass: 'ReorderHover'});
	</script>
	<? } ?> 
<? } ?>

			<? 
			} elseif ($display == "smallthumbs") { #Small Thumbs view
			?>

<div class="ResourcePanelShellSmall" id="ResourceShell<?=$ref?>">
		<div class="ResourcePanelSmall">	
			<table border="0" class="ResourceAlignSmall"><tr><td>
			<a href="<?=$url?>" <? if (!$infobox) { ?>title="<?=str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["title"])))?>"<? } ?>><? if ($result[$n]["has_image"]==1) { ?><img  src="<?=get_resource_path($ref,false,"col",false,$result[$n]["preview_extension"],-1,1,checkperm("w"))?>" class="ImageBorder"
			<? if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?=$ref?>);" onMouseOut="InfoBoxSetResource(0);"<? } ?>
			/><? } else { ?><img border=0 src="../gfx/type<?=$result[$n]["resource_type"]?>_col.gif"
			<? if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?=$ref?>);" onMouseOut="InfoBoxSetResource(0);"<? } ?>
			/><? } ?></a>
			</td>
			</tr></table>
			<div class="ResourcePanelCountry"><span class="IconPreview"><a href="preview.php?from=search&ref=<?=$ref?>&ext=<?=$result[$n]["preview_extension"]?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>" title="<?=$lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?=$lang["fullscreenpreview"]?>" width="22" height="12" /></a></span><? if (!checkperm("b")) { ?><span class="IconCollect"><?=add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span><span class="IconCollectOut"><?=remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span><? } ?></div>
<div class="clearer"></div></div>	
<div class="PanelShadow"></div></div>
			 
			<?
			} else if ($display=="list") { # List view
			?>
			<!--List Item-->
			<tr>
			<td nowrap><div class="ListTitle"><a <? if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?=$ref?>);" onMouseOut="InfoBoxSetResource(0);"<? } ?> href="<?=$url?>"><?=highlightkeywords(tidy_trim(i18n_get_translated($result[$n]["title"]),45) . 
			
			((strlen(trim($result[$n]["country"]))>1)?(", " . tidy_trim(TidyList(i18n_get_translated($result[$n]["country"])),25)):"") .
			($show_extension_in_search?" [" . strtoupper($result[$n]["file_extension"]) . "]":"")
			,$search) ?></a></div></td>
			<td><? if ($result[$n]["rating"]>0) { ?><div class="IconStar"> </div><? } else { ?>&nbsp;<? } ?></td>
			<td><?=$result[$n]["ref"]?></td>
			<td><? if (array_key_exists($result[$n]["resource_type"],$rtypes)) { ?><?=i18n_get_translated($rtypes[$result[$n]["resource_type"]])?><? } ?></td>
			<td><?=nicedate($result[$n]["creation_date"],false,true)?></td>
			<td><div class="ListTools"><a <? if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?=$ref?>);" onMouseOut="InfoBoxSetResource(0);"<? } ?> href="<?=$url?>">&gt;&nbsp;<?=$lang["action-view"]?></a> &nbsp;<? if (!checkperm("b")) { ?><?=add_to_collection_link($ref,$search)?>&gt;&nbsp;<?=$lang["action-addtocollection"]?></a> &nbsp;<? } ?><a href="resource_email.php?ref=<?=$ref?>">&gt;&nbsp;<?=$lang["action-email"]?></a></div></td>
			</tr>
			<?
			}
		
		hook("customdisplaymode");
		
			}
			
		if ($display=="list")
			{
			?>
	    	</table>
			</div>
			<?
			}
		
		if ($display!="list")
			{
			?>
			<!--Key to Panel-->
			<div class="BottomInpageKey"> 
				<?=$lang["key"]?>:
				
				<? if ($orderbyrating) { ?>
				<div class="KeyStar"><?=$lang["verybestresources"]?></div>
				<? } ?>
				
				<? if ($allow_reorder) { ?>
				<div class="KeyReorder"><?=$lang["reorderresources"]?></div>
				<div class="KeyComment"><?=$lang["addorviewcomments"]?></div>
				<? } ?>
				
				<? if ($allow_share) { ?><div class="KeyEmail"><?=$lang["emailresource"]?></div><? } ?>
				<? if (!checkperm("b")) { ?><div class="KeyCollect"><?=$lang["addtocurrentcollection"]?></div><? } ?>
				<div class="KeyPreview"><?=$lang["fullscreenpreview"]?></div>
			</div>
			<?
			}
		}
	else
		{
		?>
		<div class="BasicsBox"> 
		  <div class="NoFind">
		    <p><?=$lang["searchnomatches"]?></p>
    		<? if ($result!="")
			{
			?>
		    <p><?=$lang["try"]?>: <a href="search.php?search=<?=urlencode(strip_tags($result))?>"><?=stripslashes($result)?></a></p>
   			<?
			}
			else
			{
			?>
			<p><? if (strpos($search,"country:")!==false) { ?><p><?=$lang["tryselectingallcountries"]?> <? } 
			elseif (strpos($search,"year:")!==false) { ?><p><?=$lang["tryselectinganyyear"]?> <? } 
			elseif (strpos($search,"month:")!==false) { ?><p><?=$lang["tryselectinganymonth"]?> <? } 
			else 		{?><?=$lang["trybeinglessspecific"]?><? } ?> <?=$lang["enteringfewerkeywords"]?></p>
   			<?
			}
		  ?>
		  </div>
		</div>
		<?
		}
	?>
		  <!--Bottom Navigation - Archive, Saved Search plus Collection-->
		<div class="BottomInpageNav">
		<? if (($archive==0) && (strpos($search,"!")===false) && $archive_search) { 
			$arcresults=do_search($search,$restypes,$order_by,2,0);
			if (is_array($arcresults)) {$arcresults=count($arcresults);} else {$arcresults=0;}
			if ($arcresults>0) 
				{
				?>
				<div class="InpageNavLeftBlock"><a href="search.php?search=<?=urlencode($search)?>&archive=2">&gt;&nbsp;<?=$lang["view"]?> <span class="Selected"><?=number_format($arcresults)?></span> <?=($arcresults==1)?$lang["match"]:$lang["matches"]?> <?=$lang["inthearchive"]?></a></div>
				<? 
				}
			else
				{
				?>
				<div class="InpageNavLeftBlock">&gt;&nbsp;<?=$lang["nomatchesinthearchive"]?></div>
				<? 
				}
			} ?>
			<? if (strpos($search,"!")===false && !checkperm("b")) { ?>
			<div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?=urlencode($search)?>&restypes=<?=urlencode($restypes)?>&archive=<?=$archive?>" target="collections">&gt;&nbsp;<?=$lang["savethissearchtocollection"]?></a></div>
			<div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?=urlencode($search)?>&restypes=<?=urlencode($restypes)?>&archive=<?=$archive?>&mode=resources" target="collections">&gt;&nbsp;<?=$lang["savesearchitemstocollection"]?></a></div>
			<? } ?>
			
			<? hook("resultsbottomtoolbar"); ?>
			
			<? 
	        $url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&archive=" . $archive;	

			if (isset($draw_pager)) {pager(false);} ?>
		</div>	
	<?	
	}
	else
	{
	?>
	<div class="BasicsBox"> 
		  <div class="NoFind">
		    <p><?=$lang["mustspecifyonekeyword"]?></p>
		  </div>
	</div>
	<?
	}

# Add the infobox.
?>
<div id="InfoBox"><div id="InfoBoxInner"> </div></div>

<?
include "../include/footer.php";
?>
