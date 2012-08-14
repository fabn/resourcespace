<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("a")) {exit ("Permission denied.");}
include "../../../include/general.php";

if (!isset($feedback_prompt_text)) {$feedback_prompt_text="";}

if (getval("submit","")!="" || getval("add","")!="")
	{
	
	$f=fopen("../config/config.php","w");
	fwrite($f,"<?php\n\n\$feedback_questions=array();");

	fwrite($f,"\n\n\$feedback_prompt_text=\"" . str_replace("\"","\\\"",getval("feedback_prompt_text","")) . "\";\n\n");
	
	$readfrom=0;
	if (getval("delete_1","")!="") {$readfrom++;} # Delete first question.
			
	for ($n=1;$readfrom<count($feedback_questions);$n++)
		{
		$readfrom++;

		# Deleting next question? Skip ahead
		if (getval("delete_" . ($readfrom),"")=="")
			{	
			# Save question
			fwrite ($f,"\$feedback_questions[" . $n . "]['text']=\"" . str_replace("\"","\\\"",getval("text_" . $readfrom,"")) . "\";\n");
			fwrite ($f,"\$feedback_questions[" . $n . "]['type']=" . getval("type_" . $readfrom,1) . ";\n");
			fwrite ($f,"\$feedback_questions[" . $n . "]['options']=\"" . str_replace("\"","\\\"",getval("options_" . $readfrom,"")) . "\";\n");
			}		
		else
			{
			$n--;
			}

		# Add new question after this one?
		if (getval("add_" . $readfrom,"")!="")
			{
			$n++;
			fwrite ($f,"\$feedback_questions[" . $n . "]['text']=\"\";\n");
			fwrite ($f,"\$feedback_questions[" . $n . "]['type']=1;\n");
			fwrite ($f,"\$feedback_questions[" . $n . "]['options']=\"\";\n");
			}
		}
	
	$add="";
	if (getval("add","")!="")
		{
		# Add a new question
		fwrite ($f,"\$feedback_questions[" . $n . "]['text']=\"\";\n");
		fwrite ($f,"\$feedback_questions[" . $n . "]['type']=1;\n");
		fwrite ($f,"\$feedback_questions[" . $n . "]['options']=\"\";\n");
		$add="#add";
		}

	fwrite($f,"?>");
	fclose($f);
	redirect("plugins/feedback/pages/setup.php?nc=". time() . $add);
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>User Feedback Configuration</h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p>Pop-up prompt box text:<br />
<textarea rows=6 cols=50 style="width:600px;" name="feedback_prompt_text"><?php echo $feedback_prompt_text ?></textarea>
</p>
<h2>Questions</h2>
<hr />

<?php for ($n=1;$n<=count($feedback_questions);$n++)
	{
	?>
   <p>Type:
   <select name="type_<?php echo $n?>">
   <option value="1" <?php if ($feedback_questions[$n]["type"]==1) { ?>selected<?php } ?>>Small Text Field</option>
   <option value="2" <?php if ($feedback_questions[$n]["type"]==2) { ?>selected<?php } ?>>Large Text Field</option>
   <option value="3" <?php if ($feedback_questions[$n]["type"]==3) { ?>selected<?php } ?>>List: Single Selection</option>
   <option value="5" <?php if ($feedback_questions[$n]["type"]==5) { ?>selected<?php } ?>>List: Multiple Selection</option>
   <option value="4" <?php if ($feedback_questions[$n]["type"]==4) { ?>selected<?php } ?>>Label</option>
   </select>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   <input type="checkbox" name="delete_<?php echo $n?>" value="yes"> Delete this question?
   <input type="checkbox" name="add_<?php echo $n?>" value="yes"> Add new question after this one?
	</p>

	<p>
Text / HTML:<br/>
   <textarea rows=3 cols=50 style="width:600px;" name="text_<?php echo $n?>"><?php echo $feedback_questions[$n]["text"] ?></textarea>
   </p>
	
	<p>Options: (comma separated) <br />
   	<textarea rows=2 cols=50 style="width:600px;" name="options_<?php echo $n?>"><?php echo $feedback_questions[$n]["options"] ?></textarea>
   	</p>
   
	<hr />
	<?php
	}
?>
<br /><br /><a name="add"></a>
<input type="submit" name="add" value="Add New Field">   

<input type="submit" name="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;">   

<br/><br/>
<p>&lt; <a href="../../../pages/team/team_plugins.php">Back to Plugin Manager</a></p>

</form>
</div>

<?php include "../../../include/footer.php"; ?>	