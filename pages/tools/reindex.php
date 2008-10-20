<?
#
# Reindex.php
#
#
# Reindexes the resource metadata. This should be unnecessary unless the resource_keyword table has been corrupted.
#

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*5);
echo "<pre>";

$resources=sql_query("select ref from resource order by ref");
for ($n=0;$n<count($resources);$n++)
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
			echo $data[$m]["value"];
			add_keyword_mappings($ref,i18n_get_indexable($data[$m]["value"]),$data[$m]["ref"]);		
			}
		}
		
	$words=sql_value("select count(*) value from resource_keyword where resource='$ref'",0);
	echo "Done $ref ($n/" . count($resources) . ") - $words words<br />\n";
	}
?>