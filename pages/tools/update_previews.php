<?php
#
#
# Quick 'n' dirty script to update all preview images.
# It's done one at a time via the browser so progress can be monitored.
#
#
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/image_processing.php";
include "../../include/resource_functions.php";
include_once "../../include/collections_functions.php";
function update_preview($ref){
    global $previewbased;
    $resourceinfo=sql_query("select ref, file_extension from resource where ref='$ref'");
    if (count($resourceinfo)>0){
        create_previews($ref, false,($previewbased?"jpg":$resourceinfo[0]["file_extension"]),false, $previewbased);
        return true;
    }
    return false;
}
$collectionid=getvalescaped("col", false);
$max=sql_value("select max(ref) value from resource",0);
$ref=getvalescaped("ref",false);
$previewbased=getvalescaped("previewbased",false);
if ($collectionid === false){
    if ($ref===false) $ref = 1;
    if (update_preview($ref)){
    	?>
    	<img src="<?php echo get_resource_path($ref,false,"pre",false)?>">
    	<?php
    	}
    else
    	{
    	echo "Skipping $ref";
    	}
    
    if ($ref<$max && getval("only","")=="")
    	{
    	?>
    	<meta http-equiv="refresh" content="1;url=<?php echo $baseurl?>/pages/tools/update_previews.php?ref=<?php echo $ref+1?>&previewbased=<?php echo $previewbased?>"/>
    	<?php
    	}
    else
    	{
    	?>
    	Done.	
    	<?php
    	}
}
else {
    $collection = get_collection_resources($collectionid);
    if (!is_array($collection)){
        echo "Collection id returned no resources.";
        die();
    }
    if ($ref===false){
        $ref = $collection[0];
        $key = 0;
    }
    else {
        $key = array_search($ref, $collection);
    }
    if (update_preview($ref)){
        ?>
        <img src="<?php echo get_resource_path($ref,false,"pre",false)?>">
        <?php 
    }
    if (isset($collection[$key+1])){
        $next_ref = $collection[$key+1];
        ?>
        <meta http-equiv="refresh" content="1;url=<?php echo $baseurl?>/pages/tools/update_previews.php?col=<?php echo $collectionid?>&ref=<?php echo $next_ref?>&previewbased=<?php echo $previewbased?>"/>
        <?php
    }
    else {
        ?>
        Done.
        <?php 
    }
    
}

	
