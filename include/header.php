<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=htmlspecialchars($applicationname)?></title>
<link href="css/wrdsnpics.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
<link href="css/Col-<?=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu")?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<!--[if lte IE 6]> <link href="css/wrdsnpicsIE.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<!--[if lte IE 5.6]> <link href="css/wrdsnpicsIE5.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<?=$headerinsert?>

<? if (($pagename!="terms") && ($pagename!="change_language") && ($pagename!="login") && ($pagename!="user_request") && ($pagename!="user_password") && ($pagename!="done") && (getval("k","")=="")) { ?>
<script language="Javascript">
if (!top.collections) {document.location='index.php?url=' + escape(document.location);} // Missing frameset? redirect to frameset.
</script>
<? } ?>

<? hook("headblock"); ?>

</head>

<body>
<? hook("bodystart"); ?>

<?
# Commented as it was causing IE to 'jump'
# <body onLoad="if (document.getElementById('searchbox')) {document.getElementById('searchbox').focus();}">
?>

<!--Global Header-->
<?
if (($pagename=="terms") && (getval("url","")=="index.php")) {$loginterms=true;} else {$loginterms=false;}
if ($pagename!="preview") { ?>
<div id="Header">

<? 
if (!isset($allow_password_change)) {$allow_password_change=true;}

if (isset($username) && ($pagename!="login") && ($loginterms==false)) { ?>
<div id="HeaderNav1" class="HorizontalNav ">
		<ul>
		<li><? if ($allow_password_change) { ?><a href="change_password.php"><? } ?><?=$userfullname?><? if ($allow_password_change) { ?></a><? } ?></li>
		<li><a href="login.php?logout=true&nc=<?=time()?>" target="_top"><?=$lang["logout"]?></a></li>
		<li><a href="contact.php"><?=$lang["contactus"]?></a></li>
		</ul>
</div>


<div id="HeaderNav2" class="HorizontalNav HorizontalWhiteNav">
		<? if (checkperm("s")) { ?>
		<ul>
		<? if (!$use_theme_as_home) { ?><li><a href="home.php" target="main"><?=$lang["home"]?></a></li><? }  ?>
		
		<? if 	(
			(checkperm("s"))
		&&
			(
				(strlen(@$_COOKIE["search"])>0)
			||
				((strlen(@$search)>0) && (strpos($search,"!")===false))
			)
		)
		{?><li><a target="main" href="search.php"><?=$lang["searchresults"]?></a></li><? } ?>
		<li><a target="main" href="themes.php"><?=$lang["themes"]?></a></li>
		<? if ($recent_link) { ?><li><a target="main" href="search.php?search=<?=urlencode("!last1000")?>"><?=$lang["recent"]?></a></li><? } ?>
		<? if ($mycollections_link) { ?><li><a target="main" href="collection_manage.php"><?=$lang["mycollections"]?></a></li><? } ?>
		<? if (checkperm("d")) { ?><li><a target="main" href="contribute.php"><?=$lang["mycontributions"]?></a></li><? } ?>
		<? if (($research_request) && (checkperm("q"))) { ?><li><a target="main" href="research_request.php"><?=$lang["researchrequest"]?></a></li><? } ?>
		
		<? if ($speedtagging && checkperm("n")) { ?><li><a target="main" href="tag.php"><?=$lang["tagging"]?></a></li><? } ?>
		
		<li><a target="main" href="help.php"><?=$lang["helpandadvice"]?></a></li>
		<? if (checkperm("t")) { ?><li><a target="main" href="team_home.php"><?=$lang["teamcentre"]?></a></li><? } ?>
		</ul>
		<? } else { ?>
		&nbsp;
		<? } ?>
</div>

<? }  else { # Empty Header?>
<div id="HeaderNav1" class="HorizontalNav ">&nbsp;</div>
<div id="HeaderNav2" class="HorizontalNav HorizontalWhiteNav">&nbsp;</div>
<? } ?>
</div>
<? } ?>
<div class="clearer"></div>
<? if (checkperm("s") && ($pagename!="search_advanced") && ($pagename!="preview") && ($pagename!="admin_header") && ($loginterms==false)) { ?>
<? include "searchbar.php"; ?>
<? } ?>

<? 
# Determine which content holder div to use
if (($pagename=="login") || ($pagename=="user_password") || ($pagename=="user_request")) {$div="CentralSpaceLogin";}
else {$div="CentralSpace";}
?>
<!--Main Part of the page-->
<? if (($pagename!="login") && ($pagename!="user_password") && ($pagename!="user_request")) { ?><div id="CentralSpaceContainer"><? } ?>
<div id="<?=$div?>">