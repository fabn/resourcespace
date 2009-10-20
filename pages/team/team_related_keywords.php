<?php
/**
 * Manage related keywords page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("k")) {exit ("Permission denied.");}
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
  <h1><?php echo $lang["managerelatedkeywords"]?></h1>
  <p><?php echo text("introtext")?></p>
 
<?php 
$keywords=get_grouped_related_keywords($find);

# pager
$per_page=15;
$results=count($keywords);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="team_related_keywords.php?find=" . urlencode($find);
$jumpcount=1;

?><div class="TopInpageNav"><?php pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["keyword"]?></td>
<td><?php echo $find==""?$lang["relatedkeywords"]:$lang["matchingrelatedkeywords"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
for ($n=$offset;(($n<count($keywords)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="team_related_keywords_edit.php?keyword=<?php echo $keywords[$n]["keyword"]?>"><?php echo $keywords[$n]["keyword"]?></div></td>
	<td><?php echo tidy_trim(htmlspecialchars($keywords[$n]["related"]),45)?></td>
	<td><div class="ListTools"><a href="team_related_keywords_edit.php?keyword=<?php echo $keywords[$n]["keyword"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?> </a></div></td>
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
			<label for="find"><?php echo $lang["search"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<div class="BasicsBox">
    <form method="post" action="team_related_keywords_edit.php">
		<div class="Question">
			<label for="create"><?php echo $lang["newkeywordrelationship"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="keyword" id="keyword" value="" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="createsubmit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>


<?php
include "../../include/footer.php";
?>