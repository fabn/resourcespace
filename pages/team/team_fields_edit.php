<?
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
	
# Fetch option data.
$options=get_field_options_with_stats($field);

include "../../include/header.php";
?>

<div class="BasicsBox"> 
<p>
<a href="team_fields.php">&lt;&nbsp;<?=$lang["backtofieldlist"]?></a>
</p>
<h1><?=$lang["managefieldoptions"] . " - " . $fieldinfo["title"]?></h1>
  
<? if ($show_all_languages)
	{
  	?>
  	<p>&gt;&nbsp;<a href="team_fields_edit.php?field=<?=$field?>&show_all_languages="><?=$lang["hidealllanguages"]?></a></p>
	<?
  	}
  else
  	{
	?>
  	<p>&gt;&nbsp;<a href="team_fields_edit.php?field=<?=$field?>&show_all_languages=true"><?=$lang["showalllanguages"]?></a></p>
  	<?
  	}
?>
  
</div>

<form method=post>
<input type=hidden name="show_all_languages" value="<?=$show_all_languages?"true":""?>">
<input type=hidden name="save" value="true">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?=$lang["keyword"]?></td>
<td><?=$lang["matchingresourcesheading"]?></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>

<?
for ($n=0;$n<count($options);$n++)
	{
	$trans=i18n_get_translations($options[$n]["rawoption"]);

	# Work out the default language value (for the title and first field)
	$var="";
	if (array_key_exists($defaultlanguage,$trans)) {$var=$trans[$defaultlanguage];}
	?>
	<tr>
	<td><div class="ListTitle">
	<? if ($show_all_languages) { ?><h2><?=$var?></h2><? } ?>
	<table border="0" cellspacing="0" cellpadding="0" class="ListViewSubTable">
	<?
	# Show the default language
	?>
	<tr>
	<td><?=$languages[$defaultlanguage]?></td>
	<td><input type="text" name="field_<?=$defaultlanguage?>_<?=$n?>" class="medwidth" value="<?=$var?>" /></td>
	</tr>
	<?
	# Also list all other languages
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
				<td><?=$langname?></td>
				<td width="20%"><input type="text" name="field_<?=$langcode?>_<?=$n?>" class="medwidth" value="<?=$var?>" /></td>
				</tr>
				<?
				}
			else
				{
				# Not showing other languages - hide this field.
				?>
				<input type="hidden" name="field_<?=$langcode?>_<?=$n?>" value="<?=$var?>" />
				<?
				}
			}
		}
	?>
	</table>
	<? if ($show_all_languages) { ?><br /><? } ?>
	</div></td>
	<td align="right" valign="top"><?=$options[$n]["count"]?></td>
	<td align="right" valign="top"><div class="ListTools">
	<input type="submit" name="submit_field_<?=$n?>" value="<?=$lang["save"]?>" />
	<input type="submit" name="delete_field_<?=$n?>" value="<?=$lang["delete"]?>" />
	</div></td>
	</tr>
	<?
	}
?>

</table>
</div>
</form>

<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="newkeyword"><?=$lang["addkeyword"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="newkeyword" id="newkeyword" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<?		
include "../../include/footer.php";
?>