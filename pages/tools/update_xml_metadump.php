<?php
#
# update_xml_metadump.php
#
#
# update XML metadump files in filestore from scratch
#

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

$sql="";
if (getval("ref","")!="") {$sql="where r.ref='" . getvalescaped("ref","",true) . "'";}

set_time_limit(60*60*5);
echo "<pre><strong>\nUpdating XML metadata dump files...</strong>\n\n";

$start = getval('start','0');
if (!is_numeric($start)){ $start = 0; }

$resources=sql_query("select r.ref,u.username,u.fullname from resource r left outer join user u on r.created_by=u.ref $sql order by ref");
for ($n=$start;$n<count($resources);$n++)
	{
	$ref=$resources[$n]["ref"];

	update_xml_metadump($ref);

	echo "Done $ref ($n/" . count($resources) . ")<br />\n";
	flush();
	}
?>
