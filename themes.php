<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/collections_functions.php";

$header=getvalescaped("header","");

include "include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["themes"]?></h1>
  <p><?=text("introtext")?></p>
  
<?
$headers=get_theme_headers();
for ($n=0;$n<count($headers);$n++)
	{
	if ($header=="" || $header==$headers[$n])
		{
		?>
		<div class="RecordBox">
		<div class="RecordPanel">  

		<div class="RecordHeader">
		<?
		$image=get_theme_image($headers[$n]);
		if (($image) && ($theme_images))
			{
			?><div style="float:left;margin-right:12px;"><img class="CollectImageBorder" src="<?=$image?>" /></div><?
			}
		?>
		<h1 style="margin-top:12px;float:left;"><?=$headers[$n]?></h1>
		</div>
		
		<div class="Listview" style="margin-top:10px;margin-bottom:5px;clear:left;">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewTitleStyle">
		<td><?=$lang["name"]?></td>
		<td width="5%"><?=$lang["items"]?></td>
		<td><div class="ListTools"><?=$lang["tools"]?></div></td>
		</tr>
		
		<?
		$themes=get_themes($headers[$n]);
		for ($m=0;$m<count($themes);$m++)
			{
			?>
			<tr>
			<td width="50%"><div class="ListTitle"><a href="search.php?search=!collection<?=$themes[$m]["ref"]?>"><?=htmlspecialchars($themes[$m]["name"])?></a></div></td>
			<td width="5%"><?=$themes[$m]["c"]?></td>
			
			<td><div class="ListTools"><a href="search.php?search=<?=urlencode("!collection" . $themes[$m]["ref"])?>">&gt;&nbsp;<?=$lang["action-view"]?></a>
			
			&nbsp;<a href="collections.php?collection=<?=$themes[$m]["ref"]?>" target="collections">&gt;&nbsp;<?=$lang["action-select"]?></a>
		
			<? if (isset($zipcommand)) { ?>
			&nbsp;<a href="collection_download.php?collection=<?=$themes[$m]["ref"]?>"
			>&gt;&nbsp;<?=$lang["action-download"]?></a>
			<? } ?>
			
			<? if ($contact_sheet==true) { ?>
			&nbsp;<a href="contactsheet_settings.php?c=<?=$themes[$m]["ref"]?>">&gt;&nbsp;<?=strtolower($lang["contactsheet"])?></a>
			<? } ?>
		
			<? if (checkperm("v") || checkperm ("g")) { ?> &nbsp;<a href="collection_email.php?ref=<?=$themes[$m]["ref"]?>" target="main">&gt;&nbsp;<?=$lang["action-email"]?></a><?}?>
		
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
			<tr class="ListviewTitleStyle">
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
?>


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