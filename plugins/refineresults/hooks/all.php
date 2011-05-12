<?php

function HookRefineresultsSearchBeforesearchresults()
	{
	global $lang,$search,$k,$archive;
	if ($k!="") {return false;}
	
	#if (substr($search,0,1)=="!") {return false;} # Only work for normal (non 'special') searches
	?>
	<div class="SearchOptionNav"><a href="#" onClick="
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
	"><span id='RefinePlus'>+</span> <?php echo $lang["refineresults"]?></a></div>
	<?php $origsearch=getval("origsearch","");
	if ($origsearch!=$search && strlen($search)>strlen($origsearch)){?>&gt;&nbsp;<a href="search.php?clearrefine=true&search=<?php echo $origsearch?>"><?php echo $lang["returntooriginalresults"]?></a><?php } ?>
	<?php
	return true;
	}
	
function HookRefineresultsSearchBeforesearchresultsexpandspace()
	{
	global $lang,$search,$k,$archive;
	if ($k!="") {return false;}
	?>
	<div class="clearerleft"></div>
	<div class="RecordBox" id="RefineResults" style="display:none;">
	<div class="RecordPanel">  
	
	<form method="post">
	<div class="Question" id="question_related" style="border-top:none;">
	<label for="related"><?php echo $lang["additionalkeywords"]?></label>
	<input class="stdwidth" type=text name="refine_keywords" value="">
	<input type=hidden name="archive" value="<?php echo $archive?>">
	<input type=hidden name="search" value="<?php echo htmlspecialchars($search) ?>">
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

	$totalrefinementarray=array();
	// clear refineresults cookie if 'back to original'
	if (getval("clearrefine",false)){
		setcookie("refineresults","");
		setcookie("origsearch","");
	}
	else {
	// create a cookie with any new refine keywords and current refineresults cookie. 
	$refine=trim(getvalescaped("refine_keywords",""));
	$refineresults=getvalescaped("refineresults","");
	$totalrefinement=$refineresults.",".$refine;
	$totalrefinementarray=explode(",",$totalrefinement);
	$totalrefinementarray=array_unique($totalrefinementarray);
	$totalrefinementarray=array_filter($totalrefinementarray);
	}
	
	$totalrefinement=implode(",",$totalrefinementarray);
	$refine=$totalrefinement;

	if ($refine!="")
		{
		setcookie("refineresults",$refine);
		}
	
	// to enable 'back to original search', we need to separate the refinements from the search
	// set a cookie with the original search in it, using the current search minus any refinements.
	$searcharray=explode(",",$search);

	$origsearch=array();
	foreach ($searcharray as $searchitem){
		if (!in_array($searchitem,$totalrefinementarray)){
			$origsearch[]=trim($searchitem);
		}
	}

	$origsearch=trim(implode(",",$origsearch));
	setcookie("origsearch",$origsearch);


	if ($origsearch!="" && $refine!=""){$search=$origsearch.",".$refine;}
	else if ($refine!=""){$search=$refine;}

	
	if (getval("clearrefine",false)){
		setcookie("origsearch",$search);
		setcookie("search","");
	}

	}

?>
