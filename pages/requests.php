<?php
/**
 * View my own requests
 * 
 * @package ResourceSpace
 */
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/request_functions.php";
include "../include/collections_functions.php";

$offset=getvalescaped("offset",0);

include "../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["myrequests"]?></h1>
  <p><?php echo text("introtext")?></p>
 
<?php 
$requests=get_user_requests();

# pager
$per_page=10;
$results=count($requests);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="requests.php?";
$jumpcount=1;

?><div class="TopInpageNav"><?php pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["requestorderid"]?></td>
<td><?php echo $lang["description"]?></td>
<td><?php echo $lang["date"]?></td>
<td><?php echo $lang["itemstitle"]?></td>
<td><?php echo $lang["status"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
$statusname=array("","","","");
$requesttypes=array("","","","");

for ($n=$offset;(($n<count($requests)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><?php echo $requests[$n]["ref"]?></td>
	<td><?php echo $requests[$n]["comments"] ?></td>
	<td><?php echo nicedate($requests[$n]["created"],true)?></td>
	<td><?php echo $requests[$n]["c"] ?></td>
	<td><?php echo $lang["resourcerequeststatus" . $requests[$n]["status"]] ?></td>
	<td>
	<div class="ListTools">
	<?php if ($requests[$n]["collection_id"] > 0){ // only show tools if the collection still exists ?>
		<a href="search.php?search=<?php echo urlencode("!collection" . $requests[$n]["collection"])?>">&gt;&nbsp;<?php echo $lang["action-view"]?></a>
        &nbsp;<a <?php if ($frameless_collections && !checkperm("b")){ ?>href onclick="ChangeCollection(<?php echo $requests[$n]["collection"]?>);"
                <?php } elseif ($autoshow_thumbs) {?>onclick=" top.document.getElementById('topframe').rows='*<?php if ($collection_resize!=true) {?>,3<?php } ?>,138'; return true;"
                href="collections.php?collection=<?php echo $requests[$n]["collection"]?>&amp;thumbs=show" target="collections"
                <?php } else {?>href="collections.php?collection=<?php echo $requests[$n]["collection"]?>" target="collections"<?php }?>>&gt;&nbsp;<?php echo $lang["action-select"]?></a>
	<?php } // end of if collection still exists ?>
	</div>
	</td>
	</tr>
	<?php
	}
?>

</table>
</div><!--end of Listview -->
<div class="BottomInpageNav"><?php pager(false); ?>
</div>
</div><!-- end of BasicsBox -->




<?php
include "../include/footer.php";
?>
