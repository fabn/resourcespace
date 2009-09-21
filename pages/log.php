<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","");

include "../include/header.php";
?>
<div class="BasicsBox">
<p><a href="view.php?ref=<?php echo $ref?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>
<h1><?php echo $lang["resourcelog"]?></h1>
</div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td><?php echo $lang["date"]?></td>
<td><?php echo $lang["user"]?></td>
<td><?php echo $lang["action"]?></td>
<td><?php echo $lang["field"]?></td>
<td><?php echo $lang["difference"]?></td>
</tr>

<?php
$log=get_resource_log($ref);
for ($n=0;$n<count($log);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td><?php echo nicedate($log[$n]["date"],true,false)?></td>
	<td><?php echo $log[$n]["username"]?> (<?php echo $log[$n]["fullname"]?>)</td>
	<td><?php echo $lang["log-" . $log[$n]["type"]]." ".$log[$n]["notes"]?></td>
	<td><?php echo i18n_get_translated($log[$n]["title"])?></td>
	<td><?php echo nl2br(htmlspecialchars($log[$n]["diff"]))?></td>
	</tr>
	<?php
	}
?>
</table>
</div>
<?php
include "../include/footer.php";
?>
