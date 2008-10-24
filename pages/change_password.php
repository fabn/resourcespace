<?
include "../include/db.php";
include "../include/authenticate.php"; if (checkperm("p")) {exit("Not allowed.");}
include "../include/general.php";

if (getval("save","")!="")
	{
	if (getval("password","")!=getval("password2","")) {$error2=true;}
	else
		{
		$message=change_password(getvalescaped("password",""));
		if ($message===true)
			{
			redirect("pages/home.php");
			}
		else
			{
			$error=true;
			}
		}
	}
include "../include/header.php";
?>

	<h1><?=$lang["changeyourpassword"]?></h1>

    <p><?=text("introtext")?></p>
	
	<? if (getval("expired","")!="") { ?><div class="FormError">!! <?=$lang["password_expired"]?> !!</div><? } ?>
	    
	<form method="post">  
	<input type="hidden" name="expired" value="<?=getval("expired","")?>">
	<div class="Question">
	<label for="password"><?=$lang["newpassword"]?></label>
	<input type="password" name="password" id="password" class="stdwidth">
	<? if (isset($error)) { ?><div class="FormError">!! <?=$message?> !!</div><? } ?>
	<div class="clearerleft"> </div>
	</div>

	<div class="Question">
	<label for="password2"><?=$lang["newpasswordretype"]?></label>
	<input type="password" name="password2" id="password2" class="stdwidth">
	<? if (isset($error2)) { ?><div class="FormError">!! <?=$lang["passwordnotmatch"]?> !!</div><? } ?>
	<div class="clearerleft"> </div>
	</div>

	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	

<?
include "../include/footer.php";
?>