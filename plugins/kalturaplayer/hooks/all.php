<?php
function HookKalturaplayerAllAdditionalheaderjs(){
	global $baseurl;
	?>


	<script type="text/javascript" src="<?php echo $baseurl?>/lib/js/jquery-1.6.1.min.js" ></script>
		
	<style type="text/css">
		@import url("<?php echo $baseurl?>/plugins/kalturaplayer/lib/kaltura-html5player-widget.1.6a/skins/jquery.ui.themes/kaltura-dark/jquery-ui-1.7.2.css");
	</style>  
	<style type="text/css">
		@import url("<?php echo $baseurl?>/plugins/kalturaplayer/lib/kaltura-html5player-widget.1.6a/mwEmbed-player-static.css");
	</style>	
	<script type="text/javascript" src="<?php echo $baseurl?>/plugins/kalturaplayer/lib/kaltura-html5player-widget.1.6a/mwEmbed-player-static.js" ></script>
<?php }

function HookKalturaplayerAllSwfplayer(){
	global $ref,$baseurl,$flashpath, $width,$height,$thumb,$pagename,$userfixedtheme,$ffmpeg_preview_max_width,$ffmpeg_preview_max_height;
	if ($pagename=="search"){global $result,$n;$resource=$result[$n];} else {global $resource;}
	
	$flashpath=str_replace(urlencode($baseurl),"",$flashpath);

	$flashpath=urldecode($baseurl.$flashpath);
	$width=$ffmpeg_preview_max_width;
	$height=$ffmpeg_preview_max_height;
	if ($pagename=="search"){$width="355";$height=355/$ffmpeg_preview_max_width*$ffmpeg_preview_max_height;}
	?>
	
<div class="Picture" id="videoContainer" style="width:<?php echo $width?>px;height:<?php echo $height?>px;">
	<video id="vid<?php echo $ref?>" width="<?php echo $width?>" height="<?php echo $height?>" poster="<?php echo urldecode($thumb);?>" durationHint="33">
	<source src="<?php echo $flashpath;?>"/>
	<?php if (isset($resource['ffmpeg_alt_previews']) && $resource['ffmpeg_alt_previews']!=''){
		$alt_files=explode(",",$resource['ffmpeg_alt_previews']);
		foreach ($alt_files as $alt_file){ 
			$alt_file_path=dirname($flashpath)."/".$alt_file;
			?><source src="<?php echo $alt_file_path;?>"/><?php 
		}
	}
	?>
	</video>
</div>

	<?php
	return true;
}


function HookKalturaplayerViewReplaceembedcode(){
	global $baseurl,$ffmpeg_preview_max_height,$ffmpeg_preview_max_width, $flashpath, $thumb,$colour,$height,$width,$alt_file_path,$resource;
	echo htmlspecialchars('<script type="text/javascript" src="'. $baseurl.'/plugins/kalturaplayer/lib/kaltura-html5player-widget.1.6a/mwEmbed-player-static.js"></script><video poster="'.urldecode($thumb).'" style="width:'.$width.'px;height:'.$height.'px;" ><source src="'.$flashpath.'" >');
	
	if (isset($resource['ffmpeg_alt_previews']) && $resource['ffmpeg_alt_previews']!=''){
		$alt_files=explode(",",$resource['ffmpeg_alt_previews']);
		foreach ($alt_files as $alt_file){ 
			$alt_file_path=dirname($flashpath)."/".$alt_file;
			echo htmlspecialchars('</source><source src="'.$alt_file_path.'"/>');
		}
	}
	echo htmlspecialchars('</video>');
return true;
}
