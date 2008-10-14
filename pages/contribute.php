<?
include "../include/db.php";
include "../include/authenticate.php";if (!checkperm("d")) {exit ("Permission denied.");}
include "../include/general.php";

include "../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["mycontributions"]?></h1>
  <p><?=text("introtext")?></p>

	<div class="VerticalNav">
	<ul>
	
	<? if ($usercontribute_swfupload) { ?>
	<li><a href="edit.php?ref=-<?=$userref?>&swf=true"><?=$lang["contributenewresource"]?></a></li>
	<? }  else { ?>
	<li><a href="create.php?archive=-2"><?=$lang["contributenewresource"]?></a></li>
	<? } ?>

	<? if (!checkperm("e0") && checkperm("e-2")) { ?>
	<li><a href="search.php?search=!contributions<?=$userref?>&archive=-2"><?=$lang["viewcontributedps"]?></a></li>
	<? } ?>
	
	<? if (!checkperm("e0") && checkperm("e-1")) { ?>
	<li><a href="search.php?search=!contributions<?=$userref?>&archive=-1"><?=$lang["viewcontributedpr"]?></a></li>
	<? } ?>
	
	<? if ($show_user_contributed_resources) { ?>
	<li><a href="search.php?search=!contributions<?=$userref?>&archive=0"><?=$lang["viewcontributedsubittedl"]?></a></li>
	<? } ?>
	
	</ul>
	</div>
	
  </div>

<?
include "../include/footer.php";
?>