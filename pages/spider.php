<?php
include "../include/db.php";
include "../include/general.php";
include "../include/resource_functions.php";

# Spider.php - provide a spiderable set of pages. Designed for the Google Appliance but should work with other
# search engines / appliances.

$password=getvalescaped("password",""); if ($password!=$spider_password) {exit ("Incorrect password.");}
$ref=getvalescaped("ref","",true);
$higher=getvalescaped("higher","");
$lower=getvalescaped("lower","");

# Log in as '$spider_usergroup' so only specific fields are shown.
$usergroup=$spider_usergroup;
$usergroup_data=get_usergroup($usergroup);
$usersearchfilter=$usergroup_data["search_filter"];
$userpermissions=array_merge(explode(",",trim($global_permissions)),explode(",",trim($usergroup_data["permissions"]))); 


if (($ref=="") && ($lower==""))
	{
	# Index page
	?><html><head><title>Spider Index</title></head><body><h1>Spider Index</h1><?php
	$max=get_max_resource_ref();
	for ($n=1;$n<=$max;$n+=1000)
		{
		$upper=$n+999;if ($upper>$max) {$upper=$max;}
		?><p><a href="spider.php?password=<?php echo $password?>&lower=<?php echo $n?>&higher=<?php echo $upper?>"><?php echo $n?> to <?php echo $upper?></a></p><?php
		}
	?></body></html><?php
	}


if ($lower!="")
	{
	# Resource list
	?><html><head><title>Spider Index</title></head><body><h1>Spider Index</h1>
	<p>
	<?php
	$list=get_resource_ref_range($lower,$higher);
	for ($n=1;$n<count($list);$n++)
		{
		$access=get_resource_access($list[$n]);
		if (in_array($access,$spider_access))
			{
			?>
			<a href="spider.php?password=<?php echo $password?>&ref=<?php echo $list[$n]?>"><?php echo $list[$n]?></a>
			<?php
			}
		}
	?></p></body></html><?php
	}
	
if ($ref!="")
	{
	# Resource view
	$resource=get_resource_data($ref);$resourcedata=get_resource_field_data($ref);
	$access=get_resource_access($ref);	
	if (!in_array($access,$spider_access)) {exit("Access denied");}
	
	if ($resource["has_image"]==1)
		{
		$thumbnail=get_resource_path($ref,false,"col",false,$resource["preview_extension"]);
		}
	else
		{
		$thumbnail=$baseurl."/gfx/type" . $resource["resource_type"] . "_col.gif";
		}
	?><html><head>
	<meta name="country" content="<?php echo TidyList($resource["field3"])?>">
	<meta name="date" content="<?php echo $resource["field".$date_field]?>">
	<meta name="thumbnail" content="<?php echo $thumbnail?>">
	<meta name="target" content="<?php echo $baseurl?>/pages/view.php?ref=<?php echo $ref?>">
	<?php
	$textblock="";
	for ($n=0;$n<count($resourcedata);$n++)
		{
		if ($resourcedata[$n]["keywords_index"]==1)
			{
			$value=trim($resourcedata[$n]["value"]);
			if (substr($value,0,1)==",") {$value=TidyList($value);}
			if ($value!="" && ($resourcedata[$n]["hide_when_restricted"]!=1 || $access==0)) 
				{
				$textblock.="<p rs_fieldid=\"" . $resourcedata[$n]["ref"] . "\" rs_fieldname=\"" . $resourcedata[$n]["name"] . "\">" . htmlspecialchars($value) . "</p>\n";
				}
			}
		if (($resourcedata[$n]["name"]=="caption") || ($resourcedata[$n]["name"]=="extract"))
			{
			?><meta name="description" content="<?php echo str_replace("\"","",trim($resourcedata[$n]["value"]))?>">
			<?php
			}
		if (($resourcedata[$n]["name"]=="project"))
			{
			?><meta name="projectid" content="<?php echo str_replace("\"","",trim($resourcedata[$n]["value"]))?>">
			<?php
			}
		if (($resourcedata[$n]["name"]=="videoid"))
			{
			?><meta name="videoid" content="<?php echo str_replace("\"","",trim($resourcedata[$n]["value"]))?>">
			<?php
			}
		}

	# Add resourcetype
	$resourcetypes=get_resource_types();
	foreach ($resourcetypes as $rt)
		{
		if ($rt["ref"]==$resource["resource_type"])
			{
			?>
			<meta name="resourcetype" content="<?php echo $rt["name"] ?>">
			<?php
			}
		}
	?>
	<title><?php echo trim($resource["field".$view_title_field])?></title></head>
	<body><h1><?php echo trim($resource["field".$view_title_field])?></h1>
	<?php echo $textblock?>
	</body></html><?php
	}
	
	
	
	
