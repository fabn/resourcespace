<?
include "include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "include/authenticate.php";}

include "include/general.php";

$ref=getval("ref","");
$size=getval("size","");
$ext=getval("ext","");

# Add a meta refresh to the header
$url=$baseurl . "/download.php?ref=" . $ref  . "&size=" . $size . "&ext=" . $ext . "&k=" . $k;
/*
Done with Javascript instead (this was a test to see if it got around the Opera blocking issue)
$headerinsert.="<meta http-equiv=\"refresh\" content=\"1;url=" . $url . "\"/>";
*/

include "include/header.php";
?>
<script language="Javascript">
window.setTimeout("document.location='download.php?ref=<?=$ref?>&size=<?=$size?>&ext=<?=$ext?>&k=<?=$k?>'",1000);
</script>

<div class="BasicsBox">

    
	<? if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),"opera")!==false) { 
	# Opera browser? Provide a download link instead. Opera blocks any attempt to send it a download (meta/js redirect)	?>
    <h1><?=$lang["downloadresource"]?></h1>
    <p style="font-weight:bold;">&gt;&nbsp;<a href="<?=$url?>"><?=$lang["rightclicktodownload"]?></a></p>
	<? } else { 
	# Any other browser - standard 'your download will start shortly' text.
	?>
    <h1><?=$lang["downloadinprogress"]?></h1>
    <p><?=text("introtext")?></p>
	<? } ?>
    <p><a href="view.php?ref=<?=$ref?>&k=<?=$k?>">&gt;&nbsp;<?=$lang["backtoview"]?></a></p>
    
    <? if ($k=="") { ?>
    <p><a href="search.php">&gt;&nbsp;<?=$lang["backtosearch"]?></a></p>
    <p><a href="home.php">&gt;&nbsp;<?=$lang["backtohome"]?></a></p>
    <? } ?>
</div>

<?
include "include/footer.php";
?>