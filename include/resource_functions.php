<?php
# Resource functions
# Functions to create, edit and index resources

function create_resource($resource_type,$archive=999,$user=-1)
	{
	# Create a new resource.
	global $always_record_resource_creator;

	if ($archive==999)
		{
		# Work out an appropriate default state
		$archive=0;
		if (!checkperm("e0")) {$archive=2;} # Can't create a resource in normal state? create in archive.
		}
	if ($archive==-2 || $archive==-1 || (isset($always_record_resource_creator) and $always_record_resource_creator))
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

	# set defaults for resource here (in case there are edit filters that depend on them)
	set_resource_defaults($insert);	

	# Always index the resource ID as a keyword
	remove_keyword_mappings($insert, $insert, -1);
	add_keyword_mappings($insert, $insert, -1);

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
	$expiry_field_edited=false;
	$resource_data=get_resource_data($ref);
	
	for ($n=0;$n<count($fields);$n++)
		{
		if (!(
		
		# Not if field has write access denied
		checkperm("F" . $fields[$n]["ref"])
		||
		(checkperm("F*") && !checkperm("F-" . $fields[$n]["ref"]))
			
		))
			{
			if ($fields[$n]["type"]==2)
				{
				# construct the value from the ticked boxes
				$val=","; # Note: it seems wrong to start with a comma, but this ensures it is treated as a comma separated list by split_keywords(), so if just one item is selected it still does individual word adding, so 'South Asia' is split to 'South Asia','South','Asia'.
				$options=trim_array(explode(",",$fields[$n]["options"]));

				for ($m=0;$m<count($options);$m++)
					{
					$name=$fields[$n]["ref"] . "_" . urlencode($options[$m]);
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
				
				# Add time?
				if (getval("field_" . $fields[$n]["ref"] . "-h","")!="")
					{
					$val.=" " . getvalescaped("field_" . $fields[$n]["ref"] . "-h","");
					$val.=":" . getvalescaped("field_" . $fields[$n]["ref"] . "-i","");
					}
				else
					{
					# Blank date/time for invalid values.
					if (strlen($val)!=10) {$val="";}
					}
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
			elseif ($fields[$n]["type"] == 3)
				{
				$val=getvalescaped("field_" . $fields[$n]["ref"],"");				
				// if it doesn't already start with a comma, add one
				if (substr($val,0,1) != ',')
					{
					$val = ','.$val;
					}
				}
			else
				{
				# Set the value exactly as sent.
				$val=getvalescaped("field_" . $fields[$n]["ref"],"");
				} 
			if ($fields[$n]["value"]!== str_replace("\\","",$val))
				{
				# This value is different from the value we have on record.

				# Write this edit to the log (including the diff)
				resource_log($ref,'e',$fields[$n]["ref"],"",$fields[$n]["value"],$val);
				
				# Expiry field? Set that expiry date(s) have changed so the expiry notification flag will be reset later in this function.
				if ($fields[$n]["type"]==6) {$expiry_field_edited=true;}

				# If 'resource_column' is set, then we need to add this to a query to back-update
				# the related columns on the resource table
				$resource_column=$fields[$n]["resource_column"];

				# If this is a 'joined' field we need to add it to the resource column
				$joins=get_resource_table_joins();
				if (in_array($fields[$n]["ref"],$joins)){
					$val=strip_leading_comma($val);	
					sql_query("update resource set field".$fields[$n]["ref"]."='".escape_check($val)."' where ref='$ref'");
				}		

				global $use_resource_column_data;
				if ($use_resource_column_data){
					# By default, also write the resource table column mapping (if set)
					$write_column=true;

					# For metadata templates, support an alternative title field (so the original title field can be used as part of metadata)
					global $metadata_template_title_field,$metadata_template_resource_type;
					if (isset($metadata_template_title_field) && $metadata_template_resource_type==$resource_data["resource_type"])
						{
						if ($resource_column=="title") {$write_column=false;} # Do not write the original title.
						if ($metadata_template_title_field=$fields[$n]["ref"]) {$resource_column="title";} # Write the metadata template title to the title column instead.
						}

					# Add to resource column SQL
					if (strlen($resource_column)>0 && $write_column)
						{
						if ($resource_sql!="") {$resource_sql.=",";}
						if (trim($val)=="" || trim($val)==",")
							{
							# Insert null for empty columns.
							$resource_sql.=$resource_column . "=null";
							}
						else
							{
							$mapval=$val;
						
							# Fix for legacy systems using a 'rating' mapped to an integer rating column on the resource table  - when writing numeric values, remove any comma (rating was a dropdown box and the value is therefore prefixed with a comma)
							if (is_numeric(str_replace(",","",$mapval))) {$mapval=str_replace(",","",$mapval);}
						
							$resource_sql.=$resource_column . "='" . escape_check($mapval) . "'";
							}
						
						}
				}
				# Purge existing data and keyword mappings, decrease keyword hitcounts.
				sql_query("delete from resource_data where resource='$ref' and resource_type_field='" . $fields[$n]["ref"] . "'");
				
				# Insert new data and keyword mappings, increase keyword hitcounts.
				sql_query("insert into resource_data(resource,resource_type_field,value) values('$ref','" . $fields[$n]["ref"] . "','" . str_replace((!(strpos($val,"\'")===false)?"\'":"'"),"''",$val) ."')");
	
				$oldval=$fields[$n]["value"];
				
				if ($fields[$n]["type"]==3 && substr($oldval,0,1) != ',')
					{
					# Prepend a comma when indexing dropdowns
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

	# Always index the resource ID as a keyword
	remove_keyword_mappings($ref, $ref, -1);
	add_keyword_mappings($ref, $ref, -1);

	# save resource defaults
	set_resource_defaults($ref);	 
		
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

	# Expiry field(s) edited? Reset the notification flag so that warnings are sent again when the date is reached.
	$expirysql="";
	if ($expiry_field_edited) {$expirysql=",expiry_notification_sent=0";}

	# Also update archive status and access level
	if (!checkperm("F*")) # Only if 'full' access (not selected fields only).
		{
		sql_query("update resource set archive='" . getvalescaped("archive",0,true) . "',access='" . getvalescaped("access",0,true) . "' $expirysql where ref='$ref'");
		}
	# For access level 3 (custom) - also save custom permissions
	if (getvalescaped("access",0)==3) {save_resource_custom_access($ref);}

	# Update XML metadata dump file
	update_xml_metadump($ref);		
	
	hook("aftersaveresourcedata");

	if (count($errors)==0) {return true;} else {return $errors;}
	}
	


function set_resource_defaults($ref) 
	{	
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
	}

function save_resource_data_multi($collection)
	{
	# Save all submitted data for collection $collection, this is for the 'edit multiple resources' feature
	# Loop through the field data and save (if necessary)
	$list=get_collection_resources($collection);
	$ref=$list[0];
	$fields=get_resource_field_data($ref,true);
	global $auto_order_checkbox;
	$expiry_field_edited=false;

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
					$name=$fields[$n]["ref"] . "_" . urlencode($options[$m]);
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
					
				$val=strip_leading_comma($val);		
				#echo "<li>existing=$existing, new=$val";
				if ($existing!=str_replace("\\","",$val))
					{
					# This value is different from the value we have on record.
					
					# Write this edit to the log.
					resource_log($ref,'m',$fields[$n]["ref"],"",$existing,$val);
		
					# Expiry field? Set that expiry date(s) have changed so the expiry notification flag will be reset later in this function.
					if ($fields[$n]["type"]==6) {$expiry_field_edited=true;}
				
					# If this is a 'joined' field we need to add it to the resource column
					$joins=get_resource_table_joins();
					if (in_array($fields[$n]["ref"],$joins)){
						sql_query("update resource set field".$fields[$n]["ref"]."='".escape_check($val)."' where ref='$ref'");
					}		
					
					global $use_resource_column_data;
					if ($use_resource_column_data){
						# If 'resource_column' is set, then we need to add this to a query to back-update
						# the related columns on the resource table
						if (strlen($fields[$n]["resource_column"])>0)
							{	
							sql_query("update resource set " . $fields[$n]["resource_column"] . "='" . escape_check($val) . "' where ref='$ref'");
							}
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
	
	# Expiry field(s) edited? Reset the notification flag so that warnings are sent again when the date is reached.
	if ($expiry_field_edited)
		{
		if (count($list)>0)
			{
			sql_query("update resource set expiry_notification_sent=0 where ref in (" . join(",",$list) . ")");
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
		
		
	# Update XML metadata dump file for all edited resources.
	for ($m=0;$m<count($list);$m++)
		{
		update_xml_metadump($list[$m]);
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
		$kw=substr($keywords[$n],0,100); # Trim keywords to 100 chars as this is the length of the keywords column.
		
		global $noadd;
		if (!(in_array($kw,$noadd)))
			{
			#echo "<li>adding " . $keywords[$n];
			$keyword=resolve_keyword($kw);
			if ($keyword===false)
				{
				# This is a new keyword. Create and discover the new keyword ref.
				sql_query("insert into keyword(keyword,soundex,hit_count) values ('" . escape_check($kw) . 	"',left(soundex('" . escape_check($kw) . "'),10),0)");
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
	$fieldinfo=sql_query("select keywords_index,resource_column,partial_index,type from resource_type_field where ref='$field'");
	if (count($fieldinfo)==0) {return false;} else {$fieldinfo=$fieldinfo[0];}
	
	if ($fieldinfo["keywords_index"])
		{
		# Fetch previous value and remove the index for those keywords
		$existing=sql_value("select value from resource_data where resource='$resource' and resource_type_field='$field'","");
		remove_keyword_mappings($resource,i18n_get_indexable($existing),$field,$fieldinfo["partial_index"]);
		
		if (($fieldinfo['type'] == 2 || $fieldinfo['type'] == 3 || $fieldinfo['type'] == 7) && substr($value,0,1) <> ','){
			$value = ','.$value;
		}
		
		$value=strip_leading_comma($value);	
		
		# Index the new value
		add_keyword_mappings($resource,i18n_get_indexable($value),$field,$fieldinfo["partial_index"]);
		}
		
	# Delete the old value (if any) and add a new value.
	sql_query("delete from resource_data where resource='$resource' and resource_type_field='$field' limit 1");
	$value=escape_check($value);
	sql_query("insert into resource_data(resource,resource_type_field,value) values ('$resource','$field','$value')");
	
	if ($value=="") {$value="null";} else {$value="'" . $value . "'";}
	
	# Also update resource table?
	global $use_resource_column_data;
	if ($use_resource_column_data){
	$column=$fieldinfo["resource_column"];
	if (strlen($column)>0)
		{
		sql_query("update resource set $column = $value where ref='$resource'");
		}
	}	
		
	# If this is a 'joined' field we need to add it to the resource column
	$joins=get_resource_table_joins();
	if (in_array($field,$joins)){
		sql_query("update resource set field".$field."=".$value." where ref='$resource'");
		}			
		
	}

if (!function_exists("email_resource")){	
function email_resource($resource,$resourcename,$fromusername,$userlist,$message,$access=-1,$expires="",$useremail="",$from_name="")
	{
	# Attempt to resolve all users in the string $userlist to user references.

	global $baseurl,$email_from,$applicationname,$lang,$userref;
	
	if ($useremail==""){$useremail=$email_from;}
	
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
	if ($fromusername==""){$fromusername=$applicationname;} // fromusername is used for describing the sender's name inside the email
	if ($from_name==""){$from_name=$applicationname;} // from_name is for the email headers, and needs to match the email address (app name or user name)
	
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
		$templatevars['from_name']=$from_name;
		
		# Build message and send.
		$body=$templatevars['fromusername']." ". $lang["hasemailedyouaresource"] . $templatevars['message']."\n\n" . $lang["clicktoviewresource"] . "\n\n" . $templatevars['url'];
		send_mail($emails[$n],$subject,$body,$fromusername,$useremail,"emailresource",$templatevars,$from_name);
		
		# log this
		resource_log($resource,"E","",$notes=$ulist[$n]);
		
		}
		
	# Return an empty string (all OK).
	return "";
	}
}

function delete_resource($ref)
	{
	# Delete the resource, all related entries in tables and all files on disk
	
	if ($ref<0) {return false;} # Can't delete the template

	$current_state=sql_value("select archive value from resource where ref='$ref'",0);

	global $resource_deletion_state;
	if (isset($resource_deletion_state) && $current_state!=3) # Really delete if already in the 'deleted' state.
		{
		# $resource_deletion_state is set. Do not delete this resource, instead move it to the specified state.
		
		sql_query("update resource set archive='" . $resource_deletion_state . "' where ref='" . $ref . "'");
		
		# Remove the resource from any collections
		sql_query("delete from collection_resource where resource='$ref'");
			
		return true;
		}
	
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
	sql_query("delete from resource_alt_files where resource='$ref'");
		
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
	
	# copy joined fields to the resource column
	$joins=get_resource_table_joins();
	$joins_sql="";
	foreach ($joins as $join){
		$joins_sql.=",field$join ";
	}
	
	global $use_resource_column_data;
	$add="";
	if ($use_resource_column_data){$add="title,country,";}
	
	# First copy the resources row
	sql_query("insert into resource($add resource_type,creation_date,rating,archive,access,created_by $joins_sql) select $add" . (($resource_type==-1)?"resource_type":("'" . $resource_type . "'")) . ",creation_date,rating,archive,access,created_by $joins_sql from resource where ref='$from';");
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
	global $always_record_resource_creator;
	if ((!checkperm("c")) || $archive<0 || (isset($always_record_resource_creator) && $always_record_resource_creator))
		{
		# Update the user record
		sql_query("update resource set created_by='$userref' where ref='$to'");

		# Also add the user's username and full name to the keywords index so the resource is searchable using this name.
		global $username,$userfullname;
		add_keyword_mappings($to,$username . " " . $userfullname,-1);
		}
	
	# Now copy all data
	sql_query("insert into resource_data(resource,resource_type_field,value) select '$to',rd.resource_type_field,rd.value from resource_data rd join resource r on rd.resource=r.ref join resource_type_field rtf on rd.resource_type_field=rtf.ref and (rtf.resource_type=r.resource_type or rtf.resource_type=999 or rtf.resource_type=0) where rd.resource='$from'");

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
	
function resource_log($resource,$type,$field,$notes="",$fromvalue="",$tovalue="",$usage=0)
	{
	global $userref;
	
	# Do not log edits to user templates.
	if ($resource<0) {return false;}
	
	# Add difference to file.
	$diff="";
	if ($field!="" && ($fromvalue !== $tovalue))
		{
		$diff=log_diff($fromvalue,$tovalue);
		}
	
	sql_query("insert into resource_log(date,user,resource,type,resource_type_field,notes,diff,usageoption) values (now()," . (($userref!="")?"'$userref'":"null") . ",'$resource','$type'," . (($field!="")?"'$field'":"null") . ",'" . escape_check($notes) . "','" . escape_check($diff) . "','$usage')");
	}

function get_resource_log($resource)
	{
	return sql_query("select r.date,u.username,u.fullname,r.type,f.title,r.notes,r.diff,r.usageoption from resource_log r left outer join user u on u.ref=r.user left outer join resource_type_field f on f.ref=r.resource_type_field where resource='$resource' order by r.date");
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
	
	# Clear data that is no longer needed (data/keywords set for other types).
	sql_query("delete from resource_data where resource='$ref' and resource_type_field not in (select ref from resource_type_field where resource_type='$type' or resource_type=999 and resource_type=0)");
	sql_query("delete from resource_keyword where resource='$ref' and resource_type_field not in (select ref from resource_type_field where resource_type='$type' or resource_type=999 and resource_type=0)");	
	
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
	return sql_query("select ref,type,exiftool_field,options,name from resource_type_field where length(exiftool_field)>0 and (resource_type='$resource_type' or resource_type='0')  order by exiftool_field");
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
						
						# Remove initial comma (for checkbox lists)
						if (substr($writevalue,0,1)==",") {$writevalue=substr($writevalue,1);}
						
						foreach ($field as $field)
							{
							$command.="-".$field."=\"". str_replace("\"","\\\"",$writevalue) . "\" " ;
							}
						}
					$command.=" '$tmpfile'";
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

function import_resource($path,$type,$title,$ingest=false)
	{
	# Import the resource at the given path
	# This is used by staticsync.php and Camillo's SOAP API
	# Note that the file will be used at it's present location and will not be copied.

	global $syncdir;

	# Create resource
	$r=create_resource($type);
			
	# Work out extension based on path
	$extension=explode(".",$path);$extension=trim(strtolower(end($extension)));

	# file_path should only really be set to indicate a staticsync location. Otherwise, it should just be left blank.
	if ($ingest){$file_path="";} else {$file_path=escape_check($path);}

	# Store extension/data in the database
	sql_query("update resource set archive=0,file_path='".$file_path."',file_extension='$extension',preview_extension='$extension',file_modified=now() where ref='$r'");
			
	# Store original filename in field, if set
	if (!$ingest)
		{
		# This file remains in situ; store the full path in file_path to indicate that the file is stored remotely.
		global $filename_field;
		if (isset($filename_field))
			{
			global $use_resource_column_data;
			if (!$use_resource_column_data){
				$s=explode("/",$path);
				$filename=end($s);
				} 
			else {
				$filename=$path; // which will update file_path as well in old installs. 
				}
			update_field($r,$filename_field,$filename);
			}
		}
	else
		{
		# This file is being ingested. Store only the filename.
		$s=explode("/",$path);
		$filename=end($s);
		
		global $filename_field;
		if (isset($filename_field))
			{
			update_field($r,$filename_field,$filename);
			}
			
		
		# Move the file
		global $syncdir;
		$destination=get_resource_path($r,true,"",true,$extension);	
		$result=rename($syncdir . "/" . $path,$destination);
		if ($result===false)
			{
			# The rename failed. The file is possibly still being copied or uploaded and must be ignored on this pass.
			# Delete the resouce just created and return false.
			delete_resource($r);
			return false;
			}
		//chmod($destination,0777);
		}

	# Add title
	update_field($r,8,$title);
	
	# get file metadata 
	extract_exif_comment($r,$extension);
	
	# Ensure folder is created, then create previews.
	get_resource_path($r,false,"pre",true,$extension);	
	
	# Generate previews/thumbnails (if configured i.e if not completed by offline process 'create_previews.php')
	global $enable_thumbnail_creation_on_upload;
	if ($enable_thumbnail_creation_on_upload) {create_previews($r,false,$extension);}

	# Pass back the newly created resource ID.
	return $r;
	}

function get_alternative_files($resource)
	{
	# Returns a list of alternative files for the given resource
	return sql_query("select ref,name,description,file_name,file_extension,file_size,creation_date from resource_alt_files where resource='$resource'");
	}
	
function add_alternative_file($resource,$name,$description="",$file_name="",$file_extension="",$file_size=0)
	{
	sql_query("insert into resource_alt_files(resource,name,creation_date,description,file_name,file_extension,file_size) values ('$resource','" . escape_check($name) . "',now(),'" . escape_check($description) . "','" . escape_check($file_name) . "','" . escape_check($file_extension) . "','" . escape_check($file_size) . "')");
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

		# Debug
		debug("Uploading alternative file $ref with extension $extension to $path");

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
			
			# Preview creation for alternative files (enabled via config)
			global $alternative_file_previews;
			if ($alternative_file_previews)
				{
				create_previews($resource,false,$extension,false,false,$ref);
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
				
				if ($matching[$m]["value"]!== $newval)
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
				
				if ($matching[$m]["value"]!== $new)
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
	# $resource may be a resource_data array from a search, in which case, many of the permissions checks are already done.	
		
	# Returns the access that the currently logged-in user has to $resource.
	# Return values:
	# 0 = Full Access (download all sizes)
	# 1 = Restricted Access (download only those sizes that are set to allow restricted downloads)
	# 2 = Confidential (no access)
	
	# Load the 'global' access level set on the resource
	# In the case of a search, resource type and global,group and user access are passed through to this point, to avoid multiple unnecessary get_resource_data queries.
	# passthru signifies that this is the case, so that blank values in group or user access mean that there is no data to be found, so don't check again .
	$passthru="no";

	if (!is_array($resource)){
	$resourcedata=get_resource_data($resource,true);
	}
	else {
	$resourcedata=$resource;
	$passthru="yes";
	}
	
	$access=$resourcedata["access"];
	$resource_type=$resourcedata['resource_type'];
	
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
		if ($passthru=="no"){ 
			global $usergroup;
			$access=get_custom_access($resource,$usergroup);
			//echo "checked group access: ".$access;
			} 
		else {
			$access=$resource['group_access'];
		}
	}

	# Check for user-specific access (overrides any other restriction)
	global $userref;

	if ($passthru=="no"){
		$userspecific=get_custom_access_user($resource,$userref);	
		//echo "checked user access: ".$userspecific;
		} 
	else {
		$userspecific=$resourcedata['user_access'];
		}

		
	if ($userspecific!="")
		{
		return $userspecific;
		}
		
	if ($access==0 && !checkperm("g"))
		{
		# User does not have the 'g' permission. Always return restricted for live resources.
		return 1; 
		}
	
	if (checkperm('X'.$resource_type)){
		// this resource type is always restricted for this user group
		return 1;
	}

	return $access;	
	}
}
	
function get_custom_access_user($resource,$user)
	{
	return sql_value("select access value from resource_custom_access where resource='$resource' and user='$user' and (user_expires is null or user_expires>now())",false);
	}

function resource_download_allowed($resource,$size)
	{

	# For the given resource and size, can the curent user download it?
	# resource type and access may already be available in the case of search, so pass them along to get_resource_access to avoid extra queries
	# $resource can be a resource-specific search result array.
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

function get_edit_access($resource,$status=-999,$metadata=false)
	{
	# For the provided resource and metadata, does the  edit access does the current user have to this resource?
	# Checks the edit permissions (e0, e-1 etc.) and also the group edit filter which filters edit access based on resource metadata.
	
	global $userref,$usereditfilter;
	
	if ($status==-999)
		{
		# Status not provided. Calculate status
		$status=sql_value("select archive value from resource where ref='$resource'",0);
		}
	
	if ($resource==0-$userref) {return true;} # Can always edit their own user template.

	if (!checkperm("e" . $status)) {return false;} # Must have edit permission to this resource first and foremost, before checking the filter.
	
	$gotmatch=false;
	if (trim($usereditfilter)=="")
		{
		return true;
		}
	else
		{
		# An edit filter has been set. Perform edit filter processing to establish if the user can edit this resource.
		
		# Always load metadata, because the provided metadata may be missing fields due to permissions.
		$metadata=get_resource_field_data($resource,false,false);
				
		for ($n=0;$n<count($metadata);$n++)
			{
			$name=$metadata[$n]["name"];
			$value=$metadata[$n]["value"];			
			if ($name!="")
				{
				$match=filter_match($usereditfilter,$name,$value);
				if ($match==1) {return false;} # The match for this field was incorrect, always fail in this event.
				if ($match==2) {$gotmatch=true;} # The match for this field was correct.
				}
			}
		}
	
	# Default after all filter operations, allow edit.
	return $gotmatch;
	}


function filter_match($filter,$name,$value)
	{
	# In the given filter string, does name/value match?
	# Returns:
	# 0 = no match for name
	# 1 = matched name but value was not present
	# 2 = matched name and value was correct
	$s=explode(";",$filter);
	foreach ($s as $condition)
		{
		$s=explode("=",$condition);
		$checkname=$s[0];
		if ($checkname==$name)
			{
			$checkvalues=$s[1];
			
			$s=explode("|",strtoupper($checkvalues));
			$v=trim_array(explode(",",strtoupper($value)));
			foreach ($s as $checkvalue)
				{
				if (in_array($checkvalue,$v)) {return 2;}
				}
			return 1;
			}
		}
	return 0;
	}
	
function log_diff($fromvalue,$tovalue)	
	{
	# Forumlate descriptive text to describe the change made to a metadata field.

	# Remove any database escaping
	$fromvalue=str_replace("\\","",$fromvalue);
	$tovalue=str_replace("\\","",$tovalue);
	
	if (substr($fromvalue,0,1)==",")
		{
		# Work a different way for checkbox lists.
		$fromvalue=explode(",",i18n_get_translated($fromvalue));
		$tovalue=explode(",",i18n_get_translated($tovalue));
		
		# Get diffs
		$inserts=array_diff($tovalue,$fromvalue);
		$deletes=array_diff($fromvalue,$tovalue);

		# Process array diffs into meaningful strings.
		$return="";
		if (count($deletes)>0)
			{
			$return.="- " . join("\n- " , $deletes);
			}
		if (count($inserts)>0)
			{
			if ($return!="") {$return.="\n";}
			$return.="+ " . join("\n+ ", $inserts);
			}
		
		#debug($return);
		return $return;
		}

	# For standard strings, use Text_Diff
		
	require_once '../lib/Text_Diff/Diff.php';
	require_once '../lib/Text_Diff/Diff/Renderer/inline.php';

	$lines1 = explode("\n",$fromvalue);
	$lines2 = explode("\n",$tovalue);

	$diff     = new Text_Diff('native', array($lines1, $lines2));
	$renderer = new Text_Diff_Renderer_inline();
	$diff=$renderer->render($diff);
	
	$return="";

	# The inline diff syntax places inserts within <ins></ins> tags and deletes within <del></del> tags.

	# Handle deletes
	if (strpos($diff,"<del>")!==false)
		{
		$s=explode("<del>",$diff);
		for ($n=1;$n<count($s);$n++)
			{
			$t=explode("</del>",$s[$n]);
			if ($return!="") {$return.="\n";}
			$return.="- " . trim(i18n_get_translated($t[0]));
			}
		}
	# Handle inserts
	if (strpos($diff,"<ins>")!==false)
		{
		$s=explode("<ins>",$diff);
		for ($n=1;$n<count($s);$n++)
			{
			$t=explode("</ins>",$s[$n]);
			if ($return!="") {$return.="\n";}
			$return.="+ " . trim(i18n_get_translated($t[0]));
			}
		}


	#debug ($return);
	return $return;
	}
	
function update_xml_metadump($resource)
	{
	# Updates the XML metadata dump file when the resource has been altered.
	global $xml_metadump,$xml_metadump_dc_map;
	if (!$xml_metadump || $resource < 0) {return true;} # Only execute when configured and when not a template
	
	$path=dirname(get_resource_path($resource,true,"",true)) . "/metadump.xml";
	$f=fopen($path,"w");
	fwrite($f,"<?xml version=\"1.0\"?>\n");
	fwrite($f,"<record xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" resourcespace:resourceid=\"$resource\">\n\n");
  
  	$data=get_resource_field_data($resource,false,false); # Get field data ignoring permissions
  	for ($n=0;$n<count($data);$n++)
	  	{
	  	if (array_key_exists($data[$n]["name"],$xml_metadump_dc_map))
	  		{
	  		# Dublin Core field
	  		fwrite($f,"<dc:" . $xml_metadump_dc_map[$data[$n]["name"]] . " ");
	  		$endtag="</dc:" . $xml_metadump_dc_map[$data[$n]["name"]] . ">";
	  		}
	  	else
	  		{
	  		# No Dublin Core mapping. RS specific field format.
	  		fwrite($f,"<resourcespace:field ");
	  		$endtag="</resourcespace:field>";
	  		}
	  		
	  	# Value processing
	  	$value=$data[$n]["value"];
	  	if (substr($value,0,1)==",") {$value=substr($value,1);} # Checkbox lists / dropdowns; remove initial comma
	  	
	  	# Write metadata
	  	fwrite($f,"rsfieldtitle=\"" . htmlspecialchars($data[$n]["title"]) . "\" rsembeddedequiv=\"" . htmlspecialchars($data[$n]["exiftool_field"]) . "\" rsfieldref=\"" . htmlspecialchars($data[$n]["resource_type_field"]) . "\" rsfieldtype=\"" . htmlspecialchars($data[$n]["type"]) . "\">" . htmlspecialchars($value) . $endtag . "\n\n");
	  	}

	fwrite($f,"</record>\n");
	fclose($f);
	}

function get_metadata_templates()
	{
	# Returns a list of all metadata templates; i.e. resources that have been set to the resource type specified via '$metadata_template_resource_type'.
	global $metadata_template_resource_type,$metadata_template_title_field;
	return sql_query("select ref,field$metadata_template_title_field from resource where resource_type='$metadata_template_resource_type' order by field$metadata_template_title_field");
	}
 
function get_resource_collections($ref)
	{
	global $userref;
	
	# Returns a list of user collections.
	$sql="";
   
    # Include themes in my collecions? 
    # Only filter out themes if $themes_in_my_collections is set to false in config.php
   	global $themes_in_my_collections;
   	if (!$themes_in_my_collections)
   		{
   		if ($sql!="") {$sql.=" and ";}
   		$sql.="(length(c.theme)=0 or c.theme is null) ";
   		}
	if ($sql!="") {$sql="where " . $sql;}
   
	$return=sql_query ("select * from 
	(select c.*,u.username,count(r.resource) count from user u join collection c on u.ref=c.user and c.user='$userref' left outer join collection_resource r on c.ref=r.collection group by c.ref
	union
	select c.*,u.username,count(r.resource) count from user_collection uc join collection c on uc.collection=c.ref and uc.user='$userref' and c.user<>'$userref' left outer join collection_resource r on c.ref=r.collection left join user u on c.user=u.ref group by c.ref) clist where clist.ref in (select collection from collection_resource cr where cr.resource=$ref)");
	
	return $return;
	}
	
function download_summary($resource)
	{
	# Returns a summary of downloads by usage type
	return sql_query("select usageoption,count(*) c from resource_log where resource='$resource' and type='D' group by usageoption order by usageoption");
	}
?>
