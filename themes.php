<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/collections_functions.php";

$manage=getval("manage",false);if ($manage!==false) {$manage=true;}

include "include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$manage?$lang["managethemes"]:$lang["themes"]?></h1>
  <p><?=$manage?text("manage"):text("introtext")?></p>
  
<?
$headers=get_theme_headers();
for ($n=0;$n<count($headers);$n++)
	{
	?>
	<? if ($manage) { ?><div class="VerticalNav"><? } else  { ?><div class="ThemeBox"><? } ?>
	<h1 class="NavUnderline" style="margin-bottom:10px;padding-bottom:2px;margin-top:15px;">
	<?
	$image=get_theme_image($headers[$n]);
	if (($image) && ($theme_images))
		{
		?><img class="CollectImageBorder" src="<?=$image?>"><br /><?
		}
	?>
	<?=$headers[$n]?></h1>
	<ul>
	<?
	$themes=get_themes($headers[$n]);
	for ($m=0;$m<count($themes);$m++)
		{
		?>
	    <li><a href="search.php?search=!collection<?=$themes[$m]["ref"]?>"><?=htmlspecialchars($themes[$m]["name"])?></a> <? hook("collectioninfo"); ?>
	    <? if ($manage) { ?>
	    &nbsp;&nbsp;
	    <span class="OxColourPale">
	    <a href="collections.php?collection=<?=$themes[$m]["ref"]?>" target="collections"><span class="OxColourPale">&gt; <?=$lang["editcollection"]?></span></a>&nbsp;
   	    <a href="collection_edit.php?ref=<?=$themes[$m]["ref"]?>"><span class="OxColourPale">&gt; <?=$lang["editproperties"]?></span></a>
	    </span>
	    <? } ?>
	    </li>
	    <?
	    }
	?>
	</ul>
	</div>
	<?
	}
?>

<?
if (!$manage)
	{
	$headers=get_smart_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		?>
		<div class="ThemeBox">
		<h1 class="NavUnderline" style="margin-bottom:10px;padding-bottom:2px;margin-top:15px;">
		<?=i18n_get_translated($headers[$n]["smart_theme_name"])?></h1>
		<ul>
		<?
		$themes=get_smart_themes($headers[$n]["ref"]);
		for ($m=0;$m<count($themes);$m++)
			{
			$s=$headers[$n]["name"] . ":" . $themes[$m];
			?>
			<li><a href="search.php?search=<?=urlencode($s)?>"><?=htmlspecialchars(i18n_get_translated($themes[$m]))?></a>
			</li>
			<?
			}
		?>
		</ul>
		</div>
		<?
		}
	}
?>


</div>

<? if (!$manage) { ?>
<div class="clearerleft"> </div>
<div class="BasicsBox">
	<h2>&nbsp;</h2>
    <h1><?=$lang["findapubliccollection"]?></h1>
    <p class="tight"><?=text("findpublic")?></p>
    <p><a href="collection_public.php"><?=$lang["findapubliccollection"]?>&nbsp;&gt;</a></p>
</div>
<? } ?>

<?
include "include/footer.php";
?>