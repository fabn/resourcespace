<?php
include "../include/db.php";
include "../include/general.php";

$errors=false;
if (getval("save","")!="")
	{
	if ($user_account_auto_creation)
		{
		# Automatically create a new user account
		$success=auto_create_user_account();
		}
	else
		{
		$success=email_user_request();
		}
		
	if ($success)
		{
		redirect("pages/done.php?text=user_request");
		}
	else
		{
		$errors=true;
		}
	}
include "../include/header.php";
?>

<h1><?php echo $lang["requestuserlogin"]?></h1>
<p><?php echo text("introtext")?></p>

<form method="post">  
<div class="Question">
<label for="name"><?php echo $lang["yourname"]?></label>
<input type=text name="name" id="name" class="stdwidth" value="<?php echo htmlspecialchars(getvalescaped("name",""))?>">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="email"><?php echo $lang["youremailaddress"]?></label>
<input type=text name="email" id="email" class="stdwidth" value="<?php echo htmlspecialchars(getvalescaped("email",""))?>">
<div class="clearerleft"> </div>
</div>

<?php # Add custom fields 
if (isset($custom_registration_fields))
	{
	$custom=explode(",",$custom_registration_fields);
	for ($n=0;$n<count($custom);$n++)
		{
		?>
		<div class="Question">
		<label for="custom<?php echo $n?>"><?php echo htmlspecialchars(i18n_get_translated($custom[$n]))?></label>
		<input type=text name="custom<?php echo $n?>" id="custom<?php echo $n?>" class="stdwidth" value="<?php echo htmlspecialchars(getvalescaped("custom" . $n,""))?>">
		<div class="clearerleft"> </div>
		</div>
		<?php
		}
	}
?>


<div class="Question">
<label for="email"><?php echo $lang["userrequestcomment"]?></label>
<textarea name="userrequestcomment" id="userrequestcomment" class="stdwidth"><?php echo htmlspecialchars(getvalescaped("userrequestcomment",""))?></textarea>
<div class="clearerleft"> </div>
</div>	

<div class="QuestionSubmit">
<?php if ($errors) { ?><div class="FormError">!! <?php echo $lang["requiredfields"] ?> !!</div><?php } ?>
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["requestuserlogin"]?>&nbsp;&nbsp;" />
</div>
</form>
	

<?php
include "../include/footer.php";
?>