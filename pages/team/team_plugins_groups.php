<?php
/**
 * Plugins management interface (part of team center) - Group access control
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 * @author Dan Huby
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "../../include/general.php";

$plugin=getvalescaped("plugin","");

# Fetch current access level
$access=sql_value("select enabled_groups value from plugins where name='$plugin'","");


# Fetch user groups
$groups=get_usergroups();

# Save group activation options
if (getval("save","")!="")
	{
	$access="";
	if (getval("access","")=="some")
		{
		foreach ($groups as $group)
			{
			if (getval("group_" . $group["ref"],"")!="")
				{
				if ($access!="") {$access.=",";}
				$access.=$group["ref"];
				}
			}
		
		}
	# Update database
	sql_query("update plugins set enabled_groups='$access' where name='$plugin'","");
	redirect("pages/team/team_plugins.php");
	}

include "../../include/header.php";
$s=explode(",",$access);
?>
<div class="BasicsBox"> 
<p><a onClick="return CentralSpaceLoad(this,true);" href="<?php echo $baseurl_short?>pages/team/team_plugins.php">&lt; <?php echo $lang["pluginssetup"] ?></a></p>
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["groupaccess"] . ': ' . $plugin ?></h1>

<form onSubmit="return CentralSpacePost(this,true);" method="post" action="<?php echo $baseurl_short?>pages/team/team_plugins_groups.php?save=true">
<p>
<input type="radio" name="access" value="all" <?php if ($access=="") { ?>checked<?php } ?>> <?php echo $lang["plugin-groupsallaccess"] ?>
<br/>
<input type="radio" name="access" value="some" id="some" <?php if ($access!="") { ?>checked<?php } ?>> <?php echo $lang["plugin-groupsspecific"] ?>
<?php foreach ($groups as $group) { ?>
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name="group_<?php echo $group["ref"] ?>" value="yes" <?php if (in_array($group["ref"],$s)) { ?>checked<?php } ?> onClick="document.getElementById('some').checked=true;"><?php echo $group["name"]?>
<?php } ?>
</p>

<input type=hidden name="plugin" value="<?php echo getvalescaped('plugin','')?>"/>
  
  
<input name="save" type="submit" value="<?php echo $lang["save"] ?>">
</form>



        

<?php include "../../include/footer.php"; ?>
