<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

$delete_code=getval("delete_code","");
if ($delete_code!="")
	{
	# Delete discount code.
	sql_query("delete from discount_code where code='$delete_code'");
	}

elseif (getval("add","")!="")
	{
	# Add discount code.
	sql_query("delete from discount_code where code='" . getvalescaped("code","") . "'"); # Clear any existing matching code.
	sql_query("insert into discount_code(code,percent,expires) values ('" . getvalescaped("code","") . "','" . getvalescaped("percent","") . "','" . getvalescaped("expires","") . "');");
	}

$discount_codes=sql_query("select code,percent,expires from discount_code order by code");

include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>Discount Code Configuration</h1>

  <div class="VerticalNav">

<table class="InfoTable">
<tr>
<td><strong>Code</strong></th>
<td><strong>Percent</strong></th>
<td><strong>Expires</strong></th>
<td> &nbsp; </td>
</tr>
<?php foreach ($discount_codes as $discount_code) { ?>
<tr>
<td><?php echo $discount_code["code"] ?></td>
<td><?php echo $discount_code["percent"] ?></td>
<td><?php echo $discount_code["expires"] ?></td>
<td><a href="#" onClick="if (confirm('Are you sure you wish to delete this discount code?')) {document.getElementById('delete_code').value='<?php echo $discount_code["code"] ?>';document.getElementById('discountform').submit();}">&gt;&nbsp;Delete</a></td>
</tr>
<?php } ?>
</table>

<Br><br><br>
<h2>Add Code</h2>
<form id="discountform" name="discountform" method="post" action="">
<input type="hidden" name="delete_code" id="delete_code" value="">

Code: <input type="text" name="code" size="20" value="NEWCODE">
&nbsp;
Percent: <input type="text" name="percent" size="2" value="10">%
&nbsp;
Expires: (yyyy-mm-dd) <input type="text" name="expires" size="12" value="<?php echo date("Y-m-d",time()+60*60*24*100) ?>">
<input type="submit" name="add" value="Add discount code">   
</form>


</div>	
<?php
include "../../../include/footer.php";
?>
