<?php
include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection","",true),$k))) {include "../include/authenticate.php";}
include "../include/research_functions.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

# Disable info box for external access.
if ($k!="") {$infobox=false;} 
# Disable checkboxes for external users.
if ($k!="") {$use_checkboxes_for_selection=false;}

# Hide/show thumbs - set cookie must be before header is sent
$thumbs=getval("thumbs",$thumbs_default);
setcookie("thumbs",$thumbs,0);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html class="CollectBack">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $applicationname?></title>
<link href="../css/global.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
<?php if (!hook("adjustcolortheme")){?>
<link href="../css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu")?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss"/>
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
		<link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/style.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
		<?php
		}
	}
?>

<?php
$collection=getvalescaped("collection","",true);
$entername=getvalescaped("entername","");

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

# Load collection info.
$cinfo=get_collection($usercollection);

# Check to see if the user can edit this collection.
$allow_reorder=false;
if (($k=="") && (($userref==$cinfo["user"]) || ($cinfo["allow_changes"]==1) || (checkperm("h"))))
	{
	$allow_reorder=true;
	}

# Include function for reordering / infobox
if ($allow_reorder || $infobox)
	{
	?>
	<script src="../lib/js/prototype.js" type="text/javascript"></script>
	<script src="../lib/js/scriptaculous.js" type="text/javascript"></script>
	<script src="../lib/js/infobox_collection.js" type="text/javascript"></script>
	<script type="text/javascript">
	function ReorderResources(id1,id2)
		{
		document.location='collections.php?reorder=' + id1 + '-' + id2;
		}
	</script>
	<?php
	
	# Also check for the parameter and reorder as necessary.
	$reorder=getvalescaped("reorder","");
	if ($reorder!="")
		{
		$r=explode("-",$reorder);
		swap_collection_order(substr($r[0],13),$r[1],$usercollection);
		}
	}

?>
<script type="text/javascript">
function ToggleThumbs()
	{
	document.getElementById("collectbody").style.paddingTop="400px";
	
	<?php if ($thumbs=="show") { ?>
	document.getElementById("CollectionSpace").style.visibility="hidden";
	top.document.getElementById("topframe").rows="*<?php if ($collection_resize!=true) {?>,3<?php } ?>,33";
	<?php } else { ?>
	top.document.getElementById("topframe").rows="*<?php if ($collection_resize!=true) {?>,3<?php } ?>,138";
	<?php } ?>
	}
<?php if ($thumbs=="hide") { ?>
top.document.getElementById("topframe").rows="*<?php if ($collection_resize!=true) {?>,3<?php } ?>,33";
<?php } ?>
</script>

<?php if(!hook("clearmaincheckboxesfromcollectionframe")){?>
<?php if ($use_checkboxes_for_selection){?>
<!--clear checkboxes-->
<script type="text/javascript">
var checkboxes=parent.main.$$('input.checkselect');
checkboxes.each(function(box)
{box.checked=false;});
</script>
<?php } ?>
<?php } #end hook clearmaincheckboxesfromcollectionframe?>

</head>

<body class="CollectBack" id="collectbody"<?php if ($infobox) { ?> OnMouseMove="InfoBoxMM(event);"<?php } ?>>
<?php

$add=getvalescaped("add","");
if ($add!="")
	{
	hook("preaddtocollection");
	#add to current collection
	if (add_resource_to_collection($add,$usercollection)==false)
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
	
$research=getvalescaped("research","");
if ($research!="")
	{
	hook("preresearch");
	$col=get_research_request_collection($research);
	if ($col==false)
		{
		$rr=get_research_request($research);
		$new=create_collection ($rr["user"],"Request: " . $rr["name"],1);
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
$cinfo=get_collection($usercollection);
$feedback=$cinfo["request_feedback"];

if(!hook("updatemaincheckboxesfromcollectionframe")){
	if ($use_checkboxes_for_selection){	
		# update checkboxes in main window
		for ($n=0;$n<count($result);$n++)			
			{
			$ref=$result[$n]["ref"];
			?>
			<script type="text/javascript">
			if (parent.main.$('check<?php echo $ref?>')!=null){parent.main.$('check<?php echo $ref?>').checked=true;}
			</script>
		<?php
		}
	}
} # end hook updatemaincheckboxesfromcollectionframe


if ($thumbs=="show") { 

# Too many to show?
if (count($result)>$max_collection_thumbs && $k=="")
	{
	?>
	<script type="text/javascript">
	alert("<?php echo $lang["maxcollectionthumbsreached"]?>");
	window.setTimeout("ToggleThumbs();document.location='collections.php?thumbs=hide';",1000);
	</script>
	<?php
	$result=array(); # Empty the result set so nothing is drawn; the window will be resized shortly anyway.
	}

# ---------------------------- Maximised view
if ($k!="")
	{
	# Anonymous access, slightly different display
	$tempcol=get_collection($usercollection);
	?>
<div id="CollectionMenu">
  <h2><?php echo $tempcol["name"]?></h2>
	<br />
	<?php echo $lang["created"] . " " . nicedate($tempcol["created"])?><br />
  	<?php echo count($result) . " " . $lang["youfoundresources"]?><br />
  	<?php if (isset($zipcommand)) { ?>
	<a href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>" target="main">&gt;&nbsp;<?php echo $lang["action-download"]?></a>
	<?php } ?>
    <?php if ($feedback) {?><br /><br /><a target="main" href="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["sendfeedback"]?></a><?php } ?>
    <?php if (count($result)>0 && checkperm("q"))
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($usercollection);
		if ($min_access!=0)
			{
		    ?>
		    <br/><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>&k=<?php echo $k?>">&gt; <?php echo 	$lang["requestall"]?></a>
		    <?php
		    }
	    }
	?>
    <br/><a href="collections.php?thumbs=hide&collection=<?php echo $usercollection ?>&k=<?php echo $k?>" onClick="ToggleThumbs();">&gt; <?php echo $lang["hidethumbnails"]?></a>
</div>
<?php 
} else { 
?>
<div id="CollectionMenu">
<?php if (!hook("thumbsmenu")) { ?>
  <h2><?php echo $lang["mycollections"]?></h2>
  <form method="get" id="colselect">
		<div class="SearchItem" style="padding:0;margin:0;"><?php echo $lang["currentcollection"]?>:
		<select name="collection" onchange="if($(this).value==-1){$('entername').toggle();$('entername').focus();return false;} document.getElementById('colselect').submit();" class="SearchWidth">
		<?php
		$list=get_user_collections($userref);
		$found=false;
		for ($n=0;$n<count($list);$n++)
			{
			#show only active collections if a start date is set for $active_collections 
			if (strtotime($list[$n]['created']) > ((isset($active_collections))?strtotime($active_collections):1))
					{ ?>
				<option value="<?php echo $list[$n]["ref"]?>" <?php if ($usercollection==$list[$n]["ref"]) {?> 	selected<?php $found=true;} ?>><?php echo htmlspecialchars($list[$n]["name"])?></option>
			<?php }
			}
		if ($found==false)
			{
			# Add this one at the end, it can't be found
			$notfound=get_collection($usercollection);
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
		<input type=text id="entername" name="entername" style="display:none;" class="SearchWidth" onUnfocus="$(this).submit();">
		</div>			
  </form>

  <ul>
  	<?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
    <?php if (checkperm("s")) { ?><li><a href="collection_manage.php" target="main">&gt; <?php echo $lang["managemycollections"]?></a></li>
    <?php if ($allow_share) { ?><li><a href="collection_share.php?ref=<?php echo $usercollection?>" target="main">&gt; <?php echo $lang["share"]?></a></li><?php } ?>
    
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a target="main" href="collection_edit.php?ref=<?php echo $usercollection?>">&gt;&nbsp;<?php echo $allow_share?$lang["edit"]:$lang["editcollection"]?></a></li><?php } ?>

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
    if ((count($result)>0) && checkperm("e" . $result[0]["archive"]) && allow_multi_edit($usercollection)) { ?>
    <li class="clearerleft"><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main">&gt; <?php echo $lang["viewall"]?></a></li>
    <li><a href="edit.php?collection=<?php echo $usercollection?>" target="main">&gt; <?php echo $lang["editall"]?></a></li>

    <?php } else { ?>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main">&gt; <?php echo $lang["viewall"]?></a></li>
    <?php } ?>
    
    <?php if (count($result)>0)
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($usercollection);
		if ($min_access!=0)
			{
		    ?>
		    <li><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>&k=<?php echo $k?>">&gt; <?php echo 	$lang["requestall"]?></a></li>
		    <?php
		    }
	    }
	?>
    
   	<?php if (isset($zipcommand)) { ?>
    <li><a target="main" href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>">&gt; <?php echo $lang["zipall"]?></a></li>
	<?php } ?>
    <li><a href="collections.php?thumbs=hide" onClick="ToggleThumbs();">&gt; <?php echo $lang["hidethumbnails"]?></a></li>
  </ul>
<?php } ?>
</div>

<?php } ?>

<!--Resource panels-->
<div id="CollectionSpace">

<?php
# Loop through saved searches
for ($n=0;$n<count($searches);$n++)			
		{
		$ref=$searches[$n]["ref"];
		$url="search.php?search=" . urlencode($searches[$n]["search"]) . "&restypes=" . urlencode($searches[$n]["restypes"]) . "&archive=" . $searches[$n]["archive"];
		?>
		<!--Resource Panel-->
		<div class="CollectionPanelShell">
		<table border="0" class="CollectionResourceAlign"><tr><td>
		<a target="main" href="<?php echo $url?>"><img border=0 width=56 height=75 src="../gfx/images/save-search.gif"/></a></td>
		</tr></table>
		<div class="CollectionPanelInfo"><a target="main" href="<?php echo $url?>"><?php echo $lang["savedsearch"]?> <?php echo $n+1?></a>&nbsp;</div>
		<div class="CollectionPanelInfo"><a href="collections.php?removesearch=<?php echo $ref?>&nc=<?php echo time()?>">x <?php echo $lang["action-remove"]?>
</a></div>				
		</div>
		<?php		
		}

# Loop through thumbnails
if (count($result)>0) 
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
		<table border="0" class="CollectionResourceAlign"><tr><td>
		<a target="main" href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo $k?>"><?php if ($result[$n]["has_image"]==1) { 
		
		$colimgpath=get_resource_path($ref,false,"col",false,$result[$n]["preview_extension"],-1,1,false,$result[$n]["file_modified"])
		?>
		<img border=0 src="<?php echo $colimgpath?>" class="CollectImageBorder"
		<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
		/>
			<?php
		
		} else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true) ?>"
		<?php if ($infobox) { ?>onMouseOver="InfoBoxSetResource(<?php echo $ref?>);" onMouseOut="InfoBoxSetResource(0);"<?php } ?>
		/><?php } ?></a></td>
		</tr></table>
		<?php } /* end hook rendercollectionthumb */?>
		
		<?php 
		if ($use_resource_column_data){$title=$result[$n]["title"];}
		if (!$use_resource_column_data)
			{
			$title=get_data_by_field($result[$n]['ref'],$view_title_field);	
			if (isset($metadata_template_title_field) && isset($metadata_template_resource_type))
				{
				if ($result[$n]['resource_type']==$metadata_template_resource_type)
					{
					$title=get_data_by_field($ref,$metadata_template_title_field);
					}	
				}	
			}	
		?>	
		
		<div class="CollectionPanelInfo"><a target="main" href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo $k?>"><?php echo tidy_trim(i18n_get_translated($title),14);?></a>&nbsp;</div>
	
		<?php if ($k!="" && $feedback) { # Allow feedback for external access key users
		?>
		<div class="CollectionPanelInfo">
		<span class="IconComment <?php if ($result[$n]["commentset"]>0) { ?>IconCommentAnim<?php } ?>"><a target="main" href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo $usercollection?>&k=<?php echo $k?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>		
		</div>
		<?php } ?>
	
		<?php if ($k=="") { ?><div class="CollectionPanelInfo">
		<?php if (($feedback) || ($collection_reorder_caption && $allow_reorder)) { ?>
		<span class="IconComment <?php if ($result[$n]["commentset"]>0) { ?>IconCommentAnim<?php } ?>"><a target="main" href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo $usercollection?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>		
		<?php } ?>
		
		<?php if ($collection_reorder_caption && $allow_reorder) { ?>
		<div class="IconReorder" onMouseDown="InfoBoxWaiting=false;"> </div>
		<span class="IconRemove"><a href="collections.php?remove=<?php echo $ref?>&nc=<?php echo time()?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>
		<?php } else { ?>
		<a href="collections.php?remove=<?php echo $ref?>&nc=<?php echo time()?>">x <?php echo $lang["action-remove"]?></a>
		<?php } ?>
		</div><?php } ?>			
		</div>
		<?php if ($collection_reorder_caption && $allow_reorder) { 
		# Javascript drag/drop enabling.
		?>
		<script type="text/javascript">
		new Draggable('ResourceShell<?php echo $ref?>',{handle: 'IconReorder', revert: true});
		Droppables.add('ResourceShell<?php echo $ref?>',{accept: 'CollectionPanelShell', onDrop: function(element) {ReorderResources(element.id,<?php echo $ref?>);}, hoverclass: 'ReorderHover'});
		</script>
		<?php } ?>
<?php } ?>		
		<?php		
		}
	}

	# Plugin for additional collection listings	(deprecated)
	if (file_exists("plugins/collection_listing.php")) {include "plugins/collection_listing.php";}
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

if ($k!="")
	{
	# Anonymous access, slightly different display
	$tempcol=get_collection($usercollection);
	?>
<div id="CollectionMinTitle"><h2><?php echo $tempcol["name"]?></h2></div>
<div id="CollectionMinRightNav">
  	<?php if (isset($zipcommand)) { ?>
	<li><a href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>" target="main"><?php echo $lang["action-download"]?></a></li>
	<?php } ?>
    <?php if ($feedback) {?><li><a target="main" href="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>"><?php echo $lang["sendfeedback"]?></a></li><?php } ?>
   	<li><a href="collections.php?thumbs=show&collection=<?php echo $usercollection?>&k=<?php echo $k?>" onClick="ToggleThumbs();"><?php echo $lang["showthumbnails"]?></li>
</div>
<?php 
} else { 
?>

<div id="CollectionMinTitle"><h2><?php echo $lang["mycollections"]?></h2></div>

<!--Menu-->	
<div id="CollectionMinRightNav">
  <ul>
<?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
    <?php if (checkperm("s")) { ?><li><a href="collection_manage.php" target="main"><?php echo $lang["managemycollections"]?></a></li>
    <?php if ($allow_share) { ?><li><a href="collection_share.php?ref=<?php echo $usercollection?>" target="main"><?php echo $lang["share"]?></a></li><?php } ?>
    
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a target="main" href="collection_edit.php?ref=<?php echo $usercollection?>">&nbsp;<?php echo $allow_share?$lang["edit"]:$lang["editcollection"]?></a></li><?php } ?>

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
    if ((count($result)>0) && checkperm("e" . $result[0]["archive"]) && allow_multi_edit($usercollection)) { ?>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main"><?php echo $lang["viewall"]?></a></li>
    <li><a href="edit.php?collection=<?php echo $usercollection?>" target="main"><?php echo $lang["editall"]?></a></li>    
    <?php } else { ?>
    <li><a href="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>" target="main"><?php echo $lang["viewall"]?></a></li>
    <?php } ?>
   	<?php if (isset($zipcommand)) { ?>
    <li><a target="main" href="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>"><?php echo $lang["zipall"]?></a></li>
	<?php } ?>
    <?php if (count($result)>0 && $k=="" && checkperm("q"))
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($usercollection);
		if ($min_access!=0)
			{
		    ?>
		    <li><a target="main" href="collection_request.php?ref=<?php echo $usercollection?>"><?php echo 	$lang["request"]?></a></li>
		    <?php
		    }
	    }
	?>
    <?php if (count($result)<=$max_collection_thumbs) { ?><li><a href="collections.php?thumbs=show" onClick="ToggleThumbs();"><?php echo $lang["showthumbnails"]?></a></li><?php } ?>
    
  </ul>
</div>

<!--Collection Dropdown-->	
<div id="CollectionMinDropTitle"><?php echo $lang["currentcollection"]?>:&nbsp;</div>				
<div id="CollectionMinDrop">
<form id="colselect" method="get">
		<div class="MinSearchItem">
		<select name="collection" class="SearchWidth" onchange="if($(this).value==-1){$('entername').toggle();$('entername').focus();return false;} document.getElementById('colselect').submit();">
		<?php
		$found=false;
		$list=get_user_collections($userref);
		for ($n=0;$n<count($list);$n++)
			{
			?>
			<option value="<?php echo $list[$n]["ref"]?>" <?php if ($usercollection==$list[$n]["ref"]) {?> selected<?php $found=true;}?>><?php echo htmlspecialchars($list[$n]["name"])?></option>
			<?php
			}
		if ($found==false)
			{
			# Add this one at the end, it can't be found
			$notfound=get_collection($usercollection);
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
		<input type=text id="entername" name="entername" style="display:inline;display:none;" class="SearchWidth" onUnfocus="$(this).submit();">
		</div>				
  </form>
</div>
<?php } ?>
<?php } ?>
<!--Collection Count-->	
<div id="CollectionMinitems"><strong><?php echo count($result)?></strong>&nbsp;<?php echo $lang["items"]?></div>		
<?php } ?>


</body>
</html>
