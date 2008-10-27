<?
include "../include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}

include "../include/general.php";

$ref=getval("ref","");
$size=getval("size","");
$ext=getval("ext","");

if (!($url=hook("getdownloadurl", "", array($ref, $size, $ext))))
	{
	$url=$baseurl."/pages/download.php?ref=" . $ref  . "&size=" . $size . "&ext=" . $ext . "&k=" . $k;
	}

# For Opera and Internet Explorer 7 - redirected downloads are always blocked, so use the '$save_as' config option
# to present a link instead.
if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),"opera")!==false) {$save_as=true;}
if (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]),"msie 7.")!==false) {$save_as=true;}

include "../include/header.php";

if (!$save_as)
	{
	?>
	<script language="Javascript">
	window.setTimeout("document.location='<?=$url?>'",1000);
	</script>
	<?
	}
?>

<div class="BasicsBox">

    
	<? if ($save_as) { 
	# $save_as set or Opera browser? Provide a download link instead. Opera blocks any attempt to send it a download (meta/js redirect)	?>
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
include "../include/footer.php";
?>