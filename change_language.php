<?
include "include/db.php";
include "include/general.php";

if (getval("save","")!="")
	{
	setcookie("language",getval("language",""),time()+(3600*24*1000));
	redirect("home.php");
	}
include "include/header.php";
?>

<h1><?=$lang["languageselection"]?></h1>
<p><?=text("introtext")?></p>
  
<form method="post" target="_top">  
<div class="Question">
<label for="password"><?=$lang["language"]?></label>
<select class="stdwidth" name="language">
<? reset ($languages); foreach ($languages as $key=>$value) { ?>
<option value="<?=$key?>" <? if ($language==$key) { ?>selected<? } ?>><?=$value?></option>
<? } ?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>		
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>


<?
include "include/footer.php";
?>