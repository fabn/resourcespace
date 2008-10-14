<?
# multilingual_fields.php
# Allows language alternatives to be entered for free text metadata fields.
?>
<p><a href="#" class="OptionToggle" onClick="l=document.getElementById('LanguageEntry_<?=$n?>');if (l.style.display=='block') {l.style.display='none';this.innerHTML='<?=$lang["showtranslations"]?>';} else {l.style.display='block';this.innerHTML='<?=$lang["hidetranslations"]?>';} return false;"><?=$lang["showtranslations"]?></a></p>

<table class="OptionTable" style="display:none;" id="LanguageEntry_<?=$n?>">
<?

reset ($languages);
foreach ($languages as $langkey => $langname)
	{
	if ($language!=$langkey)
		{
		if (array_key_exists($langkey,$translations)) {$transval=$translations[$langkey];} else {$transval="";}
		?>
		<tr>
		<td nowrap valign="top"><?=htmlspecialchars($langname)?>&nbsp;&nbsp;</td>

		<?
		if ($fields[$n]["type"]==0)
			{
			?>
			<td><input type="text" class="stdwidth" name="multilingual_<?=$n?>_<?=$langkey?>" value="<?=htmlspecialchars($transval)?>"></td>
			<?
			}
		else
			{
			?>
			<td><textarea rows=6 cols=50 name="multilingual_<?=$n?>_<?=$langkey?>"><?=htmlspecialchars($transval)?></textarea></td>
			<?
			}
		?>
		</tr>
		<?	
		}
	}
?>
</table>