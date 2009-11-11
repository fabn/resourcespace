<?php

function HookTransformViewAfterresourceactions (){
	global $ref;
	global $access;
	global $lang;
	global $resource;
	
	// fixme - for some reason this isn't pulling from config default for plugin even when set as global
	// hack below makes it work, but need to figure this out at some point
	if (!isset($cropper_allowed_extensions)){
		$cropper_allowed_extensions = array('TIF','TIFF','JPG','JPEG','PNG','GIF','BMP'); // file formats that can be transformed
	}

	if ($access==0 && checkperm('transform') && $resource['has_image']==1 && in_array(strtoupper($resource['file_extension']),$cropper_allowed_extensions)){
		echo "&nbsp;&nbsp;<li><a href='../plugins/transform/pages/crop.php?ref=$ref'>&gt; ";
		echo $lang['transform'];
		echo "</a></li>";
		return true;
	}

}

?>
