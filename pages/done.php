<?php
include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
if (getval("user","")!="") {include "../include/authenticate.php";} #Authenticate if already logged in, so the correct theme is displayed when using user group specific themes.

if (getval("refreshcollection","")!="")
	{
	refresh_collection_frame();
	}

# fetch the current search 
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
    <h1><?php echo $lang["complete"]?></h1>
    <p><?php echo text(getvalescaped("text",""))?></p>

   
    <?php if (getval("user","")!="" || getval("k","")!="") { # User logged in? ?>
 
 	<?php
 	# Ability to link back to a resource page
	$resource=getval("resource","");
	if ($resource!="")
		{
		?>
	    <p><a href="view.php?ref=<?php echo $resource?>&k=<?php echo getval("k","") ?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>
		<?php
		}
	?>
 
	<?php if (getval("k","")=="") { ?>
    <p><a href="search.php?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["backtoresults"]?></a></p>

    <p><a href="<?php echo ($use_theme_as_home?'themes.php':$default_home_page)?>">&gt;&nbsp;<?php echo $lang["backtohome"]?></a></p>

    <?php } ?>
    
    <?php hook("extra");?>
    <?php } else {?>
    <p><a href="../login.php">&gt;&nbsp;<?php echo $lang["backtouser"]?></a></p>
    <?php } ?>
</div>

<?php
include "../include/footer.php";
?>
