<?php
include(dirname(__FILE__)."/../../../include/db.php");
include(dirname(__FILE__)."/../../../include/general.php");
include(dirname(__FILE__)."/../../../include/search_functions.php");
include(dirname(__FILE__)."/../../../include/collections_functions.php");
include(dirname(__FILE__)."/rssfeed.php");

$api=true;
include(dirname(__FILE__)."/../../../include/authenticate.php");

// extra check if rss_limits are enabled
// enabled in config. Recommended to prevent anyone with
// an RSS URL from being able to do arbitrary searches of DB
if (isset($rss_limits) && $rss_limits){
	$keytotest = $api_scramble_key.getval('key','').getval('search','').getval('archive','');
	if (md5($keytotest) <> getval('skey','')){
		header("HTTP/1.0 403 Forbidden.");
		echo "HTTP/1.0 403 Forbidden.";
		exit;
	}
}


$search=getvalescaped("search","");
$starsearch=getvalescaped("starsearch","");


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

######## CAMILLO
#$order_by=getvalescaped("order_by","relevance");if (strpos($search,"!")===false) {setcookie("saved_order_by",$order_by);}
$order_by=getvalescaped("order_by","date");if (strpos($search,"!")===false) {setcookie("saved_order_by",$order_by);}
######## CAMILLO

$display=getvalescaped("display","thumbs");setcookie("display",$display);
$per_page=getvalescaped("per_page",12);setcookie("per_page",$per_page);
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

	$refs=array();
	#echo "search=$search";
	
	# Special query? Ignore restypes
	if (strpos($search,"!")!==false) {$restypes="";}
	
	# Story only? Display as list
	#if ($restypes=="2") {$display="list";}
	
	$result=do_search($search,$restypes,"relevance",$archive,100,"desc",false,$starsearch);
	
	//echo $result[0];

	# Create a title for the feed
	$searchstring="search=$search&restypes=$restypes&archive=$archive&starsearch=$starsearch";
	$feed_title=$applicationname ." - ".xml_entities(get_search_title($searchstring));

	
$r = new RSSFeed($feed_title, $baseurl, str_replace("%search%", xml_entities($searchstring), $lang["filtered_resource_update_for"]));

// rss fields can include any of thumbs, smallthumbs, list, xlthumbs display fields, or data_joins.
$all_field_info=get_fields_for_search_display($rss_fields);

$n=0;
foreach ($rss_fields as $display_field)
	{
	# Find field in selected list
	for ($m=0;$m<count($all_field_info);$m++)
		{
		if ($all_field_info[$m]["ref"]==$display_field)
			{
			$field_info=$all_field_info[$m];
			$df[$n]['ref']=$display_field;
			$df[$n]['name']=$field_info['name'];
			$df[$n]['title']=$field_info['title'];
			$df[$n]['type']=$field_info['type'];
			$df[$n]['value_filter']=$field_info['value_filter'];
			$n++;
			}
		}
	}
$n=0;	

//$r->AddImage($title, $url, $link, $description = '')

# loop and display the results
for ($n=0;$n<count($result);$n++)			
	{
	$ref=$result[$n]["ref"];
	$title=xml_entities(i18n_get_translated($result[$n]["field".$view_title_field]));
	$creation_date=$result[$n]["field".$date_field];
	
	//echo $time = time();//date("r");
	
	// 2007-12-12 23:32:43
	// 0123456789012345678
    $year = substr($creation_date, 0, 4);
    $month = substr($creation_date, 5, 2);
    $day = substr($creation_date, 8, 2);
    $hour = substr($creation_date, 11, 2);
    $min = substr($creation_date, 14, 2);
    $sec = substr($creation_date, 17, 2);
    $pubdate = date('D, d M Y H:i:s +0100', mktime($hour, $min, $sec, $month, $day, $year));
		
	$url = $baseurl."/pages/view.php?ref=".$ref;
	
	$imgurl="";
	$imgurl=get_resource_path($result[$n]['ref'],true,"col",false);
	if ($result[$n]['has_image']!=1){ $imgurl=$baseurl."/gfx/".get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true,false,true);} 
	else{$imgurl=get_resource_path($result[$n]['ref'],false,"col",false);}
	$add_desc="";
	foreach ($rss_fields as $rssfield)
		{
		if (is_array($result[$n]))
			{	
			if (isset($result[$n]['field'.$rssfield])){	
				$value=i18n_get_translated($result[$n]['field'.$rssfield]);
			} else {
				$value=i18n_get_translated(get_data_by_field($result[$n]['ref'],$rssfield));
			}
			if ($value!="" && $value!=","){
				// allow for value filters
				for ($x=0;$x<count($df);$x++)
					{
					if ($df[$x]['ref']==$rssfield)
						{
						$plugin="../../value_filter_" . $df[$x]['name'] . ".php";
						if ($df[$x]['value_filter']!=""){
							eval($df[$x]['value_filter']);
						}
						else if (file_exists($plugin)) {include $plugin;}
						else if ($df[$x]["type"]==4 || $df[$x]["type"]==6 || $df[$x]["type"]==10) { 
							$value=NiceDate($value,true,false);
						}	
						if ($rss_show_field_titles){$add_desc.=$df[$x]['title'].": ";}	
					}	
							
				}
					
				$add_desc.=xml_entities($value)."<![CDATA[<br/>]]>";
			}
		}
	}
	
	$description = "<![CDATA[<img src='$imgurl' align='left' height='75'  border='0' />]]>". $add_desc;
	
	$val["pubDate"] = $pubdate;
	//$val["Category"] = $category;
	$val["guid"] = $ref;


	//	function AddArticle($title, $link, $description, $author, $optional = '')
	//	$r->AddArticle($category." - ".substr($title,0,20)."...", $url, $title, "", $val);
	$r->AddArticle($title, $url, $description,$val);	
	}

//Header("content-type: text/xml");

$r->Output();			
			
?>
