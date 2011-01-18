<?php
/**
 * Team center home page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";

if ($send_statistics) {send_statistics();}

if (getval("showdisk","")!="")
	{
	# Some disk size allocation
	if (!file_exists($storagedir)) {mkdir($storagedir,0777);}
	$avail=disk_total_space($storagedir);
	$free=disk_free_space($storagedir);
	$used=$avail-$free;
	
	# Quota?
	$overquota=false;
	if (isset($disksize))
		{
		# Disk quota functionality. Calculate the usage by the $storagedir folder only rather than the whole disk.
		# Unix only due to reliance on 'du' command
		$avail=$disksize*(1024*1024*1024);
		$used=explode("\n",shell_exec("du -Lc --summarize ".escapeshellarg($storagedir)));$used=explode("\t",$used[count($used)-2]);$used=$used[0];
		$used=$used*1024;
		
		$free=$avail-$used;
		if ($free<=0) {$free=0;$used=$avail;$overquota=true;}
		}
	}
	
include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["teamcentre"]?></h1>
  <p><?php echo text("introtext")?></p>
  
	<div class="VerticalNav">
	<ul>
	
	<?php if (checkperm("c")) { ?><li><a href="team_resource.php"><?php echo $lang["manageresources"]?></a></li><?php } ?>

	<?php if (checkperm("i")) { ?><li><a href="team_archive.php"><?php echo $lang["managearchiveresources"]?></a></li><?php } ?>
	
	<?php if (checkperm("R")) { ?><li><a href="team_request.php"><?php echo $lang["managerequestsorders"]?></a></li><?php } ?>
	
    <?php if (checkperm("r")) { ?><li><a href="team_research.php"><?php echo $lang["manageresearchrequests"]?></a></li><?php } ?>

    <?php if (checkperm("u")) { ?><li><a href="team_user.php"><?php echo $lang["manageusers"]?></a></li><?php } ?>

    <?php if (checkperm("o")) { ?><li><a href="team_content.php"><?php echo $lang["managecontent"]?></a></li><?php } ?>
    
    <li><a href="team_stats.php"><?php echo $lang["viewstatistics"]?></a></li>
    
    <li><a href="team_report.php"><?php echo $lang["viewreports"]?></a></li>

    <?php if (checkperm("m")) { ?><li><a href="team_mail.php"><?php echo $lang["sendbulkmail"]?></a></li><?php } ?>

	<?php if (checkperm("a")) { ?>
    <li><a href="team_export.php"><?php echo $lang["exportdata"]?></a></li>
    <li><a href="../check.php"><?php echo $lang["installationcheck"]?></a></li>
	<?php } ?>
	
	<?php if (checkperm("a")) { ?>
	<?php if ($use_plugins_manager == true){ ?>
	<li><a href="team_plugins.php"><?php echo $lang["pluginssetup"]?></a></li>
	<?php } ?>
	<li><a href="../admin/index.php"><?php echo $lang["systemsetup"]?></a></li>
    <li><a href="team_reportbug.php"><?php echo $lang["reportbug"]?></a></li>
	<?php hook("customteamfunctionadmin")?>
	<?php } ?>
	
	</ul>
	</div>
	
<?php if (checkperm("u") && !checkperm("U")) { # Full admin access only to user/disk quota status
?>
<p><?php echo $lang["usersonline"]?>:
<?php
$active=get_active_users();
for ($n=0;$n<count($active);$n++) {if($n>0) {echo", ";}echo "<b>" . $active[$n]["username"] . "</b> (" . $active[$n]["t"] . ")";}
?>
</p>	

<p><?php echo $lang["diskusage"]?>:  <?php if (getval("showdisk","")!="") { ?><b><?php echo round(($avail?$used/$avail:0)*100,0)?>%</b> (<?php echo $lang["available"]?>: <?php echo formatfilesize($avail)?>; <?php echo $lang["used"]?>: <?php echo formatfilesize($used)?>; <?php echo $lang["free"]?>:  <?php echo formatfilesize($free)?>)<?php } else { ?><a href="team_home.php?showdisk=true"><?php echo $lang["action-view"]?></a><?php } ?>
</p>
<?php } ?>

</div>

<?php
include "../../include/footer.php";
?>