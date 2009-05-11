

<?php
# Include theme bar?
if ($use_theme_bar && !in_array($pagename,array("search_advanced","login","preview","admin_header","user_password","user_request")) && ($loginterms==false))
	{
	?></td></tr></table><?php
	}
?>
<div class="clearer"> </div>

</div><!--End div-CentralSpace-->
<?php if (($pagename!="login") && ($pagename!="user_password") && ($pagename!="user_request")) { ?></div><?php } ?><!--End div-CentralSpaceContainer-->

<div class="clearer"></div>

<?php if (($pagename!="login") && ($pagename!="user_password") && ($pagename!="done") && ($pagename!="preview") && ($pagename!="change_language") && ($loginterms==false)) { ?>
<!--Global Footer-->
<div id="Footer">

<script language="Javascript">
function SetCookie(cookieName,cookieValue,nDays) {
 var today = new Date();
 var expire = new Date();
 if (nDays==null || nDays==0) nDays=1;
 expire.setTime(today.getTime() + 3600000*24*nDays);
 document.cookie = cookieName+"="+escape(cookieValue)
                 + ";expires="+expire.toGMTString();
}
function SwapCSS(css)
	{
	document.getElementById('colourcss').href='<?php echo $baseurl?>/css/Col-' + css + '.css?css_reload_key=<?php echo $css_reload_key?>';
	<?php if (!checkperm("b") && !$frameless_collections) { ?>top.collections.document.getElementById('colourcss').href='<?php echo $baseurl?>/css/Col-' + css + '.css';<?php } ?>
	SetCookie("colourcss",css,1000);	
	}
</script>

<?php if (getval("k","")=="") { ?>
<div id="FooterNavLeft" class=""><?php if (isset($userfixedtheme) && $userfixedtheme=="") { ?><?php echo $lang["interface"]?>:&nbsp;&nbsp;<a href="#" onClick="SwapCSS('greyblu');return false;"><img src="<?php echo $baseurl?>/gfx/interface/BlueChip.gif" alt="" width="11" height="11" /></a>&nbsp;<a href="#" onClick="SwapCSS('whitegry');return false;"><img src="<?php echo $baseurl?>/gfx/interface/WhiteChip.gif" alt="" width="11" height="11" /></a>&nbsp;<a href="#" onClick="SwapCSS('black');return false;"><img src="<?php echo $baseurl?>/gfx/interface/BlackChip.gif" alt="" width="11" height="11" /></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
<?php if ($disable_languages==false){?>
<?php echo $lang["language"]?>: <a href="<?php echo $baseurl?>/pages/change_language.php"><?php echo $languages[$language]?></a>
<?php } ?>
</div>

<?php if ($about_link || $contact_link) { ?>
<div id="FooterNavRight" class="HorizontalNav HorizontalWhiteNav">
		<ul>
		<?php if (!$use_theme_as_home) { ?><li><a href="<?php echo $baseurl?>/pages/home.php"><?php echo $lang["home"]?></a></li><?php } ?>
		<?php if ($about_link) { ?><li><a href="<?php echo $baseurl?>/pages/about.php"><?php echo $lang["aboutus"]?></a></li><?php } ?>
		<?php if ($contact_link) { ?><li><a href="<?php echo $baseurl?>/pages/contact.php"><?php echo $lang["contactus"]?></a></li><?php } ?>
<!--	<li><a href="#">Terms&nbsp;&amp;&nbsp;Conditions</a></li>-->
<!--	<li><a href="#">Team&nbsp;Centre</a></li>-->
		</ul>
</div>
<?php } ?>

<?php } ?>

<div id="FooterNavRight" class="OxColourPale"><?php echo text("footer")?></div>

<div class="clearer"></div>
</div>
<?php } ?>



<!--c<?php echo $querycount?>, t<?php echo $querytime?>-->
<br />

<?php hook("footerbottom"); ?>

<div id="chromeFix"></div>

</body>
</html>
	
