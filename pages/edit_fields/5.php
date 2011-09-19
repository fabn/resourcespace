<?php /* -------- Larger area entry ---------------- */ ?>
<textarea class="stdwidth" rows=20 cols=80 name="<?php echo $name?>" id="<?php echo $name?>" <?php echo $help_js; ?>
<?php if ($edit_autosave) {?>onChange="AutoSave('<?php echo $fields[$n]["ref"]?>');"<?php } ?>
><?php echo htmlspecialchars($value)?></textarea>
