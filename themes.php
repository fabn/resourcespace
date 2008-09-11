<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/collections_functions.php";

$header=getvalescaped("header","");
$theme=getvalescaped("theme","");

include "include/header.php";
?>


<div class="BasicsBox"> 
<form method=post id="themeform">

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
	<label for="theme"><?=$lang["themecategory"]?></label>
	<select xclass="stdwidth" name="theme" id="theme" onchange="document.getElementById('themeform').submit();">
	<?
	if ($theme=="")
		{
		?><option value=""><?=$lang["select"]?></option><?
		}
	
	# Level 1 headers
	$headers=get_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		$value=($headers[$n]);
		?><option value="<?=$value?>" <? if ($theme==$value)  { ?>selected<? } ?>><?=$headers[$n]?></option><?
		# Level 2 headers
		$headers2=get_theme_headers($headers[$n]);
		for ($m=0;$m<count($headers2);$m++)
			{
			$value=($headers[$n] . ";;" . $headers2[$m]);
			?><option value="<?=$value?>" <? if ($theme==$value)  { ?>selected<? } ?> >&nbsp;&nbsp;&nbsp;&#746;&nbsp;<?=$headers2[$m]?></option><?
			# Level 3 headers
			$headers3=get_theme_headers($headers[$n],$headers2[$m]);
			for ($o=0;$o<count($headers3);$o++)
				{
				$value=($headers[$n] . ";;" . $headers2[$m] . ";;" . $headers3[$o]);
				?><option value="<?=$value?>" <? if ($theme==$value)  { ?>selected<? } ?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#746;&nbsp;<?=$headers3[$o]?></option><?
				}
			}
		}
	?>
	</select>
	<div class="clearerleft"> </div>
	</div>
	
	</div>
	</div>
	<?
	}

# Display Themes

if ($theme!="")
	{
	# Display just the selected theme
	DisplayTheme($theme);
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
if ($header=="")
	{
	$headers=get_smart_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		if (checkperm("f*") || checkperm("f" . $headers[$n]["ref"]))
			{
			?>
			<div class="RecordBox">
			<div class="RecordPanel">  

			<div class="RecordHeader">
			<h1 style="margin-top:5px;"><?=i18n_get_translated($headers[$n]["smart_theme_name"])?></h1>
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
				$s=$headers[$n]["name"] . ":" . $themes[$m];
				?>
				<tr>
				<td><div class="ListTitle"><a href="search.php?search=<?=urlencode($s)?>"><?=htmlspecialchars(i18n_get_translated($themes[$m]))?></a></div></td>
				<td><div class="ListTools"><a href="search.php?search=<?=urlencode($s)?>">&gt;&nbsp;<?=$lang["action-view"]?></a></div></td>
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

function DisplayTheme($theme)
	{
	global $lang,$flag_new_themes,$contact_sheet,$theme_images;
	
	$theme2="";$theme3="";
	if (strpos($theme,";;")!==false)
		{
		# Multiple levels of themes. Expand and set vars.
		$ts=explode(";;",$theme);
		$theme=$ts[0];
		$theme2=$ts[1];
		if (count($ts)==3) {$theme3=$ts[2];}
		}

	# Work out theme name
	if ($theme!="") {$themename=$theme;}
	if ($theme2!="") {$themename=$theme2;}
	if ($theme3!="") {$themename=$theme3;}

	$themes=get_themes($theme,$theme2,$theme3);
	if (count($themes)>0)
		{
		?>
		<div class="RecordBox">
		<div class="RecordPanel">  
		
		<div class="RecordHeader">
		<?
		$image=get_theme_image($theme, $theme2, $theme3);
		if (($image) && ($theme_images))
			{
			?><div style="float:left;margin-right:12px;"><img class="CollectImageBorder" src="<?=$image?>" /></div><?
			}
		?>
		<h1 style="margin-top:12px;float:left;"><?=$themename?></h1>
		</div>
		
		<div class="Listview" style="margin-top:10px;margin-bottom:5px;clear:left;">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewBoxedTitleStyle">
		<td><?=$lang["name"]?></td>
		<td width="5%"><?=$lang["items"]?></td>
		<td><div class="ListTools"><?=$lang["tools"]?></div></td>
		</tr>
		
		<?
		for ($m=0;$m<count($themes);$m++)
			{
			?>
			<tr>
			<td width="50%"><div class="ListTitle"><a href="search.php?search=!collection<?=$themes[$m]["ref"]?>&bc_from=themes"><?=htmlspecialchars($themes[$m]["name"])?></a>
			<? if ($flag_new_themes && (time()-strtotime($themes[$m]["created"]))<(60*60*24*30)) { ?><div class="NewFlag"><?=$lang["newflag"]?></div><? } ?>
			</div></td>
			<td width="5%"><?=$themes[$m]["c"]?></td>
			
			<td nowrap><div class="ListTools"><a href="search.php?search=<?=urlencode("!collection" . $themes[$m]["ref"])?>">&gt;&nbsp;<?=$lang["action-view"]?></a>
			
			&nbsp;<a href="collections.php?collection=<?=$themes[$m]["ref"]?>" target="collections">&gt;&nbsp;<?=$lang["action-select"]?></a>
		
			<? if (isset($zipcommand)) { ?>
			&nbsp;<a href="collection_download.php?collection=<?=$themes[$m]["ref"]?>"
			>&gt;&nbsp;<?=$lang["action-download"]?></a>
			<? } ?>
			
			<? if ($contact_sheet==true) { ?>
			&nbsp;<a href="contactsheet_settings.php?c=<?=$themes[$m]["ref"]?>">&gt;&nbsp;<?=$lang["contactsheet"]?></a>
			<? } ?>
		
			<? if (checkperm("v") || checkperm ("g")) { ?> &nbsp;<a href="collection_share.php?ref=<?=$themes[$m]["ref"]?>" target="main">&gt;&nbsp;<?=$lang["share"]?></a><?}?>
		
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

<div class="clearerleft"> </div>
<div class="BasicsBox">
	<h2>&nbsp;</h2>
    <h1><?=$lang["findapubliccollection"]?></h1>
    <p class="tight"><?=text("findpublic")?></p>
    <p><a href="collection_public.php"><?=$lang["findapubliccollection"]?>&nbsp;&gt;</a></p>
</div>

<?
include "include/footer.php";
?>