
<?php 
## for existing installations that have extensive resource view 
## hit counts and want to switch to tracking hit counts as downloads, rather than views.

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";

set_time_limit(60*60*40);

echo "Resetting hit counts to download counts derived from the resource log...";

$rd=sql_query("select ref from resource");
for ($n=0;$n<count($rd);$n++)
	{
	$ref=$rd[$n]['ref'];
	echo "Updating " . $ref. "<br>";
	$count=sql_query("select * from resource_log where resource=$ref and type='d'");
	sql_query("update resource set hit_count=0,new_hit_count=".count($count)." where ref='$ref'");
	}
echo "...done.";

?>
