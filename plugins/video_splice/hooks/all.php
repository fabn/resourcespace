<?php 
function HookVideo_spliceAllCollectiontoolcompact1($collection, $count_result){
	# Link in collections bar (minimised)
	global $collection,$lang,$pagename;
	if ($pagename=="collections" && $count_result!=0){
	?>
    <option value="<?php echo $collection?>|0|0|../plugins/video_splice/pages/splice.php?collection=<?php echo $collection ?>|main|false">&gt;&nbsp;<?php echo $lang["action-splice"]?>...</option>
	<?php
	}
}
