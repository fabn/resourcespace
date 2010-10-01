<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";


if (getval("submit","")!="")
	{
	$rotd_field=getvalescaped("rotd_field","");
	$rotd_discount=getvalescaped("rotd_discount","");
		
	$f=fopen("../config/config.php","w");
	fwrite($f,"<?php \$rotd_field='$rotd_field'; ?>");
	fwrite($f,"<?php \$rotd_discount='$rotd_discount'; ?>");
	fclose($f);
	redirect("pages/team/team_home.php");
	}

$rotd_fields=sql_query("select * from resource_type_field where type=4 order by resource_type,order_by");

include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>Resource Of The Day Configuration</h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p>A resource with the 'Resource Of The Day' field set to today's date will be displayed on the home page. If no resource matches today's date a random resource with the field set will be used instead. If no resources have the field set then it will revert to the default slideshow functionality.</p>

<p>Please specify which date field should be used to determine the "resource of the day" (you will normally need to set up a new field for this purpose).</p>
   <p><label for="rotd_field">Resource Of The Day Field:</label>
   
   <select name="rotd_field">
   <?php foreach ($rotd_fields as $field) { ?>
   <option value="<?php echo $field["ref"] ?>" <?php if ($field["ref"]==$rotd_field) {echo "selected"; } ?>><?php echo $field["title"] ?></option>
   <?php } ?>
   </select>
	</p>

   <p><label for="$rotd_discount">Resource Of The Day Discount Percent:</label>
   <input size="3" type="text" name="rotd_discount" value="<?php echo (isset($rotd_discount)?$rotd_discount:0) ?>">%
	</p>


<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>	