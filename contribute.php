<?
include "include/db.php";
include "include/authenticate.php";if (!checkperm("d")) {exit ("Permission denied.");}
include "include/general.php";

include "include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["mycontributions"]?></h1>
  <p><?=text("introtext")?></p>

	<div class="VerticalNav">
	<ul>
	
	<? if ($usercontribute_fancyupload) { ?>
	<li><a href="edit.php?ref=-<?=$userref?>&fancy=true"><?=$lang["contributenewresource"]?></a></li>
	<? }  else { ?>
	<li><a href="create.php?archive=-2"><?=$lang["contributenewresource"]?></a></li>
	<? } ?>

	<? if (!checkperm("e0")) { ?>
	<li><a href="search.php?search=!contributions<?=$userref?>&archive=-2"><?=$lang["viewcontributedps"]?></a></li>
	<li><a href="search.php?search=!contributions<?=$userref?>&archive=-1"><?=$lang["viewcontributedpr"]?></a></li>
	<? } ?>
	
	
	<li><a href="search.php?search=!contributions<?=$userref?>&archive=0"><?=$lang["viewcontributedsubittedl"]?></a></li>
	
	</ul>
	</div>
	
  </div>

<?
include "include/footer.php";
?>