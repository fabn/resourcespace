<?
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/resource_functions.php";

# Fetch user data

if (getval("from","")!="")
	{
	# Copy data
	$to=copy_resource(getvalescaped("from",""));
	if ($to===false) {$error=true;} else
		{
		redirect("pages/edit.php?ref=" . $to);
		}
	}

include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["copyresource"]?></h1>

<p><?=text("introtext")?></p>

<form method=post>

<div class="Question"><label><?=$lang["resourceid"]?></label><input name="from" type="text" class="shrtwidth" value="">
<? if (isset($error)) { ?><div class="FormError">!! <?=$lang["resourceidnotfound"]?> !!</div><? } ?><div class="clearerleft"> </div></div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["copyresource"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "../../include/footer.php";
?>