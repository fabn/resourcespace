<?php 

# Choose a colour based on the theme.
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$colour="505050";
if ($theme=="greyblu") {$colour="446693";}	
if ($theme=="whitegry") {$colour="999999";}	

# Flash MP3 Player was found at http://flash-mp3-player.net/players/maxi/
?>

<tr class="DownloadDBlend">
<td><h2><?php echo $lang["preview"] ?></h2></td>
<td align="center" colspan="2">

<object type="application/x-shockwave-flash" data="../lib/flashplayer/player_mp3_maxi.swf" width="200" height="20">
<param name="movie" value="../lib/flashplayer/player_mp3_maxi.swf" />
<param name="FlashVars" value="mp3=<?php echo $mp3path?>&width=200&bgcolor=<?php echo $colour?>&volume=200&showvolume=1" />
</object>


</td>
</tr>
