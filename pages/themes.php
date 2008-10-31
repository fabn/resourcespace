<?
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/collections_functions.php";

$header=getvalescaped("header","");
$theme1=getvalescaped("theme1","");
$theme2=getvalescaped("theme2","");
$theme3=getvalescaped("theme3","");

# When changing higher levels, deselect the lower levels.
$lastlevelchange=getvalescaped("lastlevelchange","");
if ($lastlevelchange=="1") {$theme2="";$theme3="";}
if ($lastlevelchange=="2") {$theme3="";}

include "../include/header.php";
?>


<div class="BasicsBox"> 
<form method=get id="themeform">
<input type="hidden" name="lastlevelchange" id="lastlevelchange" value="">

  <h1><?=$lang["themes"]?></h1>
  <p><?=text("introtext")?></p>
  <style>.ListviewTitleBoxed {background-color:#fff;}</style>
<?
if ($theme_category_levels>1)
	{
	# Display dropdown box for multiple theme selection levels.
	?>
	<div class="RecordBox">
	<div class="RecordPanel">  
	
	<div class="Question" style="border-top:none;">
	<label for="theme1"><?=$lang["themecategory"] . " 1" ?></label>
	<select class="stdwidth" name="theme1" id="theme1" onchange="document.getElementById('lastlevelchange').value='1';document.getElementById('themeform').submit();">
	<?
	if ($theme1=="")
		{
		?><option value=""><?=$lang["select"]?></option><?
		}
	
	# ----------------- Level 1 headers -------------------------
	$headers=get_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		?><option value="<?=$headers[$n]?>" <? if (stripslashes($theme1)==stripslashes($headers[$n]))  { ?>selected<? } ?>><?=str_replace("*","",$headers[$n])?></option><?
		}
	?>
	</select>
	<div class="clearerleft"> </div>
	</div>
	
	<?
	# ----------------- Level 2 headers -------------------------
	if ($theme1!="" && $theme_category_levels>1)
		{
		$headers=get_theme_headers($theme1);
		if (count($headers)>0)
			{
			?>
			<div class="Question" style="border-top:none;">
			<label for="theme2"><?=$lang["themecategory"] . " 2" ?></label>
	
			<select class="stdwidth" name="theme2" id="theme2" onchange="document.getElementById('lastlevelchange').value='2';document.getElementById('themeform').submit();">
			<?
			if ($theme2=="")
				{
				?><option value=""><?=$lang["select"]?></option><?
				}
			for ($n=0;$n<count($headers);$n++)
				{
				?><option value="<?=$headers[$n]?>" <? if (stripslashes($theme2)==stripslashes($headers[$n]))  { ?>selected<? } ?> ><?=str_replace("*","",$headers[$n])?></option><?
				}
			?>
			</select>
			<div class="clearerleft"> </div>
			</div>
			<?
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
			<label for="theme3"><?=$lang["themecategory"] . " 3" ?></label>
			<select class="stdwidth" name="theme3" id="theme3" onchange="document.getElementById('lastlevelchange').value='3';document.getElementById('themeform').submit();">
			<?
			if ($theme3=="")
				{
				?><option value=""><?=$lang["select"]?></option><?
				}
			for ($n=0;$n<count($headers);$n++)
				{
				?><option value="<?=$headers[$n]?>" <? if (stripslashes($theme3)==stripslashes($headers[$n]))  { ?>selected<? } ?>><?=str_replace("*","",$headers[$n])?></option><?
				}
			?>
			</select>
			<div class="clearerleft"> </div>
			</div>
			<?
			}
		}
	?>
	</div>
	</div>
	<?
	}

# Display Themes

if ($theme1!="")
	{
	# Display just the selected theme
	DisplayTheme($theme1,$theme2,$theme3);
	}
elseif ($theme_category_levels==1)
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

<?
# ------- Smart Themes -------------
if ($header=="")
	{
	$headers=get_smart_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		if ((checkperm("f*") || checkperm("f" . $headers[$n]["ref"]))
		&& !checkperm("f-" . $headers[$n]["ref"]))
			{
			?>
			<div class="RecordBox">
			<div class="RecordPanel">  

			<div class="RecordHeader">
			<h1 style="margin-top:5px;"><?=str_replace("*","",i18n_get_translated($headers[$n]["smart_theme_name"]))?></h1>
			</div>
		
			<div class="Listview" style="margin-top:10px;margin-bottom:10px;clear:left;">
			<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
			<tr class="ListviewBoxedTitleStyle">
			<td><?=$lang["name"]?></td>
			<td><div class="ListTools"><?=$lang["tools"]?></div></td>
			</tr>
			
			<?
			$themes=get_smart_themes($headers[$n]["ref"]);
			for ($m=0;$m<count($themes);$m++)
				{
				$s=$headers[$n]["name"] . ":" . $themes[$m]["name"];

				# Indent this item?				
				$indent=str_pad("",$themes[$m]["indent"]*5," ") . ($themes[$m]["indent"]==0?"":"&#746;") . "&nbsp;";
				$indent=str_replace(" ","&nbsp;",$indent);

				?>
				<tr>
				<td><div class="ListTitle"><?=$indent?><a href="search.php?search=<?=urlencode($s)?>&resetrestypes=true"><?=i18n_get_translated($themes[$m]["name"])?></a></div></td>
				<td><div class="ListTools"><a href="search.php?search=<?=urlencode($s)?>&resetrestypes=true">&gt;&nbsp;<?=$lang["action-view"]?></a></div></td>
				</tr>
				<?
				}
			?>
			</table>
			</div>
			
			</div>
			<div class="PanelShadow"> </div>
			</div>
			<?
			}
		}
	}

function DisplayTheme($theme1,$theme2="",$theme3="")
	{
	global $lang,$flag_new_themes,$contact_sheet,$theme_images,$allow_share,$zipcommand;

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
		<?
		$image=get_theme_image($theme1, $theme2, $theme3);
		if (($image) && ($theme_images))
			{
			?><div style="float:left;margin-right:12px;"><img class="CollectImageBorder" src="<?=$image?>" /></div><?
			}
		?>
		<h1 style="margin-top:12px;float:left;"><?=stripslashes(str_replace("*","",$themename))?></h1>
		</div>
		
		<div class="Listview" style="margin-top:10px;margin-bottom:5px;clear:left;">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewBoxedTitleStyle">
		<td><?=$lang["name"]?></td>
		<td width="5%"><?=$lang["itemstitle"]?></td>
		<td><div class="ListTools"><?=$lang["tools"]?></div></td>
		</tr>
		
		<?
		for ($m=0;$m<count($themes);$m++)
			{
			?>
			<tr>
			<td width="50%"><div class="ListTitle"><a href="search.php?search=!collection<?=$themes[$m]["ref"]?>&bc_from=themes"  title="<?=$lang["collectionviewhover"]?>"><?=htmlspecialchars($themes[$m]["name"])?></a>
			<? if ($flag_new_themes && (time()-strtotime($themes[$m]["created"]))<(60*60*24*30)) { ?><div class="NewFlag"><?=$lang["newflag"]?></div><? } ?>
			</div></td>
			<td width="5%"><?=$themes[$m]["c"]?></td>
			
			<td nowrap><div class="ListTools"><a href="search.php?search=<?=urlencode("!collection" . $themes[$m]["ref"])?>" title="<?=$lang["collectionviewhover"]?>">&gt;&nbsp;<?=$lang["action-view"]?></a>
			
			<? if (!checkperm("b")) { ?>&nbsp;<?=change_collection_link($themes[$m]["ref"])?>&gt;&nbsp;<?=$lang["action-select"]?></a><? } ?>
		
			<? if (isset($zipcommand)) { ?>
			&nbsp;<a href="collection_download.php?collection=<?=$themes[$m]["ref"]?>"
			>&gt;&nbsp;<?=$lang["action-download"]?></a>
			<? } ?>
			
			<? if ($contact_sheet==true) { ?>
			&nbsp;<a href="contactsheet_settings.php?c=<?=$themes[$m]["ref"]?>"  title="<?=$lang["collectioncontacthover"]?>">&gt;&nbsp;<?=$lang["contactsheet"]?></a>
			<? } ?>
		
			<? if ($allow_share && (checkperm("v") || checkperm ("g"))) { ?> &nbsp;<a href="collection_share.php?ref=<?=$themes[$m]["ref"]?>" target="main">&gt;&nbsp;<?=$lang["share"]?></a><?}?>
		
			<? if (checkperm("h")) {?>&nbsp;<a href="collection_edit.php?ref=<?=$themes[$m]["ref"]?>">&gt;&nbsp;<?=$lang["action-edit"]?></a><?}?>
		
			<? hook("addcustomtool"); ?>
			
			</td>
			</tr>
			<?
			}
		?>
		</table>
		</div>
		
		</div>
		<div class="PanelShadow"> </div>
		</div>
		<?
		}
	}
?>

</form>
</div>

<? if (!checkperm("b") && $enable_public_collections) { ?>
<div class="clearerleft"> </div>
<div class="BasicsBox">
	<h2>&nbsp;</h2>
    <h1><?=$lang["findapubliccollection"]?></h1>
    <p class="tight"><?=text("findpublic")?></p>
    <p><a href="collection_public.php"><?=$lang["findapubliccollection"]?>&nbsp;&gt;</a></p>
</div>
<? } ?>

<?
include "../include/footer.php";
?>