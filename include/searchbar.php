<?php

# pull values from cookies if necessary, for non-search pages where this info hasn't been submitted
if (!isset($restypes)) {$restypes=@$_COOKIE["restypes"];}
if (!isset($search) || ((strpos($search,"!")!==false))) {$quicksearch=(isset($_COOKIE["search"])?$_COOKIE["search"]:"");} else {$quicksearch=$search;}

include_once("search_functions.php");

# Load the basic search fields, so we know which to strip from the search string
$fields=get_simple_search_fields();
$simple_fields=array();
for ($n=0;$n<count($fields);$n++)
	{
	$simple_fields[]=$fields[$n]["name"];
	}
# Also strip date related fields.
$simple_fields[]="year";$simple_fields[]="month";$simple_fields[]="day";
hook("simplesearch_stripsimplefields");

# Process all keywords, putting set fieldname/value pairs into an associative array ready for setting later.
# Also build a quicksearch string.

# Recognise a quoted search, which is a search for an exact string
$quoted_string=false;
if (substr($quicksearch,0,1)=="\"" && substr($quicksearch,-1,1)=="\"") {$quoted_string=true;$quicksearch=substr($quicksearch,1,-1);}

$quicksearch=refine_searchstring($quicksearch);
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

# Add the quotes back, if a quoted string
if ($quoted_string) {$quicksearch="\"" . $quicksearch . "\"";}

# Set the predefined date fields
$found_year="";if (isset($set_fields["year"])) {$found_year=$set_fields["year"];}
$found_month="";if (isset($set_fields["month"])) {$found_month=$set_fields["month"];}
$found_day="";if (isset($set_fields["day"])) {$found_day=$set_fields["day"];}


if ($display_user_rating_stars && $star_search){ ?>

	<script type="text/javascript">
	function StarSearchRatingDisplay(rating,hiclass)
		{
		for (var n=1;n<=5;n++)
			{
			jQuery('#RatingStar-'+n).removeClass('StarEmpty');
			jQuery('#RatingStar-'+n).removeClass('StarCurrent');
			jQuery('#RatingStar-'+n).removeClass('StarSelect');
			if (n<=rating)
				{
				jQuery('#RatingStar-'+n).addClass(hiclass);
				}
			else
				{
				jQuery('#RatingStar-'+n).addClass('StarEmpty');
				}
			}
		}	
	</script>
<?php } ?>

<div id="SearchBox">





<?php if (checkperm("s") && (!isset($k) || $k=="")) { ?>
<div id="SearchBoxPanel">

<?php hook("searchbartoptoolbar"); ?>

<div class="SearchSpace" <?php if (!$basic_simple_search){?>id="searchspace"<?php } ?>>

<?php if (!hook("searchbarreplace")) { ?>

  <h2><?php echo $lang["simplesearch"]?></h2>
	<p><?php echo text("searchpanel")?></p>
	
	<form id="form1" method="get" action="<?php echo $baseurl?>/pages/search.php">
	<?php if (!hook("replacesearchbox")){ ?>
		<div class="ui-widget">
        <input id="ssearchbox" <?php if ($hide_main_simple_search){?>type="hidden"<?php } ?> name="search" type="text" class="SearchWidth" value="<?php echo htmlspecialchars(stripslashes(@$quicksearch))?>">
        </div>
	<?php } ?>
<?php if ($autocomplete_search) { 
# Auto-complete search functionality
?>
<script type="text/javascript">
jQuery('#ssearchbox').autocomplete( { source: "<?php echo $baseurl?>/pages/ajax/autocomplete_search.php" } );
</script>

<?php } ?>


<?php
if (!$basic_simple_search)
	{
	# Load resource types.
	$types=get_resource_types();
	
	# More than 5 types? Always display the 'select all' option.
	if (count($types)>5) {$searchbar_selectall=true;}
	
	?>
	<input type="hidden" name="resetrestypes" value="yes">
	<div id="searchbarrt" <?php hook("searchbarrtdiv");?>>
	<?php if ($searchbar_selectall) { ?>
	<script type="text/javascript">
	function resetTickAll(){
		var checkcount=0;
		// set tickall to false, then check if it should be set to true.
		jQuery('#tickall').attr('checked',false);
		var tickboxes=jQuery('#form1 .tickbox');
		console.log(tickboxes);
			jQuery(tickboxes).each(function (elem) {
                if( tickboxes[elem].checked){checkcount=checkcount+1;}
            });
		if (checkcount==tickboxes.length){jQuery('#tickall').attr('checked',true);}	
		console.log(checkcount);
	}
	</script>
	<div class="tick"><input type='checkbox' id='tickall' name='tickall' checked="true" onclick='jQuery("#form1 :checkbox").each (function(index,Element) {jQuery(Element).attr("checked",(jQuery("#tickall").attr("checked")=="checked"));}); HideInapplicableSimpleSearchFields(true); '/>&nbsp;<?php echo $lang['all']?></div>
	<?php }?>
	<?php
	$rt=explode(",",@$restypes);
	$clear_function="";
	for ($n=0;$n<count($types);$n++)
		{
		?><div class="tick"><?php if ($searchbar_selectall){ ?>&nbsp;&nbsp;<?php } ?><input class="tickbox" id="TickBox<?php echo $types[$n]["ref"]?>" type="checkbox" name="resource<?php echo $types[$n]["ref"]?>" value="yes" <?php if (((count($rt)==1) && ($rt[0]=="")) || (in_array($types[$n]["ref"],$rt))) {?>checked="true"<?php } ?> onClick="HideInapplicableSimpleSearchFields(true);<?php if ($searchbar_selectall){?>resetTickAll();<?php } ?>"/>&nbsp;<?php echo $types[$n]["name"]?></div><?php	
		$clear_function.="document.getElementById('TickBox" . $types[$n]["ref"] . "').checked=true;";
		if ($searchbar_selectall) {$clear_function.="resetTickAll();";}
		}
	}
	?>	
	<?php if ($searchbar_selectall){?><script type="text/javascript">resetTickAll();</script><?php }?>
	<?php if (!$basic_simple_search) {?>
	</div>
	<?php }
	
	hook("searchfiltertop");
	?>

	<?php $searchbuttons="<div class=\"SearchItem\">";
	if (!$basic_simple_search) { $searchbuttons.="<input name=\"Clear\" class=\"searchbutton\" type=\"button\" value=\"&nbsp;&nbsp;".$lang['clearbutton']."&nbsp;&nbsp;\" onClick=\"document.getElementById('ssearchbox').value=''; document.getElementById('basicyear').value='';document.getElementById('basicmonth').value='';";
	if ($searchbyday) { $searchbuttons.="document.getElementById('basicday').value='';"; } 
	if ($display_user_rating_stars && $star_search) { $searchbuttons.="StarSearchRatingDisplay(0,'StarCurrent');document.getElementById('starsearch').value='';window['StarSearchRatingDone']=true;"; } 
	$searchbuttons.="ResetTicks();\"/>"; } 
	$searchbuttons.="<input name=\"Submit\" class=\"searchbutton\" type=\"submit\" value=\"&nbsp;&nbsp;". $lang['searchbutton']."&nbsp;&nbsp;\" /></div>";?>
	
	<?php if (!$searchbar_buttons_at_bottom){ echo $searchbuttons."<br/>"; } ?>

	<?php
	if (!$basic_simple_search) {
	
	// Include simple search items (if any)
	$optionfields=array();
	for ($n=0;$n<count($fields);$n++)
		{
		hook("modifysearchfieldtitle");?>
		<div class="SearchItem" id="simplesearch_<?php echo $fields[$n]["ref"] ?>"><?php echo $fields[$n]["title"]?><br />
		<?php
		
		$value=""; # to do, fetch set value.
		if (isset($set_fields[$fields[$n]["name"]])) {$value=$set_fields[$fields[$n]["name"]];}
		
	#hook to modify field type in special case. Returning zero (to get a standard text box) doesn't work, so return 1 for type 0, 2 for type 1, etc.
	if(hook("modifyfieldtype")){$fields[$n]["type"]=hook("modifyfieldtype")-1;}
		
		switch ($fields[$n]["type"])
			{
			case 0: # -------- Text boxes?><?php
			case 1:
			case 5:
			case 9:
			?>	
			<input class="SearchWidth" type=text name="field_<?php echo $fields[$n]["name"]?>" id="field_<?php echo $fields[$n]["name"]?>" value="<?php echo htmlspecialchars($value)?>"><?php
			if ($autocomplete_search) { 
				# Auto-complete search functionality
				?></div>
				<script type="text/javascript">
				jQuery("#field_<?php echo $fields[$n]["name"]?>").autocomplete( { source: "<?php echo $baseurl?>/pages/ajax/autocomplete_search.php?field=<?php echo $fields[$n]["name"]?>&fieldref=<?php echo $fields[$n]["ref"]?>"} );
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
			
			$adjusted_dropdownoptions=hook("adjustdropdownoptions");
			if ($adjusted_dropdownoptions){$options=$adjusted_dropdownoptions;}
			
			$optionfields[]=$fields[$n]["name"]; # Append to the option fields array, used by the AJAX dropdown filtering
			?>
			<select id="field_<?php echo $fields[$n]["name"]?>" name="field_drop_<?php echo $fields[$n]["name"]?>" class="SearchWidth" onChange="FilterBasicSearchOptions('<?php echo $fields[$n]["name"]?>',<?php echo $fields[$n]["resource_type"]?>);">
			  <option selected="selected" value="">&nbsp;</option>
			  <?php
			  for ($m=0;$m<count($options);$m++)
				{
				$c=i18n_get_translated($options[$m]);
				if ($c!="")
					{
					if (!hook('modifysearchfieldvalues')) 
						{
						?><option <?php if (cleanse_string($c,false)==$value) { ?>selected<?php } ?>><?php echo $c?></option><?php
                        }
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
			case 7:
			 
			
			
			
			#-------------------- Category Tree (launches popup window)
			
			# Show a smaller version of the selected node box, plus the hidden value that submits the form.
			
			# Reprocess provided value into expected format.
			$value=str_replace(";",",",$value);
			?>
			
			<div id="field_<?php echo $fields[$n]["name"]?>" >
			<div id="<?php echo $fields[$n]["name"]?>_statusbox" class="MiniCategoryBox"></div>
			<input type="hidden" name="field_cat_<?php echo $fields[$n]["name"]?>" id="<?php echo $fields[$n]["name"]?>_category" value="<?php echo $value?>">
			
			
			<?php
			# Add floating frame HTML. This must go in the footer otherwise it appears in the wrong place in IE due to it existing within a floated parent (the search bar).
			$extrafooterhtml.="
			<div class=\"RecordPanel\" style=\"display:none;position:fixed;top:100px;left:200px;text-align:left;\" id=\"cattree_" . $fields[$n]["name"] . "\">" . $lang["pleasewait"] . "</div>
			<script type=\"text/javascript\">
			// Load Category Tree
			new Ajax.Updater ('cattree_" . $fields[$n]["name"] . "','" . $baseurl_short . "pages/ajax/category_tree_popup.php?field=" . $fields[$n]["ref"] . "&value=" . urlencode($value) . "', { method: 'post', evalScripts: true });
			</script>		
			";
			?>
			<a href="#" onClick="document.getElementById('cattree_<?php echo $fields[$n]["name"]?>').style.display='block';return false;
			"><?php echo $lang["select"] ?></a></div>
			<?php
			# Add to clear function
			$clear_function.="DeselectAll('" . $fields[$n]["name"] ."');";
			
			break;			
			
			
			}
		?>
		</div>	
		<?php		
		}
	?>
	<script type="text/javascript">
	function FilterBasicSearchOptions(clickedfield,resourcetype)
		{
		if (resourcetype!=0)
			{
			// When selecting resource type specific fields, automatically untick all other resource types, because selecting something from this field will never produce resources from the other resource types.
			
			// Always untick the Tick All box
			if (jQuery('#tickall')) {jQuery('#tickall').attr('checked', false);}
			<?php
			# Untick all other resource types.
			for ($n=0;$n<count($types);$n++)
				{
				?>
				if (resourcetype!=<?php echo $types[$n]["ref"]?>) {jQuery("#TickBox<?php echo $types[$n]["ref"]?>").attr('checked', false);} else {jQuery("#TickBox<?php echo $types[$n]["ref"]?>").attr('checked', true);}
				<?php
				}
				?>
			// Hide any fields now no longer relevant.	
			HideInapplicableSimpleSearchFields(false);
			}

		<?php
		// When using more than one dropdown field, automatically filter field options using AJAX
		// in a attempt to avoid blank results sets through excessive selection of filters.
		if ($simple_search_dropdown_filtering && count($optionfields)>1) { ?>
		var Filter="";
		var clickedfieldno="";
		<?php for ($n=0;$n<count($optionfields);$n++)
			{
			?>
			Filter += "<?php if ($n>0) {echo ";";} ?><?php echo $optionfields[$n]?>:" + jQuery('#field_<?php echo $optionfields[$n]?>').value;
			
			// Display waiting message
			if (clickedfield!='<?php echo $optionfields[$n]?>')
				{
				if (jQuery('field_<?php echo $optionfields[$n]?>').attr('selectedIndex', 0);)
					{
					jQuery('field_<?php echo $optionfields[$n]?>').html("<option value=''><?php echo $lang["pleasewaitsmall"] ?></option>");
					}
				}
			else
				{
				clickedfieldno='<?php echo $n ?>';
				}
			<?php
			} ?>
		
		// Send AJAX post request.
		jQuery.post('<?php echo $baseurl_short?>pages/ajax/filter_basic_search_options.php?nofilter=' + encodeURIComponent(clickedfieldno) + '&filter=' + encodeURIComponent(Filter), { success: function(data, textStatus, jqXHR) {eval(data);} });
		<?php } ?>
		}
		
	function HideInapplicableSimpleSearchFields(reset)
		{
		<?php
		# Consider each of the fields. Hide if the resource type for this field is not checked
		for ($n=0;$n<count($fields);$n++)
			{
			if ($fields[$n]["resource_type"]!=0)
				{
				?>
				if (reset)
					{
					// When clicking checkboxes, always reset any resource type specific fields.
					document.getElementById('field_<?php echo $fields[$n]["name"]?>').value='';
					}
					
				if (!document.getElementById('TickBox<?php echo $fields[$n]["resource_type"] ?>').checked)
					{
					document.getElementById('simplesearch_<?php echo $fields[$n]["ref"] ?>').style.display='none';
					// Also deselect it.
					document.getElementById('field_<?php echo $fields[$n]["name"]?>').value='';
					}
				else
					{document.getElementById('simplesearch_<?php echo $fields[$n]["ref"] ?>').style.display='block';}
				<?php
				}
			}
		?>
		}
	HideInapplicableSimpleSearchFields();
	
	</script>
		
	<div id="basicdate" class="SearchItem"><?php echo $lang["bydate"]?><br />
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
	<select id="basicday" name="day" class="SearchWidth" style="width:73px;">
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
	
    <?php if ($star_search && $display_user_rating_stars){?>
	<div class="SearchItem"><?php echo $lang["starsminsearch"];?><br />
	<input type="hidden" id="starsearch" name="starsearch" class="SearchWidth" value="<?php echo $starsearch;?>">
		<?php if ($starsearch=="") {$starsearch=0;}?>		
		<div  class="RatingStars" onMouseOut="StarSearchRatingDisplay(document.getElementById('starsearch').value,'StarCurrent');">&nbsp;<?php 
		for ($z=1;$z<=5;$z++)
			{
			?><a href="#" onMouseOver="StarSearchRatingDisplay(<?php echo $z?>,'StarSelect');" onClick="document.getElementById('starsearch').value=<?php echo $z?>;"><span id="RatingStar-<?php echo $z?>" class="Star<?php echo ($z<=$starsearch?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
			}
		?>
		</div>
	</div>
	<?php } ?>
	
    <?php if (isset($resourceid_simple_search) and $resourceid_simple_search){ ?>
             <div class="SearchItem"><?php echo $lang["resourceid"]?><br />
             <input id="searchresourceid" name="searchresourceid" type="text" class="SearchWidth" value="" />
             </div>
    <?php } ?>


	</div>

	<script type="text/javascript">
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
	
	<?php hook("searchbarbeforebuttons"); ?>
		
	<?php if ($searchbar_buttons_at_bottom){ echo $searchbuttons; } ?>
			
  </form>
  <br />
  <?php hook("searchbarbeforebottomlinks"); ?>
  <?php if (! $disable_geocoding) { ?><p><a href="<?php echo $baseurl?>/pages/geo_search.php">&gt; <?php echo $lang["geographicsearch"]?></a></p><?php } ?>
  <?php if (! $advancedsearch_disabled) { ?><p><a href="<?php echo $baseurl?>/pages/search_advanced.php">&gt; <?php echo $lang["gotoadvancedsearch"]?></a></p><?php } ?>

  <?php hook("searchbarafterbuttons"); ?>

  <?php if ($view_new_material) { ?><p><a href="<?php echo $baseurl?>/pages/search.php?search=<?php echo urlencode("!last".$recent_search_quantity)?>">&gt; <?php echo $lang["viewnewmaterial"]?></a></p><?php } ?>
	
	<?php } ?> <!-- END of Searchbarreplace hook -->
	</div>
	</div>
	<div class="PanelShadow"></div>
<?php } ?>	
	
	<?php if (isset($anonymous_login) && (isset($username)) && ($username==$anonymous_login))
	{
	# For anonymous access, display the login panel
	?>
	<br /><div id="SearchBoxPanel">
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
	<div class="PanelShadow"></div>
	<?php
	}
?>
<?php hook("addsearchbarpanel");?>	
	
	<?php if (($research_request) && (!isset($k) || $k=="") && (checkperm("q"))) { ?>
	<?php if (!hook("replaceresearchrequestbox")) { ?>
	<div id="ResearchBoxPanel">
  	<div class="SearchSpace">
  	<?php if (!hook("replaceresearchrequestboxcontent"))  { ?>
	<h2><?php echo $lang["researchrequest"]?></h2>
	<p><?php echo text("researchrequest")?></p>
	<div class="HorizontalWhiteNav"><a href="<?php echo $baseurl?>/pages/research_request.php">&gt; <?php echo $lang["researchrequestservice"]?></a></div>
	</div><br />
	<?php } /* end replaceresearchrequestboxcontent */ ?>
	</div>
	<div class="PanelShadow"></div>
	<?php } /* end replaceresearchrequestbox */ ?>
	<?php } ?>

	<?php if ($pagename!="login" && $pagename!="user_password" && $pagename!="user_request" && $frameless_collections && !checkperm("b"))
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

<?php hook("searchbarbottom"); ?>
