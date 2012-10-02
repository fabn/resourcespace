<?php
include "../include/db.php";
include "../include/authenticate.php"; if (!checkperm("g") && !checkperm("v")) {exit ("Permission denied.");} # Cannot e-mail if can't see hi-res images. To avoid loophole whereby users could email resources to an external address, and hence download hi-res versions.
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","",true);
# Fetch resource data
$resource=get_resource_data($ref);if ($resource===false) {exit("Resource not found.");}

# fetch the current search 
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);


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
	
	$use_user_email=getvalescaped("use_user_email",false);
	if ($use_user_email){$user_email=$useremail;} else {$user_email="";} // if use_user_email, set reply-to address
	if (!$use_user_email){$from_name=$applicationname;} else {$from_name=$userfullname;} // make sure from_name matches system name
	
	if (getval("ccme",false)){ $cc=$useremail;} else {$cc="";}
	
	$errors=email_resource($ref,$resource["field".$view_title_field],$userfullname,$users,$message,$access,$expires,$user_email,$from_name,$cc);
	if ($errors=="")
		{
		# Log this			
		daily_stat("E-mailed resource",$ref);
		if (!hook("replaceresourceemailredirect")){
			redirect("pages/done.php?text=resource_email&resource=$ref&search=".urlencode($search)."&offset=".$offset."&order_by=".$order_by."&sort=".$sort."&archive=".$archive);
		}
		}
	}

include "../include/header.php";
?>
<div class="BasicsBox">
<p><a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresourceview"]?></a></p>
<h1><?php echo $lang["emailresource"]?></h1>

<p><?php echo text("introtext")?></p>

<form method=post id="resourceform" action="resource_email.php">
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

<?php if ($email_from_user){?>
<?php if ($useremail!="") { # Only allow this option if there is an email address available for the user.
?>
<div class="Question">
<label for="use_user_email"><?php echo $lang["emailfromuser"].$useremail.". ".$lang["emailfromsystem"].$email_from ?></label><input type=checkbox checked id="use_user_email" name="use_user_email">
<div class="clearerleft"> </div>
</div>
<?php } ?>
<?php } ?>

<?php if ($cc_me && $useremail!=""){?>
<div class="Question">
<label for="ccme"><?php echo str_replace("%emailaddress", $useremail, $lang["cc-emailaddress"]); ?></label><input type=checkbox checked id="ccme" name="ccme">
<div class="clearerleft"> </div>
</div>
<?php } ?>

<?php hook("additionalemailfield");?>

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
