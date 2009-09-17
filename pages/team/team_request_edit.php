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
if ($request===false) {exit("Request $ref not found.");}
	
include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["editrequestorder"]?></h1>

<form method=post>
<input type=hidden name=ref value="<?php echo $ref?>">

<div class="Question"><label><?php echo $lang["requestedby"]?></label><div class="Fixed"><?php echo $request["fullname"]?> (<?php echo $request["username"]?> / <?php echo $request["email"]?>)</div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["date"]?></label><div class="Fixed"><?php echo nicedate($request["created"],true,true)?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["comments"]?></label><div class="Fixed"><?php echo nl2br($request["comments"]) ?></div>
<div class="clearerleft"> </div></div>

<div class="Question"><label><?php echo $lang["requesteditems"]?></label><div class="Fixed"><a <?php if ($frameless_collections) { ?>href="../search.php?search=<?php echo urlencode("!collection" . $request["collection"]) ?>"
<?php } else {?>href="../collections.php?collection=<?php echo $request["collection"]?>" target="collections"<?php }?>>&gt;&nbsp;<?php echo $lang["action-select"]?></a></div>
<div class="clearerleft"> </div></div>


<div class="Question"><label><?php echo $lang["status"]?></label>
<div class="tickset">
<?php for ($n=0;$n<=2;$n++) { ?>
<div class="Inline"><input type="radio" name="status" value="<?php echo $n?>" <?php if ($request["status"]==$n) { ?>checked <?php } ?>
<?php if ($n==1) { ?> onClick="Effect.Appear('Expires',{duration:1});"<?php } else { ?>onClick="Effect.DropOut('Expires',{duration:1});"<?php } ?>

/><?php echo $lang["resourcerequeststatus" . $n]?></div>
<?php } ?>
</div>
<div class="clearerleft"> </div></div>
</div>

<div class="Question" id="Expires" <?php if ($request["status"]!=1) { ?>style="display:none;"<?php } ?>>
<label><?php echo $lang["expires"]?></label>
<select name="expires" class="stdwidth">
<option value=""><?php echo $lang["never"]?></option>
<?php for ($n=1;$n<=150;$n++)
	{
	$date=time()+(60*60*24*$n);
	$dateval=date("Y-m-d",$date);
	?><option <?php $d=date("D",$date);if (($d=="Sun") || ($d=="Sat")) { ?>style="background-color:#cccccc"<?php } ?> value="<?php echo $dateval ?>" <?php if ($dateval==$request["expires"]) { ?>selected<?php } ?>><?php echo nicedate(date("Y-m-d",$date),false,true)?></option>
	<?php
	}
?>
</select>
<div class="clearerleft"> </div>
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