<?php function HookColorthemerAllAdjustcolortheme()
{
	global $storagedir,$storageurl,$baseurl,$userfixedtheme,$defaulttheme,$css_reload_key,$pagename;
	// check if colorthemer theme is set

	if (is_numeric($userfixedtheme)){ 
		// check if theme exists yet, else use defaulttheme
		if (file_exists($storagedir."/colorthemes/".$userfixedtheme."/Col-".$userfixedtheme.".css")){ 
		?>
		<link href="<?php echo $storageurl;?>/colorthemes/<?php echo $userfixedtheme;?>/Col-<?php echo $userfixedtheme;?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
		<?php
		}
	 
	
		else { ?>
			<link href="<?php echo $baseurl?>/css/Col-<?php echo getval("colourcss",$defaulttheme)?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
			<?php
		}
		return true;
	}	 
	else { // theme isn't colorthemer_#
	return false;
	}

}

function HookColorthemerAllAdditionalheaderjs(){
	$theme="1";
	}
