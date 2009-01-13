<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";

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

include "../../include/header.php";

if (getval("print","")!="") { # Launch printable page in an iframe
?>
<iframe width=1 height=1 style="visibility:hidden" src="team_stats_print.php?year=<?php echo $year?>&groupselect=<?php echo $groupselect?>&groups=<?php echo join("_",$groups)?>"></iframe>
<?php } ?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["viewstatistics"]?></h1>
  <p><?php echo text("introtext")?></p>
  
  <form method="post">
	<div class="Question">
<label for="activity_type"><?php echo $lang["activity"]?></label><select id="activity_type" name="activity_type" class="shrtwidth">
<?php $types=get_stats_activity_types(); 
for ($n=0;$n<count($types);$n++)
	{
	?><option <?php if ($activity_type==$types[$n]) { ?>selected<?php } ?> value="<?php echo $types[$n]?>"><?php echo $lang["stat-" . strtolower(str_replace(" ","",$types[$n]))]?></option><?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>

	<div class="Question">
<label for="year"><?php echo $lang["year"]?></label><select id="year" name="year" class="shrtwidth">
<?php $years=get_stats_years(); 
for ($n=0;$n<count($years);$n++)
	{
	?><option <?php if ($year==$years[$n]) { ?>selected<?php } ?>><?php echo $years[$n]?></option><?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>


<div class="Question">
<label for="groupselect"><?php echo $lang["group"]?></label><select id="groupselect" name="groupselect" class="shrtwidth"
onchange="if (this.value=='viewall') {document.getElementById('groupselector').style.display='none';}
else {document.getElementById('groupselector').style.display='block';}">
<?php if (!checkperm("U")) { ?><option <?php if ($groupselect=="viewall") { ?>selected<?php } ?> value="viewall"><?php echo $lang["viewall"]?></option><?php } ?>
<option <?php if ($groupselect=="select") { ?>selected<?php } ?> value="select"><?php echo $lang["select"]?></option>
</select>
<div class="clearerleft"> </div>
	<table id="groupselector" cellpadding=3 cellspacing=3 style="padding-left:150px;<?php if ($groupselect=="viewall") { ?>display:none;<?php } ?>">
	<?php
	$grouplist=get_usergroups(true);
	for ($n=0;$n<count($grouplist);$n++)
		{
		?>
		<tr>
		<td valign=middle nowrap><?php echo htmlspecialchars($grouplist[$n]["name"])?>&nbsp;&nbsp;</td>
		<td width=10 valign=middle><input type=checkbox name="groups[]" value="<?php echo $grouplist[$n]["ref"]?>" <?php if (in_array($grouplist[$n]["ref"],$groups)) { ?>checked<?php } ?>></td>
		</tr>
		<?php
		}
	?></table>
	<div class="clearerleft"> </div>
</div>

<div class="Question" >
<label for="groups">&nbsp;</label>

<div class="clearerleft"> </div>
</div>


<div class="Question">
<label for="print"><?php echo $lang["printallforyear"]?></label><input type=checkbox name="print" id="print" value="yes">
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["viewstatistics"]?>&nbsp;&nbsp;" />
</div>
</form>

	<?php if ($activity_type!="") { ?>	
	<br/>
	<div class="BasicsBox">
	<img style="border:1px solid black;" src="../graph.php?activity_type=<?php echo urlencode($activity_type)?>&year=<?php echo $year?>&groupselect=<?php echo $groupselect?>&groups=<?php echo join("_",$groups)?>" width=700 height=350>
	</div>
	<?php } ?>
	
  </div>

<?php
include "../../include/footer.php";
?>