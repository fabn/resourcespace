<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("s")) {exit ("Permission denied.");}
include "include/general.php";
include "include/search_functions.php";

$archive=getvalescaped("archive",0);

if ((getval("dosearch","")!="") || (getval("countonly","")!=""))
	{
	$restypes="";
	reset($_POST);foreach ($_POST as $key=>$value)
		{
		if (substr($key,0,8)=="resource") {if ($restypes!="") {$restypes.=",";} $restypes.=substr($key,8);}
		}
	setcookie("restypes",$restypes);
		
	# advanced search - build a search query and redirect
	$fields=get_advanced_search_fields();
	$search=join(", ",explode(" ",getvalescaped("allfields",""))); # prepend 'all fields' option
	
	if (getval("year","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.="year:" . getval("year","");	
		}
	if (getval("month","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.="month:" . getval("month","");	
		}
	if (getval("day","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.="day:" . getval("day","");	
		}
		
	for ($n=0;$n<count($fields);$n++)
		{
		switch ($fields[$n]["type"])
			{
			case 0: # -------- Text boxes
			case 1:
			$name="field_" . $fields[$n]["ref"];
			$value=getvalescaped($name,"");
			if ($value!="")
				{
				$vs=split_keywords($value);
				for ($m=0;$m<count($vs);$m++)
					{
					if ($search!="") {$search.=", ";}
					$search.=$fields[$n]["name"] . ":" . strtolower($vs[$m]);
					}
				}
			break;
			
			case 2: # -------- Dropdowns / check lists
			case 3:
			$options=trim_array(explode(",",$fields[$n]["options"]));sort($options);
			$p="";
			$c=0;
			for ($m=0;$m<count($options);$m++)
				{
				$name=$fields[$n]["ref"] . "_" . $m;
				$value=getvalescaped($name,"");
				if ($value=="yes")
					{
					$c++;
					if ($p!="") {$p.=";";}
					$p.=strtolower(i18n_get_translated($options[$m]));
					}
				}
			if ($c==count($options))
				{
				# all options ticked - omit from the search
				$p="";
				}
			if ($p!="")
				{
				if ($search!="") {$search.=", ";}
				$search.=$fields[$n]["name"] . ":" . $p;
				}
			break;
			}
		}
		
	if (getval("countonly","")!="")
		{
		# Only show the results (this will appear in an iframe)
		if (count($search)==0)
			{
			$count=0;
			}
		else
			{
			$result=do_search($search,$restypes,"relevance",$archive,0);
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
		<script language="Javascript">
		<? if ($count==0) { ?>
		parent.document.getElementById("dosearch").disabled=true;
		parent.document.getElementById("dosearch").value="<?=$lang["nomatchingresources"]?>";
		<? } else { ?>
		parent.document.getElementById("dosearch").value="<?=$lang["view"]?> <?=number_format($count)?> <?=$lang["matchingresources"]?>";
		parent.document.getElementById("dosearch").disabled=false;
		<? } ?>
		</script>
		</html>
		<?
		exit();
		}
	else
		{
		# Log this			
		daily_stat("Advanced search",$userref);

		redirect("search.php?search=" . urlencode($search) . "&archive=" . $archive);
		}
	}

# Reconstruct a values array based on the search keyword, so we can pre-populate the form from the current search
$search=@$_COOKIE["search"];
$keywords=explode(", ",$search);
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
		
		$values[$name]=$keyword;
		}
	else
		{
		if ($allwords=="") {$allwords=$keyword;} else {$allwords.=", " . $keyword;}
		}
	}
$allwords=str_replace(",","",$allwords);
if (getval("resetform","")!="") {$found_year="";$found_month="";$found_day="";$allwords="";}
include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=($archive==0)?$lang["advancedsearch"]:$lang["archiveonlysearch"]?> </h1>
<p class="tight"><?=text("introtext")?></p>
<form method="post" id="advancedform">
<input type="hidden" name="countonly" id="countonly" value="">
<input type="hidden" name="archive" value="<?=$archive?>">

<script language="Javascript">
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

<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="resetform" id="resetform2" type="submit" value="&nbsp;&nbsp;<?=$lang["clearform"]?>&nbsp;&nbsp;" />&nbsp;

</div>

<!-- Search across all fields -->
<div class="Question">
<label for="allfields"><?=$lang["allfields"]?></label><input class="formfield" type=text name="allfields" id="allfields" value="<?=$allwords?>" onChange="UpdateResultCount();">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?=$lang["resourcetypes"]?></label><?
$rt=explode(",",getvalescaped("restypes",""));
$types=get_resource_types();
$wrap=0;
?><table><tr><?
for ($n=0;$n<count($types);$n++)
	{
	$wrap++;if ($wrap>3) {$wrap=1;?></tr><tr><?}
	?><td valign=top><input type=checkbox name="resource<?=$types[$n]["ref"]?>" value="yes" <? if ((((count($rt)==1) && ($rt[0]=="")) || (in_array($types[$n]["ref"],$rt))) && (getval("resetform","")=="")) {?>checked<?}?> onChange="UpdateResultCount();"></td><td width="32%" valign=top><?=$types[$n]["name"]?></td><?	
	}
?>
</tr></table>
<div class="clearerleft"> </div>
</div>

<div class="Question"><label><?=$lang["bydate"]?></label>
<select name="year" class="SearchWidth" style="width:100px;" onChange="UpdateResultCount();">
  <option selected="selected" value=""><?=$lang["anyyear"]?></option>
  <?
  $y=date("Y");
  for ($n=$minyear;$n<=$y;$n++)
	{
	?><option <? if ($n==$found_year) { ?>selected<? } ?>><?=$n?></option><?
	}
  ?>
</select>
<select name="month" class="SearchWidth" style="width:100px;" onChange="UpdateResultCount();">
  <option selected="selected" value=""><?=$lang["anymonth"]?></option>
  <?
  for ($n=1;$n<=12;$n++)
	{
	$m=str_pad($n,2,"0",STR_PAD_LEFT);
	?><option <? if ($n==$found_month) { ?>selected<? } ?> value="<?=$m?>"><?=date("F",mktime(0,0,0,$n,1,2000))?></option><?
	}
  ?>
</select>
<select name="day" class="SearchWidth" style="width:100px;" onChange="UpdateResultCount();">
  <option selected="selected" value=""><?=$lang["anyday"]?></option>
  <?
  for ($n=1;$n<=31;$n++)
	{
	$m=str_pad($n,2,"0",STR_PAD_LEFT);
	?><option <? if ($n==$found_day) { ?>selected<? } ?> value="<?=$m?>"><?=$m?></option><?
	}
  ?>
</select>
<div class="clearerleft"> </div>
</div>

<?
$fields=get_advanced_search_fields($archive>0);
$showndivide=false;
for ($n=0;$n<count($fields);$n++)
	{
	$name="field_" . $fields[$n]["ref"];
	if (($fields[$n]["resource_type"]!=0) && ($showndivide==false))
		{
		$showndivide=true;
		?>
		<h1><?=$lang["typespecific"]?></h1>
		<?
		}
	?>
	<div class="Question">
	<label><?=i18n_get_translated($fields[$n]["title"])?></label>
	<?
	if (array_key_exists($fields[$n]["name"],$values)) {$value=$values[$fields[$n]["name"]];} else {$value="";}
	if (getval("resetform","")!="") {$value="";}
	switch ($fields[$n]["type"]) {
		case 0: # -------- Text boxes
		case 1:
		?><input class="stdwidth" type=text name="field_<?=$fields[$n]["ref"]?>" value="<?=htmlspecialchars($value)?>" onChange="UpdateResultCount();" onKeyPress="if (!(updating)) {setTimeout('UpdateResultCount()',2000);updating=true;}"><?
		break;
	
		case 2: # -------- Show a check list for both dropdowns and check lists
		case 3:
		$options=trim_array(explode(",",$fields[$n]["options"]));sort($options);
		$set=trim_array(explode(";",$value));
		$wrap=0;
		$l=average_length($options);
		$cols=5;
		if ($l>10) {$cols=4;}
		if ($l>15) {$cols=3;}
		if ($l>25) {$cols=2;}
		?><table cellpadding=2 cellspacing=0><tr><?
		for ($m=0;$m<count($options);$m++)
			{
			$wrap++;if ($wrap>$cols) {$wrap=1;?></tr><tr><?}
			$name=$fields[$n]["ref"] . "_" . $m;
			?>
			<td valign=middle><input type=checkbox id="<?=$name?>" name="<?=$name?>" value="yes" <? if (in_array(strtolower(i18n_get_translated($options[$m])),$set)) {?>checked<?}?> onClick="UpdateResultCount();"></td><td valign=middle><?=htmlspecialchars(i18n_get_translated($options[$m]))?>&nbsp;&nbsp;</td>
			<?
			}
		?></tr></table><?
		break;
		}
	?>
	<div class="clearerleft"> </div>
	</div>
	<?
	}
?>
<iframe name="resultcount" id="resultcount" style="visibility:hidden;" width=1 height=1></iframe>
<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="dosearch" id="dosearch" type="submit" value="<?=$lang["viewmatchingresources"]?>" />
&nbsp;
<input name="resetform" id="resetform" type="submit" value="<?=$lang["clearform"]?>" />
</div>
</form>
</div>
<?
include "include/footer.php";
?>