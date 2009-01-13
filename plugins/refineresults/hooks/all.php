<?php

function HookRefineresultsSearchBeforesearchresults()
	{
	global $lang,$search;
	
	#if (substr($search,0,1)=="!") {return false;} # Only work for normal (non 'special') searches
	?>
	<p style="padding-bottom:0;margin-bottom:5px;"><a href="#" onClick="
	if ($('RefinePlus').innerHTML=='+')
		{
		Effect.SlideDown('RefineResults',{duration:0.5});
		$('RefinePlus').innerHTML='-';
		}
	else
		{
		Effect.SlideUp('RefineResults',{duration:0.5});
		$('RefinePlus').innerHTML='+';
		}
	"><span id='RefinePlus'>+</span> <?php echo $lang["refineresults"]?></a></p>
	<div class="RecordBox" id="RefineResults" style="display:none;">
	<div class="RecordPanel">  
	
	<form method="post">
	<div class="Question" id="question_related" >
	<label for="related"><?php echo $lang["additionalkeywords"]?></label>
	<input class="stdwidth" type=text name="refine_keywords" value="">
	<div class="clearerleft"> </div>
	</div>

	<div class="QuestionSubmit" style="padding-top:0;margin-top:0;margin-bottom:0;padding-bottom:0;">
	<label for="buttons"> </label>
	<input  name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["refine"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
	</div>
	<div class="PanelShadow"></div>
	</div>
	<?php
	
	return true;
	}

function HookRefineresultsSearchSearchstringprocessing()
	{
	global $search;
	$refine=getvalescaped("refine_keywords","");
	if ($refine!="")
		{
		$search.=" " . $refine;
		}
	}

?>