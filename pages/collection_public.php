<?
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$order_by=getvalescaped("order_by","created");
$sort=getval("sort","ASC");
$revsort = ($sort=="ASC") ? "DESC" : "ASC";
if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting

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

include "../include/header.php";
?>
  <div class="BasicsBox">
    <h2>&nbsp;</h2>
    <h1><?=$lang["findpubliccollection"]?></h1>
    <p class="tight"><?=text("introtext")?></p>
<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="find"><?=$lang["searchpubliccollections"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?=$find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<?
$collections=search_public_collections($find,$order_by,$sort);

# pager
$per_page=15;
$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}
$atoz.="<a href=\"collection_public.php?order_by=name&find=\">" . $lang["viewall"] . "</a>";
if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"collection_public.php?order_by=name&find=" . chr($n) . "\">" . chr($n) . "</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

$url="collection_public.php?paging=true&order_by=".$order_by."&sort=".$sort."&find=".urlencode($find)."";
?><div class="TopInpageNav"><?=$atoz?><? pager(false); ?></div>

<form method=post id="collectionform">
<input type=hidden name="add" id="collectionadd" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><a href="collection_public.php?offset=0&order_by=name&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["collectionname"]?></a></td>
<td><a href="collection_public.php?offset=0&order_by=user&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["owner"]?></a></td>
<td><a href="collection_public.php?offset=0&order_by=ref&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["id"]?></a></td>
<td><a href="collection_public.php?offset=0&order_by=created&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["created"]?></a></td>
<td><a href="collection_public.php?offset=0&order_by=public&sort=<?=$revsort?>&find=<?=urlencode($find)?>"><?=$lang["access"]?></a></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>
<?

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<td><div class="ListTitle">
	<a href="collections.php?collection=<?=$collections[$n]["ref"]?>" target="collections"><?=highlightkeywords($collections[$n]["name"],$find)?></a></div></td>

	<td><?=highlightkeywords($collections[$n]["username"],$find)?></td>
	<td><?=highlightkeywords($collections[$n]["ref"],$find)?></td>
	<td><?=nicedate($collections[$n]["created"],true)?></td>
	<td><?=($collections[$n]["public"]==0)?$lang["private"]:$lang["public"]?></td>
	<td><div class="ListTools"><a href="search.php?search=<?=urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?=$lang["action-view"]?></a>
	<? if ($contact_sheet==true) { ?>
   &nbsp;<a href="contactsheet_settings.php?c=<?=$collections[$n]["ref"]?>">&gt;&nbsp;<?=$lang["contactsheet"]?></a>
	<? } ?>
	<? if (!checkperm("b")) { ?>
	&nbsp;<a href="#" onclick="document.getElementById('collectionadd').value='<?=$collections[$n]["ref"]?>';document.getElementById('collectionform').submit(); return false;">&gt;&nbsp;<?=$lang["addtomycollections"]?></a><? } ?>
	</div></td>
	</tr>
	<?
	}
?>
</table>
</div>

</form>
<div class="BottomInpageNav"><? pager(false); ?></div>
</div>

<?		
include "../include/footer.php";
?>