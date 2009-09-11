<?php
include "../include/db.php";
include "../include/general.php";
include "../include/collections_functions.php";
if (getval("user","")!="") {include "../include/authenticate.php";} # Authenticate if already logged in, so the correct theme is displayed when using user group specific themes.

if (getval("refreshcollection","")!="")
	{
	refresh_collection_frame();
	}

include "../include/header.php";
?>

<div class="BasicsBox">
    <h1><?php echo $lang["complete"]?></h1>
    <p><?php echo text(getvalescaped("text",""))?></p>

   
    <?php if (getval("user","")!="") { # User logged in? ?>
 
 	<?php
 	# Ability to link back to a resource page
	$resource=getval("resource","");
	if ($resource!="")
		{
		?>
	    <p><a href="view.php?ref=<?php echo $resource?>">&gt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>
		<?php
		}
	?>
 
    <p><a href="<?php echo ($use_theme_as_home?'themes.php':$default_home_page)?>">&gt;&nbsp;<?php echo $lang["backtohome"]?></a></p>

    <p><a href="search.php">&gt;&nbsp;<?php echo $lang["backtosearch"]?></a></p>
    <?php hook("extra");?>
    <?php } else {?>
    <p><a href="../login.php">&gt;&nbsp;<?php echo $lang["backtouser"]?></a></p>
    <?php } ?>
</div>

<?php
include "../include/footer.php";
?>