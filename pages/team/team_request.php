<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("R")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/request_functions.php";
include "../../include/collections_functions.php";

$offset=getvalescaped("offset",0);

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["managerequestsorders"]?></h1>
  <p><?php echo text("introtext")?></p>
 
<?php 
$requests=get_requests();

# pager
$per_page=10;
$results=count($requests);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="team_request.php?";
$jumpcount=1;

?><div class="TopInpageNav"><?php pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["requestorderid"]?></td>
<td><?php echo $lang["username"]?></td>
<td><?php echo $lang["fullname"]?></td>
<td><?php echo $lang["date"]?></td>
<td><?php echo $lang["items"]?></td>
<td><?php echo $lang["type"]?></td>
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
	<td><?php echo $requests[$n]["username"] ?></td>
	<td><?php echo $requests[$n]["fullname"] ?></td>
	<td><?php echo nicedate($requests[$n]["created"],true)?></td>
	<td><?php echo $requests[$n]["c"] ?></td>
	<td><?php echo $lang["resourcerequesttype" . $requests[$n]["request_mode"]] ?></td>
	<td><?php echo $lang["resourcerequeststatus" . $requests[$n]["status"]] ?></td>
	<td><div class="ListTools"><a href="team_request_edit.php?ref=<?php echo $requests[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["edit"]?></a></a></div></td>
	</tr>
	<?php
	}
?>

</table>
</div><!--end of Listview -->
<div class="BottomInpageNav"><?php pager(false); ?></div>
</div><!-- end of BasicsBox -->




<?php
include "../../include/footer.php";
?>