<?php
/**
 * Report creation page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/reporting_functions.php";

$report=getvalescaped("report","");
$period=getvalescaped("period",$reporting_periods_default[0]);
$period_init=$period;

if ($period==0)
	{
	# Specific number of days specified.
	$period=getvalescaped("period_days","");
	if (!is_numeric($period) || $period<1) {$period=1;} # Invalid period specified.
	}

if ($period==-1)
	{
	# Specific date range specified.
	$from_y = getvalescaped("from-y","");
	$from_m = getvalescaped("from-m","");
	$from_d = getvalescaped("from-d","");
	
	$to_y = getvalescaped("to-y","");
	$to_m = getvalescaped("to-m","");
	$to_d = getvalescaped("to-d","");
	}
else
	{
	# Work out the from and to range based on the provided period in days.
	$start=time()-(60*60*24*$period);

	$from_y = date("Y",$start);
	$from_m = date("m",$start);
	$from_d = date("d",$start);
		
	$to_y = date("Y");
	$to_m = date("m");
	$to_d = date("d");
	}
	
$from=getvalescaped("from","");
$to=getvalescaped("to","");
$output="";

if ($report!="")
	{
	$download=getval("download","")!="";
	$output=do_report($report, $from_y, $from_m, $from_d, $to_y, $to_m, $to_d,$download);
	}
include "../../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["viewreport"]?></h1>
  <p><?php echo text("introtext")?></p>
  
<form method="post">
<div class="Question">
<label for="report"><?php echo $lang["viewreports"]?></label><select id="report" name="report" class="stdwidth">
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


<!-- Period select -->
<form method="post">
<div class="Question">
<label for="period"><?php echo $lang["period"]?></label><select id="period" name="period" class="stdwidth" onChange="
if (this.value==-1) {document.getElementById('DateRange').style.display='block';} else {document.getElementById('DateRange').style.display='none';}
if (this.value==0) {document.getElementById('SpecificDays').style.display='block';} else {document.getElementById('SpecificDays').style.display='none';}
">
<?php
foreach ($reporting_periods_default as $period_default)
	{
	?><option value="<?php echo $period_default?>" <?php if ($period_init==$period_default) { ?>selected<?php } ?>><?php echo str_replace("?",$period_default,$lang["lastndays"])?></option><?php
	}
?>
<option value="0" <?php if ($period_init==0) { ?>selected<?php } ?>><?php echo $lang["specificdays"]?></option>
<option value="-1" <?php if ($period_init==-1) { ?>selected<?php } ?>><?php echo $lang["specificdaterange"]?></option>
</select>
<div class="clearerleft"> </div>
</div>



<!-- Specific Days Selector -->
<div id="SpecificDays" <?php if ($period_init!=0) { ?>style="display:none;"<?php } ?>>
<div class="Question">
<label for="period_days"><?php echo $lang["specificdays"]?></label>
<?php
$textbox="<input type=\"text\" id=\"period_days\" name=\"period_days\" size=\"4\" value=\"" . getval("period_days","") . "\">";
echo str_replace("?",$textbox,$lang["lastndays"]);
?>
<div class="clearerleft"> </div>
</div>
</div>


<!-- Specific Date Range Selector -->
<div id="DateRange" <?php if ($period_init!=-1) { ?>style="display:none;"<?php } ?>>
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
</div>
<!-- end of Date Range Selector -->




<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["viewreport"] ?>&nbsp;&nbsp;" />
<input name="download" type="submit" value="&nbsp;&nbsp;<?php echo $lang["downloadreport"] ?>&nbsp;&nbsp;" />
</div>
</form>

<?php echo $output; ?>

</div>

<?php
include "../../include/footer.php";
?>