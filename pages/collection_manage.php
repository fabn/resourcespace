<?php 
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";
if ($video_playlists){include "../include/search_functions.php";}

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$order_by=getvalescaped("order_by","name");
$sort=getval("sort","ASC");
$revsort = ($sort=="ASC") ? "DESC" : "ASC";
if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting

$name=getvalescaped("name","");
if ($name!="")
	{
	# Create new collection
	$new=create_collection ($userref,$name);
	set_user_collection($userref,$new);
	refresh_collection_frame();
	
	# Log this
	daily_stat("New collection",$userref);
	
	redirect("pages/collection_edit.php?ref=" . $new);
	}

$delete=getvalescaped("delete","");
if ($delete!="")
	{
	# Delete collection
	delete_collection($delete);

	# Get count of collections
	$c=get_user_collections($userref);
	
	# If the user has just deleted the collection they were using, select a new collection
	if ($usercollection==$delete && count($c)>0)
		{
		# Select the first collection in the dropdown box.
		$usercollection=$c[0]["ref"];
		set_user_collection($userref,$usercollection);
		}

	# User has deleted their last collection? add a new one.
	if (count($c)==0)
		{
		# No collections to select. Create them a new collection.
		$name=get_mycollection_name($userref);
		$usercollection=create_collection ($userref,$name);
		set_user_collection($userref,$usercollection);
		}

	refresh_collection_frame($usercollection);
	}

$remove=getvalescaped("remove","");
if ($remove!="")
	{
	# Remove someone else's collection from your My Collections
	remove_collection($userref,$remove);
	refresh_collection_frame();
	}

$add=getvalescaped("add","");
if ($add!="")
	{
	# Add someone else's collection to your My Collections
	add_collection($userref,$add);
	set_user_collection($userref,$add);
	refresh_collection_frame();
	
   	# Log this
	daily_stat("Add public collection",$userref);
	}

$reload=getvalescaped("reload","");
if ($reload!="")
	{
	# Refresh the collection frame (just edited a collection)
	refresh_collection_frame();
	}

include "../include/header.php";
?>
  <div class="BasicsBox">
    <h2>&nbsp;</h2>
    <h1><?php echo $lang["managemycollections"]?></h1>
    <p class="tight"><?php echo text("introtext")?></p><br>
<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<?php

$collections=get_user_collections($userref,$find,$order_by,$sort);
# pager
$per_page=15;
$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}
$atoz.="<a href=\"collection_manage.php?order_by=name&find=\">" . $lang["viewall"] . "</a>";
if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"collection_manage.php?order_by=name&find=" . chr($n) . "\">" . chr($n) . "</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

$url="collection_manage.php?paging=true&order_by=".$order_by."&sort=".$sort."&find=".urlencode($find)."";

	?><div class="TopInpageNav"><?php echo $atoz?><?php pager(false); ?></div><?php	
?>

<form method=post id="collectionform">
<input type=hidden name="delete" id="collectiondelete" value="">
<input type=hidden name="remove" id="collectionremove" value="">
<input type=hidden name="add" id="collectionadd" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php if ($order_by=="name") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&order_by=name&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["collectionname"]?></a><?php if ($order_by=="name") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="user") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&order_by=user&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["owner"]?></a><?php if ($order_by=="user") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="ref") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&order_by=ref&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["id"]?></a><?php if ($order_by=="ref") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="created") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&order_by=created&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["created"]?></a><?php if ($order_by=="created") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="count") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&order_by=count&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["itemstitle"]?></a><?php if ($order_by=="count") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<?php if (! $hide_access_column){ ?><td><?php if ($order_by=="public") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&order_by=public&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["access"]?></a><?php if ($order_by=="public") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td><?php }?>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>
<?php

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
	if($video_playlists){$videocount=get_collection_videocount($collections[$n]["ref"]);}else{$videocount="";}		
	?><tr>
	<td><div class="ListTitle">
    <a <?php if ($collections[$n]["public"]==1 && (strlen($collections[$n]["theme"])>0)) { ?>style="font-style:italic;"<?php } ?> href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>"><?php echo highlightkeywords($collections[$n]["name"],$find)?></a></div></td>
	<td><?php echo highlightkeywords($collections[$n]["username"],$find)?></td>
	<td><?php echo highlightkeywords($collection_prefix . $collections[$n]["ref"],$find)?></td>
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
	<td><div class="ListTools"><a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["action-view"]?></a>
	&nbsp;<a <?php if ($frameless_collections && !checkperm("b")){ ?>href onclick="ChangeCollection(<?php echo $collections[$n]["ref"]?>);"
		<?php } elseif ($autoshow_thumbs) {?>onclick=" top.document.getElementById('topframe').rows='*<?php if ($collection_resize!=true) {?>,3<?php } ?>,138'; return true;"
		href="collections.php?collection=<?php echo $collections[$n]["ref"]?>&amp;thumbs=show" target="collections"
		<?php } else {?>href="collections.php?collection=<?php echo $collections[$n]["ref"]?>" target="collections"<?php }?>>&gt;&nbsp;<?php echo $lang["action-select"]?></a>
	<?php if (isset($zipcommand)) { ?>
	&nbsp;<a href="collection_download.php?collection=<?php echo $collections[$n]["ref"]?>"
	>&gt;&nbsp;<?php echo $lang["action-download"]?></a>
	<?php } ?>
	
	<?php if ($videocount>0) { ?>
    &nbsp;<a href="video_playlist.php?c=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["videoplaylist"]?></a>
	<?php } ?>
	
	<?php if ($contact_sheet==true) { ?>
    &nbsp;<a href="contactsheet_settings.php?c=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
	<?php } ?>

	<?php if ($allow_share && (checkperm("v") || checkperm ("g"))) { ?> &nbsp;<a href="collection_share.php?ref=<?php echo $collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["share"]?></a><?php } ?>
	
	<?php if ($username!=$collections[$n]["username"])	{?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["removecollectionareyousure"]?>')) {document.getElementById('collectionremove').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-remove"]?></a><?php } ?>

	<?php if ((($username==$collections[$n]["username"]) || checkperm("h")) && ($collections[$n]["cant_delete"]==0)) {?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["collectiondeleteconfirm"]?>')) {document.getElementById('collectiondelete').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-delete"]?></a><?php } ?>

	<?php if (($username==$collections[$n]["username"]) || (checkperm("h"))) {?>&nbsp;<a href="collection_edit.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?></a><?php } ?>
    <?php     # If this collection is (fully) editable, then display an edit all link
    if (($collections[$n]["count"] >0) && allow_multi_edit($collections[$n]["ref"]) && $show_edit_all_link ) { ?>
    &nbsp;<a href="edit.php?collection=<?php echo $collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["editall"]?></a>&nbsp;<?php } ?>

	<?php if (($username==$collections[$n]["username"]) || (checkperm("h"))) {?><a href="collection_log.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["log"]?></a><?php } ?>

	<?php hook("addcustomtool"); ?>
	
	</td>
	</tr><?php
	}
?>
</table>
</div>

</form>
<div class="BottomInpageNav"><?php pager(false); ?></div>
</div>

<!--Create a collection-->
<div class="BasicsBox">
    <h1><?php echo $lang["createnewcollection"]?></h1>
    <p class="tight"><?php echo text("newcollection")?></p>
    <form method="post">
		<div class="Question">
			<label for="newcollection"><?php echo $lang["collectionname"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="name" id="newcollection" value="" maxlength="100" class="shrtwidth"></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
		<div class="clearerleft"> </div>
	    </div>
	</form>
</div>

<!--Find a collection-->
<?php if($enable_public_collections){?>
<div class="BasicsBox">
    <h1><?php echo $lang["findpubliccollection"]?></h1>
    <p class="tight"><?php echo text("findpublic")?></p>
    <p><a href="collection_public.php"><?php echo $lang["findpubliccollection"]?>&nbsp;&gt;</a></p>
</div>
<?php } ?>

<?php		
include "../include/footer.php";
?>
