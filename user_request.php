<?
include "include/db.php";
include "include/general.php";

if ((getval("save","")!="") && (getval("name","")!=""))
	{
	email_user_request();
	redirect("done.php?text=user_request");
	}
include "include/header.php";
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
include "include/footer.php";
?>