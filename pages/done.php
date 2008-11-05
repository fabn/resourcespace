<?
include "../include/db.php";
include "../include/general.php";
if (getval("user","")!="") {include "../include/authenticate.php";} # Authenticate if already logged in, so the correct theme is displayed when using user group specific themes.
include "../include/header.php";
?>

<div class="BasicsBox">
    <h1><?=$lang["complete"]?></h1>
    <p><?=text(getvalescaped("text",""))?></p>
    
    <? if (getval("user","")!="") { # User logged in? ?>
    <p><a href="home.php">&gt;&nbsp;<?=$lang["backtohome"]?></a></p>
    <p><a href="search.php">&gt;&nbsp;<?=$lang["backtosearch"]?></a></p>
    <? } else {?>
    <p><a href="../login.php">&gt;&nbsp;<?=$lang["backtouser"]?></a></p>
    <? } ?>
</div>

<?
include "../include/footer.php";
?>