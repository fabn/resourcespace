<?php
include "../include/db.php";
$k=getvalescaped("k","");if ($k=="") {include "../include/authenticate.php";}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/request_functions.php";

$ref=getval("ref","");
$cinfo=get_collection($ref);
$error=false;

if (getval("save","")!="")
	{
	if ($k!="" || $userrequestmode==0)
		{
		# Request mode 0 : Simply e-mail the request.
		$result=email_collection_request($ref,getvalescaped("request",""));
		}
	else
		{
		# Request mode 1 : "Managed" mode via Manage Requests / Orders
		$result=managed_collection_request($ref,getvalescaped("request",""));
		}
	if ($result===false)
		{
		$error=$lang["requiredfields"];
		}
	else
		{
		redirect("pages/done.php?text=resource_request&k=" . $k);
		}
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
	<input name="fullname" class="stdwidth" value="<?php echo getval("fullname","") ?>"">
	<div class="clearerleft"> </div>
	</div>
	
	<div class="Question">
	<label><?php echo $lang["emailaddress"]?></label>
	<input type="hidden" name="email_label" value="<?php echo $lang["emailaddress"]?>">
	<input name="email" class="stdwidth" value="<?php echo getval("email","") ?>">
	<div class="clearerleft"> </div>
	</div>

	<div class="Question">
	<label><?php echo $lang["contacttelephone"]?></label>
	<input name="contact" class="stdwidth" value="<?php echo getval("contact","") ?>">
	<input type="hidden" name="contact_label" value="<?php echo $lang["contacttelephone"]?>">
	<div class="clearerleft"> </div>
	</div>
	<?php } ?>
	
	<div class="Question">
	<label for="requestreason"><?php echo $lang["requestreason"]?> <sup>*</sup></label>
	<textarea class="stdwidth" name="request" id="request" rows=5 cols=50><?php echo getval("request","") ?></textarea>
	<div class="clearerleft"> </div>
	</div>


<?php # Add custom fields 
if (isset($custom_request_fields))
	{
	$custom=explode(",",$custom_request_fields);
	$required=explode(",",$custom_request_required);
	
	for ($n=0;$n<count($custom);$n++)
		{
		$type=1;
		
		# Support different question types for the custom fields.
		if (isset($custom_request_types[$custom[$n]])) {$type=$custom_request_types[$custom[$n]];}
		
		if ($type==4)
			{
			# HTML type - just output the HTML.
			echo $custom_request_html[$custom[$n]];
			}
		else
			{
			?>
			<div class="Question">
			<label for="custom<?php echo $n?>"><?php echo htmlspecialchars(i18n_get_translated($custom[$n]))?>
			<?php if (in_array($custom[$n],$required)) { ?><sup>*</sup><?php } ?>
			</label>
			
			<?php if ($type==1) {  # Normal text box
			?>
			<input type=text name="custom<?php echo $n?>" id="custom<?php echo $n?>" class="stdwidth" value="<?php echo htmlspecialchars(getvalescaped("custom" . $n,""))?>">
			<?php } ?>

			<?php if ($type==2) { # Large text box 
			?>
			<textarea name="custom<?php echo $n?>" id="custom<?php echo $n?>" class="stdwidth" rows="5"><?php echo htmlspecialchars(getvalescaped("custom" . $n,""))?></textarea>
			<?php } ?>

			<?php if ($type==3) { # Drop down box
			?>
			<select name="custom<?php echo $n?>" id="custom<?php echo $n?>" class="stdwidth">
			<?php foreach ($custom_request_options[$custom[$n]] as $option)
				{
				$val=i18n_get_translated($option);
				?>
				<option <?php if (getval("custom" . $n,"")==$val) { ?>selected<?php } ?>><?php echo htmlspecialchars(i18n_get_translated($option));?></option>
				<?php
				}
			?>
			</select>
			<?php } ?>
			
			<div class="clearerleft"> </div>
			</div>
			<?php
			}
		}
	}
?>


	<div class="QuestionSubmit">
	<?php if ($error) { ?><div class="FormError">!! <?php echo $error ?> !!</div><?php } ?>
	<label for="buttons"> </label>			
	<input name="cancel" type="button" value="&nbsp;&nbsp;<?php echo $lang["cancel"]?>&nbsp;&nbsp;" onclick="document.location='view.php?ref=<?php echo $ref?>';"/>&nbsp;
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["requestcollection"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?php
include "../include/footer.php";
?>
