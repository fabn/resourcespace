<?php
/**
 * Create bulk mail page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("m")) {exit ("Permission denied.");}
include "../../include/general.php";

if (getval("send","")!="")
	{
	$result=bulk_mail(getvalescaped("users",""),getvalescaped("subject",""),getvalescaped("text",""),getval("html","")=="yes");
	if ($result=="") {$error=$lang["emailsent"];} else {$error="!! " . $result . " !!";}
	}
$headerinsert.="
<script src=\"$baseurl/lib/js/jquery.validate.min.js\" type=\"text/javascript\"></script><script type=\"text/javascript\">
jQuery(document).ready(function(){
	jQuery('#myform').validate({ 
		errorPlacement: function(error, element) {
		element.after('<span class=\"FormError\">'+error.html()+'</span>');
		},
   wrapper: 'div'});
});
</script>";

include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["sendbulkmail"]?></h1>
<form id="myform" method="post" action="team_mail.php">

<?php if (isset($error)) { ?><div class="FormError"><?php echo $error?></div><?php } ?>

<div class="Question"><label><?php echo $lang["emailrecipients"]?></label><?php include "../../include/user_select.php"; ?>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["emailhtml"]?></label><input name="html" type="checkbox" value="yes" <?php if (getval("html","")=="yes") { ?>checked<?php } ?>><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["emailsubject"]?></label><input name="subject" type="text" class="stdwidth Inline required" value="<?php echo getval("subject",$applicationname)?>"><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["emailtext"]?></label><textarea name="text" class="stdwidth Inline required" rows=25 cols=50><?php echo htmlspecialchars(getval("text",""))?></textarea><div class="clearerleft"> </div></div>

<?php hook("additionalemailfield");?>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="send" type="submit" value="&nbsp;&nbsp;<?php echo $lang["send"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>
