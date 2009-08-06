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

$max=sql_value("select max(ref) value from resource",0);
$ref=getvalescaped("ref",1);
$previewbased=getvalescaped("previewbased",false);

$resourceinfo=sql_query("select ref,file_extension from resource where ref='$ref'");
if (count($resourceinfo)>0)
	{
	create_previews($ref,false,($previewbased?"jpg":$resourceinfo[0]["file_extension"]),false,$previewbased);
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
?>
	
