<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/reporting_functions.php";

$report=getvalescaped("report","");

$from=getvalescaped("from","");
$to=getvalescaped("to","");

if ($report!="")
	{
	do_report($report, getvalescaped("from-y",""), getvalescaped("from-m",""), getvalescaped("from-d",""), getvalescaped("to-y",""), getvalescaped("to-m",""), getvalescaped("to-d",""));
	}
include "../../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["viewreport"]?></h1>
  <p><?php echo text("introtext")?></p>
  
<form method="post">
<div class="Question">
<label for="report"><?php echo $lang["viewreports"]?><br/><!--* = Does not use date range--></label><select id="report" name="report" class="stdwidth">
<?php
$reports=get_reports(); 
for ($n=0;$n<count($reports);$n++)
	{
	?><option value="<?php echo $reports[$n]["ref"]?>" <?php if ($report==$reports[$n]["ref"]) { ?>selected<?php } ?>><?php echo i18n_get_translated($reports[$n]["name"])?></option><?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["fromdate"]?><br/><?php echo $lang["inclusive"]?></label>
<?php
$name="from";
$dy=getval($name . "-y",2000);
$dm=getval($name . "-m",1);
$dd=getval($name . "-d",1);
?>
<select name="<?php echo $name?>-d">
<?php for ($m=1;$m<=31;$m++) {?><option <?php if($m==$dd){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
</select>
<select name="<?php echo $name?>-m">
<?php for ($m=1;$m<=12;$m++) {?><option <?php if($m==$dm){echo " selected";}?> value="<?php echo sprintf("%02d",$m)?>"><?php echo $lang["months"][$m-1]?></option><?php } ?>
</select>
<input type=text size=5 name="<?php echo $name?>-y" value="<?php echo $dy?>">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["todate"]?><br/><?php echo $lang["inclusive"]?></label>
<?php
$name="to";
$dy=getval($name . "-y",date("Y"));
$dm=getval($name . "-m",date("m"));
$dd=getval($name . "-d",date("d"));
?>
<select name="<?php echo $name?>-d">
<?php for ($m=1;$m<=31;$m++) {?><option <?php if($m==$dd){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
</select>
<select name="<?php echo $name?>-m">
<?php for ($m=1;$m<=12;$m++) {?><option <?php if($m==$dm){echo " selected";}?> value="<?php echo sprintf("%02d",$m)?>"><?php echo $lang["months"][$m-1]?></option><?php } ?>
</select>
<input type=text size=5 name="<?php echo $name?>-y" value="<?php echo $dy?>">
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["viewreport"]?>&nbsp;&nbsp;" />
</div>
</form>

</div>

<?php
include "../../include/footer.php";
?>