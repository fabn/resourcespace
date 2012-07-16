<?php
# Request functions
# Functions to accomodate resource requests and orders (requests with payment)

function get_request($request)
	{
	$result=sql_query("select u.username,u.fullname,u.email,r.user,r.collection,r.created,r.request_mode,r.status,r.comments,r.expires,r.assigned_to,r.reason,r.reasonapproved,u2.username assigned_to_username from request r left outer join user u  on r.user=u.ref left outer join user u2 on r.assigned_to=u2.ref where r.ref='$request'");
	if (count($result)==0)
		{
		return false;
		}
	else
		{
		return $result[0];
		}
	}

function get_user_requests()
	{
	global $userref;
	if (!is_numeric($userref)){ return false; }
	return sql_query("select u.username,u.fullname,r.*,(select count(*) from collection_resource cr where cr.collection=r.collection) c from request r left outer join user u on r.user=u.ref where r.user = '$userref' order by ref desc");
	}
	
function save_request($request)
	{
	# Use the posted form to update the request
	global $applicationname,$baseurl,$lang;
		
	$status=getvalescaped("status","",true);
	$expires=getvalescaped("expires","");
	$currentrequest=get_request($request);
	$oldstatus=$currentrequest["status"];
	$assigned_to=getvalescaped("assigned_to","");
	$reason=getvalescaped("reason","");
	$reasonapproved=getvalescaped("reasonapproved","");
	
	
	# --------------------- User Assignment ------------------------
	# Has the assigned_to value changed?
	if ($currentrequest["assigned_to"]!=$assigned_to && checkperm("Ra"))
		{
		if ($assigned_to==0)
			{
			# Cancel assignment
			sql_query("update request set assigned_to=null where ref='$request'");
			}
		else
			{
			# Update and notify user
			sql_query("update request set assigned_to='$assigned_to' where ref='$request'");

			$message=$lang["requestassignedtoyoumail"] . "\n\n$baseurl/?q=" . $request . "\n";
			$assigned_to_user=get_user($assigned_to);
			send_mail($assigned_to_user["email"],$applicationname . ": " . $lang["requestassignedtoyou"],$message);
			}
		}
	
	
	# Has either the status or the expiry date changed?
	if (($oldstatus!=$status || $expires!=$currentrequest["expires"]) && $status==1)
		{
		# --------------- APPROVED -------------
		# Send approval e-mail
		$message=$lang["requestapprovedmail"] . "\n\n";
		$message.="$baseurl/?c=" . $currentrequest["collection"] . "\n";
		if ($expires!="")
			{
			# Add expiry time to message.
			$message.=$lang["requestapprovedexpires"] . " " . nicedate($expires) . "\n\n";
			}
		$reasonapproved=str_replace(array("\\r","\\n"),"\n",$reasonapproved);$reasonapproved=str_replace("\n\n","\n",$reasonapproved); # Fix line breaks.
		send_mail($currentrequest["email"],$applicationname . ": " . $lang["requestcollection"] . " - " . $lang["resourcerequeststatus1"],$message);
		
		# Mark resources as full access for this user
		foreach (get_collection_resources($currentrequest["collection"]) as $resource)
			{
			open_access_to_user($currentrequest["user"],$resource,$expires);
			}
		}

	if ($oldstatus!=$status && $status==2)	
		{
		# --------------- DECLINED -------------
		# Send declined e-mail

		$reason=str_replace(array("\\r","\\n"),"\n",$reason);$reason=str_replace("\n\n","\n",$reason); # Fix line breaks.
		$message=$lang["requestdeclinedmail"] . "\n\n" . $reason . "\n\n$baseurl/?c=" . $currentrequest["collection"] . "\n";
		send_mail($currentrequest["email"],$applicationname . ": " . $lang["requestcollection"] . " - " . $lang["resourcerequeststatus2"],$message);

		# Remove access that my have been granted by an inadvertant 'approved' command.
		foreach (get_collection_resources($currentrequest["collection"]) as $resource)
			{
			remove_access_to_user($currentrequest["user"],$resource);
			}

		}

	if ($oldstatus!=$status && $status==0)
		{
		# --------------- PENDING -------------
		# Moved back to pending. Delete any permissions set by a previous 'approve'.
		foreach (get_collection_resources($currentrequest["collection"]) as $resource)
			{
			remove_access_to_user($currentrequest["user"],$resource);
			}
		}

	# Save status
	sql_query("update request set status='$status',expires=" . ($expires==""?"null":"'$expires'") . ",reason='$reason',reasonapproved='$reasonapproved' where ref='$request'");

	if (getval("delete","")!="")
		{
		# Delete the request - this is done AFTER any e-mails have been sent out so this can be used on approval.
		sql_query("delete from request where ref='$request'");
		return true;		
		}

	}
	
	
function get_requests()
	{
	# If permission Rb (accept resource request assignments) is set then limit the list to only those assigned to this user - EXCEPT for those that can assign requests, who can always see everything.
	$condition="";global $userref;
	if (checkperm("Rb") && !checkperm("Ra")) {$condition="where r.assigned_to='" . $userref . "'";}
	
	return sql_query("select u.username,u.fullname,r.*,(select count(*) from collection_resource cr where cr.collection=r.collection) c,r.assigned_to,u2.username assigned_to_username from request r left outer join user u on r.user=u.ref left outer join user u2 on r.assigned_to=u2.ref $condition order by status,ref desc");
	}

function email_collection_request($ref,$details)
	{
	# Request mode 0
	# E-mails a collection request (posted) to the team
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$useremail,$lang;
	
	$message="";
	if (isset($username) && trim($username)!="") {$message.=$lang["username"] . ": " . $username . "\n\n";}
	
	# Create a copy of the collection which is the one sent to the team. This is so that the admin
	# user can e-mail back an external URL to the collection if necessary, to 'unlock' full (open) access.
	# The user cannot then gain access to further resources by adding them to their original collection as the
	# shared collection is a copy.
	# A complicated scenario that is best avoided using 'managed requests'.
	$copied=create_collection(-1,$lang["requestcollection"]);
	copy_collection($ref,$copied);
	$ref=$copied;
	
	reset ($_POST);
	foreach ($_POST as $key=>$value)
		{
		if (strpos($key,"_label")!==false)
			{
			# Add custom field
			$setting=trim($_POST[str_replace("_label","",$key)]);
			if ($setting!="")
				{
				$message.=$value . ": " . $_POST[str_replace("_label","",$key)] . "\n\n";
				}
			}
		}
	if (trim($details)!="") {$message.=$lang["requestreason"] . ": " . newlines($details) . "\n\n";} else {return false;}
	
	# Add custom fields
	$c="";
	global $custom_request_fields,$custom_request_required;
	if (isset($custom_request_fields))
		{
		$custom=explode(",",$custom_request_fields);
	
		# Required fields?
		if (isset($custom_request_required)) {$required=explode(",",$custom_request_required);}
	
		for ($n=0;$n<count($custom);$n++)
			{
			if (isset($required) && in_array($custom[$n],$required) && getval("custom" . $n,"")=="")
				{
				return false; # Required field was not set.
				}
			
			$message.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n,"") . "\n\n";
			}
		}
	
	$message.=$lang["viewcollection"] . ":\n$baseurl/?c=$ref";
	send_mail($email_notify,$applicationname . ": " . $lang["requestcollection"] . " - $ref",$message,$useremail);
	
	# Increment the request counter
	sql_query("update resource set request_count=request_count+1 where ref='$ref'");
	
	return true;
	}

function managed_collection_request($ref,$details,$ref_is_resource=false)
	{
	# Request mode 1
	# Managed via the administrative interface
	
	# An e-mail is still sent.
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$useremail,$userref,$lang;

	# Has a resource reference (instead of a collection reference) been passed?
	# Manage requests only work with collections. Create a collection containing only this resource.
	if ($ref_is_resource)
		{
		$c=create_collection($userref,$lang["request"] . " " . date("ymdHis"));
		add_resource_to_collection($ref,$c);
		$ref=$c; # Proceed as normal
		}

	# Fomulate e-mail text
	$message="";
	reset ($_POST);
	foreach ($_POST as $key=>$value)
		{
		if (strpos($key,"_label")!==false)
			{
			# Add custom field
			$setting=trim($_POST[str_replace("_label","",$key)]);
			if ($setting!="")
				{
				$message.=$value . ": " . $setting . "\n\n";
				}
			}
		}
	if (trim($details)!="") {$message.=$lang["requestreason"] . ": " . newlines($details) . "\n\n";} else {return false;}
	
	# Add custom fields
	$c="";
	global $custom_request_fields,$custom_request_required;
	if (isset($custom_request_fields))
		{
		$custom=explode(",",$custom_request_fields);
	
		# Required fields?
		if (isset($custom_request_required)) {$required=explode(",",$custom_request_required);}
	
		for ($n=0;$n<count($custom);$n++)
			{
			if (isset($required) && in_array($custom[$n],$required) && getval("custom" . $n,"")=="")
				{
				return false; # Required field was not set.
				}
			
			$message.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n,"") . "\n\n";
			}
		}
	
	# Create the request
	sql_query("insert into request(user,collection,created,request_mode,status,comments) values ('$userref','$ref',now(),1,0,'" . escape_check($message) . "')");
	$request=sql_insert_id();
	
	# Send the e-mail		
	$message=$lang["username"] . ": " . $username . "\n" . $message;
	$message.=$lang["viewrequesturl"] . ":\n$baseurl/?q=$request";
	send_mail($email_notify,$applicationname . ": " . $lang["requestcollection"] . " - $ref",$message,$useremail);
	
	# Increment the request counter
	sql_query("update resource set request_count=request_count+1 where ref='$ref'");
	
	return true;
	}


function email_resource_request($ref,$details)
	{
	# E-mails a basic resource request for a single resource (posted) to the team
	# (not a managed request)
	
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$useremail,$lang;
	
	$templatevars['username']=$username . " (" . $useremail . ")";
	$templatevars['url']=$baseurl."/?r=".$ref;
	
	$htmlbreak="";
	global $use_phpmailer;
	if ($use_phpmailer){$htmlbreak="<br><br>";}
	
	$list="";
	reset ($_POST);
	foreach ($_POST as $key=>$value)
		{
		if (strpos($key,"_label")!==false)
			{
			# Add custom field	
			$data="";
			$data=$_POST[str_replace("_label","",$key)];
			$list.=$htmlbreak. $value . ": " . $data."\n";
			}
		}
	$list.=$htmlbreak;		
	$templatevars['list']=$list;

	$templatevars['details']=stripslashes($details);
	if ($templatevars['details']!=""){$adddetails=$lang["requestreason"] . ": " . newlines($templatevars['details'])."\n\n";} else {return false;}
	
	# Add custom fields
	$c="";
	global $custom_request_fields,$custom_request_required;
	if (isset($custom_request_fields))
		{
		$custom=explode(",",$custom_request_fields);
	
		# Required fields?
		if (isset($custom_request_required)) {$required=explode(",",$custom_request_required);}
	
		for ($n=0;$n<count($custom);$n++)
			{
			if (isset($required) && in_array($custom[$n],$required) && getval("custom" . $n,"")=="")
				{
				return false; # Required field was not set.
				}
			
			$c.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n,"") . "\n\n";
			}
		}
	
	$message=$lang["username"] . ": " . $username . " (" . $useremail . ")\n".$templatevars['list']."\n".$adddetails. $c . $lang["clicktoviewresource"] . "\n\n". $templatevars['url'];

	send_mail($email_notify,$applicationname . ": " . $lang["requestresource"] . " - $ref",$message,$useremail,$useremail,"emailresourcerequest",$templatevars);
	
	# Increment the request counter
	sql_query("update resource set request_count=request_count+1 where ref='$ref'");
	}

?>
