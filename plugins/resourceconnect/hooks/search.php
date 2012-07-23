<?php

/*
function HookResourceConnectSearchBeforesearchresults2()
	{
	global $lang,$search,$k,$archive,$resourceconnect_link_name,$search,$language;
	if ($k!="") {return false;} # Do not enable for external sharing
	if (substr($search,0,1)=="!") {return false;} # Only work for normal (non 'special') searches
	if ($search=="") {return false;} # Don't work for blank searches.
	
	if (!checkperm("resourceconnect")) {return false;}
	?>
	<div class="SearchOptionNav"><a href="../plugins/resourceconnect/pages/search.php?search=<?php echo urlencode($search) ?>&language_set=<?php echo $language ?>">&gt;&nbsp;<?php echo $resourceconnect_link_name ?></a></div>
	<?php
	}
*/

function HookResourceConnectSearchRepleacesearchresults()
	{
	global $lang,$language,$resourceconnect_affiliates,$baseurl,$resourceconnect_selected,$search,$resourceconnect_this,$resourceconnect_treat_local_system_as_affiliate;
	if (!checkperm("resourceconnect")) {return false;}

	# Do not replace results for special searches
	if (substr($search,0,1)=="!") {return false;}

	# Do not replace results for searches of this system.
	if (!$resourceconnect_treat_local_system_as_affiliate && $resourceconnect_selected==$resourceconnect_this) {return false;}

	$affiliate=$resourceconnect_affiliates[$resourceconnect_selected];
	$counter=$resourceconnect_selected;
	$page_size=15;
	?>	
		
	<div id="resourceconnect_container_<?php echo $counter ?>"><p><?php echo $lang["resourceconnect-pleasewait"] ?></p></div>
	<div class="clearerleft"></div>

	<script>
	// Repage / pager function
	var offset_<?php echo $counter ?>=0;
	function ResourceConnect_Repage(distance)
		{
		offset_<?php echo $counter ?>+=distance;
		if (offset_<?php echo $counter ?><0) {offset_<?php echo $counter ?>=0;}
	
		jQuery('#resourceconnect_container_<?php echo $counter ?>').load('<?php echo $baseurl ?>/plugins/resourceconnect/pages/ajax_request.php?search=<?php echo urlencode($search) ?>&pagesize=<?php echo $page_size ?>&affiliate=<?php echo $resourceconnect_selected ?>&affiliatename=<?php echo urlencode($affiliate["name"]) ?>&offset=' + offset_<?php echo $counter ?>);

		
		}

	ResourceConnect_Repage(0);
	</script>


	<?php
	
	return true;
	}
	
function HookResourceConnectSearchProcess_search_results($result,$search)
	{
	if (substr($search,0,11)!="!collection") {return false;} # Not a collection. Exit.
	$collection=substr($search,11);
	$affiliate_resources=sql_query("select * from resourceconnect_collection_resources where collection='" . escape_check($collection) . "'");
	if (count($affiliate_resources)==0) {return false;} # No affiliate resources. Exit.

	#echo "<pre>";
	#print_r($result);
	#print_r($affiliate_resources);
	#echo "</pre>";

	# Append the affiliate resources to the collection display
	foreach ($affiliate_resources as $resource)
		{
		$result[]=array
			(
			"ref"=>-87412,
			"access"=>0,
			"resource_type"=>0,
			"has_image"=>1,
			"thumb_width"=>0,
			"thumb_height"=>0,
			"file_extension"=>"",
			"field8"=>$resource["title"],
			"preview_extension"=>"",
			"file_modified"=>$resource["date_added"],
			"url"=>"../plugins/resourceconnect/pages/view.php?url=" . urlencode($resource["url"]),
			"thm_url"=>$resource["large_thumb"],
			"col_url"=>$resource["thumb"],
			"pre_url"=>$resource["xl_thumb"]
			);
		}

	return $result;
	}
	
	
function HookResourceConnectSearchReplaceresourcetools()
	{
	global $ref;
	return ($ref<0);
	}
	
function HookResourceConnectSearchReplaceresourcetoolssmall()
	{
	global $ref;
	return ($ref<0);
	}
	
function HookResourceConnectSearchReplaceresourcetoolsxl()
	{
	global $ref;
	return ($ref<0);
	}
	
	
	