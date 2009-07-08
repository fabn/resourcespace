<?php
# pull values from cookies if necessary, for non-search pages where this info hasn't been submitted
if (!isset($restypes)) {$restypes=@$_COOKIE["restypes"];}
if (!isset($search) || ((strpos($search,"!")!==false))) {$quicksearch=@$_COOKIE["search"];} else {$quicksearch=$search;}

# Load the basic search fields, so we know which to strip from the search string
$fields=get_simple_search_fields();
$simple_fields=array();
for ($n=0;$n<count($fields);$n++)
	{
	$simple_fields[]=$fields[$n]["name"];
	}
# Also strip date related fields.
$simple_fields[]="year";$simple_fields[]="month";$simple_fields[]="day";

# Process all keywords, putting set fieldname/value pairs into an associative array ready for setting later.
# Also build a quicksearch string.
$keywords=split_keywords($quicksearch);
$set_fields=array();
$simple=array();
for ($n=0;$n<count($keywords);$n++)
	{
	if (trim($keywords[$n])!="")
		{
		if (strpos($keywords[$n],":")!==false)
			{
			$s=explode(":",$keywords[$n]);
			if (isset($set_fields[$s[0]])){$set_fields[$s[0]].=" ".$s[1];}
			else {$set_fields[$s[0]]=$s[1];}
			if (!in_array($s[0],$simple_fields)) {$simple[]=trim($keywords[$n]);}
			}
		else
			{
			# Plain text (non field) search.
			$simple[]=trim($keywords[$n]);
			}
		}
	}

# Set the text search box to the stripped value.
$quicksearch=join(" ",trim_array($simple));

# Set the predefined date fields
$found_year="";if (isset($set_fields["year"])) {$found_year=$set_fields["year"];}
$found_month="";if (isset($set_fields["month"])) {$found_month=$set_fields["month"];}
$found_day="";if (isset($set_fields["day"])) {$found_day=$set_fields["day"];}

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

        <input id="ssearchbox" <?php if ($hide_main_simple_search){?>type="hidden"<?php } ?> name="search" type="text" class="SearchWidth" value="<?php echo htmlspecialchars(stripslashes(@$quicksearch))?>">

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
	$clear_function="";
	$types=get_resource_types();for ($n=0;$n<count($types);$n++)
		{
		?><div class="tick"><input id="TickBox<?php echo $n?>" type="checkbox" name="resource<?php echo $types[$n]["ref"]?>" value="yes" <?php if (((count($rt)==1) && ($rt[0]=="")) || (in_array($types[$n]["ref"],$rt))) {?>checked="true"<?php } ?> />&nbsp;<?php echo $types[$n]["name"]?></div><?php	
		$clear_function.="document.getElementById('TickBox" . $n . "').checked=true;";
		}
	}
?>
	<div class="SearchItem"><?php if (!$basic_simple_search) { ?><input name="Clear" type="button" value="&nbsp;&nbsp;<?php echo $lang["clearbutton"]?>&nbsp;&nbsp;" onClick="document.getElementById('ssearchbox').value='';
	document.getElementById('basicyear').value='';document.getElementById('basicmonth').value='';
	<?php if ($searchbyday) { ?>document.getElementById('basicday').value='';<?php } ?>
	ResetTicks();"/><?php } ?><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["searchbutton"]?>&nbsp;&nbsp;" /></div>
	<br />


	<?php
	if (!$basic_simple_search) {
	// Include simple search items (if any)
	$optionfields=array();
	for ($n=0;$n<count($fields);$n++)
		{
		?>
		<div class="SearchItem"><?php echo i18n_get_translated($fields[$n]["title"])?><br />
		<?php
		
		$value=""; # to do, fetch set value.
		if (isset($set_fields[$fields[$n]["name"]])) {$value=$set_fields[$fields[$n]["name"]];}
		
		#hook to modify field type in special case
		if(hook("modifyfieldtype")){$fields[$n]["type"]=hook("modifyfieldtype");}
		
		switch ($fields[$n]["type"])
			{
			case 0: # -------- Text boxes?><?php
			case 1:
			case 5:
			?>	
			<input class="SearchWidth" type=text name="field_<?php echo $fields[$n]["name"]?>" id="field_<?php echo $fields[$n]["name"]?>" value="<?php echo htmlspecialchars($value)?>"><?php
			if ($autocomplete_search) { 
				# Auto-complete search functionality
				?></div>
				<div id="autocomplete_search_choices_<?php echo $fields[$n]["name"]?>" class="autocomplete"></div>
				<script type="text/javascript">
				new Ajax.Autocompleter("field_<?php echo $fields[$n]["name"]?>", "autocomplete_search_choices_<?php echo $fields[$n]["name"]?>", "<?php echo $baseurl?>/pages/ajax/autocomplete_search.php?field=<?php echo $fields[$n]["name"]?>&fieldref=<?php echo $fields[$n]["ref"]?>");
				</script>
				<div class="SearchItem">
			<?php } 
			# Add to the clear function so clicking 'clear' clears this box.
			$clear_function.="document.getElementById('field_" . $fields[$n]["name"] . "').value='';";
			
			break;
		
			case 2:
			case 3:
			// Dropdown and checkbox types - display a list for each
			$options=get_field_options($fields[$n]["ref"]);
			if (hook("adjustdropdownoptions")){$options=hook("adjustdropdownoptions");}
			$optionfields[]=$fields[$n]["name"]; # Append to the option fields array, used by the AJAX dropdown filtering
			?>
			<select id="field_<?php echo $fields[$n]["name"]?>" name="field_<?php echo $fields[$n]["name"]?>" class="SearchWidth" onChange="FilterBasicSearchOptions();">
			  <option selected="selected" value="">&nbsp;</option>
			  <?php
			  for ($m=0;$m<count($options);$m++)
				{
				$c=i18n_get_translated($options[$m]);
				if ($c!="")
					{
					?><option <?php if (cleanse_string($c,false)==$value) { ?>selected<?php } ?>><?php echo $c?></option><?php
					}
				}
			  ?>
	  		</select>
			<?php

			# Add to the clear function so clicking 'clear' clears this box.
			$clear_function.="document.getElementById('field_" . $fields[$n]["name"] . "').selectedIndex=0;";
			break;
			
			case 4:
			case 6:
			// Date types
			$d_year='';$d_month='';$d_day='';
			$s=explode(" ",$value);
			if (count($s)>=1) {$d_year=$s[0];}
			if (count($s)>=2) {$d_month=$s[1];}
			if (count($s)>=3) {$d_day=$s[2];}
			?>
			<select id="field_<?php echo $fields[$n]["name"]?>_year" name="field_<?php echo $fields[$n]["name"]?>_year" <?php if (!$searchbyday) { ?>style="width:60px;"<?php } ?>>
			  <option selected="selected" value=""><?php echo $lang["anyyear"]?></option>
			  <?php
			  $y=date("Y");
			  for ($d=$y;$d>=$minyear;$d--)
				{
				?><option <?php if ($d==$d_year) { ?>selected<?php } ?>><?php echo $d?></option><?php
				}
			  ?>
			</select>
				
			<select id="field_<?php echo $fields[$n]["name"]?>_month" name="field_<?php echo $fields[$n]["name"]?>_month" class="SearchWidth" style="width:45px;">
			  <option selected="selected" value=""><?php echo $lang["anymonth"]?></option>
			  <?php
			  for ($d=1;$d<=12;$d++)
				{
				$m=str_pad($d,2,"0",STR_PAD_LEFT);
				?><option <?php if ($d==$d_month) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $lang["months"][$d-1]?></option><?php
				}
			  ?>		
			</select>
		
			<select id="field_<?php echo $fields[$n]["name"]?>_day" name="field_<?php echo $fields[$n]["name"]?>_day" class="SearchWidth" style="width:45px;">
			  <option selected="selected" value=""><?php echo $lang["anyday"]?></option>
			  <?php
			  for ($d=1;$d<=31;$d++)
				{
				$m=str_pad($d,2,"0",STR_PAD_LEFT);
				?><option <?php if ($d==$d_day) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $m?></option><?php
				}
			  ?>
			</select>
			<?php
			# Add to the clear function so clicking 'clear' clears this box.
			$clear_function.="
				document.getElementById('field_" . $fields[$n]["name"] . "_year').selectedIndex=0;
				document.getElementById('field_" . $fields[$n]["name"] . "_month').selectedIndex=0;
				document.getElementById('field_" . $fields[$n]["name"] . "_day').selectedIndex=0;
				";
			break;
			
			}
		?>
		</div>	
		<?php		
		}
	?>
	<script type="text/javascript">
	function FilterBasicSearchOptions()
		{
		<?php
		// When using more than one dropdown field, automatically filter field options using AJAX
		// in a attempt to avoid blank results sets through excessive selection of filters.
		if ($simple_search_dropdown_filtering && count($optionfields)>1) { ?>
		var Filter="";
		<?php for ($n=0;$n<count($optionfields);$n++)
			{
			?>
			Filter += "<?php if ($n>0) {echo ";";} ?><?php echo $optionfields[$n]?>:" + $('field_<?php echo $optionfields[$n]?>').value;
			<?php
			} ?>
		// Send AJAX post request.
		new Ajax.Request('<?php echo $baseurl_short?>pages/ajax/filter_basic_search_options.php?filter=' + encodeURIComponent(Filter), { method: 'post',onSuccess: function(transport) {eval(transport.responseText);} });
		<?php } ?>
		}
	</script>
		
	<div class="SearchItem"><?php echo $lang["bydate"]?><br />
	<select id="basicyear" name="year" class="SearchWidth" <?php if (!$searchbyday) { ?>style="width:70px;"<?php } ?>>
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

	<script language="Javascript">
	function ResetTicks() {<?php echo $clear_function?>}
	</script>
	
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
	
	<?php } ?>
			
  </form>

  <?php if (! $advancedsearch_disabled) { ?><p><br /><a href="<?php echo $baseurl?>/pages/search_advanced.php">&gt; <?php echo $lang["gotoadvancedsearch"]?></a></p><?php } ?>
  <?php if ($view_new_material) { ?><p><a href="<?php echo $baseurl?>/pages/search.php?search=<?php echo urlencode("!last1000")?>">&gt; <?php echo $lang["viewnewmaterial"]?></a></p><?php } ?>
	</div>
	
	<?php } ?> <!-- END of Searchbarreplace hook -->
	</div>
	<div class="PanelShadow"></div>
<?php } ?>	
	
	<?php if (($research_request) && (!isset($k) || $k=="") && (checkperm("q"))) { ?>
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
		
		<div id="CollectionFrameless"></div>
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

