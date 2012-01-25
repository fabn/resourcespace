<?php

function HookNewsTeam_homeCustomteamfunction()
	{
	global $baseurl;
	
    if (checkperm("o"))
		{
		
		?><li><a href="<?php echo $baseurl ?>/plugins/news/pages/news_edit.php">Manage News Items</a></li>
		<?php
		}
		?>
	<?php
	}




