<?
#
#
# Quick 'n' dirty script to update all images paths with new scrambled filenames.
# It's done one at a time via the browser so progress can be monitored.
#
#
include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
include "../../include/image_processing.php";
include "../../include/resource_functions.php";

$max=sql_value("select max(ref) value from resource",0);
$ref=getvalescaped("ref",1);
$ps=sql_query("select * from preview_size");
$resourceinfo=sql_query("select ref,file_extension from resource where ref='$ref'");
if (count($resourceinfo)>0)
	{
	$extension = $resourceinfo[0]['file_extension'];
	get_resource_path($ref,true,"",false,$extension);
	for ($n=0;$n<count($ps);$n++)
		{
		$id=$ps[$n]["id"];
		get_resource_path($ref,true,$id,false);
		}
	?>
	<img src="<?=get_resource_path($ref,false,"pre",false)?>">
	<?
	}
else
	{
	echo "Skipping $ref";
	}

if ($ref<$max && getval("only","")=="")
	{
	?>
	<meta http-equiv="refresh" content="0;url=<?=$baseurl?>/pages/tools/update_paths.php?ref=<?=$ref+1?>"/>
	<?
	}
else
	{
	?>
	Done.	
	<?
	}
?>
	
