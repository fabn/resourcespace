<?
include "include/db.php";
include "include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "include/general.php";

$activity_type=getvalescaped("activity_type","User session");
$year=getvalescaped("year",date("Y"));
$groupselect=getvalescaped("groupselect","viewall");
if ($groupselect=="select")
	{
	$groups=@$_POST["groups"];
	}
else
	{
	$groups=array();
	}

# For child group only access, set that selected groups only are displayed by default
# and select all available groups (if none already selected).
if (checkperm("U"))
	{
	$groupselect="select";
	if (count($groups)==0)
		{
		# No groups selected, add to groups array.
		$groups=array();
		$grouplist=get_usergroups(true);
		for ($n=0;$n<count($grouplist);$n++)
			{
			$groups[]=$grouplist[$n]["ref"];
			}
		}
	}

include "include/header.php";

if (getval("print","")!="") { # Launch printable page in an iframe
?>
<iframe width=1 height=1 style="visibility:hidden" src="team_stats_print.php?year=<?=$year?>&groupselect=<?=$groupselect?>&groups=<?=join("_",$groups)?>"></iframe>
<? } ?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["viewstatistics"]?></h1>
  <p><?=text("introtext")?></p>
  
  <form method="post">
	<div class="Question">
<label for="activity_type"><?=$lang["activity"]?></label><select id="activity_type" name="activity_type" class="shrtwidth">
<? $types=get_stats_activity_types(); 
for ($n=0;$n<count($types);$n++)
	{
	?><option <? if ($activity_type==$types[$n]) { ?>selected<? } ?> value="<?=$types[$n]?>"><?=$lang["stat-" . strtolower(str_replace(" ","",$types[$n]))]?></option><?
	}
?>
</select>
<div class="clearerleft"> </div>
</div>

	<div class="Question">
<label for="year"><?=$lang["year"]?></label><select id="year" name="year" class="shrtwidth">
<? $years=get_stats_years(); 
for ($n=0;$n<count($years);$n++)
	{
	?><option <? if ($year==$years[$n]) { ?>selected<? } ?>><?=$years[$n]?></option><?
	}
?>
</select>
<div class="clearerleft"> </div>
</div>


<div class="Question">
<label for="groupselect"><?=$lang["group"]?></label><select id="groupselect" name="groupselect" class="shrtwidth"
onchange="if (this.value=='viewall') {document.getElementById('groupselector').style.display='none';}
else {document.getElementById('groupselector').style.display='block';}">
<? if (!checkperm("U")) { ?><option <? if ($groupselect=="viewall") { ?>selected<? } ?> value="viewall"><?=$lang["viewall"]?></option><? } ?>
<option <? if ($groupselect=="select") { ?>selected<? } ?> value="select"><?=$lang["select"]?></option>
</select>
<div class="clearerleft"> </div>
	<table id="groupselector" cellpadding=3 cellspacing=3 style="padding-left:150px;<? if ($groupselect=="viewall") { ?>display:none;<? } ?>">
	<?
	$grouplist=get_usergroups(true);
	for ($n=0;$n<count($grouplist);$n++)
		{
		?>
		<tr>
		<td valign=middle nowrap><?=htmlspecialchars($grouplist[$n]["name"])?>&nbsp;&nbsp;</td>
		<td width=10 valign=middle><input type=checkbox name="groups[]" value="<?=$grouplist[$n]["ref"]?>" <? if (in_array($grouplist[$n]["ref"],$groups)) { ?>checked<? } ?>></td>
		</tr>
		<?
		}
	?></table>
	<div class="clearerleft"> </div>
</div>

<div class="Question" >
<label for="groups">&nbsp;</label>

<div class="clearerleft"> </div>
</div>


<div class="Question">
<label for="print"><?=$lang["printallforyear"]?></label><input type=checkbox name="print" id="print" value="yes">
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["viewstatistics"]?>&nbsp;&nbsp;" />
</div>
</form>

	<? if ($activity_type!="") { ?>	
	<br/>
	<div class="BasicsBox">
	<img style="border:1px solid black;" src="graph.php?activity_type=<?=urlencode($activity_type)?>&year=<?=$year?>&groupselect=<?=$groupselect?>&groups=<?=join("_",$groups)?>" width=600 height=250>
	</div>
	<? } ?>
	
  </div>

<?
include "include/footer.php";
?>