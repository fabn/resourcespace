<?php

// Add 'Enable auto login' check box
function HookAuto_loginTeam_user_editAdditionaluserfields()
	{
	global $lang, $user;
	?>
	<div class="Question"><label><?php echo $lang["auto_login_enabled"]?></label>
		<input name="auto_login_enabled" type="checkbox" value="1" <?php
			if ($user['auto_login_enabled']==1) echo 'checked'; ?> />
		<div class="clearerleft"> </div></div>
	<div class="Question"><label><?php echo $lang["auto_login_ip"]?><br/>
		<?php echo $lang["wildcardpermittedeg"]?> 192.168.*</label>
		<input name="auto_login_ip" type="text" class="stdwidth" value="<?php
			echo $user["auto_login_ip"]?>">
		<div class="clearerleft"> </div></div>
	<?php
	}

// Save auto login setting
function HookAuto_loginTeam_user_editAftersaveuser()
	{
	global $ref;
	sql_query("update user set auto_login_enabled='".getvalescaped("auto_login_enabled","0")
			."', auto_login_ip='".getvalescaped('auto_login_ip', '')."' where ref='$ref'");
	}

?>
