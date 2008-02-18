<?
# Timelist collection functionality
# Dan Huby for Mediaset, 7 November 2007

$result=sql_query("select * from collection_timelist where collection='$usercollection' order by added");

# loop and display the results
for ($n=0;$n<count($result);$n++)			
	{
	$ref=$result[$n]["resource"];
	?>
	<!--Resource Panel-->
	<div class="CollectionPanelShell">
	<table border="0" class="CollectionResourceAlign"><tr><td>
	<a target="main" href="view.php?ref=<?=$ref?>&search=<?=urlencode("!collection" . $usercollection)?>&k=<?=$k?>"> <img border=0 src="timelist_outimage.php?ref=<?=urlencode($ref)?>&timecode=<?=urlencode($result[$n]["timecode_in"])?>" class="CollectImageBorder"/></a></td>
	</tr></table>
	<div class="CollectionPanelInfo"><a target="main" href="view.php?ref=<?=$ref?>&search=<?=urlencode("!collection" . $usercollection)?>&k=<?=$k?>"><?=tidy_trim($result[$n]["description"],14)?></a>&nbsp;</div>
	<? if ($k=="") { ?><div class="CollectionPanelInfo"><a href="collections.php?removetimelistresource=<?=$ref?>&removetimelist=<?=urlencode($result[$n]["timecode_in"])?>&nc=<?=time()?>">x <?=$lang["action-remove"]?></a></div><? } ?>			
	</div>
	
	<?		
	}
?>