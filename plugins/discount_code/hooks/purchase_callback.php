<?php

function HookDiscount_codePurchase_callbackPayment_complete ()
	{
	# Find out the discount code applied to this collection.
	$code=sql_value("select discount_code value from collection_resource where collection='" . getvalescaped("custom","") . "' limit 1", "");
	
	# Find out the purchasing user
	# As this is a callback script being called by PayPal, there is no login/authentication and we can't therefore simply use $userref.
	$user=sql_value("select ref value from user where current_collection='" . getvalescaped("custom","") . "'",0);
	
	# Insert used discount code row
	sql_query("insert into discount_code_used (code,user) values ('" . escape_check($code) . "','$user')");
	
	}



?>