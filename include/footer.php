

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

<?php if (!hook("replaceswapcss")){?>
<script type="text/javascript">
function SwapCSS(css){
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
<?php } ?>

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


<?php if (!hook("replacefooternavright")){?>
<?php if ($about_link || $contact_link) { ?>
<div id="FooterNavRight" class="HorizontalNav HorizontalWhiteNav">
		<ul>
<?php if (!hook("replacefooterlinks")){?>
		<?php if (!$use_theme_as_home && !$use_recent_as_home) { ?><li><a href="<?php echo $baseurl?>/pages/<?php echo $default_home_page?>"><?php echo $lang["home"]?></a></li><?php } ?>
		<?php if ($about_link) { ?><li><a href="<?php echo $baseurl?>/pages/about.php"><?php echo $lang["aboutus"]?></a></li><?php } ?>
		<?php if ($contact_link) { ?><li><a href="<?php echo $baseurl?>/pages/contact.php"><?php echo $lang["contactus"]?></a></li><?php } ?>
<!--	<li><a href="#">Terms&nbsp;&amp;&nbsp;Conditions</a></li>-->
<!--	<li><a href="#">Team&nbsp;Centre</a></li>-->
<?php } /* end hook replacefooterlinks */ ?>
		</ul>
</div>
<?php } ?>
<?php } /* end hook replacefooternavright */ ?>

<?php } ?>

<div id="FooterNavRight" class="OxColourPale"><?php echo text("footer")?></div>

<div class="clearer"></div>
</div>
<?php } ?>

<br />

<?php draw_performance_footer();?>

<?php echo $extrafooterhtml; ?>
<?php hook("footerbottom"); ?>

<?php
//titlebar modifications

if ($show_resource_title_in_titlebar){
$general_title_pages=array("team_content","team_archive","team_resource","research_request","requests","edit","themes","collection_public","collection_manage","team_home","help","home","tag","upload_java_popup","upload_java","upload_swf","contact","geo_search","search_advanced","about","contribute","change_password");
$search_title_pages=array("contactsheet_settings","search","preview_all","collection_edit","edit","collection_download","collection_share","collection_request");
$resource_title_pages=array("view","delete","log","alternative_file","alternative_files","resource_email","edit","preview");

    if (!$frameless_collections){$parentword = 'parent.';} else { $parentword = ''; }
    
    // clear resource or search title for pages that don't apply:
    if (!in_array($pagename,array_merge($general_title_pages,$search_title_pages,$resource_title_pages))){
		echo "<script language='javascript'>\n";
		echo $parentword . "document.title = \"$applicationname\";\n";
		echo "</script>";
    }
    // place resource titles
    else if (in_array($pagename,$resource_title_pages) && !isset($_GET['collection']) && !isset($_GET['java'])) /* for edit page */{
        $title =  htmlspecialchars(i18n_get_translated(get_data_by_field($ref,$view_title_field)));
        echo "<script language='javascript'>\n";
        if ($pagename=="edit"){$title=$lang['action-edit']." - ".$title;}
        echo $parentword . "document.title = \"$applicationname - $title\";\n";
        echo "</script>";
    }

    // place collection titles
    else if (in_array($pagename,$search_title_pages)){
        if (isset($search_title)){
            $title=$lang["searchresults"]." - ".strip_tags($search_title);
        }
        else if (($pagename=="collection_download") || $pagename=="edit" && getval("collection","")!=""){
            $collectiondata=get_collection($collection);
            if (!isset($collectiondata['savedsearch'])||(isset($collectiondata['savedsearch'])&&$collectiondata['savedsearch']==null)){ $collection_tag='';} else {$collection_tag=$lang['smartcollection'].": ";}
            $title=$collection_tag.strip_tags(i18n_get_translated($collectiondata['name']));
            }  
        else {
            $collection=getval("ref","");
            $collectiondata=get_collection($collection);
            if (!isset($collectiondata['savedsearch'])||(isset($collectiondata['savedsearch'])&&$collectiondata['savedsearch']==null)){ $collection_tag='';} else {$collection_tag=$lang['smartcollection'].": ";}
            $title=$collection_tag.strip_tags(i18n_get_translated($collectiondata['name']));
            }
        // add a hyphen if title exists  
        if (strlen($title)!=0){$title="- $title";}    
        if ($pagename=="edit"){$title=" - ".$lang['action-editall']." ".$title;}
        if ($pagename=="collection_share"){$title=" - ".$lang['share']." ".$title;}
        if ($pagename=="collection_edit"){$title=" - ".$lang['action-edit']." ".$title;}
        if ($pagename=="preview_all"){$title=" - ".$lang['preview_all']." ".$title;}
        if ($pagename=="collection_download"){$title=" - ".$lang['download']." ".$title;}
        echo "<script language='javascript'>\n";
        echo $parentword . "document.title = \"$applicationname $title\";\n";
        echo "</script>";
    }
    
      // place page titles
    else if (in_array($pagename,$general_title_pages)){ 
		
		if ($pagename=="themes"){
			$pagetitle=$lang['themes'];
			for ($n=0;$n<$theme_category_levels;$n++){
				if (getval("theme".$n,"")!=""){
					$pagetitle.=" / ".getval("theme".$n,"");
				}
			}
		}
		else if (isset($lang[$pagename])){
			$pagetitle=$lang[$pagename];
		} 
		else if (isset($lang['action-'.$pagename])){
			$pagetitle=$lang["action-".$pagename];
			if (getval("java","")!=""){$pagetitle=$lang['upload']." ".$pagetitle;}
		}
		else if (isset($lang[str_replace("_","",$pagename)])){
			$pagetitle=$lang[str_replace("_","",$pagename)];
		}
		else if ($pagename=="team_content"){
			$pagetitle=$lang['managecontent'];
		}
		else if ($pagename=="collection_public"){
			$pagetitle=$lang["publiccollections"];
		}
		else if ($pagename=="collection_manage"){
			$pagetitle=$lang["mycollections"];
		}
		else if ($pagename=="team_home"){
			$pagetitle=$lang["teamcentre"];
		}
		else if ($pagename=="help"){
			$pagetitle=$lang["helpandadvice"];
		}
		else if ($pagename=="tag"){
			$pagetitle=$lang["tagging"];
		}
		else if (strpos($pagename,"upload")!==false){
			$pagetitle=$lang["upload"];
		}
		else if ($pagename=="contact"){
			$pagetitle=$lang["contactus"];
		}
		else if ($pagename=="geo_search"){
			$pagetitle=$lang["geographicsearch"];
		}
		else if ($pagename=="search_advanced"){
			$pagetitle=$lang["advancedsearch"];
			if (getval("archive","")==2){$pagetitle.=" - ".$lang['archiveonlysearch'];}
		}	
		else if ($pagename=="about"){
			$pagetitle=$lang["aboutus"];
		}	
		else if ($pagename=="contribute"){
			$pagetitle=$lang["mycontributions"];
		}	
		else if ($pagename=="change_password"){
			$pagetitle=$lang["changeyourpassword"];
		}	
		else if ($pagename=="requests"){
			$pagetitle=$lang["myrequests"];
		}	
		else if ($pagename=="team_resource"){
			$pagetitle=$lang["manageresources"];
		}	
		else if ($pagename=="team_archive"){
			$pagetitle=$lang["managearchiveresources"];
		}	
		else {
			$pagetitle="";
		}
		if (strlen($pagetitle)!=0){$pagetitle="- $pagetitle";} 
        echo "<script language='javascript'>\n";
        echo $parentword . "document.title = \"$applicationname $pagetitle\";\n";
        echo "</script>";
    }  
}
   

?>

</body>
</html>

