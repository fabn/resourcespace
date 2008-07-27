<?

# AJAX user selection.

?>
<table cellpadding="0" cellspacing="0" width="300">
<tr><td><input type="text" class="stdwidth" style="width:245px;" value="<?=$lang["starttypingusername"]?>" id="autocomplete" name="autocomplete_parameter" onClick="this.value='';" /></td>
<td><input type=button value="+" style="width:48px;" onClick="addUser();" /></td></tr>
<tr><td colspan="2" align="left"><textarea rows=6 class="stdwidth" style="width:300px;" name="users" id="users"><? if (isset($userstring)) {echo $userstring;} ?></textarea></td></tr>
</table>
<div class="clearer"> </div>

<div id="autocomplete_choices" class="autocomplete"></div>
<script type="text/javascript">
new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "user_search.php",
	{
	afterUpdateElement : addUser
	}
);
function addUser()
	{
	var username=document.getElementById("autocomplete").value;
	var users=document.getElementById("users");
	
	if (username.indexOf("<?=$lang["group"]?>")!=-1)
		{
		if ((confirm("<?=$lang["confirmaddgroup"]?>"))==false) {return false;}
		}
		
	if (username!="") 
		{
		if (users.value.length!=0) {users.value+=", ";}
		users.value+=username;
		}
	document.getElementById("autocomplete").value="";
	}
</script>
<style>
div.autocomplete {
  position:absolute;
  width:250px;
  background-color:white;
  border:1px solid #888;
  margin:0px;
  padding:0px;
}
div.autocomplete ul {
  list-style-type:none;
  margin:0px;
  padding:0px;
}
div.autocomplete ul li.selected { background-color: #ffb;}
div.autocomplete ul li {
  list-style-type:none;
  display:block;
  margin:0;
  padding:2px;
  cursor:pointer;
  color: #000;
}
</style>

