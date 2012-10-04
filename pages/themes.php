<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/collections_functions.php";

global $default_perpage_list;
$col_order_by=getvalescaped("order_by",getvalescaped("saved_themes_order_by","created"));setcookie("saved_themes_order_by",$col_order_by);
$sort=getvalescaped("sort",getvalescaped("saved_themes_sort","ASC"));setcookie("saved_themes_sort",$sort);
$per_page=getvalescaped("per_page_list",$default_perpage_list,true);setcookie("per_page_list",$per_page);

hook("themeheader");

if (!function_exists("DisplayTheme")){
function DisplayTheme($themes=array())
	{
	global $theme_direct_jump,$themes_column_sorting,$themes_ref_column,$themes_date_column,$baseurl_short,$baseurl,$default_perpage_list,$collection_prefix,$col_order_by,$revsort,$sort,$find,$getthemes,$m,$lang,$flag_new_themes,$contact_sheet,$theme_images,$allow_share,$zipcommand,$collection_download,$theme_images_align_right,$themes_category_split_pages,$themes_category_split_pages_parents,$collections_compact_style,$pagename,$show_edit_all_link,$preview_all,$userref,$collection_purge,$themes_category_split_pages,$themes_category_split_pages_parents_root_node,$enable_theme_category_sharing,$enable_theme_category_edit,$show_theme_collection_stats,$lastlevelchange;

	$col_order_by=getvalescaped("order_by",getvalescaped("saved_themes_order_by","created"));
	$sort=getvalescaped("sort",getvalescaped("saved_themes_sort","ASC"));
	$revsort = ($sort=="ASC") ? "DESC" : "ASC";
	# pager
	$per_page=getvalescaped("per_page_list",$default_perpage_list,true);

	$collection_valid_order_bys=array("name","c");

	// sorting doesn't work for nonsplit
	if (!$themes_column_sorting || !$themes_category_split_pages || $theme_direct_jump){$sort="ASC";$col_order_by="name";$themes_column_sorting=false;}

	if ($themes_ref_column){$collection_valid_order_bys[]="ref";}
	if ($themes_date_column){$collection_valid_order_bys[]="created";}
	
	$modified_collection_valid_order_bys=hook("modifycollectionvalidorderbys");
	if ($modified_collection_valid_order_bys){$collection_valid_order_bys=$modified_collection_valid_order_bys;}
	if (!in_array($col_order_by,$collection_valid_order_bys)) {$col_order_by="created";} # Check the value is one of the valid values (SQL injection filter)

	# Work out theme name
	$themecount=count($themes);
	for ($x=0;$x<$themecount;$x++)
		{
		if (isset($themes[$x])&&!isset($themes[$x+1]))
			$themename=i18n_get_translated($themes[$x]);
		}

	$getthemes=get_themes($themes);
	
	if (count($getthemes)>0)
		{
		?>
		<div class="RecordBox">
		<div class="RecordPanel">

		<div class="RecordHeader">

		<?php
		if ($themes_category_split_pages && $themes_category_split_pages_parents){?><h1><?php
		echo $lang["collections"];?></h1><?php }

		// count total items in themes
	    $totalcount=0;
	    for ($m=0;$m<count($getthemes);$m++)
			{$totalcount=$totalcount+$getthemes[$m]['c'];
		}

		if ($theme_images_align_right)
			{
			?>
			<div style="float:right;">
			<?php
			}

		$images=get_theme_image($themes);
		if (($images!==false) && ($theme_images))
			{
			for ($n=0;$n<count($images);$n++)
				{
				?><div style="float:left;margin-right:12px;"><img class="CollectImageBorder" src="<?php echo get_resource_path($images[$n],false,"col",false) ?>" /></div>
				<?php
				}
			}
		if ($theme_images_align_right)
			{
			?>
			</div>
			<?php
			}
		$themeslinks="";
		for ($x=0;$x<count($themes);$x++){
			$themeslinks.="theme".($x+1)."=".urlencode($themes[$x])."&";
		}
		?>
        <table><tr><td style="margin:0px;padding:0px;">
		<h1 ><?php if ($themes_category_split_pages && $themes_category_split_pages_parents)
			{
			if ($themes_category_split_pages_parents_root_node){?><a href="themes.php"  onClick="return CentralSpaceLoad(this,true);"><?php echo $lang["themes"];?></a> / <?php } 
			$themescrumbs="";
			for ($x=0;$x<count($themes);$x++){
				$themescrumbs.="theme".($x+1)."=".urlencode($themes[$x])."&";
				?><a href="themes.php?<?php echo $themescrumbs?>"  onClick="return CentralSpaceLoad(this,true);"><?php echo htmlspecialchars(i18n_get_translated($themes[$x]))?></a> / <?php
				}
			}
		else
			{
			echo stripslashes(str_replace("*","",$themename));
			}?></h1></td></tr><tr><td style="margin:0px;padding:0px;">
			
			<?php
			if (($show_theme_collection_stats) || (!($themes_category_split_pages) && ($enable_theme_category_sharing || $enable_theme_category_edit)))
				{
				$linkparams="";
				for ($x=0;$x<count($themes);$x++){
				$linkparams.="theme".($x+1)."=".urlencode($themes[$x])."&";
				}
				if($show_theme_collection_stats)
					{
					?>
					<p style="clear:none;"><?php $collcount = count($getthemes); echo $collcount==1 ? $lang["collections-1"] : sprintf(str_replace("%number","%d",$lang["collections-2"]),$collcount,$totalcount); hook("themeactioninline");
					?>
					</p>
					</td><td style="margin:0px;padding:0px;">
					<?php
					}
					?>
				<?php
					if(!($themes_category_split_pages))
					{
					if (checkperm("h") && $enable_theme_category_sharing)
						{
						$sharelink="";
						for ($x=0;$x<count($themes);$x++)
							{
							$sharelink.="theme".($x+1)."=" . urlencode($themes[$x]) ."&";					
							}
						?>
						
						</td><tr><td style="margin:0px;padding:0px;">
						<a href="theme_category_share.php?<?php echo $linkparams?>"  onClick="return CentralSpaceLoad(this,true);"><?php echo "> " . $lang["share"] . "</a>";
						}
					hook("themeaction");
					
					if ($enable_theme_category_edit && checkperm("t"))
						{
						?>
						<a href="theme_edit.php?<?php echo $linkparams . "lastlevelchange=" . $lastlevelchange?>" onClick="return CentralSpaceLoad(this,true);"><?php echo "> " . $lang["action-edit"] . "</a>";
						}
					}
				}
				?>
			
			</td></tr></table>
            <!-- The number of collections should never be equal to zero. -->

		<div class="clearerright"> </div>
		</div>
		<br />
		<div class="Listview" style="margin-top:10px;margin-bottom:5px;clear:left;">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewBoxedTitleStyle">
		<td><?php if ($col_order_by=="name") {?><span class="Selected"><?php } if($themes_column_sorting) { ?><a href="<?php echo $baseurl_short?>pages/themes.php?<?php echo $themeslinks?>order_by=name&sort=<?php echo $revsort?>" onClick="return CentralSpaceLoad(this);"><?php } ?><?php echo $lang["collectionname"]?><?php  if($themes_category_split_pages) { ?></a><?php } ?><?php if ($col_order_by=="name") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
		<?php if ($themes_ref_column){?>
		<td><?php if ($col_order_by=="ref") {?><span class="Selected"><?php } if($themes_column_sorting) { ?><a href="<?php echo $baseurl_short?>pages/themes.php?<?php echo $themeslinks?>order_by=ref&sort=<?php echo $revsort?>" onClick="return CentralSpaceLoad(this);"><?php } ?><?php echo $lang["id"]?><?php  if($themes_category_split_pages) { ?></a><?php } ?><?php if ($col_order_by=="ref") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
		<?php } ?>
		<?php if ($themes_date_column){?>
		<td><?php if ($col_order_by=="created") {?><span class="Selected"><?php } if($themes_column_sorting) { ?><a href="<?php echo $baseurl_short?>pages/themes.php?<?php echo $themeslinks?>order_by=created&sort=<?php echo $revsort?>" onClick="return CentralSpaceLoad(this);"><?php } ?><?php echo $lang["created"]?><?php  if($themes_category_split_pages) { ?></a><?php } ?><?php if ($col_order_by=="created") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
		<?php } ?>
		<td><?php if ($col_order_by=="c") {?><span class="Selected"><?php } if($themes_column_sorting) { ?><a href="<?php echo $baseurl_short?>pages/themes.php?<?php echo $themeslinks?>order_by=c&sort=<?php echo $revsort?>" onClick="return CentralSpaceLoad(this);"><?php } ?><?php echo $lang["itemstitle"]?><?php  if($themes_category_split_pages) { ?></a><?php } ?><?php if ($col_order_by=="c") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
		<?php hook("beforecollectiontoolscolumnheader","themes",array($themeslinks));?>
		<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
		</tr>

		<?php
		for ($m=0;$m<count($getthemes);$m++)
			{
			?>
			<tr <?php hook("collectionlistrowstyle");?>>
			<td width="50%"><div class="ListTitle"><a href="search.php?search=!collection<?php echo $getthemes[$m]["ref"]?>&bc_from=themes"  title="<?php echo $lang["collectionviewhover"]?>" onClick="return CentralSpaceLoad(this,true);"><?php echo htmlspecialchars(i18n_get_translated($getthemes[$m]["name"]))?></a>
			<?php if ($flag_new_themes && (time()-strtotime($getthemes[$m]["created"]))<(60*60*24*14)) { ?><div class="NewFlag"><?php echo $lang["newflag"]?></div><?php } ?>
			</div></td>
			<?php if ($themes_ref_column){?>
			<td><?php echo $getthemes[$m]["ref"];?></td>
			<?php } ?>
			<?php if ($themes_date_column){?>
			<td><?php echo nicedate($getthemes[$m]["created"],true)?></td>
			<?php } ?>
			<td width="5%"><?php echo $getthemes[$m]["c"]?></td>
			<?php hook("beforecollectiontoolscolumn");?>
			<td nowrap><div class="ListTools">
            <?php if ($collections_compact_style){
            include("collections_compact_style.php");
            } else {

                ?><a href="search.php?search=<?php echo urlencode("!collection" . $getthemes[$m]["ref"])?>" title="<?php echo $lang["collectionviewhover"]?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["viewall"]?></a>

                <?php if (!checkperm("b")) { ?>&nbsp;<?php echo change_collection_link($getthemes[$m]["ref"])?>&gt;&nbsp;<?php echo $lang["action-select"]?></a><?php } ?>

                <?php if (isset($zipcommand) || $collection_download) { ?>
                &nbsp;<a href="collection_download.php?collection=<?php echo $getthemes[$m]["ref"]?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["action-download"]?></a>
                <?php } ?>

                <?php if ($contact_sheet==true) { ?>
                &nbsp;<a href="contactsheet_settings.php?ref=<?php echo $getthemes[$m]["ref"]?>"  title="<?php echo $lang["collectioncontacthover"]?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
                <?php } ?>

                <?php if ($allow_share && (checkperm("v") || checkperm ("g"))) { ?> &nbsp;<a href="collection_share.php?ref=<?php echo $getthemes[$m]["ref"]?>"  onClick="return CentralSpaceLoad(this,true);" target="main">&gt;&nbsp;<?php echo $lang["share"]?></a><?php } ?>

                <?php if (checkperm("h")) {?>&nbsp;<a href="collection_edit.php?ref=<?php echo $getthemes[$m]["ref"]?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["action-edit"]?></a><?php } ?>

                <?php hook("addcustomtool","",array($getthemes[$m]["ref"])); ?>
			<?php } ?>
			</td>
			</tr>
			<?php
			}
		?>
		</table>
		</div>

		</div>
		<div class="PanelShadow"> </div>
		</div>
		<?php
		}
	}
}


$themes=array();
$themecount=0;
foreach ($_GET as $key => $value) {
	// only set necessary vars
	if (substr($key,0,5)=="theme" && $value!=""){
		$themes[$themecount]=urldecode($value);
		$themecount++;
		}
	}

$header=getvalescaped("header","");
$smart_theme=getvalescaped("smart_theme","");

# When changing higher levels, deselect the lower levels.
$lastlevelchange=getvalescaped("lastlevelchange",1);

for ($n=$lastlevelchange;$n<=$themecount;$n++){
	if ($n>$lastlevelchange && !$themes_category_split_pages){
	$themes[$n-1]="";
	}
}

//if ($lastlevelchange=="1") {$theme2="";$theme3="";}
//if ($lastlevelchange=="2") {$theme3="";}
include "../include/header.php";
?>


<div class="BasicsBox">
<form method=get id="themeform" action="themes.php">
<input type="hidden" name="lastlevelchange" id="lastlevelchange" value="">

<?php if (!$themes_category_split_pages) { ?>
  <h1><?php echo htmlspecialchars(getval("title",$lang["themes"]),ENT_QUOTES)?></h1>
  <p><?php echo text("introtext")?></p>
<?php } ?>

<?php if ($theme_direct_jump)
	{
	# Display title and description when 'direct jump' mode is enabled.
	$text=text("introtext");
	$title=htmlspecialchars(getval("title",$lang["themes"]),ENT_QUOTES);

	if (count($themes)>0)
		{
		$title=i18n_get_translated($themes[count($themes)-1]);
		if ($text=="") {$text=text("introtext" . $themes[count($themes)-1]);}
		}

	?>
  <h1><?php echo $title ?></h1>
  <p><?php echo $text ?></p>
<?php } ?>


  <style>.ListviewTitleBoxed {background-color:#fff;}</style>

<?php
if ($themes_category_split_pages && isset($themes[0]) && !$theme_direct_jump)
	{
	# Display back link
	$link="themes.php?";
	for ($x=0;$x<count($themes);$x++){
		if ($x!=0){ $link.="&"; }
		$link.="theme";
		$link.=($x==0)?"":$x;
		$link.="=". urlencode((!isset($themes[$x+1]))?"":$themes[$x]);
	}
	?>
	<p><a href="<?php echo $link?>" onClick="return CentralSpaceLoad(this,true);">&lt;&lt; <?php echo $lang["back"]?></a></p>
	<?php

}


#if ($themes_category_split_pages && $theme1=="" && $smart_theme=="")
if ($smart_theme!="")
	{
	}
elseif ($themes_category_split_pages && !$theme_direct_jump)
	{
	# --------------- Split theme categories on to separate pages -------------------
	#
	# This option shows the theme categories / subcategories as a simple list, instead of using dropdown boxes.
	#

	?>
	<?php
	if (count($themes)<$theme_category_levels){
	$headers=get_theme_headers($themes);
	if (count($headers)>0){?>
		<div class="RecordBox">
		<div class="RecordPanel">

		<div class="RecordHeader">
		<h1 style="margin-top:5px;"><?php
		if (!isset($themes[0])){
			echo $lang["themes"];
			}
		else{
			if ($themes_category_split_pages_parents){
				$themeslinks="";
				echo (count($headers)>1)?$lang["subcategories"]:$lang["subcategory"];?></h1><h1 style="margin-top:5px;"><?php if ($themes_category_split_pages_parents_root_node){?><a href="themes.php" onClick="return CentralSpaceLoad(this,true);"><?php echo $lang["themes"];?></a> / <?php } ?><?php
				for ($x=0;$x<count($themes);$x++){
					$themeslinks.="theme".($x+1)."=".urlencode($themes[$x])."&";
					?><a href="themes.php?<?php echo $themeslinks?>"  onClick="return CentralSpaceLoad(this,true);"><?php echo htmlspecialchars(i18n_get_translated($themes[$x]))?></a> / <?php
					}
			}
			else {
				echo $lang["subcategories"];
			}
		}?></h1>
		<?php hook("beforethemeheaderlist");?>
		</div>

		<div class="Listview" style="margin-top:10px;margin-bottom:10px;clear:left;">
		<table  id="themeheaders" border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewBoxedTitleStyle">
		<td><?php echo $lang["name"]?></td>
		<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
		</tr>
		<?php

		# Theme headers
		for ($n=0;$n<count($headers);$n++)
			{
			$link="themes.php?theme1=" . urlencode((!isset($themes[0]))? $headers[$n]:$themes[0]);
			$editlink="theme_edit.php?theme1=" . urlencode((!isset($themes[0]))? $headers[$n]:$themes[0]);
			$sharelink="theme_category_share.php?theme1=" . urlencode((!isset($themes[0]))? $headers[$n]:$themes[0]);
			for ($x=2;$x<count($themes)+2;$x++){
				if (isset($headers[$n])){
					$link.="&theme".$x."=" . urlencode((!isset($themes[$x-1]))? ((!isset($themes[$x-2]))?"":$headers[$n]):$themes[$x-1]);
					$editlink.="&theme".$x."=" . urlencode((!isset($themes[$x-1]))? ((!isset($themes[$x-2]))?"":$headers[$n]):$themes[$x-1]);
					$sharelink.="&theme".$x."=" . urlencode((!isset($themes[$x-1]))? ((!isset($themes[$x-2]))?"":$headers[$n]):$themes[$x-1]);
				}
			}?>
			<tr>
			<td><div class="ListTitle"><a href="<?php echo $link ?>" onClick="return CentralSpaceLoad(this,true);"><?php echo htmlspecialchars(i18n_get_translated(str_replace("*","",$headers[$n])))?></a></div></td>
			<td><div class="ListTools"><a href="<?php echo $link ?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["action-select"]?></a>
			<?php if (checkperm("h") && $enable_theme_category_sharing) {?>&nbsp;<a href="<?php echo $sharelink ?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["share"]?></a><?php }
			if ($enable_theme_category_edit && checkperm("t")) {?>&nbsp;<a href="<?php echo $editlink ?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["action-edit"]?></a><?php }
			?>
			</div></td>
			</tr>
			<?php
			}

		# Smart theme headers
		/*
		$headers=get_smart_theme_headers($themes);
		for ($n=0;$n<count($headers);$n++)
			{
			?>
			<tr>
			<td><div class="ListTitle"><a href="themes.php?smart_theme=<?php echo urlencode($headers[$n]["ref"])?>"><?php echo $headers[$n]["smart_theme_name"]?></a></div></td>
			<td><div class="ListTools"><a href="themes.php?smart_theme=<?php echo urlencode($headers[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["action-select"]?></a></div></td>
			</tr>
			<?php
			}*/

		?>
		</table>
		</div>

		</div>
		<div class="PanelShadow"> </div>
		</div>
	<?php } }/*end if subcategory headers */ ?>
	<?php
	}
else
	{
	# --------------- All theme categories on one page, OR multi level browsing via dropdowns. -------------------


	if ($theme_category_levels>1 && !$theme_direct_jump)
		{
		# Display dropdown box for multiple theme selection levels.
		?>
		<div class="RecordBox">
		<div class="RecordPanel">

		<div class="Question" style="border-top:none;">
		<label for="theme1"><?php echo $lang["themecategory"] . " 1" ?></label>
		<select class="stdwidth" name="theme1" id="theme1" onchange="document.getElementById('lastlevelchange').value='1';document.getElementById('themeform').submit();">
		<?php
		//if (!isset($themes[0]))
			//{
			?><option value=""><?php echo $lang["select"]?></option><?php
			//}

		# ----------------- Level 1 headers -------------------------
		$headers=get_theme_headers(array());
		for ($n=0;$n<count($headers);$n++)
			{
			?><option value="<?php echo htmlspecialchars($headers[$n])?>" <?php if (isset($themes[0])&&
			stripslashes($themes[0])==
			stripslashes($headers[$n]))  { ?>selected<?php } ?>><?php echo str_replace("*","",i18n_get_translated($headers[$n]))?></option><?php
			}
		?>
		</select>
		<div class="clearerleft"> </div>
		</div>

		<?php
		if (count($themes)>0){
		for ($x=0;$x<count($themes);$x++){
		# ----------------- Level headers -------------------------
		if (isset($themes[$x])&&$themes[$x]!="" && $theme_category_levels>($x+1))
			{
			$themearray=array();
			for($n=0;$n<$x+1;$n++){
				$themearray[]=$themes[$n];
				}
			$headers=get_theme_headers($themearray);
			if (count($headers)>0)
				{
				?>
				<div class="Question" style="border-top:none;">
				<label for="theme<?php echo $x+2?>"><?php echo $lang["themecategory"] . " ".($x+2) ?></label>

				<select class="stdwidth" name="theme<?php echo $x+2?>" id="theme<?php echo $x+2?>" onchange="document.getElementById('lastlevelchange').value='<?php echo $x+2?>';document.getElementById('themeform').submit();">
				<?php
				//if (!isset($themes[$x+1])||$themes[$x+1]=="")
					//{
					?><option value=""><?php echo $lang["select"]?></option><?php
					//}
				for ($n=0;$n<count($headers);$n++)
					{
					?><option value="<?php echo htmlspecialchars($headers[$n])?>" <?php if (isset($themes[$x+1])&&stripslashes($themes[$x+1])==stripslashes($headers[$n]))  { ?>selected<?php } ?> ><?php echo str_replace("*","",$headers[$n])?></option><?php
					}
				?>
				</select>
				<div class="clearerleft"> </div>
				</div>
				<?php
				}
			}
		}
	}
		?>
		</div>
		</div>
		<?php
		}
	}


# Display Themes

if (isset($themes[0]) && $theme_direct_jump==false)
	{
	# Display just the selected theme
	DisplayTheme($themes);
	}
elseif (($theme_category_levels==1 && $smart_theme=="") || $theme_direct_jump)
	{
	# Display all themes
	$headers=get_theme_headers($themes);
	for ($n=0;$n<count($headers);$n++)
		{
			DisplayTheme(array_merge($themes,array($headers[$n])));
		}
	}
?>

<?php
# ------- Smart Themes -------------
if ($header=="" && !isset($themes[0]))
	{
	$headers=get_smart_theme_headers();

	for ($n=0;$n<count($headers);$n++)
		{
		$node=getval("node",0);

		if ((checkperm("f*") || checkperm("f" . $headers[$n]["ref"]))
		&& !checkperm("f-" . $headers[$n]["ref"]) && ($smart_theme=="" || $smart_theme==$headers[$n]["ref"]))
			{
			?>
			<div class="RecordBox">
			<div class="RecordPanel">

			<div class="RecordHeader">
			<h1 style="margin-top:5px;">
			<?php if ($node==0)
				{
				# Top level node. Just display smart theme name.
				echo str_replace("*","",i18n_get_translated($headers[$n]["smart_theme_name"]));
				}
			else
				{
				# Sub node, display node name and make it a link to the previous level.
				?>
				<a href="themes.php?smart_theme=<?php echo $headers[$n]["ref"] ?>&node=<?php echo getval("parentnode",0) ?>&nodename=<?php echo getval("parentnodename","") ?>" onClick="return CentralSpaceLoad(this,true);"><?php echo getval("nodename","???") ?></a>
				<?php
				}
			?>
			</h1>
			</div>

			<div class="Listview" style="margin-top:10px;margin-bottom:10px;clear:left;">
			<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
			<tr class="ListviewBoxedTitleStyle">
			<td><?php echo $lang["name"]?></td>
			<?php hook("beforecollectiontoolscolumnheader");?>
			<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
			</tr>

			<?php
			$themes=get_smart_themes($headers[$n]["ref"],$node);
			for ($m=0;$m<count($themes);$m++)
				{
				$s=$headers[$n]["name"] . ":" . $themes[$m]["name"];

				# Indent this item?
				$indent=str_pad("",$themes[$m]["indent"]*5," ") . ($themes[$m]["indent"]==0?"":"&#746;") . "&nbsp;";
				$indent=str_replace(" ","&nbsp;",$indent);

				?>
				<tr>
				<td><div class="ListTitle"><?php echo $indent?>
				<?php if ($themes[$m]["children"]>0 && $themes_category_navigate_levels)
					{
					# Has children. Default action is to navigate to a deeper level.
					?>
					<a href="themes.php?smart_theme=<?php echo $headers[$n]["ref"] ?>&node=<?php echo $themes[$m]["node"] ?>&parentnode=<?php echo $node ?>&parentnodename=<?php echo urlencode(getval("nodename","")) ?>&nodename=<?php echo urlencode($themes[$m]["name"]) ?>" onClick="return CentralSpaceLoad(this,true);">
					<?php
					}
				else
					{
					# Has no children. Default action is to show matching resources.
					?>
					<a href="search.php?search=<?php echo urlencode($s)?>&resetrestypes=true" onClick="return CentralSpaceLoad(this,true);">
					<?php
					}
				?>

				<?php echo i18n_get_translated($themes[$m]["name"])?></a>
				</div></td>
				<?php hook("beforecollectiontoolscolumn");?>
				<td><div class="ListTools">
				<a href="search.php?search=<?php echo urlencode($s)?>&resetrestypes=true" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $themes_category_split_pages?$lang["action-viewmatchingresources"]:$lang["viewall"]?></a>
				<?php if ($themes_category_split_pages) { ?>
				<a href="themes.php?smart_theme=<?php echo $headers[$n]["ref"] ?>&node=<?php echo $themes[$m]["node"] ?>&parentnode=<?php echo $node ?>&parentnodename=<?php echo urlencode(getval("nodename","")) ?>&nodename=<?php echo urlencode($themes[$m]["name"]) ?>" onClick="return CentralSpaceLoad(this,true);">&gt;&nbsp;<?php echo $lang["action-expand"]?></a>
				<?php }
                hook("additionalsmartthemetool");?>
				</div></td>
				</tr>
				<?php
				}
			?>
			</table>
			</div>

			</div>
			<div class="PanelShadow"> </div>
			</div>
			<?php
			}
		}
	}


?>

</form>
</div>
<?php if (!$public_collections_header_only){?>
<?php if (!checkperm("b") && $enable_public_collections) { ?>
<div class="clearerleft"> </div>
<div class="BasicsBox">
	<h2>&nbsp;</h2>
    <h1><?php echo $lang["findpubliccollection"]?></h1>
    <p class="tight"><?php echo text("findpublic")?></p>
    <p><a href="collection_public.php" onClick="return CentralSpaceLoad(this,true);"><?php echo $lang["findpubliccollection"]?>&nbsp;&gt;</a></p>
</div>
<?php } ?>
<?php } ?>

<?php
include "../include/footer.php";
?>
