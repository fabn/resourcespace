<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("r")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";

$ref=getvalescaped("ref","");

if (getval("save","")!="")
	{
	# Save research request data
	save_research_request($ref);
	redirect ("pages/team/team_research.php?reload=true&nc=" . time());
	}

# Fetch research request data
$research=get_research_request($ref);
	
include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["editresearchrequest"]?></h1>

<form method=post>
<input type=hidden name=ref value="<?php echo $ref?>">

<div class="Question"><label><?php echo $lang["nameofproject"]?></label><div class="Fixed"><?php echo $research["name"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["descriptionofproject"]?></label><div class="Fixed"><?php echo $research["description"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["requestedby"]?></label><div class="Fixed"><?php echo $research["username"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["date"]?></label><div class="Fixed"><?php echo nicedate($research["created"],false,true)?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["deadline"]?></label><div class="Fixed"><?php echo nicedate($research["deadline"],false,true)?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["contacttelephone"]?></label><div class="Fixed"><?php echo $research["contact"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["finaluse"]?></label><div class="Fixed"><?php echo $research["finaluse"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["resourcetypes"]?></label><div class="Fixed">
<?php $first=true;$set=explode(", ",$research["resource_types"]);$types=get_resource_types();for ($n=0;$n<count($types);$n++) {if (in_array($types[$n]["ref"],$set)) {if (!$first) {echo ", ";}echo $types[$n]["name"];$first=false;}} ?>
</div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["noresourcesrequired"]?></label><div class="Fixed"><?php echo $research["noresources"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["shaperequired"]?></label><div class="Fixed"><?php echo $research["shape"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["assignedtoteammember"]?></label>
<select class="shrtwidth" name="assigned_to"><option value="0"><?php echo $lang["unassigned"]?></option>
<?php $users=get_users(1);
for ($n=0;$n<count($users);$n++)
	{
	?>
	<option value="<?php echo $users[$n]["ref"]?>" <?php if ($research["assigned_to"]==$users[$n]["ref"]) {?>selected<?php } ?>><?php echo $users[$n]["username"]?></option>	
	<?php
	}
?>
</select>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["status"]?></label>
<div class="tickset">
<?php for ($n=0;$n<=2;$n++) { ?>
<div class="Inline"><input type="radio" name="status" value="<?php echo $n?>" <?php if ($research["status"]==$n) { ?>checked <?php } ?>/><?php echo $lang["requeststatus" . $n]?></div>
<?php } ?>
</div>
<div class="clearerleft"> </div></div>
</div>

<div class="Question"><label><?php echo $lang["copyexistingresources"]?></label>
<input name="copyexisting" type="checkbox" value="yes"><b><?php echo $lang["yes"]?></b> <?php echo $lang["typecollectionid"]?><br/>
<input name="copyexistingref" type="text" class="shrtwidth">
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["deletethisrequest"]?></label>
<input name="delete" type="checkbox" value="yes">
<div class="clearerleft"> </div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>