<?
include "include/db.php";
include "include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "include/general.php";
include "include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");

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

include "include/header.php";
?>
  <div class="BasicsBox">
    <h2>&nbsp;</h2>
    <h1><?=$lang["findpubliccollection"]?></h1>
    <p class="tight"><?=text("introtext")?></p>

<?
$collections=search_public_collections($find);

# pager
$per_page=15;
$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;

$url="collection_public.php?find=" . urlencode($find);
$jumpcount=1;

?><div class="TopInpageNav"><? pager();	?></div>

<form method=post id="collectionform">
<input type=hidden name="add" id="collectionadd" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<tr class="ListviewTitleStyle">
<td><?=$lang["collectionname"]?></td>
<td><?=$lang["owner"]?></td>
<td><?=$lang["id"]?></td>
<td><?=$lang["created"]?></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>
<?

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="search.php?search=<?=urlencode("!collection" . $collections[$n]["ref"])?>"><?=$collections[$n]["name"]?></a></div></td>
	<td><?=$collections[$n]["username"]?></td>
	<td><?=$collections[$n]["ref"]?></td>
	<td><?=nicedate($collections[$n]["created"])?></td>
	<td><div class="ListTools"><a href="search.php?search=<?=urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?=$lang["action-view"]?></a>
	&nbsp;&nbsp;
	<a href="#" onclick="document.getElementById('collectionadd').value='<?=$collections[$n]["ref"]?>';document.getElementById('collectionform').submit(); return false;">&gt;&nbsp;<?=$lang["addtomycollections"]?></a></div></td>
	</tr>
	<?
	}
?>
</table>
</div>

</form>
<div class="BottomInpageNav"><? pager(false); ?></div>
</div>

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
include "include/footer.php";
?>