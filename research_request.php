<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/research_functions.php";

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
		redirect("done.php?text=research_request");
		}
	}

include "include/header.php";
?>

<div class="BasicsBox">
<h1><?=$lang["researchrequest"]?></h1>
<p class="tight"><?=text("introtext")?></p>
<form method="post">

<? if (getval("assign","")!="") { ?>
<div class="Question">
<label><?=$lang["requestasuser"]?></label>
<select name="as_user" class="stdwidth">
<?
$users=get_users(0,"","u.username",true);
for ($n=0;$n<count($users);$n++)
	{
	?><option value="<?=$users[$n]["ref"]?>"><?=$users[$n]["username"] . " - " . $users[$n]["fullname"] . " ("  . $users[$n]["email"] . ")"?></option>
	<?
	}
?>
</select>
<div class="clearerleft"> </div>
</div>
<? } ?>

<div class="Question">
<label><?=$lang["nameofproject"]?></label>
<input name="name" class="stdwidth" value="<?=getval("description","")?>">
<div class="clearerleft"> </div>
<? if (isset($error_name)) { ?><div class="FormError">!! <?=$lang["noprojectname"]?> !!</div><? } ?>
</div>

<div class="Question">
<label><?=$lang["descriptionofproject"]?><br/><span class="OxColourPale"><?=$lang["descriptionofprojecteg"]?></span></label>
<textarea rows=5 cols=50 name="description" class="stdwidth"><?=getval("description","")?></textarea>
<div class="clearerleft"> </div>
<? if (isset($error_description)) { ?><div class="FormError">!! <?=$lang["noprojectdescription"]?> !!</div><? } ?>
</div>

<div class="Question">
<label><?=$lang["deadline"]?></label>
<select name="deadline" class="stdwidth">
<option value=""><?=$lang["nodeadline"]?></option>
<? for ($n=0;$n<=150;$n++)
	{
	$date=time()+(60*60*24*$n);
	?><option <? $d=date("D",$date);if (($d=="Sun") || ($d=="Sat")) { ?>style="background-color:#cccccc"<? } ?> value="<?=date("Y-m-d",$date)?>"><?=nicedate(date("Y-m-d",$date),false,true)?></option>
	<?
	}
?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question" id="contacttelephone">
<label><?=$lang["contacttelephone"]?></label>
<input name="contact" class="stdwidth" value="<?=getval("contact","")?>">
<div class="clearerleft"> </div>
</div>

<div class="Question" id="finaluse">
<label><?=$lang["finaluse"]?><br/><span class="OxColourPale"><?=$lang["finaluseeg"]?></span></label>
<input name="finaluse" class="stdwidth" value="<?=getval("finaluse","")?>">
<div class="clearerleft"> </div>
</div>

<div class="Question" id="resourcetype">
<label><?=$lang["resourcetype"]?></label>
<div class="tickset lineup">
<? $types=get_resource_types();for ($n=0;$n<count($types);$n++) {?><div class="Inline"><input id="TickBox" type=checkbox name="resource<?=$types[$n]["ref"]?>" value="yes" checked>&nbsp;<?=$types[$n]["name"]?></div><? } ?>
</div>
<div class="clearerleft"> </div>
</div>

<div class="Question" id="noresourcesrequired">
<label><?=$lang["noresourcesrequired"]?></label>
<input name="noresources" class="shrtwidth" value="<?=getval("noresources","")?>">
<div class="clearerleft"> </div>
</div>

<div class="Question" id="shaperequired">
<label><?=$lang["shaperequired"]?></label>
<select name="shape" class="stdwidth">
<option><?=$lang["portrait"]?></option><option><?=$lang["landscape"]?></option><option selected><?=$lang["either"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<? if (file_exists("plugins/research_request.php")) { include "plugins/research_request.php"; } ?>


<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["sendrequest"]?>&nbsp;&nbsp;" />
</div>

</form>
</div>

<?
include "include/footer.php";
?>