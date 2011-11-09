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
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$color="505050";$bgcolor1="666666";$bgcolor2="111111";$buttoncolor="999999";
if ($theme=="greyblu") {$color="446693";$bgcolor1="6883a8";$bgcolor2="203b5e";$buttoncolor="adb4bb";}	
if ($theme=="whitegry") {$color="ffffff";$bgcolor1="ffffff";$bgcolor2="dadada";$buttoncolor="666666";}	
if ($theme=="black") {$bgcolor1="666666";$bgcolor2="111111";$buttoncolor="999999";}	

$width=$ffmpeg_preview_max_width;
$height=$ffmpeg_preview_max_height;
if ($pagename=="search"){$width="355";$height=355/$ffmpeg_preview_max_width*$ffmpeg_preview_max_height;}
?>
<?php if(!hook("swfplayer")): ?>
<object type="application/x-shockwave-flash" data="../lib/flashplayer/player_flv_maxi.swf" width="<?php echo $width?>" height="<?php echo $height?>" class="Picture">
                    <param name="allowFullScreen" value="true" />

     <param name="movie" value="../lib/flashplayer/player_flv_maxi.swf" />
     <param name="FlashVars" value="flv=<?php echo $flashpath?>&amp;width=<?php echo $width?>&amp;height=<?php echo $height?>&amp;margin=0&amp;showvolume=1&amp;volume=200&amp;showtime=2&amp;autoload=1&amp;<?php if ($pagename!=="search"){?>showfullscreen=1<?php } ?>&amp;showstop=1&amp;buttoncolor=<?php echo $buttoncolor?>&playercolor=<?php echo $color?>&bgcolor=<?php echo $color?>&bgcolor1=<?php echo $bgcolor1?>&bgcolor2=<?php echo $bgcolor2?>&startimage=<?php echo $thumb?>&playeralpha=75&autoload=1&buffermessage=&buffershowbg=0" />
</object>
<?php endif; ?>

