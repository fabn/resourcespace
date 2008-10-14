<?
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


<? if (isset($anonymous_login) && ($username==$anonymous_login))
	{
	# For anonymous access, display the login panel
	?>
	<div id="SearchBoxPanel">
	<div class="SearchSpace">

	  <h2><?=$lang["login"]?></h2>

  
  <form id="form1" method="post" action="<?=$baseurl?>/pages/login.php" target="_top">
  <div class="SearchItem"><?=$lang["username"]?><br/><input type="text" name="username" id="name" class="SearchWidth" /></div>
  
  <div class="SearchItem"><?=$lang["password"]?><br/><input type="password" name="password" id="name" class="SearchWidth" /></div>
  <div class="SearchItem"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["login"]?>&nbsp;&nbsp;" /></div>
  </form>
    <p><br/><? if ($allow_account_request) { ?><a href="user_request.php">&gt; <?=$lang["nopassword"]?> </a></p><p><? } ?>
  <a href="user_password.php">&gt; <?=$lang["forgottenpassword"]?></a></p>
	</div>
  
	</div>
	<div class="PanelShadow"></div><br />
	<?
	}
?>


<? if (checkperm("s") && (!isset($k) || $k=="")) { ?>
<div id="SearchBoxPanel">

<? hook("searchbartoptoolbar"); ?>

<div class="SearchSpace">

<? if (!hook("searchbarreplace")) { ?>

  <h2><?=$lang["simplesearch"]?></h2>
	<p><?=text("searchpanel")?></p>
	
	<form id="form1" method="get" action="<?=$baseurl?>/pages/search.php">

        <input id="ssearchbox" name="search" type="text" class="SearchWidth" value="<?=htmlspecialchars(stripslashes(@$quicksearch))?>">

<? if ($autocomplete_search) { 
# Auto-complete search functionality
?>
<div id="autocomplete_search_choices" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter("ssearchbox", "autocomplete_search_choices", "autocomplete_search.php");
</script>

<? } ?>


<?
if (!$basic_simple_search)
	{
	?>
	<input type="hidden" name="resetrestypes" value="yes">
	<?
	$rt=explode(",",@$restypes);
	$function="";
	$types=get_resource_types();for ($n=0;$n<count($types);$n++)
		{
		?><div class="tick"><input id="TickBox<?=$n?>" type="checkbox" name="resource<?=$types[$n]["ref"]?>" value="yes" <? if (((count($rt)==1) && ($rt[0]=="")) || (in_array($types[$n]["ref"],$rt))) {?>checked="true"<?}?> />&nbsp;<?=$types[$n]["name"]?></div><?	
		$function.="document.getElementById('TickBox" . $n . "').checked=true;";
		}
	?>
	<script language="Javascript">
	function ResetTicks() {<?=$function?>}
	</script>
	<?
	}
?>
	<div class="SearchItem"><? if (!$basic_simple_search) { ?><input name="Clear" type="button" value="&nbsp;&nbsp;<?=$lang["clearbutton"]?>&nbsp;&nbsp;" onClick="document.getElementById('ssearchbox').value='';
	<? if ($country_search==true) {?>document.getElementById('basiccountry').value='';
	<? } ?>
	document.getElementById('basicyear').value='';document.getElementById('basicmonth').value='';
	<? if ($searchbyday) { ?>document.getElementById('basicday').value='';<? } ?>
	ResetTicks();"/><? } ?><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["searchbutton"]?>&nbsp;&nbsp;" /></div>
	<br />


	<?
	if (!$basic_simple_search) {
	
	if ($country_search) { ?>
	<div class="SearchItem"><?=$lang["bycountry"]?><br />
	<?
	$options=get_field_options(3);
	?>
	<select id="basiccountry" name="country" class="SearchWidth">
	  <option selected="selected" value=""><?=$lang["anycountry"]?></option>
	  <?
	  for ($n=0;$n<count($options);$n++)
		{
		$c=i18n_get_translated($options[$n]);
		?><option <? if (strtolower(str_replace(".","",$c))==$setcountry) { ?>selected<? } ?>><?=$c?></option><?
		}
	  ?>

	</select>
	</div>
	<? } ?>
	
	<div class="SearchItem"><?=$lang["bydate"]?><br />
	<select id="basicyear" name="year" class="SearchWidth" style="width:70px;">
	  <option selected="selected" value=""><?=$lang["anyyear"]?></option>
	  <?
	  $y=date("Y");
	  for ($n=$y;$n>=$minyear;$n--)
		{
		?><option <? if ($n==$found_year) { ?>selected<? } ?>><?=$n?></option><?
		}
	  ?>
	</select>

	<? if ($searchbyday) { ?><br /><? } ?>

	<select id="basicmonth" name="month" class="SearchWidth" style="width:80px;">
	  <option selected="selected" value=""><?=$lang["anymonth"]?></option>
	  <?
	  for ($n=1;$n<=12;$n++)
		{
		$m=str_pad($n,2,"0",STR_PAD_LEFT);
		?><option <? if ($n==$found_month) { ?>selected<? } ?> value="<?=$m?>"><?=$lang["months"][$n-1]?></option><?
		}
	  ?>

	</select>

	<? if ($searchbyday) { ?>
	<select id="basicday" name="day" class="SearchWidth" style="width:70px;">
	  <option selected="selected" value=""><?=$lang["anyday"]?></option>
	  <?
	  for ($n=1;$n<=31;$n++)
		{
		$m=str_pad($n,2,"0",STR_PAD_LEFT);
		?><option <? if ($n==$found_day) { ?>selected<? } ?> value="<?=$m?>"><?=$m?></option><?
		}
	  ?>
	</select>
	<? } ?>

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
	
	<div class="SearchItem"><?=$lang["resultsdisplay"]?><br />
	<select name="per_page" class="SearchWidth">
	  <option value="12" <? if (getval("per_page",$default_perpage)==12) { ?>selected<? } ?> >12 <?=$lang["perpage"]?></option>
	  <option value="24" <? if (getval("per_page",$default_perpage)==24) { ?>selected<? } ?> >24 <?=$lang["perpage"]?></option>
	  <option value="48" <? if (getval("per_page",$default_perpage)==48) { ?>selected<? } ?> >48 <?=$lang["perpage"]?></option>
	  <option value="72" <? if (getval("per_page",$default_perpage)==72) { ?>selected<? } ?> >72 <?=$lang["perpage"]?></option>
	</select>
	</div>

	  <div class="tick"><label><input name="display" type="radio" value="thumbs" <? if (getval("display","thumbs")=="thumbs") { ?>checked="checked"<? } ?> />&nbsp;<?=$lang["largethumbs"]?></label>
	  <? if ($smallthumbs==true){?><label><input name="display" type="radio" value="smallthumbs" <? if (getval("display","smallthumbs")=="smallthumbs") { ?>checked="checked"<? } ?> />&nbsp;<?=$lang["smallthumbs"]?></label><?}?>
	  </div>
	  <div class="tick"><label><input type="radio" name="display" value="list" <? if (getval("display","")=="list") { ?>checked="checked"<? } ?> />&nbsp;<?=$lang["list"]?></label></div>

	<? } ?>
			
  </form>
	
  <p><br /><a href="<?=$baseurl?>/pages/search_advanced.php">&gt; <?=$lang["gotoadvancedsearch"]?></a></p>
  <? if ($view_new_material) { ?><p><a href="<?=$baseurl?>/pages/search.php?search=<?=urlencode("!last1000")?>">&gt; <?=$lang["viewnewmaterial"]?></a></p><? } ?>
	</div>
	
	<? } ?> <!-- END of Searchbarreplace hook -->
	</div>
<? } ?>

	<div class="PanelShadow"></div>
	
	
	<? if (($research_request) && (checkperm("q"))) { ?>
	<div id="ResearchBoxPanel">
  <div class="SearchSpace">
  <h2><?=$lang["researchrequest"]?></h2>
	<p><?=text("researchrequest")?></p>
	<div class="HorizontalWhiteNav"><a href="<?=$baseurl?>/pages/research_request.php">&gt; <?=$lang["researchrequestservice"]?></a></div>
	</div><br />
	</div>
	<div class="PanelShadow"></div>
	<? } ?>

	<? if ($frameless_collections && !checkperm("b"))
		{ 
		# Support for frameless collections.

		# Ensure collections functions loaded.
		include_once dirname(__FILE__)."/collections_functions.php";

		# Load collection info.
		# If $usercollection is not set then this is an external user. Extract the collection ID from the URL.
		if (!isset($usercollection)) {$usercollection=str_replace("!collection","",getval("search",""));}
		$cinfo=get_collection($usercollection);
		
		# Requires feedback?
		$feedback=$cinfo["request_feedback"];
		?>
		<div id="ResearchBoxPanel">
		  <div class="SearchSpace">
		  <h2><?=$lang["mycollections"]?></h2>
	
		<div id="CollectionFramelessCount">
		
		<!--Collection Dropdown (Empty placeholder - this content is replaced by an AJAX call -->	
		<div id="CollectionFramelessDropTitle"><?=$lang["currentcollection"]?>:&nbsp;</div>				
		<div id="CollectionFramelessDrop">
		<select id="colselect" name="collection" class="SearchWidth">
		</select>
		</div>
		<strong>?</strong>&nbsp;<?=$lang["resourcesincollection"]?>
		<!-- End of empty copy -->
		
		</div>
		
		<!--Menu-->	
		<div id="CollectionFramelessNav">
		  <ul>
		  	<? 
			# If this collection is (fully) editable, then display an extra edit all link
			if (allow_multi_edit($usercollection)) { ?>
			<li class="clearerleft"><a href="search.php?search=<?=urlencode("!collection" . $usercollection)?>&k=<?=isset($k)?$k:""?>">&gt; <?=$lang["viewall"]?></a></li>
			<li><a href="edit.php?collection=<?=$usercollection?>">&gt; <?=$lang["editall"]?></a></li>
			</li>    
			<? } else { ?>
			<li><a href="search.php?search=<?=urlencode("!collection" . $usercollection)?>&k=<?=isset($k)?$k:""?>">&gt; <?=$lang["viewall"]?></a></li>
			<? } ?>
			
			<? if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
			<? if (checkperm("s")) { ?>
			<? if ($allow_share && (checkperm("v") || checkperm("g"))) { ?><li><a href="collection_share.php?ref=<?=$usercollection?>">&gt; <?=$lang["share"]?></a></li><? } ?>
			
			<? if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a href="collection_edit.php?ref=<?=$usercollection?>">&gt;&nbsp;<?=$allow_share?$lang["edit"]:$lang["editcollection"]?></a></li><? } ?>
		
			<? if ($feedback) {?><li><a  href="collection_feedback.php?collection=<?=$usercollection?>&k=<?=$k?>">&gt;&nbsp;<?=$lang["sendfeedback"]?></a></li><? } ?>
			
			<? } ?>
			<? } else {
			$research=sql_value("select ref value from research_request where collection='$usercollection'",0);
			?>
			<li><a href="team_research_edit.php?ref=<?=$research?>">&gt;<?=$lang["editresearchrequests"]?></a></li>    
			<li><a href="team_research.php">&gt; <?=$lang["manageresearchrequests"]?></a></li>    
			<? } ?>
			
			<? if (isset($zipcommand)) { ?>
			<li><a href="collection_download.php?collection=<?=$usercollection?>">&gt; <?=$lang["zipall"]?></a></li>
			
			<li><a href="collection_manage.php">&gt; <?=$lang["managemycollections"]?></a></li>
			<? } ?>
		  </ul>
		</div>

		<div class="clearer"> </div>
	  

		</div>
		</div>
		<div class="PanelShadow"></div>
		
		<script type="text/javascript">
		UpdateCollectionDisplay();
		</script>
		<?
		}
	?>

<? hook("searchbarbottomtoolbar"); ?>
	
</div>

