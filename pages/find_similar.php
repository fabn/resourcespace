<?
include "../include/db.php";
include "../include/authenticate.php"; if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/search_functions.php";

$resource_type=getvalescaped("resource_type","");

# Loop through all the submitted keywords, build a search string
$search=array();
foreach ($_POST as $key=>$value)
	{
	if (substr($key,0,8)=="keyword_") {$search[]=urldecode(substr($key,8));}
	}

if (getval("countonly","")!="")
	{
	# Only show the results (this will appear in an iframe)
	if (count($search)==0)
		{
		$count=0;
		}
	else
		{
		$result=do_search(join(",",$search),$resource_type,"relevance",0,0);
		if (is_array($result))
			{
			$count=count($result);
			}
		else
			{
			$count=0;
			}
		}
	?>
	<html>
	<script language="Javascript">
	<? if ($count>0) {# $count--; 	?>
	parent.document.getElementById("dosearch").value="<?=$lang["view"]?> <?=(($count==$max_results)?number_format($max_results)."+":number_format($count))?> <?=($count==1)?$lang["similarresource"]:$lang["similarresources"]?>";
	parent.document.getElementById("dosearch").disabled=false;
	<? } else { ?>
	parent.document.getElementById("dosearch").disabled=true;
	parent.document.getElementById("dosearch").value="<?=$lang["nosimilarresources"]?>";
	<? } ?>
	</script>
	</html>
	<?
	}
else
	{
	# redirect to the search page.
	redirect ("pages/search.php?search=" . urlencode(join(", ",$search)) . "&resetrestypes=yes&resource" . $resource_type . "=yes");
	}

?>