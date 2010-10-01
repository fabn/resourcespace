<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";
include "../include/collections_functions.php";

$collection=getvalescaped("collection","");

# Reload collection frame to show new (empty) basket
refresh_collection_frame($usercollection);


include "../include/header.php";

?>
<div class="BasicsBox"> 
<h2>&nbsp;</h2>
<h1><?php echo $lang["downloadpurchaseitems"]?></h1>
<?php

$resources=do_search("!collection" . $collection);
$valid=true;
foreach ($resources as $resource)
		{
		if($resource["purchase_complete"]!=1) {$valid=false;}
		}
		

if (!$valid)
	{
	# ------------------- Notification not yet received. Show a please wait message. -----------------------
	?>
    <p><?php echo $lang["waitingforpaymentauthorisation"] ?></p>
	   
	<form method="post" action="purchase_download.php">
	<input type="submit" name="reload" value="&nbsp;&nbsp;&nbsp;<?php echo $lang["reload"] ?>&nbsp;&nbsp;&nbsp;">
	</form>
	<?php
	}
else
	{
	# ------------------- Show download links ----------------------------------------------------------------
	?>
	<div class="RecordPanel">
	<p><strong><?php echo $lang["downloadpurchaseitemsnow"] ?></strong></p>
	<div class="RecordDownloadSpace">

	<table class="InfoTable">
	<?php 

	foreach ($resources as $resource)
		{
		?><tr class="DownloadDBlend"><?php
		$size=$resource["purchase_size"];
		$title=get_data_by_field($resource["ref"],$view_title_field);
		?><td><h2><?php echo i18n_get_translated($title) ?></h2></td>
		<td class="DownloadButton">
		<a href="download.php?ref=<?php echo $resource["ref"] ?>&size=<?php echo $size ?>"><?php echo $lang["download"]?></a>
		</td>
		</tr><?php	
		}
	?>
	</table>
	
	</div>
	</div>
	<?php
	}
?>
</div>

<?php
include "../include/footer.php";
?>