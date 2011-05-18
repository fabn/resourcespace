<?php 

# Choose a colour based on the theme.
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$color="505050";$bgcolor1="666666";$bgcolor2="111111";$buttoncolor="999999";
if ($theme=="greyblu") {$color="446693";$bgcolor1="6883a8";$bgcolor2="203b5e";$buttoncolor="adb4bb";}	
if ($theme=="whitegry") {$color="ffffff";$bgcolor1="ffffff";$bgcolor2="dadada";$buttoncolor="666666";}	
if ($theme=="black") {$bgcolor1="666666";$bgcolor2="111111";$buttoncolor="999999";}	
# Flash MP3 Player was found at http://flash-mp3-player.net/players/maxi/
?>
<?php if ($pagename!="search"){?>
<tr class="DownloadDBlend">
<td><h2><?php echo $lang["preview"] ?></h2></td>
<td align="center" colspan="2"><?php } ?><center><object type="application/x-shockwave-flash" data="../lib/flashplayer/player_mp3_maxi.swf" width="200" height="20" <?php if ($pagename=="search"){?>style="margin:0px;margin-top:-20px;margin-left:auto;margin-right:auto;"<?php }?>>
<param name="movie" value="../lib/flashplayer/player_mp3_maxi.swf" />
<param name="FlashVars" value="mp3=<?php echo $mp3path?>&width=200&buttoncolor=<?php echo $buttoncolor?>&playercolor=<?php echo $color?>&bgcolor=<?php echo $color?>&bgcolor1=<?php echo $bgcolor1?>&bgcolor2=<?php echo $bgcolor2?>&volume=100&showvolume=1" /></object></center><?php if ($pagename!="search"){?>
</td>
</tr>
<?php } ?>
