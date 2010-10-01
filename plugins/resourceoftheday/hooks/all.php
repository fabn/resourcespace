<?php


function HookResourceofthedayAllAdjust_item_price ($origprice,$resource,$size)
	{
	include_once dirname(__FILE__)."/../inc/rotd_functions.php";
	
	# Discount pipeline support, allow multiple hook calls to modify the price multiple times
	global $purchase_pipeline_price;
	if (isset($purchase_pipeline_price[$resource][$size])) {$origprice=$purchase_pipeline_price[$resource][$size];}

	# Fetch the current resource of the day.	
	$rotd=get_resource_of_the_day();
	if ($rotd===false) {return $origprice;} # No ROTD, return standard pricing always.

	if ($resource==$rotd)
		{
		# Discount the resource of the day.
		global $rotd_discount;
		$return=round($origprice * (1-($rotd_discount/100)),2);
		$purchase_pipeline_price[$resource][$size]=$return; # Use this price instead for future hook calls.
		return $return;
		}

	# This isn't the resource of the day. Normal pricing.
	return $origprice;
	}
	



?>