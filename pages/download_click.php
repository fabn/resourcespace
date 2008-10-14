<?
include "../include/db.php";
include "../include/general.php";

$url=getval("url","");
$url=str_replace(" ","%20",$url);

include "../include/header.php";
?>

<div class="BasicsBox">
    <h1><?=$lang["downloadresource"]?></h1>
    <p><?=text("introtext")?></p>
    
    <p style="font-weight:bold;">&gt;&nbsp;<a href="<?=$url?>"><?=$lang["rightclicktodownload"]?></a></p>
    
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    
    <p><a href="search.php">&gt;&nbsp;<?=$lang["backtosearch"]?></a></p>
    <p><a href="home.php">&gt;&nbsp;<?=$lang["backtohome"]?></a></p>
</div>

<?
include "../include/footer.php";
?>