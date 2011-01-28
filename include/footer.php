

<?php
# Include theme bar?
if ($use_theme_bar && !in_array($pagename,array("search_advanced","login","preview","admin_header","user_password","user_request")) && ($loginterms==false))
	{
	?></td></tr></table><?php
	}
?>
<div class="clearer"> </div>

</div><!--End div-CentralSpace-->
<?php if (($pagename!="login") && ($pagename!="user_password") && ($pagename!="preview_all") && ($pagename!="user_request")) { ?></div><?php } ?><!--End div-CentralSpaceContainer-->

<div class="clearer"></div>

<?php hook("footertop"); ?>

<?php if (($pagename!="login") && ($pagename!="user_request") && ($pagename!="user_password") && ($pagename!="preview_all")&& ($pagename!="done") && ($pagename!="preview") && ($pagename!="change_language") && ($loginterms==false)) { ?>
<!--Global Footer-->
<div id="Footer">

<script type="text/javascript">

function SwapCSS(css)
	{
	document.getElementById('colourcss').href='<?php echo $baseurl?>/css/Col-' + css + '.css?css_reload_key=<?php echo $css_reload_key?>';
	<?php if (!checkperm("b") && !$frameless_collections) { ?>parent.collections.document.getElementById('colourcss').href='<?php echo $baseurl?>/css/Col-' + css + '.css';<?php } ?>
	
	<?php 
	if ($global_cookies){?>
	document.cookie ='colourcss='+css+'; path=/';<?php } 
	else { ?> 
	SetCookie("colourcss",css,1000);  
	<?php }?>
	
<?php for ($n=0;$n<count($plugins);$n++)
	{
	$csspath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/css/Col-".$theme.".css";	
	if (file_exists($csspath))
		{
		?>
	document.getElementById('<?php echo $plugins[$n]?>css').href='<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/Col-' + css + '.css';
	<?php if (!checkperm("b") && !$frameless_collections) { ?>parent.collections.document.getElementById('<?php echo $plugins[$n]?>css').href='<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/Col-' + css + '.css';<?php } ?>
	<?php }
	}?>
	
	}
</script>

<?php if (getval("k","")=="") { ?>
<div id="FooterNavLeft" class=""><?php if (isset($userfixedtheme) && $userfixedtheme=="") { ?><?php echo $lang["interface"]?>:&nbsp;&nbsp;
<?php // enable custom theme chips 
	if (count($available_themes!=0)){
		foreach ($available_themes as $available_theme){?>
		&nbsp;<a href="#" onClick="SwapCSS('<?php echo $available_theme?>');return false;"><img src="<?php echo $baseurl?>/gfx/interface/<?php echo ucfirst($available_theme)?>Chip.gif" alt="" width="11" height="11" /></a>
	<?php }
	}
?>	
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
<?php if ($disable_languages==false){?>
<?php echo $lang["language"]?>: <a href="<?php echo $baseurl?>/pages/change_language.php"><?php echo $languages[$language]?></a>
<?php } ?>
</div>


<?php if ($about_link || $contact_link) { ?>
<div id="FooterNavRight" class="HorizontalNav HorizontalWhiteNav">
		<ul>
<?php if (!hook("replacefooterlinks")){?>
		<?php if (!$use_theme_as_home && !$use_recent_as_home) { ?><li><a href="<?php echo $baseurl?>/pages/home.php"><?php echo $lang["home"]?></a></li><?php } ?>
		<?php if ($about_link) { ?><li><a href="<?php echo $baseurl?>/pages/about.php"><?php echo $lang["aboutus"]?></a></li><?php } ?>
		<?php if ($contact_link) { ?><li><a href="<?php echo $baseurl?>/pages/contact.php"><?php echo $lang["contactus"]?></a></li><?php } ?>
<!--	<li><a href="#">Terms&nbsp;&amp;&nbsp;Conditions</a></li>-->
<!--	<li><a href="#">Team&nbsp;Centre</a></li>-->
<?php } /* end hook replacefooterlinks */ ?>
		</ul>
</div>
<?php } ?>

<?php } ?>

<div id="FooterNavRight" class="OxColourPale"><?php echo text("footer")?></div>

<div class="clearer"></div>
</div>
<?php } ?>

<br />

<?php if ($config_show_performance_footer)
	{
	# --- If configured (for debug/development only) show query statistics
	?>
	<table class="InfoTable" style="float: right;margin-right: 10px;">
	<tr><td>Query count</td><td><?php echo $querycount?></td></tr>
	<tr><td>Query time</td><td><?php echo round($querytime,4)?></td></tr>
	<tr><td colspan=2><a href="#" onClick="document.getElementById('querylog').style.display='block';return false;">&gt;&nbsp;details</a></td></tr>
	</table>
	<table class="InfoTable" id="querylog" style="display: none; float: right; margin: 10px;">
	<?php
	arsort($querylog);
	foreach ($querylog as $query=>$time)
		{
		?>
		<tr><td align="left"><?php echo $query?></td><td>&nbsp;
		<table class="InfoTable">
		<?php if (strtolower(substr($query,0,6))=="select"){
			
			$explain=sql_query("explain ".$query);
			?><tr><?php
			foreach ($explain[0] as $explainitem=>$value){?>
				<td align="left">   
				<?php echo $explainitem?></td><?php 
				}
			?></tr><?php
			for($n=0;$n<count($explain);$n++){
				?><tr><?php
				foreach ($explain[$n] as $explainitem=>$value){?>
				<td align="left">   
					<?php echo str_replace(",",", ",$value)?></td><?php 
					}
				?></tr><?php	
				}
			}	?>
		</table>
		</td><td><?php echo round($time,4)?></td></tr>
		<?php	
		}
	?>
	</table>
	<?php
	}
?>

<?php echo $extrafooterhtml; ?>
<?php hook("footerbottom"); ?>


</body>
</html>
	
