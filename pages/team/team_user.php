<?
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("u")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/collections_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$order_by=getvalescaped("order_by","u.username");

if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting

if (getval("newuser","")!="")
	{
	$new=new_user(getvalescaped("newuser",""));
	if ($new===false)
		{
		$error=$lang["useralreadyexists"];
		}
	else
		{
		redirect("pages/team/team_user_edit.php?ref=" . $new);
		}
	}
	
include "../../include/header.php";
?>


<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["manageusers"]?></h1>
  <p><?=text("introtext")?></p>

<? if (isset($error)) { ?><div class="FormError">!! <?=$error?> !!</div><? } ?>

<? 
# pager
$per_page=15;

# Fetch rows
$users=get_users(0,$find,$order_by,true,$offset+$per_page);

$results=count($users);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;

$url="team_user.php?order_by=" . $order_by . "&find=" . urlencode($find);
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}
$atoz.="<a href=\"team_user.php?order_by=u.username&find=\">" . $lang["viewall"] . "</a>";
if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"team_user.php?order_by=u.username&find=" . chr($n) . "\">" . chr($n) . "</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

?>

<div class="TopInpageNav"><?=$atoz?><? pager(false); ?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><a href="team_user.php?offset=0&order_by=u.username&find=<?=urlencode($find)?>"><?=$lang["username"]?></a></td>
<td><a href="team_user.php?offset=0&order_by=u.fullname&find=<?=urlencode($find)?>"><?=$lang["fullname"]?></a></td>
<td><a href="team_user.php?offset=0&order_by=g.name&find=<?=urlencode($find)?>"><?=$lang["group"]?></a></td>
<td><a href="team_user.php?offset=0&order_by=email&find=<?=urlencode($find)?>"><?=$lang["email"]?></a></td>
<td><a href="team_user.php?offset=0&order_by=<?=(($order_by=="last_active")?"last_active+desc":"last_active")?>&find=<?=urlencode($find)?>"><?=$lang["lastactive"]?></a></td>
<td><a href="team_user.php?offset=0&order_by=last_browser&find=<?=urlencode($find)?>"><?=$lang["lastbrowser"]?></a></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>

<?
for ($n=$offset;(($n<count($users)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="team_user_edit.php?ref=<?=$users[$n]["ref"]?>&backurl=<?=urlencode($url . "&offset=" . $offset)?>"><?=$users[$n]["username"]?></div></td>
	<td><?=$users[$n]["fullname"]?></td>
	<td><?=$users[$n]["groupname"]?></td>
	<td><?=$users[$n]["email"]?></td>
	<td><?=nicedate($users[$n]["last_active"],true)?></td>
	<td><?=resolve_user_agent($users[$n]["last_browser"],true)?></td>
	<td><? if (($usergroup==3) || ($users[$n]["usergroup"]!=3)) { ?><div class="ListTools">
	<a href="team_user_log.php?ref=<?=$users[$n]["ref"]?>&backurl=<?=urlencode($url . "&offset=" . $offset)?>">&gt;&nbsp;<?=$lang["log"]?></a>
	&nbsp;
	<a href="team_user_edit.php?ref=<?=$users[$n]["ref"]?>&backurl=<?=urlencode($url . "&offset=" . $offset)?>">&gt;&nbsp;<?=$lang["edit"]?></a></div><? } ?></td>
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
			<label for="find"><?=$lang["searchusers"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?=$find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="newuser"><?=$lang["createuserwithusername"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="newuser" id="newuser" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<?
include "../../include/footer.php";
?>