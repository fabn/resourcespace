<?php

function HookVideo_spliceCollectionsCollectiontool()
	{
	# Link in collections bar (maximised)
	global $usercollection;
	?>
    <li><a target="main" href="../plugins/video_splice/pages/splice.php?collection=<?php echo $usercollection ?>">&gt; Splice</a></li>
	<?php
	}

function HookVideo_spliceCollectionsCollectiontoolmin()
	{
	# Link in collections bar (minimised)
	global $usercollection;
	?>
    <li><a target="main" href="../plugins/video_splice/pages/splice.php?collection=<?php echo $usercollection ?>">Splice</a></li>
	<?php
	}





?>