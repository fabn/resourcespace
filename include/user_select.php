<?php

# AJAX user selection.

global $default_user_select;
if (!isset($userstring)) {$userstring="";}
if ($userstring=="") {$userstring=$default_user_select;}

?>
<table cellpadding="0" cellspacing="0" width="300">

<!-- autocomplete -->
<tr><td><input type="text" class="medwidth" placeholder="<?php echo $lang["starttypingusername"]?>" id="autocomplete" name="autocomplete_parameter" onClick="this.value='';" /></td>
<td><input type=button value="+" class="medcomplementwidth" onClick="addUser();" /></td></tr>
<!-- -->

<!-- user string -->
<tr><td colspan="2" align="left"><textarea rows=6 class="stdwidth" name="users" id="users" <?php if (!$sharing_userlists){?>onChange="this.value=this.value.replace(/[^,] /g,function replacespaces(str) {return str.substring(0,1) + ', ';});"<?php } else { ?>onChange="addUser();checkUserlist();updateUserSelect();"<?php } ?>><?php echo $userstring; ?></textarea></td></tr>
<!-- -->


<?php if ($sharing_userlists){?>
	<tr><td>
	<div id="userlist_name_div" style="display:none;">
		<input type="text" class="medwidth" value="<?php echo $lang['typeauserlistname']?>"  id="userlist_name_value" name="userlist_parameter" onClick="this.value='';" /></div>
	</td>

	<td>
	<div id="userlist_+" style="display:none;"><input type=button value="<?php echo $lang['saveuserlist']?>" class="medcomplementwidth" onClick="saveUserList();" />
	</td></tr>

	<?php $user_userlists=sql_query("select * from user_userlist where user='$userref'");?>

	<tr><td>
	<select id="userlist_select" class="medwidth" onchange="document.getElementById('users').value=$(this).value;document.getElementById('userlist_name_div').style.display='none';document.getElementById('userlist_+').style.display='none';if ($('userlist_select').value==''){$('userlist_delete').style.display='none';}else{$('userlist_delete').style.display='inline';}"></select>
	</td>
	
	<td>
	<input type=button id="userlist_delete" value="<?php echo $lang['deleteuserlist']?>" style="display:none;" class="medcomplementwidth" onClick="deleteUserList();" />
	</td></tr>

<?php } ?>

<?php hook ("addtouserselect");?>

</table>

<?php if ($sharing_userlists){?>
<div id="autocomplete_userlist_choices" class="autocomplete"></div>
<?php } ?>

<script type="text/javascript">

function addUser(event,ui)
	{
	var username=document.getElementById("autocomplete").value;
	var users=document.getElementById("users");

	if (typeof ui!=='undefined') {username=ui.item.value;}
	
	if (username.indexOf("<?php echo $lang["group"]?>")!=-1)
		{
		if ((confirm("<?php echo $lang["confirmaddgroup"]?>"))==false) {return false;}
		}
		
	if (username!="") 
		{
		if (users.value.length!=0) {users.value+=", ";}
		users.value+=username;
		//var input = users.value;var splitted = input.split(', ');splitted=splitted.uniq();splitted=splitted.sort();users.value = splitted.join(', '); 
		}
		
	document.getElementById("autocomplete").value="";
	
	<?php if ($sharing_userlists){?>
	var parameters = 'userstring='+ users.value;
	var newstring=new Ajax.Request("<?php echo $baseurl?>/pages/ajax/username_list_update.php",
		{
		parameters: parameters,
		onComplete: function(modified) {users.value=modified.responseText;	checkUserlist();}
		}
		);

	<?php } ?>
	return false;
	}

jQuery('#autocomplete').autocomplete(
	{
	source: "<?php echo $baseurl?>/pages/ajax/autocomplete_user.php",
	select: addUser
	} );


<?php if ($sharing_userlists){?>
updateUserSelect();
new Ajax.Autocompleter("userlist_name_value", "autocomplete_userlist_choices", "<?php echo $baseurl?>/pages/ajax/autocomplete_userlist.php");
<?php } ?>


<?php if ($sharing_userlists){?>	
function checkUserlist()
	{
	// conditionally add option to save userlist if string is new
	var userstring=document.getElementById("users").value;

	var sel = document.getElementById('userlist_select').options;
	var newstring=true;
	
	for (n=0; n<=sel.length-1;n++) {
		if(document.getElementById('users').value==sel[n].value){
			sel[n].selected=true;document.getElementById("userlist_delete").style.display='inline';
			newstring=false;
		
		break;}
	}
	
	if (newstring){
	 document.getElementById("userlist_name_div").style.display='block';
     document.getElementById("userlist_+").style.display='block';
	 document.getElementById('userlist_select').value="";	
	 document.getElementById('userlist_name_value').value='<?php echo $lang['typeauserlistname']?>';	
	 document.getElementById("userlist_delete").style.display='none';
	}
	else {
	 document.getElementById("userlist_name_div").style.display='none';
     document.getElementById("userlist_+").style.display='none';
     sel.options.selected=document.getElementById('users').value;
	}
}

function saveUserList()
	{
	var parameters = 'user=<?php echo $userref?>&userstring='+ document.getElementById("users").value+'&userlistname='+document.getElementById("userlist_name_value").value;
	new Ajax.Updater("userlist_select","<?php echo $baseurl?>/pages/ajax/userlist_save.php",
		{
		parameters: parameters,
		onComplete: function(){
			document.getElementById("userlist_name_div").style.display='none';
			document.getElementById("userlist_+").style.display='none';
			updateUserSelect();
			}
		}
	);

}

function deleteUserList()
	{
	var parameters = 'delete=true&userlistref='+document.getElementById('userlist_select').options[document.getElementById('userlist_select').selectedIndex].id;
	new Ajax.Updater("userlist_select","<?php echo $baseurl?>/pages/ajax/userlist_save.php",
		{
		parameters: parameters,
		onComplete: function(){
			updateUserSelect();
			//document.getElementById("userlist_name_div").style.display='none';
			//document.getElementById("userlist_+").style.display='none';
			//document.getElementById("userlist_delete").style.display='none';
			}
		}
	);

}


function updateUserSelect()
	{
	var parameters = 'user=<?php echo $userref?>&userstring='+document.getElementById("users").value;
	new Ajax.Updater("userlist_select","<?php echo $baseurl?>/pages/ajax/userlist_select_update.php",
		{
		parameters: parameters,
		onComplete: function(){checkUserlist();
			}
		}
	);

}


<?php } ?>
</script>


