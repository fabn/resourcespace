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
	