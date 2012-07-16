<?php
include "../include/db.php";
include "../include/authenticate.php";if (!checkperm("R")) {exit ("Permission denied.");}
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
<a href="request_log.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k ?>&go=previous&<?php echo hook("nextpreviousextraurl") ?>">&lt;&nbsp;<?php echo $lang["previousresult"]?></a>
<?php 
hook("viewallresults");
if ($k=="") { ?>
|
<a href="search.php?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["viewallresults"]?></a>
<?php } ?>
|
<a href="request_log.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>&go=next&<?php echo hook("nextpreviousextraurl") ?>"><?php echo $lang["nextresult"]?>&nbsp;&gt;</a>
</div>

<h1><?php echo $lang["requestlog"] . " : " . $lang["resourceid"] . " " .  $ref ?></h1>

</div>


<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td width="5%"><?php echo $lang["date"]?></td>
<td width="5%"><?php echo "ref" ?></td>
<td width="5%"><?php echo $lang["user"]?></td>
<td width="25%"><?php echo $lang["comments"]?></td>
<td width="10%"><?php echo $lang["status"]?></td>
<td width="25%"><?php echo $lang["approvalreason"] ?></td>
<td width="25%"><?php echo $lang["declinereason"] ?></td>
</tr>

<?php
#$log=get_resource_log($ref);
$log=sql_query("select rq.created date, rq.ref ref, u.fullname username, rq.comments, rq.status status, rq.reason reason, rq.reasonapproved reasonapproved from request rq left outer join user u on u.ref=rq.user left outer join collection_resource cr on cr.collection=rq.collection where cr.resource=$ref;");

for ($n=0;$n<count($log);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td nowrap><?php echo nicedate($log[$n]["date"],true,true)?></td>
	<td nowrap><?php echo $log[$n]["ref"] ?></td>
	<td nowrap><?php echo $log[$n]["username"] ?></td>
	<td><?php echo $log[$n]["comments"] ?></td>
	<td nowrap><?php
	switch ($log[$n]["status"])
		{
		Case 0: echo $lang["resourcerequeststatus0"];break;
		Case 1: echo $lang["resourcerequeststatus1"];break;
		Case 2: echo $lang["resourcerequeststatus2"];break;
		}
	?>
	</td>
	<td><?php echo $log[$n]["reasonapproved"] ?></td>
	<td><?php echo $log[$n]["reason"] ?></td>
	
	
	</tr>
	<?php
	}
?>
</table>
</div>
<?php
include "../include/footer.php";
?>
