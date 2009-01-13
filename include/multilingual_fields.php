<?php
# multilingual_fields.php
# Allows language alternatives to be entered for free text metadata fields.
?>
<p><a href="#" class="OptionToggle" onClick="l=document.getElementById('LanguageEntry_<?php echo $n?>');if (l.style.display=='block') {l.style.display='none';this.innerHTML='<?php echo $lang["showtranslations"]?>';} else {l.style.display='block';this.innerHTML='<?php echo $lang["hidetranslations"]?>';} return false;"><?php echo $lang["showtranslations"]?></a></p>

<table class="OptionTable" style="display:none;" id="LanguageEntry_<?php echo $n?>">
<?php

reset ($languages);
foreach ($languages as $langkey => $langname)
	{
	if ($language!=$langkey)
		{
		if (array_key_exists($langkey,$translations)) {$transval=$translations[$langkey];} else {$transval="";}
		?>
		<tr>
		<td nowrap valign="top"><?php echo htmlspecialchars($langname)?>&nbsp;&nbsp;</td>

		<?php
		if ($fields[$n]["type"]==0)
			{
			?>
			<td><input type="text" class="stdwidth" name="multilingual_<?php echo $n?>_<?php echo $langkey?>" value="<?php echo htmlspecialchars($transval)?>"></td>
			<?php
			}
		else
			{
			?>
			<td><textarea rows=6 cols=50 name="multilingual_<?php echo $n?>_<?php echo $langkey?>"><?php echo htmlspecialchars($transval)?></textarea></td>
			<?php
			}
		?>
		</tr>
		<?php	
		}
	}
?>
</table>