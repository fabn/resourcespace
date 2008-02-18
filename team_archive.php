<?
include "include/db.php";
include "include/authenticate.php";if (!checkperm("i")) {exit ("Permission denied.");}
include "include/general.php";

include "include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["managearchiveresources"]?></h1>
  <p><?=text("introtext")?></p>
  
	<div class="VerticalNav">
	<ul>
	
	<li><a href="search_advanced.php?archive=2"><?=$lang["searcharchivedresources"]?></a></li>

	<li><a href="search.php?search=<?=urlencode("!archivepending")?>"><?=$lang["viewresourcespendingarchive"]?></a></li>

	</ul>
	</div>
	
	<p><a href="team_home.php">&gt;&nbsp;<?=$lang["backtoteamhome"]?></a></p>
  </div>

<?
include "include/footer.php";
?>