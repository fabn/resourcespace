<?php

# Check access keys
function HookResourceconnectAllCheck_access_key($resource,$key)
	{

	# Generate access key and check that the key is correct for this resource.
	global $scramble_key;
	$access_key=md5("resourceconnect" . $scramble_key);
	
	if ($key!=substr(md5($access_key . $resource),0,10)) {return false;} # Invalid access key. Fall back to user logins.

	global $resourceconnect_user; # Which user to use for remote access?
	
	global $usergroup,$userpermissions,$userrequestmode;
	$userinfo=sql_query("select u.usergroup,g.permissions from user u join usergroup g on u.usergroup=g.ref where u.ref='$resourceconnect_user'");
	if (count($userinfo)>0)
		{
		$usergroup=$userinfo[0]["usergroup"];
		$userpermissions=explode(",",$userinfo[0]["permissions"]);
		if (hook("modifyuserpermissions")){$userpermissions=hook("modifyuserpermissions");}
		$userrequestmode=0; # Always use 'email' request mode for external users
		}
	
	return true;
	}

function HookResourceConnectAllInitialise()
	{
	# Work out the current affiliate
	global $lang,$language,$resourceconnect_affiliates,$baseurl,$resourceconnect_selected,$resourceconnect_this;

	# Work out which affiliate this site is
	$resourceconnect_this="";
	for ($n=0;$n<count($resourceconnect_affiliates);$n++)			
		{
		if ($resourceconnect_affiliates[$n]["baseurl"]==$baseurl) {$resourceconnect_this=$n;break;}
		}
	if ($resourceconnect_this==="") {exit("ResourceConnect error: current affiliate not found in configured affiliate list - ensure baseurls match");}
	
	$resourceconnect_selected=getval("resourceconnect_selected","");
	if ($resourceconnect_selected=="" || !isset($resourceconnect_affiliates[$resourceconnect_selected]))
		{
		# Not yet set, default to this site
		$resourceconnect_selected=$resourceconnect_this;
		}
#	setcookie("resourceconnect_selected",$resourceconnect_selected);
	setcookie("resourceconnect_selected",$resourceconnect_selected,0,"/");
	}

function HookResourceConnectAllSearchfiltertop()
	{
	# Option to search affiliate systems in the basic search panel
	global $lang,$language,$resourceconnect_affiliates,$baseurl,$resourceconnect_selected;
	if (!checkperm("resourceconnect")) {return false;}
	?>

	<div class="SearchItem"><?php echo $lang["resourceconnect-affiliate"];?><br />
	<select class="SearchWidth" name="resourceconnect_selected">
	
	<?php for ($n=0;$n<count($resourceconnect_affiliates);$n++)
		{
		?>
		<option value="<?php echo $n ?>" <?php if ($resourceconnect_selected==$n) { ?>selected<?php } ?>><?php echo $resourceconnect_affiliates[$n]["name"] ?></option>
		<?php		
		}
	?>
	</select>
	</div>
	<?php
	}


function HookResourceConnectAllGenerate_collection_access_key($collection,$k,$userref,$feedback,$email,$access,$expires)
	{
	# When sharing externally, add the external access key to an empty row if the collection is empty, so the key still validates.
	$c=sql_value("select count(*) value from collection_resource where collection='$collection'",0);
	if ($c>0) {return false;} # Contains resources, key already present
	
	sql_query("insert into external_access_keys(resource,access_key,collection,user,request_feedback,email,date,access,expires) values (-1,'$k','$collection','$userref','$feedback','" . escape_check($email) . "',now(),$access," . (($expires=="")?"null":"'" . $expires . "'"). ");");
	
	}

