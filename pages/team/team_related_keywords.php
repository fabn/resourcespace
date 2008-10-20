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
  <h1><?=$lang["managerelatedkeywords"]?></h1>
  <p><?=text("introtext")?></p>
 
<? 
$keywords=get_grouped_related_keywords($find);

# pager
$per_page=15;
$results=count($keywords);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="team_related_keywords.php?find=" . urlencode($find);
$jumpcount=1;

?><div class="TopInpageNav"><? pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?=$lang["keyword"]?></td>
<td><?=$find==""?$lang["relatedkeywords"]:$lang["matchingrelatedkeywords"]?></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>

<?
for ($n=$offset;(($n<count($keywords)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="team_related_keywords_edit.php?keyword=<?=$keywords[$n]["keyword"]?>"><?=$keywords[$n]["keyword"]?></div></td>
	<td><?=tidy_trim(htmlspecialchars($keywords[$n]["related"]),45)?></td>
	<td><div class="ListTools"><a href="team_related_keywords_edit.php?keyword=<?=$keywords[$n]["keyword"]?>">&gt;&nbsp;<?=$lang["action-edit"]?> </a></div></td>
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
			<label for="find"><?=$lang["search"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?=$find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<div class="BasicsBox">
    <form method="post" action="team_related_keywords_edit.php">
		<div class="Question">
			<label for="create"><?=$lang["newkeywordrelationship"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="keyword" id="keyword" value="" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="createsubmit" type="submit" value="&nbsp;&nbsp;<?=$lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>


<?
include "../../include/footer.php";
?>