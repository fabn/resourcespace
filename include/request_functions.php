<?php
# Request functions
# Functions to accomodate resource requests and orders (requests with payment)

function get_requests()
	{
	return sql_query("select u.username,u.fullname,r.* from request r left outer join user u on r.user=u.ref order by ref desc");
	}

function email_collection_request($ref,$details)
	{
	# Request mode 0
	# E-mails a collection request (posted) to the team
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$useremail,$lang;
	$message=$lang["username"] . ": " . $username . "\n";
	
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
				$message.=$value . ": " . $_POST[str_replace("_label","",$key)] . "\n";
				}
			}
		}
		
	if (trim($details)!="") {$message.=$lang["requestreason"] . ": " . newlines($details) . "\n\n";}
	$message.=$lang["viewcollection"] . ":\n$baseurl/?c=$ref";
	send_mail($email_notify,$applicationname . ": " . $lang["requestcollection"] . " - $ref",$message,$useremail);
	
	# Increment the request counter
	sql_query("update resource set request_count=request_count+1 where ref='$ref'");
	}

function managed_collection_request($ref,$details)
	{
	# Request mode 1
	# Managed via the administrative interface
	
	# An e-mail is still sent.
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$useremail,$userref,$lang;

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
				$message.=$value . ": " . $setting . "\n";
				}
			}
		}
	if (trim($details)!="") {$message.=$lang["requestreason"] . ": " . newlines($details) . "\n\n";}
	
	# Create the request
	sql_query("insert into request(user,collection,created,request_mode,status,comments) values ('$userref','$ref',now(),1,0,'" . escape_check($message) . "')");
	$request=sql_insert_id();
	
	# Send the e-mail		
	$message=$lang["username"] . ": " . $username . "\n" . $message;
	$message.=$lang["viewrequesturl"] . ":\n$baseurl/?q=$request";
	send_mail($email_notify,$applicationname . ": " . $lang["requestcollection"] . " - $ref",$message,$useremail);
	
	# Increment the request counter
	sql_query("update resource set request_count=request_count+1 where ref='$ref'");
	}


?>