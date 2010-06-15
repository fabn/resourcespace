<?php

# Quick script to fix database entries that need commas at the beginning.
# When some values have commas and others don't, sorting doesn't work correctly!!!

# run this and then drop the field# columns on your resource table to allow them to rebuild with new values. 

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";

$resource_type_fields=sql_query("select ref from resource_type_field where type=2 OR type=3");

for($n=0;$n<count($resource_type_fields);$n++){
	$values=sql_query("select resource,resource_type_field,value from resource_data where resource_type_field=".$resource_type_fields[$n]['ref']);
	for ($x=0;$x<count($values);$x++){
		if (substr($values[$x]['value'],0,1) != ',')
			{
			echo "updating ref: ".$values[$x]['resource']. " field: ".$values[$x]['resource_type_field']. " value: ".$values[$x]['value']."<br>";
			sql_query("update resource_data set value=',".mysql_escape_string($values[$x]['value'])."' where resource='".$values[$x]['resource']."' and resource_type_field='".$values[$x]['resource_type_field']."'");
			}
	}		
}

