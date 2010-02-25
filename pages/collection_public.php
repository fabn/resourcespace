<?php
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$order_by=getvalescaped("order_by","created");
$override_group_restrict=getvalescaped("override_group_restrict","false");
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
    <h1><?php echo $lang["findpubliccollection"]?></h1>
    <p class="tight"><?php echo text("introtext")?></p>
<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="find"><?php echo $lang["searchpubliccollections"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<?php
$collections=search_public_collections($find,$order_by,$sort,$public_collections_exclude_themes,false,false,$override_group_restrict=="true");

# pager
$per_page=15;
$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}

if ($public_collections_confine_group)
	{
	$atoz.="<a href=\"collection_public.php?order_by=name&override_group_restrict=false&find=\">" . $lang["viewmygroupsonly"] . "</a> &nbsp; | &nbsp;";	
	$atoz.="<a href=\"collection_public.php?order_by=name&override_group_restrict=true&find=\">" . $lang["viewall"] . "</a> &nbsp;&nbsp;&nbsp;";	
	}
else
	{
	$atoz.="<a href=\"collection_public.php?order_by=name&find=\">" . $lang["viewall"] . "</a>";
	}


if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"collection_public.php?order_by=name&find=" . chr($n) . "&override_group_restrict=" . $override_group_restrict . "\">" . chr($n) . "</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

$url="collection_public.php?paging=true&order_by=".$order_by."&sort=".$sort."&find=".urlencode($find)."&override_group_restrict=" . $override_group_restrict;
?><div class="TopInpageNav"><?php echo $atoz?><?php pager(false); ?></div>

<form method=post id="collectionform">
<input type=hidden name="add" id="collectionadd" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php if ($order_by=="name") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&order_by=name&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["collectionname"]?></a><?php if ($order_by=="name") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="user") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&order_by=user&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["owner"]?></a><?php if ($order_by=="user") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="ref") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&order_by=ref&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["id"]?></a><?php if ($order_by=="ref") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="created") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&order_by=created&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["created"]?></a><?php if ($order_by=="created") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($order_by=="public") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&order_by=public&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["access"]?></a><?php if ($order_by=="public") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>
<?php

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<td><div class="ListTitle">
	<a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>"><?php echo highlightkeywords($collections[$n]["name"],$find)?></a></div></td>

	<td><?php echo highlightkeywords($collections[$n]["username"],$find)?></td>
	<td><?php echo highlightkeywords($collections[$n]["ref"],$find)?></td>
	<td><?php echo nicedate($collections[$n]["created"],true)?></td>
	<td><?php echo ($collections[$n]["public"]==0)?$lang["private"]:$lang["public"]?></td>
	<td><div class="ListTools"><a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["action-view"]?></a>
	<?php if ($contact_sheet==true) { ?>
   &nbsp;<a href="contactsheet_settings.php?c=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
	<?php } ?>
	<?php if (!checkperm("b")) { ?>
	&nbsp;<a href="#" onclick="document.getElementById('collectionadd').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit(); return false;">&gt;&nbsp;<?php echo $lang["addtomycollections"]?></a><?php } ?>
	</div></td>
	</tr>
	<?php
	}
?>
</table>
</div>

</form>
<div class="BottomInpageNav"><?php pager(false); ?></div>
</div>

<?php		
include "../include/footer.php";
?>
