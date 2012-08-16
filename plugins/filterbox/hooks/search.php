<?php

function HookFilterboxSearchSearchbarreplace()
	{
	global $lang, $search, $archive, $baseurl, $autocomplete_search;
	include_once(dirname(__FILE__)."/../../../include/search_functions.php");
	?>

	<h2><?php echo $lang["filtertitle"]?></h2>
	<p><?php echo $lang["filtertext"]?></p>

	<form method="post">
	<div class="Question" id="question_related" style="border-top:none;">
	<input class="SearchWidth" type=text id="refine_keywords" name="refine_keywords" value=""
		   autofocus />
	<?php if ($autocomplete_search)
		{
		# Auto-complete search functionality
		?>
		<script type="text/javascript">
		jQuery(document).ready(function () {
		jQuery("#refine_keywords").autocomplete( { source: "<?php echo $baseurl?>/plugins/filterbox/ajax/autocomplete_filter.php" } );
			}
		</script>
	<?php
		}
	?>
	<input type=hidden name="archive" value="<?php echo $archive?>" />
	<input type=hidden name="search" value="<?php echo htmlspecialchars($search) ?>" />
	</div>

	<div class="QuestionSubmit"
		 style="padding-top:0;margin-top:0;margin-bottom:0;padding-bottom:0;">
	<input name="save" type="submit" value="&nbsp;&nbsp;<?php
			echo $lang["filterbutton"]?>&nbsp;&nbsp;" />
	</div>
	</form>

	</div>
	</div>
	<br />
	<div id="SearchBoxPanel">
	<div class="SearchSpace">
	<?php
	return false;
	}

global $basic_simple_search;
if ($basic_simple_search)
	{
	function HookFilterboxSearchSearchbarbeforebottomlinks()
		{
		global $lang;
		?>
		<p><a onClick="document.getElementById('ssearchbox').value=''; document.getElementById('basicyear').value='';document.getElementById('basicmonth').value='';">&gt; <?php echo $lang['clearbutton']?></a></p>
		<?php
		}
	}

function HookFilterboxSearchSearchstringprocessing()
	{
	global $search;
	$refine=trim(getvalescaped("refine_keywords", ""));
	if ($refine != "")
		$search .= ",".$refine;

	$search=refine_searchstring($search);
	}

?>
