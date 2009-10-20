<?php
/**
 * Manage field options page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("k")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/resource_functions.php";

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h1><?php echo $lang["managefieldoptions"]?></h1>
  <p><?php echo text("introtext")?></p>
</div>

<?php 
$fields=get_fields_with_options();
?>
<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["field"]?></td>
<td><?php echo $lang["options"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
for ($n=0;$n<count($fields);$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="team_fields_edit.php?field=<?php echo $fields[$n]["ref"]?>"><?php echo $fields[$n]["title"]?></a></div></td>
	<td><?php echo substr(tidylist(i18n_get_translated($fields[$n]["options"])),0,100) . "..." ?></td>
	<td><div class="ListTools"><a href="team_fields_edit.php?field=<?php echo $fields[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?> </a></div></td>
	</tr>
	<?php
	}
?>

</table>
</div>


<?php
include "../../include/footer.php";
?>