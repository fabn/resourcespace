<?
include "../include/db.php";
include "../include/general.php";

if ((getval("save","")!="") && (getval("name","")!=""))
	{
	email_user_request();
	redirect("pages/done.php?text=user_request");
	}
include "../include/header.php";
?>

<h1><?=$lang["requestuserlogin"]?></h1>
<p><?=text("introtext")?></p>

<form method="post">  
<div class="Question">
<label for="name"><?=$lang["yourname"]?></label>
<input type=text name="name" id="name" class="stdwidth">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="email"><?=$lang["youremailaddress"]?></label>
<input type=text name="email" id="email" class="stdwidth">
<div class="clearerleft"> </div>
</div>

<? # Add custom fields 
if (isset($custom_registration_fields))
	{
	$custom=explode(",",$custom_registration_fields);
	for ($n=0;$n<count($custom);$n++)
		{
		?>
		<div class="Question">
		<label for="custom<?=$n?>"><?=htmlspecialchars(i18n_get_translated($custom[$n]))?></label>
		<input type=text name="custom<?=$n?>" id="custom<?=$n?>" class="stdwidth">
		<div class="clearerleft"> </div>
		</div>
		<?
		}
	}
?>


<div class="Question">
<label for="email"><?=$lang["userrequestcomment"]?></label>
<textarea name="userrequestcomment" id="userrequestcomment" class="stdwidth"></textarea>
<div class="clearerleft"> </div>
</div>	

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["requestuserlogin"]?>&nbsp;&nbsp;" />
</div>
</form>
	

<?
include "../include/footer.php";
?>