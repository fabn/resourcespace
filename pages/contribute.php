<?php
require_once "../include/db.php";
require_once "../include/authenticate.php";if (!checkperm("d")&&!(checkperm('c') && checkperm('e0'))) {exit ("Permission denied.");}
require_once "../include/general.php";

include "../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["mycontributions"]?></h1>
  <p><?php echo text("introtext")?></p>

	<div class="VerticalNav">
	<ul>

	<?php 
	$batchenabled = false;
	if (isset($usercontribute_javaupload) && $usercontribute_javaupload) { 
		$batchenabled=true; ?>
		<li><a href="edit.php?ref=-<?php echo $userref?>&java=true">
		<?php 
			if (isset($usercontribute_swfupload) && $usercontribute_swfupload) { 
				echo $lang["addresourcebatchbrowserjava"]; 
			} else { 
				echo $lang["contributenewresource"]; 
			} 
		?>
		</a></li>
	<?php } ?>

	<?php if (isset($usercontribute_swfupload) && $usercontribute_swfupload) { 
		$batchenabled=true; ?>
		<li><a href="edit.php?ref=-<?php echo $userref?>&swf=true">
		<?php 
			if (isset($usercontribute_javaupload) && $usercontribute_javaupload) { 
				echo $lang["addresourcebatchbrowser"]; 
			} else { 
				echo $lang["contributenewresource"]; 
			} 
		?>
		</a></li>
	<?php }  ?>

	<?php if (!$batchenabled){ ?>
	<li><a href="create.php<?php
		if (checkperm('e-2')){
			// resources go into submitted first
			echo '?archive=-2';
		} elseif (checkperm('e0')&&checkperm('c')){
			// user can edit/create in live state
			// use without archive parameter
		} else {
			// default to waiting for review
			echo '?archive=-1';
		} 
	?>"><?php echo $lang["contributenewresource"]?></a></li>
	<?php } ?>

	<?php if (checkperm("e-2")) { ?>
	<li><a href="search.php?search=!contributions<?php echo $userref?>&archive=-2"><?php echo $lang["viewcontributedps"]?></a></li>
	<?php } ?>
	
	<?php if (checkperm("e-1")) { ?>
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
