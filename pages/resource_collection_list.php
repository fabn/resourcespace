<?php $collections=get_resource_collections($ref);

if (count($collections)!=0){
?>

        <div class="RecordBox">
        <div class="RecordPanel">  
        <div class="Title"><?php echo $lang['associatedcollections']?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["collectionname"]?></td>
<td><?php echo $lang["owner"]?></td>
<td><?php echo $lang["id"]?></td>
<td><?php echo $lang["created"]?></td>
<td><?php echo $lang["itemstitle"]?></td>
<?php if (! $hide_access_column){ ?><td><?php echo $lang["access"]?></td><?php } ?>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>
<?php

for ($n=0;$n<count($collections);$n++)
	{	
	?><tr>
	<td><div class="ListTitle">
    <a <?php if ($collections[$n]["public"]==1 && (strlen($collections[$n]["theme"])>0)) { ?>style="font-style:italic;"<?php } ?> href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>"><?php echo $collections[$n]["name"]?></a></div></td>
	<td><?php echo $collections[$n]["username"]?></td>
	<td><?php echo $collection_prefix . $collections[$n]["ref"]?></td>
	<td><?php echo nicedate($collections[$n]["created"],true)?></td>
	<td><?php echo $collections[$n]["count"]?></td>
<?php if (! $hide_access_column){ ?>	<td><?php
# Work out the correct access mode to display
if ($collections[$n]["public"]==0)
	{
	echo $lang["private"];
	}
else
	{
	if (strlen($collections[$n]["theme"])>0)
		{
		echo $lang["theme"];
		}
	else
		{
		echo $lang["public"];
		}
	}
?></td><?php
}
?>
	<td><div class="ListTools">
    <?php if ($collections_compact_style){
    include("collections_compact_style.php"); } else { ?>
    <a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["viewall"]?></a>
	&nbsp;<a <?php if ($frameless_collections && !checkperm("b")){ ?>href onclick="ChangeCollection(<?php echo $collections[$n]["ref"]?>);"
		<?php } elseif ($autoshow_thumbs) {?>onclick=" top.document.getElementById('topframe').rows='*<?php if ($collection_resize!=true) {?>,3<?php } ?>,138'; return true;"
		href="collections.php?collection=<?php echo $collections[$n]["ref"]?>&amp;thumbs=show" target="collections"
		<?php } else {?>href="collections.php?collection=<?php echo $collections[$n]["ref"]?>" target="collections"<?php }?>>&gt;&nbsp;<?php echo $lang["action-select"]?></a>
	<?php if (isset($zipcommand)) { ?>
	&nbsp;<a href="collection_download.php?collection=<?php echo $collections[$n]["ref"]?>"
	>&gt;&nbsp;<?php echo $lang["action-download"]?></a>
	<?php } ?>
	
	<?php if ($contact_sheet==true) { ?>
    &nbsp;<a href="contactsheet_settings.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
	<?php } ?>

	<?php if ($allow_share && (checkperm("v") || checkperm ("g"))) { ?> &nbsp;<a href="collection_share.php?ref=<?php echo $collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["share"]?></a><?php } ?>
	
	<!--<?php if ($username!=$collections[$n]["username"])	{?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["removecollectionareyousure"]?>')) {document.getElementById('collectionremove').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-remove"]?></a><?php } ?>-->

	<!--<?php if ((($username==$collections[$n]["username"]) || checkperm("h")) && ($collections[$n]["cant_delete"]==0)) {?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["collectiondeleteconfirm"]?>')) {document.getElementById('collectiondelete').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-delete"]?></a><?php } ?>-->

	<?php if (($username==$collections[$n]["username"]) || (checkperm("h"))) {?>&nbsp;<a href="collection_edit.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?></a>&nbsp;<?php } ?>
    <?php     # If this collection is (fully) editable, then display an edit all link
    if (($collections[$n]["count"] >0) && allow_multi_edit($collections[$n]["ref"]) && $show_edit_all_link ) { ?>
    &nbsp;<a href="edit.php?collection=<?php echo $collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["action-editall"]?></a>&nbsp;<?php } ?>

	<?php if (($username==$collections[$n]["username"]) || (checkperm("h"))) {?><a href="collection_log.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["log"]?></a><?php } ?>
	
	</td>
	</tr><?php
	}
}
?>
</table></div>
        </div>
        <div class="PanelShadow"></div>
        </div>
<?php } ?>
