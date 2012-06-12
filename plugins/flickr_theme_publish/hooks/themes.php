<?php

function HookFlickr_theme_publishThemesAddcustomtool($theme)
	{
	# Adds a Flickr link to the themes page.
	global $lang;
	
	# Work out how many resources in this theme are unpublished.
	$unpublished=sql_value("select count(*) value from resource join collection_resource on resource.ref=collection_resource.resource where collection_resource.collection='" . $theme . "' and flickr_photo_id is null",0);
	
	?>
	&nbsp;<a href="../plugins/flickr_theme_publish/pages/sync.php?theme=<?php echo $theme?>">&gt;&nbsp;Flickr<?php if ($unpublished>0) { echo " <strong>(" . ($unpublished==1 ? $lang["unpublished-1"] : str_replace("%number", $unpublished, $lang["unpublished-2"])) . ")</strong>"; } ?></a>
	<?php
	}

function HookFlickr_theme_publishThemesCollectiontoolcompact1($collection, $count_result)
    {
    # Adds a Flickr command to the themes page in collection compact style.
    global $getthemes, $m, $lang;
    $theme = $getthemes[$m];

    if ($count_result>0) # Don't show the option if the theme is empty.
        {

        # Work out how many resources in this theme are unpublished.
        $unpublished = sql_value("select count(*) value from resource join collection_resource on resource.ref=collection_resource.resource where collection_resource.collection='" . $theme["ref"] . "' and flickr_photo_id is null",0);

        ?>
        <option value="../plugins/flickr_theme_publish/pages/sync.php?theme=<?php echo $theme["ref"]?>">&gt;&nbsp;<?php echo $lang["publish_to_flickr"] ?>...<?php if ($unpublished>0) { echo " <strong>(" . ($unpublished==1 ? $lang["unpublished-1"] : str_replace("%number", $unpublished, $lang["unpublished-2"])) . ")</strong>"; } ?></option>
        <?php
        }
    }
?>
