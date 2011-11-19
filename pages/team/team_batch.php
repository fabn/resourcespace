<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";

include "../../include/header.php";
$resource_type = getvalescaped('resource_type','');
$collection_add = getvalescaped("collection_add","");
?>
<div class="BasicsBox">

	<?php
	# Define the titles.
	$titleh1 = $lang["addresourcebatchftp"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["specifyftpserver"]), $lang["header-upload-subtitle"]);
	?>

	<h1><?php echo $titleh1 ?></h1>
	<h2><?php echo $titleh2 ?></h2>

	<form method="post" action="team_batch_select.php?resource_type=<?php echo $resource_type?>">
	<input type="hidden" name="no_exif" value="<?php echo getval("no_exif","")?>">
	<input type="hidden" name="autorotate" value="<?php echo getval("autorotate","")?>">
	<input type="hidden" name="collection_add" value="<?php echo $collection_add?>">

		<div class="Question"><label><?php echo $lang["ftpserver"]?></label><input name="ftp_server" type="text" class="stdwidth" value="<?php echo $ftp_server?>"><div class="clearerleft"> </div></div>

		<div class="Question"><label><?php echo $lang["ftpusername"]?></label><input name="ftp_username" type="text" class="stdwidth" value="<?php echo $ftp_username?>"><div class="clearerleft"> </div></div>

		<div class="Question"><label><?php echo $lang["ftppassword"]?></label><input name="ftp_password" type="password" class="stdwidth" value="<?php echo $ftp_password?>"><div class="clearerleft"> </div></div>

		<div class="Question"><label><?php echo $lang["ftpfolder"]?></label><input name="ftp_folder" type="text" class="stdwidth" value="<?php echo $ftp_defaultfolder?>"><div class="clearerleft"> </div></div>

		<div class="QuestionSubmit">
			<label for="buttons"> </label>
			<input name="back" type="button" onclick="history.back(-1)" value="&nbsp;&nbsp;<?php echo $lang["back"] ?>&nbsp;&nbsp;" />
			<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["next"] ?>&nbsp;&nbsp;" />
		</div>
	</form>
</div>

<?php
include "../../include/footer.php";
?>
