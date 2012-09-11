<?php

function HookResourceconnectPreviewAfterheader()
	{
	?>
	<!-- START GRAB -->
	<?php
	}
	

function HookResourceconnectPreviewBeforefooter()
	{
	?>
	<!-- END GRAB -->
	<?php
	}

function HookResourceconnectPreviewNextpreviousextraurl()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 

	# Forward the resourceconnect source.

	global $baseurl;
	echo "resourceconnect_source=" .$baseurl;
	}
	
function HookResourceconnectPreviewViewextraurl()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 

	# Forward the resourceconnect source.

	global $baseurl;
	echo "resourceconnect_source=" .$baseurl;
	}
	
function HookResourceconnectPreviewSearchextraurl()
	{
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 

	# Forward the resourceconnect source.

	global $baseurl;
	echo "resourceconnect_source=" .$baseurl;
	}
	
function HookResourceconnectPreviewNextpreviewregeneratekey()
	{	
	if (getval("resourceconnect_source","")=="") {return false;} # Not a ResourceConnect result set. 
	
	global $ref,$k,$scramble_key;
	
	# Create a new key when moving next/back for a given result set.
	
	$access_key=md5("resourceconnect" . $scramble_key);
	$k=substr(md5($access_key . $ref),0,10);
	
	return $k;
	}	