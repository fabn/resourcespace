<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

$usergroups = sql_query("SELECT ref,name FROM usergroup");

// for test
//$ldapauth['ldapgroupfield'] = 'memberUid';

if (getval("submit","")!="") {

	$ldapauth=array();
	$ldapauth['enable'] = isset($_POST['enable']);
        $ldapauth['ldapserver'] = $_POST['ldapserver'];
	$ldapauth['port'] = $_POST['port'];
	$ldapauth['basedn']= $_POST['basedn'];
	$ldapauth['loginfield'] = $_POST['loginfield'];
	$ldapauth['usersuffix'] = $_POST['usersuffix'];
	$ldapauth['createusers'] = isset($_POST['createusers']);
	$ldapauth['groupbased'] = isset($_POST['groupbased']);
	$ldapauth['newusergroup'] = $_POST['newusergroup'];
	$ldapauth['ldapusercontainer'] = $_POST['ldapusercontainer'];
	$ldapauth['ldaptype'] = $_POST['ldaptype'];
	$ldapauth['rootdn'] = $_POST['rootdn'];
	$ldapauth['rootpass'] = $_POST['rootpass'];
	$ldapauth['addomain'] = $_POST['addomain'];
	$ldapauth['ldapgroupcontainer'] = $_POST['ldapgroupcontainer'];
	
	if (isset($_POST['ldapGroupName']))
	{
		$ldapGroupCount = count($_POST['ldapGroupName']);
		
		for ($ti= 0; $ti < $ldapGroupCount; $ti++)
		{
			$grpName = $_POST['ldapGroupName'][$ti];
			$ldapauth['groupmap'][$grpName]['rsGroup'] = $_POST['ldapmaptors'][$grpName];
			$ldapauth['groupmap'][$grpName]['enabled'] = isset($_POST['ldapGroupEnable'][$grpName]);
		}
	}
		
	set_plugin_config("posixldapauth", $ldapauth);

	redirect("pages/team/team_home.php");

} else {
	
	$ldapauth = get_plugin_config("posixldapauth");
	if ($ldapauth == null){
	    $ldapauth['enable'] = false;
	    $ldapauth['ldapserver'] = 'localhost';
	    $ldapauth['port'] = '389';
	    $ldapauth['basedn']= 'dc=mydomain,dc=net';
	    $ldapauth['loginfield'] = 'uid';
	    $ldapauth['usersuffix'] = '';
	    $ldapauth['createusers'] = true;
	    $ldapauth['groupbased'] = false;
	    $ldapauth['newusergroup'] = '2';
	    $ldapauth['ldapusercontainer'] = 'cn=users';
	    $ldapauth['ldaptype'] = 0;
		$ldapauth['rootdn'] ="admin@example.com";
		$ldapauth['rootpass'] = "";
		$ldapauth['addomain'] = "example.com";
	}
	if (!isset($ldapauth['ldapgroupcontainer']))
	{
		$ldapauth['ldapgroupcontainer'] = "";	
	}
	

}

//$ldapauth['ldaptype'] = 1;
if ($ldapauth['enable'])
{
  $enabled = "checked";
  // we get a list of groups from the LDAP;
  include_once ("../hooks/ldap_class.php");
  $ldapConf['host'] = $ldapauth['ldapserver'];
	$ldapConf['basedn'] = $ldapauth['basedn'];
	
	$objLDAP = new ldapAuth($ldapConf);
	
	
	if ($objLDAP->connect())
	{
		// we need to check for the kind of LDAP we are talking to here!
		if ($ldapauth['ldaptype'] == 1 )
		{
			// we need to bind!
			if (!$objLDAP->auth($ldapauth['rootdn'],$ldapauth['rootpass'],1,$ldapauth['addomain']))
			{
				$errmsg["auth"] = "Could not bind to AD, please check credentials";
			}	
		}
		
		if (!isset ($errmsg))
		{
			// get the groups
			error_log( " ldapauth:setup.php line 94 GOT TO THE GROUP SELECT ");
			$ldapGroupList = $objLDAP->listGroups($ldapauth['ldaptype'],$ldapauth['ldapgroupcontainer']);
			if (is_array($ldapGroupList)) {
				$ldapGroupsFound = true;
			} else { 
				$ldapGroupsFound = false;
			}
		}
		
				
	} else {
		echo "Connection to LDAP Server failed";	
	}

}  
else
{ 
	 $enabled = "";
}
if ($ldapauth['createusers'])
  $createusers = "checked";
else
  $createusers = "";

if ($ldapauth['groupbased'])
  $groupbased = "checked";
else
  $groupbased = "";


$headerinsert.="
	<script src=\"ldap_functions.js\" language=\"JavaScript1.2\"></script>
	";
include "../../../include/header.php";

?>
<script type="text/javascript">
	
</script>
<div class="BasicsBox"> 

  <h2>&nbsp;</h2>

  <h1>Ldapauth Configuration</h1>

  <div class="VerticalNav">

    <form id="form1" name="form1" method="post" action="">

      <p><label for="enable">Enabled:</label><input type="checkbox" name="enable" id="enable" accesskey="e" tabindex="1" <?php echo $enabled ?> /></p>

      <p><label for="ldapserver">LDAP Server:</label><input id="ldapserver" name="ldapserver" type="text" value="<?php echo $ldapauth['ldapserver']; ?>" size="30" />
      <label for="ldapauth">:</label><input name="port" type="text" value="<?php echo $ldapauth['port']; ?>" size="6" /></p>

      <fieldset>
        <legend>LDAP Information</legend>
	  <table id='tableldaptype'>
	  	<tr>
	  		<th><label for="ldaptype">LDAP Type:</label></th>
	  		<td>
	  			<select id='ldaptype' name='ldaptype' onclick='ldapsetDisplayFields()'>
	  			<option value=0 <?php if($ldapauth['ldaptype'] == 0) {echo "selected"; } ?> >Open Directory</option>
	  			<option value=1 <?php if($ldapauth['ldaptype'] == 1) {echo "selected"; } ?> >Active Directory</option>
	  			</select>
	  		</td>
	  	</tr>
	  
	    <tr id="trootdn">
	    	<th><label id='lrootdn' for="rootdn">AD Admin:</label></th>
	    	<td><input id="rootdn" name="rootdn" type="text" value="<?php if (isset($ldapauth['rootdn'])) { echo $ldapauth['rootdn']; }?>" size="30" /></td>
	    </tr>
	    <tr id="trootpass">
	    	<th><label for="rootpass">AD Password:</label></th>
	    	<td><input id="rootpass" name="rootpass" type="password" value="<?php if (isset($ldapauth['rootpass'])) { echo $ldapauth['rootpass']; } ?>" size="30" /></td>
	    </tr>
	   	<tr id="taddomain">
	   		<th><label for="addomian">AD Domain:</label></th>
	   		<td><input id="addomain"  name="addomain" type="text" value="<?php if (isset($ldapauth['addomain'])) { echo $ldapauth['addomain']; }?>" size="30" /></td>
	   	</tr>
	   	<tr id="tbasedn">
	    	<th><label for="basedn">Base DN:</label></th>
	    	<td><input id="basedn" name="basedn" type="text" value="<?php echo $ldapauth['basedn']; ?>" size="50" /></td>
	    </tr>
	    <tr id="tldapusercontainer">
	    	<th><label for="ldapusercontainer">User Container:</label></th>
	    	<td><input id="ldapusercontainer" name="ldapusercontainer" type="text" value="<?php echo $ldapauth['ldapusercontainer']; ?>" size="30" /> This is added to the base dn</td>
	    </tr>
	       <tr id="tldapgroupcontainer">
	    	<th><label for="ldapgroupcontainer">Group Container:</label></th>
	    	<td><input id="ldapgroupcontainer" name="ldapgroupcontainer" type="text" value="<?php echo $ldapauth['ldapgroupcontainer']; ?> " size="30" /> Leave blank for default OSX Server mapping</td>
	    </tr>
	    <tr id="tloginfield">
	    	<th><label for="loginfield">Login Field:</label></th>
	    	<td><input id="loginfield" name="loginfield" type="text" value="<?php echo $ldapauth['loginfield']; ?>" size="30" /></td>
	    </tr>
	    <tr>
	    	<th><label for="testConn">Test Connection:</label></th>
	    	<td><button name="testConn" type="button" onclick="testLdapConn()">Test</button></td>
	    </tr>
	  </table>
	</fieldset>

	<fieldset><legend>ResourceSpace Configuration</legend>
	  <table>
            <tr>
            	<th><label for="usersuffix">User Suffix:</label></th>
            	<td><input name="usersuffix" type="text" value="<?php echo $ldapauth['usersuffix']; ?>" size="30" /></td>
            </tr>
            <tr>
            	<th><label for="createusers">Create Users:</label></th>
            	<td><input name="createusers" type="checkbox" <?php echo $createusers; ?> /></td>
            </tr>
            <tbody id="ldapconf-cu">
             	<tr><th><label for="groupbased">Group Based User Creation:</label></th><td><input name="groupbased" type="checkbox" <?php echo $groupbased; ?> /></td></tr>
              <tbody id="group-false">
                <tr><th><label for="newusergroup">New User Group</label></th>
        	  <td>
                    <select name="newusergroup">
        	      <?php
        	      
        		foreach ($usergroups as $usergroup){
			  $ref = $usergroup['ref'];
        		  echo '<option value="'.$ref.'"';
			  if ($ref == $ldapauth['newusergroup'])
                            echo "selected";

			  echo '>'.$usergroup['name'].'</option>';	
        		}
        		
                      ?>
		    </select>
                  </td>
		</tr>
              </tbody>
            </tbody>
          </table>
        </fieldset>
        <?php
        if ($enabled && !isset($errmsg))
        {
	     		
	     
	        echo '<fieldset><legend>Group Mapping</legend>';
	        
	        // Check to see if we found any groups!
	        if ($ldapGroupsFound)
	        {
	        	
		        // here we display the group mapping for the LDAP user groups:
		        echo "<table>";
		        // header row
		        echo '<tr><th>Group Name</th>';
		        echo '<th>Map To</th>';
		        echo '<th>Enable Group</th>';
		        echo "</tr>";
		        
		        // now display each group
		        $tmpx = count($ldapGroupList);	
				for ($i=0; $i < $tmpx; $i++) 
				{
				    //echo $ldapGroupList[$i]['cn'] ." : " . $info[$i]['gidnumber']. "<br>";
					echo "<tr>";
					echo '<td><input name="ldapGroupName[]" type="text" value="'. $ldapGroupList[$i]['cn'] . '" size="30" readonly="readonly"></td>';
					echo "<td>";
					$lGroupName = $ldapGroupList[$i]['cn'];
					// create the usergroup list
					echo '<select name="ldapmaptors['.$lGroupName.']">';
		     		foreach ($usergroups as $usergroup)
		     		{
				 		$ref = $usergroup['ref'];
	        		  	echo '<option value="'.$ref.'"';
	        		  	// check mapping;
	        		  	if (isset($ldapauth['groupmap'][$lGroupName]['rsGroup']))
	        		  	{
					  		if ($ref == $ldapauth['groupmap'][$lGroupName]['rsGroup'])
		                    {
		                          echo "selected";
		                    }
	        		  	}
				  		echo '>'.$usergroup['name'].'</option>';	
	        		}
	        		echo "</select>";
					echo "</td>";
					echo "<td>";
					echo '<input name="ldapGroupEnable['.$lGroupName.']" type="checkbox" ';
					// check to see if the enabled exists and if it has a value!
					if (isset($ldapauth['groupmap'][$lGroupName]['enabled']))
					{
						if( $ldapauth['groupmap'][$lGroupName]['enabled']) 
						{
							echo "checked";	
						}
					}
					echo ' />'; 
					echo "</td>";
					echo "</tr>";
				}
		        
		        
		        echo "</table>";
		       
	        } else {
	        	
	        	echo "<p>" . $ldapGroupList ."</p>"; 	
	        }
	        
	         
	        echo "</fieldset>";
        } else {
        	
        	if (isset($errmsg))
        	{
        		foreach ($errmsg as $msg)
        		{
        			echo "Error: " . $msg ." <br>";	
        		}	
        	}
        }
        ?>
        
   
        <input type="submit" name="submit" value="<?php echo $lang["save"]?>"/>

    </form>
  </div>	
<script type="text/javascript">
	ldapsetDisplayFields();
	</script>
