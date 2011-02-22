<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

$ref=getvalescaped("ref","",true);
$k=getvalescaped("k","");

# fetch the current search (for finding simlar matches)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);
$starsearch=getvalescaped("starsearch","");
$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

# next / previous resource browsing
$go=getval("go","");
if ($go!="")
	{
	$origref=$ref; # Store the reference of the resource before we move, in case we need to revert this.
	
	# Re-run the search and locate the next and previous records.
	$result=do_search($search,$restypes,$order_by,$archive,240+$offset+1,$sort,false,$starsearch);
	if (is_array($result))
		{
		# Locate this resource
		$pos=-1;
		for ($n=0;$n<count($result);$n++)
			{
			if ($result[$n]["ref"]==$ref) {$pos=$n;}
			}
		if ($pos!=-1)
			{
			if (($go=="previous") && ($pos>0)) {$ref=$result[$pos-1]["ref"];}
			if (($go=="next") && ($pos<($n-1))) {$ref=$result[$pos+1]["ref"];if (($pos+1)>=($offset+72)) {$offset=$pos+1;}} # move to next page if we've advanced far enough
			}
		else
			{
			?>
			<script type="text/javascript">
			alert('<?php echo $lang["resourcenotinresults"] ?>');
			</script>
			<?php
			}
		}
	# Check access permissions for this new resource, if an external user.
	$newkey=hook("nextpreviewregeneratekey");
	if (is_string($newkey)) {$k=$newkey;}
	if ($k!="" && !check_access_key($ref,$k)) {$ref=$origref;} # cancel the move.
	}


include "../include/header.php";
?>
<div class="BasicsBox">
<p><a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>


<div class="backtoresults">
<a href="log.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k ?>&go=previous&<?php echo hook("nextpreviousextraurl") ?>">&lt;&nbsp;<?php echo $lang["previousresult"]?></a>
<?php 
hook("viewallresults");
if ($k=="") { ?>
|
<a href="search.php?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["viewallresults"]?></a>
<?php } ?>
|
<a href="log.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>&go=next&<?php echo hook("nextpreviousextraurl") ?>"><?php echo $lang["nextresult"]?>&nbsp;&gt;</a>
</div>

<h1><?php echo $lang["resourcelog"] . " : " . $lang["resourceid"] . " " .  $ref ?></h1>

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
	<td nowrap><?php echo $log[$n]["access_key"]!=""?$lang["externalusersharing"] . ": " . $log[$n]["access_key"] . " " . $lang["viauser"] . " " . $log[$n]["shared_by"]:$log[$n]["fullname"]?></td>
	<td><?php echo $lang["log-" . $log[$n]["type"]]." ".$log[$n]["notes"]?></td>
	<td><?php echo i18n_get_translated($log[$n]["title"])?></td>
	<td><?php if ($log[$n]["usageoption"]!="-1"){
        // if usageoption is set to -1 when logging, you can avoid the usage description here
        echo nl2br(htmlspecialchars($log[$n]["diff"])) . (($log[$n]["notes"]=="" || $log[$n]["notes"]=="-1")?"":$lang["usage"] . ": " . nl2br(htmlspecialchars($log[$n]["notes"])) . "<br>" . $lang["indicateusagemedium"] . ": " . @$download_usage_options[$log[$n]["usageoption"]]);
	}
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
