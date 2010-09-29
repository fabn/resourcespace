<?php

function HookFlickr_theme_publishThemesAddcustomtool($theme)
	{
	# Adds a Flickr link to the themes page.
	global $lang;
	
	# Work out how many resources in this theme are unpublished.
	$unpublished=sql_value("select count(*) value from resource join collection_resource on resource.ref=collection_resource.resource where collection_resource.collection='" . $theme . "' and flickr_photo_id is null",0);
	
	?>
	&nbsp;<a href="../plugins/flickr_theme_publish/pages/sync.php?theme=<?php echo $theme?>">&gt;&nbsp;Flickr<?php if ($unpublished>0) { echo " <strong>(" . $unpublished . " " . $lang["unpublished"] . ")</strong>"; } ?></a>
	<?php
	}



?>