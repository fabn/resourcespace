<?php 
include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/search_functions.php";

$collection=getvalescaped("c","");
$collectiondata=get_collection($collection);
include "../include/header.php";
?>
  <div class="BasicsBox">
    <h2>&nbsp;</h2>
    <h1><?php echo $lang["videoplaylist"]. " - ".$collectiondata['name']?></h1>
    
<?php

$resources=do_search("!collection".$collection);


foreach ($resources as $resource){
	if (in_array($resource['resource_type'],$videotypes)){
	$videos[]=$resource;
	}
}

$flashpath="";
$title="";
for ($n=0;$n<count($videos);$n++){
	# FLV player - plays the FLV file created to preview video resources.
	$video=$videos[$n];
	$resource_flashpath="";
	if (file_exists(get_resource_path($video['ref'],true,"pre",false,$ffmpeg_preview_extension)))
		{
		$resource_flashpath=get_resource_path($video['ref'],false,"pre",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
		}
	else 
		{
		$resource_flashpath=get_resource_path($video['ref'],false,"",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
		}
	if ($resource_flashpath!=""){	
	$flashpath.=urlencode($resource_flashpath)."|";
	$title.=$video['title']."|";
	} 
}
$flashpath=substr($flashpath,0,-1);
$title=substr($title,0,-1);

# Choose a colour based on the theme.
# This is quite hacky, and ideally of course this would be CSS based, but the FLV player requires that the colour
# is passed as a parameter.
# The default is a neutral grey which should be acceptable for most user generated themes.
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$colour="505050";
if ($theme=="greyblu") {$colour="446693";}

?>
<object type="application/x-shockwave-flash" data="../lib/flashplayer/player_flv_multi.swf" width="<?php echo $ffmpeg_preview_max_width?>" height="<?php echo $ffmpeg_preview_max_height?>" class="Picture">
                    <param name="allowFullScreen" value="true" />

     <param name="movie" value="../lib/flashplayer/player_flv_multi.swf" />
     <param name="FlashVars" value="flv=<?php echo $flashpath?>&amp;width=<?php echo $ffmpeg_preview_max_width?>&amp;height=<?php echo $ffmpeg_preview_max_height?>&amp;margin=0&amp;buffer=10&amp;showvolume=1&amp;volume=200&amp;showtime=1&amp;autoload=0&amp;title=<?php echo $title;?>&amp;showfullscreen=1&amp;showstop=1&amp;playercolor=<?php echo $colour?>" />
</object>

</div>
<?php		
include "../include/footer.php";
?>
