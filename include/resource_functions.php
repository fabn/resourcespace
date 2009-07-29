<?php
# Resource functions
# Functions to create, edit and index resources

function create_resource($resource_type,$archive=999,$user=-1)
	{
	# Create a new resource.

	if ($archive==999)
		{
		# Work out an appropriate default state
		$archive=0;
		if (!checkperm("e0")) {$archive=2;} # Can't create a resource in normal state? create in archive.
		}
	if ($archive==-2 || $archive==-1)
		{
		# Work out user ref - note: only for content in status -2 and -1 (user submitted / pending review).
		global $userref;
		$user=$userref;
		} else {$user=-1;}
	
	sql_query("insert into resource(resource_type,creation_date,archive,created_by) values ('$resource_type',now(),'$archive','$user')");
	
	$insert=sql_insert_id();
	
	# Copying a resource of the 'pending review' state? Notify, if configured.
	if ($archive==-1)
		{
		notify_user_contributed_submitted(array($insert));
		}
	
	# Log this			
	daily_stat("Create resource",$insert);
	resource_log($insert,'c',0);

	return $insert;
	}
	
function save_resource_data($ref,$multi)
	{
	# Save all submitted data for resource $ref.
	# Also re-index all keywords from indexable fields.
		
	global $auto_order_checkbox,$userresourcedefaults,$multilingual_text_fields,$languages,$language;
	# Loop through the field data and save (if necessary)
	$errors=array();
	$resource_sql="";
	$fields=get_resource_field_data($ref,$multi);
	for ($n=0;$n<count($fields);$n++)
		{
		if (!checkperm("F" . $fields[$n]["ref"]))
			{
			if ($fields[$n]["type"]==2)
				{
				# construct the value from the ticked boxes
				$val=","; # Note: it seems wrong to start with a comma, but this ensures it is treated as a comma separated list by split_keywords(), so if just one item is selected it still does individual word adding, so 'South Asia' is split to 'South Asia','South','Asia'.
				$options=trim_array(explode(",",$fields[$n]["options"]));
				if ($auto_order_checkbox) {sort($options);}
				for ($m=0;$m<count($options);$m++)
					{
					$name=$fields[$n]["ref"] . "_" . $m;
					if (getval($name,"")=="yes")
						{
						if ($val!=",") {$val.=",";}
						$val.=$options[$m];
						}
					}
				}
			elseif ($fields[$n]["type"]==4 || $fields[$n]["type"]==6)
				{
				# date type, construct the value from the date dropdowns
				$val=getvalescaped("field_" . $fields[$n]["ref"] . "-y","");
				$val.="-" . getvalescaped("field_" . $fields[$n]["ref"] . "-m","");
				$val.="-" . getvalescaped("field_" . $fields[$n]["ref"] . "-d","");
				if (strlen($val)!=10) {$val="";}
				}
			elseif ($multilingual_text_fields && ($fields[$n]["type"]==0 || $fields[$n]["type"]==1 || $fields[$n]["type"]==5))
				{
				# Construct a multilingual string from the submitted translations
				$val=getvalescaped("field_" . $fields[$n]["ref"],"");
				$val="~" . $language . ":" . $val;
				reset ($languages);
				foreach ($languages as $langkey => $langname)
					{
					if ($language!=$langkey)
						{
						$val.="~" . $langkey . ":" . getvalescaped("multilingual_" . $n . "_" . $langkey,"");
						}
					}
				}
			else
				{
				# Set the value exactly as sent.
				$val=getvalescaped("field_" . $fields[$n]["ref"],"");
				}
			if ($fields[$n]["value"]!=$val)
				{
				# This value is different from the value we have on record.
				
				# Write this edit to the log.
				resource_log($ref,'e',$fields[$n]["ref"]);
				
				# If 'resource_column' is set, then we need to add this to a query to back-update
				# the related columns on the resource table
				if (strlen($fields[$n]["resource_column"])>0)
					{
					if ($resource_sql!="") {$resource_sql.=",";}
					$resource_sql.=$fields[$n]["resource_column"] . "='" . escape_check($val) . "'";
					}
				# Purge existing data and keyword mappings, decrease keyword hitcounts.
				sql_query("delete from resource_data where resource='$ref' and resource_type_field='" . $fields[$n]["ref"] . "'");
				
				# Insert new data and keyword mappings, increase keyword hitcounts.
				sql_query("insert into resource_data(resource,resource_type_field,value) values('$ref','" . $fields[$n]["ref"] . "','" . str_replace((!(strpos($val,"\'")===false)?"\'":"'"),"''",$val) ."')");
	
				$oldval=$fields[$n]["value"];
				
				if ($fields[$n]["type"]==3)
					{
					# Prepend a comma when indexing dropdowns
					$val="," . getvalescaped("field_" . $fields[$n]["ref"],"");
					$oldval="," . $oldval;
					}
				
				if ($fields[$n]["keywords_index"]==1)
					{
					# Date field? These need indexing differently.
					$is_date=($fields[$n]["type"]==4 || $fields[$n]["type"]==6);
					
					remove_keyword_mappings($ref, i18n_get_indexable($oldval), $fields[$n]["ref"], $fields[$n]["partial_index"],$is_date);
					add_keyword_mappings($ref, i18n_get_indexable($val), $fields[$n]["ref"], $fields[$n]["partial_index"],$is_date);
					}
				
				# update resources table if necessary
				if ($resource_sql!="") sql_query("update resource set $resource_sql where ref='$ref'");
				}
			
			# Check required fields have been entered.
			if ($fields[$n]["required"]==1 && ($val=="" || $val==","))
				{
				global $lang;
				$errors[$fields[$n]["ref"]]=$lang["requiredfield"];
				}
			}
		}
		
	# Save all the resource defaults
	global $userresourcedefaults;
	if ($userresourcedefaults!="")
		{
		$s=explode(";",$userresourcedefaults);
		for ($n=0;$n<count($s);$n++)
			{
			$e=explode("=",$s[$n]);
			# Find field(s) - multiple fields can be returned to support several fields with the same name.
			$f=sql_array("select ref value from resource_type_field where name='" . escape_check($e[0]) . "'");
			if (count($f)==0) {exit ("Field(s) with short name '" . $e[0] . "' not found in resource defaults for this user group.");}
			for ($m=0;$m<count($f);$m++)
				{
				update_field($ref,$f[$m],$e[1]);
				}
			}
		}
		
	# Also save related resources field
	sql_query("delete from resource_related where resource='$ref' or related='$ref'"); # remove existing related items
	$related=explode(",",getvalescaped("related",""));
	# Make sure all submitted values are numeric
	$ok=array();for ($n=0;$n<count($related);$n++) {if (is_numeric(trim($related[$n]))) {$ok[]=trim($related[$n]);}}
	if (count($ok)>0) {sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$ok) . ")");}
					
	// Notify the resources team ($email_notify) if moving from pending review->submission.
	$archive=getvalescaped("archive",0);
	$oldarchive=sql_value("select archive value from resource where ref='$ref'",0);
	if ($oldarchive==-2 && $archive==-1 && $ref>0)
		{
		notify_user_contributed_submitted(array($ref));
		}
	if ($oldarchive==-1 && $archive==-2 && $ref>0)
		{
		notify_user_contributed_unsubmitted(array($ref));
		}	

	# Also update archive status and access level
	sql_query("update resource set archive='" . getvalescaped("archive",0) . "',access='" . getvalescaped("access",0) . "' where ref='$ref'");
	
	# For access level 3 (custom) - also save custom permissions
	if (getvalescaped("access",0)==3) {save_resource_custom_access($ref);}
	
	hook("aftersaveresourcedata");
	
	if (count($errors)==0) {return true;} else {return $errors;}
	}
	

function save_resource_data_multi($collection)
	{
	# Save all submitted data for collection $collection, this is for the 'edit multiple resources' feature
	# Loop through the field data and save (if necessary)
	$list=get_collection_resources($collection);
	$ref=$list[0];
	$fields=get_resource_field_data($ref,true);
	global $auto_order_checkbox;
	
	for ($n=0;$n<count($fields);$n++)
		{
		if (getval("editthis_field_" . $fields[$n]["ref"],"")!="")
			{
			if ($fields[$n]["type"]==2)
				{
				# construct the value from the ticked boxes
				$val=","; # Note: it seems wrong to start with a comma, but this ensures it is treated as a comma separated list by split_keywords(), so if just one item is selected it still does individual word adding, so 'South Asia' is split to 'South Asia','South','Asia'.
				$options=trim_array(explode(",",$fields[$n]["options"]));
				if ($auto_order_checkbox) {sort($options);}
				
				for ($m=0;$m<count($options);$m++)
					{
					$name=$fields[$n]["ref"] . "_" . $m;
					if (getval($name,"")=="yes")
						{
						if ($val!=",") {$val.=",";}
						$val.=$options[$m];
						}
					}
				}
			elseif ($fields[$n]["type"]==4 || $fields[$n]["type"]==6)
				{
				# date/expiry date type, construct the value from the date dropdowns
				$val=getvalescaped("field_" . $fields[$n]["ref"] . "-y","");
				$val.="-" . getvalescaped("field_" . $fields[$n]["ref"] . "-m","");
				$val.="-" . getvalescaped("field_" . $fields[$n]["ref"] . "-d","");
				if (strlen($val)!=10) {$val="";}
				}
			else
				{
				$val=getvalescaped("field_" . $fields[$n]["ref"],"");
				}
			$origval=$val;
			# Loop through all the resources and save.
			for ($m=0;$m<count($list);$m++)
				{
				$ref=$list[$m];
				$resource_sql="";

				# Work out existing field value.
				$existing=escape_check(sql_value("select value from resource_data where resource='$ref' and resource_type_field='" . $fields[$n]["ref"] . "'",""));
				
				# Find and replace mode? Perform the find and replace.
				if (getval("modeselect_" . $fields[$n]["ref"],"")=="FR")
					{
					$val=str_replace
						(
						getvalescaped("find_" . $fields[$n]["ref"],""),
						getvalescaped("replace_" . $fields[$n]["ref"],""),
						$existing
						);
					}
				
				# Append text/option(s) mode?
				if (getval("modeselect_" . $fields[$n]["ref"],"")=="AP")
					{
					if ($fields[$n]["type"]!=2 && $fields[$n]["type"]!=3)
						{
						# Automatically append a space when appending text types.
						$val=$existing . " " . $origval;
						}
					else
						{
						# Checkbox/dropdown types can just append immediately (a comma will already be present at the beginning of $origval).
						$val=$existing . $origval;
						}
					}
					
				# Remove text/option(s) mode?
				if (getval("modeselect_" . $fields[$n]["ref"],"")=="RM")
					{
					$val=str_replace($origval,"",$existing);
					}
					
				#echo "<li>existing=$existing, new=$val";
				if ($existing!=$val)
					{
					# This value is different from the value we have on record.
		
					# Write this edit to the log.
					resource_log($ref,'m',$fields[$n]["ref"]);
		
					# If 'resource_column' is set, then we need to add this to a query to back-update
					# the related columns on the resource table
					if (strlen($fields[$n]["resource_column"])>0)
						{
						sql_query("update resource set " . $fields[$n]["resource_column"] . "='" . escape_check($val) . "' where ref='$ref'");
						}
					# Purge existing data and keyword mappings, decrease keyword hitcounts.
					sql_query("delete from resource_data where resource='$ref' and resource_type_field='" . $fields[$n]["ref"] . "'");
					
					# Insert new data and keyword mappings, increase keyword hitcounts.
					sql_query("insert into resource_data(resource,resource_type_field,value) values('$ref','" . $fields[$n]["ref"] . "','" . escape_check($val) . "')");
		
					$oldval=$existing;
					$newval=$val;
					
					if ($fields[$n]["type"]==3)
						{
						# Prepend a comma when indexing dropdowns
						$newval="," . $val;
						$oldval="," . $oldval;
						}
					
					if ($fields[$n]["keywords_index"]==1)
						{
						# Date field? These need indexing differently.
						$is_date=($fields[$n]["type"]==4 || $fields[$n]["type"]==6); 
						remove_keyword_mappings($ref,i18n_get_indexable($oldval),$fields[$n]["ref"],$fields[$n]["partial_index"],$is_date);
						add_keyword_mappings($ref,i18n_get_indexable($newval),$fields[$n]["ref"],$fields[$n]["partial_index"],$is_date);
						}
					}
				}
			}
		}
		
	# Also save related resources field
	if (getval("editthis_related","")!="")
		{
		$related=explode(",",getvalescaped("related",""));
		# Make sure all submitted values are numeric
		$ok=array();for ($n=0;$n<count($related);$n++) {if (is_numeric(trim($related[$n]))) {$ok[]=trim($related[$n]);}}

		for ($m=0;$m<count($list);$m++)
			{
			$ref=$list[$m];
			sql_query("delete from resource_related where resource='$ref' or related='$ref'"); # remove existing related items
			if (count($ok)>0) {sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$ok) . ")");}
			}
		}

	# Also update archive status
	if (getval("editthis_status","")!="")
		{
		$notifyrefs=array();
		for ($m=0;$m<count($list);$m++)
			{
			$ref=$list[$m];
			$archive=getvalescaped("archive",0);
			$oldarchive=sql_value("select archive value from resource where ref='$ref'",0);
			
			if ($oldarchive!=$archive)
				{
				sql_query("update resource set archive='" . $archive . "' where ref='$ref'");

				if ($oldarchive==-2 && $archive==-1)
					{
					# Notify the admin users of this change.
					$notifyrefs[]=$ref;
					}
				}
			}
		if (count($notifyrefs)>0)
			{
			# Notify the admin users of any submitted resources.
			notify_user_contributed_submitted($notifyrefs);
			}
		}
	
	# Also update access level
	if (getval("editthis_access","")!="")
		{
		for ($m=0;$m<count($list);$m++)
			{
			$ref=$list[$m];
			$access=getvalescaped("access",0);
			sql_query("update resource set access='$access' where ref='$ref'");
			
			# For access level 3 (custom) - also save custom permissions
			if ($access==3) {save_resource_custom_access($ref);}
			}
		}
		
		hook("aftersaveresourcedata");
		
	}

function remove_keyword_mappings($ref,$string,$resource_type_field,$partial_index=false,$is_date=false)
	{
	# Removes one instance of each keyword->resource mapping for each occurrence of that
	# keyword in $string.
	# This is used to remove keyword mappings when a field has changed.
	# We also decrease the hit count for each keyword.
	if (trim($string)=="") {return false;}
	$keywords=split_keywords($string,true,$partial_index,$is_date);
	for ($n=0;$n<count($keywords);$n++)
		{
		#echo "<li>removing " . $keywords[$n];
		sql_query("delete from resource_keyword where resource='$ref' and keyword=(select ref from keyword where keyword='" . escape_check($keywords[$n]) . "') and resource_type_field='$resource_type_field' limit 1");
		sql_query("update keyword set hit_count=hit_count-1 where keyword='" . escape_check($keywords[$n]) . "' limit 1");
		}	
	}
	
function add_keyword_mappings($ref,$string,$resource_type_field,$partial_index=false,$is_date=false)
	{
	# For each instance of a keyword in $string, add a keyword->resource mapping.
	# Create keywords that do not yet exist.
	# Increase the hit count of each keyword that matches.
	# Store the position and field the string was entered against for advanced searching.
	if (trim($string)=="") {return false;}
	$keywords=split_keywords($string,true,$partial_index,$is_date);
	for ($n=0;$n<count($keywords);$n++)
		{
		global $noadd;
		if ((!(in_array($keywords[$n],$noadd))) && (strlen($keywords[$n])<=50))
			{
			#echo "<li>adding " . $keywords[$n];
			$keyword=resolve_keyword($keywords[$n]);
			if ($keyword===false)
				{
				# This is a new keyword. Create and discover the new keyword ref.
				sql_query("insert into keyword(keyword,soundex,hit_count) values ('" . escape_check($keywords[$n]) . 	"',soundex('" . escape_check($keywords[$n]) . "'),0)");
				$keyword=sql_insert_id();
				#echo "<li>New keyword.";
				}
			# create mapping, increase hit count.
			sql_query("insert into resource_keyword(resource,keyword,position,resource_type_field) values ('$ref','$keyword','$n','$resource_type_field')");
			sql_query("update keyword set hit_count=hit_count+1 where ref='$keyword' limit 1");
			
			# Log this
			daily_stat("Keyword added to resource",$keyword);
			}	
		}	
	}
	
function update_field($resource,$field,$value)
	{
	# Updates a field. Works out the previous value, so this is not efficient if we already know what this previous value is (hence it is not used for edit where multiple fields are saved)

	# Fetch some information about the field
	$fieldinfo=sql_query("select keywords_index,resource_column,partial_index from resource_type_field where ref='$field'");
	if (count($fieldinfo)==0) {return false;} else {$fieldinfo=$fieldinfo[0];}
	
	if ($fieldinfo["keywords_index"])
		{
		# Fetch previous value and remove the index for those keywords
		$existing=sql_value("select value from resource_data where resource='$resource' and resource_type_field='$field'","");
		remove_keyword_mappings($resource,i18n_get_indexable($existing),$field,$fieldinfo["partial_index"]);
		
		# Index the new value
		add_keyword_mappings($resource,i18n_get_indexable($value),$field,$fieldinfo["partial_index"]);
		}
		
	# Delete the old value (if any) and add a new value.
	sql_query("delete from resource_data where resource='$resource' and resource_type_field='$field' limit 1");
	$value=escape_check($value);
	sql_query("insert into resource_data(resource,resource_type_field,value) values ('$resource','$field','$value')");
	
	# Also update resource table?
	$column=$fieldinfo["resource_column"];
	if (strlen($column)>0)
		{
		sql_query("update resource set $column = '$value' where ref='$resource'");
		}
	}

if (!function_exists("email_resource")){	
function email_resource($resource,$resourcename,$fromusername,$userlist,$message,$access=-1,$expires="")
	{
	# Attempt to resolve all users in the string $userlist to user references.

	global $baseurl,$email_from,$applicationname,$lang,$userref;
	
	# remove any line breaks that may have been entered
	$userlist=str_replace("\\r\\n",",",$userlist);

	if (trim($userlist)=="") {return ($lang["mustspecifyoneusername"]);}
	$userlist=resolve_userlist_groups($userlist);
	$ulist=trim_array(explode(",",$userlist));
	$ulist=array_filter($ulist);
	$ulist=array_values($ulist);

	$emails=array();
	$key_required=array();
	
	for ($n=0;$n<count($ulist);$n++)
		{
		$uname=$ulist[$n];
		$email=sql_value("select email value from user where username='" . escape_check($uname) . "'",'');
		if ($email=='')
			{
			# Not a recognised user, if @ sign present, assume e-mail address specified
			if (strpos($uname,"@")===false) {return($lang["couldnotmatchallusernames"]);}
			$emails[$n]=$uname;
			$key_required[$n]=true;
			}
		else
			{
			# Add e-mail address from user account
			$emails[$n]=$email;
			$key_required[$n]=false;
			}
		}

	# Send an e-mail to each resolved user / e-mail address
	$subject="$applicationname: $resourcename";
	if ($message!="") {$message="\n\n" . $lang["message"] . ": " . str_replace(array("\\n","\\r","\\"),array("\n","\r",""),$message);}
	for ($n=0;$n<count($emails);$n++)
		{
		$key="";
		# Do we need to add an external access key for this user (e-mail specified rather than username)?
		if ($key_required[$n])
			{
			$k=substr(md5(time()),0,10);
			sql_query("insert into external_access_keys(resource,access_key,user,access,expires) values ('$resource','$k','$userref','$access'," . (($expires=="")?"null":"'" . $expires . "'"). ");");
			$key="&k=". $k;
			}
		
		# make vars available to template
		$templatevars['thumbnail']=get_resource_path($resource,true,"thm",false);
		$templatevars['url']=$baseurl . "/?r=" . $resource . $key;
		$templatevars['fromusername']=$fromusername;
		$templatevars['message']=$message;
		$templatevars['resourcename']=$resourcename;
		
		# Build message and send.
		$body=$templatevars['fromusername']." ". $lang["hasemailedyouaresource"] . $templatevars['message']."\n\n" . $lang["clicktoviewresource"] . "\n\n" . $templatevars['url'];
		send_mail($emails[$n],$subject,$body,"","","emailresource",$templatevars);
		}
		
	# Return an empty string (all OK).
	return "";
	}
}

function delete_resource($ref)
	{
	if ($ref<0) {return false;} # Can't delete the template
	# Delete the resource, all related entries in tables and all files on disk
	
	# Get info
	$resource=sql_query("select is_transcoding, file_extension, preview_extension from resource where ref='$ref'","jpg");
	if (!$resource) {return false;} # Resource not found in database
	$resource=$resource[0];
	
	# Is transcoding
	if ($resource['is_transcoding']==1) {return false;} # Can't delete when transcoding

	# Delete files first
	$extensions = array();
	$extensions[]=$resource['file_extension']?$resource['file_extension']:"jpg";
	$extensions[]=$resource['preview_extension']?$resource['preview_extension']:"jpg";
	$extensions[]=$GLOBALS['ffmpeg_preview_extension'];
	$extensions=array_unique($extensions);

	foreach ($extensions as $extension)
		{
		$sizes=get_image_sizes($ref,true,$extension);
		foreach ($sizes as $size)
			{
			if (file_exists($size['path'])) {unlink($size['path']);}
			}
		}
	
	# Delete any alternative files
	$alternatives=get_alternative_files($ref);
	for ($n=0;$n<count($alternatives);$n++)
		{
		$path=get_resource_path($ref, true, "", true, $alternatives[$n]["file_extension"], -1, 1, false, "", $alternatives[$n]["ref"]);
		if (file_exists($path)) {unlink($path);}
		}
	
	# Log the deletion of this resource for any collection it was in. 
	$in_collections=sql_query("select * from collection_resource where resource = '$ref'");
	if (count($in_collections)>0){
		if (!function_exists("collection_log")){include ("collections_functions.php");}
		for($n=0;$n<count($in_collections);$n++)
			{
			collection_log($in_collections[$n]['collection'],'d',$in_collections[$n]['resource']);
			}
		}
		
	# Delete all database entries
	sql_query("delete from resource where ref='$ref'");
	sql_query("delete from resource_data where resource='$ref'");
	sql_query("delete from resource_dimensions where resource='$ref'");
	sql_query("delete from resource_keyword where resource='$ref'");
	sql_query("delete from resource_related where resource='$ref' or related='$ref'");
	sql_query("delete from collection_resource where resource='$ref'");
	sql_query("delete from resource_custom_access where resource='$ref'");
	sql_query("delete from external_access_keys where resource='$ref'");
	
	hook("afterdeleteresource","all",$ref);
	
	return true;
	}

function get_max_resource_ref()
	{
	# Returns the highest resource reference in use.
	return sql_value("select max(ref) value from resource",0);
	}

function get_resource_ref_range($lower,$higher)
	{
	# Returns an array of resource references in the range $lower to $upper.
	return sql_array("select ref value from resource where ref>='$lower' and ref<='$higher' and archive=0 order by ref",0);
	}
	
function copy_resource($from,$resource_type=-1)
	{
	# Create a new resource, copying all data from the resource with reference $from.
	# Note this copies only the data and not any attached file. It's very unlikely the
	# same file would be in the system twice, however users may want to clone an existing resource
	# to avoid reentering data if the resource is very similar.
	# If $resource_type if specified then the resource type for the new resource will be set to $resource_type
	# rather than simply copied from the $from resource.
	
	# Check that the resource exists
	if (sql_value("select count(*) value from resource where ref='$from'",0)==0) {return false;}
	
	# First copy the resources row
	sql_query("insert into resource(title,resource_type,creation_date,rating,country,archive,access,created_by) select title," . (($resource_type==-1)?"resource_type":("'" . $resource_type . "'")) . ",creation_date,rating,country,archive,access,created_by from resource where ref='$from';");
	$to=sql_insert_id();
	
	# Copying a resource of the 'pending review' state? Notify, if configured.
	$archive=sql_value("select archive value from resource where ref='$from'",0);
	if ($archive==-1)
		{
		notify_user_contributed_submitted(array($to));
		}
	
	# Set that this resource was created by this user. 
	# This needs to be done if either:
	# 1) The user does not have direct 'resource create' permissions and is therefore contributing using My Contributions directly into the live state
	# 2) The user is contributiting via My Contributions to the standard User Contributed pre-live states.
	global $userref;
	if ((!checkperm("c")) || $archive<0)
		{
		# Update the user record
		sql_query("update resource set created_by='$userref' where ref='$to'");

		# Also add the user's username and full name to the keywords index so the resource is searchable using this name.
		global $username,$userfullname;
		add_keyword_mappings($to,$username . " " . $userfullname,-1);
		}
	
	# Now copy all data
	sql_query("insert into resource_data(resource,resource_type_field,value) select '$to',resource_type_field,value from resource_data where resource='$from'");

	# Copy keyword mappings
	sql_query("insert into resource_keyword(resource,keyword,hit_count,position,resource_type_field) select '$to',keyword,hit_count,position,resource_type_field from resource_keyword where resource='$from'");
	
	# Copy relationships
	sql_query("insert into resource_related(resource,related) select '$to',related from resource_related where resource='$from'");

	# Copy access
	sql_query("insert into resource_custom_access(resource,usergroup,access) select '$to',usergroup,access from resource_custom_access where resource='$from'");
	
	# Log this			
	daily_stat("Create resource",$to);
	resource_log($to,'c',0);
	
	return $to;
	}
	
function resource_log($resource,$type,$field)
	{
	global $userref;
	sql_query("insert into resource_log(date,user,resource,type,resource_type_field) values (now()," . (($userref!="")?"'$userref'":"null") . ",'$resource','$type','$field')");
	}

function get_resource_log($resource)
	{
	return sql_query("select r.date,u.username,u.fullname,r.type,f.title from resource_log r left outer join user u on u.ref=r.user left outer join resource_type_field f on f.ref=r.resource_type_field where resource='$resource' order by r.date");
	}
	
function get_resource_type_name($type)
	{
	global $lang;
	if ($type==999) {return $lang["archive"];}
	return i18n_get_translated(sql_value("select name value from resource_type where ref='$type'",""));
	}
	
function get_resource_custom_access($resource)
	{
	# Return a list of usergroups with the custom access level for resource $resource (if set)
	$sql="";
	if (checkperm("E"))
		{
		# Restrict to this group and children groups only.
		global $usergroup,$usergroupparent;
		$sql="where g.parent='$usergroup' or g.ref='$usergroup' or g.ref='$usergroupparent'";
		}
	return sql_query("select g.ref,g.name,g.permissions,c.access from usergroup g left outer join resource_custom_access c on g.ref=c.usergroup and c.resource='$resource' $sql order by (g.permissions like '%v%') desc,g.name");
	}
	
function save_resource_custom_access($resource)
	{
	$groups=get_resource_custom_access($resource);
	sql_query("delete from resource_custom_access where resource='$resource'");
	for ($n=0;$n<count($groups);$n++)
		{
		$usergroup=$groups[$n]["ref"];
		$access=getvalescaped("custom_" . $usergroup,0);
		sql_query("insert into resource_custom_access(resource,usergroup,access) values ('$resource','$usergroup','$access')");
		}
	}
	
function get_custom_access($resource,$usergroup)
	{
	global $custom_access;
	if ($custom_access==false) {return 0;} # Custom access disabled? Always return 'open' access for resources marked as custom.

	return sql_value("select access value from resource_custom_access where resource='$resource' and usergroup='$usergroup'",2);
	}
	
function get_themes_by_resource($ref)
	{
	$themes=sql_query("select c.ref,c.theme,c.theme2,c.theme3,c.name,u.fullname from collection_resource cr join collection c on cr.collection=c.ref and cr.resource='$ref' and c.public=1 left outer join user u on c.user=u.ref order by length(theme) desc");
	# Combine the theme categories into one string so multiple category levels display correctly.
	$return=array();
	for ($n=0;$n<count($themes);$n++)
		{
		if (checkperm("j*") || checkperm("j" . $themes[$n]["theme"]))
			{
			$theme="";
			if ($themes[$n]["theme"]!="") {$theme=$themes[$n]["theme"];}
			if ($themes[$n]["theme2"]!="") {$theme.=" / " . $themes[$n]["theme2"];}
			if ($themes[$n]["theme3"]!="") {$theme.=" / " . $themes[$n]["theme3"];}
			$themes[$n]["theme"]=$theme;			
			$return[]=$themes[$n];
			}
		}
	return $return;
	}

function update_resource_type($ref,$type)
	{
	sql_query("update resource set resource_type='$type' where ref='$ref'");
	}
	
function relate_to_array($ref,$array)	
	{
	# Relates a resource to each in a simple array of ref numbers
		sql_query("delete from resource_related where resource='$ref' or related='$ref'");  
		sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$array) . ")");
	}		

function get_exiftool_fields($resource_type)
	{
	# Returns a list of exiftool fields, which are basically fields with an 'exiftool field' set.
	return sql_query("select ref,type,exiftool_field from resource_type_field where length(exiftool_field)>0 and (resource_type='$resource_type' or resource_type='0')  order by exiftool_field");
	}

function write_metadata($path,$ref)
	{
	global $exiftool_path,$exiftool_remove_existing,$storagedir,$exiftool_write,$exiftool_no_process;
	
	# Fetch file extension
	$resource_data=get_resource_data($ref);
	$extension=$resource_data["file_extension"];
	$resource_type=$resource_data["resource_type"];
		
	if (isset($exiftool_path) && ($exiftool_write) && !in_array($extension,$exiftool_no_process))
		{
		if (file_exists(stripslashes($exiftool_path) . "/exiftool"))
			{
		      if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);}
				$filename = pathinfo($path);
				$filename = $filename['basename'];	
				$tmpfile=$storagedir . "/tmp/" . $filename;				
				copy($path,$tmpfile);
			
					#Now that we have already copied the original file, we can use exiftool's overwrite_original on the tmpfile
					$command=$exiftool_path."/exiftool -overwrite_original ";
					if ($exiftool_remove_existing) {$command.="-EXIF:all -XMP:all= -IPTC:all= ";}
					$write_to=get_exiftool_fields($resource_type);
					for($i=0;$i< count($write_to);$i++)
						{
						$fieldtype=$write_to[$i]['type'];
						$field=explode(",",$write_to[$i]['exiftool_field']);
						# write datetype fields as ISO 8601 date ("c") 
						if ($fieldtype=="4"){$writevalue=date("c",strtotime(get_data_by_field($ref,$write_to[$i]['ref'])));}
						else {$writevalue=get_data_by_field($ref,$write_to[$i]['ref']);}
						foreach ($field as $field)
							{
							$command.="-".$field."=\"". str_replace("\"","\\\"",$writevalue) . "\" " ;
							}
						}
					$command.=" $tmpfile";
					$output=shell_exec($command) or die("Problem writing metadata: $output <br />Command was: $command");
					
			return $tmpfile;
			}
		else
			{
			return false;
			}
		}
	else
		{
		return false;
		}
	}

function delete_exif_tmpfile($tmpfile)
{
	if(file_exists($tmpfile)){unlink ($tmpfile);}
}

function import_resource($path,$type,$title)
	{
	# Import the resource at the given path
	# This is used by staticsync.php and Camillo's SOAP API
	# Note that the file will be used at it's present location and will not be copied.

	# Create resource
	$r=create_resource($type);
			
	# Work out extension
	$extension=explode(".",$path);$extension=trim(strtolower($extension[count($extension)-1]));

	# Store extension/data in the database
	sql_query("update resource set archive=0,file_extension='$extension',preview_extension='$extension',file_modified=now() where ref='$r'");
			
	# Store original filename in field, if set
	global $filename_field;
	if (isset($filename_field))
		{
		update_field($r,$filename_field,$path);
		}
	# Add title
	update_field($r,8,$title);
	
	# get file metadata 
	global $exiftool_path;
	extract_exif_comment($r,$extension);
	
	# Ensure folder is created, then create previews.
	get_resource_path($r,false,"pre",true,$extension);	
	create_previews($r,false,$extension);

	# Pass back the newly created resource ID.
	return $r;
	}

function get_alternative_files($resource)
	{
	# Returns a list of alternative files for the given resource
	return sql_query("select ref,name,description,file_name,file_extension,file_size,creation_date from resource_alt_files where resource='$resource'");
	}
	
function add_alternative_file($resource,$name)
	{
	sql_query("insert into resource_alt_files(resource,name,creation_date) values ('$resource','$name',now())");
	return sql_insert_id();
	}
	
function delete_alternative_file($resource,$ref)
	{
	# Delete any uploaded file.
	$info=get_alternative_file($resource,$ref);
	$path=get_resource_path($resource, true, "", true, $info["file_extension"], -1, 1, false, "", $ref);
	if (file_exists($path)) {unlink($path);}
	
	# Delete the database row
	sql_query("delete from resource_alt_files where resource='$resource' and ref='$ref'");
	}
	
function get_alternative_file($resource,$ref)
	{
	# Returns the row for the requested alternative file
	$return=sql_query("select ref,name,description,file_name,file_extension,file_size,creation_date from resource_alt_files where resource='$resource' and ref='$ref'");
	if (count($return)==0) {return false;} else {return $return[0];}
	}
	
function save_alternative_file($resource,$ref)
	{
	# Saves the 'alternative file' edit form back to the database
	$sql="";
	
	# Uploaded file provided?
	if (array_key_exists("userfile",$_FILES))
    	{
    	# Fetch filename / path
    	$processfile=$_FILES['userfile'];
   	    $filename=strtolower(str_replace(" ","_",$processfile['name']));
    
    	# Work out extension
    	$extension=explode(".",$filename);$extension=trim(strtolower($extension[count($extension)-1]));

		# Find the path for this resource.
    	$path=get_resource_path($resource, true, "", true, $extension, -1, 1, false, "", $ref);

		if ($filename!="")
			{
			$result=move_uploaded_file($processfile['tmp_name'], $path);
			if ($result==false)
				{
				exit("File upload error. Please check the size of the file you are trying to upload.");
				}
			else
				{
				chmod($path,0777);
				$file_size=@filesize($path);
				$sql.=",file_name='" . escape_check($filename) . "',file_extension='" . escape_check($extension) . "',file_size='" . $file_size . "',creation_date=now()";
				}
			}

		}
	# Save data back to the database.
	sql_query("update resource_alt_files set name='" . getvalescaped("name","") . "',description='" . getvalescaped("description","") . "' $sql where resource='$resource' and ref='$ref'");
	}
	
function user_rating_save($ref,$rating)
	{
	# Save a user rating for a given resource
	$resource=get_resource_data($ref);
	
	# Recalculate the averate rating
	$total=$resource["user_rating_total"]; if ($total=="") {$total=0;}
	$count=$resource["user_rating_count"]; if ($count=="") {$count=0;}

	# Increment the total and count and work out a new average.
	$total+=$rating;
	$count++;
	$average=ceil($total/$count);
	
	# Save to the database
	sql_query("update resource set user_rating='$average',user_rating_total='$total',user_rating_count='$count' where ref='$ref'");
	}
		
function notify_user_contributed_submitted($refs)
	{
	// Send a notification mail to the administrators when resources are moved from "User Contributed - Pending Submission" to "User Contributed - Pending Review"
	global $notify_user_contributed_submitted,$applicationname,$email_notify,$baseurl,$lang;
	if (!$notify_user_contributed_submitted) {return false;} # Only if configured.
	
	$htmlbreak="";
	global $use_phpmailer;
	if ($use_phpmailer){$htmlbreak="<br><br>";}
	
	$list="";
	for ($n=0;$n<count($refs);$n++)
		{
		$url="";
		$url=$baseurl . "/?r=" . $refs[$n];
		
		if ($use_phpmailer){$url="<a href=\"$url\">$url</a>";}
		
		$list.=$htmlbreak . $url . "\n\n";
		}
		
	$list.=$htmlbreak;	
	
	$templatevars['url']=$baseurl . "/pages/search.php?search=!userpending";	
	$templatevars['list']=$list;
		
	$message=$lang["userresourcessubmitted"] . "\n\n". $templatevars['list'] . $lang["viewalluserpending"] . "\n\n" . $templatevars['url'];
	
	send_mail($email_notify,$applicationname . ": " . $lang["status-1"],$message,"","","emailnotifyresourcessubmitted",$templatevars);
	}
	
function notify_user_contributed_unsubmitted($refs)
	{
	// Send a notification mail to the administrators when resources are moved from "User Contributed - Pending Submission" to "User Contributed - Pending Review"
	
	global $notify_user_contributed_unsubmitted,$applicationname,$email_notify,$baseurl,$lang;
	if (!$notify_user_contributed_unsubmitted) {return false;} # Only if configured.
	
	$htmlbreak="";
	global $use_phpmailer;
	if ($use_phpmailer){$htmlbreak="<br><br>";}
	
	$list="";

	for ($n=0;$n<count($refs);$n++)
		{
		$url="";	
		$url=$baseurl . "/?r=" . $refs[$n];
		
		if ($use_phpmailer){$url="<a href=\"$url\">$url</a>";}
		
		$list.=$htmlbreak . $url . "\n\n";
		}
	
	$list.=$htmlbreak;		

	$templatevars['url']=$baseurl . "/pages/search.php?search=!userpending";	
	$templatevars['list']=$list;
		
	$message=$lang["userresourcesunsubmitted"]."\n\n". $templatevars['list'] . $lang["viewalluserpending"] . "\n\n" . $templatevars['url'];

	send_mail($email_notify,$applicationname . ": " . $lang["status-2"],$message,"","","emailnotifyresourcesunsubmitted",$templatevars);
	}	
	
	
function get_fields_with_options()
	{
	# Returns a list of fields that have option lists (checking user permissions)
	# Used for 'manage field options' page.
	$fields=sql_query("select ref, name, title, type, options ,order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown from resource_type_field where type=2 or type=3 order by resource_type,order_by");
	$return=array();
	# Apply permissions.
	for ($n=0;$n<count($fields);$n++)
		{
		if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"])) && 
		!checkperm("f-" . $fields[$n]["ref"]))
			{
			$return[]=$fields[$n];
			}
		}
	return $return;
	}

function get_field($field)
	{
	$return=sql_query("select ref, name, title, type, options ,order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown from resource_type_field where ref='$field'");
	if (count($return)>0) {return $return[0];} else {return false;}
	}

function get_field_options_with_stats($field)
	{
	# For a given field, list all options with usage stats.
	# This is for the 'manage field options' page.

	$rawoptions=sql_value("select options value from resource_type_field where ref='$field'","");
	$options=trim_array(explode(",",i18n_get_translated($rawoptions)));
	$rawoptions=trim_array(explode(",",$rawoptions));
	
	# For the given field, fetch a stats count for each keyword.
	$usage=sql_query("select rk.resource_type_field,k.keyword,count(*) c from resource_keyword rk join keyword k on rk.keyword=k.ref where resource_type_field='$field' group by k.keyword");
	
	$return=array();
	for ($n=0;$n<count($options);$n++)
		{
		# Find the option in the usage array and extract the count
		$count=0;
		for ($m=0;$m<count($usage);$m++)
			{
			$keyword=get_keyword_from_option($options[$n]);
			if ($keyword==$usage[$m]["keyword"]) {$count=$usage[$m]["c"];}
			}
			
		$return[]=array("option"=>$options[$n],"rawoption"=>$rawoptions[$n],"count"=>$count);
		}
	return $return;
	}
	
function save_field_options($field)
	{
	# Save the field options after editing.
	global $languages;
	
	$fielddata=get_field($field);
	$options=trim_array(explode(",",$fielddata["options"]));

	for ($n=0;$n<count($options);$n++)
		{
		if (getval("submit_field_" . $n,"")!="")
			{
			# This option/language combination is being renamed.

			# Construct a new option from the posted languages
			$new="";
			foreach ($languages as $langcode=>$langname)
				{
				$val=getvalescaped("field_" . $langcode . "_" . $n,"");
				if ($val!="") {$new.="~" . $langcode . ":" . $val;}
				}

			# Construct a new options value by creating a new array replacing the item in position $n
			$newoptions=array_merge(array_slice($options,0,$n),array($new),array_slice($options,$n+1));

			# Update the options field.
			sql_query("update resource_type_field set options='" . escape_check(join(", ",$newoptions)) . "' where ref='$field'");
			
			# Loop through all matching resources.
			# The matches list uses 'like' so could potentially return values that do not have this option set. However each value list split out and analysed separately.
			$matching=sql_query("select resource,value from resource_data where resource_type_field='$field' and value like '%" . escape_check($options[$n]) . "%'");
			for ($m=0;$m<count($matching);$m++)
				{
				$ref=$matching[$m]["resource"];
				#echo "Processing $ref to update " . $options[$n] . "<br>existing value is " . $matching[$m]["value"] . "<br/>";
								
				$set=trim_array(explode(",",$matching[$m]["value"]));
				
				# Construct a new value omitting the old and adding the new.
				$newval=array();
				for ($s=0;$s<count($set);$s++)
					{
					if ($set[$s]!==$options[$n]) {$newval[]=$set[$s];}
					}
				$newval[]=$new; # Set the new value on the end of this string
				$newval=join(",",$newval);
				
				#echo "Old value = '" . $matching[$m]["value"] . "', new value = '" . $newval . "'";
				
				if ($matching[$m]["value"]!=$newval)
					{
					# Value has changed. Update.

					# Delete existing keywords index for this field.
					sql_query("delete from resource_keyword where resource='$ref' and resource_type_field='$field'");
					
					# Store value and reindex
					update_field($ref,$field,$newval);
					}
				}
			
			}


		if (getval("delete_field_" . $n,"")!="")
			{
			# This field option is being deleted.
			
			# Construct a new options value by creating a new array ommitting the item in position $n
			$new=array_merge(array_slice($options,0,$n),array_slice($options,$n+1));
			
			sql_query("update resource_type_field set options='" . escape_check(join(", ",$new)) . "' where ref='$field'");
			
			# Loop through all matching resources.
			# The matches list uses 'like' so could potentially return values that do not have this option set. However each value list split out and analysed separately.
			$matching=sql_query("select resource,value from resource_data where resource_type_field='$field' and value like '%" . escape_check($options[$n]) . "%'");
			for ($m=0;$m<count($matching);$m++)
				{
				$ref=$matching[$m]["resource"];
				#echo "Processing $ref to remove " . $options[$n] . "<br>existing value is " . $matching[$m]["value"] . "<br/>";
								
				$set=trim_array(explode(",",$matching[$m]["value"]));
				$new=array();
				for ($s=0;$s<count($set);$s++)
					{
					if ($set[$s]!==$options[$n]) {$new[]=$set[$s];}
					}
				$new=join(",",$new);
				
				if ($matching[$m]["value"]!=$new)
					{
					# Value has changed. Update.

					# Delete existing keywords index for this field.
					sql_query("delete from resource_keyword where resource='$ref' and resource_type_field='$field'");
					
					# Store value and reindex
					update_field($ref,$field,$new);
					}
				}
			}
		}
	}
	
function get_resources_matching_keyword($keyword,$field)
	{
	# Returns an array of resource references for resources matching the given keyword string.
	$keyref=resolve_keyword($keyword);echo $keyref;
	return sql_array("select distinct resource value from resource_keyword where keyword='$keyref' and resource_type_field='$field'");
	}
	
function get_keyword_from_option($option)
	{
	# For the given field option, return the keyword that will be indexed.
	$keywords=split_keywords("," . $option);
	return $keywords[1];
	}
	
function add_field_option($field,$option)
	{
	sql_query("update resource_type_field set options=concat(options,', " . escape_check($option) . "') where ref='$field'");
	return true;
	}

if (!function_exists("get_resource_access")){	
function get_resource_access($resource)
	{
	# Returns the access that the currently logged-in user has to $resource.
	# Return values:
	# 0 = Full Access (download all sizes)
	# 1 = Restricted Access (download only those sizes that are set to allow restricted downloads)
	# 2 = Confidential (no access)
	
	# Load the 'global' access level set on the resource
	$resourcedata=get_resource_data($resource);
	$access=$resourcedata["access"];

	global $k;
	if ($k!="")
		{
		# External access - check how this was shared.
		$extaccess=sql_value("select access value from external_access_keys where access_key='" . escape_check($k) . "'",-1);
		if ($extaccess!=-1) {return $extaccess;}
		}
	
	if (checkperm("v"))
		{
		# Permission to access all resources
		# Always return 0
		return 0; 
		}
	
	if ($access==3)
		{
		# Load custom access level
		global $usergroup;
		$access=get_custom_access($resource,$usergroup);
		}

	# Check for user-specific access (overrides any other restriction)
	global $userref;
	$userspecific=get_custom_access_user($resource,$userref);	
	if ($userspecific!==false)
		{
		return $userspecific;
		}
		
	if ($access==0 && !checkperm("g"))
		{
		# User does not have the 'g' permission. Always return restricted for live resources.
		return 1; 
		}

	return $access;	
	}
}
	
function get_custom_access_user($resource,$user)
	{
	return sql_value("select access value from resource_custom_access where resource='$resource' and user='$user'",false);
	}

function resource_download_allowed($resource,$size)
	{
	# For the given resource and size, can the curent user download it?
	$access=get_resource_access($resource);
	
	# Full access
	if ($access==0)
		{
		return true;
		}
		
	# Restricted
	if ($access==1)
		{
		if ($size=="")
			{
			# Original file - access depends on the 'restricted_full_download' config setting.
			global $restricted_full_download;
			return $restricted_full_download;
			}
		else
			{
			# Return the restricted access setting for this resource type.
			return (sql_value("select allow_restricted value from preview_size where id='" . escape_check($size) . "'",0)==1);
			}
		}
		
	# Confidential
	if ($access==2)
		{
		return false;
		}
	
	}

?>
