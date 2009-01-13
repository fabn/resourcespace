<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["manageresources"]?></h1>
  <p><?php echo text("introtext")?></p>

	<div class="VerticalNav">
	<ul>
	
	<?php if (checkperm("c")) { ?>
	<li><a target="main" href="../create.php"><?php echo $lang["addresource"]?></a></li>
    <li><a href="../edit.php?ref=-<?php echo $userref?>&swf=true"><?php echo $lang["addresourcebatchbrowser"]?></a></li>
    <li><a href="../edit.php?ref=-<?php echo $userref?>"><?php echo $lang["addresourcebatchftp"]?></a></li>

    <li><a href="../upload_swf.php?replace=true"><?php echo $lang["replaceresourcebatch"]?></a></li>    
    
    <li><a href="team_copy.php"><?php echo $lang["copyresource"]?></a></li>
    <li><a href="../search.php?search=<?php echo urlencode("!userpending")?>"><?php echo $lang["viewuserpending"]?></a></li>

    <!--<li><a href="../search.php?search=<?php echo urlencode("!duplicates")?>"><?php echo $lang["viewduplicates"]?></a></li>-->
    
    <?php if (checkperm("k")) { ?>
    <li><a href="team_related_keywords.php"><?php echo $lang["managerelatedkeywords"]?></a></li>
    <li><a href="team_fields.php"><?php echo $lang["managefieldoptions"]?></a></li>
    <?php } ?>
    
    <?php } ?>

	</ul>
	</div>
	
	<p><a href="team_home.php">&gt;&nbsp;<?php echo $lang["backtoteamhome"]?></a></p>
  </div>

<?php
include "../../include/footer.php";
?>