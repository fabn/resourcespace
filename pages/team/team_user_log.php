<?
include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
include "../../include/resource_functions.php";

$ref=getvalescaped("ref","");
$userdata=get_user($ref);

include "../../include/header.php";
?>
<div class="BasicsBox">
<p><a href="<?=getval("backurl","")?>">&lt;&nbsp;<?=$lang["backtosearch"]?></a></p>
<h1><?=$lang["userlog"] . ": " . $userdata["fullname"]?></h1>
</div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td><?=$lang["date"]?></td>
<td><?=$lang["resourceid"]?></td>
<td><?=$lang["resourcetitle"]?></td>
<td><?=$lang["action"]?></td>
<td><?=$lang["field"]?></td>
</tr>

<?
$log=get_user_log($ref);
for ($n=0;$n<count($log);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td><?=$log[$n]["date"]?></td>
	<td><?=$log[$n]["resourceid"]?></td>
	<td><?=$log[$n]["resourcetitle"]?></td>
	<td><?=$lang["log-" . $log[$n]["type"]]?></td>
	<td><?=i18n_get_translated($log[$n]["title"])?></td>
	</tr>
	<?
	}
?>
</table>
</div>
<p><a href="<?=getval("backurl","")?>">&lt;&nbsp;<?=$lang["backtosearch"]?></a></p>
<?
include "../../include/footer.php";
?>
