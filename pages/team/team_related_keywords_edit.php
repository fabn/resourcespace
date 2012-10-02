<?php
/**
 * Edit related keywords page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("k")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";

$keyword=strtolower(getvalescaped("keyword",""));
$related=strtolower(getvalescaped("related",""));

if (getval("save","")!="")
	{
	# Save data
	save_related_keywords($keyword,$related);
	redirect ("pages/team/team_related_keywords.php?nc=" . time());
	}

# Fetch existing relationships
$related=get_grouped_related_keywords("",$keyword);
if (count($related)==0)
	{
	$related="";
	}
else
	{
	$related=$related[0]["related"];
	}

include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["managerelatedkeywords"]?></h1>

<form method=post id="mainform" action="team_related_keywords_edit.php">
<input type="hidden" name="keyword" value="<?php echo $keyword?>">

<div class="Question"><label><?php echo $lang["keyword"]?></label><div class="Fixed"><?php echo $keyword?></div><div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["relatedkeywords"]?></label><textarea name="related" class="stdwidth" rows=5 cols=50><?php echo htmlspecialchars($related)?></textarea><div class="clearerleft"></div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>