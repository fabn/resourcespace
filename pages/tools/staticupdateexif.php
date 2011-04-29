<?php

# This script is useful for initial imports when you're working out metadata mappings. However, be aware that 
# local ResourceSpace field edits could be overwritten by original file metadata during this process.

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*40);

echo "Updating EXIF/IPTC...";

$rd=sql_query("select ref,file_extension from resource where has_image=1 ");
for ($n=0;$n<count($rd);$n++)
	{
	$ref=$rd[$n]['ref'];
	echo "." . $ref;
	extract_exif_comment($rd[$n]['ref'],$rd[$n]['file_extension']);
	}
echo "...done.";

?>
