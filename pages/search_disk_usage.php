<?php
include "../include/db.php";
include "../include/general.php";
include "../include/resource_functions.php"; //for checking scr access
include "../include/search_functions.php";
include "../include/collections_functions.php";

$results=do_search(getval("search",""),getvalescaped("restypes",""),"relevance",getval("archive",""),-1,"desc",false,"",false,true);
$disk_usage=$results[0]["total_disk_usage"];
$count=$results[0]["total_resources"];

include ("../include/header.php");

?>
<p><a href="search.php?search=<?php echo urlencode(getval("search","")) ?>">&lt; <?php echo $lang["back"] ?></a></p>

<h1><?php echo $lang["searchitemsdiskusage"] ?></h1>
<p><?php echo $lang["matchingresourceslabel"] . ": " . $count?>
<br />
<?php echo $lang["diskusage"] . ": " . formatfilesize($disk_usage) ?></p>

<?php


include ("../include/footer.php");