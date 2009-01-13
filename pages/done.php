<?php
include "../include/db.php";
include "../include/general.php";
if (getval("user","")!="") {include "../include/authenticate.php";} # Authenticate if already logged in, so the correct theme is displayed when using user group specific themes.
include "../include/header.php";
?>

<div class="BasicsBox">
    <h1><?php echo $lang["complete"]?></h1>
    <p><?php echo text(getvalescaped("text",""))?></p>
    
    <?php if (getval("user","")!="") { # User logged in? ?>
    <p><a href="home.php">&gt;&nbsp;<?php echo $lang["backtohome"]?></a></p>
    <p><a href="search.php">&gt;&nbsp;<?php echo $lang["backtosearch"]?></a></p>
    <?php hook("extra");?>
    <?php } else {?>
    <p><a href="../login.php">&gt;&nbsp;<?php echo $lang["backtouser"]?></a></p>
    <?php } ?>
</div>

<?php
include "../include/footer.php";
?>