<?
include "include/db.php";
include "include/general.php";

if (getval("save","")!="")
	{
	if (email_reminder(getvalescaped("email","")))
		{
		redirect("done.php?text=user_password");
		}
	else
		{
		$error=true;
		}
	}
include "include/header.php";
?>

    <h1><?=$lang["requestnewpassword"]?></h1>
    <p><?=text("introtext")?></p>
	
	  
	<form method="post">  
	<div class="Question">
	<label for="email"><?=$lang["youremailaddress"]?></label>
	<input type=text name="email" id="email" class="stdwidth">
	<? if (isset($error)) { ?><div class="FormError">!! <?=$lang["emailnotfound"]?> !!</div><? } ?>
	<div class="clearerleft"> </div>
	</div>
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["sendreminder"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	

<?
include "include/footer.php";
?>