<?
# FLV player - plays the FLV file created to preview video resources.
$flashpath=get_resource_path($ref,false,"",false,$ffmpeg_preview_extension);
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
<object type="application/x-shockwave-flash" data="../lib/flashplayer/player_flv.swf" width="<?=$ffmpeg_preview_max_width?>" height="<?=$ffmpeg_preview_max_height?>" class="Picture">
     <param name="movie" value="../lib/flashplayer/player_flv.swf" />
     <param name="FlashVars" value="flv=<?=$flashpath?>&amp;width=<?=$ffmpeg_preview_max_width?>&amp;height=<?=$ffmpeg_preview_max_height?>&amp;margin=0&amp;buffer=10&amp;autoload=0&amp;showstop=1&amp;playercolor=<?=$colour?>" />
</object>