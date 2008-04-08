<?
include "include/db.php";
include "include/authenticate.php"; 
include "include/general.php";
include "include/resource_functions.php";
include "include/collections_functions.php";
include "include/image_processing.php";

# Editing resource or collection of resources (multiple)?
$ref=getvalescaped("ref","");
$collection=getvalescaped("collection","");
if ($collection!="") 
	{
	# If editing multiple items, use the first resource as the template
	$multiple=true;
	$items=get_collection_resources($collection);
	if (count($items)==0) {exit("You cannot edit an empty collection.");}
	$ref=$items[0];
	}
else
	{
	$multiple=false;
	}

if (getval("regenexif","")!="")
	{
	extract_exif_comment($ref);
	}


# Fetch resource data.
$resource=get_resource_data($ref);

# Not allowed to edit this resource?
if ((!checkperm("e" . $resource["archive"])) && ($ref>0)) {exit ("Permission denied.");}

if (getval("regen","")!="")
	{
	create_previews($ref,false,$resource["file_extension"]);
	}
	
if (getval("save","")!="")
	{
	# save data
	if (!$multiple)
		{
		save_resource_data($ref,$multiple);
	
		if ($ref>0)
			{
			# Log this			
			daily_stat("Resource edit",$ref);
			redirect("view.php?ref=" . $ref);
			}
		else
			{
			if (getval("fancy","")!="")
				{
				$resource_type=getvalescaped("resource_type","");
				update_resource_type($ref,$resource_type);
				redirect("upload_fancy.php");
				}
			else
				{
				redirect("team_batch.php");
				}
			}
		}
	else
		{
		# Save multiple resources
		save_resource_data_multi($collection);
		redirect("search.php?refreshcollectionframe=true&search=!collection" . $collection);
		}
	}

if (getval("tweak","")!="")
	{
	$tweak=getval("tweak","");
	switch($tweak)
		{
		case "rotateclock":
		tweak_preview_images($ref,270,0,$resource["preview_extension"]);
		break;
		case "rotateanti":
		tweak_preview_images($ref,90,0,$resource["preview_extension"]);
		break;
		case "gammaplus":
		tweak_preview_images($ref,0,1.3,$resource["preview_extension"]);
		break;
		case "gammaminus":
		tweak_preview_images($ref,0,0.7,$resource["preview_extension"]);
		break;
		case "restore":
		create_previews($ref,false,$resource["file_extension"]);
		break;
		}
	}

include "include/header.php";
?>
<div class="BasicsBox"> 

<form method="post" id="mainform">

<? 
if ($multiple) { ?>
<h1><?=$lang["editmultipleresources"]?></h1>
<p><?=count($items)?> <?=$lang["resourcesselected"]?>. <?=text("multiple")?></p>

<? } elseif ($ref>0) { ?>
<h1><?=$lang["editresource"]?></h1>

<div class="Question">
<label><?=$lang["resourceid"]?></label>
<div class="Fixed"><?=$ref?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><? if ($resource["resource_type"]==1) {?><?=$lang["image"]?><?} elseif ($resource["resource_type"]==3) { ?><?=$lang["previewimage"]?><?} else {?><?=$lang["file"]?><?}?></label>
<? if ($resource["has_image"]==1) { ?><img align="top" src="<?=get_resource_path($ref,"thm",false,$resource["preview_extension"])?>?nc=<?=time()?>" class="ImageBorder" style="margin-right:10px;"/>
<? } elseif ($resource["file_extension"]!="") { ?><strong><?=strtoupper($resource["file_extension"] . " " . $lang["file"]) . " (" . formatfilesize(@filesize(get_resource_path($ref,"",false,$resource["file_extension"]))) . ")" ?></strong><? } ?>

<a href="upload.php?ref=<?=$ref?>">&gt;&nbsp;<?=$lang["uploadafile"]?></a>
</div>
<? if ($resource["has_image"]==1) { ?>
<div class="Question">
<label><?=$lang["imagecorrection"]?><br/><?=$lang["previewthumbonly"]?></label><select class="stdwidth" name="tweak" id="tweak" onChange="document.getElementById('mainform').submit();">
<option value=""><?=$lang["select"]?></option>
<option value="rotateclock"><?=$lang["rotateclockwise"]?></option>
<option value="rotateanti"><?=$lang["rotateanticlockwise"]?></option>
<option value="gammaplus"><?=$lang["increasegamma"]?></option>
<option value="gammaminus"><?=$lang["decreasegamma"]?></option>
<option value="restore"><?=$lang["restoreoriginal"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<? } ?>

<? } else { # For batch uploads, specify default content (writes to resource with ID [negative user ref]) ?>
<h1><?=$lang["specifydefaultcontent"]?></h1>
<p><?=text("batch")?></p>

<? if (getval("fancy","")!="") { # We need to ask for the resource type here for FancyUploads
?>
<div class="Question">
<label for="resourcetype"><?=$lang["resourcetype"]?></label>
<select name="resource_type" id="resourcetype" class="shrtwidth">
<?
$types=get_resource_types();
for ($n=0;$n<count($types);$n++)
	{
	?><option value="<?=$types[$n]["ref"]?>"><?=$types[$n]["name"]?></option><?
	}
?></select>
<div class="clearerleft"> </div>
</div>
<? } ?>

<? } ?>

<?
$lastrt=-1;

# Batch uploads - "copy data from" feature
if ($ref<0)
	{ 
	?>
	<div class="Question">
	<label for="copyfrom"><?=$lang["batchcopyfrom"]?></label>
	<input class="stdwidth" type="text" name="copyfrom" id="copyfrom" value="" style="width:80px;">
	<input type="submit" name="copyfromsubmit" value="<?=$lang["copy"]?>">
	</div>
	<?
	}


$use=$ref;
# 'Copy from' been supplied? Load data from this resource instead.
if (getval("copyfrom","")!="") {$use=getvalescaped("copyfrom","");}
	
$fields=get_resource_field_data($use,$multiple);
for ($n=0;$n<count($fields);$n++)
	{
	if (!(($resource["archive"]==0) && ($fields[$n]["resource_type"]==999))) {

	$name="field_" . $fields[$n]["ref"];
	$value=$fields[$n]["value"];
	
	if (($fields[$n]["resource_type"]!=$lastrt)&& ($lastrt!=-1))
		{
		?><br><h1><?=get_resource_type_name($fields[$n]["resource_type"])?> <?=$lang["properties"]?></h1><?
		}
	$lastrt=$fields[$n]["resource_type"];
	if (getval("resetform","")!="") {$value="";}
	?>
	<? if ($multiple) { # Multiple items, a toggle checkbox appears which activates the question
	?><div><input name="editthis_<?=$name?>" id="editthis_<?=$n?>" type="checkbox" value="yes" onClick="var q=document.getElementById('question_<?=$n?>');var m=document.getElementById('modeselect_<?=$n?>');if (this.checked) {q.style.display='block';m.style.display='block';} else {q.style.display='none';m.style.display='none';}">&nbsp;<label for="editthis<?=$n?>"><?=htmlspecialchars(i18n_get_translated($fields[$n]["title"]))?></label></div><? } ?>

	<?
	if ($multiple)
		{
		# When editing multiple, give option to select Replace All Text or Find and Replace
		?>
		<div class="Question" id="modeselect_<?=$n?>" style="display:none;padding-bottom:0;margin-bottom:0;">
		<label><?=$lang["editmode"]?></label>
		<select name="modeselect_<?=$fields[$n]["ref"]?>" class="stdwidth" onChange="var fr=document.getElementById('findreplace_<?=$n?>');var q=document.getElementById('question_<?=$n?>');if (this.value=='FR') {fr.style.display='block';q.style.display='none';} else {fr.style.display='none';q.style.display='block';}">
		<option value="RT"><?=$lang["replacealltext"]?></option>
		<option value="FR"><?=$lang["findandreplace"]?></option>
		</select>
		</div>
		
		<div class="Question" id="findreplace_<?=$n?>" style="display:none;border-top:none;">
		<label>&nbsp;</label>
		<?=$lang["find"]?> <input type="text" name="find_<?=$fields[$n]["ref"]?>" class="shrtwidth">
		<?=$lang["andreplacewith"]?> <input type="text" name="replace_<?=$fields[$n]["ref"]?>" class="shrtwidth">
		</div>
		<?
		}
	?>

	<div class="Question" id="question_<?=$n?>" <?if ($multiple) {?>style="display:none;border-top:none;"<? } ?>>
	<label for="<?=$name?>"><?if (!$multiple) {?><?=htmlspecialchars(i18n_get_translated($fields[$n]["title"]))?> <? if ($fields[$n]["keywords_index"]==1) { ?><sup>*</sup><? } ?><? } ?></label>
	<?

	switch ($fields[$n]["type"]) {
		case 0: # -------- Plain text entry
		?><input class="stdwidth" type=text name="<?=$name?>" id="<?=$name?>" value="<?=htmlspecialchars($value)?>"><?
		break;
	
		case 1: # -------- Text area entry
		?><textarea class="stdwidth" rows=6 cols=50 name="<?=$name?>" id="<?=$name?>"><?=htmlspecialchars($value)?></textarea><?
		break;
		
		case 5: # -------- Larger text area entry
		?><textarea class="stdwidth" style="width:450px;" rows=20 cols=80 name="<?=$name?>" id="<?=$name?>"><?=htmlspecialchars($value)?></textarea><?
		break;
		
		case 2: # -------- Check box list
		$options=trim_array(explode(",",$fields[$n]["options"]));sort($options);
		$set=trim_array(explode(",",$value));
		$wrap=0;
		$l=average_length($options);
		$cols=5;
		if ($l>10) {$cols=4;}
		if ($l>15) {$cols=3;}
		if ($l>25) {$cols=2;}
		?>
		<table cellpadding=2 cellspacing=0><tr>
		<?
		for ($m=0;$m<count($options);$m++)
			{
			$name=$fields[$n]["ref"] . "_" . $m;
			$wrap++;if ($wrap>$cols) {$wrap=1;?></tr><tr><?}
			?>
			<td width="1"><input type="checkbox" name="<?=$name?>" value="yes" <?if (in_array($options[$m],$set)) {?>checked<?}?> /></td><td><?=htmlspecialchars(i18n_get_translated($options[$m]))?>&nbsp;&nbsp;</td>
			<?
			}
		?></tr></table><?
		break;

		case 3: # -------- Drop down list
		$options=explode(",",$fields[$n]["options"]);
		?><select class="stdwidth" name="<?=$name?>" id="<?=$name?>"><?
		for ($m=0;$m<count($options);$m++)
			{
			?>
			<option value="<?=htmlspecialchars(trim($options[$m]))?>" <?if (trim($options[$m])==trim($value)) {?>selected<?}?>><?=htmlspecialchars(trim(i18n_get_translated($options[$m])))?></option>
			<?
			}
		?></select><?
		break;
		
		
		case 4: # -------- Date selector
		case 6: # Also includes expiry date
        $dy=date("Y");$dm=date("m");$dd=date("d");

		if ($fields[$n]["type"]==6) {$dy="";$dm="";$dd="";}
		if ($value!="")
        	{
            #fetch the date parts from the value
            $sd=split(" ",$value);
            $value=$sd[0];
            $sd=split("-",$value);
            if (count($sd)>=3)
            	{
	            $dy=intval($sd[0]);$dm=intval($sd[1]);$dd=intval($sd[2]);
	            }
            }
        ?>
        <select name="<?=$name?>-d"><option value=""><?=$lang["day"]?></option>
        <?for ($m=1;$m<=31;$m++) {?><option <?if($m==$dd){echo " selected";}?>><?=sprintf("%02d",$m)?></option><?}?>
        </select>
            
        <select name="<?=$name?>-m"><option value=""><?=$lang["month"]?></option>
        <?for ($m=1;$m<=12;$m++) {?><option <?if($m==$dm){echo " selected";}?> value="<?=sprintf("%02d",$m)?>"><?=$lang["months"][$m-1]?></option><?}?>
        </select>
           
        <input type=text size=5 name="<?=$name?>-y" value="<?=$dy?>">
        <?
		break;
		}
		?>
		<div class="clearerleft"> </div>
		</div>
		<?
	}
	}
?>
<br><h1><?=$lang["statusandrelationships"]?></h1>

	<!-- Archive Status -->
	<? if ($multiple) { ?><div><input name="editthis_status" id="editthis_status" value="yes" type="checkbox" onClick="var q=document.getElementById('question_status');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editthis<?=$n?>"><?=$lang["status"]?></label></div><? } ?>
	<div class="Question" id="question_status" <? if ($multiple) {?>style="display:none;"<?}?>>
	<label for="archive"><?=$lang["status"]?></label>
	<select class="stdwidth" name="archive" id="archive">
	
	<? for ($n=-2;$n<=2;$n++) { ?>
	<? if (checkperm("e" . $n)) { ?><option value="<?=$n?>" <? if ($resource["archive"]==$n) { ?>selected<? } ?>><?=$lang["status" . $n]?></option><? } ?>
	<? } ?>
	</select>
	<div class="clearerleft"> </div>
	</div>
	
	<!-- Access -->
	<? if ($multiple) { ?><div><input name="editthis_access" id="editthis_access" value="yes" type="checkbox" onClick="var q=document.getElementById('question_access');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editthis<?=$n?>"><?=$lang["access"]?></label></div><? } ?>
	<div class="Question" id="question_access" <? if ($multiple) {?>style="display:none;"<?}?>>
	<label for="archive"><?=$lang["access"]?></label>
	<select class="stdwidth" name="access" id="access" onChange="var c=document.getElementById('custom_access');if (this.value==3) {c.style.display='block';} else {c.style.display='none';}">
	
	<? for ($n=0;$n<=3;$n++) { ?>
	<option value="<?=$n?>" <? if ($resource["access"]==$n) { ?>selected<? } ?>><?=$lang["access" . $n]?></option>
	<? } ?>
	</select>
	<div class="clearerleft"> </div>
	<table id="custom_access" cellpadding=3 cellspacing=3 style="padding-left:150px;<? if ($resource["access"]!=3) { ?>display:none;<? } ?>">
	<?
	$groups=get_resource_custom_access($ref);
	for ($n=0;$n<count($groups);$n++)
		{
		$access=2;$editable=true;
		if ($groups[$n]["access"]!="") {$access=$groups[$n]["access"];}
		$perms=explode(",",$groups[$n]["permissions"]);
		if (in_array("v",$perms)) {$access=0;$editable=false;}
		?>
		<tr>
		<td valign=middle nowrap><?=htmlspecialchars($groups[$n]["name"])?>&nbsp;&nbsp;</td>
		<td width=10 valign=middle><input type=radio name="custom_<?=$groups[$n]["ref"]?>" value="0" <? if (!$editable) { ?>disabled<? } ?> <? if ($access==0) { ?>checked<? } ?>></td>
		<td align=left valign=middle><?=$lang["access0"]?></td>
		<td width=10 valign=middle><input type=radio name="custom_<?=$groups[$n]["ref"]?>" value="1" <? if (!$editable) { ?>disabled<? } ?> <? if ($access==1) { ?>checked<? } ?>></td>
		<td align=left valign=middle><?=$lang["access1"]?></td>
		<td width=10 valign=middle><input type=radio name="custom_<?=$groups[$n]["ref"]?>" value="2" <? if (!$editable) { ?>disabled<? } ?> <? if ($access==2) { ?>checked<? } ?>></td>
		<td align=left valign=middle><?=$lang["access2"]?></td>
		</tr>
		<?
		}
	?></table>
	<div class="clearerleft"> </div>
	</div>
	

	<!-- Related Resources -->
	<? if ($multiple) { ?><div><input name="editthis_related" id="editthis_related" value="yes" type="checkbox" onClick="var q=document.getElementById('question_related');if (q.style.display!='block') {q.style.display='block';} else {q.style.display='none';}">&nbsp;<label for="editthis<?=$n?>"><?=$lang["relatedresources"]?></label></div><? } ?>
	<div class="Question" id="question_related" <? if ($multiple) {?>style="display:none;"<?}?>>
	<label for="related"><?=$lang["relatedresources"]?></label>
	<textarea class="stdwidth" rows=3 cols=50 name="related" id="related"><?=((getval("resetform","")!="")?"":join(", ",get_related_resources($ref)))?></textarea>
	<div class="clearerleft"> </div>
	</div>

	<div class="QuestionSubmit">
	<label for="buttons"> </label>
	<input name="resetform" type="submit" value="<?=$lang["clearform"]?>" />&nbsp;
	<input <? if ($multiple) { ?>onclick="return confirm('<?=$lang["confirmeditall"]?>');"<? } ?> name="save" type="submit" value="&nbsp;&nbsp;<?=($ref>0)?$lang["save"]:$lang["next"]?>&nbsp;&nbsp;" />
	</div>
</form>
<p><sup>*</sup> <?=$lang["indexedsearchable"]?></p>
</div>

<!--<p><a href="view.php?ref=<?=$ref?>">Back to view</a></p>-->
<?
include "include/footer.php";
?>
