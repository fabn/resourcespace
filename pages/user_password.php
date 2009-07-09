<?php
include "../include/db.php";
include "../include/general.php";
if (!$allow_password_reset) {exit("Password requests have been disabled.");} # User should never see this.

if (getval("save","")!="")
	{
	if (email_reminder(getvalescaped("email","")))
		{
		redirect("pages/done.php?text=user_password");
		}
	else
		{
		$error=true;
		}
	}
include "../include/header.php";
?>

    <h1><?php echo $lang["requestnewpassword"]?></h1>
    <p><?php echo text("introtext")?></p>
	
	  
	<form method="post">  
	<div class="Question">
	<label for="email"><?php echo $lang["youremailaddress"]?></label>
	<input type=text name="email" id="email" class="stdwidth">
	<?php if (isset($error)) { ?><div class="FormError">!! <?php echo $lang["emailnotfound"]?> !!</div><?php } ?>
	<div class="clearerleft"> </div>
	</div>
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["sendnewpassword"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	

<?php
include "../include/footer.php";
?>