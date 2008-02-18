<?
# FLV player - plays the FLV file created to preview video resources.
$flashpath="../download.php?noattach=true&ref=$ref&ext=flv";
$flashpath=urlencode($flashpath);

$thumb=get_resource_path($ref,"pre",false,"jpg");
$thumb=urlencode($thumb);
?>
<object type="application/x-shockwave-flash" data="flashplayer/player_flv.swf" width="480" height="270" class="Picture">
     <param name="movie" value="flashplayer/player_flv.swf" />
     <param name="FlashVars" value="flv=<?=$flashpath?>&amp;width=480&amp;height=270&amp;startimage=<?=$thumb?>&amp;margin=0&amp;buffer=10&amp;autoload=1&amp;showstop=1&amp;playercolor=446693" />
</object>