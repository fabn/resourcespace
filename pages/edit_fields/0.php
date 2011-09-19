<?php /* -------- Plain text entry ---------------- */ ?>
<input class="stdwidth" type=text name="<?php echo $name?>" id="<?php echo $name?>" value="<?php echo htmlspecialchars($value)?>" <?php echo $help_js; ?>
<?php if ($edit_autosave) {?>onChange="AutoSave('<?php echo $fields[$n]["ref"] ?>');"<?php } ?>
>