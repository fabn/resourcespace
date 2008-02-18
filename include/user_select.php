<script language="Javascript">
function addUser()
	{
	var userselect=document.getElementById("userselect");
	var users=document.getElementById("users");
	var username=userselect.value;
	
	if (username.indexOf(",")!=-1)
		{
		if ((confirm("<?=$lang["confirmaddgroup"]?>"))==false) {return false;}
		}
		
	if (username!="") 
		{
		if (users.value.length!=0) {users.value+=", ";}
		users.value+=username;
		}
	}
</script>
<select size="8" id="userselect" name="userselect" class="shrtwidth" style="width:245px;">
<option value=""><?=$lang["selectgroupuser"]?></option>
<?
$groups=get_usergroups();
for ($n=0;$n<count($groups);$n++)
	{
	$show=true;
	if (checkperm("E") && ($groups[$n]["ref"]!=$usergroup) && ($groups[$n]["parent"]!=$usergroup) && ($groups[$n]["ref"]!=$usergroupparent)) {$show=false;}
	if ($show)
		{
		$users=get_users($groups[$n]["ref"]);
		$ulist="";
		
		for ($m=0;$m<count($users);$m++) {if ($ulist!="") {$ulist.=", ";};$ulist.=$users[$m]["username"];}
		if ($ulist!="")
			{
			?><option value="<?=$ulist?>"><?=$lang["group"]?>: <?=$groups[$n]["name"]?></option><?
			}
		}
	}

$users=get_users();
for ($n=0;$n<count($users);$n++)
	{
	$show=true;
	if (checkperm("E") && ($users[$n]["groupref"]!=$usergroup) && ($users[$n]["groupparent"]!=$usergroup) && ($users[$n]["groupref"]!=$usergroupparent)) {$show=false;}
	if ($show)
		{
		?><option value="<?=$users[$n]["username"]?>"><?=$users[$n]["username"] . " - " . $users[$n]["fullname"]?></option>
		<?
		}
	}
?>
</select>&nbsp;<input type=button value="<?=$lang["add"]?>" style="width:55px;" onClick="addUser();">
<br/>
<textarea rows=6 class="stdwidth" name="users" id="users" style="margin-left:150px;"><? if (isset($userstring)) {echo $userstring;} ?></textarea>


