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

	# Set parent resource field details. Merge the contents of 
	global $videosplice_parent_field;
	$history="Merged from ";
	for ($n=0;$n<count($videos);$n++)
		{
		if ($n>0) {$history.=", ";}
		$crop_from=get_data_by_field($videos[$n]["ref"],$videosplice_parent_field);
		$history.=$videos[$n]["ref"] . ($crop_from!=""?" (cropped from $crop_from)":"");
		}
	update_field($ref,$videosplice_parent_field,$history);


	# Establish FFMPEG location.
	global $ffmpeg_path;
	$ffmpeg_path_working=$ffmpeg_path . "/ffmpeg";
	if (!file_exists($ffmpeg_path_working)) {$ffmpeg_path_working.=".exe";}
	$ffmpeg_path_working=escapeshellarg($ffmpeg_path_working);
	
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
			exit("Error: Video " . $videos[$n]["ref"] . " does not have an $ffmpeg_preview_extension file attached as it's main file. Was it the output of a crop operation?");
			}
		# Encode intermediary
		$intermediary=get_temp_dir() . "/video_splice_temp_" . $videos[$n]["ref"] . ".mpg";
		$shell_exec_cmd = $ffmpeg_path_working . " -y -i " . escapeshellarg($source) . " -sameq " . escapeshellarg($intermediary);
		#echo $shell_exec_cmd;
		$output=exec($shell_exec_cmd);
		
		$vidlist.=" " . escapeshellarg($intermediary);
		}
		
	# Target is the first file.
	$target=get_resource_path($ref,true,"",true,$ffmpeg_preview_extension,-1,1,false,"",-1,false);
		
	$shell_exec_cmd = "cat $vidlist > " . escapeshellarg($target . ".mpg"); # Combine all MPEGS to make one file (concat like this won't work for FLV, we have to convert to MPEG first)
	$output=exec($shell_exec_cmd);
	$shell_exec_cmd = $ffmpeg_path_working . " -y -i " . escapeshellarg($target . ".mpg") . " -sameq " . escapeshellarg($target);
	$output=exec($shell_exec_cmd);
	
	create_previews($ref,false,$ffmpeg_preview_extension);
	redirect("pages/view.php?ref=" . $ref);
	}
	
include "../../../include/header.php";
?>

<h1>Splice</h1>
<p>Splices several video resources together to form one combined video resource.</p>
<p>Drag and drop to rearrange the video clips.</p>
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
<input type="submit" name="splice" value="Splice" style="width:150px;">
</form>

<?php

include "../../../include/footer.php";

?>