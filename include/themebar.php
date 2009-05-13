<?php 
include_once("collections_functions.php");

?>
<div id="ThemeBox">

<div id="ThemeBoxPanel">

	<div class="SearchSpace">
				<?php if ($pagename=="team_home"||$pagename=="team_related_keywords"||$pagename=="team_research"||$pagename=="team_export"||$pagename=="team_mail"||$pagename=="team_report"||$pagename=="team_stats"||$pagename=="team_content"||$pagename=="team_user"||$pagename=="team_research"||$pagename=="team_archive"||$pagename=="team_resource"||$pagename=="user_password"){$cd="../";}else{$cd="";}?>


<h2><?php echo $lang["themes"] ?></h2>
<?php

# Display all themes
$headers=get_theme_headers();
for ($n=0;$n<count($headers);$n++)
	{
	DisplayThemeBar($headers[$n]);
	}

function DisplayThemeBar($theme1)
	{
	global $lang,$flag_new_themes,$contact_sheet,$theme_images,$allow_share,$zipcommand;

	# Work out theme name
	$themename=$theme1;

	$themes=get_themes($theme1);
	if (count($themes)>0)
		{
		?>
		<br><b><?php echo stripslashes(str_replace("*","",$themename))?></b><br>

		<?php
		for ($m=0;$m<count($themes);$m++)
			{
			?>
			<a href="search.php?search=!collection<?php echo $themes[$m]["ref"]?>&bc_from=themes"  title="<?php echo $lang["collectionviewhover"]?>"><?php echo htmlspecialchars($themes[$m]["name"])?></a><br>
			<?php
			}
		?><?php
		}
	}

?>


<?php
# ------- Smart Themes -------------
    if(!function_exists("get_smart_theme_headers")){include("collections_functions.php");}
	$headers=get_smart_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		if ((checkperm("f*") || checkperm("f" . $headers[$n]["ref"]))
		&& !checkperm("f-" . $headers[$n]["ref"]))
			{
			$header_name="";
			$header_name=$headers[$n]["smart_theme_name"];
			$smart_theme_display="";
			$smart_theme_display=getval("smart_theme_$n","off");
			#echo $smart_theme_display;
			?>
<div 
onclick="SetCookie('smart_theme_<?php echo $n?>',<?php if ($smart_theme_display=='off') {?>'on'<?php } else { ?>'off'<?php }?>,1000);
$('<?php echo $header_name?>').toggle();
return false;"> 

			<?php echo "<B>".str_replace("*","",i18n_get_translated($headers[$n]["smart_theme_name"]))."</B><br>"?></div>
		
<div id="<?php echo $header_name?>" style="display:<?php if ($smart_theme_display == 'off'){echo 'none';} else {echo '';}?>" > 
			<?php
			$themes=get_smart_themes($headers[$n]["ref"]);
			for ($m=0;$m<count($themes);$m++)
				{
				$s=$headers[$n]["name"] . ":" . $themes[$m]["name"];

				# Indent this item?				
				$indent=str_pad("",$themes[$m]["indent"]*5," ") . ($themes[$m]["indent"]==0?"":"&#746;") . "&nbsp;";
				$indent=str_replace(" ","&nbsp;",$indent);

				?>
				<br>

				<?php echo $indent?>&nbsp;<a href="<?php echo $cd?>search.php?search=<?php echo urlencode($s)?>"><?php echo i18n_get_translated($themes[$m]["name"])?></a><?php echo $indent?>
				<?php
				}
			?><br><br>
</div>

			<?php
			}
		}

// bottom hook content:
?>

