<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";
include "../include/collections_functions.php";

if (getval("purchaseonaccount","")!="" && $userrequestmode==3)
	{
	# Invoice mode.
	# Mark as payment complete.
	payment_set_complete($usercollection);
	
	# Set new user collection to empty the basket (without destroying the old basket which contains the 'paid' flag to enable the download).
	$oldcollection=$usercollection;
	$name=get_mycollection_name($userref);
	$newcollection=create_collection ($userref,$name,0,1); // make not deletable
	set_user_collection($userref,$newcollection);
	
	# Redirect to basket (old) collection for download.
	redirect("pages/purchase_download.php?collection=" . $oldcollection);
	}


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
				
				# Pricing adjustment hook (for discounts or other price adjustments plugin).
				$priceadjust=hook("adjust_item_price","",array($price,$resource["ref"],$size["id"]));
				if ($priceadjust!==false)
					{
					$price=$priceadjust;
					}
		
				
				?>
				<option value="<?php echo $size["id"] ?>"  <?php if ($size["id"]==$resource["purchase_size"]) { ?>selected<?php } ?>><?php echo $name . " - " . $currency_symbol . " " . number_format($price,2)  ?></option>
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
	<?php hook("purchase_extra_options"); ?>
	
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
	$pricing_discounted=$pricing; # Copy the pricing, which may be group specific
	include "../include/config.php"; # Reinclude the config so that $pricing is now the default, and we can work out group discounts
	
	$resources=do_search("!collection" . $usercollection);
	$n=1;
	$paypal="";
	$totalprice=0;
	$totalprice_ex_discount=0;
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
				
				# Add to total price				
				if (array_key_exists($id,$pricing_discounted)) {$price=$pricing_discounted[$id];}	else {$price=999;}

				# Add to ex-discount price also
				if (array_key_exists($id,$pricing)) {$price_ex_discount=$pricing[$id];}	else {$price_ex_discount=999;}
				$totalprice_ex_discount+=$price_ex_discount;
								
				# Pricing adjustment hook (for discounts or other price adjustments plugin).
				$priceadjust=hook("adjust_item_price","",array($price,$resource["ref"],$size["id"]));
				if ($priceadjust!==false)
					{
					$price=$priceadjust;
					}
								
				$totalprice+=$price;
				# Build up the paypal string...
				$paypal.="<input type=\"hidden\" name=\"item_name_" . $n . "\" value=\"" . $title . " (" . $size["name"] . ")\">\n";
				$paypal.="<input type=\"hidden\" name=\"amount_" . $n . "\" value=\"" . $price . "\">\n";
				$paypal.="<input type=\"hidden\" name=\"quantity_" . $n . "\" value=\"1\">\n";
				$n++;

				# Store the selected size for use by the download page later; also store the price so it can be logged in the resource log if/when the purchase is completed.
				purchase_set_size($usercollection,$resource["ref"],$size["id"],$price);
				}
			}
		}	
	
	
	
	?>
	<div class="BasicsBox"> 
	<h2>&nbsp;</h2>
	<h1><?php echo ($userrequestmode==2)?$lang["proceedtocheckout"]:$lang["accountholderpayment"] ?></h1>
	<?php hook ("price_display_extras"); ?>

	<table class="InfoTable">
	<tr><td><?php echo $lang["subtotal"] ?></td><td align="right"><?php echo $currency_symbol . " " . number_format($totalprice_ex_discount,2) ?></td></tr>

	<?php if ($totalprice!=$totalprice_ex_discount || true) { 
		# Display discount (always for now)
		?>	
		<tr><td><?php echo $lang["discountsapplied"] ?></td><td align="right"><?php echo $currency_symbol . " " . number_format($totalprice_ex_discount-$totalprice,2) ?></td></tr>
		<?php
		}
	?>
			
	<tr><td><strong><?php echo $lang["totalprice"] ?></strong></td><td align="right"><strong><?php echo $currency_symbol . " " . number_format($totalprice,2) ?></strong></td></tr>
	</table>
	<br>
	
	<?php if ($userrequestmode==2)
		{
		# Payment immediate - use PayPal.
		if (!hook("paymentgateway")) # Allow other payment gateways to be hooked in, instead of PayPal.
			{
			?>
			<form name="_xclick" class="form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_cart">
			<input type="hidden" name="upload" value="1">
			<input type="hidden" name="business" value="<?php echo $payment_address ?>">
			<input type="hidden" name="currency_code" value="<?php echo $payment_currency ?>">
			<input type="hidden" name="cancel_return" value="<?php echo $baseurl?>">
			<input type="hidden" name="notify_url" value="<?php echo $baseurl?>/pages/purchase_callback.php">
			<input type="hidden" name="return" value="<?php echo $baseurl?>/pages/purchase_download.php?collection=<?php echo $usercollection ?>">
			<input type="hidden" name="custom" value="<?php echo $usercollection ?>">
			<?php echo $paypal?>
			<p><input type="submit" name="submit" value="&nbsp;&nbsp;&nbsp;<?php echo $lang["proceedtocheckout"]?>&nbsp;&nbsp;&nbsp;"></p>
			</form>
			<?php
			}
		}
	?>
	
	<?php if ($userrequestmode==3)
		{
		# Invoice payment.
		?>
		<form method="post" action="purchase.php" onsubmit="return confirm('<?php echo $lang["areyousurepayaccount"] ?>');">
		<p><input type="submit" name="purchaseonaccount"  value="&nbsp;&nbsp;&nbsp;<?php echo $lang["purchaseonaccount"]?>&nbsp;&nbsp;&nbsp;"></p>		
		</form>
		<?php
		}
	?>
	</div>
<?php
}

include "../include/footer.php";
?>