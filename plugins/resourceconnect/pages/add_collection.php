<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/search_functions.php";
include "../../../include/collections_functions.php";
include "../../../include/authenticate.php";


$title=getvalescaped("title","");
$thumb=getvalescaped("thumb","");
$large_thumb=getvalescaped("large_thumb","");
$xl_thumb=getvalescaped("xl_thumb","");

$url=getvalescaped("url","");
$back=getvalescaped("back","");

# Remove any existing
sql_query("delete from resourceconnect_collection_resources where url='$url'");


# Add to collection
sql_query("insert into resourceconnect_collection_resources (collection,thumb,large_thumb,xl_thumb,url,title) values ('$usercollection','$thumb','$large_thumb','$xl_thumb','$url','$title')");


redirect("pages/collections.php?nc=" . time());

/*
refresh_collection_frame();

$bodyattribs="onload=\"window.setTimeout('history.go(-1);',1000);";

include "../../../include/header.php";
?>
<h1><?php echo $lang["addtocollection"] ?></h1>
<p><?php echo $lang["resourceconnect-addedcollection"] ?></p>
<p>&lt;&nbsp;<a href="<?php echo $back ?>"><?php echo $lang["backtoresourceview"] ?></a></p>
<?php
include "../../../include/footer.php";
?>
*/