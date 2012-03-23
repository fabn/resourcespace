<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/authenticate.php";
include "../../../include/search_functions.php";
include "../../../include/resource_functions.php";
include "../../../include/image_processing.php";

if (getval("data","")!="")
	{
	$vars=array();
	parse_str(getval("data",""),$vars);

	$n=0;
	foreach($vars["splice_reel"] as $vid)
		{
		$n++;
		# Update the timestamp to reorder the collection. This is a bit hacky but changing how collections are ordered is probably quite a long winded task.
		sql_query("update collection_resource set date_added='" . date("Y-m-d H:i:s",time()-$n) . "' where collection='" . $usercollection . "' and resource='" . escape_check($vid) . "'");
		}
	?>
	top.collections.location.href="<?php echo $baseurl ?>/pages/collections.php?nc=<?php echo time() ?>";
	<?php
	exit();
	}

# Fetch videos
$videos=do_search("!collection" . $usercollection);


if (getval("splice","")!="" && count($videos)>1)
	{
	# Splice the videos together.
	$ref=copy_resource($videos[0]["ref"]);	# Base new resource on first video (top copy metadata).

	# Set parent resource field details.
	global $videosplice_parent_field;
	$resources="";
	for ($n=0;$n<count($videos);$n++)
		{
		if ($n>0) {$resources.=", ";}
		$crop_from=get_data_by_field($videos[$n]["ref"],$videosplice_parent_field);
		$resources.=$videos[$n]["ref"] . ($crop_from!="" ? " " . str_replace("%resourceinfo", $crop_from, $lang["cropped_from_resource"]) : "");
		}
	$history = str_replace("%resources", $resources, $lang["merged_from_resources"]);
	update_field($ref,$videosplice_parent_field,$history);


	# Establish FFMPEG location.
	$ffmpeg_fullpath = get_utility_path("ffmpeg");

	$vidlist="";

	# Create FFMpeg syntax to merge all additional videos.
	for ($n=0;$n<count($videos);$n++)
		{
		# Work out source/destination
		global $ffmpeg_preview_extension;
		if (file_exists(get_resource_path($videos[$n]["ref"],true,"",false,$ffmpeg_preview_extension)))
			{
			$source=get_resource_path($videos[$n]["ref"],true,"",false,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
			}
		else 
			{
			exit(str_replace(array("%resourceid", "%filetype"), array($videos[$n]["ref"], $ffmpeg_preview_extension), $lang["error-no-ffmpegpreviewfile"]));
			}
		# Encode intermediary
		$intermediary = get_temp_dir() . "/video_splice_temp_" . $videos[$n]["ref"] . ".mpg";
		if ($config_windows) {$intermediary = str_replace("/", "\\", $intermediary);}
		$shell_exec_cmd = $ffmpeg_fullpath . " -y -i " . escapeshellarg($source) . " -sameq " . escapeshellarg($intermediary);
		#echo $shell_exec_cmd;
		$output = exec($shell_exec_cmd);
		
		$vidlist.= " " . escapeshellarg($intermediary);
		}
	$vidlist = trim($vidlist);
	
	# Target is the first file.
	$target = get_resource_path($ref,true,"",true,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
	$targetmpg = $target . ".mpg";
	# Combine all MPEGS to make one file (this doesn't work for FLV, we had to convert to MPEG first)
	if ($config_windows)
		{
		$shell_exec_cmd = "copy/b " . str_replace(array(" ", "/"), array("+", "\\"), $vidlist) . " " . escapeshellarg($targetmpg);
		}
	else
		{
		$shell_exec_cmd = "cat $vidlist > " . escapeshellarg($targetmpg);
		}
	$output = exec($shell_exec_cmd);

	# Convert the MPEG file back to FLV.
	$shell_exec_cmd = $ffmpeg_fullpath . " -y -i " . escapeshellarg($targetmpg) . " -sameq " . escapeshellarg($target);
	if ($config_windows) {$shell_exec_cmd = str_replace("/", "\\", $shell_exec_cmd);}
	$output = exec($shell_exec_cmd);

	# Remove the temporary files.
	for ($n=0;$n<count($videos);$n++)
		{
		$intermediary = get_temp_dir() . "/video_splice_temp_" . $videos[$n]["ref"] . ".mpg";
		if ($config_windows) {$intermediary = str_replace("/", "\\", $intermediary);}
		unlink($intermediary);
		}
	unlink($targetmpg);

	# Update the file extension.
	$result = sql_query("update resource set file_extension = '$ffmpeg_preview_extension' where ref = '$ref' limit 1");

	# Create previews.
	create_previews($ref,false,$ffmpeg_preview_extension);
	redirect("pages/view.php?ref=" . $ref);
	}

include "../../../include/header.php";
?>

<h1><?php echo $lang["splice"]?></h1>
<p><?php echo $lang["intro-splice"]?></p>
<p><?php echo $lang["drag_and_drop_to_rearrange"]?></p>
<div id="splice_scroll" style="width:90%;height:140px;overflow:auto;background-image: url('../gfx/FilmStrip.gif');background-repeat:repeat-x;">
<div id="splice_reel" style="white-space:nowrap;padding:20px;">
<?php
foreach ($videos as $video)
	{
	if ($video["has_image"])
		{
		$img=get_resource_path($video["ref"],false,"col",false,$video["preview_extension"],-1,1,false,$video["file_modified"]);
		}
	else
		{
		$img="../../../gfx/" . get_nopreview_icon($video["resource_type"],$video["file_extension"],true);
		}
	?><img src="<?php echo $img ?>" id="splice_<?php echo $video["ref"] ?>" style="vertical-align:middle;padding:3px;"><?php
	}
?>
</div></div>

<script type="text/javascript">
Sortable.create("splice_reel", {
    onUpdate: function() {
        new Ajax.Request("splice.php", {
            method: "post",
            parameters: { data: Sortable.serialize("splice_reel") },
            evalJS: "force"
        });
    }
,tag:'img',scroll:'splice_scroll'

});
</script>

<form method="post">
<input type="submit" name="splice" value="<?php echo $lang["action-splice"]?>" style="width:150px;">
</form>

<?php

include "../../../include/footer.php";

?>