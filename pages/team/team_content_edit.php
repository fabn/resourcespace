<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("o")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";

$page=getvalescaped("page","");
$name=getvalescaped("name","");
$find=getvalescaped("find","");
$newhelp=getvalescaped("newhelp","");
$editlanguage=getvalescaped("editlanguage",$defaultlanguage);
$editgroup=getvalescaped("editgroup","");

# get custom value from database, unless it has been newly passed from team_content.php
if (getval("custom","")==1){ $custom=1; $newcustom=true; } else {$custom=check_site_text_custom($page,$name); $newcustom=false;}

# Fetch user data
$text=get_site_text($page,$name,$editlanguage,$editgroup);

if ((getval("save","")!="") && (getval("langswitch","")==""))
	{
	# Save data
	save_site_text($page,$name,$editlanguage,$editgroup);
	if ($newhelp!=""){
		redirect("pages/team/team_content_edit.php?page=help&name=".$newhelp);
		}
	if (getval("custom","")==1){
		redirect("pages/team/team_content_edit.php?page=$page&name=$name");
		}	
	redirect ("pages/team/team_content.php?nc=" . time()."&find=".$find);
	}

include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["managecontent"]?></h1>

<form method=post id="mainform">
<input type=hidden name=page value="<?php echo $page?>">
<input type=hidden name=name value="<?php echo $name?>">
<input type=hidden name=langswitch id=langswitch value="">
<input type=hidden name=groupswitch id=groupswitch value="">

<div class="Question"><label><?php echo $lang["page"]?></label><div class="Fixed"><?php echo $page?></div><div class="clearerleft"> </div></div>
<?php if ($page=="help"){?>
<div class="Question"><label><?php echo $lang["name"]?></label><input type=text name="name" class="stdwidth" value="<?php echo htmlspecialchars($name)?>">
<?php } else { ?>
<div class="Question"><label><?php echo $lang["name"]?></label><div class="Fixed"><?php echo $name?></div><div class="clearerleft"> </div></div>
<?php } ?>

<div class="Question">
<label for="password"><?php echo $lang["language"]?></label>
<select class="stdwidth" name="editlanguage" onchange="document.getElementById('langswitch').value='yes';document.getElementById('mainform').submit();">
<?php foreach ($languages as $key=>$value) { ?>
<option value="<?php echo $key?>" <?php if ($editlanguage==$key) { ?>selected<?php } ?>><?php echo $value?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>

<?php if(!hook("managecontenteditgroupselector")){ ?>
<div class="Question">
<label for="password"><?php echo $lang["group"]?></label>
<select class="stdwidth" name="editgroup" onchange="document.getElementById('groupswitch').value='yes';document.getElementById('mainform').submit();">
<option value=""></option>
<?php 
$groups=get_usergroups();
for ($n=0;$n<count($groups);$n++) { ?>
<option value="<?php echo $groups[$n]["ref"]?>" <?php if ($editgroup==$groups[$n]["ref"]) { ?>selected<?php } ?>><?php echo $groups[$n]["name"]?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } /* End managecontenteditgroupselector */?>

<div class="Question"><label><?php echo $lang["text"]?></label><textarea name="text" class="stdwidth" rows=15 cols=50><?php echo htmlspecialchars($text)?></textarea><div class="clearerleft"> </div></div>

<!-- disabled next two as they are in system setup, and making these available on the team centre could lead to accidental deletes and copies.
<div class="Question"><label>Tick to delete this item</label><input name="deleteme" type="checkbox" value="yes"><div class="clearerleft"> </div></div>

<div class="Question"><label>Tick to save as a copy</label><input name="copyme" type="checkbox" value="yes"><div class="clearerleft"> </div></div>
-->

<?php # add special ability to create and remove help pages
if ($page=="help") { ?>
<?php if ($name!="introtext"){ ?>
	<div class="Question"><label><?php echo $lang["ticktodeletehelp"]?></label><input name="deleteme" type="checkbox" value="yes"><div class="clearerleft"> </div></div>
<?php } ?><br><br>
<label><?php echo $lang["createnewhelp"]?></label><input name="newhelp" type=text value=""><div class="clearerleft"> </div>
<?php } ?>

<?php # add ability to delete custom page/name entries
 if ($custom==1 && $page!="help"){ ?>
	<div class="Question"><label><?php echo $lang["ticktodeletehelp"]?></label><input name="deletecustom" type="checkbox" value="yes"><div class="clearerleft"> </div></div>
<?php } ?>


<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>
