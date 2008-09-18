<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("m")) {exit ("Permission denied.");}
include "include/general.php";

if (getval("send","")!="")
	{
	$result=bulk_mail(getvalescaped("users",""),getvalescaped("subject",""),getvalescaped("text",""));
	if ($result=="") {$error=$lang["emailsent"];} else {$error="!! " . $result . " !!";}
	}

include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["sendbulkmail"]?></h1>

<form method="post" action="team_mail.php">

<? if (isset($error)) { ?><div class="FormError"><?=$error?></div><? } ?>

<div class="Question"><label><?=$lang["emailrecipients"]?></label><? include "include/user_select.php"; ?>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["emailsubject"]?></label><input name="subject" type="text" class="stdwidth" value="<?=getval("subject",$applicationname)?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["emailtext"]?></label><textarea name="text" class="stdwidth" rows=25 cols=50><?=htmlspecialchars(getval("text",""))?></textarea><div class="clearerleft"> </div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="send" type="submit" value="&nbsp;&nbsp;<?=$lang["send"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "include/footer.php";
?>