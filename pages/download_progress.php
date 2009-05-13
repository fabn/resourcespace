<?php
include "../include/db.php";
include "../include/general.php";

# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}

$ref=getval("ref","");
$size=getval("size","");
$ext=getval("ext","");
$alternative=getval("alternative",-1);

if (!($url=hook("getdownloadurl", "", array($ref, $size, $ext, 1, $alternative)))) // used in remotedownload-plugin
	{
	$url=$baseurl."/pages/download.php?ref=" . $ref  . "&size=" . $size . "&ext=" . $ext . "&k=" . $k . "&alternative=" . $alternative;
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
	window.setTimeout("document.location='<?php echo $url?>'",1000);
	</script>
	<?php
	}
?>

<div class="BasicsBox">

    
	<?php if ($save_as) { 
	# $save_as set or Opera browser? Provide a download link instead. Opera blocks any attempt to send it a download (meta/js redirect)	?>
    <h1><?php echo $lang["downloadresource"]?></h1>
    <p style="font-weight:bold;">&gt;&nbsp;<a href="<?php echo $url?>"><?php echo $lang["rightclicktodownload"]?></a></p>
	<?php } else { 
	# Any other browser - standard 'your download will start shortly' text.
	?>
    <h1><?php echo $lang["downloadinprogress"]?></h1>
    <p><?php echo text("introtext")?></p>
	<?php } ?>
    <p><a href="view.php?ref=<?php echo $ref?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["backtoview"]?></a></p>
    
    <?php if ($k=="") { ?>
    <p><a href="search.php">&gt;&nbsp;<?php echo $lang["backtosearch"]?></a></p>
    <p><a href="home.php">&gt;&nbsp;<?php echo $lang["backtohome"]?></a></p>
    <?php } ?>
</div>

<?php
include "../include/footer.php";
?>