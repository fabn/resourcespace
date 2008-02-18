<?
$path=get_resource_path($ref,"",false,$resource["file_extension"]);
if (file_exists($path))
	{
	?>
	<table cellpadding="0" cellspacing="0">
	<tr>
	<td><?=$lang["fileinformation"]?></td>
	<td><?=$lang["filesize"]?></td>
	<td><?=$lang["options"]?></td>
	</tr>
	
	<tr class="DownloadDBlend">
	<td rowspan=2><h2><?=strtoupper($resource["file_extension"])?> Video <?=$lang["file"]?></h2></td>
	<td rowspan=2><?=formatfilesize(filesize($path))?></td>
	<td class="DownloadButton HorizontalWhiteNav"><a href="terms.php?url=<?=urlencode("download_progress.php?ref=" . $ref . "&ext=" . $resource["file_extension"] . "&k=" . $k)?>">Download</a></td>
	</tr>
	
	<tr class="DownloadDBlend">
	<td class="DownloadButton HorizontalWhiteNav"><a href="download.php?ref=<?=$ref?>&ext=<?=$resource["file_extension"]?>&k=<?=$k?>&noattach=true">Play</a></td>
	</tr>
	</table>
	<?
	}
?>
<xembed src="download.php?ref=<?=$ref?>&ext=<?=$resource["file_extension"]?>&k=<?=$k?>&noattach=true">

