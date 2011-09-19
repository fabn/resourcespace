<?php /* -------- Text area entry ---------------- */ ?>
<textarea class="stdwidth" rows=6 cols=50 name="<?php echo $name?>" id="<?php echo $name?>" <?php echo $help_js; ?>
<?php if ($edit_autosave) {?>onChange="AutoSave('<?php echo $fields[$n]["ref"] ?>');"<?php } ?>
><?php echo htmlspecialchars($value)?></textarea>