<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("r")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/research_functions.php";
include "../../include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");

if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting

if (getval("reload","")!="")
	{
	refresh_collection_frame();
	}
	
include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["manageresearchrequests"]?></h1>
  <p><?php echo text("introtext")?></p>
 
<?php 
$requests=get_research_requests($find);

# pager
$per_page=10;
$results=count($requests);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$url="team_research.php?find=" . urlencode($find);
$jumpcount=1;

?><div class="TopInpageNav"><?php pager();	?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php echo $lang["researchid"]?></td>
<td><?php echo $lang["nameofproject"]?></td>
<td><?php echo $lang["date"]?></td>
<td><?php echo $lang["status"]?></td>
<td><?php echo $lang["assignedto"]?></td>
<td><?php echo $lang["collectionid"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
$statusname=array("Unassigned","In progress","Complete");
for ($n=$offset;(($n<count($requests)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><?php echo $requests[$n]["ref"]?></td>
	<td><div class="ListTitle"><a href="team_research_edit.php?ref=<?php echo $requests[$n]["ref"]?>"><?php echo $requests[$n]["name"]?></a>&nbsp;</div></td>
	<td><?php echo nicedate($requests[$n]["created"],false,true)?></td>
	<td><?php echo $statusname[$requests[$n]["status"]]?></td>
	<td><?php echo (strlen($requests[$n]["assigned_username"])==0)?"-":$requests[$n]["assigned_username"]?></td>
	<td><?php echo (strlen($requests[$n]["collection"])==0)?"-":$requests[$n]["collection"]?></td>
	<td><div class="ListTools"><a href="team_research_edit.php?ref=<?php echo $requests[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["editresearch"]?></a>&nbsp;&nbsp;<a href="../collections.php?research=<?php echo $requests[$n]["ref"]?>" target="collections">&gt;&nbsp;<?php echo $lang["editcollection"]?></a></div></td>
	</tr>
	<?php
	}
?>

</table>
</div>
<div class="BottomInpageNav"><div class="InpageNavLeftBlock"><a href="../research_request.php?assign=true">&gt;&nbsp;<?php echo $lang["createresearchforuser"]?></a></div><?php pager(false); ?></div>
</div>


<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="findpublic"><?php echo $lang["searchresearchrequests"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>


<?php
include "../../include/footer.php";
?>