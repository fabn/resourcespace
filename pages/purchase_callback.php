<?php
include "../include/db.php";
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";

# Handle the callback from PayPal and mark the collection items as purchased.

// Read the post from PayPal
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value)
		{
		$value = urlencode($value); $req .= "&$key=$value";
		}

# Send this request back to PayPal for verification.
$header = "";
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

// Process validation from PayPal
if (!$fp)
	{ // HTTP ERROR
	echo "HTTP error.";
	}
else
	{
	// NO HTTP ERROR
	fputs ($fp, $header . $req);
	while (!feof($fp))
		{
		$res = fgets ($fp, 1024);		
		
		if (strcmp($res, "VERIFIED") == 0)
			{
			echo "Verified.";
			
			// Mark these items as bought.
			payment_set_complete(getvalescaped("custom",""));
			
			hook("payment_complete");
			} 
		else
			{
			echo "Not verified";
			}
		}
	}

?>