<?php
include "../../../include/db.php";
include "../../../include/authenticate.php";
include "../../../include/general.php";
include "../../../include/search_functions.php";

$search=getvalescaped("search","");
$affiliate_selected=getvalescaped("affiliate","");
include "../../../include/header.php";
$page_size=$resourceconnect_pagesize;


# Show only one affiliate?
if ($affiliate_selected!="")
	{
	$resourceconnect_affiliates=array($resourceconnect_affiliates[$affiliate_selected]);
	$page_size=$resourceconnect_pagesize_expanded;
	}


?>
<p><a href="../../../pages/search.php?search=<?php echo urlencode($search) ?>">&lt;&nbsp;<?php echo $lang["backtosearch"] ?></a></p>
<h1 style="padding-bottom:10px;"><?php echo $resourceconnect_title ?></h1>
<?php

$counter=0;
foreach ($resourceconnect_affiliates as $affiliate)
	{

	if ($affiliate["baseurl"]!=$baseurl) # Do not search self.
		{
		?>
		<div class="RecordBox"> 
		<div class="RecordPanel" style="margin-bottom:0;padding-bottom:0;">  
		
		<div class="backtoresults">
		<a href="#" onClick="Repage_<?php echo $counter ?>(-<?php echo $page_size ?>);return false;">&lt;&nbsp;<?php echo $lang["previous"]?></a>
		|
		<?php if ($affiliate_selected=="") { ?>
		<a href="search.php?search=<?php echo urlencode($search) ?>&affiliate=<?php echo $counter ?>"><?php echo $lang["viewall"]?></a>
		|
		<?php } ?>
		<a href="#" onClick="Repage_<?php echo $counter ?>(<?php echo $page_size ?>);return false;"><?php echo $lang["next"]?>&nbsp;&gt;</a>
		</div>
		
		<h1><?php echo $affiliate["name"] ?></h1>
		<div id="resourceconnect_container_<?php echo $counter ?>"><p><?php echo $lang["pleasewait"] ?></p></div>
		<div class="clearerleft"></div>

		</div>
		</div>
	
		<script>
		// Repage / pager function
		var offset_<?php echo $counter ?>=0;
		function Repage_<?php echo $counter ?>(distance)
			{
			offset_<?php echo $counter ?>+=distance;
			if (offset_<?php echo $counter ?><0) {offset_<?php echo $counter ?>=0;}
			new Ajax.Updater ('resourceconnect_container_<?php echo $counter ?>','ajax_request.php?search=<?php echo urlencode($search) ?>&pagesize=<?php echo $page_size ?>&affiliate=<?php echo ($affiliate_selected!=""?$affiliate_selected:$counter) ?>&offset=' + offset_<?php echo $counter ?>);
			}
	
		Repage_<?php echo $counter ?>(0);
		</script>
		<?php
		}
	$counter++;
	}
?>

<?php




include "../../../include/footer.php";