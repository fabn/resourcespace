<?php /* -------- Date select ---------------- */ 

# Start with a null date
$dy="";$dm="";$dd=""; 
$dh="";$di="";

if (($ref<0 || $value=="") && $reset_date_upload_template && $reset_date_field==$fields[$n]["ref"])
	{
	# Upload template: always reset to today's date (if configured).
	$dy=date("Y");$dm=date("m");$dd=date("d");
	$dh=date("H");$di=date("i");
	}
elseif ($value!="")
	{
    #fetch the date parts from the value
    $sd=explode(" ",$value);
    if (count($sd)>=2)
    	{
    	# Attempt to extract hours and minutes from second part.
    	$st=explode(":",$sd[1]);
    	if (count($st)>=2)
    		{
    		$dh=$st[0];
    		$di=$st[1];
    		}
    	}
    $value=$sd[0];
    $sd=explode("-",$value);
    if (count($sd)>=3)
    	{
        $dy=intval($sd[0]);$dm=intval($sd[1]);$dd=intval($sd[2]);
        }
    }
?>
<select name="<?php echo $name?>-d"><option value=""><?php echo $lang["day"]?></option>
<?php for ($m=1;$m<=31;$m++) {?><option <?php if($m==$dd){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
</select>
    
<select name="<?php echo $name?>-m"><option value=""><?php echo $lang["month"]?></option>
<?php for ($m=1;$m<=12;$m++) {?><option <?php if($m==$dm){echo " selected";}?> value="<?php echo sprintf("%02d",$m)?>"><?php echo $lang["months"][$m-1]?></option><?php } ?>
</select>
   
<input type=text size=5 name="<?php echo $name?>-y" value="<?php echo $dy?>">

<!-- Time (optional) -->
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<select name="<?php echo $name?>-h"><option value=""><?php echo $lang["hour-abbreviated"]?></option>
<?php for ($m=0;$m<=23;$m++) {?><option <?php if($m==$dh && $dh!=""){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
</select>

<select name="<?php echo $name?>-i"><option value=""><?php echo $lang["minute-abbreviated"]?></option>
<?php for ($m=0;$m<=59;$m++) {?><option <?php if($m==$di && $di!=""){echo " selected";}?>><?php echo sprintf("%02d",$m)?></option><?php } ?>
</select>

