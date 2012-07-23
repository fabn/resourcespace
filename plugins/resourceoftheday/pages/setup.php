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

$rotd_fields=sql_query("select * from resource_type_field where type in(4,10) order by resource_type,order_by");

include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["rotd-configuraion"] ?></h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p><?php echo $lang["intro-rotd-configuration"] ?></p>

<p><?php echo $lang["specify-date-field"] ?></p>
   <p><label for="rotd_field"><?php echo $lang["rotd-field"] . ":" ?></label>
   
   <select name="rotd_field">
   <?php foreach ($rotd_fields as $field) { ?>
   <option value="<?php echo $field["ref"] ?>" <?php if ($field["ref"]==$rotd_field) {echo "selected"; } ?>><?php echo lang_or_i18n_get_translated($field["title"],"fieldtitle-") ?></option>
   <?php } ?>
   </select>
	</p>

   <p><label for="$rotd_discount"><?php echo $lang["rotd-discount"] . ":" ?></label>
   <input size="3" type="text" name="rotd_discount" value="<?php echo (isset($rotd_discount)?$rotd_discount:0) ?>">%
	</p>


<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>	