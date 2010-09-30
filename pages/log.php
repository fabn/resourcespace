<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","",true);

# fetch the current search (for finding simlar matches)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

include "../include/header.php";
?>
<div class="BasicsBox">
<p><a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>
<h1><?php echo $lang["resourcelog"]?></h1>
</div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td width="10%"><?php echo $lang["date"]?></td>
<td width="10%"><?php echo $lang["user"]?></td>
<td width="10%"><?php echo $lang["action"]?></td>
<td width="10%"><?php echo $lang["field"]?></td>
<td><?php echo $lang["difference"]?></td>
</tr>

<?php
$log=get_resource_log($ref);
for ($n=0;$n<count($log);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td nowrap><?php echo nicedate($log[$n]["date"],true,true)?></td>
	<td nowrap><?php echo $log[$n]["fullname"]?></td>
	<td><?php echo $lang["log-" . $log[$n]["type"]]." ".$log[$n]["notes"]?></td>
	<td><?php echo i18n_get_translated($log[$n]["title"])?></td>
	<td><?php echo nl2br(htmlspecialchars($log[$n]["diff"])) . (($log[$n]["notes"]=="" || $log[$n]["notes"]=="-1")?"":$lang["usage"] . ": " . nl2br(htmlspecialchars($log[$n]["notes"])) . "<br>" . $lang["indicateusagemedium"] . ": " . @$download_usage_options[$log[$n]["usageoption"]]);
	
	# For purchases, append size and price
	if ($log[$n]["type"]=="p") {echo " (" . ($log[$n]["purchase_size"]==""?$lang["collection_download_original"]:$log[$n]["purchase_size"]) . ", " . $currency_symbol . number_format($log[$n]["purchase_price"],2) . ")";}
	
	?></td>
	</tr>
	<?php
	}
?>
</table>
</div>
<?php
include "../include/footer.php";
?>
