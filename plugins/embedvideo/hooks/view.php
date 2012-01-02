<?php

function HookEmbedvideoViewAfterresourceactions()
	{
	global $embedvideo_resourcetype,$ffmpeg_preview_extension,$resource,$ref,$ffmpeg_preview_max_width,$ffmpeg_preview_max_height,$userfixedtheme,$baseurl,$lang;
	
	if ($resource["resource_type"]!=$embedvideo_resourcetype) {return false;} # Not the right type.
	?>

	<?php
# FLV player - plays the FLV file created to preview video resources.

if (file_exists(get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension)))
	{
	$flashpath=get_resource_path($ref,false,"pre",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
	}
else 
	{
	$flashpath=get_resource_path($ref,false,"",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
	}
$flashpath=urlencode($flashpath);

$thumb=get_resource_path($ref,false,"pre",false,"jpg"); 
$thumb=urlencode($thumb);

# Choose a colour based on the theme.
# This is quite hacky, and ideally of course this would be CSS based, but the FLV player requires that the colour
# is passed as a parameter.
# The default is a neutral grey which should be acceptable for most user generated themes.
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$colour="505050";
if ($theme=="greyblu") {$colour="446693";}

?>
<li><a href="#" onClick="
if (document.getElementById('embedvideo').style.display=='block') {document.getElementById('embedvideo').style.display='none';} else {document.getElementById('embedvideo').style.display='block';}
if (document.getElementById('embedvideo2').style.display=='block') {document.getElementById('embedvideo2').style.display='none';} else {document.getElementById('embedvideo2').style.display='block';}
 return false;">&gt;&nbsp;<?php echo $lang["embed"]?></a></li>
<p id="embedvideo2" style="display:none;float:left;padding:10px 0 3px 0;"><?php echo $lang["embed_help"] ?></p>
<textarea id="embedvideo" style="width:335px;height:200px;display:none;"><?php if (!hook("replaceembedcode")){?><?php echo htmlspecialchars('
<object type="application/x-shockwave-flash" data="' . $baseurl . '/lib/flashplayer/player_flv_maxi.swf" width="' . $ffmpeg_preview_max_width . '" height="' . $ffmpeg_preview_max_height . '" class="Picture"><param name="allowFullScreen" value="true" /><param name="movie" value="' . $baseurl . '/lib/flashplayer/player_flv_maxi.swf" /><param name="FlashVars" value="flv=' . $flashpath . '&amp;width=' . $ffmpeg_preview_max_width . '&amp;height=' . $ffmpeg_preview_max_height . '&amp;margin=0&amp;buffer=10&amp;showvolume=1&amp;volume=200&amp;showtime=1&amp;autoplay=1&amp;autoload=0&amp;showfullscreen=1&amp;showstop=1&amp;playercolor=' . $colour . '&startimage=' . $thumb . '" /></object>
'); ?><?php } // end hook replaceembedcode ?></textarea>

	<?php
		
	return true;
	}
	
?>
