<?php
function HookApi_coreChange_passwordAfterchangepasswordform(){
	global $lang,$baseurl;
	?>	<h1><?php echo $lang["apiaccess"]?></h1>
	<p><?php echo $lang["apiaccess-intro"]?></p>
	<div class="Question" id="api-core">
	&gt;&nbsp;<a onClick="return CentralSpaceLoad(this,true);" href="<?php echo $baseurl?>/plugins/api_core/index.php"><?php echo $lang["apiaccess"];?></a><br /><br />
	<div class="clearerleft"></div>

	</div><?php
}
