<?php
include "../include/db.php";
include "../include/general.php";

if ((getval("save","")!="") && (getval("name","")!=""))
	{
	email_user_request();
	redirect("pages/done.php?text=user_request");
	}
include "../include/header.php";
?>

<h1><?php echo $lang["requestuserlogin"]?></h1>
<p><?php echo text("introtext")?></p>

<form method="post">  
<div class="Question">
<label for="name"><?php echo $lang["yourname"]?></label>
<input type=text name="name" id="name" class="stdwidth">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="email"><?php echo $lang["youremailaddress"]?></label>
<input type=text name="email" id="email" class="stdwidth">
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
		<input type=text name="custom<?php echo $n?>" id="custom<?php echo $n?>" class="stdwidth">
		<div class="clearerleft"> </div>
		</div>
		<?php
		}
	}
?>


<div class="Question">
<label for="email"><?php echo $lang["userrequestcomment"]?></label>
<textarea name="userrequestcomment" id="userrequestcomment" class="stdwidth"></textarea>
<div class="clearerleft"> </div>
</div>	

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["requestuserlogin"]?>&nbsp;&nbsp;" />
</div>
</form>
	

<?php
include "../include/footer.php";
?>