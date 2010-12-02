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
# Disable checkboxes for external users.
if ($k!="") {$use_checkboxes_for_selection=false;}

$search=getvalescaped("search","");

# create a display_fields array with information needed for detailed field highlighting
$df=array();

if (isset($metadata_template_resource_type) && isset($metadata_template_title_field)){
	$thumbs_display_fields[]=$metadata_template_title_field;
	}
$all_field_info=get_fields_for_search_display(array_unique(array_merge($thumbs_display_fields,$list_display_fields,$xl_thumbs_display_fields,$small_thumbs_display_fields)));

# get display and normalize display specific variables
$display=getvalescaped("display",$default_display);setcookie("display",$display);

if ($display=="thumbs"){ 
	$display_fields	= $thumbs_display_fields;  
	if (isset($search_result_title_height)) { $result_title_height = $search_result_title_height; }
	$results_title_trim = $search_results_title_trim;
	$results_title_wordwrap	= $search_results_title_wordwrap;
	}
	
if ($display=="list"){ 
	$display_fields	= $list_display_fields; 
	$results_title_trim = $list_search_results_title_trim;
	}
	
if ($display=="smallthumbs"){ 
	$display_fields	= $small_thumbs_display_fields; 
	if (isset($small_search_result_title_height)) { $result_title_height = $small_search_result_title_height; }
	$results_title_trim = $small_search_results_title_trim;
	$results_title_wordwrap = $small_search_results_title_wordwrap;
	}
if ($display=="xlthumbs"){ 
	$display_fields = $xl_thumbs_display_fields;
	if (isset($xl_search_result_title_height)) { $result_title_height = $xl_search_result_title_height; }
	$results_title_trim = $xl_search_results_title_trim;
	$results_title_wordwrap = $xl_search_results_title_wordwrap;
	}

$n=0;
foreach ($display_fields as $display_field)
	{
	# Find field in selected list
	for ($m=0;$m<count($all_field_info);$m++)
		{
		if ($all_field_info[$m]["ref"]==$display_field)
			{
			$field_info=$all_field_info[$m];
			$df[$n]['ref']=$display_field;
			$df[$n]['indexed']=$field_info['keywords_index'];
			$df[$n]['partial_index']=$field_info['partial_index'];
			$df[$n]['name']=$field_info['name'];
			$df[$n]['title']=$field_info['title'];
			$df[$n]['value_filter']=$field_info['value_filter'];
			$n++;
			}
		}
	}
$n=0;	

# create a sort_fields array with information for sort fields
$n=0;
$sf=array();
foreach ($sort_fields as $sort_field)
	{
	# Find field in selected list
	for ($m=0;$m<count($all_field_info);$m++)
		{
		if ($all_field_info[$m]["ref"]==$sort_field)
			{
			$field_info=$all_field_info[$m];
			$sf[$n]['ref']=$sort_field;
			$sf[$n]['title']=$field_info['title'];
			$n++;
			}
		}
	}
$n=0;	

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
			elseif (strpos($key,"_drop_")!==false)
				{
				# Dropdown field
				# Add keyword exactly as it is as the full value is indexed as a single keyword for dropdown boxes.
				$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . substr($key,11) . ":" . $value;
				}		
			elseif (strpos($key,"_cat_")!==false)
				{
				# Category tree field
				# Add keyword exactly as it is as the full value is indexed as a single keyword for dropdown boxes.
				$value=str_replace(",",";",$value);
				if (substr($value,0,1)==";") {$value=substr($value,1);}
				
				$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . substr($key,10) . ":" . $value;
				}		

			elseif (strpos($key,"_month")===false && strpos($key,"_day")===false)
				{
				# Standard field
				$values=explode(" ",$value);
				foreach ($values as $value)
					{
					# Standard field
					$search=(($search=="")?"":join(", ",split_keywords($search)) . ", ") . substr($key,6) . ":" . $value;
					}
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

$searchresourceid = "";
if (is_numeric(getval("searchresourceid",""))){
	$searchresourceid = getval("searchresourceid","");
	$search = "!resource$searchresourceid";
}
	
hook("searchstringprocessing");


# Fetch and set the values
if (strpos($search,"!")===false) {setcookie("search",$search);} # store the search in a cookie if not a special search
$offset=getvalescaped("offset",0);if (strpos($search,"!")===false) {setcookie("saved_offset",$offset);}
if ((!is_numeric($offset)) || ($offset<0)) {$offset=0;}
$order_by=getvalescaped("order_by",$default_sort);if (strpos($search,"!")===false) {setcookie("saved_order_by",$order_by);}
if ($order_by=="") {$order_by=$default_sort;}
$per_page=getvalescaped("per_page",$default_perpage);setcookie("per_page",$per_page);
$archive=getvalescaped("archive",0);if (strpos($search,"!")===false) {setcookie("saved_archive",$archive);}
$jumpcount=0;

# Most sorts such as popularity, date, and ID should be descending by default,
# but it seems custom display fields like title or country should be the opposite.
$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);setcookie("saved_sort",$sort);
$revsort = ($sort=="ASC") ? "DESC" : "ASC";

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
		if (!isset($collectiondata['savedsearch'])||(isset($collectiondata['savedsearch'])&&$collectiondata['savedsearch']==null)){ $collection_tag='';} else {$collection_tag=$lang['smartcollection'].": ";}
		
		$collection_title = '<div align="left"><h1>'.$collection_tag.$collectiondata ["name"].'</h1> ';
		if ($k==""){$collection_title.='<a href="collections.php?collection='.$collectiondata["ref"].'" target="collections">&gt;&nbsp;'.$lang['action-select'].' '.$lang['collection'].'</a>';}
		if ($k==""&&$preview_all){$collection_title.='&nbsp;&nbsp;<a href="preview_all.php?ref='.$collectiondata["ref"].'&order_by='.$order_by.'&sort='.$sort.'&archive='.$archive.'&k='.$k.'">&gt;&nbsp;'.$lang['preview_all'].'</a>';}
		$collection_title.='</div>';
		if ($display!="list"){$collection_title.= '<br>';}
		}
	}


if ($search_titles){
	if (substr($search,0,5)=="!last"){
		$collection_title = '<h1>'.$lang["recent"].' '.substr($search,5,strlen($search)).'</h1> ';
	}
	if (substr($search,0,8)=="!related") {
		$resource=explode(" ",$search);$resource=str_replace("!related","",$resource[0]);
		$collection_title = '<h1>'.$lang["relatedresources"].' - '.$lang['id'].$resource.'</h1> ';
	}
	if ($archive==-2){
	$collection_title = '<h1>'.$lang["status-2"].'</h1> ';
	}
	if ($archive==-1){
	$collection_title = '<h1>'.$lang["status-1"].'</h1> ';
	}
	if ($archive==2){
	$collection_title = '<h1>'.$lang["status2"].'</h1> ';
	}
	if ($archive==3){
	$collection_title = '<h1>'.$lang["status3"].'</h1> ';
	}
	if (substr($search,0,15)=="!archivepending") {
		$collection_title = '<h1>'.$lang["status1"].'</h1> ';
	}
	if (substr($search,0,14)=="!contributions") {
		$cuser=explode(" ",$search);$cuser=str_replace("!contributions","",$cuser[0]);
		if ($cuser==$userref && $archive==-2){$collection_title = '<h1>'.$lang["viewcontributedps"].'</h1> ';}
	}
	if (substr($search,0,7)=="!unused") {
		$collection_title = '<h1>'.$lang["uncollectedresources"].'</h1> ';
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

# if search is not a special search (ie. !recent), use starsearchvalue.
if (getval("search","")!="" && strpos(getval("search",""),"!")!==false)
	{
	$starsearch="";
	}
else
	{
	$starsearch=getvalescaped("starsearch","");	
	setcookie("starsearch",$starsearch);
}

# If returning to an old search, restore the page/order by
if (!array_key_exists("search",$_GET))
	{
	$offset=getvalescaped("saved_offset",0);setcookie("saved_offset",$offset);
	$order_by=getvalescaped("saved_order_by","relevance");setcookie("saved_order_by",$order_by);
	$sort=getvalescaped("saved_sort","");setcookie("saved_sort",$sort);
	$archive=getvalescaped("saved_archive",0);setcookie("saved_archive",$archive);
	}
	
# If requested, refresh the collection frame (for redirects from saves)
if (getval("refreshcollectionframe","")!="")
	{
	refresh_collection_frame();
	}

# Include javascript for infobox panels.
$headerinsert.="
<script src=\"../lib/js/infobox" . ($infobox_image_mode?"_image":"") . ".js?css_reload_key=" . $css_reload_key . "\" type=\"text/javascript\"></script>
";

if ($display_user_rating_stars){
	$headerinsert.="
	<script src=\"".$baseurl."/lib/js/user_rating_searchview.js\" type=\"text/javascript\"></script>";
}

if ($infobox)
	$bodyattribs="OnMouseMove='InfoBoxMM(event);'";

# Include function for reordering
if ($allow_reorder && $display!="list")
	{
	$url="search.php?search=" . urlencode($search) ;
	
	# Also check for the parameter and reorder as necessary.
	$reorder=getvalescaped("reorder","");
	if ($reorder!="")
		{
		$r=explode("-",$reorder);
		$wait=swap_collection_order(substr($r[0],13),$r[1],substr($search,11));
        refresh_collection_frame();
		?><script>document.location='<?php echo $url?>';top.collections.location.href='collections.php?ref=<?php echo substr($search,11);?>';</script><?php
		}
	}

# Initialise the results references array (used later for search suggestions)
$refs=array();

# Special query? Ignore restypes
if (strpos($search,"!")!==false) {$restypes="";}

# Do the search!
$result=do_search($search,$restypes,$order_by,$archive,$per_page+$offset,$sort,false,$starsearch);

# Do the public collection search if configured.

if (($search_includes_themes || $search_includes_public_collections) && $search!="" && substr($search,0,1)!="!" && $offset==0)
{
    $collections=search_public_collections($search,"theme","ASC",!$search_includes_themes,!$search_includes_public_collections,true);
}
# Special case: numeric searches (resource ID) and one result: redirect immediately to the resource view.
if ((($config_search_for_number && is_numeric($search)) || $searchresourceid > 0) && is_array($result) && count($result)==1)
	{
	redirect("pages/view.php?ref=" . $result[0]["ref"] . "&search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&sort=".$sort."&offset=" . urlencode($offset) . "&archive=" . $archive . "&k=" . $k);
	}
	

# Include the page header to and render the search results
include "../include/header.php";

if ($allow_reorder && $display!="list")
	{
	$url="search.php?search=" . urlencode($search) ;
	?>
	<script type="text/javascript">
	function ReorderResources(id1,id2)
		{
		document.location='<?php echo $url?>&reorder=' + id1 + '-' + id2;
		}
	</script><?php
}

# Extra CSS to support more height for titles on thumbnails.
if (isset($result_title_height))
	{
	?>
	<style>
	.ResourcePanelInfo .extended
		{
		white-space:normal;
		height: <?php echo $result_title_height ?>px;
		}
	</style>
	<?php
	}


# Extra CSS if using Image Infoboxes ($infobox_image_mode)
if ($infobox_image_mode)
	{
	?>
	<style>
	#InfoBox
		{
		width:400px;height:450px;
		}
	#InfoBoxInner
		{
		height:350px;
		}
	</style>
	<?php
	
	}

if (is_array($result)||(isset($collections)&&(count($collections)>0)))
	{
	$url="search.php?search=" . urlencode($search) . "&order_by=" . $order_by . "&sort=".$sort."&offset=" . $offset . "&archive=" . $archive."&sort=".$sort;
	?>
	<div class="TopInpageNav">
	<div class="InpageNavLeftBlock"><?php echo $lang["youfound"]?>:<br /><span class="Selected"><?php echo number_format(is_array($result)?count($result):0)?><?php echo (count($result)==$max_results)?"+":""?></span> <?php if (count($result)==1){echo $lang["youfoundresource"];} else {echo $lang["youfoundresources"];}?></div>
	<div class="InpageNavLeftBlock"><?php echo $lang["display"]?>:<br />
	<?php if ($xlthumbs==true) { ?> <?php if ($display=="xlthumbs") { ?><span class="Selected"><?php echo $lang["xlthumbs"]?></span><?php } else { ?><a href="<?php echo $url?>&display=xlthumbs&k=<?php echo $k?>"><?php echo $lang["xlthumbs"]?></a><?php } ?>&nbsp; |&nbsp;<?php } ?>
	<?php if ($display=="thumbs") { ?> <span class="Selected"><?php echo $lang["largethumbs"]?></span><?php } else { ?><a href="<?php echo $url?>&display=thumbs&k=<?php echo $k?>"><?php echo $lang["largethumbs"]?></a><?php } ?>&nbsp; |&nbsp; 
	<?php if ($smallthumbs==true) { ?> <?php if ($display=="smallthumbs") { ?><span class="Selected"><?php echo $lang["smallthumbs"]?></span><?php } else { ?><a href="<?php echo $url?>&display=smallthumbs&k=<?php echo $k?>"><?php echo $lang["smallthumbs"]?></a><?php } ?>&nbsp; |&nbsp;<?php } ?>
	<?php if ($display=="list") { ?> <span class="Selected"><?php echo $lang["list"]?></span><?php } else { ?><a href="<?php echo $url?>&display=list&k=<?php echo $k?>"><?php echo $lang["list"]?></a><?php } ?> <?php hook("adddisplaymode"); ?> </div>
	<?php
	
	# order by
	#if (strpos($search,"!")===false)
	if (true) # Ordering enabled for collections/themes too now at the request of N Ward / Oxfam
		{
		$rel=$lang["relevance"];
		if (strpos($search,"!")!==false) {$rel=$lang["asadded"];}
		?>
		<div class="InpageNavLeftBlock "><?php echo $lang["sortorder"]?>:<br /><?php if ($order_by=="relevance") {?><span class="Selected"><?php echo $rel?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=relevance&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $rel?></a><?php } ?>
		
		<?php if ($random_sort){?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="random") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=random&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["random"]?></a></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=random&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["random"]?></a><?php } ?>
		<?php } ?>
		
		&nbsp;|&nbsp;
		<?php if ($order_by=="popularity") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=popularity&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["popularity"]?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=popularity&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["popularity"]?></a><?php } ?>
		
		<?php if ($orderbyrating) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="rating") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=rating&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["rating"]?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=rating&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["rating"]?></a><?php } ?>
		<?php } ?>
		
		<?php if ($date_column){?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="date") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=date&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["date"]?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=date&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["date"]?></a><?php } ?>
		<?php } ?>
		
		<?php if ($colour_sort) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="colour") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=colour&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["colour"]?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=colour&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["colour"]?></a><?php } ?>
		<?php } ?>
		
		<?php if ($order_by_resource_id) { ?>
		&nbsp;|&nbsp;
		<?php if ($order_by=="resourceid") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=resourceid&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["resourceid"]?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=resourceid&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["resourceid"]?></a><?php } ?>
		<?php } ?>
		
		<?php # add thumbs_display_fields to sort order links for thumbs views
		if (count($sf)>0){
			for ($x=0;$x<count($sf);$x++)
				{
				if (!isset($metadata_template_title_field)){$metadata_template_title_field=false;} 
				if ($sf[$x]['ref']!=$metadata_template_title_field){?>
				&nbsp;|&nbsp;
				<?php if ($order_by=="field".$sf[$x]['ref']) {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=field<?php echo $sf[$x]['ref']?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo i18n_get_translated($sf[$x]['title'])?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=field<?php echo $sf[$x]['ref']?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo i18n_get_translated($sf[$x]['title'])?></a><?php } ?>
				<?php } ?>
				<?php } ?>	
			<?php } ?>		
		
		<?php hook("sortorder");?>
		</div>
		<div class="InpageNavLeftBlock"><?php echo $lang["resultsdisplay"]?>:<br />
		<?php 
		for($n=0;$n<count($results_display_array);$n++){?>
		<?php if ($per_page==$results_display_array[$n]){?><span class="Selected"><?php echo $results_display_array[$n]?></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>&per_page=<?php echo $results_display_array[$n]?>&sort=<?php echo $sort?>"><?php echo $results_display_array[$n]?></a><?php } ?><?php if ($n>-1&&$n<count($results_display_array)-1){?>&nbsp;|<?php } ?>
		<?php } ?>
		</div>
		
		<?php
		}
		
	$results=count($result);
	$totalpages=ceil($results/$per_page);
	if ($offset>$results) {$offset=0;}
	$curpage=floor($offset/$per_page)+1;
	$url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&sort=".$sort."&archive=" . $archive . "&k=" . $k."&sort=".$sort;	

	pager();
	$draw_pager=true;
	?>
	</div>
	<?php echo $collection_title ?>
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
		<?php if (!hook("listcheckboxesheader")){?>
		<?php if ($use_checkboxes_for_selection){?><td><?php echo $lang['addremove'];?></td><?php } ?>
		<?php } # end hook listcheckboxesheader 

		for ($x=0;$x<count($df);$x++)
			{?>
			<?php if ($order_by=="field".$df[$x]['ref']) {?><td class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=field<?php echo $df[$x]['ref']?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo i18n_get_translated($df[$x]['title'])?></a><div class="<?php echo $sort?>">&nbsp;</div></td><?php } else { ?><td><a href="search.php?search=<?php echo urlencode($search)?>&order_by=field<?php echo $df[$x]['ref']?>&sort=<?php echo $revsort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo i18n_get_translated($df[$x]['title'])?></a></td><?php } ?>
			<?php }
		
		if ($display_user_rating_stars && $k=="" ){?><td><?php if ($order_by=="popularity") {?><span class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&order_by=popularity&archive=<?php echo $archive?>&k=<?php echo $k?>&sort=<?php echo $revsort?>"><?php echo $lang["popularity"]?></a><div class="<?php echo $sort?>">&nbsp;</div></span><?php } else { ?><a href="search.php?search=<?php echo urlencode($search)?>&order_by=popularity&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["popularity"]?></a><?php } ?></td><?php } ?>
		<td>&nbsp;</td><!-- contains admin ratings -->
		<?php if ($id_column){?><?php if ($order_by=="resourceid"){?><td class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=resourceid&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["id"]?></a><div class="<?php echo $sort?>">&nbsp;</div></td><?php } else { ?><td><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=resourceid&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["id"]?></a></td><?php } ?><?php } ?>
		<?php if ($resource_type_column){?><?php if ($order_by=="resourcetype"){?><td class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=resourcetype&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["type"]?></a><div class="<?php echo $sort?>">&nbsp;</div></td><?php } else { ?><td><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=resourcetype&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["type"]?></a></td><?php } ?><?php } ?>
		<?php if ($date_column){?><?php if ($order_by=="date"){?><td class="Selected"><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=date&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["date"]?></a><div class="<?php echo $sort?>">&nbsp;</div></td><?php } else { ?><td><a href="search.php?search=<?php echo urlencode($search)?>&sort=<?php echo $revsort?>&order_by=date&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["date"]?></a></td><?php } ?><?php } ?>
		<?php hook("addlistviewtitlecolumn");?>
		<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
		</tr>
		<?php } ?> <!--end hook replace listviewtitlerow-->
		<?php
		}
		
	# Include public collections and themes in the main search, if configured.		
	if (isset($collections))
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
	if (is_array($result)){
	# loop and display the results
	for ($n=$offset;(($n<count($result)) && ($n<($offset+$per_page)));$n++)			
		{
		$ref=$result[$n]["ref"];
		$GLOBALS['get_resource_data_cache'][$ref] = $result[$n];
		$url="view.php?ref=" . $ref . "&search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&sort=".$sort."&offset=" . urlencode($offset) . "&archive=" . $archive . "&k=" . $k; ?>
		<?php 
		$rating="";
		if (isset($rating_field)){$rating="field".$rating_field;}
		
			
				
			if ($display=="thumbs") { #  ---------------------------- Thumbnails view ----------------------------
			?>
		 
<?php if (!hook("renderresultthumb")) { ?>

<!--Resource Panel-->
	<div class="ResourcePanelShell" id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanel">
	
<?php if (!hook("renderimagethumb")) { ?>			
	<?php $access=get_resource_access($result[$n]);
	$use_watermark=check_use_watermark();?>
	<table border="0" class="ResourceAlign<?php if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideo<?php } ?>">
	<tr><td>
	<a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img <?php if ($result[$n]["thumb_width"]!="" && $result[$n]["thumb_width"]!=0 && $result[$n]["thumb_height"]!="") { ?> width="<?php echo $result[$n]["thumb_width"]?>" height="<?php echo $result[$n]["thumb_height"]?>" <?php } ?> src="<?php echo get_resource_path($ref,false,"thm",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"])?>" class="ImageBorder"
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	 /><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],false) ?>" 
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	/><?php } ?></a>
		</td>
		</tr></table>
<?php } ?> <!-- END HOOK Renderimagethumb-->	
<?php if ($display_user_rating_stars && $k==""){ ?>
		<?php if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}?>
		
		<div  class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">&nbsp;<?php 
		for ($z=1;$z<=5;$z++)
			{
			?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"><span id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
			}
		?>
		</div>
		<?php } ?>
<?php if (!hook("replaceicons")) { ?>
<?php hook("icons");?>
<?php } //end hook replaceicons ?>
<?php if (!hook("rendertitlethumb")) { ?>	

<?php } ?> <!-- END HOOK Rendertitlethumb -->			
		
		<?php
		# thumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			#value filter plugin -tbd	
			$value=$result[$n]['field'.$df[$x]['ref']];
			$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
			if ($df[$x]['value_filter']!=""){
				eval($df[$x]['value_filter']);
			}
			else if (file_exists($plugin)) {include $plugin;}
			# swap title fields if necessary
			if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field)){
				if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type)){
					$value=$result[$n]['field'.$metadata_template_title_field];
					}
				}
			?>		
			<?php 
			// extended css behavior 
			if ( in_array($df[$x]['ref'],$thumbs_display_extended_fields) &&
			( (isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) ){ ?>
			<?php if (!hook("replaceresourcepanelinfo")){?>
			<div class="ResourcePanelInfo"><div class="extended">
			<?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link
			echo str_replace("#zwspace","&#x200b",highlightkeywords(htmlspecialchars(wordwrap(tidy_trim(TidyList(i18n_get_translated($value)),$results_title_trim),$results_title_wordwrap,"#zwspace;",true)),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed']))?><?php if ($show_extension_in_search) { ?><?php echo " [" . strtoupper($result[$n]["file_extension"] . "]")?><?php } ?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div></div>
			<?php } /* end hook replaceresourcepanelinfo */?>
			<?php 

			// normal behavior
			} else if  ( (isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) {?> 
			<div class="ResourcePanelInfo"><?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),20),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed'])?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div><div class="clearer"></div>
			<?php } ?>
			<?php
			}
		?>
		
		<div class="ResourcePanelIcons">&nbsp;</div>	
				
		<?php if (!hook("replacefullscreenpreviewicon")){?>
		<span class="IconPreview"><a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
		<?php } /* end hook replacefullscreenpreviewicon */?>
		
		<?php if(!hook("iconcollect")){?>
		<?php if (!checkperm("b") && $k=="" && !$use_checkboxes_for_selection) { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12"/></a></span>
		<?php } ?>
		<?php } # end hook iconcollect ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="" && !$use_checkboxes_for_selection) { ?>
		<?php if ($search=="!collection".$usercollection){?><span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
		
		<?php if ($allow_share && $k=="") { ?><span class="IconEmail"><a href="resource_email.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["emailresource"]?>"><img src="../gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span><?php } ?>
		<?php if (isset($result[$n][$rating]) && 
		$result[$n][$rating]>0) { ?><div class="IconStar"></div><?php } ?>
		<?php if ($collection_reorder_caption && $allow_reorder) { ?>
		<span class="IconComment"><a href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo substr($search,11)?>" title="<?php echo $lang["addorviewcomments"]?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>			
		<?php if ($order_by=="relevance"){?><div class="IconReorder" onmousedown="InfoBoxWaiting=false;"> </div><?php } ?>
		<?php } 
		hook("largesearchicon");?><div class="clearer"></div>
		<?php if(!hook("thumbscheckboxes")){?>
		<?php if ($use_checkboxes_for_selection){?><input type="checkbox" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onclick="if ($('check<?php echo $ref?>').checked){ <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if ($('check<?php echo $ref?>').checked==false){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&sort=<?php echo $revsort?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"><?php } ?>
		<?php } # end hook thumbscheckboxes?>
	</div>
<div class="PanelShadow"></div>
</div>
<?php if ($allow_reorder && $display!="list") { 
# Javascript drag/drop enabling.
?>
<script type="text/javascript">
new Draggable('ResourceShell<?php echo $ref?>',{handle: 'IconReorder', revert: true});
Droppables.add('ResourceShell<?php echo $ref?>',{accept: 'ResourcePanelShell', onDrop: function(element) {ReorderResources(element.id,<?php echo $ref?>);}, hoverclass: 'ReorderHover'});
</script>
<?php } ?> 
<?php } ?>

		<?php 
		
		
		
		
		
		
		} 
		
					if ($display=="xlthumbs") { #  ---------------------------- X-Large Thumbnails view ----------------------------
			?>
		 
<?php if (!hook("renderresultlargethumb")) { ?>

<!--Resource Panel-->
	<div class="ResourcePanelShellLarge" id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanelLarge">
	
<?php if (!hook("renderimagelargethumb")) { ?>			
	<?php $access=get_resource_access($result[$n]);
	$use_watermark=check_use_watermark();?>
	<table border="0" class="ResourceAlignLarge<?php if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideoLarge<?php } ?>">
	<tr><td>
	<a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img src="<?php echo get_resource_path($ref,false,"pre",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"])?>" class="ImageBorder"
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	 /><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],false) ?>" 
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	/><?php } ?></a>
		</td>
		</tr></table>
<?php } ?> <!-- END HOOK Renderimagelargethumb-->	
<?php if ($display_user_rating_stars && $k==""){ ?>
		<?php if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}?>
		
		<div  class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">&nbsp;<?php
		for ($z=1;$z<=5;$z++)
			{
			?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"><span id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
			}
		?>
		</div>
		<?php } ?>
<?php if (!hook("replaceicons")) { ?>
<?php hook("icons");?>
<?php } //end hook replaceicons ?>
<?php if (!hook("rendertitlelargethumb")) { ?>	

<?php } ?> <!-- END HOOK Rendertitlelargethumb -->			
		
		<?php
		# thumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			#value filter plugin -tbd	
			$value=$result[$n]['field'.$df[$x]['ref']];
			$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
			if ($df[$x]['value_filter']!=""){
				eval($df[$x]['value_filter']);
			}
			else if (file_exists($plugin)) {include $plugin;}
			# swap title fields if necessary
			if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field)){
				if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type)){
					$value=$result[$n]['field'.$metadata_template_title_field];
					}
				}
			?>		
			<?php 
			// extended css behavior 
			if ( in_array($df[$x]['ref'],$xl_thumbs_display_extended_fields) &&
			( (isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) ){ ?>
			<?php if (!hook("replaceresourcepanelinfolarge")){?>
			<div class="ResourcePanelInfo"><div class="extended">
			<?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link
			echo str_replace("#zwspace","&#x200b",highlightkeywords(htmlspecialchars(wordwrap(tidy_trim(TidyList(i18n_get_translated($value)),$results_title_trim),$results_title_wordwrap,"#zwspace;",true)),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed']))?><?php if ($show_extension_in_search) { ?><?php echo " [" . strtoupper($result[$n]["file_extension"] . "]")?><?php } ?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div></div>
			<?php } /* end hook replaceresourcepanelinfolarge */?>
			<?php 

			// normal behavior
			} else if  ( (isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) {?> 
			<div class="ResourcePanelInfo"><?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),28),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed'])?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div><div class="clearer"></div>
			<?php } ?>
			<?php
			}
		?>
		
		<div class="ResourcePanelIcons">&nbsp;</div>	
				
		<?php if (!hook("replacefullscreenpreviewicon")){?>
		<span class="IconPreview"><a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
		<?php } /* end hook replacefullscreenpreviewicon */?>
		
		<?php if(!hook("iconcollect")){?>
		<?php if (!checkperm("b") && $k=="" && !$use_checkboxes_for_selection) { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12"/></a></span>
		<?php } ?>
		<?php } # end hook iconcollect ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="" && !$use_checkboxes_for_selection) { ?>
		<?php if ($search=="!collection".$usercollection){?><span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
		
		<?php if ($allow_share && $k=="") { ?><span class="IconEmail"><a href="resource_email.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["emailresource"]?>"><img src="../gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span><?php } ?>
		<?php if (isset($result[$n][$rating]) && $result[$n][$rating]>0) { ?><div class="IconStar"></div><?php } ?>
		<?php if ($collection_reorder_caption && $allow_reorder) { ?>
		<span class="IconComment"><a href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo substr($search,11)?>" title="<?php echo $lang["addorviewcomments"]?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>			
		<?php if ($order_by=="relevance"){?><div class="IconReorder" onmousedown="InfoBoxWaiting=false;"> </div><?php } ?>
		<?php } hook("xlargesearchicon");?>
		<div class="clearer"></div>
		<?php if(!hook("thumbscheckboxes")){?>
		<?php if ($use_checkboxes_for_selection){?><input type="checkbox" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onclick="if ($('check<?php echo $ref?>').checked){ <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if ($('check<?php echo $ref?>').checked==false){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&sort=<?php echo $revsort?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"><?php } ?>
		<?php } # end hook thumbscheckboxes?>
	</div>
<div class="PanelShadow"></div>
</div>
<?php if ($allow_reorder && $display!="list") { 
# Javascript drag/drop enabling.
?>
<script type="text/javascript">
new Draggable('ResourceShell<?php echo $ref?>',{handle: 'IconReorder', revert: true});
Droppables.add('ResourceShell<?php echo $ref?>',{accept: 'ResourcePanelShellLarge', onDrop: function(element) {ReorderResources(element.id,<?php echo $ref?>);}, hoverclass: 'ReorderHover'});
</script>
<?php } ?> 
<?php } ?>

		<?php 
		

		} elseif ($display == "smallthumbs") { # ---------------- Small Thumbs view ---------------------
		?>

<div class="ResourcePanelShellSmall" <?php if ($display_user_rating_stars){?> <?php } ?>id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanelSmall">	
		<?php if (!hook("renderimagesmallthumb")){;?>
		<?php $access=get_resource_access($result[$n]);
		$use_watermark=check_use_watermark();?>
		<table border="0" class="ResourceAlignSmall"><tr><td>
		<a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img  src="<?php echo get_resource_path($ref,false,"col",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"])?>" class="ImageBorder"
		<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
		 /><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true) ?>" 
		<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
		/><?php } ?></a>
		</td>
		</tr></table>				
		<?php } /* end Renderimagesmallthumb */?>
		
		
		<?php
		# smallthumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			#value filter plugin -tbd	
			$value=$result[$n]['field'.$df[$x]['ref']];
			$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
			if ($df[$x]['value_filter']!=""){
				eval($df[$x]['value_filter']);
			}
			else if (file_exists($plugin)) {include $plugin;}
			# swap title fields if necessary
			if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field)){
				if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type)){
					$value=$result[$n]['field'.$metadata_template_title_field];
					}
				}
			?>		
			<?php 
			// extended css behavior 
			if ( in_array($df[$x]['ref'],$small_thumbs_display_extended_fields) &&
			( (isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) ){ ?>
			<?php if (!hook("replaceresourcepanelinfosmall")){?>
			<div class="ResourcePanelInfo"><div class="extended">
			<?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link
			echo str_replace("#zwspace","&#x200b",highlightkeywords(htmlspecialchars(wordwrap(tidy_trim(TidyList(i18n_get_translated($value)),$results_title_trim),$results_title_wordwrap,"#zwspace;",true)),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed']))?><?php if ($show_extension_in_search) { ?><?php echo " [" . strtoupper($result[$n]["file_extension"] . "]")?><?php } ?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div></div>
			<?php } /* end hook replaceresourcepanelinfolarge */?>
			<?php 

			// normal behavior
			} else if  ( (isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) {?> 
			<div class="ResourcePanelInfo"><?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),28),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed'])?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div><div class="clearer"></div>
			<?php } ?>
			<?php
			}
		?>
		
		
		
		<?php if ($display_user_rating_stars && $k==""){ ?>
		<?php if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}?>
		
		<div  class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">&nbsp;<?php
	    for ($z=1;$z<=5;$z++)
			{
			?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"><span id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
			}
		?>
		</div>
		<?php } ?><?php hook("smallsearchfreeicon");?>
		<div class="ResourcePanelSmallIcons">
		<?php hook("smallsearchicon");?>
		<span class="IconPreview">
		<a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
		
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
		
		
		
		
		
		
		} else if ($display=="list") { # ----------------  List view -------------------
		?>
		<?php if (!hook("replacelistitem")) {?>
		<!--List Item-->
		<tr>
		<?php if(!hook("listcheckboxes")){?>
		<?php if ($use_checkboxes_for_selection){?><td width="30px"><input type="checkbox" style="position:relative;margin-bottom:-4px;top:-3px;height:21px;" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onclick="if ($('check<?php echo $ref?>').checked){ <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if ($('check<?php echo $ref?>').checked==false){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"></td><?php } ?>
		<?php } #end hook listcheckboxes 
		
	
		for ($x=0;$x<count($df);$x++){
		$value=$result[$n]['field'.$df[$x]['ref']];
		$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
		if ($df[$x]['value_filter']!=""){
			eval($df[$x]['value_filter']);
		}
		else if (file_exists($plugin)) {include $plugin;}
		# swap title fields if necessary
		if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field)){
			if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type)){
				$value=$result[$n]['field'.$metadata_template_title_field];
				}
			}
		if ( (isset($metadata_template_title_field)&& $df[$x]['ref']!=$metadata_template_title_field ) || !isset($metadata_template_title_field) ) {
			
			?><td nowrap><?php if ($x==0){ // add link to first item only ?><div class="ListTitle"><a <?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?> href="<?php echo $url?>"><?php } //end link conditional?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),$results_title_trim),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed']) ?><?php if ($x==0){ // add link to first item only ?></a><?php } //end link conditional ?>&nbsp;</div></td>
			<?php } 
		}
		
		if ($display_user_rating_stars && $k==""){ ?>
			<td>
			<?php if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}?>
		
			<div  class="RatingStars" style="text-align:left;margin:0px;" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">
			<?php for ($z=1;$z<=5;$z++)
				{
				?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"><span id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
				}
			?>
		</div></td>
		<?php } ?>
		
		
		<td><?php if (isset($result[$n][$rating])&& $result[$n][$rating]>0) { ?><?php for ($y=0;$y<$result[$n][$rating];$y++){?> <div class="IconStar"></div><?php } } else { ?>&nbsp;<?php } ?></td>
		<?php if ($id_column){?><td><?php echo $result[$n]["ref"]?></td><?php } ?>
		<?php if ($resource_type_column){?><td><?php if (array_key_exists($result[$n]["resource_type"],$rtypes)) { ?><?php echo i18n_get_translated($rtypes[$result[$n]["resource_type"]])?><?php } ?></td><?php } ?>
		<?php if ($date_column){?><td><?php echo nicedate($result[$n]["creation_date"],false,true)?></td><?php } ?>
		<?php hook("addlistviewcolumn");?>
		<td><div class="ListTools"><a <?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);"onmouseout="InfoBoxSetResource(0);"<?php } ?> href="<?php echo $url?>">&gt;&nbsp;<?php echo $lang["action-view"]?></a> &nbsp;<?php

		if (!checkperm("b")&& $k=="") { ?>
		<?php echo add_to_collection_link($ref,$search)?>&gt;&nbsp;<?php echo $lang["action-addtocollection"]?></a> &nbsp;
		<?php } ?>

		<?php if ($allow_share && $k=="") { ?><a href="resource_email.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["action-email"]?></a><?php } ?></div></td>
		
		
		</tr>
		<?php }
		
		
		
		
		 ?><!--end hook replacelistitem--> 
		<?php
		}
	
	hook("customdisplaymode");
	
		}
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
		<?php if (!hook("replacesearchkey")){?>
		<div class="BottomInpageKey"> 
			<?php echo $lang["key"]?>:
			<?php if ($display=="thumbs") { ?>
				
				<?php if ($orderbyrating) { ?><div class="KeyStar"><?php echo $lang["verybestresources"]?></div><?php } ?>
				<?php if ($allow_reorder) { ?><div class="KeyReorder"><?php echo $lang["reorderresources"]?></div><?php } ?>
				<?php if ($allow_reorder || (substr($search,0,11)=="!collection")) { ?><div class="KeyComment"><?php echo $lang["addorviewcomments"]?></div><?php } ?>
				<?php if ($allow_share) { ?><div class="KeyEmail"><?php echo $lang["emailresource"]?></div><?php } ?>
			<?php } ?>
			
			<?php if (!checkperm("b")) { ?><div class="KeyCollect"><?php echo $lang["addtocurrentcollection"]?></div><?php } ?>
			<div class="KeyPreview"><?php echo $lang["fullscreenpreview"]?></div>
			<?php hook("searchkey");?>
		</div>
		<?php }/*end replacesearchkey */?>
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
	<?php if($allow_smart_collections) { ?><div class="InpageNavLeftBlock"><a href="collections.php?addsmartcollection=<?php echo urlencode($search)?>&restypes=<?php echo urlencode($restypes)?>&archive=<?php echo $archive?>" target="collections">&gt;&nbsp;<?php echo $lang["savesearchassmartcollection"]?></a></div><?php } ?>
	<div class="InpageNavLeftBlock"><a href="collections.php?addsearch=<?php echo urlencode($search)?>&restypes=<?php echo urlencode($restypes)?>&archive=<?php echo $archive?>&mode=resources" target="collections">&gt;&nbsp;<?php echo $lang["savesearchitemstocollection"]?></a></div>
	<?php } ?>
	
	<?php hook("resultsbottomtoolbar"); ?>
	
	<?php 
	$url="search.php?search=" . urlencode($search) . "&order_by=" . urlencode($order_by) . "&sort=".$sort."&archive=" . $archive . "&k=" . $k;	

	if (isset($draw_pager)) {pager(false);} ?>
</div>	

<?php hook("endofsearchpage");?>
<?php	


# Add the infobox.
?>
<div id="InfoBox"><div id="InfoBoxInner"> </div></div>
<?php
include "../include/footer.php";
?>
