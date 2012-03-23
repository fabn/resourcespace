<?php

function HookVideo_spliceViewAfterresourceactions()
	{
	global $videosplice_resourcetype,$resource,$lang,$config_windows;
	
	if ($resource["resource_type"]!=$videosplice_resourcetype) {return false;} # Not the right type.


	if (getval("video_splice_cut_from_hours","")!="")
		{
		# Process actions
		$error="";
		
		# Receive input
		$fh=getvalescaped("video_splice_cut_from_hours","");
		$fm=getvalescaped("video_splice_cut_from_minutes","");
		$fs=getvalescaped("video_splice_cut_from_seconds","");
		
		$th=getvalescaped("video_splice_cut_to_hours","");
		$tm=getvalescaped("video_splice_cut_to_minutes","");
		$ts=getvalescaped("video_splice_cut_to_seconds","");
		
		$preview=getvalescaped("preview","")!="";
		
		# Calculate a duration, as needed by FFMPEG
		$from_seconds=($fh*60*60) + ($fm*60) + $fs;
		$to_seconds=($th*60*60) + ($tm*60) + $ts;
		$seconds=$to_seconds-$from_seconds;
		
		# Any problems?
		if ($seconds<=0) {$error = $lang["error-from_time_after_to_time"];}
		
		# Convert seconds to HH:MM:SS as required by FFmpeg.
		$dh=floor($seconds/(60*60));
		$dm=floor(($seconds-($dh*60*60))/60);
		$ds=floor($seconds-($dh*60*60)-($dm*60));
		
		# Show error message if necessary
		if ($error!="")
			{
			?>
			<script type="text/javascript">
			alert("<?php echo $error ?>");
			</script>
			<?php
			}
		else
			{
			# Process video.
			$ss=$fh . ":" . $fm . ":" . $fs;
			$t=str_pad($dh,2,"0",STR_PAD_LEFT) . ":" . str_pad($dm,2,"0",STR_PAD_LEFT) . ":" . str_pad($ds,2,"0",STR_PAD_LEFT);
			
			# Establish FFMPEG location.
			$ffmpeg_fullpath = get_utility_path("ffmpeg");

			# Work out source/destination
			global $ffmpeg_preview_extension,$ref;
			if (file_exists(get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension)))
				{
				$source=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
				}
			else 
				{
				$source=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
				}
			
			# Preview only?
			global $userref;
			if ($preview)
				{
				# Preview only.
				$target=get_temp_dir() . "/video_splice_preview_" . $userref . "." . $ffmpeg_preview_extension;
				}
			else
				{
				# Not a preview. Create a new resource.
				$newref=copy_resource($ref);
				$target=get_resource_path($newref,true,"",true,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
				
				# Set parent resource field details.
				global $videosplice_parent_field;
				update_field($newref,$videosplice_parent_field,$ref . ": " . $resource["field8"] . " [$fh:$fm:$fs - $th:$tm:$ts]");
				
				# Set created_by, archive and extension
				sql_query("update resource set created_by='$userref',archive=-2,file_extension='" . $ffmpeg_preview_extension . "' where ref='$newref'");
				}
			# Unlink the target
			if (file_exists($target)) {unlink ($target);}

			if ($config_windows)
				{
				# Windows systems have a hard time with the long paths used for video generation.
				$target_ext = strrchr($target, '.');
				$source_ext = strrchr($source, '.');
				$target_temp = get_temp_dir() . "/vs_t" . $newref . $target_ext;
				$target_temp = str_replace("/", "\\", $target_temp);
				$source_temp = get_temp_dir() . "/vs_s" . $ref . $source_ext;
				$source_temp = str_replace("/", "\\", $source_temp);
				copy($source, $source_temp);
				$shell_exec_cmd = $ffmpeg_fullpath . " -y -i " . escapeshellarg($source_temp) . " -ss $ss -t $t " . escapeshellarg($target_temp);
				$output = exec($shell_exec_cmd);
				rename($target_temp, $target);
				unlink($source_temp);
				}
			else
				{
				$shell_exec_cmd = $ffmpeg_fullpath . " -y -i " . escapeshellarg($source) . " -ss $ss -t $t " . escapeshellarg($target);
				$output = exec($shell_exec_cmd);
				}
			#echo "<p>" . $shell_exec_cmd . "</p>";

			# Generate preview/thumbs if not in preview mode
			if (!$preview)
				{
				include_once "../include/image_processing.php";
				create_previews($newref,false,$ffmpeg_preview_extension);

				# Add the resource to the user's collection.
				global $usercollection,$baseurl;
				add_resource_to_collection($newref,$usercollection);
				?>
				<script type="text/javascript">
				top.collections.location.href="<?php echo $baseurl ?>/pages/collections.php?nc=<?php echo time() ?>";
				</script>
				<?php

				}
			}
		}

?>
<li><a href="#" onClick="
if (document.getElementById('videocut').style.display=='block') {document.getElementById('videocut').style.display='none';} else {document.getElementById('videocut').style.display='block';} return false;">&gt;&nbsp;<?php echo $lang["action-cut"]?></a></li>
<form id="videocut" style="<?php if (!(isset($preview) && $preview)) { ?>display:none;<?php } ?>padding:10px 0 3px 0;" method="post">

<table>
<tr>
<td><?php echo $lang["from-time"]?></td>
<td><?php echo $lang["hh"]?><select name="video_splice_cut_from_hours">
<?php for ($n=0;$n<100;$n++) {?><option <?php if (isset($fh) && $fh==$n) { ?>selected<?php } ?>><?php echo str_pad($n, 2, "0", STR_PAD_LEFT) ?></option><?php } ?>
</select></td>
<td><?php echo $lang["mm"]?><select name="video_splice_cut_from_minutes">
<?php for ($n=0;$n<60;$n++) {?><option <?php if (isset($fm) && $fm==$n) { ?>selected<?php } ?>><?php echo str_pad($n, 2, "0", STR_PAD_LEFT) ?></option><?php } ?>
</select></td>
<td><?php echo $lang["ss"]?><select name="video_splice_cut_from_seconds">
<?php for ($n=0;$n<60;$n++) {?><option <?php if (isset($fs) && $fs==$n) { ?>selected<?php } ?>><?php echo str_pad($n, 2, "0", STR_PAD_LEFT) ?></option><?php } ?>
</select></td>
</tr>

<tr>
<td><?php echo $lang["to-time"]?></td>
<td><?php echo $lang["hh"]?><select name="video_splice_cut_to_hours">
<?php for ($n=0;$n<100;$n++) {?><option <?php if (isset($th) && $th==$n) { ?>selected<?php } ?>><?php echo str_pad($n, 2, "0", STR_PAD_LEFT) ?></option><?php } ?>
</select></td>
<td><?php echo $lang["mm"]?><select name="video_splice_cut_to_minutes">
<?php for ($n=0;$n<60;$n++) {?><option <?php if (isset($tm) && $tm==$n) { ?>selected<?php } ?>><?php echo str_pad($n, 2, "0", STR_PAD_LEFT) ?></option><?php } ?>
</select></td>
<td><?php echo $lang["ss"]?><select name="video_splice_cut_to_seconds">
<?php for ($n=0;$n<60;$n++) {?><option <?php if (isset($ts) && $ts==$n) { ?>selected<?php } ?>><?php echo str_pad($n, 2, "0", STR_PAD_LEFT) ?></option><?php } ?>
</select></td>
</tr>

<tr><td colspan=4 align="center">
<input type="submit" name="preview" value="<?php echo $lang["action-preview"]?>" style="width:40%;">
&nbsp;&nbsp;
<input type="submit" name="cut" value="<?php echo $lang["action-cut"]?>" style="width:40%;">
</td></tr>

</table>

<?php
if (isset($preview) && $preview)
	{
	# Show the preview
	
	# Work out a colour theme
	global $userfixedtheme;
	$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
	$colour="505050";
	if ($theme=="greyblu") {$colour="446693";}
	global $baseurl;
	
	# Embedded preview player
	?>
	<p align="center">
	<object type="application/x-shockwave-flash" data="../lib/flashplayer/player_flv_maxi.swf" width="240" height="135">
    <param name="allowFullScreen" value="true" />
	
     <param name="movie" value="../lib/flashplayer/player_flv_maxi.swf" />
     <param name="FlashVars" value="flv=<?php echo convert_path_to_url($target) ?>&amp;width=240&amp;height=135&amp;margin=0&amp;buffer=10&amp;showvolume=0&amp;volume=200&amp;showtime=0&amp;autoplay=1&amp;autoload=1&amp;showfullscreen=0&amp;showstop=0&amp;playercolor=<?php echo $colour?>" />
	</object>
	</p>
	<?php
	}
?>



</form>

	<?php
		
	return true;
	}
	
?>
