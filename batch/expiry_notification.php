<?php
include(dirname(__FILE__) . "/../include/db.php");
include(dirname(__FILE__) . "/../include/general.php");
include(dirname(__FILE__) . "/../include/image_processing.php");
include(dirname(__FILE__) . "/../include/resource_functions.php");

# Check this is enabled
if (!isset($expiry_notification_mail)) {exit("Please set expiry_notification_mail in config.php to enable this feature.");}

# Fetch expired resources
$expired=sql_query('select r.ref,r.field8 as title from resource r join resource_data rd on r.ref=rd.resource join resource_type_field rtf on rd.resource_type_field=rtf.ref and rtf.type=6 where 
r.expiry_notification_sent<>1 and rd.value<>"" and rd.value<=now()');

if (count($expired)>0)
	{
	# Send notifications
	$refs=array();
	$body=$lang["resourceexpirymail"] . "\n";
	foreach ($expired as $resource)
		{
		$refs[]=$resource["ref"];
		echo "<br>Sending expiry notification for: " . $resource["ref"] . " - " . $resource["title"];
		
		$body.="\n" . $resource["ref"] . " - " . $resource["title"];
		$body.="\n" . $baseurl . "/r?=" . $resource["ref"] . "\n";
		}
	
	# Send mail
	send_mail($expiry_notification_mail,$lang["resourceexpiry"],$body);

	# Update notification flag so an expiry is not sent again until the expiry field(s) is edited.
	sql_query("update resource set expiry_notification_sent=1 where ref in (" . join(",",$refs) . ")");
	}
else
	{
	echo "Nothing to do.";
	}




?>
