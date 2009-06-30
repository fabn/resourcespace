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
	
	<?php if (checkperm("c")): // Check if user can create resources ?>

		<?php if($upload_methods['single_upload']): // Test if single upload is allowed. ?>
			<li><a target="main" href="../create.php"><?php echo $lang["addresource"]?></a></li>
		<?php endif // Test if single upload is allowed ?>

		<?php if($upload_methods['in_browser_upload']): // Test if in browser is allowed. ?>
			<li><a href="../edit.php?ref=-<?php echo $userref?>&amp;swf=true"><?php echo $lang["addresourcebatchbrowser"]?></a></li>
		<?php endif // Test if in browser upload is allowed. ?>

		<?php if($upload_methods['in_browser_upload_java']): // Test if in browser is allowed. ?>
			<li><a href="../edit.php?ref=-<?php echo $userref?>&amp;java=true"><?php echo $lang["addresourcebatchbrowserjava"]?></a></li>
		<?php endif // Test if in browser upload is allowed. ?>

		<?php if($upload_methods['fetch_from_ftp']): // Test if fetching resources from FTP is allowed. ?>
			<li><a href="../edit.php?ref=-<?php echo $userref?>"><?php echo $lang["addresourcebatchftp"]?></a></li>
		<?php endif // Test if fetching resources from FTP is allowed. ?>

		<?php if($upload_methods['fetch_from_local_folder']): // Test if fetching resources from local upload folder is allowed. ?>
			<li><a href="../edit.php?ref=-<?php echo $userref?>&amp;local=true"><?php echo $lang["addresourcebatchlocalfolder"]?></a></li>
		<?php endif // Test if fetching resources from local upload folder is allowed. ?>

		<li><a href="../upload_swf.php?replace=true"><?php echo $lang["replaceresourcebatch"]?></a></li>    

		<li><a href="team_copy.php"><?php echo $lang["copyresource"]?></a></li>
		<li><a href="../search.php?search=&archive=-1"><?php echo $lang["viewuserpending"]?></a></li>

		<li><a href="../search.php?search=!contributions<?php echo $userref?>&archive=-2"><?php echo $lang["viewcontributedps"]?></a></li>

		<!--<li><a href="../search.php?search=<?php echo urlencode("!duplicates")?>"><?php echo $lang["viewduplicates"]?></a></li>-->
		<li><a href="../search.php?search=<?php echo urlencode("!unused")?>"><?php echo $lang["viewuncollectedresources"]?></a></li>
		<?php if (checkperm("k")): // Check if user can manage keywords and fields ?>
			<li><a href="team_related_keywords.php"><?php echo $lang["managerelatedkeywords"]?></a></li>
			<li><a href="team_fields.php"><?php echo $lang["managefieldoptions"]?></a></li>
		<?php endif // Check if user can manage keywords and fields ?>

	<?php endif // Check if user can create resources ?>

	</ul>
	</div>

	<p><a href="team_home.php">&gt;&nbsp;<?php echo $lang["backtoteamhome"]?></a></p>
  </div>

<?php
include "../../include/footer.php";
?>
