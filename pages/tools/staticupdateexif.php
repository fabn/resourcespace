<?
include "../../include/db.php";
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*40);

echo "Updating EXIF/IPTC...";

$rd=sql_array("select ref value from resource where has_image=1 and resource_type=1");
for ($n=0;$n<count($rd);$n++)
	{
	echo "." . $rd[$n];
	extract_exif_comment($rd[$n]);
	}
echo "...done.";

?>
