<?php
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$col_order_by=getvalescaped("col_order_by","created");
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
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["searchbutton"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<?php
$collections=search_public_collections($find,$col_order_by,$sort,$public_collections_exclude_themes,false,true,$override_group_restrict=="true");

# pager
$per_page=15;
if ($collection_dropdown_user_access_mode){$per_page=10;}
$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}

if ($public_collections_confine_group)
	{
	$atoz.="<a href=\"collection_public.php?col_order_by=name&override_group_restrict=false&find=\">" . $lang["viewmygroupsonly"] . "</a> &nbsp; | &nbsp;";	
	$atoz.="<a href=\"collection_public.php?col_order_by=name&override_group_restrict=true&find=\">" . $lang["viewall"] . "</a> &nbsp;&nbsp;&nbsp;";	
	}
else
	{
	$atoz.="<a href=\"collection_public.php?col_order_by=name&find=\">" . $lang["viewall"] . "</a>";
	}


if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"collection_public.php?col_order_by=name&find=" . chr($n) . "&override_group_restrict=" . $override_group_restrict . "\">&nbsp;" . chr($n) . "&nbsp;</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

$url="collection_public.php?paging=true&col_order_by=".$col_order_by."&sort=".$sort."&find=".urlencode($find)."&override_group_restrict=" . $override_group_restrict;
?><div class="TopInpageNav"><?php echo $atoz?><?php pager(false); ?></div>

<form method=post id="collectionform">
<input type=hidden name="add" id="collectionadd" value="">

<?php

// count how many collections are owned by the user versus just shared, and show at top
$mycollcount = 0;
$othcollcount = 0;
for($i=0;$i<count($collections);$i++){
	if ($collections[$i]['user'] == $userref){
		$mycollcount++;
	} else {
		$othcollcount++;
	}
}

$collcount = count($collections);
switch ($collcount)
    {
    case 0:
        echo $lang["total-collections-0"];
        break;
    case 1:
        echo $lang["total-collections-1"];
        break;
    default:
        echo str_replace("%number", $collcount, $lang["total-collections-2"]);
    }
echo " ";
switch ($mycollcount)
    {
    case 0:
        echo $lang["owned_by_you-0"];
        break;
    case 1:
        echo $lang["owned_by_you-1"];
        break;
    default:
        echo str_replace("%mynumber", $mycollcount, $lang["owned_by_you-2"]);
    }
echo "<br />";
?>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php if ($col_order_by=="name") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&col_order_by=name&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["collectionname"]?></a><?php if ($col_order_by=="name") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<?php if (!$collection_public_hide_owner) { ?><td><?php if ($col_order_by=="user") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&col_order_by=user&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["owner"]?></a><?php if ($col_order_by=="user") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td><?php } ?>
<td><?php if ($col_order_by=="ref") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&col_order_by=ref&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["id"]?></a><?php if ($col_order_by=="ref") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($col_order_by=="created") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&col_order_by=created&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["created"]?></a><?php if ($col_order_by=="created") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($col_order_by=="count") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&col_order_by=count&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["itemstitle"]?></a><?php if ($col_order_by=="count") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<?php if (!$hide_access_column){ ?><td><?php if ($col_order_by=="public") {?><span class="Selected"><?php } ?><a href="collection_public.php?offset=0&col_order_by=public&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["access"]?></a><?php if ($col_order_by=="public") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td><?php } ?>
<?php hook("beforecollectiontoolscolumnheader");?>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>
<?php

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
	?>
    <tr <?php hook("collectionlistrowstyle");?>>
	<td><div class="ListTitle">
	<a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>"><?php echo highlightkeywords($collections[$n]["name"],$find)?></a></div></td>

	<?php if (!$collection_public_hide_owner) { ?><td><?php echo highlightkeywords($collections[$n]["fullname"],$find)?></td><?php } ?>
	<td><?php echo highlightkeywords($collections[$n]["ref"],$find)?></td>
	<td><?php echo nicedate($collections[$n]["created"],true)?></td>
    <td><?php echo $collections[$n]["count"]?></td>
	<?php if (!$hide_access_column_public){ ?><td><?php echo ($collections[$n]["public"]==0)?$lang["private"]:$lang["public"]?></td><?php } ?>
	<?php hook("beforecollectiontoolscolumn");?>
	<td><div class="ListTools">
    <?php if ($collections_compact_style){
        include("collections_compact_style.php");
    } else {
    ?><a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["viewall"]?></a>
	<?php if ($contact_sheet==true) { ?>
    &nbsp;<a href="contactsheet_settings.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
	<?php } ?>
	<?php if (!checkperm("b")) { ?>
	&nbsp;<a href="#" onclick="document.getElementById('collectionadd').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit(); return false;">&gt;&nbsp;<?php echo $lang["addtomycollections"]?></a><?php } ?>
	</div></td>
    <?php } ?>
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
