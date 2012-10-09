<?php
global $baseurl,$pagename,$edit_autosave,$n,$fields;
$readonly=($pagename=="search_advanced");
?>

<div class="dynamickeywords">
<input type="text" class="stdwidth" value="<?php echo $lang["starttypingkeyword"]?>" onFocus="if (this.value=='<?php echo $lang["starttypingkeyword"]?>') {this.value='';}" onBlur="if (this.value=='') {this.value='<?php echo $lang["starttypingkeyword"]?>'};" name="<?php echo $name ?>_selector" id="<?php echo $name ?>_selector" />

<input type="hidden" name="<?php echo $name ?>" id="<?php echo $name ?>" value="<?php echo htmlspecialchars($value) ?>"/>


<div id="<?php echo $name?>_selected" class="keywordsselected"></div>
</div>

<script type="text/javascript">


	var Keywords_<?php echo $name ?>= new Array();
	var KeywordCounter_<?php echo $name ?>=0;
	var KeywordsTranslated_<?php echo $name ?>= new Array();

	function selectKeyword_<?php echo $name ?>(event, ui)
		{
		// var keyword=document.getElementById("<?php echo $name ?>_selector").value;
		var keyword=ui.item.value;
		
		if (keyword.substring(0,<?php echo mb_strlen($lang["createnewentryfor"], 'UTF-8') ?>)=="<?php echo $lang["createnewentryfor"] ?>")
			{
			keyword=keyword.substring(<?php echo mb_strlen($lang["createnewentryfor"], 'UTF-8')+1 ?>);

			// Add the word.
			jQuery.post("<?php echo $baseurl?>/pages/edit_fields/9_ajax/add_keyword.php?field=<?php echo $field["ref"] ?>&keyword=" + encodeURI(keyword));
			}

		addKeyword_<?php echo $name ?>(keyword);
		updateSelectedKeywords_<?php echo $name ?>(true);
		document.getElementById('<?php echo $name ?>_selector').value='';
		return false;
		}

	function addKeyword_<?php echo $name ?>(keyword)
		{
		removeKeyword_<?php echo $name ?>(keyword,false); // remove any existing match in the list.
		Keywords_<?php echo $name ?>[KeywordCounter_<?php echo $name ?>]=keyword;
		KeywordCounter_<?php echo $name ?>++;
		}

	function removeKeyword_<?php echo $name ?>(keyword,user_action)
		{
		var replacement=Keywords_<?php echo $name ?>;
		counter=0;
		for (var n=0;n<KeywordCounter_<?php echo $name ?>;n++)
			{
			if (keyword!=Keywords_<?php echo $name ?>[n]) {replacement[counter]=Keywords_<?php echo $name ?>[n];counter++;}
			}
		Keywords_<?php echo $name ?> = replacement;
		KeywordCounter_<?php echo $name ?> =counter;
		updateSelectedKeywords_<?php echo $name ?>(user_action);
		}

	function updateSelectedKeywords_<?php echo $name ?>(user_action)
		{
		var html="";
		var value="";
		for (var n=0;n<KeywordCounter_<?php echo $name ?>;n++)
			{
			html+='<a href="#" onClick="removeKeyword_<?php echo $name ?>(\'' + Keywords_<?php echo $name ?>[n] +'\',true);return false;">[ x ]</a> &nbsp;' + Keywords_<?php echo $name ?>[n] + '<br/>';
			value+="," + resolveTranslated_<?php echo $name ?>(Keywords_<?php echo $name ?>[n]);
			}
		document.getElementById('<?php echo $name?>_selected').innerHTML=html;
		document.getElementById('<?php echo $name?>').value=value;
		
		// Update the result counter, if the function is available (e.g. on Advanced Search).
		if( typeof( UpdateResultCount ) == 'function' )
			{
			UpdateResultCount();
			}
		<?php if ($edit_autosave) {?>if (user_action) {AutoSave('<?php echo $fields[$n]["ref"] ?>');}<?php } ?>
		}
		
	function resolveTranslated_<?php echo $name ?>(keyword)
		{
		if (typeof KeywordsTranslated_<?php echo $name ?>[keyword]=='undefined')
			{
			return keyword;
			}
		else
			{
			return KeywordsTranslated_<?php echo $name ?>[keyword];
			}
		}

	<?php 
	# Load translations - store original untranslated strings for each keyword, as this is what is actually set.
	$options=trim_array(explode(",",$field["options"]));
	for ($m=0;$m<count($options);$m++)
		{
		$trans=i18n_get_translated($options[$m]);
		if ($trans!="" && $trans!=$options[$m]) # Only add if actually different (i.e., an i18n string)
			{
			?>
			KeywordsTranslated_<?php echo $name ?>["<?php echo $trans ?>"]="<?php echo $options[$m] ?>";
			<?php
			}
		}


	# Select all selected options
	$options=trim_array(explode(",",$value));
	for ($m=0;$m<count($options);$m++)
		{
		$trans=i18n_get_translated($options[$m]);
		if ($trans!="")
			{
			?>
			addKeyword_<?php echo $name ?>("<?php echo $trans ?>");
			<?php
			}
		}
	?>

	jQuery('#<?php echo $name?>_selector').autocomplete( { source: "<?php echo $baseurl?>/pages/edit_fields/9_ajax/suggest_keywords.php?field=<?php echo $field["ref"] ?>&readonly=<?php echo $readonly ?>", 
		select : selectKeyword_<?php echo $name ?>
		});

	updateSelectedKeywords_<?php echo $name ?>(false);

</script>
<?php
/* include dirname(__FILE__) . "/../../include/user_select.php"; 
*/
?>
