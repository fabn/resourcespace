<?
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("k")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/resource_functions.php";

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h1><?=$lang["managefieldoptions"]?></h1>
  <p><?=text("introtext")?></p>
</div>

<? 
$fields=get_fields_with_options();
?>
<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?=$lang["field"]?></td>
<td><?=$lang["options"]?></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>

<?
for ($n=0;$n<count($fields);$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="team_fields_edit.php?field=<?=$fields[$n]["ref"]?>"><?=$fields[$n]["title"]?></a></div></td>
	<td><?=substr(tidylist(i18n_get_translated($fields[$n]["options"])),0,100) . "..." ?></td>
	<td><div class="ListTools"><a href="team_fields_edit.php?field=<?=$fields[$n]["ref"]?>">&gt;&nbsp;<?=$lang["action-edit"]?> </a></div></td>
	</tr>
	<?
	}
?>

</table>
</div>


<?
include "../../include/footer.php";
?>