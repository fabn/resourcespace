<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/resource_functions.php";

$ref=getval("ref","");
$error="";

if (getval("save","")!="")
	{
	if (md5("RS" . $username . getvalescaped("password",""))!=$userpassword)
		{
		$error=$lang["wrongpassword"];
		}
	else
		{
		delete_resource($ref);
		redirect("done.php?text=deleted");
		}
	}
include "include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["deleteresource"]?></h1>
  <p><?=text("introtext")?></p>
  
	<form method="post">  
	<input type=hidden name=ref value="<?=$ref?>">
	
	<div class="Question">
	<label><?=$lang["resourceid"]?></label>
	<div class="Fixed"><?=$ref?></div>
	<div class="clearerleft"> </div>
	</div>
	
	<div class="Question">
	<label for="password"><?=$lang["yourpassword"]?></label>
	<input type=password class="shrtwidth" name="password" id="password" />
	<div class="clearerleft"> </div>
	<? if ($error!="") { ?><div class="FormError">!! <?=$error?> !!</div><? } ?>
	</div>
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["deleteresource"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?
include "include/footer.php";
?>