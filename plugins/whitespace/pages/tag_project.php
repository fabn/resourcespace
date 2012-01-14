<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("n")) {exit("Permission denied");}
include "../../../include/general.php";
include "../../../include/resource_functions.php";
include "../../../include/search_functions.php";
include "../include/airotek_functions.php";

$ref=getval("ref","");
$project=get_project($ref);


if (getval("next","")!="")
	{
	redirect("plugins/airotek/pages/tag.php?project=" . $ref);
	}

include "../../../include/header.php";
?>
<style>#CentralSpaceContainer {margin:0px 25px 0px;padding:0 0px 0 0;text-align:left;} #CentralSpace {margin-left: 0px; padding: 0; }</style>

<div class="BasicsBox" style=""> 
<form method="post" id="mainform">

<div class="RecordBox" style="margin-right:-15px;">
<div class="RecordPanel">

<h1>Project Summary</h1>

<div class="Question">
<label for="keywords">Description</label>
<?php echo $lang['desc-'.$project['field']]?>
</div>

<div class="Question">
<label for="keywords">Image Range</label>
all
</div>

<div class="Question">
<label for="keywords">Total Number of Poles:</label>
<?php $result=do_search("");
echo count($result);?>
</div>

<div class="Question">
<label for="keywords">Specific Project / Analyst:</label>
<?php $field=get_fields(array($project["field"])); echo $field[0]['title'];?>
</div>

<div class="Question">
<label for="keywords">Due Date:</label>
<?php echo nicedate($project['due'],true)?>
</div>

<div class="Question">
<label for="keywords">Notes:</label>
<?php echo $project['notes']?>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="next" type="submit" default value="&nbsp;&nbsp;<?php echo $lang["next"]?>&nbsp;&nbsp;" />
</div>

<div class="clearerleft"> </div>
</div></div>



<p><?php echo $lang["leaderboard"]?><table>
<?php
$lb=sql_query("select u.fullname,count(*) c from user u join resource_log rl on rl.user=u.ref where rl.resource_type_field='$speedtaggingfield' group by u.ref order by c desc limit 5;");
for ($n=0;$n<count($lb);$n++)
	{
	?>
	<tr><td><?php echo $lb[$n]["fullname"]?></td><td><?php echo $lb[$n]["c"]?></td></tr>
	<?php
	}
?>
</table></p>

</form>
</div>
<?php
include "../../../include/footer.php";
?>
<script src="<?php echo $magictouch_secure ?>://www.magictoolbox.com/mt/<?php echo $magictouch_account_id ?>/magictouch.js" type="text/javascript" defer="defer"></script>
