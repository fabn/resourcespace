<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../../../include/general.php";
include "../../../include/collections_functions.php";
include "../../../include/resource_functions.php";
include "../../../include/search_functions.php"; 
include "../../../include/plugin_functions.php"; 
include "../include/airotek_functions.php";


$ref=getvalescaped("ref","",true);
$offset=getval("offset",0);
$find=getvalescaped("find","");
$col_order_by=getvalescaped("col_order_by","name");
$sort=getval("sort","ASC");


$collection=get_project($ref);

if (getval("name","")!="")
	{
	# Save collection data
	save_project($ref);	
	redirect("plugins/airotek/pages/tag_project_manage.php");
	}

	
include "../../../include/header.php";
?>
<div class="BasicsBox">
<h1>Edit Project</h1>
<p>This page allows you to give your project a name, select the field to be analyzed, assign a user, due date, and notes. It will need a locking option as well.</p>

<form method=post id="collectionform">
<input type=hidden name=redirect id=redirect value=yes>
<input type=hidden name=ref value="<?php echo $ref?>">

<div class="Question">
<label for="name">Name</label><input type=text class="stdwidth" name="name" id="name" value="<?php echo $collection["name"]?>" maxlength="100">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="field">Field</label><select class="stdwidth" name="field" id="field" value="<?php echo $collection["field"]?>">
<option value="1096" <?php if ($collection['field']==1096){?>selected<?php } ?>>Encroachment</option>
<option value="1095" <?php if ($collection['field']==1095){?>selected<?php } ?>>Framing</option></option>
<!--<option value="1097" <?php if ($collection['field']==1097){?>selected<?php } ?>>Aviary</option>-->
<option value="1094" <?php if ($collection['field']==1094){?>selected<?php } ?>>Attachments</option>
</select>
<div class="clearerleft"> </div>
</div>
<?php config_userselect_field("assign","Assign",$collection['user'],false) ?>

<div class="Question">
<label for="due">Due</label><input type=text class="stdwidth" name="due" id="due" value="<?php echo $collection["due"]?>" maxlength="100">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="notes">Notes</label><textarea class="stdwidth" name="notes" id="notes"><?php echo $collection["notes"]?></textarea>
<div class="clearerleft"> </div>
</div>


<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../../include/footer.php";
?>
