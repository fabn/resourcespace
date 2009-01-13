<?php
# pull values from cookies if necessary, for non-search pages where this info hasn't been submitted
if (!isset($restypes)) {$restypes=@$_COOKIE["restypes"];}
if (!isset($search) || ((strpos($search,"!")!==false))) {$quicksearch=@$_COOKIE["search"];} else {$quicksearch=$search;}

$setcountry="";
# Attempt to resolve a comma separated, country appended format back to the user's original quick search.
# This is purely a visual aid so the side bar stays the same rather than displaying the expanded query.
$count_special=0;# Keep track of how many other 'specials' we find
$keywords=split_keywords($quicksearch);
$simple=array();
$found_country="";$found_year="";$found_month="";$found_day="";
for ($n=0;$n<count($keywords);$n++)
	{
	if (trim($keywords[$n])!="")
		{
		if (strpos($keywords[$n],":")!==false) {$count_special++;} else {$simple[]=trim($keywords[$n]);}
		if (substr($keywords[$n],0,8)=="country:") {$count_special--;$found_country=substr($keywords[$n],8);}
		if (substr($keywords[$n],0,5)=="year:") {$count_special--;$found_year=substr($keywords[$n],5);}
		if (substr($keywords[$n],0,6)=="month:") {$count_special--;$found_month=substr($keywords[$n],6);}
		if (substr($keywords[$n],0,4)=="day:") {$count_special--;$found_day=substr($keywords[$n],4);}
		}
	}
if (($count_special==0) && (($found_country!="") || ($found_year!="") || ($found_month!="") || ($found_day!=""))) {$setcountry=$found_country;$quicksearch=join(" ",trim_array($simple));}

?>
<div id="SearchBox">


<?php if (isset($anonymous_login) && ($username==$anonymous_login))
	{
	# For anonymous access, display the login panel
	?>
	<div id="SearchBoxPanel">
	<div class="SearchSpace">

	  <h2><?php echo $lang["login"]?></h2>

  
  <form id="form1" method="post" action="<?php echo $baseurl?>/login.php" target="_top">
  <div class="SearchItem"><?php echo $lang["username"]?><br/><input type="text" name="username" id="name" class="SearchWidth" /></div>
  
  <div class="SearchItem"><?php echo $lang["password"]?><br/><input type="password" name="password" id="name" class="SearchWidth" /></div>
  <div class="SearchItem"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["login"]?>&nbsp;&nbsp;" /></div>
  </form>
    <p><br/><?php if ($allow_account_request) { ?><a href="user_request.php">&gt; <?php echo $lang["nopassword"]?> </a></p><p><?php } ?>
  <a href="user_password.php">&gt; <?php echo $lang["forgottenpassword"]?></a></p>
	</div>
  
	</div>
	<div class="PanelShadow"></div><br />
	<?php
	}
?>


<?php if (checkperm("s") && (!isset($k) || $k=="")) { ?>
<div id="SearchBoxPanel">

<?php hook("searchbartoptoolbar"); ?>

<div class="SearchSpace">

<?php if (!hook("searchbarreplace")) { ?>

  <h2><?php echo $lang["simplesearch"]?></h2>
	<p><?php echo text("searchpanel")?></p>
	
	<form id="form1" method="get" action="<?php echo $baseurl?>/pages/search.php">

        <input id="ssearchbox" name="search" type="text" class="SearchWidth" value="<?php echo htmlspecialchars(stripslashes(@$quicksearch))?>">

<?php if ($autocomplete_search) { 
# Auto-complete search functionality
?>
<div id="autocomplete_search_choices" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter("ssearchbox", "autocomplete_search_choices", "<?php echo $baseurl?>/pages/ajax/autocomplete_search.php");
</script>

<?php } ?>


<?php
if (!$basic_simple_search)
	{
	?>
	<input type="hidden" name="resetrestypes" value="yes">
	<?php
	$rt=explode(",",@$restypes);
	$function="";
	$types=get_resource_types();for ($n=0;$n<count($types);$n++)
		{
		?><div class="tick"><input id="TickBox<?php echo $n?>" type="checkbox" name="resource<?php echo $types[$n]["ref"]?>" value="yes" <?php if (((count($rt)==1) && ($rt[0]=="")) || (in_array($types[$n]["ref"],$rt))) {?>checked="true"<?php } ?> />&nbsp;<?php echo $types[$n]["name"]?></div><?php	
		$function.="document.getElementById('TickBox" . $n . "').checked=true;";
		}
	?>
	<script language="Javascript">
	function ResetTicks() {<?php echo $function?>}
	</script>
	<?php
	}
?>
	<div class="SearchItem"><?php if (!$basic_simple_search) { ?><input name="Clear" type="button" value="&nbsp;&nbsp;<?php echo $lang["clearbutton"]?>&nbsp;&nbsp;" onClick="document.getElementById('ssearchbox').value='';
	<?php if ($country_search==true) {?>document.getElementById('basiccountry').value='';
	<?php } ?>
	document.getElementById('basicyear').value='';document.getElementById('basicmonth').value='';
	<?php if ($searchbyday) { ?>document.getElementById('basicday').value='';<?php } ?>
	ResetTicks();"/><?php } ?><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["searchbutton"]?>&nbsp;&nbsp;" /></div>
	<br />


	<?php
	if (!$basic_simple_search) {
	
	if ($country_search) { ?>
	<div class="SearchItem"><?php echo $lang["bycountry"]?><br />
	<?php
	$options=get_field_options(3);
	?>
	<select id="basiccountry" name="country" class="SearchWidth">
	  <option selected="selected" value=""><?php echo $lang["anycountry"]?></option>
	  <?php
	  for ($n=0;$n<count($options);$n++)
		{
		$c=i18n_get_translated($options[$n]);
		?><option <?php if (strtolower(str_replace(".","",$c))==$setcountry) { ?>selected<?php } ?>><?php echo $c?></option><?php
		}
	  ?>

	</select>
	</div>
	<?php } ?>
	
	<div class="SearchItem"><?php echo $lang["bydate"]?><br />
	<select id="basicyear" name="year" class="SearchWidth" style="width:70px;">
	  <option selected="selected" value=""><?php echo $lang["anyyear"]?></option>
	  <?php
	  $y=date("Y");
	  for ($n=$y;$n>=$minyear;$n--)
		{
		?><option <?php if ($n==$found_year) { ?>selected<?php } ?>><?php echo $n?></option><?php
		}
	  ?>
	</select>

	<?php if ($searchbyday) { ?><br /><?php } ?>

	<select id="basicmonth" name="month" class="SearchWidth" style="width:80px;">
	  <option selected="selected" value=""><?php echo $lang["anymonth"]?></option>
	  <?php
	  for ($n=1;$n<=12;$n++)
		{
		$m=str_pad($n,2,"0",STR_PAD_LEFT);
		?><option <?php if ($n==$found_month) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $lang["months"][$n-1]?></option><?php
		}
	  ?>

	</select>

	<?php if ($searchbyday) { ?>
	<select id="basicday" name="day" class="SearchWidth" style="width:70px;">
	  <option selected="selected" value=""><?php echo $lang["anyday"]?></option>
	  <?php
	  for ($n=1;$n<=31;$n++)
		{
		$m=str_pad($n,2,"0",STR_PAD_LEFT);
		?><option <?php if ($n==$found_day) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $m?></option><?php
		}
	  ?>
	</select>
	<?php } ?>

	</div>
	
	<!--				
	<div class="SearchItem">By Category<br />
	<select name="Country" class="SearchWidth">
	  <option selected="selected">All</option>
	  <option>Places</option>
		<option>People</option>
	  <option>Places</option>
		<option>People</option>
	  <option>Places</option>
	</select>
	</div>
	-->
	
	<div class="SearchItem"><?php echo $lang["resultsdisplay"]?><br />
	<select name="per_page" class="SearchWidth">
	  <option value="12" <?php if (getval("per_page",$default_perpage)==12) { ?>selected<?php } ?> >12 <?php echo $lang["perpage"]?></option>
	  <option value="24" <?php if (getval("per_page",$default_perpage)==24) { ?>selected<?php } ?> >24 <?php echo $lang["perpage"]?></option>
	  <option value="48" <?php if (getval("per_page",$default_perpage)==48) { ?>selected<?php } ?> >48 <?php echo $lang["perpage"]?></option>
	  <option value="72" <?php if (getval("per_page",$default_perpage)==72) { ?>selected<?php } ?> >72 <?php echo $lang["perpage"]?></option>
   	  <option value="120" <?php if (getval("per_page",$default_perpage)==120) { ?>selected<?php } ?> >120 <?php echo $lang["perpage"]?></option>
  	  <option value="240" <?php if (getval("per_page",$default_perpage)==240) { ?>selected<?php } ?> >240 <?php echo $lang["perpage"]?></option>
	</select>
	</div>

	  <div class="tick"><label><input name="display" type="radio" value="thumbs" <?php if (getval("display","thumbs")=="thumbs") { ?>checked="checked"<?php } ?> />&nbsp;<?php echo $lang["largethumbs"]?></label>
	  <?php if ($smallthumbs==true){?><label><input name="display" type="radio" value="smallthumbs" <?php if (getval("display","smallthumbs")=="smallthumbs") { ?>checked="checked"<?php } ?> />&nbsp;<?php echo $lang["smallthumbs"]?></label><?php } ?>
	  </div>
	  <div class="tick"><label><input type="radio" name="display" value="list" <?php if (getval("display","")=="list") { ?>checked="checked"<?php } ?> />&nbsp;<?php echo $lang["list"]?></label></div>

	<?php } ?>
			
  </form>
	
  <p><br /><a href="<?php echo $baseurl?>/pages/search_advanced.php">&gt; <?php echo $lang["gotoadvancedsearch"]?></a></p>
  <?php if ($view_new_material) { ?><p><a href="<?php echo $baseurl?>/pages/search.php?search=<?php echo urlencode("!last1000")?>">&gt; <?php echo $lang["viewnewmaterial"]?></a></p><?php } ?>
	</div>
	
	<?php } ?> <!-- END of Searchbarreplace hook -->
	</div>
<?php } ?>

	<div class="PanelShadow"></div>
	
	
	<?php if (($research_request) && (checkperm("q"))) { ?>
	<div id="ResearchBoxPanel">
  <div class="SearchSpace">
  <h2><?php echo $lang["researchrequest"]?></h2>
	<p><?php echo text("researchrequest")?></p>
	<div class="HorizontalWhiteNav"><a href="<?php echo $baseurl?>/pages/research_request.php">&gt; <?php echo $lang["researchrequestservice"]?></a></div>
	</div><br />
	</div>
	<div class="PanelShadow"></div>
	<?php } ?>

	<?php if ($frameless_collections && !checkperm("b"))
		{ 
		# Support for frameless collections.

		# Ensure collections functions loaded.
		include_once dirname(__FILE__)."/collections_functions.php";

		# Load collection info.
		# If $usercollection is not set then this is an external user. Extract the collection ID from the URL.
		if (!isset($usercollection)) {$usercollection=getval("collection",str_replace("!collection","",getval("search","")));}
		$cinfo=get_collection($usercollection);
		
		# Requires feedback?
		$feedback=$cinfo["request_feedback"];
		?>
		<div id="ResearchBoxPanel">
		  <div class="SearchSpace">
		  <h2><?php echo $lang["mycollections"]?></h2>
	
		<div id="CollectionFramelessCount">
		
		<!--Collection Dropdown (Empty placeholder - this content is replaced by an AJAX call -->	
		<div id="CollectionFramelessDropTitle"><?php echo $lang["currentcollection"]?>:&nbsp;</div>				
		<div id="CollectionFramelessDrop">
		<select id="colselect" name="collection" class="SearchWidth">
		</select>
		</div>
		<strong>?</strong>&nbsp;<?php echo $lang["resourcesincollection"]?>
		<!-- End of empty copy -->
		
		</div>
		
		<!--Menu-->	
		<div id="CollectionFramelessNav">
		  <ul>
		  	<?php 
			# If this collection is (fully) editable, then display an extra edit all link
			if (allow_multi_edit($usercollection)) { ?>
			<li class="clearerleft"><a href="<?php echo $baseurl_short?>pages/search.php?search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo isset($k)?$k:""?>">&gt; <?php echo $lang["viewall"]?></a></li>
			<li><a href="<?php echo $baseurl_short?>pages/edit.php?collection=<?php echo $usercollection?>">&gt; <?php echo $lang["editall"]?></a></li>
			</li>    
			<?php } else { ?>
			<li><a href="<?php echo $baseurl_short?>pages/search.php?search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo isset($k)?$k:""?>">&gt; <?php echo $lang["viewall"]?></a></li>
			<?php } ?>
			
			<?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
			<?php if (checkperm("s")) { ?>
			<?php if ($allow_share && (checkperm("v") || checkperm("g"))) { ?><li><a href="<?php echo $baseurl_short?>pages/collection_share.php?ref=<?php echo $usercollection?>">&gt; <?php echo $lang["share"]?></a></li><?php } ?>
			
			<?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a href="<?php echo $baseurl_short?>pages/collection_edit.php?ref=<?php echo $usercollection?>">&gt;&nbsp;<?php echo $allow_share?$lang["edit"]:$lang["editcollection"]?></a></li><?php } ?>
			<?php } ?>
		
			<?php if ($feedback) {?><li><a  href="<?php echo $baseurl_short?>pages/collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["sendfeedback"]?></a></li><?php } ?>
			
			<?php } else {
			$research=sql_value("select ref value from research_request where collection='$usercollection'",0);
			?>
			<li><a href="<?php echo $baseurl_short?>pages/team_research_edit.php?ref=<?php echo $research?>">&gt;<?php echo $lang["editresearchrequests"]?></a></li>    
			<li><a href="<?php echo $baseurl_short?>pages/team_research.php">&gt; <?php echo $lang["manageresearchrequests"]?></a></li>    
			<?php } ?>
			
			<?php if (isset($zipcommand)) { ?>
			<li><a href="<?php echo $baseurl_short?>pages/collection_download.php?collection=<?php echo $usercollection?>">&gt; <?php echo $lang["zipall"]?></a></li>
			
			<li><a href="<?php echo $baseurl_short?>pages/collection_manage.php">&gt; <?php echo $lang["managemycollections"]?></a></li>
			<?php } ?>
		  </ul>
		</div>

		<div class="clearer"> </div>
	  

		</div>
		</div>
		<div class="PanelShadow"></div>
		
		<script type="text/javascript">
		UpdateCollectionDisplay();
		</script>
		<?php
		}
	?>

<?php hook("searchbarbottomtoolbar"); ?>
	
</div>

