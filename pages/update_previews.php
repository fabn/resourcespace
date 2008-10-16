<?
#
#
# Quick 'n' dirty script to update all preview images.
# It's done one at a time via the browser so progress can be monitored.
#
#
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";

$max=sql_value("select max(ref) value from resource",0);
$ref=getvalescaped("ref",1);

$resourceinfo=sql_query("select ref,file_extension from resource where ref='$ref'");
if (count($resourceinfo)>0)
	{
	create_previews($ref,false,$resourceinfo[0]["file_extension"]);
	?>
	<img src="../<?=get_resource_path($ref,false,"pre",false)?>">
	<?
	}
else
	{
	echo "Skipping $ref";
	}

if ($ref<$max && getval("only","")=="")
	{
	?>
	<meta http-equiv="refresh" content="1;url=<?=$baseurl?>/update_previews.php?ref=<?=$ref+1?>"/>
	<?
	}
else
	{
	?>
	Done.	
	<?
	}
?>
	