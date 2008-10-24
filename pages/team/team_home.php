<?
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";

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
	$used=explode("\n",shell_exec("du -Lc ".escapeshellarg($storagedir)));$used=explode("\t",$used[count($used)-2]);$used=$used[0];
	$used=$used*1024;
	
	$free=$avail-$used;
	if ($free<=0) {$free=0;$used=$avail;$overquota=true;}
	}

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["teamcentre"]?></h1>
  <p><?=text("introtext")?></p>
  
	<div class="VerticalNav">
	<ul>
	
	<? if (checkperm("c") && !$overquota) { ?><li><a href="team_resource.php"><?=$lang["manageresources"]?></a></li><? } ?>

	<? if ($overquota) { ?><li style="color:red;font-weight:bold;"><?=$lang["overquota"]?></li><? } ?>

	<? if (checkperm("i")) { ?><li><a href="team_archive.php"><?=$lang["managearchiveresources"]?></a></li><? } ?>
	
    <? if (checkperm("r")) { ?><li><a href="team_research.php"><?=$lang["manageresearchrequests"]?></a></li><? } ?>

    <? if (checkperm("u")) { ?><li><a href="team_user.php"><?=$lang["manageusers"]?></a></li><? } ?>

    <? if (checkperm("o")) { ?><li><a href="team_content.php"><?=$lang["managecontent"]?></a></li><? } ?>

    <? if (checkperm("k")) { ?><li><a href="team_related_keywords.php"><?=$lang["managerelatedkeywords"]?></a></li><? } ?>

    
    <li><a href="team_stats.php"><?=$lang["viewstatistics"]?></a></li>
    
    <li><a href="team_report.php"><?=$lang["viewreports"]?></a></li>

    <? if (checkperm("m")) { ?><li><a href="team_mail.php"><?=$lang["sendbulkmail"]?></a></li><? } ?>

    <li><a href="team_export.php"><?=$lang["exportdata"]?></a></li>

	<? if (checkperm("a")) { ?>
	<li><a target="main" href="../admin/index.php"><?=$lang["systemsetup"]?></a></li>
<?	
/*
if ($config_pluginmanager_enabled)
{
?>
	<li><a target="main" href="team_pluginmanager.php">Manage Software Plugins</a></li>
<?	
}
*/
?>
	<?hook("customteamfunctionadmin")?>
	<? } ?>
	
	</ul>
	</div>
	
<? if (checkperm("u") && !checkperm("U")) { # Full admin access only to user/disk quota status
?>
<p><?=$lang["usersonline"]?>:
<?
$active=get_active_users();
for ($n=0;$n<count($active);$n++) {if($n>0) {echo", ";}echo "<b>" . $active[$n]["username"] . "</b> (" . $active[$n]["t"] . ")";}
?>
</p>	

<p><?=$lang["diskusage"]?>:  <b><?=round(($avail?$used/$avail:0)*100,0)?>%</b> (<?=$lang["available"]?>: <?=formatfilesize($avail)?>; <?=$lang["used"]?>: <?=formatfilesize($used)?>; <?=$lang["free"]?>:  <?=formatfilesize($free)?>)
</p>
<? } ?>

</div>

<?
include "../../include/footer.php";
?>