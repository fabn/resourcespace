<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

if ((isset($allow_resource_deletion) and !$allow_resource_deletion) or checkperm('D')){
	include "../include/header.php";
	echo "Error: Resource deletion is disabled.";
	exit;
} else {

$ref=getvalescaped("ref","",true);
$resource=get_resource_data($ref);

$error="";

# Not allowed to edit this resource? They shouldn't have been able to get here.
if (!get_edit_access($ref,$resource["archive"])) {exit ("Permission denied.");}

if (getval("save","")!="")
	{
	if ($delete_requires_password && md5("RS" . $username . getvalescaped("password",""))!=$userpassword)
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
  
  <?php if ($resource["archive"]==3) { ?><p><strong><?php echo $lang["finaldeletion"] ?></strong></p><?php } ?>
  
	<form method="post">  
	<input type=hidden name=ref value="<?php echo $ref?>">
	
	<div class="Question">
	<label><?php echo $lang["resourceid"]?></label>
	<div class="Fixed"><?php echo $ref?></div>
	<div class="clearerleft"> </div>
	</div>
	
	<?php if ($delete_requires_password) { ?>
	<div class="Question">
	<label for="password"><?php echo $lang["yourpassword"]?></label>
	<input type=password class="shrtwidth" name="password" id="password" />
	<div class="clearerleft"> </div>
	<?php if ($error!="") { ?><div class="FormError">!! <?php echo $error?> !!</div><?php } ?>
	</div>
	<?php } ?>
	
	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["deleteresource"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?php
	}

} // end of block to prevent deletion if disabled
	
include "../include/footer.php";

?>
