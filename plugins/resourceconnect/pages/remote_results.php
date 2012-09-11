<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/search_functions.php";
include_once "../config/config.php";

$search=getvalescaped("search","");
$sign=getvalescaped("sign","");
$offset=getvalescaped("offset",0);
$pagesize=getvalescaped("pagesize",$resourceconnect_pagesize);
$affiliatename=getvalescaped("affiliatename","");

# Authenticate as 'resourceconnect' user.
global $resourceconnect_user; # Which user to use for remote access?
global $usergroup,$userpermissions,$userrequestmode;
$userinfo=sql_query("select u.usergroup,g.permissions from user u join usergroup g on u.usergroup=g.ref where u.ref='$resourceconnect_user'");
if (count($userinfo)>0)
	{
	$usergroup=$userinfo[0]["usergroup"];
	$userpermissions=explode(",",$userinfo[0]["permissions"]);
	if (hook("modifyuserpermissions")){$userpermissions=hook("modifyuserpermissions");}
	$userrequestmode=0; # Always use 'email' request mode for external users
	}

$restypes="";
# Resolve resource types
$resource_types=get_resource_types();
$rtx=explode(",",getvalescaped("restypes",""));
foreach ($rtx as $rt)
	{
	# Locate the resource type name in the local list.	
	# We have to handle resource type names because the resource type numeric IDs could be different from system to system.
	foreach ($resource_types as $resource_type)
		{
		if ($resource_type["name"]==$rt)
			{
			if ($restypes!="") {$restypes.=",";}	
			$restypes.=$resource_type["ref"];
			}
		}
	}
#print_r($restypes);


$results=do_search($search,$restypes,"relevance",0,$pagesize+$offset,"desc",false,"",true); # Search, ignoring filters (as fields are unlikely to match).

# The access key is used to sign all inbound queries, the remote system must therefore know the access key.
$access_key=md5("resourceconnect" . $scramble_key);

# Check the search query against the signature.
$expected_sign=md5($access_key . $search);
if ($sign!=$expected_sign) {exit("<p>Sorry, the request was not signed with the correct access key for this installation.</p>");}

if ($offset>count($results)) {while ($offset>count($results)) {$offset-=$pagesize;}}
if ($offset<0) {$offset=0;}

if (!is_array($results))
	{
	?>
	<h1><?php echo $affiliatename ?></h1>
	<p><?php echo $lang["nomatchingresources"] ?></p>
	<?php
	}
else
	{
	?>
	<div class="TopInpageNav">
	<div class="InpageNavLeftBlock"><?php echo $lang["youfound"] ?>:<br><span class="Selected"><?php echo count($results) ?></span> resources</div>

	<div class="InpageNavLeftBlock"><?php echo $lang["resourceconnect-affiliate"] ?>:<br><span class="Selected"><?php echo $affiliatename ?></span></div>	

	
	<span class="HorizontalWhiteNav">
	<br />
	<a href="#" 
	
	<?php if ($offset-$pagesize>=0) { ?>onClick="ResourceConnect_Repage(-<?php echo $pagesize ?>);return false;"<?php } ?>
	>&lt;&nbsp;<?php echo $lang["previous"]?></a>
	|
	<?php echo $lang["page"] . " " .  (floor($offset/$pagesize)+1) . " " . $lang["of"] . " " . (floor(count($results)/$pagesize)+1) ?>
	|
	<a href="#" 
	<?php if ($offset+$pagesize<=count($results)) { ?>onClick="ResourceConnect_Repage(<?php echo $pagesize ?>);return false;"<?php } ?>
	
	><?php echo $lang["next"]?>&nbsp;&gt;</a>
	</span>
	</div>
	<div class="clearerleft"></div>
	<!--<h1><?php echo $affiliatename ?></h1>-->
	<?php
	
	for ($n=$offset;$n<count($results) && $n<($offset+$pagesize);$n++)
		{
		$result=$results[$n];
		$ref=$result["ref"];
		$url=$baseurl . "/pages/view.php?ref=" . $ref . "&k=" . substr(md5($access_key . $ref),0,10) . "&language_set=" . urlencode($language) . "&search=" . urlencode($search) . "&offset=" . $offset . "&resourceconnect_source=" . urlencode(getval("resourceconnect_source",""));
		
		# Wrap with local page that includes header/footer/sidebar
		$link_url="../plugins/resourceconnect/pages/view.php?search=" . urlencode($search) . "&url=" . urlencode($url);
		
		$title=str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result["field".$view_title_field])));
		
		# Add to collections link.
		$add_url=getval("resourceconnect_source","") . "/plugins/resourceconnect/pages/add_collection.php?nc=" . time();
		$add_url.="&title=" . urlencode(get_data_by_field($ref,$view_title_field));
		$add_url.="&url=" . urlencode(str_replace("&search","&source_search",$url)); # Move the search so it doesn't get set, and therefore the nav is hidden when viewing the resource
		$add_url.="&back=" . urlencode($baseurl . "/pages/view.php?" . $_SERVER["QUERY_STRING"]);
		# Add image 
		if ($result["has_image"]==1)
			{ 
			$add_url.="&thumb=" . urlencode(get_resource_path($ref,false,"col",false,"jpg"));
			$add_url.="&large_thumb=" . urlencode(get_resource_path($ref,false,"thm",false,"jpg"));
			$add_url.="&xl_thumb=" . urlencode(get_resource_path($ref,false,"pre",false,"jpg"));
			}	
		else
			{
			$add_url.="&thumb=" . urlencode($baseurl . "/gfx/" . get_nopreview_icon($result["resource_type"],$result["file_extension"],true));
			$add_url.="&large_thumb=" . urlencode($baseurl . "/gfx/" . get_nopreview_icon($result["resource_type"],$result["file_extension"],false));
			$add_url.="&xl_thumb=" . urlencode($baseurl . "/gfx/" . get_nopreview_icon($result["resource_type"],$result["file_extension"],false));
			}
		
		?>
		<div class="ResourcePanelShell">
		<div class="ResourcePanel">
		
		
		<table border="0" class="ResourceAlign"><tr><td>
		<a xtarget="_blank" href="<?php echo $link_url?>" title="<?php echo $title ?>"><?php if ($result["has_image"]==1) { ?><img  src="<?php echo get_resource_path($ref,false,"thm",false,$result["preview_extension"],-1,1,false,$result["file_modified"])?>" class="ImageBorder"
		/><?php } else { ?><img border=0 src="<?php echo $baseurl ?>/gfx/<?php echo get_nopreview_icon($result["resource_type"],$result["file_extension"],false,false,true) ?>" 
		/><?php } ?></a>
		</td>
		</tr>
	
		</table>	
		<div class="ResourcePanelInfo"><?php echo tidy_trim($title,25) ?>&nbsp;</div>

		<div class="ResourcePanelIcons">		
		<span class="IconCollect"><a target="collections" href="<?php echo $add_url ?>"><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		</div>
		<div class="clearer"></div>
		
		</div>
		</div>
		
		<?php
		
		
		}
	}