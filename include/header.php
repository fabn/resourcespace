<?php 
// blank starsearch cookie in case $star_search was turned off
setcookie("starsearch","");
if ($collections_compact_style && $frameless_collections){echo "error. collections_compact_style and frameless_collections are currently incompatible";}
// cookies have to go above the header output
if ($display_user_rating_stars && $star_search){
	# if seardch is not a special search (ie. !recent), use starsearchvalue.
	if (getval("search","")!="" && strpos(getval("search",""),"!")!==false)
		{
		$starsearch="";
		}
	else
		{
		$starsearch=getvalescaped("starsearch","");	
		setcookie("starsearch",$starsearch);
	    }
	}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<?php if ($include_rs_header_info){?>
<!--<?php hook("copyrightinsert");?>
ResourceSpace version <?php echo $productversion?>

Copyright Oxfam GB, Montala, WWF International, Tom Gleason, David Dwiggins, Historic New England, Colorhythm LLC, Worldcolor 2006-2010
http://www.resourcespace.org/
-->
<?php } ?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<title><?php echo htmlspecialchars($applicationname)?></title>
<link rel="icon" type="image/png" href="<?php echo $baseurl."/".$header_favicon?>" />
<link href="<?php echo $baseurl?>/css/global.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" />
<?php if (!hook("adjustcolortheme")){ ?>
<link href="<?php echo $baseurl?>/css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss",$defaulttheme)?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<?php } ?>
<?php if ($pagename!="preview_all"){?><!--[if lte IE 7]> <link href="<?php echo $baseurl?>/css/globalIE.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]--><?php } ?>
<!--[if lte IE 5.6]> <link href="<?php echo $baseurl?>/css/globalIE5.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<script src="<?php echo $baseurl?>/lib/js/prototype.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>
<script src="<?php echo $baseurl?>/lib/js/scriptaculous.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>
<script src="<?php echo $baseurl?>/lib/js/global.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>
<script src="<?php echo $baseurl?>/lib/js/category_tree.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>

<?php if ($frameless_collections) { ?>
<script src="<?php echo $baseurl?>/lib/js/frameless_collections.js?css_reload_key=<?php echo $css_reload_key?>" type="text/javascript"></script>
<script type="text/javascript">
var baseurl_short="<?php echo $baseurl_short?>";
</script>
<?php } ?>

<?php hook("additionalheaderjs");?>

<?php
# Include CSS files for for each of the plugins too (if provided)
for ($n=0;$n<count($plugins);$n++)
	{
	$csspath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/css/style.css";
	if (file_exists($csspath))
		{
		?>
		<link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/style.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print"  />
		<?php
		}
	$theme=((isset($userfixedtheme) && $userfixedtheme!=""))?$userfixedtheme:getval("colourcss",$defaulttheme);
	$csspath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/css/Col-".$theme.".css";	
	if (file_exists($csspath))
		{
		?>
		<link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/Col-<?php echo $theme?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="<?php echo $plugins[$n]?>css" />
		<?php
		}	
	}
?>

<?php
echo $headerinsert;
$extrafooterhtml="";
?>

<?php
# Check for the frameset, and if necessary, redirect to index.php so the frameset is drawn.
if (($pagename!="terms") && ($pagename!="change_language") && ($pagename!="login") && ($pagename!="user_request") && ($pagename!="user_password") && ($pagename!="done") && (getval("k","")=="") && (!$frameless_collections) && (!checkperm("b"))) { ?>
<script type="text/javascript">
if (!top.collections) {document.location='<?php echo $baseurl?>/index.php?url=' + escape(document.location);} // Missing frameset? redirect to frameset.
</script>
<?php } ?>

<?php hook("headblock"); ?>

<?php if ($collections_compact_style && $pagename!="login"){ include dirname(__FILE__)."/../lib/js/colactions.js";}?>

</head>

<body <?php if (isset($bodyattribs)) { ?><?php echo $bodyattribs?><?php } ?>>
<?php hook("bodystart"); ?>

<?php
# Commented as it was causing IE to 'jump'
# <body onLoad="if (document.getElementById('searchbox')) {document.getElementById('searchbox').focus();}">
?>

<!--Global Header-->
<?php
if (($pagename=="terms") && (getval("url","")=="index.php")) {$loginterms=true;} else {$loginterms=false;}
if ($pagename!="preview" && $pagename!="preview_all") { ?>

<?php
$homepage_url=$baseurl."/pages/".$default_home_page;
if ($use_theme_as_home){$homepage_url=$baseurl."/pages/themes.php";}
if ($use_recent_as_home){$homepage_url=$baseurl."/pages/search.php?search=".urlencode('!last'.$recent_search_quantity);}
?>

<div id="Header" <?php if ($header_text_title){?>style="background:none;"<?php } ?>>
<?php if ($header_link && !$header_text_title){?><a class="headerlink" style="margin:20px 25px; position: absolute; display: block; width: <?php echo $header_link_width?>px; height: <?php echo $header_link_height?>px;  top: 0px; left: 0px;" href="<?php echo $homepage_url?>"></a><?php } ?>
<?php if ($header_text_title){?>
    <div id="TextHeader"><a href="<?php echo $homepage_url?>"><?php echo $applicationname;?></a></div>
    <?php if ($applicationdesc!=""){?>
        <div id="TextDesc"><?php echo i18n_get_translated($applicationdesc);?></div>
    <?php } ?>
<?php }


hook("headertop");

if (!isset($allow_password_change)) {$allow_password_change=true;}

if (isset($username) && ($pagename!="login") && ($loginterms==false)) { ?>
<div id="HeaderNav1" class="HorizontalNav ">

<?php if (isset($anonymous_login) && ($username==$anonymous_login))
	{
	?>
	<ul>
	<li><a href="<?php echo $baseurl?>/login.php" target="_top"><?php echo $lang["login"]?></a></li>
	<?php if ($contact_link) { ?><li><a href="<?php echo $baseurl?>/pages/contact.php"><?php echo $lang["contactus"]?></a></li><?php } ?>
	</ul>
	<?php
	}
else
	{
	if (!hook("replaceheadernav1")) {
	?>
	<ul>
	<?php if (!hook("replaceheaderfullnamelink")){?>
	<li><?php if ($allow_password_change && !checkperm("p")) { ?>
	<a href="<?php echo $baseurl?>/pages/change_password.php"><?php } ?><?php echo ($userfullname=="" ? $username : $userfullname) ?><?php } /* end replacefullnamelink */?><?php if ($allow_password_change && !checkperm("p")) { ?></a><?php } ?></li>
	<?php hook("addtoplinks");?>
	<li><a href="<?php echo $baseurl?>/login.php?logout=true&nc=<?php echo time()?>" target="_top"><?php echo $lang["logout"]?></a></li>
	<?php hook("addtologintoolbarmiddle");?>
	<?php if ($contact_link) { ?><li><a href="<?php echo $baseurl?>/pages/contact.php"><?php echo $lang["contactus"]?></a></li><?php } ?>
	</ul>
	<?php
	} /* end replaceheadernav1 */
	}
?>
</div>

<?php 
# Work out target to use for links
if (!$frameless_collections && !checkperm("b")) {$target="main";} else {$target="_top";}

?>

<div id="HeaderNav2" class="HorizontalNav HorizontalWhiteNav">

<?php if ($breadcrumbs) { ?>
<div class="Breadcrumbs"><?php echo get_breadcrumbs()?></div>
<?php } ?>
		<?php if (!hook("replaceheadernav2")) { ?>
		<ul>
		<?php if (!$use_theme_as_home && !$use_recent_as_home) { ?><li><a href="<?php echo $baseurl?>/pages/<?php echo $default_home_page?>" target="<?php echo $target?>"><?php echo $lang["home"]?></a></li><?php }  
		hook("topnavlinksafterhome");
		?>
		<?php if ($advanced_search_nav) { ?><li><a href="<?php echo $baseurl?>/pages/search_advanced.php" target="<?php echo $target?>"><?php echo $lang["advancedsearch"]?></a></li><?php }  ?>
		<?php if 	(
			(checkperm("s"))  && (! $disable_searchresults )
		&&
			(
				(isset($_COOKIE["search"]) && strlen($_COOKIE["search"])>0)
			||
				(isset($search) && (strlen($search)>0) && (strpos($search,"!")===false))
			)
		)
		{?>
		<?php if ($search_results_link){?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/search.php"><?php echo $lang["searchresults"]?></a></li><?php } ?><?php } ?>
		<?php if (checkperm("s") && $enable_themes) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/themes.php"><?php echo $lang["themes"]?></a></li><?php } ?>
		<?php if (checkperm("s") && ($public_collections_top_nav || $public_collections_header_only)) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/collection_public.php"><?php echo $lang["publiccollections"]?></a></li><?php } ?>
		<?php if (checkperm("s") && $mycollections_link && !checkperm("b")) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/collection_manage.php"><?php echo $lang["mycollections"]?></a></li><?php } ?>
		<?php if (!hook("replacerecentlink")) { ?>
		<?php if (checkperm("s") && $recent_link) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/search.php?search=<?php echo urlencode("!last".$recent_search_quantity)?>"><?php echo $lang["recent"]?></a></li><?php } ?>
		<?php } /* end hook replacerecentlink */?>
		<?php if (checkperm("s") && $myrequests_link && checkperm("q")) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/requests.php"><?php echo $lang["myrequests"]?></a></li><?php } ?>
		<?php if (!hook("replacemycontributionslink")) { ?>
		<?php if (checkperm("d")||(isset($mycontributions_link) && $mycontributions_link && checkperm("c"))) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/contribute.php"><?php echo $lang["mycontributions"]?></a></li><?php } ?>
		<?php } /* end hook replacemycontributionslink */?>
		<?php if (!hook("replaceresearchrequestlink")) { ?>
		<?php if (($research_request) && (checkperm("s")) && (checkperm("q"))) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/research_request.php"><?php echo $lang["researchrequest"]?></a></li><?php } ?>
		<?php } ?>
		<?php if ($speedtagging && checkperm("s") && checkperm("n")) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/tag.php"><?php echo $lang["tagging"]?></a></li><?php } ?>
		
		<?php 
		/* ------------ Customisable top navigation ------------------- */
		if (isset($custom_top_nav))
			{
			for ($n=0;$n<count($custom_top_nav);$n++)
				{
				
				if (preg_match("/^https?\:\/\/.+/",$custom_top_nav[$n]['link'])){
					$isextlink = true;
				} else {
					$isextlink = false;
				}
				if(strpos($custom_top_nav[$n]["title"],"(lang)")!==false){
					$custom_top_nav_title=str_replace("(lang)","",$custom_top_nav[$n]["title"]);
					$custom_top_nav[$n]["title"]=$lang[$custom_top_nav_title];
				}
				?>
				<li><a target="<?php if ($isextlink){echo "_top"; } else {  echo $target; } ?>" href="<?php echo $custom_top_nav[$n]["link"] ?>"><?php echo i18n_get_translated($custom_top_nav[$n]["title"]) ?></a></li>
				<?php
				}
			}
		?>
		
		
		<?php if ($help_link){?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/help.php"><?php echo $lang["helpandadvice"]?></a></li><?php } ?>
		<?php if ($top_nav_upload && checkperm("c")) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/edit.php?ref=-<?php echo @$userref?>&amp;<?php echo $top_nav_upload_type ?>=true"><?php echo $lang["upload"]?></a></li><?php } ?>
		<?php if (checkperm("t")) { ?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/pages/team/team_home.php"><?php echo $lang["teamcentre"]?></a>
		<?php if ($team_centre_alert_icon && (checkperm("R")||checkperm("r")) &&  (sql_value("select sum(thecount) value from (select count(*) thecount from request where status = 0 union select count(*) thecount from research_request where status = 0) as theunion",0) > 0)){
			echo "<img src='$baseurl/gfx/images/attention_16.png' width='16' height='16' style='position:relative;top:3px;' />";	
		} ?>
		</li><?php } ?>

<?php hook("toptoolbaradder"); ?>
		</ul>
<?php } /* end replaceheadernav1 */ ?>
		
</div>

<?php }  else { # Empty Header?>
<div id="HeaderNav1" class="HorizontalNav ">&nbsp;</div>
<div id="HeaderNav2" class="HorizontalNav HorizontalWhiteNav">&nbsp;</div>
<?php } ?>
</div>
<?php } ?>

<?php hook("headerbottom"); ?>

<div class="clearer"></div>
<?php
# Include simple search sidebar?
$omit_searchbar_pages=array("index","preview_all","search_advanced","preview","admin_header");
$modified_omit_searchbar_pages=hook("modifyomitsearchbarpages");
if ($modified_omit_searchbar_pages){$omit_searchbar_pages=$modified_omit_searchbar_pages;}

if (!in_array($pagename,$omit_searchbar_pages) && ($loginterms==false)) 	
	{
	include "searchbar.php";
	}
?>	



<?php 
# Determine which content holder div to use
if (($pagename=="login") || ($pagename=="user_password") || ($pagename=="user_request")) {$div="CentralSpaceLogin";}
else {$div="CentralSpace";}
?>
<!--Main Part of the page-->
<?php if (($pagename!="login") && ($pagename!="user_password") && ($pagename!="user_request")) { ?><div id="CentralSpaceContainer"><?php } ?>
<div id="<?php echo $div?>">


<?php
# Include theme bar?
if ($use_theme_bar && (getval("k","")=="") && !in_array($pagename,array("themes","preview_all","done","search_advanced","login","preview","admin_header","user_password","user_request")) && ($loginterms==false))
	{
	# Tables seem to be the only solution to having a left AND right side bar, due to the way the clear CSS attribute works.
	?>
	<table width="100%" style="margin:0;padding:0;"><tr><td width="185" valign="top" align="left" style="margin:0;padding:0;">
	<?php
	include "themebar.php";
	?>
	</td><td valign="top" style="margin:0;padding:0;">
	<?php
	}
?>
