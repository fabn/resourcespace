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
$group=getval("group",0,true);

# pager
$per_page=getvalescaped("per_page_list",$default_perpage_list);setcookie("per_page_list",$per_page);


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

# Fetch rows
$users=get_users($group,$find,$order_by,true,$offset+$per_page);
$groups=get_usergroups(true);

$results=count($users);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;

$url=$baseurl . "/pages/team/team_user.php?group=" . $group . "&order_by=" . $order_by . "&find=" . urlencode($find);
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}
$atoz.="<a href=\"" . $baseurl . "/pages/team/team_user.php?order_by=u.username&group=" . $group . "&find=\" onClick=\"return CentralSpaceLoad(this);\">" . $lang["viewall"] . "</a>";
if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"" . $baseurl . "/pages/team/team_user.php?order_by=u.username&group=" . $group . "&find=" . chr($n) . "\" onClick=\"return CentralSpaceLoad(this);\">&nbsp;" . chr($n) . "&nbsp;</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

?>

<div class="TopInpageNav"><?php echo $atoz?>	<div class="InpageNavLeftBlock"><?php echo $lang["resultsdisplay"]?>:
	<?php 
	for($n=0;$n<count($list_display_array);$n++){?>
	<?php if ($per_page==$list_display_array[$n]){?><span class="Selected"><?php echo $list_display_array[$n]?></span><?php } else { ?><a href="<?php echo $url; ?>&per_page_list=<?php echo $list_display_array[$n]?>" onClick="return CentralSpaceLoad(this);"><?php echo $list_display_array[$n]?></a><?php } ?>&nbsp;|
	<?php } ?>
	<?php if ($per_page==99999){?><span class="Selected"><?php echo $lang["all"]?></span><?php } else { ?><a href="<?php echo $url; ?>&per_page_list=99999" onClick="return CentralSpaceLoad(this);"><?php echo $lang["all"]?></a><?php } ?>
	</div> <?php pager(false); ?></div>

<strong><?php echo $lang["total"] . ": " . count($users); ?> </strong><?php echo $lang["users"]; ?>
<br />

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="u.username")?"u.username+desc":"u.username")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["username"]?></a></td>
<?php if (!hook("replacefullnameheader")){?>
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="u.fullname")?"u.fullname+desc":"u.fullname")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["fullname"]?></a></td>
<?php } ?>
<?php if (!hook("replacegroupnameheader")){?>
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="g.name")?"g.name+desc":"g.name")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["group"]?></a></td>
<?php } ?>
<?php if (!hook("replaceemailheader")){?>
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="email")?"email+desc":"email")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["email"]?></a></td>
<?php } ?>
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="created")?"created+desc,created+desc":"created")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["created"]?></a></td>
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="approved")?"approved+desc":"approved")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["approved"] ?></a></td>
<td><a href="<?php echo $baseurl ?>/pages/team/team_user.php?offset=0&order_by=<?php echo (($order_by=="last_active")?"last_active+desc":"last_active")?>&find=<?php echo urlencode($find)?>" onClick="return CentralSpaceLoad(this);"><?php echo $lang["lastactive"]?></a></td>

<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
for ($n=$offset;(($n<count($users)) && ($n<($offset+$per_page)));$n++)
	{
	?>
	<tr>
	<td><div class="ListTitle"><a href="<?php echo $baseurl ?>/pages/team/team_user_edit.php?ref=<?php echo $users[$n]["ref"]?>&backurl=<?php echo urlencode($url . "&offset=" . $offset)?>" onClick="return CentralSpaceLoad(this,true);"><?php echo $users[$n]["username"]?></div></td>
	<?php if (!hook("replacefullnamerow")){?>
	<td><?php echo $users[$n]["fullname"]?></td>
	<?php } ?>
	<?php if (!hook("replacegroupnamerow")){?>
	<td><?php echo $users[$n]["groupname"]?></td>
	<?php } ?>
	<?php if (!hook("replaceemailrow")){?>
	<td><?php echo htmlentities($users[$n]["email"])?></td>
	<?php } ?>
	<td><?php echo nicedate($users[$n]["created"]) ?></td>
	<td><?php echo $users[$n]["approved"]?$lang["yes"]:$lang["no"] ?></td>
	<td><?php echo nicedate($users[$n]["last_active"]) ?></td>

	<td><?php if (($usergroup==3) || ($users[$n]["usergroup"]!=3)) { ?><div class="ListTools">
	<a href="<?php echo $baseurl ?>/pages/team/team_user_log.php?ref=<?php echo $users[$n]["ref"]?>&backurl=<?php echo urlencode($url . "&offset=" . $offset)?>">&gt;&nbsp;<?php echo $lang["log"]?></a>
	&nbsp;
	<a href="<?php echo $baseurl ?>/pages/team/team_user_edit.php?ref=<?php echo $users[$n]["ref"]?>&backurl=<?php echo urlencode($url . "&offset=" . $offset)?>">&gt;&nbsp;<?php echo $lang["action-edit"]?></a>
	<?php hook("usertool")?>
	</div><?php } ?>
	</td>
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
    <label for="group"><?php echo $lang["group"]; ?></label>
    <div class="tickset">
      <div class="Inline"><select name="group" id="group" onChange="this.form.submit();">
        <option value="0"<?php if ($group == 0) { echo " selected"; } ?>><?php echo $lang["all"]; ?></option>
<?php
  for($n=0;$n<count($groups);$n++){
?>
        <option value="<?php echo $groups[$n]["ref"]; ?>"<?php if ($group == $groups[$n]["ref"]) { echo " selected"; } ?>><?php echo $groups[$n]["name"]; ?></option>
<?php
  }
?>
        </select>
      </div>
    </div>
		<div class="clearerleft"> </div>
  </div>
  </form>
</div>

<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<label for="find"><?php echo $lang["searchusers"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo $find?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["searchbutton"]?>&nbsp;&nbsp;" /></div>
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


<div class="BasicsBox">
<div class="Question"><label><?php echo $lang["purgeusers"]?></label>
<div class="Fixed"><a href="<?php echo $baseurl ?>/pages/team/team_user_purge.php">&gt;&nbsp;<?php echo $lang["purge"]?></a></div>
<div class="clearerleft"> </div></div>
</div>



<?php
include "../../include/footer.php";
?>
