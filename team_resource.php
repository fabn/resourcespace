<?
include "include/db.php";
include "include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "include/general.php";

include "include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["manageresources"]?></h1>
  <p><?=text("introtext")?></p>

	<div class="VerticalNav">
	<ul>
	
	<? if (checkperm("c")) { ?>
	<li><a target="main" href="create.php"><?=$lang["addresource"]?></a></li>
    <li><a href="edit.php?ref=-<?=$userref?>&fancy=true"><?=$lang["addresourcebatchbrowser"]?></a></li>
    <li><a href="edit.php?ref=-<?=$userref?>"><?=$lang["addresourcebatchftp"]?></a></li>

    <li><a href="upload_fancy.php?replace=true"><?=$lang["replaceresourcebatch"]?></a></li>    
    
    <li><a href="team_copy.php"><?=$lang["copyresource"]?></a></li>
    <li><a href="search.php?search=<?=urlencode("!userpending")?>"><?=$lang["viewuserpending"]?></a></li>
    <? } ?>

	</ul>
	</div>
	
	<p><a href="team_home.php">&gt;&nbsp;<?=$lang["backtoteamhome"]?></a></p>
  </div>

<?
include "include/footer.php";
?>