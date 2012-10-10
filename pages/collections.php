<?php
include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$userrequestmode=0;$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection","",true),$k))) {include "../include/authenticate.php";}
if (checkperm("b")){exit("Permission denied");}
include "../include/research_functions.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

// copied from collection_manage to support compact style collection adds (without redirecting to collection_manage)
$addcollection=getvalescaped("addcollection","");
if ($addcollection!="")
	{
	# Add someone else's collection to your My Collections
	add_collection($userref,$addcollection);
	set_user_collection($userref,$addcollection);
	refresh_collection_frame();
	
   	# Log this
	daily_stat("Add public collection",$userref);
	}
/////

# Disable info box for external access.
if ($k!="") {$infobox=false;} 
# Disable checkboxes for external users.
if ($k!="") {$use_checkboxes_for_selection=false;}

# Hide/show thumbs - set cookie must be before header is sent
$thumbs=getval("thumbs",$thumbs_default);
setcookie("thumbs",$thumbs,0);

# Basket mode? - this is for the e-commerce user request modes.
if ($userrequestmode==2 || $userrequestmode==3)
	{
	# Enable basket
	$basket=true;	
	}
else
	{
	$basket=false;
	}

# Load collection info.
$cinfo=get_collection($usercollection);
	
# Check to see if the user can edit this collection.
$allow_reorder=false;
if (($k=="") && (($userref==$cinfo["user"]) || ($cinfo["allow_changes"]==1) || (checkperm("h"))))
	{
	$allow_reorder=true;
	}	
	
# Include function for reordering / infobox
if (($allow_reorder && $collection_reorder_caption) || $infobox || $use_checkboxes_for_selection || $collections_compact_style)
	{
	# Also check for the parameter and reorder as necessary.
	$reorder=getvalescaped("reorder",false);
	if ($reorder)
		{
		$neworder=json_decode(getvalescaped("order",false));
		update_collection_order($neworder,$usercollection);
		exit("SUCCESS");
		}
	}

	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html class="CollectBack">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $applicationname?></title>
<link href="../css/global.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
<?php if (!hook("adjustcolortheme")){?>
<link href="../css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss",$defaulttheme)?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss"/>
<?php } ?>
<!--[if lte IE 6]> <link href="../css/globalIE.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<!--[if lte IE 5.6]> <link href="../css/globalIE5.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<?php
# Include CSS files for for each of the plugins too (if provided)
for ($n=0;$n<count($plugins);$n++)
	{
	$csspath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/css/style.css";
	if (file_exists($csspath))
		{
		?>
		<link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/style.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print"  />
		<?php
		}
	$theme=((isset($userfixedtheme) && $userfixedtheme!=""))?$userfixedtheme:getval("colourcss",$defaulttheme);
	$csspath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/css/Col-".$theme.".css";	
	if (file_exists($csspath))
		{
		?>
		<link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/Col-<?php echo $theme?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="<?php echo $plugins[$n]?>css" />
		<?php
		}	
	}
?>

<?php
$collection=getvalescaped("collection","",true);
$entername=getvalescaped("entername","");


# ------------ Change the collection, if a collection ID has been provided ----------------
if ($collection!="")
	{
	hook("prechangecollection");
	#change current collection
	
	if ($k=="" && $collection==-1)
		{
		# Create new collection
		if ($entername!=""){ $name=$entername;} 
		else { $name=get_mycollection_name($userref);}
		$new=create_collection ($userref,$name);
		set_user_collection($userref,$new);
		
		# Log this
		daily_stat("New collection",$userref);
		}
	else
		{
		# Switch the existing collection
		if ($k=="") {set_user_collection($userref,$collection);}
		$usercollection=$collection;
		}

	hook("postchangecollection");
	}


# Include function for reordering / infobox
if (($allow_reorder && $collection_reorder_caption) || $infobox || $use_checkboxes_for_selection || $collections_compact_style)
	{
	?>
	 <script src="<?php echo $baseurl?>/lib/js/jquery-1.7.2.min.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>
     <script src="<?php echo $baseurl?>/lib/js/jquery-ui-1.8.20.custom.min.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>
	 <!--[if lte IE 7]><script src="<?php echo $baseurl?>/lib/js/json2.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script><![endif]-->
     <script type="text/javascript">
        jQuery.noConflict();
     </script>

	<script src="../lib/js/infobox_collection.js" type="text/javascript"></script>
	<script type="text/javascript">
	function ReorderResources(idsInOrder)
		{
		var newOrder = [];
		jQuery.each(idsInOrder, function() {
			newOrder.push(this.substring(13));
			}); 
		jQuery.ajax({
		  type: 'GET',
		  url: 'collections.php?collection=<?php echo $usercollection ?>&reorder=true',
		  data: {order:JSON.stringify(newOrder)},
		  dataType: 'json'
		});		
		}
		
		jQuery(document).ready(function() {
			jQuery('#CollectionSpace').sortable({
				items: ".CollectionPanelShell",
				handle: ".IconReorder",
				stop: function(event, ui) {
					var idsInOrder = jQuery('#CollectionSpace').sortable("toArray");
					ReorderResources(idsInOrder);
					}
			});
			jQuery('.CollectionPanelShell').disableSelection();
			
		});	
		
		
	</script>
<?php } ?>

<script type="text/javascript">
function ToggleThumbs()
	{
	document.getElementById("collectbody").style.paddingTop="400px";
	
	<?php if ($thumbs=="show") { ?>
	//document.getElementById("CollectionSpace").style.visibility="hidden";
	top.document.getElementById("topframe").rows="*<?php if ($collection_resize!=true) {?>,3<?php } ?>,33";
	<?php } else { ?>
	top.document.getElementById("topframe").rows="*<?php if ($collection_resize!=true) {?>,3<?php } ?>,<?php echo $collection_frame_height?>";
	<?php } ?>
	}
<?php if ($thumbs=="hide") { ?>
top.document.getElementById("topframe").rows="*<?php if ($collection_resize!=true) {?>,3<?php } ?>,33";
<?php } ?>
</script>

<?php if(!hook("clearmaincheckboxesfromcollectionframe")){ ?>
<?php if ($use_checkboxes_for_selection){ ?>
<!--clear checkboxes-->
<script type="text/javascript">
jQuery(".checkselect",parent.main.document).each(function(index, Element)
{jQuery(Element).attr('checked',false);});
</script>
<?php } ?>
<?php } #end hook clearmaincheckboxesfromcollectionframe?>
	
<style>
#CollectionMenuExp
	{
	height:<?php echo $collection_frame_height-15?>px;
	<?php if ($remove_collections_vertical_line){?>border-right: 0px;<?php }?>
	}
</style>

<?php hook("headblock");?>

<?php if ($collections_compact_style){ include ("../lib/js/colactions.js");}?>
</head>

<body class="CollectBack" id="collectbody"<?php if ($infobox) { ?> OnMouseMove="InfoBoxMM(event);"<?php } ?>>
<?php

$add=getvalescaped("add","");
if ($add!="")
	{
	hook("preaddtocollection");
	#add to current collection
	if (add_resource_to_collection($add,$usercollection,false,getvalescaped("size",""))==false)
		{ ?><script language="Javascript">alert("<?php echo $lang["cantmodifycollection"]?>");</script><?php };
	
   	# Log this
	daily_stat("Add resource to collection",$add);
	
	# Update resource/keyword kit count
	$search=getvalescaped("search","");
	if ((strpos($search,"!")===false) && ($search!="")) {update_resource_keyword_hitcount($add,$search);}
	hook("postaddtocollection");
	
	# Show warning?
	if (isset($collection_share_warning) && $collection_share_warning)
		{
		?><script language="Javascript">alert("<?php echo $lang["sharedcollectionaddwarning"]?>");</script><?php
		}
	}

$remove=getvalescaped("remove","");
if ($remove!="")
	{
	hook("preremovefromcollection");
	#remove from current collection
	if (remove_resource_from_collection($remove,$usercollection)==false)
		{ ?><script language="Javascript">alert("<?php echo $lang["cantmodifycollection"]?>");</script><?php };
	hook("postremovefromcollection");
	}
	
$addsearch=getvalescaped("addsearch",-1);
if ($addsearch!=-1)
	{
	hook("preaddsearch");
	if (getval("mode","")=="")
		{
		#add saved search
		add_saved_search($usercollection);
		
		# Log this
		daily_stat("Add saved search to collection",0);
		}
	else
		{
		#add saved search (the items themselves rather than just the query)
		add_saved_search_items($usercollection);
		
		# Log this
		daily_stat("Add saved search items to collection",0);
		}
	hook("postaddsearch");
	}

$removesearch=getvalescaped("removesearch","");
if ($removesearch!="")
	{
	hook("preremovesearch");
	#remove saved search
	remove_saved_search($usercollection,$removesearch);
	hook("postremovesearch");
	}
	
$addsmartcollection=getvalescaped("addsmartcollection",-1);
if ($addsmartcollection!=-1)
	{
	
	# add collection which autopopulates with a saved search 
	add_smart_collection();
		
	# Log this
	daily_stat("Added smart collection",0);	
	}
	
$research=getvalescaped("research","");
if ($research!="")
	{
	hook("preresearch");
	$col=get_research_request_collection($research);
	if ($col==false)
		{
		$rr=get_research_request($research);
		$name=$lang["research"] . ": " . $rr["name"];
		$new=create_collection ($rr["user"],$name,1);
		set_user_collection($userref,$new);
		set_research_collection($research,$new);
		}
	else
		{
		set_user_collection($userref,$col);
		}
	hook("postresearch");
	}
	
hook("processusercommand");
?>


<?php 
$searches=get_saved_searches($usercollection);
$result=do_search("!collection" . $usercollection);
$count_result=count($result);
$hook_count=hook("countresult","",array($usercollection,$count_result));if (is_numeric($hook_count)) {$count_result=$hook_count;} # Allow count display to be overridden by a plugin (e.g. that adds it's own resources from elsewhere e.g. ResourceConnect).
$feedback=$cinfo["request_feedback"];



# E-commerce functionality. Work out total price, if $basket_stores_size is enabled so that they've already selected a suitable size.
$totalprice=0;
if (($userrequestmode==2 || $userrequestmode==3) && $basket_stores_size)
	{
	foreach ($result as $resource)
		{
		# For each resource in the collection, fetch the price (set in config.php, or config override for group specific pricing)
		$id=$resource["purchase_size"];
		if ($id=="") {$id="hpr";} # Treat original size as "hpr".
		if (array_key_exists($id,$pricing))
			{
			$price=$pricing[$id];
			
			# Pricing adjustment hook (for discounts or other price adjustments plugin).
			$priceadjust=hook("adjust_item_price","",array($price,$resource["ref"],$resource["purchase_size"]));
			if ($priceadjust!==false)
				{
				$price=$priceadjust;
				}
			
			$totalprice+=$price;
			}
		else
			{
			$totalprice+=999; # Error.
			}
		}
	}


if(!hook("updatemaincheckboxesfromcollectionframe")){
	if ($use_checkboxes_for_selection){	
		# update checkboxes in main window
		for ($n=0;$n<count($result);$n++)			
			{
			$ref=$result[$n]["ref"];
			?>
			<script type="text/javascript">
			if (jQuery('#check<?php echo $ref?>',parent.main.document)){
				jQuery('#check<?php echo $ref?>',parent.main.document).attr('checked',true);}
			</script>
		<?php
		}
	}
} # end hook updatemaincheckboxesfromcollectionframe


if ($thumbs=="show") { 

# Too many to show?
if ($count_result>$max_collection_thumbs && $k=="")
	{
	?>
	<script type="text/javascript">
	alert("<?php echo $lang["maxcollectionthumbsreached"]?>");
	window.setTimeout("ToggleThumbs();document.location='collections.php?thumbs=hide';",1000);
	</script>
	<?php
	$result=array(); # Empty the result set so nothing is drawn; the window will be resized shortly anyway.
	}

# ---------------------------- Maximised view -------------------------------------------------------------------------
if ($basket)
	{
	# ------------------------ Basket Mode ----------------------------------------
	?>
	<div id="CollectionMenu">
	<h2><?php echo $lang["yourbasket"] ?></h2>
	<form target="main" action="purchase.php">

	<?php if ($count_result==0) { ?>
	<p><br /><?php echo $lang["yourbasketisempty"] ?></p><br /><br /><br />
	<?php } else { ?>
	<p><br /><?php if ($count_result==1) {echo $lang["yourbasketcontains-1"];} else {echo str_replace("%qty",$count_result,$lang["yourbasketcontains-2"]);} ?>

	<?php if ($basket_stores_size) {
	# If they have already selected the size, we can show a total price here.
	?><br/><?php echo $lang["totalprice"] ?>: <?php echo $currency_symbol . " " . number_format($totalprice,2) ?><?php } ?>
	
	</p>

	<p style="padding-bottom:10px;"><input type="submit" name="buy" value="&nbsp;&nbsp;&nbsp;<?php echo $lang["buynow"] ?>&nbsp;&nbsp;&nbsp;" /></p>
	<?php } ?>
	<?php if (!$disable_collection_toggle) { ?>
    <a href="collections.php?thumbs=hide&collection=<?php echo $usercollection ?>&k=<?php echo $k?>" onClick="ToggleThumbs();">&gt; <?php echo $lang["hidethumbnails"]?></a>
  <?php } ?>


	</form>
	</div>
	<?php	
	}
elseif ($k!="")
	{
	# ------------- Anonymous access, slightly different display ------------------
	$tempcol=$cinfo;
	?>
<div id="CollectionMenu">
  <h2><?php echo $tempcol["name"]?></h2>
	<br />
	<?php echo $lang["created"] . " " . nicedate($tempcol["created"])?><br />
  	<?php echo $count_result . " " . $lang["youfoundresources"]?><br />
    <?php if ((isset($zipcommand) || $collection_download) && $count_result>0) { ?>
	<a href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>" target="main">&gt;&nbsp;<?php echo $lang["action-download"]?></a>
	<?php } ?>
    <?php if ($feedback) {?><br /><br /><a target="main" href="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["sendfeedback"]?></a><?php } ?>
    <?php if ($count_result>0 && checkperm("q"))
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($result);
		if ($min_access!=0)
			{
		    ?>
		    <br/><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>&k=<?php echo $k?>">&gt; <?php echo 	$lang["requestall"]?></a>
		    <?php
		    }
	    }
	?>
	<?php if (!$disable_collection_toggle) { ?>
    <br/><a href="collections.php?thumbs=hide&collection=<?php echo $usercollection ?>&k=<?php echo $k?>" onClick="ToggleThumbs();">&gt; <?php echo $lang["hidethumbnails"]?></a>
  <?php } ?>
</div>
<?php 
} else { 
# -------------------------- Standard display --------------------------------------------
?>
<?php if ($collection_dropdown_user_access_mode){?>
<div id="CollectionMenuExp">
<?php } else { ?>
<div id="CollectionMenu">
<?php } ?>

<?php if (!hook("thumbsmenu")) { ?>
  <?php if (!hook("replacecollectiontitle")) { ?><h2 id="CollectionsPanelHeader"><?php if ($collections_compact_style){?><a href="collection_manage.php" target="main"><?php } ?><?php echo $lang["mycollections"]?><?php if ($collections_compact_style){?></a><?php } ?></h2><?php } ?>
  <form method="get" id="colselect">
		<div class="SearchItem" style="padding:0;margin:0;"><?php echo $lang["currentcollection"]?>&nbsp;(<strong><?php echo $count_result?></strong>&nbsp;<?php if ($count_result==1){echo $lang["item"];} else {echo $lang["items"];}?>): 
		<select name="collection" id="collection" onchange="if(document.getElementById('collection').value==-1){document.getElementById('entername').style.display='block';document.getElementById('entername').focus();return false;} document.getElementById('colselect').submit();"<?php if ($collection_dropdown_user_access_mode){?>class="SearchWidthExp"<?php } else { ?> class="SearchWidth"<?php } ?>>
		<?php
		$list=get_user_collections($userref);
		$found=false;
		for ($n=0;$n<count($list);$n++)
			{

            if ($collection_dropdown_user_access_mode){    
                $colusername=$list[$n]['fullname'];
                
                # Work out the correct access mode to display
                if (!hook('collectionaccessmode')) {
                    if ($list[$n]["public"]==0){
                        $accessmode= $lang["private"];
                    }
                    else{
                        if (strlen($list[$n]["theme"])>0){
                            $accessmode= $lang["theme"];
                        }
                    else{
                            $accessmode= $lang["public"];
                        }
                    }
                }
            }
                
			if (!isset($list[$n]['savedsearch'])||(isset($list[$n]['savedsearch'])&&$list[$n]['savedsearch']==null)){ $collection_tag='';} else {$collection_tag=$lang['smartcollection'].": ";}
		
			#show only active collections if a start date is set for $active_collections 
			if (strtotime($list[$n]['created']) > ((isset($active_collections))?strtotime($active_collections):1))
					{ ?>
			<option value="<?php echo $list[$n]["ref"]?>" <?php if ($usercollection==$list[$n]["ref"]) {?> 	selected<?php $found=true;} ?>><?php echo $collection_tag.htmlspecialchars(i18n_get_translated($list[$n]["name"]))?> <?php if ($collection_dropdown_user_access_mode){echo "(". $colusername."/".$accessmode.")"; } ?></option>
			<?php }
			}
		if ($found==false)
			{
			# Add this one at the end, it can't be found
			$notfound=$cinfo;
			if ($notfound!==false)
				{
				?>
				<option selected><?php echo $notfound["name"]?></option>
				<?php
				}
			}
		?>
		<option value="-1">(<?php echo $lang["createnewcollection"]?>)</option>
		</select>
		<input type=text id="entername" name="entername" style="display:none;" class="SearchWidth" onUnfocus="document.getElementById('colselect').submit();">
		</div>			
  </form>

  <ul>
  <?php if ($collections_compact_style){
    include("collections_compact_style.php");
    }
    else { ?>
  	<?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
    <?php if (checkperm("s")) { ?><li><a href="collection_manage.php" target="main">&gt; <?php echo $lang["managemycollections"];?></a></li>
	<?php if ($contact_sheet==true && $collections_compact_style) { ?><li><a href="contactsheet_settings.php?ref=<?php echo $usercollection?>" target="main">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a></li><?php } ?>
    <?php if ($allow_share) { ?><li><a href="collection_share.php?ref=<?php echo $usercollection?>" target="main">&gt; <?php echo $lang["share"]?></a></li><?php } ?>
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a target="main" href="collection_edit.php?ref=<?php echo $usercollection?>">&gt;&nbsp;<?php echo $allow_share?$lang["action-edit"]:$lang["editcollection"]?></a></li><?php } ?>
    <?php if ((($userref==$cinfo["user"]) || (checkperm("h"))) && $collection_sorting) {?><li><a target="main" href="collection_sort.php?collection=<?php echo $usercollection?>">&gt;&nbsp;<?php echo $lang["sort"]?></a></li><?php } ?>
	<?php if ($preview_all){?><li><a href="preview_all.php?ref=<?php echo $usercollection?>" target="main">&gt;&nbsp;<?php echo $lang["preview_all"]?></a></li><?php } ?>
	<?php hook("collectiontool2");?>
    <?php if ($feedback) {?><li><a target="main" href="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["sendfeedback"]?></a></li><?php } ?>
    
    <?php } ?>
    <?php } else {
	if (!hook("replacecollectionsresearchlinks")){	
    $research=sql_value("select ref value from research_request where collection='$usercollection'",0);	
	?>
    <li><a href="team/team_research.php" target="main">&gt; <?php echo $lang["manageresearchrequests"]?></a></li>    
    <li><a href="team/team_research_edit.php?ref=<?php echo $research?>" target="main">&gt; <?php echo $lang["editresearchrequests"]?></a></li>    
    <?php } /* end hook replacecollectionsresearchlinks */ ?>
	<?php } ?>
    
    <?php 
    # If this collection is (fully) editable, then display an extra edit all link
    if ((count($result)>0) && checkperm("e" . $result[0]["archive"]) && allow_multi_edit($result)) { ?>
    <li class="clearerleft"><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main">&gt; <?php echo $lang["viewall"]?></a></li>
    <li><a href="edit.php?collection=<?php echo $usercollection?>" target="main">&gt; <?php echo $lang["action-editall"]?></a></li>

    <?php } else { ?>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main">&gt; <?php echo $lang["viewall"]?></a></li>
    <?php } ?>
    
    <?php if ($count_result>0)
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($result);
		if ($min_access!=0)
			{
		    ?>
		    <li><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>&k=<?php echo $k?>">&gt; <?php echo 	$lang["requestall"]?></a></li>
		    <?php
		    }
	    }
	?>
    
    <?php if ((isset($zipcommand) || $collection_download) && $count_result>0) { ?>
    <li><a target="main" href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>">&gt; <?php echo $lang["action-download"]?></a></li>
	<?php } ?>
	<?php hook("collectiontool");?>
	<?php if (!$disable_collection_toggle) { ?>
    <li><a href="collections.php?thumbs=hide" onClick="ToggleThumbs();">&gt; <?php echo $lang["hidethumbnails"]?></a></li>
  <?php } ?>
<?php } /* end compact collections */?>
</ul>
<?php } ?>
</div>

<?php } ?>

<!--Resource panels-->
<?php if ($collection_dropdown_user_access_mode){?>
<div id="CollectionSpaceExp">
<?php } else { ?>
<div id="CollectionSpace">
<?php } ?>

<?php 
# Loop through saved searches
if (isset($cinfo['savedsearch'])&&$cinfo['savedsearch']==null)
	{ // don't include saved search item in result if this is a smart collection  

	# Setting the save search icon
	$folder = "../gfx/images/";
	$iconpath = $folder . "save-search" . "_" . $language . ".gif";
	if (!file_exists($iconpath))
		{
		# A language specific icon is not found, use the default icon
		$iconpath = $folder . "save-search.gif";
		}

	for ($n=0;$n<count($searches);$n++)			
		{
		$ref=$searches[$n]["ref"];
		$url="search.php?search=" . urlencode($searches[$n]["search"]) . "&restypes=" . urlencode($searches[$n]["restypes"]) . "&archive=" . $searches[$n]["archive"];
		?>
		<!--Resource Panel-->
		<div class="CollectionPanelShell">
		<table border="0" class="CollectionResourceAlign"><tr><td>
		<a target="main" href="<?php echo $url?>"><img border=0 width=56 height=75 src="<?php echo $iconpath?>"/></a></td>
		</tr></table>
		<div class="CollectionPanelInfo"><a target="main" href="<?php echo $url?>"><?php echo tidy_trim($lang["savedsearch"],(13-strlen($n+1)))?> <?php echo $n+1?></a>&nbsp;</div>
		<div class="CollectionPanelInfo"><a href="collections.php?removesearch=<?php echo $ref?>&nc=<?php echo time()?>">x <?php echo $lang["action-remove"]?>
		</a></div>				
		</div>
		<?php		
		}
}		

# Loop through thumbnails
if ($count_result>0) 
	{
	# loop and display the results
	for ($n=0;$n<count($result);$n++)			
		{
		$ref=$result[$n]["ref"];
		?>
<?php if (!hook("resourceview")) { ?>
		<!--Resource Panel-->
		<div class="CollectionPanelShell" id="ResourceShell<?php echo $ref?>">
		<?php if (!hook("rendercollectionthumb")){?>
		<?php $access=get_resource_access($result[$n]);
		$use_watermark=check_use_watermark();?>
		<table border="0" class="CollectionResourceAlign"><tr><td>
		<a target="main" href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo $k?>"><?php if ($result[$n]["has_image"]==1) { 
		
		$colimgpath=get_resource_path($ref,false,"col",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"])
		?>
		<img border=0 src="<?php echo $colimgpath?>" class="CollectImageBorder" <?php if (!$infobox) { ?>title="<?php echo htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field]))?>" alt="<?php echo htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field]))?>"<?php } ?> 
		<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
		/>
			<?php
		
		} else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true) ?>"
		<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
		/><?php } ?></a></td>
		</tr></table>
		<?php } /* end hook rendercollectionthumb */?>
		
		<?php 

		$title=$result[$n]["field".$view_title_field];	
		if (isset($metadata_template_title_field) && isset($metadata_template_resource_type))
			{
			if ($result[$n]['resource_type']==$metadata_template_resource_type)
				{
				$title=$result[$n]["field".$metadata_template_title_field];
				}	
			}	
			
		?>	
		<?php if (!hook("replacecolresourcetitle")){?>
		<div class="CollectionPanelInfo"><a target="main" href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo $k?>" <?php if (!$infobox) { ?>title="<?php echo htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field]))?>"<?php } ?> ><?php echo tidy_trim(i18n_get_translated($title),14);?></a>&nbsp;</div>
		<?php } ?>
		
		<?php if ($k!="" && $feedback) { # Allow feedback for external access key users
		?>
		<div class="CollectionPanelInfo">
		<span class="IconComment <?php if ($result[$n]["commentset"]>0) { ?>IconCommentAnim<?php } ?>"><a target="main" href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo $usercollection?>&k=<?php echo $k?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>		
		</div>
		<?php } ?>
	
		<?php if ($k=="") { ?><div class="CollectionPanelInfo">
		<?php if (($feedback) || (($collection_reorder_caption || $collection_commenting) && $allow_reorder)) { ?>
		<span class="IconComment <?php if ($result[$n]["commentset"]>0) { ?>IconCommentAnim<?php } ?>"><a target="main" href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo $usercollection?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>		
		<?php } ?>

		<?php if ($collection_reorder_caption  && $allow_reorder) { ?>
		<div class="IconReorder" onMouseDown="InfoBoxWaiting=false;"> </div>
		<?php if (!hook("replaceremovelink")){?>
		<span class="IconRemove"><a href="collections.php?remove=<?php echo $ref?>&nc=<?php echo time()?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>
		<?php } //end hook replaceremovelink ?>
		<?php } else { 
			if (!isset($cinfo['savedsearch'])||(isset($cinfo['savedsearch'])&&$cinfo['savedsearch']==null)){ // add 'remove' link only if this is not a smart collection 
			?>
		<?php if (!hook("replaceremovelink")){?>
		<a href="collections.php?remove=<?php echo $ref?>&nc=<?php echo time()?>">x <?php echo $lang["action-remove"]?></a>
		<?php } //end hook replaceremovelink ?>
			<?php } ?>
		<?php } ?>
		</div><?php } ?>
		</div>
<?php } ?>
		<?php
		}
	}

	# Plugin for additional collection listings	(deprecated)
	if (file_exists("plugins/collection_listing.php")) {include "plugins/collection_listing.php";}

	hook("thumblistextra");
	?>
	</div>
	<?php

# Add the infobox.
?>
<div id="InfoBoxCollection"><div id="InfoBoxCollectionInner"> </div></div>
<?php
}
else
{
# ------------------------- Minimised view
?>
<!--Title-->	
<?php if (!hook("nothumbs")) {

if ($basket)
	{
	# ------------------------ Basket Mode ----------------------------------------
	?>
	<div id="CollectionMinTitle"><h2><?php echo $lang["yourbasket"] ?></h2></div>
	<div id="CollectionMinRightNav">
	<form target="main" action="purchase.php">
	<ul>
	
	<?php if ($count_result==0) { ?>
	<li><?php echo $lang["yourbasketisempty"] ?></li>
	<?php } else { ?>

	<?php if ($basket_stores_size) {
	# If they have already selected the size, we can show a total price here.
	?><li><?php echo $lang["totalprice"] ?>: <?php echo $currency_symbol . " " . number_format($totalprice,2) ?><?php } ?></li>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main"><?php echo $lang["viewall"]?></a></li>
	<li><input type="submit" name="buy" value="&nbsp;&nbsp;&nbsp;<?php echo $lang["buynow"] ?>&nbsp;&nbsp;&nbsp;" /></li>
	<?php } ?>
  <?php if (!$disable_collection_toggle) { ?>
    <?php if ($count_result<=$max_collection_thumbs) { ?><li><a href="collections.php?thumbs=show&collection=<?php echo $usercollection ?>&k=<?php echo $k?>" onClick="ToggleThumbs();"><?php echo $lang["showthumbnails"]?></a></li><?php } ?>
  <?php } ?>
    </ul>
	</form>

	</div>
	<?php	
	}
elseif ($k!="")
	{
	# Anonymous access, slightly different display
	$tempcol=$cinfo;
	?>
<div id="CollectionMinTitle"><h2><?php echo $tempcol["name"]?></h2></div>
<div id="CollectionMinRightNav">
    <?php if ((isset($zipcommand) || $collection_download) && $count_result>0) { ?>
	<li><a href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>" target="main"><?php echo $lang["action-download"]?></a></li>
	<?php } ?>
    <?php if ($feedback) {?><li><a target="main" href="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>"><?php echo $lang["sendfeedback"]?></a></li><?php } ?>
   	<?php if ($count_result>0)
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($result);
		if ($min_access!=0)
			{
		    ?>
		    <li><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>&k=<?php echo $k?>"><?php echo 	$lang["requestall"]?></a></li>
		    <?php
		    }
	    }
	?>
  <?php if (!$disable_collection_toggle) { ?>
   	<li><a href="collections.php?thumbs=show&collection=<?php echo $usercollection?>&k=<?php echo $k?>" onClick="ToggleThumbs();"><?php echo $lang["showthumbnails"]?></li>
  <?php } ?>
</div>
<?php 
} else { 
?>

<div id="CollectionMinTitle"><?php if (!hook("replacecollectiontitle")) { ?><h2><?php if ($collections_compact_style){?><a href="collection_manage.php" target="main"><?php } ?><?php echo $lang["mycollections"]?><?php if ($collections_compact_style){?></a><?php }?></h2><?php } ?></div>

<!--Menu-->	
<div id="CollectionMinRightNav"><div id="MinSearchItem">
  <?php if ($collections_compact_style){
    include("collections_compact_style.php");
    }
    else { ?>
    <ul>
    <?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
    <?php if (checkperm("s")) { ?><?php if (!$collections_compact_style){?><li><a href="collection_manage.php" target="main"><?php echo $lang["managemycollections"]?></a></li><?php } ?>
    <?php if ($contact_sheet==true && $collections_compact_style) { ?>
    <li><a href="contactsheet_settings.php?ref=<?php echo $usercollection?>" target="main">&nbsp;<?php echo $lang["contactsheet"]?></a></li>
	<?php } ?>
	<?php if ($allow_share) { ?><li><a href="collection_share.php?ref=<?php echo $usercollection?>" target="main"><?php echo $lang["share"]?></a></li><?php } ?>
    
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a target="main" href="collection_edit.php?ref=<?php echo $usercollection?>">&nbsp;<?php echo $allow_share?$lang["action-edit"]:$lang["editcollection"]?></a></li><?php } ?>
    <?php if ((($userref==$cinfo["user"]) || (checkperm("h"))) && $collection_sorting) {?><li><a target="main" href="collection_sort.php?collection=<?php echo $usercollection?>">&nbsp;<?php echo $lang["sort"]?></a></li><?php } ?>

	<?php if ($preview_all){?><li><a href="preview_all.php?ref=<?php echo $usercollection?>" target="main"><?php echo $lang["preview_all"]?></a></li><?php } ?>
    <?php hook('collectiontool2min');?>
    <?php if ($feedback) {?><li><a target="main" href="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>">&nbsp;<?php echo $lang["sendfeedback"]?></a></li><?php } ?>
    
    <?php } ?>
    <?php } else {
	if (!hook("replacecollectionsresearchlinks")){	
    $research=sql_value("select ref value from research_request where collection='$usercollection'",0);	
	?>
    <li><a href="team/team_research.php" target="main"><?php echo $lang["manageresearchrequests"]?></a></li>   
    <li><a href="team/team_research_edit.php?ref=<?php echo $research?>" target="main"><?php echo $lang["editresearchrequests"]?></a></li>         
    <?php } /* end hook replacecollectionsresearchlinks */ ?>	
	<?php } ?>
    <?php 
    # If this collection is (fully) editable, then display an extra edit all link
    if ((count($result)>0) && checkperm("e" . $result[0]["archive"]) && allow_multi_edit($result)) { ?>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main"><?php echo $lang["viewall"]?></a></li>
    <li><a href="edit.php?collection=<?php echo $usercollection?>" target="main"><?php echo $lang["action-editall"]?></a></li>    
    <?php } else { ?>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main"><?php echo $lang["viewall"]?></a></li>
    <?php } ?>
    <?php if ((isset($zipcommand) || $collection_download) && $count_result>0) { ?>
    <li><a target="main" href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>"><?php echo $lang["action-download"]?></a></li>
	<?php } ?>
    <?php if ($count_result>0 && $k=="" && checkperm("q"))
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($result);
		if ($min_access!=0)
			{
		    ?>
		    <li><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>"><?php echo 	$lang["action-request"]?></a></li>
		    <?php
		    }
	    }
	?>
	<?php hook("collectiontoolmin");?>
    <?php if (($count_result<=$max_collection_thumbs) && !$disable_collection_toggle) { ?><li><a href="collections.php?thumbs=show" onClick="ToggleThumbs();"><?php echo $lang["showthumbnails"]?></a></li><?php } ?>
    
  </ul>
  <?php } ?>
</div>
</div>
<!--Collection Dropdown-->	
<div id="CollectionMinDropTitle"><?php echo $lang["currentcollection"]?>:&nbsp;</div>				
<div id="CollectionMinDrop">
<form id="colselect" method="get">
		<div class="MinSearchItem">
		<select name="collection" id="collection" <?php if ($collection_dropdown_user_access_mode){?>class="SearchWidthExp"<?php } else { ?> class="SearchWidth"<?php } ?> onchange="if(document.getElementById('collection').value==-1){document.getElementById('entername').style.display='inline';document.getElementById('entername').focus();return false;} document.getElementById('colselect').submit();">
		<?php
		$found=false;
		$list=get_user_collections($userref);
		for ($n=0;$n<count($list);$n++)
			{
            if ($collection_dropdown_user_access_mode){    
                $colusername=$list[$n]['fullname'];
                # Work out the correct access mode to display
                if (!hook('collectionaccessmode')) {
                    if ($list[$n]["public"]==0){
                        $accessmode= $lang["private"];
                    }
                    else{
                        if (strlen($list[$n]["theme"])>0){
                            $accessmode= $lang["theme"];
                        }
                    else{
                            $accessmode= $lang["public"];
                        }
                    }
                }
            }
             
		    if (!isset($list[$n]['savedsearch'])||(isset($list[$n]['savedsearch'])&&$list[$n]['savedsearch']==null)){ $collection_tag='';} else {$collection_tag=$lang['smartcollection'].": ";}
			#show only active collections if a start date is set for $active_collections 
			if (strtotime($list[$n]['created']) > ((isset($active_collections))?strtotime($active_collections):1))	
			{ ?>
			<option value="<?php echo $list[$n]["ref"]?>" <?php if ($usercollection==$list[$n]["ref"]) {?> selected<?php $found=true;}?>><?php echo $collection_tag.htmlspecialchars($list[$n]["name"])?> <?php if ($collection_dropdown_user_access_mode){echo "(". $colusername."/".$accessmode.")"; } ?></option>
			<?php }
			}
		if ($found==false)
			{
			# Add this one at the end, it can't be found
			$notfound=$cinfo;
			if ($notfound!==false)
				{
				?>
				<option selected><?php echo $notfound["name"]?></option>
				<?php
				}
			}
		?>
		<option value="-1">(<?php echo $lang["createnewcollection"]?>)</option>
		</select>
		<input type=text id="entername" name="entername" style="display:inline;display:none;" class="SearchWidth" onUnfocus="document.getElementById('colselect').submit();">
		</div>				
  </form>
</div>
<?php } ?>
<?php } ?>
<!--Collection Count-->	
<div id="CollectionMinitems"><strong><?php echo $count_result?></strong>&nbsp;<?php if ($count_result==1){echo $lang["item"];} else {echo $lang["items"];}?></div>		
<?php } ?>

<?php draw_performance_footer();?>


</body>
</html>
