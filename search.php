<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/search_functions.php";

$search=getvalescaped("search","");

# Append extra search parameters
$country=getvalescaped("country","");
if ($country!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "country:" . $country;}
$year=getvalescaped("year","");
if ($year!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "year:" . $year;}
$month=getvalescaped("month","");
if ($month!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "month:" . $month;}
$day=getvalescaped("day","");
if ($day!="") {$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . "day:" . $day;}


if (strpos($search,"!")===false) {setcookie("search",$search);} # store the search in a cookie if not a special search
$offset=getvalescaped("offset",0);if (strpos($search,"!")===false) {setcookie("saved_offset",$offset);}
if ((!is_numeric($offset)) || ($offset<0)) {$offset=0;}
$order_by=getvalescaped("order_by","relevance");if (strpos($search,"!")===false) {setcookie("saved_order_by",$order_by);}
$display=getvalescaped("display","thumbs");setcookie("display",$display);
$per_page=getvalescaped("per_page",$default_perpage);setcookie("per_page",$per_page);
$archive=getvalescaped("archive",0);if (strpos($search,"!")===false) {setcookie("saved_archive",$archive);}
$jumpcount=0;

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


include "include/header.php";


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
		<div class="InpageNavLeftBlock"><?=$lang["display"]?>:<br /><? if ($display=="thumbs") { ?><span class="Selected"><?=$lang["thumbnails"]?></span><? } else { ?><a href="<?=$url?>&display=thumbs"><?=$lang["thumbnails"]?></a><? } ?>&nbsp;|&nbsp;<? if ($display=="list") { ?><span class="Selected"><?=$lang["list"]?></span><? } else { ?><a href="<?=$url?>&display=list"><?=$lang["list"]?></a><? } ?></div>
		<?
		
		# order by
		#if (strpos($search,"!")===false)
		if (true) # Ordering enabled for collections/themes too now at the request of N Ward / Oxfam
			{
			$rel=$lang["relevance"];
			if (strpos($search,"!")!==false) {$rel=$lang["asadded"];}
			?>
			<div class="InpageNavLeftBlock "><?=$lang["sortorder"]?>:<br /><? if ($order_by=="relevance") {?><span class="Selected"><?=$rel?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=relevance&archive=<?=$archive?>"><?=$rel?></a><? } ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="popularity") {?><span class="Selected"><?=$lang["popularity"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=popularity&archive=<?=$archive?>"><?=$lang["popularity"]?></a><? } ?>
			
			<? if ($orderbyrating) { ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="rating") {?><span class="Selected"><?=$lang["rating"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=rating&archive=<?=$archive?>"><?=$lang["rating"]?></a><? } ?>
			<? } ?>
			
			&nbsp;|&nbsp;
			<? if ($order_by=="date") {?><span class="Selected"><?=$lang["date"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=date&archive=<?=$archive?>"><?=$lang["date"]?></a><? } ?>
			&nbsp;|&nbsp;
			<? if ($order_by=="colour") {?><span class="Selected"><?=$lang["colour"]?></span><? } else { ?><a href="search.php?search=<?=urlencode($search)?>&order_by=colour&archive=<?=$archive?>"><?=$lang["colour"]?></a><? } ?>
			</div>
			<?
			}
			
		$results=count($result);
	    $totalpages=ceil($results/$per_page);
	    if ($offset>$results) {$offset=0;}
    	$curpage=floor($offset/$per_page)+1;
        $url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&archive=" . $archive;	

		pager();
		$draw_pager=true;
		?></div>
		<?		
		
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
				$refs[]=$result[$n]["ref"]; # add this to a list of results, for query refining later
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
		if ($display=="list")
			{
			$rtypes=array();
			$types=get_resource_types();
			for ($n=0;$n<count($types);$n++) {$rtypes[$types[$n]["ref"]]=$types[$n]["name"];}
			}
		
		# loop and display the results
		for ($n=$offset;(($n<count($result)) && ($n<($offset+$per_page)));$n++)			
			{
			$ref=$result[$n]["ref"];
			$url="view.php?ref=" . $ref . "&search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&offset=" . urlencode($offset) . "&archive=" . $archive;
			if ($display=="thumbs") {
			?>
			 
<? if (!hook("renderresultthumb")) { ?>

	<!--Resource Panel-->
	<div class="ResourcePanelShell">
		<div class="ResourcePanel">
			<table border="0" class="ResourceAlign<? if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideo<? } ?>"><tr><td>
			<a href="<?=$url?>" title="<?=str_replace(array("\"","'"),"",htmlspecialchars($result[$n]["title"]))?>"><? if ($result[$n]["has_image"]==1) { ?><img width="<?=$result[$n]["thumb_width"]?>" height="<?=$result[$n]["thumb_height"]?>" src="<?=get_resource_path($ref,"thm",false,$result[$n]["preview_extension"])?>" class="ImageBorder" /><? } else { ?><img border=0 src="gfx/type<?=$result[$n]["resource_type"]?>.gif"><? } ?></a>
			</td>
			</tr></table>
			
			<div class="ResourcePanelInfo"><a href="<?=$url?>" title="<?=str_replace(array("\"","'"),"",htmlspecialchars($result[$n]["title"]))?>"><?=highlightkeywords(htmlspecialchars(tidy_trim($result[$n]["title"],22)),$search)?></a>&nbsp;</div>
			<div class="ResourcePanelCountry"><?=highlightkeywords(tidy_trim(TidyList(i18n_get_translated($result[$n]["country"])),14),$search)?>&nbsp;</div>
				

			<span class="IconPreview"><a href="preview.php?from=search&ref=<?=$ref?>&ext=<?=$result[$n]["preview_extension"]?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>" title="<?=$lang["fullscreenpreview"]?>"><img src="gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
			<span class="IconCollect"><a href="collections.php?add=<?=$ref?>&nc=<?=time()?>&search=<?=urlencode($search)?>" target="collections" title="<?=$lang["addtocurrentcollection"]?>"><img src="gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
			<span class="IconEmail"><a href="resource_email.php?ref=<?=$ref?>" title="<?=$lang["emailresource"]?>"><img src="gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span>
			<? if ($result[$n]["rating"]>0) { ?><div class="IconStar"></div><? } ?>
			<div class="clearer"></div>
		</div>
	<div class="PanelShadow"></div>
	</div>

<? } ?>
			 
			<?
			} else { # List view
			?>
			<!--List Item-->
			<tr>
			<td nowrap><div class="ListTitle"><a href="<?=$url?>"><?=highlightkeywords(tidy_trim($result[$n]["title"],45) . ((strlen(trim($result[$n]["country"]))>1)?(", " . tidy_trim(TidyList(i18n_get_translated($result[$n]["country"])),25)):""),$search) ?></a></div></td>
			<td><? if ($result[$n]["rating"]>0) { ?><div class="IconStar"> </div><? } else { ?>&nbsp;<? } ?></td>
			<td><?=$result[$n]["ref"]?></td>
			<td><?=$rtypes[$result[$n]["resource_type"]]?></td>
			<td><?=nicedate($result[$n]["creation_date"],false,true)?></td>
			<td ><div class="ListTools"><a href="<?=$url?>">&gt;&nbsp;<?=$lang["action-view"]?></a> &nbsp;<a href="collections.php?add=<?=$ref?>&nc=<?=time()?>&search=<?=urlencode($search)?>" target="collections">&gt;&nbsp;<?=$lang["action-addtocollection"]?></a> &nbsp;<a href="resource_email.php?ref=<?=$ref?>">&gt;&nbsp;<?=$lang["action-email"]?></a></div></td>
			</tr>
			<?
			}
			}
			
		if ($display=="list")
			{
			?>
	    	</table>
			</div>
			<?
			}
		?>
		<!--Key to Panel-->
		<div class="BottomInpageKey"> 
			<?=$lang["key"]?>:
		  <div class="KeyStar"><?=$lang["verybestresources"]?></div>
			<div class="KeyEmail"><?=$lang["emailresource"]?></div>
			<div class="KeyCollect"><?=$lang["addtocurrentcollection"]?></div>
			<div class="KeyPreview"><?=$lang["fullscreenpreview"]?></div>
		</div>
		<?
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
			<? if (strpos($search,"!")===false) { ?>
			<div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?=urlencode($search)?>&restypes=<?=urlencode($restypes)?>&archive=<?=$archive?>" target="collections">&gt;&nbsp;<?=$lang["savethissearchtocollection"]?></a></div>
			<div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?=urlencode($search)?>&restypes=<?=urlencode($restypes)?>&archive=<?=$archive?>&mode=resources" target="collections">&gt;&nbsp;<?=$lang["savesearchitemstocollection"]?></a></div>
			<? } ?>
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

include "include/footer.php";
?>
