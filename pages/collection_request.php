<?php
include "../include/db.php";
$k=getvalescaped("k","");if ($k=="") {include "../include/authenticate.php";}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/request_functions.php";

$ref=getval("ref","");
$cinfo=get_collection($ref);

if (getval("save","")!="")
	{
	if ($k!="" || $userrequestmode==0)
		{
		# Request mode 0 : Simply e-mail the request.
		email_collection_request($ref,getvalescaped("request",""));
		}
	else
		{
		# Request mode 1 : "Managed" mode via Manage Requests / Orders
		managed_collection_request($ref,getvalescaped("request",""));
		}
	redirect("pages/done.php?text=resource_request");
	}
include "../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["requestcollection"]?></h1>
  <p><?php echo text("introtext")?></p>
  
	<form method="post">  
	<input type=hidden name=ref value="<?php echo $ref?>">
	
	<div class="Question">
	<label><?php echo $lang["collectionname"]?></label>
	<div class="Fixed"><?php echo $cinfo["name"]?></div>
	<div class="clearerleft"> </div>
	</div>

	<?php 
	# Only ask for user details if this is an external share. Otherwise this is already known from the user record.
	if ($k!="") { ?>
	<div class="Question">
	<label><?php echo $lang["fullname"]?></label>
	<input type="hidden" name="fullname_label" value="<?php echo $lang["fullname"]?>">
	<input name="fullname" class="stdwidth" value="">
	<div class="clearerleft"> </div>
	</div>
	
	<div class="Question">
	<label><?php echo $lang["emailaddress"]?></label>
	<input type="hidden" name="email_label" value="<?php echo $lang["emailaddress"]?>">
	<input name="email" class="stdwidth" value="">
	<div class="clearerleft"> </div>
	</div>

	<div class="Question">
	<label><?php echo $lang["contacttelephone"]?></label>
	<input name="contact" class="stdwidth">
	<input type="hidden" name="contact_label" value="">
	<div class="clearerleft"> </div>
	</div>
	<?php } ?>
	
	<div class="Question">
	<label for="requestreason"><?php echo $lang["requestreason"]?></label>
	<textarea class="stdwidth" name="request" id="request" rows=5 cols=50></textarea>
	<div class="clearerleft"> </div>
	</div>

	<div class="QuestionSubmit">
	<label for="buttons"> </label>			
	<input name="cancel" type="button" value="&nbsp;&nbsp;<?php echo $lang["cancel"]?>&nbsp;&nbsp;" onclick="document.location='view.php?ref=<?php echo $ref?>';"/>&nbsp;
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["requestcollection"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?php
include "../include/footer.php";
?>
