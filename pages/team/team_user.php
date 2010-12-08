<?php
/**
 * User management start page (part of team center)
 * 
 * @Package ResourceSpace
 * @Subpackage Pages_Team
 */
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
  <h1><?php echo $lang["manageusers"]?></h1>
  <p><?php echo text("introtext")?></p>

<?php if (isset($error)) { ?><div class="FormError">!! <?php echo $error?> !!</div><?php } ?>

<?php 
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

<div class="TopInpageNav"><?php echo $atoz?><?php pager(false); ?></div>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><a href="team_user.php?offset=0&order_by=<?php echo (($order_by=="u.username")?"u.username+desc":"u.username")?>&find=<?php echo urlencode($find)?>"><?php echo $lang["username"]?></a></td>
<?php if (!hook("replacefullnameheader")){?>
<td><a href="team_user.php?offset=0&order_by=<?php echo (($order_by=="u.fullname")?"u.fullname+desc":"u.fullname")?>&find=<?php echo urlencode($find)?>"><?php echo $lang["fullname"]?></a></td>
<?php } ?>
<?php if (!hook("replacegroupnameheader")){?>
<td><a href="team_user.php?offset=0&order_by=<?php echo (($order_by=="g.name")?"g.name+desc":"g.name")?>&find=<?php echo urlencode($find)?>"><?php echo $lang["group"]?></a></td>
<?php } ?>
<?php if (!hook("replaceemailheader")){?>
<td><a href="team_user.php?offset=0&order_by=<?php echo (($order_by=="email")?"email+desc":"email")?>&find=<?php echo urlencode($find)?>"><?php echo $lang["email"]?></a></td>
<?php } ?>
<td><a href="team_user.php?offset=0&order_by=<?php echo (($order_by=="last_active,approved")?"last_active+desc,approved+desc":"last_active,approved")?>&find=<?php echo urlencode($find)?>"><?php echo $lang["approved"] . " / " . $lang["lastactive"]?></a></td>
<td><a href="team_user.php?offset=0&order_by=<?php echo (($order_by=="last_browser")?"last_browser+desc":"last_browser")?>&find=<?php echo urlencode($find)?>"><?php echo $lang["lastbrowser"]?></a></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
for ($n=$offset;(($n<count($users)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="team_user_edit.php?ref=<?php echo $users[$n]["ref"]?>&backurl=<?php echo urlencode($url . "&offset=" . $offset)?>"><?php echo $users[$n]["username"]?></div></td>
	<?php if (!hook("replacefullnamerow")){?>
	<td><?php echo $users[$n]["fullname"]?></td>
	<?php } ?>
	<?php if (!hook("replacegroupnamerow")){?>
	<td><?php echo i18n_get_translated($users[$n]["groupname"])?></td>
	<?php } ?>
	<?php if (!hook("replaceemailrow")){?>
	<td><?php echo htmlentities($users[$n]["email"])?></td>
	<?php } ?>
	<td><?php 
	# Forumulate a sensible last active date that also includes a non-approved indication.
	$last_active=$lang["never"];
	if ($users[$n]["approved"]==0)
		{
		$last_active=$lang["notapproved"];		
		}
	elseif ($users[$n]["last_active"]!="")
		{
		$last_active=nicedate($users[$n]["last_active"],true);
		}
	echo $last_active?></td>
	<td><?php echo resolve_user_agent($users[$n]["last_browser"],true)?></td>
	<td><?php if (($usergroup==3) || ($users[$n]["usergroup"]!=3)) { ?><div class="ListTools">
	<a href="team_user_log.php?ref=<?php echo $users[$n]["ref"]?>&backurl=<?php echo urlencode($url . "&offset=" . $offset)?>">&gt;&nbsp;<?php echo $lang["log"]?></a>
	&nbsp;
	<a href="team_user_edit.php?ref=<?php echo $users[$n]["ref"]?>&backurl=<?php echo urlencode($url . "&offset=" . $offset)?>">&gt;&nbsp;<?php echo $lang["edit"]?></a></div><?php } ?></td>
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
			<label for="find"><?php echo $lang["searchusers"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["search"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="newuser"><?php echo $lang["createuserwithusername"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="newuser" id="newuser" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>

<?php
include "../../include/footer.php";
?>
