<?php
include "../include/db.php";
include "../include/authenticate.php"; if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/search_functions.php";

$archive=getvalescaped("archive",0);
$starsearch=getvalescaped("starsearch","");	
setcookie("starsearch",$starsearch);

# Disable auto-save function, only applicable to edit form. Some fields pick up on this value when rendering then fail to work.
$edit_autosave=false;

if ((getval("dosearch","")!="") || (getval("countonly","")!=""))
	{
	$restypes="";
	reset($_POST);foreach ($_POST as $key=>$value)
		{
		if (substr($key,0,12)=="resourcetype") {if ($restypes!="") {$restypes.=",";} $restypes.=substr($key,12);}
		}
	setcookie("restypes",$restypes);
		
	# advanced search - build a search query and redirect
	$fields=get_advanced_search_fields();

	# Build a search query from the search form
	$search=search_form_to_search_query($fields);
			$search=refine_searchstring($search);
	if (getval("countonly","")!="")
		{
		# Only show the results (this will appear in an iframe)
		if (count($search)==0)
			{
			$count=0;
			}
		else
			{
			$result=do_search($search,$restypes,"relevance",$archive,0,"",false,$starsearch);
			if (is_array($result))
				{
				$count=count($result);
				}
			else
				{
				$count=0;
				}
			}
		?>
		<html>
		<script type="text/javascript">
		<?php if ($count==0) { ?>
		parent.document.getElementById("dosearch").disabled=true;
		parent.document.getElementById("dosearch").value="<?php echo $lang["nomatchingresources"]?>";
		<?php } else { ?>
		parent.document.getElementById("dosearch").value="<?php echo $lang["view"] . " " . number_format($count) . " " . $lang["matchingresources"] ?>";
		parent.document.getElementById("dosearch").disabled=false;
		<?php } ?>
		</script>
		</html>
		<?php
		exit();
		}
	else
		{
		# Log this			
		daily_stat("Advanced search",$userref);

		redirect("pages/search.php?search=" . urlencode($search) . "&archive=" . $archive);
		}
	}



# Reconstruct a values array based on the search keyword, so we can pre-populate the form from the current search
$search=@$_COOKIE["search"];
$keywords=explode(",",$search);
$allwords="";$found_year="";$found_month="";$found_day="";
$values=array();
for ($n=0;$n<count($keywords);$n++)
	{
	$keyword=$keywords[$n];
	if (strpos($keyword,":")!==false)
		{
		$k=explode(":",$keyword);
		$name=$k[0];
		$keyword=$k[1];
		if ($name=="day") {$found_day=$keyword;}
		if ($name=="month") {$found_month=$keyword;}
		if ($name=="year") {$found_year=$keyword;}
		if (isset($values[$name])){$values[$name].=" ".$keyword;}
		else {
			$values[$name]=$keyword;
		}
		}
	else
		{
		if ($allwords=="") {$allwords=$keyword;} else {$allwords.=", " . $keyword;}
		}
	}
$allwords=str_replace(",","",$allwords);
if (getval("resetform","")!="") {$found_year="";$found_month="";$found_day="";$allwords="";$starsearch="";}
include "../include/header.php";
?>

<div class="BasicsBox">
<h1><?php echo ($archive==0)?$lang["advancedsearch"]:$lang["archiveonlysearch"]?> </h1>
<p class="tight"><?php echo text("introtext")?></p>
<form method="post" id="advancedform">
<input type="hidden" name="countonly" id="countonly" value="">
<input type="hidden" name="archive" value="<?php echo $archive?>">

<script type="text/javascript">
var updating=false;
function UpdateResultCount()
	{
	updating=false;
	// set the target of the form to be the result count iframe and submit
	document.getElementById("advancedform").target="resultcount";
	document.getElementById("countonly").value="yes";
	document.getElementById("advancedform").submit();
	document.getElementById("advancedform").target="";
	document.getElementById("countonly").value="";
	}
</script>


<!-- Search across all fields -->
<div class="Question">
<label for="allfields"><?php echo $lang["allfields"]?></label><input class="stdwidth" type=text name="allfields" id="allfields" value="<?php echo $allwords?>" onChange="UpdateResultCount();">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["resourcetypes"]?></label><?php
$rt=explode(",",getvalescaped("restypes",""));
$types=get_resource_types();
$wrap=0;
?><table><tr><?php
for ($n=0;$n<count($types);$n++)
	{
	$wrap++;if ($wrap>4) {$wrap=1;?></tr><tr><?php }
	?><td valign=middle><input type=checkbox name="resourcetype<?php echo $types[$n]["ref"]?>" value="yes" <?php if ((((count($rt)==1) && ($rt[0]=="")) || (in_array($types[$n]["ref"],$rt))) && (getval("resetform","")=="")) {?>checked<?php } ?> onChange="UpdateResultCount();"></td><td valign=middle><?php echo $types[$n]["name"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><?php	
	}
?>
</tr></table>
<div class="clearerleft"> </div>
</div>


<!-- Search for resource ID(s) -->
<div class="Question">
<label for="resourceids"><?php echo $lang["resourceids"]?></label><input class="stdwidth" type=text name="resourceids" id="resourceids" value="<?php echo htmlspecialchars(getval("resourceids","")) ?>" onChange="UpdateResultCount();">
<div class="clearerleft"> </div>
</div>


<div class="Question"><label><?php echo $lang["bydate"]?></label>
<select name="year" class="SearchWidth" style="width:100px;" onChange="UpdateResultCount();">
  <option value=""><?php echo $lang["anyyear"]?></option>
  <?php
  $y=date("Y");
  for ($n=$minyear;$n<=$y;$n++)
	{
	?><option <?php if ($n==$found_year) { ?>selected<?php } ?>><?php echo $n?></option><?php
	}
  ?>
</select>
<select name="month" class="SearchWidth" style="width:100px;" onChange="UpdateResultCount();">
  <option value=""><?php echo $lang["anymonth"]?></option>
  <?php
  for ($n=1;$n<=12;$n++)
	{
	$m=str_pad($n,2,"0",STR_PAD_LEFT);
	?><option <?php if ($n==$found_month) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $lang["months"][$n-1]?></option><?php
	}
  ?>
</select>
<select name="day" class="SearchWidth" style="width:100px;" onChange="UpdateResultCount();">
  <option value=""><?php echo $lang["anyday"]?></option>
  <?php
  for ($n=1;$n<=31;$n++)
	{
	$m=str_pad($n,2,"0",STR_PAD_LEFT);
	?><option <?php if ($n==$found_day) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $m?></option><?php
	}
  ?>
</select>
<div class="clearerleft"> </div>
</div>
<?php if ($star_search && $display_user_rating_stars){?>
<div class="Question"><label><?php echo $lang["starsminsearch"];?></label>
<select id="starsearch" name="starsearch" class="SearchWidth" onChange="UpdateResultCount();">
<option value=""><?php echo $lang['anynumberofstars']?></option>
<?php for ($n=1;$n<=5;$n++){?>
	 <option value="<?php echo $n;?>" <?php if ($n==$starsearch){?>selected<?php } ?>><?php for ($x=0;$x<$n;$x++){?>&#9733;<?php } ?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<iframe src="blank.html" name="resultcount" id="resultcount" style="visibility:hidden;" width=1 height=1></iframe>
<?php
# Fetch fields
$fields=get_advanced_search_fields($archive>0);
$showndivide=-1;

# Preload resource types
$rtypes=get_resource_types();

for ($n=0;$n<count($fields);$n++)
	{
	# Show a dividing header for resource type specific fields?
	if (($fields[$n]["resource_type"]!=0) && ($showndivide!=$fields[$n]["resource_type"]))
		{
		$showndivide=$fields[$n]["resource_type"];
		$label="??";
		# Find resource type name
		for ($m=0;$m<count($rtypes);$m++)
			{
			# Note: get_resource_types() has already translated the resource type name for the current user.
			if ($rtypes[$m]["ref"]==$fields[$n]["resource_type"]) {$label=$rtypes[$m]["name"];}
			}
		?>
		<h1><?php echo $lang["typespecific"] . ": " . $label ?></h1>
		<?php
		}

	# Work out a default value
	if (array_key_exists($fields[$n]["name"],$values)) {$value=$values[$fields[$n]["name"]];} else {$value="";}
	if (getval("resetform","")!="") {$value="";}
	
	# Render this field
	render_search_field($fields[$n],$value,true);
	}
?>
<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="dosearch" id="dosearch" type="submit" value="<?php echo $lang["action-viewmatchingresources"]?>" />
&nbsp;
<input name="resetform" id="resetform" type="submit" value="<?php echo $lang["clearbutton"]?>" />
</div>
</form>
</div>
<?php // show result count as it stands ?>
<script type="text/javascript">UpdateResultCount();</script>
<?php
include "../include/footer.php";
?>
