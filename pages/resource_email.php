<?php
include "../include/db.php";
include "../include/authenticate.php"; if (!checkperm("g") && !checkperm("v")) {exit ("Permission denied.");} # Cannot e-mail if can't see hi-res images. To avoid loophole whereby users could email resources to an external address, and hence download hi-res versions.
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","",true);
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
	$access=getvalescaped("access","");
	if (hook("modifyresourceaccess")){$access=hook("modifyresourceaccess");}
	$expires=getvalescaped("expires","");
	$errors=email_resource($ref,$resource["field".$view_title_field],$userfullname,$users,$message,$access,$expires);
	if ($errors=="")
		{
		# Log this			
		daily_stat("E-mailed resource",$ref);

		redirect("pages/done.php?text=resource_email&resource=$ref");
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
<label><?php echo $lang["resourcetitle"]?></label><div class="Fixed"><?php echo htmlspecialchars(i18n_get_translated($resource["field".$view_title_field]))?></div>
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

<?php if(!hook("replaceemailtousers")){?>
<div class="Question">
<label for="users"><?php echo $lang["emailtousers"]?></label><?php include "../include/user_select.php"; ?>
<div class="clearerleft"> </div>
<?php if ($errors!="") { ?><div class="FormError">!! <?php echo $errors?> !!</div><?php } ?>
</div>
<?php } ?>

<?php if(!hook("replaceemailaccessselector")){?>
<div class="Question" id="question_access">
<label for="archive"><?php echo $lang["externalselectresourceaccess"]?></label>
<select class="stdwidth" name="access" id="access">
<?php
# List available access levels. The highest level must be the minimum user access level.
for ($n=$access;$n<=1;$n++) { ?>
<option value="<?php echo $n?>"><?php echo $lang["access" . $n]?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<?php if(!hook("replaceemailexpiryselector")){?>
<div class="Question">
<label><?php echo $lang["externalselectresourceexpires"]?></label>
<select name="expires" class="stdwidth">
<option value=""><?php echo $lang["never"]?></option>
<?php for ($n=1;$n<=150;$n++)
	{
	$date=time()+(60*60*24*$n);
	?><option <?php $d=date("D",$date);if (($d=="Sun") || ($d=="Sat")) { ?>style="background-color:#cccccc"<?php } ?> value="<?php echo date("Y-m-d",$date)?>"><?php echo nicedate(date("Y-m-d",$date),false,true)?></option>
	<?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<?php if(!hook("replaceemailsubmitbutton")){?>
<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["emailresource"]?>&nbsp;&nbsp;" />
</div>
<?php } # end replaceemailsubmitbutton ?>

</form>
</div>

<?php		
include "../include/footer.php";
?>
