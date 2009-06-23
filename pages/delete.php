<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

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
		hook("custompredeleteresource");

		delete_resource($ref);
		
		hook("custompostdeleteresource");
		
		redirect("pages/done.php?text=deleted&refreshcollection=true");
		}
	}
include "../include/header.php";

$resource=get_resource_data($ref);
if (isset($resource['is_transcoding']) && $resource['is_transcoding']==1)
	{
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["deleteresource"]?></h1>
  <p class="FormIncorrect"><?php echo $lang["cantdeletewhiletranscoding"]?></p>
</div>
<?php	
	}
else
	{
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["deleteresource"]?></h1>
  <p><?php echo text("introtext")?></p>
  
	<form method="post">  
	<input type=hidden name=ref value="<?php echo $ref?>">
	
	<div class="Question">
	<label><?php echo $lang["resourceid"]?></label>
	<div class="Fixed"><?php echo $ref?></div>
	<div class="clearerleft"> </div>
	</div>
	
	<div class="Question">
	<label for="password"><?php echo $lang["yourpassword"]?></label>
	<input type=password class="shrtwidth" name="password" id="password" />
	<div class="clearerleft"> </div>
	<?php if ($error!="") { ?><div class="FormError">!! <?php echo $error?> !!</div><?php } ?>
	</div>
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["deleteresource"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?php
	}
	
include "../include/footer.php";
?>