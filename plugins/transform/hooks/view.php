<?php

function HookTransformViewAfterresourceactions (){
	global $ref;
	global $access;
	global $lang;
	if ($access==0 && checkperm('transform')){
		echo "&nbsp;&nbsp;<li><a href='../plugins/transform/pages/crop.php?ref=$ref'>&gt; ";
		echo $lang['transform'];
		echo "</a></li>";
		return true;
	}

}

?>
