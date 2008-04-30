<?

require(dirname(__FILE__)."/../pages/cpm.php");
/*
function HookMessagingAllToptoolbaradder()
	{
	global $baseurl;
?>
<li><a target="main" href="<?=$baseurl?>/plugins/messaging/pages/main.php">Mail</a> 
<?
	return true;
	}
*/
function HookMessagingAllAddtologintoolbarmiddle()
	{
	global $baseurl, $userref;
	
	$pm = new cpm($userref);
	$pm->getmessages();
?>
<li><a target="main" href="<?=$baseurl?>/plugins/messaging/pages/main.php">Mail (<?=count($pm->messages)?>)</a> 
<?
	return true;
	}
?>