<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";

include "../../include/header.php";
$resource_type=getvalescaped('resource_type','');
?>
<div class="BasicsBox">
	<h1><?php echo $lang["specifyftpserver"]?></h1>
	<p><?php echo text("introtext")?></p>

	<form method="post" action="team_batch_select.php?resource_type=<?php echo $resource_type?>">

		<div class="Question"><label><?php echo $lang["ftpserver"]?></label><input name="ftp_server" type="text" class="stdwidth" value="<?php echo $ftp_server?>"><div class="clearerleft"> </div></div>

		<div class="Question"><label><?php echo $lang["ftpusername"]?></label><input name="ftp_username" type="text" class="stdwidth" value="<?php echo $ftp_username?>"><div class="clearerleft"> </div></div>

		<div class="Question"><label><?php echo $lang["ftppassword"]?></label><input name="ftp_password" type="password" class="stdwidth" value="<?php echo $ftp_password?>"><div class="clearerleft"> </div></div>

		<div class="Question"><label><?php echo $lang["ftpfolder"]?></label><input name="ftp_folder" type="text" class="stdwidth" value="<?php echo $ftp_defaultfolder?>"><div class="clearerleft"> </div></div>

		<!--div class="Question"><label><?php echo $lang["uselocalupload"]?></label><input name="use_local" type="checkbox" value="yes"><div class="clearerleft"> </div></div-->

		<div class="QuestionSubmit">
			<label for="buttons"> </label>
			<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["connect"]?>&nbsp;&nbsp;" />
		</div>
	</form>
</div>

<?php
include "../../include/footer.php";
?>
