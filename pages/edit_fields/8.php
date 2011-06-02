<?php /* -------- Formatted CKeditor text area entry ---------------- */ ?>

<script type="text/javascript" src="../lib/ckeditor/ckeditor.js"></script>

<br /><br />
<textarea class="stdwidth" rows=20 cols=80 name="<?php echo $name?>" id="<?php echo $name?>" <?php echo $help_js; ?>><?php echo htmlspecialchars($value)?></textarea>


<script type="text/javascript">

// Replace the <textarea id="editor1"> with an CKEditor instance.
var editor = CKEDITOR.replace( '<?php echo $name?>',
	{
		// Defines a simpler toolbar to be used in this sample.
		// Note that we have added out "MyButton" button here.
		toolbar : [ [ 'Styles', 'Bold', 'Italic', 'Underline', 'RemoveFormat' ] ]
	});

</script>

