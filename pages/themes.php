<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/collections_functions.php";

$header=getvalescaped("header","");
$theme1=getvalescaped("theme1","");
$theme2=getvalescaped("theme2","");
$theme3=getvalescaped("theme3","");
$smart_theme=getvalescaped("smart_theme","");

# When changing higher levels, deselect the lower levels.
$lastlevelchange=getvalescaped("lastlevelchange","");
if ($lastlevelchange=="1") {$theme2="";$theme3="";}
if ($lastlevelchange=="2") {$theme3="";}

include "../include/header.php";
?>


<div class="BasicsBox"> 
<form method=get id="themeform">
<input type="hidden" name="lastlevelchange" id="lastlevelchange" value="">

<?php if (!$themes_category_split_pages) { ?>
  <h1><?php echo getval("title",$lang["themes"])?></h1>
  <p><?php echo text("introtext")?></p>
<?php } ?>

  <style>.ListviewTitleBoxed {background-color:#fff;}</style>

<?php


if ($themes_category_split_pages && $theme1=="" && $smart_theme=="")
	{
	# --------------- Split theme categories on to separate pages -------------------
	?>
	<div class="RecordBox">
	<div class="RecordPanel">  
	
	<div class="RecordHeader">
	<h1 style="margin-top:5px;"><?php echo $lang["themes"] ?></h1>
	</div>
	
	<div class="Listview" style="margin-top:10px;margin-bottom:10px;clear:left;">
	<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
	<tr class="ListviewBoxedTitleStyle">
	<td><?php echo $lang["name"]?></td>
	<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
	</tr>
	<?php
	
	# Theme headers
	$headers=get_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		?>
		<tr>
		<td><div class="ListTitle"><a href="themes.php?theme1=<?php echo urlencode($headers[$n])?>"><?php echo str_replace("*","",$headers[$n])?></a></div></td>
		<td><div class="ListTools"><a href="themes.php?theme1=<?php echo urlencode($headers[$n])?>">&gt;&nbsp;<?php echo $lang["action-select"]?></a></div></td>
		</tr>
		<?php
		}
	# Smart theme headers
	$headers=get_smart_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		?>
		<tr>
		<td><div class="ListTitle"><a href="themes.php?smart_theme=<?php echo urlencode($headers[$n]["ref"])?>"><?php echo $headers[$n]["smart_theme_name"]?></a></div></td>
		<td><div class="ListTools"><a href="themes.php?smart_theme=<?php echo urlencode($headers[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["action-select"]?></a></div></td>
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
else
	{
	# --------------- All theme categories on one page, OR multi level browsing via dropdowns. -------------------
	
	if ($themes_category_split_pages)
		{
		# Display back link
		?>
		<p><a href="themes.php">&lt;&lt; <?php echo $lang["backtothemes"]?></a></p>
		<?php
		}
	
	if ($theme_category_levels>1)
		{
		# Display dropdown box for multiple theme selection levels.
		?>
		<div class="RecordBox">
		<div class="RecordPanel">  
		
		<div class="Question" style="border-top:none;">
		<label for="theme1"><?php echo $lang["themecategory"] . " 1" ?></label>
		<select class="stdwidth" name="theme1" id="theme1" onchange="document.getElementById('lastlevelchange').value='1';document.getElementById('themeform').submit();">
		<?php
		if ($theme1=="")
			{
			?><option value=""><?php echo $lang["select"]?></option><?php
			}
		
		# ----------------- Level 1 headers -------------------------
		$headers=get_theme_headers();
		for ($n=0;$n<count($headers);$n++)
			{
			?><option value="<?php echo htmlspecialchars($headers[$n])?>" <?php if (stripslashes($theme1)==stripslashes($headers[$n]))  { ?>selected<?php } ?>><?php echo str_replace("*","",$headers[$n])?></option><?php
			}
		?>
		</select>
		<div class="clearerleft"> </div>
		</div>
		
		<?php
		# ----------------- Level 2 headers -------------------------
		if ($theme1!="" && $theme_category_levels>1)
			{
			$headers=get_theme_headers($theme1);
			if (count($headers)>0)
				{
				?>
				<div class="Question" style="border-top:none;">
				<label for="theme2"><?php echo $lang["themecategory"] . " 2" ?></label>
		
				<select class="stdwidth" name="theme2" id="theme2" onchange="document.getElementById('lastlevelchange').value='2';document.getElementById('themeform').submit();">
				<?php
				if ($theme2=="")
					{
					?><option value=""><?php echo $lang["select"]?></option><?php
					}
				for ($n=0;$n<count($headers);$n++)
					{
					?><option value="<?php echo htmlspecialchars($headers[$n])?>" <?php if (stripslashes($theme2)==stripslashes($headers[$n]))  { ?>selected<?php } ?> ><?php echo str_replace("*","",$headers[$n])?></option><?php
					}
				?>
				</select>
				<div class="clearerleft"> </div>
				</div>
				<?php
				}
			}
		
		# ----------------- Level 3 headers -------------------------
		if ($theme2!="" && $theme_category_levels>2)
			{
			$headers=get_theme_headers($theme1,$theme2);
			if (count($headers)>0)
				{
				?>
				<div class="Question" style="border-top:none;">
				<label for="theme3"><?php echo $lang["themecategory"] . " 3" ?></label>
				<select class="stdwidth" name="theme3" id="theme3" onchange="document.getElementById('lastlevelchange').value='3';document.getElementById('themeform').submit();">
				<?php
				if ($theme3=="")
					{
					?><option value=""><?php echo $lang["select"]?></option><?php
					}
				for ($n=0;$n<count($headers);$n++)
					{
					?><option value="<?php echo htmlspecialchars($headers[$n])?>" <?php if (stripslashes($theme3)==stripslashes($headers[$n]))  { ?>selected<?php } ?>><?php echo str_replace("*","",$headers[$n])?></option><?php
					}
				?>
				</select>
				<div class="clearerleft"> </div>
				</div>
				<?php
				}
			}
		?>
		</div>
		</div>
		<?php
		}
	
	# Display Themes
	
	if ($theme1!="")
		{
		# Display just the selected theme
		DisplayTheme($theme1,$theme2,$theme3);
		}
	elseif ($theme_category_levels==1 && $smart_theme=="")
		{
		# Display all themes
		$headers=get_theme_headers();
		for ($n=0;$n<count($headers);$n++)
			{
			if ($header=="" || $header==$headers[$n])
				{
				DisplayTheme($headers[$n]);
				}
			}
		}
	?>
	
	<?php
	# ------- Smart Themes -------------
	if ($header=="" && $theme1=="")
		{
		$headers=get_smart_theme_headers();
		for ($n=0;$n<count($headers);$n++)
			{
			if ((checkperm("f*") || checkperm("f" . $headers[$n]["ref"]))
			&& !checkperm("f-" . $headers[$n]["ref"]) && ($smart_theme=="" || $smart_theme==$headers[$n]["ref"]))
				{
				?>
				<div class="RecordBox">
				<div class="RecordPanel">  
	
				<div class="RecordHeader">
				<h1 style="margin-top:5px;"><?php echo str_replace("*","",i18n_get_translated($headers[$n]["smart_theme_name"]))?></h1>
				</div>
			
				<div class="Listview" style="margin-top:10px;margin-bottom:10px;clear:left;">
				<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
				<tr class="ListviewBoxedTitleStyle">
				<td><?php echo $lang["name"]?></td>
				<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
				</tr>
				
				<?php
				$themes=get_smart_themes($headers[$n]["ref"]);
				for ($m=0;$m<count($themes);$m++)
					{
					$s=$headers[$n]["name"] . ":" . $themes[$m]["name"];
	
					# Indent this item?				
					$indent=str_pad("",$themes[$m]["indent"]*5," ") . ($themes[$m]["indent"]==0?"":"&#746;") . "&nbsp;";
					$indent=str_replace(" ","&nbsp;",$indent);
	
					?>
					<tr>
					<td><div class="ListTitle"><?php echo $indent?><a href="search.php?search=<?php echo urlencode($s)?>&resetrestypes=true"><?php echo i18n_get_translated($themes[$m]["name"])?></a></div></td>
					<td><div class="ListTools"><a href="search.php?search=<?php echo urlencode($s)?>&resetrestypes=true">&gt;&nbsp;<?php echo $lang["action-view"]?></a></div></td>
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
	}

function DisplayTheme($theme1,$theme2="",$theme3="")
	{
	global $lang,$flag_new_themes,$contact_sheet,$theme_images,$allow_share,$zipcommand,$theme_images_align_right;

	# Work out theme name
	if ($theme1!="") {$themename=$theme1;}
	if ($theme2!="") {$themename=$theme2;}
	if ($theme3!="") {$themename=$theme3;}

	$themes=get_themes($theme1,$theme2,$theme3);
	if (count($themes)>0)
		{
		?>
		<div class="RecordBox">
		<div class="RecordPanel">  
		
		<div class="RecordHeader">
		
		<?php
		if ($theme_images_align_right)
			{
			?>
			<div style="float:right;">
			<?php	
			}
		
		$images=get_theme_image($theme1, $theme2, $theme3);
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
		?>
		<h1 style="<?php if (!$theme_images_align_right) { ?>margin-top:12px;<?php } ?>float:left;<?php if ($theme_images_align_right) { ?>margin-bottom:50px;<?php } ?>"><?php echo stripslashes(str_replace("*","",$themename))?></h1>

		<div class="clearerright"> </div>
		</div>
		
		<div class="Listview" style="margin-top:10px;margin-bottom:5px;clear:left;">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewBoxedTitleStyle">
		<td><?php echo $lang["name"]?></td>
		<td width="5%"><?php echo $lang["itemstitle"]?></td>
		<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
		</tr>
		
		<?php
		for ($m=0;$m<count($themes);$m++)
			{
			?>
			<tr>
			<td width="50%"><div class="ListTitle"><a href="search.php?search=!collection<?php echo $themes[$m]["ref"]?>&bc_from=themes"  title="<?php echo $lang["collectionviewhover"]?>"><?php echo htmlspecialchars($themes[$m]["name"])?></a>
			<?php if ($flag_new_themes && (time()-strtotime($themes[$m]["created"]))<(60*60*24*30)) { ?><div class="NewFlag"><?php echo $lang["newflag"]?></div><?php } ?>
			</div></td>
			<td width="5%"><?php echo $themes[$m]["c"]?></td>
			
			<td nowrap><div class="ListTools"><a href="search.php?search=<?php echo urlencode("!collection" . $themes[$m]["ref"])?>" title="<?php echo $lang["collectionviewhover"]?>">&gt;&nbsp;<?php echo $lang["action-view"]?></a>
			
			<?php if (!checkperm("b")) { ?>&nbsp;<?php echo change_collection_link($themes[$m]["ref"])?>&gt;&nbsp;<?php echo $lang["action-select"]?></a><?php } ?>
		
			<?php if (isset($zipcommand)) { ?>
			&nbsp;<a href="collection_download.php?collection=<?php echo $themes[$m]["ref"]?>"
			>&gt;&nbsp;<?php echo $lang["action-download"]?></a>
			<?php } ?>
			
			<?php if ($contact_sheet==true) { ?>
			&nbsp;<a href="contactsheet_settings.php?c=<?php echo $themes[$m]["ref"]?>"  title="<?php echo $lang["collectioncontacthover"]?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
			<?php } ?>
		
			<?php if ($allow_share && (checkperm("v") || checkperm ("g"))) { ?> &nbsp;<a href="collection_share.php?ref=<?php echo $themes[$m]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["share"]?></a><?php } ?>
		
			<?php if (checkperm("h")) {?>&nbsp;<a href="collection_edit.php?ref=<?php echo $themes[$m]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?></a><?php } ?>
		
			<?php hook("addcustomtool"); ?>
			
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
?>

</form>
</div>

<?php if (!checkperm("b") && $enable_public_collections) { ?>
<div class="clearerleft"> </div>
<div class="BasicsBox">
	<h2>&nbsp;</h2>
    <h1><?php echo $lang["findapubliccollection"]?></h1>
    <p class="tight"><?php echo text("findpublic")?></p>
    <p><a href="collection_public.php"><?php echo $lang["findapubliccollection"]?>&nbsp;&gt;</a></p>
</div>
<?php } ?>

<?php
include "../include/footer.php";
?>
