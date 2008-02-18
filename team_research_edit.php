<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("r")) {exit ("Permission denied.");}
include "include/general.php";
include "include/research_functions.php";

$ref=getvalescaped("ref","");

if (getval("save","")!="")
	{
	# Save research request data
	save_research_request($ref);
	redirect ("team_research.php?reload=true&nc=" . time());
	}

# Fetch research request data
$research=get_research_request($ref);
	
include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["editresearchrequest"]?></h1>

<form method=post>
<input type=hidden name=ref value="<?=$ref?>">

<div class="Question"><label><?=$lang["nameofproject"]?></label><div class="Fixed"><?=$research["name"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["descriptionofproject"]?></label><div class="Fixed"><?=$research["description"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["requestedby"]?></label><div class="Fixed"><?=$research["username"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["date"]?></label><div class="Fixed"><?=nicedate($research["created"],false,true)?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["deadline"]?></label><div class="Fixed"><?=nicedate($research["deadline"],false,true)?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["contacttelephone"]?></label><div class="Fixed"><?=$research["contact"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["finaluse"]?></label><div class="Fixed"><?=$research["finaluse"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["resourcetypes"]?></label><div class="Fixed">
<? $first=true;$set=explode(", ",$research["resource_types"]);$types=get_resource_types();for ($n=0;$n<count($types);$n++) {if (in_array($types[$n]["ref"],$set)) {if (!$first) {echo ", ";}echo $types[$n]["name"];$first=false;}} ?>
</div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["noresourcesrequired"]?></label><div class="Fixed"><?=$research["noresources"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["shaperequired"]?></label><div class="Fixed"><?=$research["shape"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["assignedtoteammember"]?></label>
<select class="shrtwidth" name="assigned_to"><option value="0"><?=$lang["unassigned"]?></option>
<? $users=get_users(1);
for ($n=0;$n<count($users);$n++)
	{
	?>
	<option value="<?=$users[$n]["ref"]?>" <? if ($research["assigned_to"]==$users[$n]["ref"]) {?>selected<?}?>><?=$users[$n]["username"]?></option>	
	<?
	}
?>
</select>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["status"]?></label>
<div class="tickset">
<? for ($n=0;$n<=2;$n++) { ?>
<div class="Inline"><input type="radio" name="status" value="<?=$n?>" <? if ($research["status"]==$n) { ?>checked <? } ?>/><?=$lang["requeststatus" . $n]?></div>
<? } ?>
</div>
<div class="clearerleft"> </div></div>
</div>

<div class="Question"><label><?=$lang["copyexistingresources"]?></label>
<input name="copyexisting" type="checkbox" value="yes"><b><?=$lang["yes"]?></b> <?=$lang["typecollectionid"]?><br/>
<input name="copyexistingref" type="text" class="shrtwidth">
<div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["deletethisrequest"]?></label>
<input name="delete" type="checkbox" value="yes">
<div class="clearerleft"> </div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "include/footer.php";
?>