<?php 
function HookVideo_spliceAllCollectiontoolcompact()
	{
	# Link in collections bar (minimised)
	global $usercollection,$lang;
	?>
    <option value="../plugins/video_splice/pages/splice.php?collection=<?php echo $usercollection ?>">&gt;&nbsp;<?php echo $lang["action-splice"]?>...</option>
	<?php
	}
