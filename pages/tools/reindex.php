<?php
#
# Reindex.php
#
#
# Reindexes the resource metadata. This should be unnecessary unless the resource_keyword table has been corrupted.
#

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

$sql="";
if (getval("ref","")!="") {$sql="where r.ref='" . getvalescaped("ref","",true) . "'";}

set_time_limit(60*60*5);
echo "<pre>";

$start = getval('start','0');
if (!is_numeric($start)){ $start = 0; }

$resources=sql_query("select r.ref,u.username,u.fullname from resource r left outer join user u on r.created_by=u.ref $sql order by ref");
for ($n=$start;$n<count($resources);$n++)
	{
	$ref=$resources[$n]["ref"];

	# Delete existing keywords
	sql_query("delete from resource_keyword where resource='$ref'");

	# Index fields
	$data=get_resource_field_data($ref);
	for ($m=0;$m<count($data);$m++)
		{
		if ($data[$m]["keywords_index"]==1)
			{
			#echo $data[$m]["value"];
			$value=$data[$m]["value"];
			if ($data[$m]["type"]==3 || $data[$m]["type"]==2)
				{
				# Prepend a comma when indexing dropdowns
				$value="," . $value;
				}
			
			# Date field? These need indexing differently.
			$is_date=($data[$m]["type"]==4 || $data[$m]["type"]==6);
			
			add_keyword_mappings($ref,i18n_get_indexable($value),$data[$m]["ref"],$data[$m]["partial_index"],$is_date);		
			}
		}
	
	# Also index contributed by field.
	add_keyword_mappings($ref,$resources[$n]["username"] . " " . $resources[$n]["fullname"],-1);		
	
	# Always index the resource ID as a keyword
	remove_keyword_mappings($ref, $ref, -1);
	add_keyword_mappings($ref, $ref, -1);
	
	$words=sql_value("select count(*) value from resource_keyword where resource='$ref'",0);
	echo "Done $ref ($n/" . count($resources) . ") - $words words<br />\n";
	}
?>
