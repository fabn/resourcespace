<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("o")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";
include "../../include/collections_functions.php";

$offset=getvalescaped("offset",0);

if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting
$find=getvalescaped("find","");
$page=getvalescaped("page","");
$name=getvalescaped("name","");

if ($page && $name){redirect("pages/team/team_content_edit.php?page=$page&name=$name&save=true&custom=1");}

include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["managecontent"]?></h1>
  <p><?php echo text("introtext")?></p>
 
<?php 
$text=get_all_site_text($find);

# pager
$per_page=15;
$results=count($text);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="team_content.php?find=" . urlencode($find);
$jumpcount=1;

?><div class="TopInpageNav"><?php pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["page"]?></td>
<td><?php echo $lang["name"]?></td>
<td><?php echo $lang["text"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
for ($n=$offset;(($n<count($text)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><?php echo $text[$n]["page"]?></td>
	<td><div class="ListTitle"><a href="team_content_edit.php?page=<?php echo $text[$n]["page"]?>&name=<?php echo $text[$n]["name"]?>&find=<?php echo $find?>"><?php echo $text[$n]["name"]?></div></td>
	<td><?php echo tidy_trim(htmlspecialchars($text[$n]["text"]),45)?></td>
	<td><div class="ListTools"><a href="team_content_edit.php?page=<?php echo $text[$n]["page"]?>&name=<?php echo $text[$n]["name"]?>&find=<?php echo $find?>">&gt;&nbsp;<?php echo $lang["action-edit"]?> </a></div></td>
	</tr>
	<?php
	}
?>

</table>
</div>
<div class="BottomInpageNav"><?php pager(false); ?></div>
</div>


<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="find"><?php echo $lang["searchcontent"]?><br/><?php echo $lang["searchcontenteg"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<?php if ($site_text_custom_create){?>
<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="find"><?php echo $lang["addnewcontent"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="page" id="page" maxlength="50" class="shrtwidth" /></div>
			 <div class="Inline"><input type=text name="name" id="name" maxlength="50" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<? } ?>

<?php
include "../../include/footer.php";
?>
