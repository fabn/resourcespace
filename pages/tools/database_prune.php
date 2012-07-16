<?php
#
# database_prune.php
#
# Cleans the database of unused / orphaned rows
#

include "../../include/db.php";
include "../../include/general.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
if ($use_mysqli){$function="mysql_affected_rows";} else {$function="mysqli_affected_rows";}

sql_query("delete from collection where public<>1 and user not in (select ref from user)");
echo sql_affected_rows() . " orphaned collections deleted.<br/><br/>";

sql_query("delete from collection_keyword where collection not in (select ref from collection) or keyword not in (select ref from keyword)");
echo sql_affected_rows() . " orphaned collection keywords deleted.<br/><br/>";

sql_query("delete from collection_log where collection not in (select ref from collection)");
echo sql_affected_rows() . " orphaned collection log rows deleted.<br/><br/>";

sql_query("delete from collection_resource where collection not in (select ref from collection) or resource not in (select ref from resource)");
echo sql_affected_rows() . " orphaned collection log rows deleted.<br/><br/>";

sql_query("delete from collection_savedsearch where collection not in (select ref from collection)");
echo sql_affected_rows() . " orphaned collection saved searches deleted.<br/><br/>";

sql_query("delete from external_access_keys where resource not in (select ref from resource)");
echo sql_affected_rows() . " orphaned external access keys deleted.<br/><br/>";

sql_query("delete from ip_lockout");
echo sql_affected_rows() . " IP address lock-outs deleted.<br/><br/>";

sql_query("delete from resource_keyword where resource not in (select ref from resource)");
echo sql_affected_rows() . " orphaned resource-keyword relationships deleted.<br/><br/>";

sql_query("delete from keyword where ref not in (select keyword from resource_keyword) and ref not in (select keyword from keyword_related) and ref not in (select related from keyword_related) and ref not in (select keyword from collection_keyword)");
echo sql_affected_rows() . " unused keywords deleted.<br/><br/>";

sql_query("delete from resource_alt_files where resource not in (select ref from resource)");
echo sql_affected_rows() . " orphaned alternative files deleted.<br/><br/>";

sql_query("delete from resource_custom_access where resource not in (select ref from resource) or (user not in (select ref from user) and usergroup not in (select ref from usergroup))");
echo sql_affected_rows() . " orphaned resource custom access rows deleted.<br/><br/>";

sql_query("delete from resource_dimensions where resource not in (select ref from resource)");
echo sql_affected_rows() . " orphaned resource dimension rows deleted.<br/><br/>";

sql_query("delete from resource_log where resource<>0 and resource not in (select ref from resource)");
echo sql_affected_rows() . " orphaned resource log rows deleted.<br/><br/>";

sql_query("delete from resource_related where resource not in (select ref from resource) or related not in (select ref from resource)");
echo sql_affected_rows() . " orphaned resource related rows deleted.<br/><br/>";

sql_query("delete from resource_type_field where resource_type<>999 and resource_type<>0 and resource_type not in (select ref from resource_type)");
echo sql_affected_rows() . " orphaned fields deleted.<br/><br/>";

sql_query("delete from user_collection where user not in (select ref from user) or collection not in (select ref from collection)");
echo sql_affected_rows() . " orphaned user-collection relationships deleted.<br/><br/>";

sql_query("delete from resource_data where resource not in (select ref from resource) or resource_type_field not in (select ref from resource_type_field)");
echo sql_affected_rows() . " orphaned resource data rows deleted.<br/><br/>";

# Clean out and resource data that is set for fields not applicable to a given resource type.
$r=get_resource_types();
for ($n=0;$n<count($r);$n++)
	{
	$rt=$r[$n]["ref"];
	$fields=sql_array("select ref value from resource_type_field where resource_type=0 or resource_type=999 or resource_type='" . $rt . "'");
	if (count($fields)>0)
		{
		sql_query("delete from resource_data where resource in (select ref from resource where resource_type='$rt') and resource_type_field not in (" . join (",",$fields) . ")");
		echo sql_affected_rows() . " orphaned resource data rows deleted for resource type $rt.<br/><br/>";
		}
	}

hook("dbprune");


?>
