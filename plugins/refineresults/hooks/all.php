<?php

function HookRefineresultsSearchBeforesearchresults()
	{
	global $result,$lang,$search,$k,$archive,$parameters_string;
	if ($k!="" || !is_array($result) || count($result)==0) {return false;}
	
	#if (substr($search,0,1)=="!") {return false;} # Only work for normal (non 'special') searches
	?>
	<div class="SearchOptionNav"><a href="#" onClick="
	   function OnFinish(){
        $('refine_keywords').focus();
	}
	if ($('RefinePlus').innerHTML=='+')
		{
		Effect.SlideDown('RefineResults',{duration:0.5,afterUpdate:OnFinish});
		$('RefinePlus').innerHTML='&minus;';
		}
	else
		{
		Effect.SlideUp('RefineResults',{duration:0.5});
		$('RefinePlus').innerHTML='+';
		}
	"><span id='RefinePlus'>+</span> <?php echo $lang["refineresults"]?></a><?php if ($search!=""){?>&nbsp;&nbsp;<a href='search.php?search=<?php echo $parameters_string?>'>&gt;&nbsp;<?php echo $lang["clearsearch"]?></a><?php } ?></div>
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
	<select id="refinefields" name="refinefields" class="shrtwidth" >
	<option value="">Search All</option>
	<?php $refineresultsfields=get_advanced_search_fields();
		foreach($refineresultsfields as $field){
			?><option value="<?php echo $field['name']?>"><?php echo $field['title']?></option>
		<?php } ?>
	</select>
	<input class="stdwidth" type=text id="refine_keywords" name="refine_keywords" value="">
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
	$fieldspecific=getvalescaped("refinefields","");
	$refine=trim(getvalescaped("refine_keywords",""));
	if ($refine!="")
		{
		if ($fieldspecific!=""){$refine=$fieldspecific.":".$refine;}	
		$search.="," . $refine;	
		}
	$search=refine_searchstring($search);	
	}

?>
