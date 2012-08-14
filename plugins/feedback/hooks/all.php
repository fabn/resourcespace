<?php

function HookFeedbackAllToptoolbaradder()
	{
	global $target,$baseurl,$feedback_questions,$feedback_prompt_text,$pagename;
	
	?><li><a target="<?php echo $target?>" href="<?php echo $baseurl?>/plugins/feedback/pages/feedback.php">User Survey</a></li>
	
	<?php
	if ($pagename=="setup" || $pagename=="feedback") {return true;} # Do not appear on the setup page or during giving feedback.
	
	# Form a check key based on the feedback form, so that form changes prompt a new message.
	# $check=md5(serialize($feedback_questions));
	
	if (getval("feedback_completed","")=="")
		{
		?>
		<div id="feedback_prompt" style="border:1px solid #BBB;border-bottom-width:3px;border-bottom-color:#bbb;background-color:white;width:300px;height:150px;position:absolute;top:100px;left:300px;text-align:left;padding:10px;color:black;">
		<?php echo $feedback_prompt_text; ?>
		
		<div style="text-align:right;">
		<input type="button" value="Yes" onClick="SetCookie('feedback_completed','yes',30);document.location.href='<?php echo $baseurl?>/plugins/feedback/pages/feedback.php';">
		<input type="button" value="No" onClick="SetCookie('feedback_completed','yes',30);document.getElementById('feedback_prompt').style.display='none';">
		<input type="button" value="Remind me later" onClick="SetCookie('feedback_completed','yes',0.5);document.getElementById('feedback_prompt').style.display='none';">
		</div>
		</div>
		<?php
		}
	}


?>