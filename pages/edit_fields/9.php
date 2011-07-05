<?php
global $baseurl;
?>

<div class="dynamickeywords">
<input type="text" class="stdwidth" value="<?php echo $lang["starttypingkeyword"]?>" onFocus="if (this.value=='<?php echo $lang["starttypingkeyword"]?>') {this.value='';}" onBlur="if (this.value=='') {this.value='<?php echo $lang["starttypingkeyword"]?>'};" name="<?php echo $name ?>_selector" id="<?php echo $name ?>_selector" />

<input type="hidden" name="<?php echo $name ?>" id="<?php echo $name ?>" value="<?php echo htmlspecialchars($value) ?>"/>

<div id="<?php echo $name?>_choices" class="autocomplete"></div>

<div id="<?php echo $name?>_selected" class="keywordsselected"></div>
</div>

<script type="text/javascript">

new Ajax.Autocompleter("<?php echo $name?>_selector", "<?php echo $name?>_choices", "<?php echo $baseurl?>/pages/edit_fields/9_ajax/suggest_keywords.php?field=<?php echo $fields[$n]["ref"] ?>",
	{
	afterUpdateElement : selectKeyword_<?php echo $name ?>
	}
);

var Keywords_<?php echo $name ?>= new Array();
var KeywordCounter_<?php echo $name ?>=0;
var KeywordsTranslated_<?php echo $name ?>= new Array();

function selectKeyword_<?php echo $name ?>()
	{
	var keyword=document.getElementById("<?php echo $name ?>_selector").value;
	if (keyword.substring(0,<?php echo strlen($lang["createnewentryfor"]) ?>)=="<?php echo $lang["createnewentryfor"] ?>")
		{
		keyword=keyword.substring(<?php echo strlen($lang["createnewentryfor"])+1 ?>);

		// Add the word.
		new Ajax.Request("<?php echo $baseurl?>/pages/edit_fields/9_ajax/add_keyword.php?field=<?php echo $fields[$n]["ref"] ?>&keyword=" + escape(keyword));
		}

	addKeyword_<?php echo $name ?>(keyword);
	updateSelectedKeywords_<?php echo $name ?>();
	document.getElementById('<?php echo $name ?>_selector').value='';
	}

function addKeyword_<?php echo $name ?>(keyword)
	{
	removeKeyword_<?php echo $name ?>(keyword); // remove any existing match in the list.
	Keywords_<?php echo $name ?>[KeywordCounter_<?php echo $name ?>]=keyword;
	KeywordCounter_<?php echo $name ?>++;
	}

function removeKeyword_<?php echo $name ?>(keyword)
	{
	var replacement=Keywords_<?php echo $name ?>;
	counter=0;
	for (var n=0;n<KeywordCounter_<?php echo $name ?>;n++)
		{
		if (keyword!=Keywords_<?php echo $name ?>[n]) {replacement[counter]=Keywords_<?php echo $name ?>[n];counter++;}
		}
	Keywords_<?php echo $name ?> = replacement;
	KeywordCounter_<?php echo $name ?> =counter;
	updateSelectedKeywords_<?php echo $name ?>();
	}

function updateSelectedKeywords_<?php echo $name ?>()
	{
	var html="";
	var value="";
	for (var n=0;n<KeywordCounter_<?php echo $name ?>;n++)
		{
		html+='<a href="#" onClick="removeKeyword_<?php echo $name ?>(\'' + Keywords_<?php echo $name ?>[n] +'\');return false;">[ x ]</a> &nbsp;' + Keywords_<?php echo $name ?>[n] + '<br/>';
		value+="," + resolveTranslated_<?php echo $name ?>(Keywords_<?php echo $name ?>[n]);
		}
	document.getElementById('<?php echo $name?>_selected').innerHTML=html;
	document.getElementById('<?php echo $name?>').value=value;
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
$options=trim_array(explode(",",$fields[$n]["options"]));
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
updateSelectedKeywords_<?php echo $name ?>();


</script>
<?php
/* include dirname(__FILE__) . "/../../include/user_select.php"; 
*/
?>