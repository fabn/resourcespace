<?php
# Research functions
# Functions to accomodate research requests

if (!function_exists("send_research_request")){
function send_research_request()
	{
	# Insert a search request into the requests table.
	
	# Resolve resource types
	$rt="";
	$types=get_resource_types();for ($n=0;$n<count($types);$n++) {if (getval("resource" . $types[$n]["ref"],"")!="") {if ($rt!="") {$rt.=", ";} $rt.=$types[$n]["ref"];}}
	
	global $userref;
	$as_user=getvalescaped("as_user",$userref,true); # If userref submitted, use that, else use this user
	
	# Insert the request
	sql_query("insert into research_request(created,user,name,description,deadline,contact,email,finaluse,resource_types,noresources,shape)
	values (now(),'$as_user','" . getvalescaped("name","") . "','" . getvalescaped("description","") . "'," .
	((getvalescaped("deadline","")=="")?"null":"'" . getvalescaped("deadline","") . "'") . 
	",'" . getvalescaped("contact","") . "','" . getvalescaped("email","") . "','" . getvalescaped("finaluse","") . "','" . $rt . "'," .
	((getvalescaped("noresources","")=="")?"null":"'" . getvalescaped("noresources","") . "'") . 
	",'" . getvalescaped("shape","") . "')");
	
	# E-mails a resource request (posted) to the team
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$userfullname,$useremail,$lang;
	
	$templatevars['ref']=sql_insert_id();
	$templatevars['teamresearchurl']=$baseurl."/pages/team/team_research.php";
	$templatevars['username']=$username;
	$templatevars['userfullname']=$userfullname;
	$templatevars['useremail']=getvalescaped("email",$useremail); # Use provided e-mail (for anonymous access) or drop back to user email.
	$templatevars['url']=$baseurl."/pages/team/team_research_edit.php?ref=".$templatevars['ref'];
	
	$message="'$username' ($userfullname - $useremail) " . $lang["haspostedresearchrequest"] . ".\n\n";
	$message.=$templatevars['teamresearchurl'];
	hook("modifyresearchrequestemail");
	send_mail($email_notify,$applicationname . ": " . $lang["newresearchrequestwaiting"],$message,$useremail,"","emailnewresearchrequestwaiting",$templatevars);
	}
}

function get_research_requests($find="",$order_by="name",$sort="ASC")
	{
	if ($find!="") {$searchsql="where name like '%$find%' or description like '%$find%' or contact like '%$find%' or ref='$find'";} else {$searchsql="";}
	return sql_query("select *,(select username from user u where u.ref=r.user) username, (select username from user u where u.ref=r.assigned_to) assigned_username from research_request r $searchsql order by $order_by $sort");
	}

if (!function_exists("get_research_request")){
function get_research_request($ref)
	{
	$return=sql_query("select *,email,(select username from user u where u.ref=r.user) username, (select username from user u where u.ref=r.assigned_to) assigned_username from research_request r where ref='$ref'");
	return $return[0];
	}
}	

if (!function_exists("save_research_request")){	
function save_research_request($ref)
	{
	# Save
	global $baseurl,$email_from,$applicationname,$lang;
	
	if (getval("delete","")!="")
		{
		# Delete this request.
		sql_query("delete from research_request where ref='$ref' limit 1");
		return true;
		}
	# Check the status, if changed e-mail the originator
	$oldstatus=sql_value("select status value from research_request where ref='$ref'",0);
	$newstatus=getvalescaped("status",0);
	$collection=sql_value("select collection value from research_request where ref='$ref'",0);
	
	$templatevars['url']=$baseurl . "/?c=" . $collection;	
	
	if ($oldstatus!=$newstatus)
		{
		$email=sql_value("select u.email value from user u,research_request r where u.ref=r.user and r.ref='$ref'","");
		$message="";
		if ($newstatus==1) 
			{
			$message=$lang["researchrequestassignedmessage"];$subject=$lang["researchrequestassigned"];

			send_mail ($email,$applicationname . ": " . $subject,$message,"","","emailresearchrequestassigned",$templatevars);			
			# Log this
			daily_stat("Assigned research request",0);
			}
		if ($newstatus==2)
			{
			$message=$lang["researchrequestcompletemessage"] . "\n\n" . $lang["clicklinkviewcollection"] . "\n\n" . $templatevars['url'];$subject=$lang["researchrequestcomplete"];

			send_mail ($email,$applicationname . ": " . $subject,$message,"","","emailresearchrequestcomplete",$templatevars);
			# Log this			
			daily_stat("Processed research request",0);
			}
		
		}
	
	sql_query("update research_request set status='" . $newstatus . "',assigned_to='" . getvalescaped("assigned_to",0) . "' where ref='$ref'");
	
	# Copy existing collection
	if (getvalescaped("copyexisting","")!="" && is_numeric($collection))
		{
		sql_query("insert into collection_resource(collection,resource) select '$collection',resource from collection_resource where collection='" . getvalescaped("copyexistingref","") . "' and resource not in (select resource from collection_resource where collection='$collection');");
		}
	}
}

if (!function_exists("get_research_request_collection")){	
function get_research_request_collection($ref)
	{
	$return=sql_value("select collection value from research_request where ref='$ref'",0);
	if (($return==0) || (strlen($return)==0)) {return false;} else {return $return;}
	}
}	

if (!function_exists("set_research_collection")){
function set_research_collection($research,$collection)
	{
	sql_query("update research_request set collection='$collection' where ref='$research'");
	}
}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>
