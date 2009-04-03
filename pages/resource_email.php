<?php
include "../include/db.php";
include "../include/authenticate.php"; if (!checkperm("g") && !checkperm("v")) {exit ("Permission denied.");} # Cannot e-mail if can't see hi-res images. To avoid loophole whereby users could email resources to an external address, and hence download hi-res versions.
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","");
# Fetch resource data
$resource=get_resource_data($ref);if ($resource===false) {exit("Resource not found.");}

# Load access level and check.
$access=get_resource_access($ref);
if (!($allow_share && ($access==0 || ($access==1 && $restricted_share)))) {exit("Access denied.");}

$errors="";
if (getval("save","")!="")
	{
	# Email resource
	# Build a new list and insert
	$users=getvalescaped("users","");
	$message=getvalescaped("message","");
	$errors=email_resource($ref,$resource["title"],$userfullname,$users,$message);
	if ($errors=="")
		{
		# Log this			
		daily_stat("E-mailed resource",$ref);

		redirect("pages/done.php?text=resource_email");
		}
	}

include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["emailresource"]?></h1>

<p><?php echo text("introtext")?></p>

<form method=post id="resourceform">
<input type=hidden name=ref value="<?php echo $ref?>">

<div class="Question">
<label><?php echo $lang["resourcetitle"]?></label><div class="Fixed"><?php echo htmlspecialchars(i18n_get_translated($resource["title"]))?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["resourceid"]?></label><div class="Fixed"><?php echo $resource["ref"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="message"><?php echo $lang["message"]?></label><textarea class="stdwidth" rows=6 cols=50 name="message" id="message"></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="users"><?php echo $lang["emailtousers"]?></label><?php include "../include/user_select.php"; ?>
<div class="clearerleft"> </div>
<?php if ($errors!="") { ?><div class="FormError">!! <?php echo $errors?> !!</div><?php } ?>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["emailresource"]?>&nbsp;&nbsp;" />
</div>

</form>
</div>

<?php		
include "../include/footer.php";
?>