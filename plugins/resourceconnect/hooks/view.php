<?php

function HookResourceconnectViewNextpreviewregeneratekey()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 
	
	global $ref,$k,$scramble_key;
	
	# Create a new key when moving next/back for a given result set.
	
	$access_key=md5("resourceconnect" . $scramble_key);
	$k=md5($access_key . $ref);
	
	return $k;
	}

function HookResourceconnectViewViewallresults()	
	{
	# View all results.
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 

	global $lang,$search;
	?>
	|
	<a href="<?php echo getval("resourceconnect_source","") ?>/plugins/resourceconnect/pages/search.php?search=<?php echo urlencode($search) ?>"><?php echo $lang["viewallresults"]?></a>
	<?php
	
	}


function HookResourceconnectViewNextpreviousextraurl()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 

	# Forward the resourceconnect source.

	global $baseurl;
	echo "resourceconnect_source=" .$baseurl;
	}
	
function HookResourceconnectViewPreviewextraurl()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 

	# Forward the resourceconnect source.

	global $baseurl;
	echo "resourceconnect_source=" .$baseurl;
	}


function HookResourceconnectViewResourceactions_anonymous()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set.

	global $lang,$title_field,$ref,$baseurl,$search,$offset,$scramble_key,$language,$resource;
	
	# Generate access key
	$access_key=md5("resourceconnect" . $scramble_key);
	
	# Formulate resource link (for collections bar)
	$view_url=$baseurl . "/pages/view.php?ref=" . $ref . "&k=" . substr(md5($access_key . $ref),0,10) . "&language_set=" . urlencode($language) . "&resourceconnect_source=" . urlencode($baseurl);
	
	# Add to collections link.
	$url=getval("resourceconnect_source","") . "/plugins/resourceconnect/pages/add_collection.php?nc=" . time();
	$url.="&title=" . urlencode(get_data_by_field($ref,$title_field));
	$url.="&url=" . urlencode($view_url);
	
	# Add back URL
	$url.="&back=" . urlencode($baseurl . "/pages/view.php?" . $_SERVER["QUERY_STRING"]);
	
	# Add images
	if ($resource["has_image"]==1)
		{ 
		$url.="&thumb=" . urlencode(get_resource_path($ref,false,"col",false,"jpg"));
		}	
	else
		{
		$url.="&thumb=" . urlencode($baseurl . "/gfx/" . get_nopreview_icon($resource["resource_type"],$resource["file_extension"],true));
		}

	?>	
	<li><a target="collections" href="<?php echo $url ?>">&gt; <?php echo $lang["action-addtocollection"]?></a></li>
	<?php 
	}
	
	
function HookResourceconnectViewAfterheader()
	{
	?>
	<!-- START GRAB -->
	<?php
	}
	

function HookResourceconnectViewBeforefooter()
	{
	?>
	<!-- END GRAB -->
	<?php
	}

