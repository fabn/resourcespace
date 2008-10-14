<?
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("o")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";
include "../../include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");

if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting


include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["managecontent"]?></h1>
  <p><?=text("introtext")?></p>
 
<? 
$text=get_all_site_text($find);

# pager
$per_page=15;
$results=count($text);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="team_content.php?find=" . urlencode($find);
$jumpcount=1;

?><div class="TopInpageNav"><? pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?=$lang["page"]?></td>
<td><?=$lang["name"]?></td>
<td><?=$lang["text"]?></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>

<?
for ($n=$offset;(($n<count($text)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><?=$text[$n]["page"]?></td>
	<td><div class="ListTitle"><a href="team_content_edit.php?page=<?=$text[$n]["page"]?>&name=<?=$text[$n]["name"]?>"><?=$text[$n]["name"]?></div></td>
	<td><?=tidy_trim(htmlspecialchars($text[$n]["text"]),45)?></td>
	<td><div class="ListTools"><a href="team_content_edit.php?page=<?=$text[$n]["page"]?>&name=<?=$text[$n]["name"]?>">&gt;&nbsp;<?=$lang["action-edit"]?> </a></div></td>
	</tr>
	<?
	}
?>

</table>
</div>
<div class="BottomInpageNav"><? pager(false); ?></div>
</div>


<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="find"><?=$lang["searchcontent"]?><br/><?=$lang["searchcontenteg"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?=$find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<?
include "../../include/footer.php";
?>