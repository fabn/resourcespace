<?
# FLV player - plays the FLV file created to preview video resources.
$flashpath="../../pages/download.php?noattach=true&ref=$ref&ext=flv";
$flashpath=urlencode($flashpath);

$thumb="../".get_resource_path($ref,false,"pre",false,"jpg");
$thumb=urlencode($thumb);

# Choose a colour based on the theme.
# This is quite hacky, and ideally of course this would be CSS based, but the FLV player requires that the colour
# is passed as a parameter.
# The default is a neutral grey which should be acceptable for most user generated themes.
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$colour="505050";
if ($theme=="greyblu") {$colour="446693";}

?>
<object type="application/x-shockwave-flash" data="../lib/flashplayer/player_flv.swf" width="480" height="270" class="Picture">
     <param name="movie" value="flashplayer/player_flv.swf" />
     <param name="FlashVars" value="flv=<?=$flashpath?>&amp;width=480&amp;height=270&amp;margin=0&amp;buffer=10&amp;autoload=0&amp;showstop=1&amp;playercolor=<?=$colour?>" />
</object>