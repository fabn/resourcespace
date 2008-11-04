<?
include "../include/db.php";
include "../include/general.php";
include "../include/resource_functions.php";

# Spider.php - provide a spiderable set of pages. Designed for the Google Appliance but should work with other
# search engines / appliances.

$password=getvalescaped("password",""); if ($password!=$spider_password) {exit ("Incorrect password.");}
$ref=getvalescaped("ref","");
$higher=getvalescaped("higher","");
$lower=getvalescaped("lower","");


if (($ref=="") && ($lower==""))
	{
	# Index page
	?><html><head><title>Spider Index</title></head><body><h1>Spider Index</h1><?
	$max=get_max_resource_ref();
	for ($n=1;$n<=$max;$n+=1000)
		{
		$upper=$n+999;if ($upper>$max) {$upper=$max;}
		?><p><a href="spider.php?password=<?=$password?>&lower=<?=$n?>&higher=<?=$upper?>"><?=$n?> to <?=$upper?></a></p><?
		}
	?></body></html><?
	}


if ($lower!="")
	{
	# Resource list
	?><html><head><title>Spider Index</title></head><body><h1>Spider Index</h1>
	<p>
	<?
	$list=get_resource_ref_range($lower,$higher);
	for ($n=1;$n<count($list);$n++)
		{
		?>
		<a href="spider.php?password=<?=$password?>&ref=<?=$list[$n]?>"><?=$list[$n]?></a>
		<?
		}
	?></p></body></html><?
	}
	
if ($ref!="")
	{
	# Resource view
	$userpermissions[]="f*"; # Set access to all fields.
	$resource=get_resource_data($ref);$resourcedata=get_resource_field_data($ref);

	if ($resource["has_image"]==1)
		{
		$thumbnail=get_resource_path($ref,false,"col",false,$resource["preview_extension"]);
		}
	else
		{
		$thumbnail=$baseurl."/gfx/type" . $resource["resource_type"] . "_col.gif";
		}
	?><html><head>
	<meta name="country" content="<?=TidyList($resource["country"])?>">
	<meta name="date" content="<?=$resource["creation_date"]?>">
	<meta name="thumbnail" content="<?=$thumbnail?>">
	<meta name="target" content="<?=$baseurl?>/pages/view.php?ref=<?=$ref?>">
	<?
	$textblock="";
	for ($n=0;$n<count($resourcedata);$n++)
		{
		if ($resourcedata[$n]["keywords_index"]==1)
			{
			$value=trim($resourcedata[$n]["value"]);
			if (substr($value,0,1)==",") {$value=TidyList($value);}
			if ($value!="") {$textblock.="<p>$value</p>\n";}
			}
		if (($resourcedata[$n]["name"]=="caption") || ($resourcedata[$n]["name"]=="extract"))
			{
			?><meta name="description" content="<?=str_replace("\"","",trim($resourcedata[$n]["value"]))?>">
			<?
			}
		if (($resourcedata[$n]["name"]=="project"))
			{
			?><meta name="projectid" content="<?=str_replace("\"","",trim($resourcedata[$n]["value"]))?>">
			<?
			}
		if (($resourcedata[$n]["name"]=="videoid"))
			{
			?><meta name="videoid" content="<?=str_replace("\"","",trim($resourcedata[$n]["value"]))?>">
			<?
			}
		}
	?>
	<title><?=trim($resource["title"])?></title></head>
	<body><h1><?=trim($resource["title"])?></h1>
	<?=$textblock?>
	</body></html><?
	}
	
	
	
	