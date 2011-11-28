<?php

# Quick script to fix database entries that need commas at the beginning (dropdown fields edited using collection edit before r1940). 
# When some values have commas and others don't, sorting doesn't work correctly!!!

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";

$resource_type_fields=sql_query("select ref from resource_type_field where type=3");
$joins=get_resource_table_joins();

for($n=0;$n<count($resource_type_fields);$n++){
	$values=sql_query("select resource,resource_type_field,value from resource_data where resource_type_field=".$resource_type_fields[$n]['ref']);
	for ($x=0;$x<count($values);$x++){
		if (substr($values[$x]['value'],0,1) != ',')
			{
			echo "updating resource_data- ref: ".$values[$x]['resource']. ", field: ".$values[$x]['resource_type_field']. ", value: ,".$values[$x]['value']."<br>";
			sql_query("update resource_data set value=',".escape_check($values[$x]['value'])."' where resource='".$values[$x]['resource']."' and resource_type_field='".$values[$x]['resource_type_field']."'");
			if (in_array($values[$x]['resource_type_field'],$joins)){
				echo "updating resource- ref: ".$values[$x]['resource']. ", field".$values[$x]['resource_type_field'].", value: ,".$values[$x]['value']."<br>";
				#echo("update resource set field".$values[$x]['resource_type_field']."=',".mysql_escape_string($values[$x]['value'])."' where ref='".$values[$x]['resource']."'");
				sql_query("update resource set field".$values[$x]['resource_type_field']."=',".escape_check($values[$x]['value'])."' where ref='".$values[$x]['resource']."'");
				}
			}
	}		
}

