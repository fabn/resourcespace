<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

include "../include/header.php";


if (getval("submit","")=="")
	{
	# ------------------- Show the size selection screen -----------------------
	?>
	<div class="BasicsBox"> 
	  <h2>&nbsp;</h2>
	  <h1><?php echo $lang["buynow"]?></h1>
	  <p><?php echo $lang["buynowintro"]?></p>
	   
	<form method="post" action="purchase.php">
	<table class="InfoTable">
	<?php 
	$showbuy=false;
	$resources=do_search("!collection" . $usercollection);
	foreach ($resources as $resource)
		{
		?><tr><?php
		$sizes=get_image_sizes($resource["ref"]);
		$title=get_data_by_field($resource["ref"],$view_title_field);
		?><td><?php echo $title?></td><td>
		<?php
		if (count($sizes)==0)
			{
			?>
			<?php echo $lang["nodownloadsavailable"] ?>
			<?php
			}
		else
			{
			?><select class="stdwidth" name="select_<?php echo $resource["ref"] ?>"><?php
			# List all sizes with pricing options.
			foreach ($sizes as $size)
				{
				$name=$size["name"];
				$id=$size["id"];
				$showbuy=true;
				if ($id=="") {$id="hpr";}
							
				if (array_key_exists($id,$pricing))
					{
					$price=$pricing[$id];
					}
				else
					{
					$price=999; # Error.
					}
				
				?>
				<option value="<?php echo $size["id"] ?>"><?php echo $name . " - " . $currency_symbol . " " . number_format($price,2)  ?></option>
				<?php
				}
			?></select><?php
			}
		?>
		</td>
		</tr><?php	
		}
	?>
	</table>
	<p>&nbsp;</p>
	<?php if ($showbuy) { ?>
		<p><input type="submit" name="submit" value="&nbsp;&nbsp;&nbsp;<?php echo $lang["buynow"]?>&nbsp;&nbsp;&nbsp;"></p>
	<?php } ?>
	</form>
	</div>
	<?php
	}
else
	{
	# ----------------------------------- Show the PayPal integration instead ------------------------------------
	$resources=do_search("!collection" . $usercollection);
	$n=1;
	$paypal="";
	$totalprice=0;
	foreach ($resources as $resource)
		{
		$sizes=get_image_sizes($resource["ref"]);
		$title=get_data_by_field($resource["ref"],$view_title_field);
		foreach ($sizes as $size)
			{
			if (getval("select_" . $resource["ref"],"")==$size["id"])
				{
				$name=$size["name"];
				$id=$size["id"];
				if ($id=="") {$id="hpr";}
								
				# Store the selected size for use by the download page later.
				purchase_set_size($usercollection,$resource["ref"],$size["id"]);
								
				if (array_key_exists($id,$pricing)) {$price=$pricing[$id];}	else {$price=999;}
				$totalprice+=$price;
				# Build up the paypal string...
				$paypal.="<input type=\"hidden\" name=\"item_name_" . $n . "\" value=\"" . $title . " (" . i18n_get_translated($size["name"]) . ")\">\n";
				$paypal.="<input type=\"hidden\" name=\"amount_" . $n . "\" value=\"" . $price . "\">\n";
				$paypal.="<input type=\"hidden\" name=\"quantity_" . $n . "\" value=\"1\">\n";
				$n++;
				}
			}
		}	
	
	
	
	?>
	<div class="BasicsBox"> 
	<h2>&nbsp;</h2>
	<h1><?php echo $lang["proceedtocheckout"] ?></h1>
	<p><?php echo $lang["totalprice"] ?>: <?php echo $currency_symbol . " " . number_format($totalprice,2) ?></p>
	<form name="_xclick" class="form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_cart">
	<input type="hidden" name="upload" value="1">
	<input type="hidden" name="business" value="<?php echo $payment_address ?>">
	<input type="hidden" name="currency_code" value="<?php echo $payment_currency ?>">
	<input type="hidden" name="cancel_return" value="<?php echo $baseurl?>">
	<input type="hidden" name="notify_url" value="<?php echo $baseurl?>/pages/purchase_callback.php">
	<input type="hidden" name="return" value="<?php echo $baseurl?>/pages/purchase_download.php">
	<input type="hidden" name="custom" value="<?php echo $usercollection ?>">
	<?php echo $paypal?>
	<p><input type="submit" name="submit" value="&nbsp;&nbsp;&nbsp;<?php echo $lang["proceedtocheckout"]?>&nbsp;&nbsp;&nbsp;"></p>
	</form>
	</div>
<?php
}

include "../include/footer.php";
?>