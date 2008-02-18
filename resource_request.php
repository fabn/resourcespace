<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";

$ref=getval("ref","");

if (getval("save","")!="")
	{
	email_resource_request($ref,getvalescaped("request",""));
	redirect("done.php?text=resource_request");
	}
include "include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["requestresource"]?></h1>
  <p><?=text("introtext")?></p>
  
	<form method="post">  
	<input type=hidden name=ref value="<?=$ref?>">
	
	<div class="Question">
	<label><?=$lang["resourceid"]?></label>
	<div class="Fixed"><?=$ref?></div>
	<div class="clearerleft"> </div>
	</div>
	
	<div class="Question">
	<label><?=$lang["contacttelephone"]?></label>
	<input name="contact" class="stdwidth">
	<input type="hidden" name="contact_label" value="<?=$lang["contacttelephone"]?>">
	<div class="clearerleft"> </div>
	</div>
	
	<div class="Question">
	<label><?=$lang["finaluse"]?><br/><span class="OxColourPale"><?=$lang["finaluseeg"]?></span></label>
	<input name="finaluse" class="stdwidth">
	<input type="hidden" name="finaluse_label" value="<?=$lang["finaluse"]?>">
	<div class="clearerleft"> </div>
	</div>

<? if (file_exists("plugins/resource_request.php")) { include "plugins/resource_request.php"; } ?>

	<div class="Question">
	<label for="request"><?=$lang["message"]?></label>
	<textarea class="stdwidth" name="request" id="request" rows=5 cols=50></textarea>
	<div class="clearerleft"> </div>
	</div>
	

	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="cancel" type="button" value="&nbsp;&nbsp;<?=$lang["cancel"]?>&nbsp;&nbsp;" onclick="document.location='view.php?ref=<?=$ref?>';"/>&nbsp;
	<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["requestresource"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?
include "include/footer.php";
?>