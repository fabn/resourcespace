<?
include "include/db.php";
include "include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "include/general.php";
include "include/collections_functions.php";

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
	
	redirect("collection_edit.php?ref=" . $new);
	}

$delete=getvalescaped("delete","");
if ($delete!="")
	{
	# Delete collection
	delete_collection($delete);
	refresh_collection_frame();
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

include "include/header.php";
?>
  <div class="BasicsBox">
    <h2>&nbsp;</h2>
    <h1><?=$lang["managemycollections"]?></h1>
    <p class="tight"><?=text("introtext")?></p><br>
<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?=$find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<?

$collections=get_user_collections($userref,$find,$order_by,$sort);
# pager
$per_page=15;
$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="collection_manage.php?paging=true";
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

	?><div class="TopInpageNav"><?=$atoz?><? pager(false); ?></div><?	
?>

<form method=post id="collectionform">
<input type=hidden name="delete" id="collectiondelete" value="">
<input type=hidden name="remove" id="collectionremove" value="">
<input type=hidden name="add" id="collectionadd" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><a href="collection_manage.php?offset=0&order_by=name&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["collectionname"]?></a></td>
<td><a href="collection_manage.php?offset=0&order_by=user&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["owner"]?></a></td>
<td><a href="collection_manage.php?offset=0&order_by=ref&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["id"]?></a></td>
<td><a href="collection_manage.php?offset=0&order_by=created&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["created"]?></a></td>
<td><a href="collection_manage.php?offset=0&order_by=count&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["itemstitle"]?></a></td>
<td><a href="collection_manage.php?offset=0&order_by=public&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["access"]?></a></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>
<?

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
	?><tr>
	<td><div class="ListTitle">
	<a href="collections.php?collection=<?=$collections[$n]["ref"]?>" target="collections"><?=$collections[$n]["name"]?></a></div></td>

	<td><?=$collections[$n]["username"]?></td>
	<td><?=$collections[$n]["ref"]?></td>
	<td><?=nicedate($collections[$n]["created"],true)?></td>
	<td><?=$collections[$n]["count"]?></td>
	<td><?=($collections[$n]["public"]==0)?$lang["private"]:$lang["public"]?></td>
	
	<td><div class="ListTools"><a href="search.php?search=<?=urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?=$lang["action-view"]?></a>
	&nbsp;<a href="collections.php?collection=<?=$collections[$n]["ref"]?>" target="collections">&gt;&nbsp;<?=$lang["action-select"]?></a>
	<? if (isset($zipcommand)) { ?>
	&nbsp;<a href="collection_download.php?collection=<?=$collections[$n]["ref"]?>"
	>&gt;&nbsp;<?=$lang["action-download"]?></a>
	<? } ?>
	
	<? if ($contact_sheet==true) { ?>
    &nbsp;<a href="contactsheet_settings.php?c=<?=$collections[$n]["ref"]?>">&gt;&nbsp;<?=$lang["contactsheet"]?></a>
	<? } ?>

	<? if (checkperm("v") || checkperm ("g")) { ?> &nbsp;<a href="collection_share.php?ref=<?=$collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?=$lang["share"]?></a><?}?>
	
	<? if ($username!=$collections[$n]["username"])	{?>&nbsp;<a href="#" onclick="if (confirm('<?=$lang["removecollectionareyousure"]?>')) {document.getElementById('collectionremove').value='<?=$collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?=$lang["action-remove"]?></a><?}?>

	<? if ((($username==$collections[$n]["name"]) || checkperm("h")) && ($collections[$n]["cant_delete"]==0)) {?>&nbsp;<a href="#" onclick="if (confirm('<?=$lang["collectiondeleteconfirm"]?>')) {document.getElementById('collectiondelete').value='<?=$collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?=$lang["action-delete"]?></a><?} ?>

	<? if (($username==$collections[$n]["name"]) || (checkperm("h"))) {?>&nbsp;<a href="collection_edit.php?ref=<?=$collections[$n]["ref"]?>">&gt;&nbsp;<?=$lang["action-edit"]?></a><?}?>

	<? hook("addcustomtool"); ?>
	
	</td>
	</tr><?
	}
?>
</table>
</div>

</form>
<div class="BottomInpageNav"><? pager(false); ?></div>
</div>

<!--Find a collection-->
<div class="BasicsBox">
    <h1><?=$lang["createnewcollection"]?></h1>
    <p class="tight"><?=text("newcollection")?></p>
    <form method="post">
		<div class="Question">
			<label for="newcollection"><?=$lang["collectionname"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="name" id="newcollection" value="" maxlength="100" class="shrtwidth"></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
		<div class="clearerleft"> </div>
	    </div>
	</form>
</div>

<div class="BasicsBox">
    <h1><?=$lang["findpubliccollection"]?></h1>
    <p class="tight"><?=text("findpublic")?></p>
    <p><a href="collection_public.php"><?=$lang["findpubliccollection"]?>&nbsp;&gt;</a></p>
</div>


<?		
include "include/footer.php";
?>