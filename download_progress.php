<?
include "include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "include/authenticate.php";}

include "include/general.php";

$ref=getval("ref","");
$size=getval("size","");
$ext=getval("ext","");


include "include/header.php";
?>
<script language="Javascript">
window.setTimeout("document.location='download.php?ref=<?=$ref?>&size=<?=$size?>&ext=<?=$ext?>&k=<?=$k?>'",1000);
</script>

<div class="BasicsBox">
    <h1><?=$lang["downloadinprogress"]?></h1>
    <p><?=text("introtext")?></p>
    
    <p><a href="view.php?ref=<?=$ref?>&k=<?=$k?>">&gt;&nbsp;<?=$lang["backtoview"]?></a></p>
    
    <? if ($k=="") { ?>
    <p><a href="search.php">&gt;&nbsp;<?=$lang["backtosearch"]?></a></p>
    <p><a href="home.php">&gt;&nbsp;<?=$lang["backtohome"]?></a></p>
    <? } ?>
</div>

<?
include "include/footer.php";
?>