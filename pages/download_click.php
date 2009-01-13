<?php
include "../include/db.php";
include "../include/general.php";

$url=getval("url","");
$url=str_replace(" ","%20",$url);

include "../include/header.php";
?>

<div class="BasicsBox">
    <h1><?php echo $lang["downloadresource"]?></h1>
    <p><?php echo text("introtext")?></p>
    
    <p style="font-weight:bold;">&gt;&nbsp;<a href="<?php echo $url?>"><?php echo $lang["rightclicktodownload"]?></a></p>
    
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    <p><a href="search.php">&gt;&nbsp;<?php echo $lang["backtosearch"]?></a></p>
    <p><a href="home.php">&gt;&nbsp;<?php echo $lang["backtohome"]?></a></p>
</div>

<?php
include "../include/footer.php";
?>