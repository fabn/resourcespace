<?php
#
# Reindex_collections.php
#
#
# Reindexes the collection index used for public searching.
#

include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/collections_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*5);
echo "<pre>";

$collections=sql_query("select * from collection where public=1 order by ref");
for ($n=0;$n<count($collections);$n++)
	{
	$ref=$collections[$n]["ref"];


	# Update the keywords index for this collection
	sql_query("delete from collection_keyword where collection='$ref'"); # Remove existing keywords
	
	# Define an indexable string from the name, themes and keywords.
	$index_string=$collections[$n]["name"] . " " . $collections[$n]["keywords"] . " " . $collections[$n]["theme"] . " " . $collections[$n]["theme2"] . " " . $collections[$n]["theme3"];
	$keywords=split_keywords($index_string,true);
	$words=count($keywords);
	for ($m=0;$m<count($keywords);$m++)
		{
		$keyref=resolve_keyword($keywords[$m],true);
		sql_query("insert into collection_keyword values ('$ref','$keyref')");
		}
	

	echo "Done $ref (" . $n+1 . "/" . count($collections) . ") - $words words<br />\n";
	}
?>