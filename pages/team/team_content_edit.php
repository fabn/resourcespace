<?
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("o")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";

$page=getvalescaped("page","");
$name=getvalescaped("name","");
$editlanguage=getvalescaped("editlanguage",$defaultlanguage);
$editgroup=getvalescaped("editgroup","");

# Fetch user data
$text=get_site_text($page,$name,$editlanguage,$editgroup);

if ((getval("save","")!="") && (getval("langswitch","")==""))
	{
	# Save data
	save_site_text($page,$name,$editlanguage,$editgroup);
	redirect ("pages/team/team_content.php?nc=" . time());
	}

include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["managecontent"]?></h1>

<form method=post id="mainform">
<input type=hidden name=page value="<?=$page?>">
<input type=hidden name=name value="<?=$name?>">
<input type=hidden name=langswitch id=langswitch value="">
<input type=hidden name=groupswitch id=groupswitch value="">

<div class="Question"><label><?=$lang["page"]?></label><div class="Fixed"><?=$page?></div><div class="clearerleft"> </div></div>

<div class="Question"><label><?=$lang["name"]?></label><div class="Fixed"><?=$name?></div><div class="clearerleft"> </div></div>

<div class="Question">
<label for="password"><?=$lang["language"]?></label>
<select class="stdwidth" name="editlanguage" onchange="document.getElementById('langswitch').value='yes';document.getElementById('mainform').submit();">
<? foreach ($languages as $key=>$value) { ?>
<option value="<?=$key?>" <? if ($editlanguage==$key) { ?>selected<? } ?>><?=$value?></option>
<? } ?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="password"><?=$lang["group"]?></label>
<select class="stdwidth" name="editgroup" onchange="document.getElementById('groupswitch').value='yes';document.getElementById('mainform').submit();">
<option value=""></option>
<? 
$groups=get_usergroups();
for ($n=0;$n<count($groups);$n++) { ?>
<option value="<?=$groups[$n]["ref"]?>" <? if ($editgroup==$groups[$n]["ref"]) { ?>selected<? } ?>><?=$groups[$n]["name"]?></option>
<? } ?>
</select>
<div class="clearerleft"> </div>
</div>


<div class="Question"><label><?=$lang["text"]?></label><textarea name="text" class="stdwidth" rows=15 cols=50><?=htmlspecialchars($text)?></textarea><div class="clearerleft"> </div></div>

<!-- disabled next two as they are in system setup, and making these available on the team centre could lead to accidental deletes and copies.
<div class="Question"><label>Tick to delete this item</label><input name="deleteme" type="checkbox" value="yes"><div class="clearerleft"> </div></div>

<div class="Question"><label>Tick to save as a copy</label><input name="copyme" type="checkbox" value="yes"><div class="clearerleft"> </div></div>
-->

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "../../include/footer.php";
?>