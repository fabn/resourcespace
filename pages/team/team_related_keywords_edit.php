<?
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("o")) {exit ("Permission denied.");}
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
<h1><?=$lang["managecontent"]?></h1>

<form method=post id="mainform">
<input type="hidden" name="keyword" value="<?=$keyword?>">

<div class="Question"><label><?=$lang["keyword"]?></label><div class="Fixed"><?=$keyword?></div><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["relatedkeywords"]?></label><textarea name="related" class="stdwidth" rows=5 cols=50><?=htmlspecialchars($related)?></textarea><div class="clearerleft"></div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "../../include/footer.php";
?>