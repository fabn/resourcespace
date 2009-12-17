<?php

function HookTransformViewAfterresourceactions (){
	global $ref;
	global $access;
	global $lang;
	global $resource;
	global $cropper_allowed_extensions;
	// fixme - for some reason this isn't pulling from config default for plugin even when set as global
	// hack below makes it work, but need to figure this out at some point
	if (!isset($cropper_allowed_extensions)){
		$cropper_allowed_extensions = array('TIF','TIFF','JPG','JPEG','PNG','GIF','BMP','PSD'); // file formats that can be transformed
	} else {
		// in case these have been overriden, make sure these are all in uppercase.
		for($i=0;$i<count($cropper_allowed_extensions);$i++){
			$cropper_allowed_extensions[$i] = strtoupper($cropper_allowed_extensions[$i]);
		}	
	}

	if ($access==0 && $resource['has_image']==1 && in_array(strtoupper($resource['file_extension']),$cropper_allowed_extensions)){
		echo "&nbsp;&nbsp;<li><a href='../plugins/transform/pages/crop.php?ref=$ref'>&gt; ";
		echo $lang['transform'];
		echo "</a></li>";
		return true;
	}

}

?>
