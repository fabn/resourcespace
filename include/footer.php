
<div class="clearer"></div>
</div><!--End div-CentralSpace-->
<? if (($pagename!="login") && ($pagename!="user_password") && ($pagename!="user_request")) { ?></div><? } ?><!--End div-CentralSpaceContainer-->

<div class="clearer"></div>

<? if (($pagename!="login") && ($pagename!="preview") && ($pagename!="change_language") && ($loginterms==false)) { ?>
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
	document.getElementById('colourcss').href='css/Col-' + css + '.css';
	top.collections.document.getElementById('colourcss').href='css/Col-' + css + '.css';
	SetCookie("colourcss",css,1000);	
	}
</script>

<? if (getval("k","")=="") { ?>
<div id="FooterNavLeft" class=""><? if (isset($userfixedtheme) && $userfixedtheme=="") { ?><?=$lang["interface"]?>:&nbsp;&nbsp;<a href="#" onClick="SwapCSS('greyblu');return false;"><img src="gfx/interface/BlueChip.gif" alt="" width="11" height="11" /></a>&nbsp;<a href="#" onClick="SwapCSS('whitegry');return false;"><img src="gfx/interface/WhiteChip.gif" alt="" width="11" height="11" /></a>&nbsp;<a href="#" onClick="SwapCSS('black');return false;"><img src="gfx/interface/BlackChip.gif" alt="" width="11" height="11" /></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? } ?>
<?=$lang["language"]?>: <a href="change_language.php"><?=$languages[$language]?></a>
</div>

<div id="FooterNavRight" class="HorizontalNav HorizontalWhiteNav">
		<ul>
		<li><a href="home.php"><?=$lang["home"]?></a></li>
		<li><a href="about.php"><?=$lang["aboutus"]?></a></li>
		<li><a href="contact.php"><?=$lang["contactus"]?></a></li>
<!--	<li><a href="#">Terms&nbsp;&amp;&nbsp;Conditions</a></li>-->
<!--	<li><a href="#">Team&nbsp;Centre</a></li>-->
		</ul>
</div>
<? } ?>

<div id="FooterNavRight" class="OxColourPale"><?=text("footer")?></div>

<div class="clearer"></div>
</div>
<? } ?>
<? if ($pagename=="login") { 
# Smaller footer just for the login screen to include language selection.
?>
<div id="Footer">
<div id="FooterNavLeft"><?=$lang["language"]?>: <a href="change_language.php"><?=$languages[$language]?></a></div>
</div>
<? } ?>


<!--c<?=$querycount?>, t<?=$querytime?>-->
<br />
</body>
</html>
	
