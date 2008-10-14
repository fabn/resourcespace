<?
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";

include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["specifyftpserver"]?></h1>
<p><?=text("introtext")?></p>

<form method=post action="team_batch_select.php">

<div class="Question"><label><?=$lang["ftpserver"]?></label><input name="ftp_server" type="text" class="stdwidth" value="<?=$ftp_server?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["ftpusername"]?></label><input name="ftp_username" type="text" class="stdwidth" value="<?=$ftp_username?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["ftppassword"]?></label><input name="ftp_password" type="password" class="stdwidth" value="<?=$ftp_password?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["ftpfolder"]?></label><input name="ftp_folder" type="text" class="stdwidth" value="<?=$ftp_defaultfolder?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["uselocalupload"]?></label><input name="use_local" type="checkbox" value="yes"><div class="clearerleft"> </div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["connect"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?
include "../../include/footer.php";
?>