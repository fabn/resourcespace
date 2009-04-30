<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*1);
ini_set("track_errors","on");
#error_reporting(0);

# This script moves any non-scrambled resources over to the scrambled URL.
exit("You must manually enable this script."); # prevent accidental use!

?>
<html>
<body>
<?php

$resources=sql_query("select ref,file_extension from resource order by ref desc");
for ($n=0;$n<count($resources);$n++)
	{
	$ref=$resources[$n]["ref"];
	$extension=$resources[$n]["file_extension"];
	if ($extension=="") {$extension="jpg";}

	$sizes=get_image_sizes($ref,true,$extension,false);
	for ($m=0;$m<count($sizes);$m++)
		{
		$path=get_resource_path($ref,true,$sizes[$m]["id"],false,$extension,false);
		if (file_exists($path))
			{
			$newpath=get_resource_path($ref,true,$sizes[$m]["id"],true,$extension,true);
			
			echo "<li>$ref - old path=$path, new path=$newpath";
			rename ($path,$newpath);
			}
		}
	}
?>
</body>
</html>
