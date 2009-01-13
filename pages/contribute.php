<?php
include "../include/db.php";
include "../include/authenticate.php";if (!checkperm("d")) {exit ("Permission denied.");}
include "../include/general.php";

include "../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["mycontributions"]?></h1>
  <p><?php echo text("introtext")?></p>

	<div class="VerticalNav">
	<ul>
	
	<?php if ($usercontribute_swfupload) { ?>
	<li><a href="edit.php?ref=-<?php echo $userref?>&swf=true"><?php echo $lang["contributenewresource"]?></a></li>
	<?php }  else { ?>
	<li><a href="create.php?archive=-2"><?php echo $lang["contributenewresource"]?></a></li>
	<?php } ?>

	<?php if (!checkperm("e0") && checkperm("e-2")) { ?>
	<li><a href="search.php?search=!contributions<?php echo $userref?>&archive=-2"><?php echo $lang["viewcontributedps"]?></a></li>
	<?php } ?>
	
	<?php if (!checkperm("e0") && checkperm("e-1")) { ?>
	<li><a href="search.php?search=!contributions<?php echo $userref?>&archive=-1"><?php echo $lang["viewcontributedpr"]?></a></li>
	<?php } ?>
	
	<?php if ($show_user_contributed_resources) { ?>
	<li><a href="search.php?search=!contributions<?php echo $userref?>&archive=0"><?php echo $lang["viewcontributedsubittedl"]?></a></li>
	<?php } ?>
	
	</ul>
	</div>
	
  </div>

<?php
include "../include/footer.php";
?>