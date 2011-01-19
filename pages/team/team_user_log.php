<?php
/**
 * User log display page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
include "../../include/resource_functions.php";

$ref=getvalescaped("ref","",true);
$userdata=get_user($ref);

include "../../include/header.php";
?>
<div class="BasicsBox">
<p><a href="<?php echo getval("backurl","")?>">&lt;&nbsp;<?php echo $lang["backtosearch"]?></a></p>
<h1><?php echo $lang["userlog"] . ": " . $userdata["fullname"]?></h1>
</div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td><?php echo $lang["date"]?></td>
<td><?php echo $lang["resourceid"]?></td>
<td><?php echo $lang["resourcetitle"]?></td>
<td><?php echo $lang["action"]?></td>
<td><?php echo $lang["field"]?></td>
</tr>

<?php
$log=get_user_log($ref);
for ($n=0;$n<count($log);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td><?php echo $log[$n]["date"]?></td>
	<td><?php echo $log[$n]["resourceid"]?></td>
	<td><?php echo i18n_get_translated($log[$n]["resourcetitle"])?></td>
	<td><?php echo $lang["log-" . $log[$n]["type"]];
	
	# For purchases, append size and price
	if ($log[$n]["type"]=="p") {echo " (" . ($log[$n]["purchase_size"]==""?$lang["collection_download_original"]:$log[$n]["purchase_size"]) . ", " . $currency_symbol . number_format($log[$n]["purchase_price"],2) . ")";}

	?></td>
	<td><?php echo $log[$n]["title"] ?></td>
	</tr>
	<?php
	}
?>
</table>
</div>
<p><a href="<?php echo getval("backurl","")?>">&lt;&nbsp;<?php echo $lang["backtosearch"]?></a></p>
<?php
include "../../include/footer.php";
?>
