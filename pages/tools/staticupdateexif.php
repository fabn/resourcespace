<?php
include "../../include/db.php";
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*40);

echo "Updating EXIF/IPTC...";

$rd=sql_query("select ref,file_extension from resource where has_image=1 and resource_type=1");
for ($n=0;$n<count($rd);$n++)
	{
	echo "." . $rd[$n]['ref'];
	extract_exif_comment($rd[$n]['ref'],$rd[$n]['file_extension']);
	}
echo "...done.";

?>
