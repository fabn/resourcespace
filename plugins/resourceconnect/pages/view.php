<?php
include "../../../include/db.php";

$k=getvalescaped("k","");
$kauth=true;
if ($k!="")
	{
	# Check that a valid access key exists for this collection.
	$kauth=false;
	
	$col=getvalescaped("col","");
	$keys=sql_query("select access_key from external_access_keys where collection='$col' and access_key='$k'");
	$kauth=count($keys)>0;
	}

if ($k=="" || !$kauth) {include "../../../include/authenticate.php";}
include "../../../include/general.php";
include "../../../include/search_functions.php";

# Wrap the remote view page with the local header/footer.

$url=getvalescaped("url","");
$search=getvalescaped("search","");
setcookie("search",$search);

if ($url=="")
	{
	# Special case. Allow this page to function just like the normal view.php page, supporting parameters being passed directly, so that the links on the page still work.
	
	# Assemble a URL from the existing parameters.
	$url=getvalescaped("resourceconnect_source","") . "/pages/view.php?" . $_SERVER["QUERY_STRING"];
	}

$html=file_get_contents($url);

#<!-- START GRAB -->
#<!-- END GRAB -->

$s=strpos($html, "<!-- START GRAB -->");
$e=strpos($html, "<!-- END GRAB -->",$s);
$html=substr($html,$s,$e-$s);

include "../../../include/header.php";

echo $html;

/*
?>
<a href="<?php echo str_replace("view.php","download.php",$url) ?>">Download test</a>
<?php
*/

include "../../../include/footer.php";