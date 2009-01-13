<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/research_functions.php";

if (getval("save","")!="")
	{
	$errors=false;
	if (getvalescaped("name","")=="") {$errors=true;$error_name=true;}
	if (getvalescaped("description","")=="") {$errors=true;$error_description=true;}
	if ($errors==false) 
		{
		# Log this
		daily_stat("New research request",0);

		send_research_request();
		redirect("pages/done.php?text=research_request");
		}
	}

include "../include/header.php";
?>

<div class="BasicsBox">
<h1><?php echo $lang["researchrequest"]?></h1>
<p class="tight"><?php echo text("introtext")?></p>
<form method="post">

<?php if (getval("assign","")!="") { ?>
<div class="Question">
<label><?php echo $lang["requestasuser"]?></label>
<select name="as_user" class="stdwidth">
<?php
$users=get_users(0,"","u.username",true);
for ($n=0;$n<count($users);$n++)
	{
	?><option value="<?php echo $users[$n]["ref"]?>"><?php echo $users[$n]["username"] . " - " . $users[$n]["fullname"] . " ("  . $users[$n]["email"] . ")"?></option>
	<?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="Question">
<label><?php echo $lang["nameofproject"]?></label>
<input name="name" class="stdwidth" value="<?php echo getval("description","")?>">
<div class="clearerleft"> </div>
<?php if (isset($error_name)) { ?><div class="FormError">!! <?php echo $lang["noprojectname"]?> !!</div><?php } ?>
</div>

<div class="Question">
<label><?php echo $lang["descriptionofproject"]?><br/><span class="OxColourPale"><?php echo $lang["descriptionofprojecteg"]?></span></label>
<textarea rows=5 cols=50 name="description" class="stdwidth"><?php echo getval("description","")?></textarea>
<div class="clearerleft"> </div>
<?php if (isset($error_description)) { ?><div class="FormError">!! <?php echo $lang["noprojectdescription"]?> !!</div><?php } ?>
</div>

<div class="Question">
<label><?php echo $lang["deadline"]?></label>
<select name="deadline" class="stdwidth">
<option value=""><?php echo $lang["nodeadline"]?></option>
<?php for ($n=0;$n<=150;$n++)
	{
	$date=time()+(60*60*24*$n);
	?><option <?php $d=date("D",$date);if (($d=="Sun") || ($d=="Sat")) { ?>style="background-color:#cccccc"<?php } ?> value="<?php echo date("Y-m-d",$date)?>"><?php echo nicedate(date("Y-m-d",$date),false,true)?></option>
	<?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question" id="contacttelephone">
<label><?php echo $lang["contacttelephone"]?></label>
<input name="contact" class="stdwidth" value="<?php echo getval("contact","")?>">
<div class="clearerleft"> </div>
</div>

<div class="Question" id="finaluse">
<label><?php echo $lang["finaluse"]?><br/><span class="OxColourPale"><?php echo $lang["finaluseeg"]?></span></label>
<input name="finaluse" class="stdwidth" value="<?php echo getval("finaluse","")?>">
<div class="clearerleft"> </div>
</div>

<div class="Question" id="resourcetype">
<label><?php echo $lang["resourcetype"]?></label>
<div class="tickset lineup">
<?php $types=get_resource_types();for ($n=0;$n<count($types);$n++) {?><div class="Inline"><input id="TickBox" type=checkbox name="resource<?php echo $types[$n]["ref"]?>" value="yes" checked>&nbsp;<?php echo $types[$n]["name"]?></div><?php } ?>
</div>
<div class="clearerleft"> </div>
</div>

<div class="Question" id="noresourcesrequired">
<label><?php echo $lang["noresourcesrequired"]?></label>
<input name="noresources" class="shrtwidth" value="<?php echo getval("noresources","")?>">
<div class="clearerleft"> </div>
</div>

<div class="Question" id="shaperequired">
<label><?php echo $lang["shaperequired"]?></label>
<select name="shape" class="stdwidth">
<option><?php echo $lang["portrait"]?></option><option><?php echo $lang["landscape"]?></option><option selected><?php echo $lang["either"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<?php if (file_exists("plugins/research_request.php")) { include "plugins/research_request.php"; } ?>


<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["sendrequest"]?>&nbsp;&nbsp;" />
</div>

</form>
</div>

<?php
include "../include/footer.php";
?>