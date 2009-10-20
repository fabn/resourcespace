<?php
/**
 * Edit field options page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("o")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/resource_functions.php";

$field=strtolower(getvalescaped("field",""));
$fieldinfo=get_field($field);
$show_all_languages=getval("show_all_languages",false);

if (getval("save","")!="")
	{
	save_field_options($field);
	}

if (getval("newkeyword","")!="")
	{
	add_field_option($field,getvalescaped("newkeyword",""));
	}

include "../../include/header.php";

# Fetch option data.
$options=get_field_options_with_stats($field);
?>

<div class="BasicsBox"> 
<p>
<a href="team_fields.php">&lt;&nbsp;<?php echo $lang["backtofieldlist"]?></a>
</p>
<h1><?php echo $lang["managefieldoptions"] . " - " . $fieldinfo["title"]?></h1>
  
<?php if ($show_all_languages)
	{
  	?>
  	<p>&gt;&nbsp;<a href="team_fields_edit.php?field=<?php echo $field?>&show_all_languages="><?php echo $lang["hidealllanguages"]?></a></p>
	<?php
  	}
  else
  	{
	?>
  	<p>&gt;&nbsp;<a href="team_fields_edit.php?field=<?php echo $field?>&show_all_languages=true"><?php echo $lang["showalllanguages"]?></a></p>
  	<?php
  	}
?>
  
</div>

<form method=post>
<input type=hidden name="show_all_languages" value="<?php echo $show_all_languages?"true":""?>">
<input type=hidden name="save" value="true">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["keyword"]?></td>
<td><?php echo $lang["matchingresourcesheading"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
for ($n=0;$n<count($options);$n++)
	{
	$trans=i18n_get_translations($options[$n]["rawoption"]);

	# Work out the default language value (for the title and first field)
	$var="";
	if (array_key_exists($defaultlanguage,$trans)) {$var=$trans[$defaultlanguage];}
	?>
	<tr>
	<td><div class="ListTitle">
	<?php if ($show_all_languages) { ?><h2><?php echo $var?></h2><?php } ?>
	<table border="0" cellspacing="0" cellpadding="0" class="ListViewSubTable">
	<?php
	# Show the default language
	?>
	<tr>
	<td><?php echo $languages[$defaultlanguage]?></td>
	<td><input type="text" name="field_<?php echo $defaultlanguage?>_<?php echo $n?>" class="medwidth" value="<?php echo $var?>" /></td>
	</tr>
	<?php
	#�Also list all other languages
	foreach ($languages as $langcode=>$langname)
		{
		if ($langcode!=$defaultlanguage)
			{
			$var="";
			if (array_key_exists($langcode,$trans)) {$var=$trans[$langcode];}

			if ($show_all_languages)
				{
				?>
				<tr>
				<td><?php echo $langname?></td>
				<td width="20%"><input type="text" name="field_<?php echo $langcode?>_<?php echo $n?>" class="medwidth" value="<?php echo $var?>" /></td>
				</tr>
				<?php
				}
			else
				{
				# Not showing other languages - hide this field.
				?>
				<input type="hidden" name="field_<?php echo $langcode?>_<?php echo $n?>" value="<?php echo $var?>" />
				<?php
				}
			}
		}
	?>
	</table>
	<?php if ($show_all_languages) { ?><br /><?php } ?>
	</div></td>
	<td align="right" valign="top"><?php echo $options[$n]["count"]?></td>
	<td align="right" valign="top"><div class="ListTools">
	<input type="submit" name="submit_field_<?php echo $n?>" value="<?php echo $lang["save"]?>" />
	<input type="submit" name="delete_field_<?php echo $n?>" value="<?php echo $lang["delete"]?>" onClick="return confirm('<?php echo $lang["confirmdeletefieldoption"]?>');" />
	</div></td>
	</tr>
	<?php
	}
?>

</table>
</div>
</form>

<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="newkeyword"><?php echo $lang["addkeyword"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="newkeyword" id="newkeyword" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<?php		
include "../../include/footer.php";
?>