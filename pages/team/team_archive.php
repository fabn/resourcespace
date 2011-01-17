<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("i")) {exit ("Permission denied.");}
include "../../include/general.php";

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["managearchiveresources"]?></h1>
  <p><?php echo text("introtext")?></p>
  
	<div class="VerticalNav">
	<ul>
	<li><a target="main" href="../edit.php?ref=-<?php echo $userref?>&single=true&archive=2"><?php echo $lang["newarchiveresource"]?></a></li>

	<li><a href="../search_advanced.php?archive=2"><?php echo $lang["searcharchivedresources"]?></a></li>

	<li><a href="../search.php?search=<?php echo urlencode("!archivepending")?>"><?php echo $lang["viewresourcespendingarchive"]?></a></li>

	</ul>
	</div>
	
	<p><a href="team_home.php">&gt;&nbsp;<?php echo $lang["backtoteamhome"]?></a></p>
  </div>

<?php
include "../../include/footer.php";
?>
