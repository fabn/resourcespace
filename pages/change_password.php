<?php
include "../include/db.php";
include "../include/authenticate.php"; if (checkperm("p")) {exit("Not allowed.");}
include "../include/general.php";

hook("prechangepasswordform");

if (getval("save","")!="")
	{
	if (getval("password","")!=getval("password2","")) {$error2=true;}
	else
		{
		$message=change_password(getvalescaped("password",""));
		if ($message===true)
			{
			redirect("pages/" . ($use_theme_as_home?'themes.php':$default_home_page));
			}
		else
			{
			$error=true;
			}
		}
	}
include "../include/header.php";
?>
<div class="BasicsBox"> 
	<h1><?php echo $lang["changeyourpassword"]?></h1>

    <p><?php echo text("introtext")?></p>

	<?php if (getval("expired","")!="") { ?><div class="FormError">!! <?php echo $lang["password_expired"]?> !!</div><?php } ?>

	<form method="post">
	<input type="hidden" name="expired" value="<?php echo getvalescaped("expired","")?>">
	<div class="Question">
	<label for="password"><?php echo $lang["newpassword"]?></label>
	<input type="password" name="password" id="password" class="stdwidth">
	<?php if (isset($error)) { ?><div class="FormError">!! <?php echo $message?> !!</div><?php } ?>
	<div class="clearerleft"> </div>
	</div>

	<div class="Question">
	<label for="password2"><?php echo $lang["newpasswordretype"]?></label>
	<input type="password" name="password2" id="password2" class="stdwidth">
	<?php if (isset($error2)) { ?><div class="FormError">!! <?php echo $lang["passwordnotmatch"]?> !!</div><?php } ?>
	<div class="clearerleft"> </div>
	</div>


	<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" /><div class="clearerleft"> </div>
	</div>
	</form>

<?php hook("afterchangepasswordform");?>
</div>
<?php
include "../include/footer.php";
?>
