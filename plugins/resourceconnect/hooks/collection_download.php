<?php

function HookResourceConnectCollection_downloadInitialise()
	{
	global $inside_plugin;
	if (!isset($inside_plugin))
		{
		redirect("plugins/resourceconnect/pages/collection_download.php?collection=" . getval("collection",""));
		}
	}
