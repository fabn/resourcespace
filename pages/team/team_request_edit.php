<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("R")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/request_functions.php";
include "../../include/collections_functions.php";

$ref=getvalescaped("ref","");

if (getval("save","")!="")
	{
	# Save research request data
	save_request($ref);
	redirect ("pages/team/team_request.php?reload=true&nc=" . time());
	}

# Fetch research request data
$request=get_request($ref);
	
include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["editrequestorder"]?></h1>

<form method=post>
<input type=hidden name=ref value="<?php echo $ref?>">

<div class="Question"><label><?php echo $lang["requestedby"]?></label><div class="Fixed"><?php echo $request["username"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["date"]?></label><div class="Fixed"><?php echo nicedate($request["created"],false,true)?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["comments"]?></label><div class="Fixed"><?php echo $request["comments"]?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["status"]?></label>
<div class="tickset">
<?php for ($n=0;$n<=1;$n++) { ?>
<div class="Inline"><input type="radio" name="status" value="<?php echo $n?>" <?php if ($request["status"]==$n) { ?>checked <?php } ?>/><?php echo $lang["resourcerequeststatus0" . $n]?></div>
<?php } ?>
</div>
<div class="clearerleft"> </div></div>
</div>

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