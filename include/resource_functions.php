<?
# Resource functions
# Functions to create, edit and index resources

function create_resource($resource_type,$archive=-1,$user=-1)
	{
	# Create a new resource.

	if ($archive==-1)
		{
		# Work out an appropriate default state
		$archive=0;
		if (!checkperm("e0")) {$archive=2;} # Can't create a resource in normal state? create in archive.
		}
	if ($archive==-2)
		{
		# Work out user ref - note: only for content in status -2 (user submitted).
		global $userref;
		$user=$userref;
		} else {$user=-1;}
	
	sql_query("insert into resource(resource_type,creation_date,archive,created_by) values ('$resource_type',now(),'$archive','$user')");
	
	$insert=sql_insert_id();
	
	# Log this			
	daily_stat("Create resource",$insert);
	resource_log($insert,'c',0);

	return $insert;
	}
	
function save_resource_data($ref,$multi)
	{
	# Save all submitted data for resource $ref.
	# Also re-index all keywords from indexable fields.
		
	global $auto_order_checkbox;
	# Loop through the field data and save (if necessary)
	$errors=array();
	$resource_sql="";
	$fields=get_resource_field_data($ref,$multi);
	for ($n=0;$n<count($fields);$n++)
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
		else
			{
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
			
			if ($fields[$n]["keywords_index"]==1) {
				remove_keyword_mappings($ref,i18n_get_indexable($oldval),$fields[$n]["ref"]);
				add_keyword_mappings($ref,i18n_get_indexable($val),$fields[$n]["ref"]);
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
	# Also save related resources field
	sql_query("delete from resource_related where resource='$ref'"); # remove existing related items
	$related=explode(",",getvalescaped("related",""));
	# Make sure all submitted values are numeric
	$ok=array();for ($n=0;$n<count($related);$n++) {if (is_numeric(trim($related[$n]))) {$ok[]=trim($related[$n]);}}
	if (count($ok)>0) {sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$ok) . ")");}
	
	# Also update archive status and access level
	sql_query("update resource set archive='" . getvalescaped("archive",0) . "',access='" . getvalescaped("access",0) . "' where ref='$ref'");
	
	# For access level 3 (custom) - also save custom permissions
	if (getvalescaped("access",0)==3) {save_resource_custom_access($ref);}
	
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
			elseif ($fields[$n]["type"]==4)
				{
				# date type, construct the value from the date dropdowns
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
				
				# Append text mode?
				if (getval("modeselect_" . $fields[$n]["ref"],"")=="AP")
					{
					$val=$existing . " " . $origval;
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
						remove_keyword_mappings($ref,i18n_get_indexable($oldval),$fields[$n]["ref"]);
						add_keyword_mappings($ref,i18n_get_indexable($newval),$fields[$n]["ref"]);
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
			sql_query("delete from resource_related where resource='$ref'"); # remove existing related items
			if (count($ok)>0) {sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$ok) . ")");}
			}
		}

	# Also update archive status
	if (getval("editthis_status","")!="")
		{
		for ($m=0;$m<count($list);$m++)
			{
			$ref=$list[$m];
			sql_query("update resource set archive='" . getvalescaped("archive",0) . "' where ref='$ref'");
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
	}

function remove_keyword_mappings($ref,$string,$resource_type_field)
	{
	# Removes one instance of each keyword->resource mapping for each occurrence of that
	# keyword in $string.
	# This is used to remove keyword mappings when a field has changed.
	# We also decrease the hit count for each keyword.
	if (trim($string)=="") {return false;}
	$keywords=split_keywords($string,true);
	for ($n=0;$n<count($keywords);$n++)
		{
		#echo "<li>removing " . $keywords[$n];
		sql_query("delete from resource_keyword where resource='$ref' and keyword=(select ref from keyword where keyword='" . escape_check($keywords[$n]) . "') and resource_type_field='$resource_type_field' limit 1");
		sql_query("update keyword set hit_count=hit_count-1 where keyword='" . escape_check($keywords[$n]) . "' limit 1");
		}	
	}
	
function add_keyword_mappings($ref,$string,$resource_type_field)
	{
	# For each instance of a keyword in $string, add a keyword->resource mapping.
	# Create keywords that do not yet exist.
	# Increase the hit count of each keyword that matches.
	# Store the position and field the string was entered against for advanced searching.
	if (trim($string)=="") {return false;}
	$keywords=split_keywords($string,true);
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
	# Updates a field. Works out the previous value, so this is not efficient if we already know what this previous value is
	$existing=sql_value("select value from resource_data where resource='$resource' and resource_type_field='$field'","");
	remove_keyword_mappings($resource,$existing,$field);
	add_keyword_mappings($resource,$value,$field);
	sql_query("delete from resource_data where resource='$resource' and resource_type_field='$field' limit 1");
	$value=escape_check($value);
	sql_query("insert into resource_data(resource,resource_type_field,value) values ('$resource','$field','$value')");
	
	# Also update resource table?
	$column=sql_value("select resource_column value from resource_type_field where ref='$field'","");
	if (strlen($column)>0)
		{
		sql_query("update resource set $column = '$value' where ref='$resource'");
		}
	}
	

function email_resource($resource,$resourcename,$fromusername,$userlist,$message)
	{
	# Attempt to resolve all users in the string $userlist to user references.
	# Add $collection to these user's 'My Collections' page
	# Send them an e-mail linking to this collection
	global $baseurl,$email_from,$applicationname,$lang,$userref;
	
	if (trim($userlist)=="") {return ($lang["mustspecifyoneusername"]);}
	$userlist=resolve_userlist_groups($userlist);
	$ulist=trim_array(explode(",",$userlist));
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
			sql_query("insert into external_access_keys(resource,access_key,user) values ('$resource','$k','$userref');");
			$key="&k=". $k;
			}

		# Build message and send.
		$body="$fromusername " . $lang["hasemailedyouaresource"] . "$message\n\n" . $lang["clicktoviewresource"] . "\n\n" . $baseurl . "/?r=" . $resource . $key;
		send_mail($emails[$n],$subject,$body);
		}
		
	# Return an empty string (all OK).
	return "";
	}

function delete_resource($ref)
	{
	if ($ref<0) {return false;} # Can't delete the template
	# Delete the resource, all related entries in tables and all files on disk

	# Delete files first
	$extension=sql_value("select file_extension value from resource where ref='$ref'","jpg");
	if ($extension=="") {$extension="jpg";}
	$extension2=sql_value("select preview_extension value from resource where ref='$ref'","jpg");
	if ($extension2=="") {$extension2="jpg";}

	$sizes=get_image_sizes($ref,true,$extension);
	for ($n=0;$n<count($sizes);$n++)
		{
		$path=get_resource_path($ref,$sizes[$n]["id"],false,$extension);
		if (file_exists($path)) {unlink($path);}
		}
	$sizes=get_image_sizes($ref,true,$extension2);
	for ($n=0;$n<count($sizes);$n++)
		{
		# Also delete alternative previews (where extension is different)
		$path=get_resource_path($ref,$sizes[$n]["id"],false,$extension2);
		if (file_exists($path)) {unlink($path);}
		}

	$path=get_resource_path($ref,"",false,$extension);
	if (file_exists($path)) {unlink($path);}
	
	# Delete all database entries
	sql_query("delete from resource where ref='$ref'");
	sql_query("delete from resource_data where resource='$ref'");
	sql_query("delete from resource_keyword where resource='$ref'");
	sql_query("delete from resource_related where resource='$ref' or related='$ref'");
	sql_query("delete from collection_resource where resource='$ref'");
	sql_query("delete from resource_custom_access where resource='$ref'");
	sql_query("delete from external_access_keys where resource='$ref'");	
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
	
	# Set the access level for user contributed resources.
	global $userref;
	if (!checkperm("e0")) {sql_query("update resource set access=-2,created_by='$userref' where ref='$to'");}
	
	# User contributed but e0 permission, so can contribute straight to live - still need to set 'created by'
	if (!checkperm("c")) {sql_query("update resource set created_by='$userref' where ref='$to'");}
	
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
	sql_query("insert into resource_log(date,user,resource,type,resource_type_field) values (now(),'$userref','$resource','$type','$field')");
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
	$return=sql_query("select c.ref,c.theme,c.theme2,c.theme3,c.name,u.fullname from collection_resource cr join collection c on cr.collection=c.ref and cr.resource='$ref' and c.public=1 left outer join user u on c.user=u.ref order by length(theme) desc");
	# Combine the theme categories into one string so multiple category levels display correctly.
	for ($n=0;$n<count($return);$n++)
		{
		if (checkperm("j*") || checkperm("j" . $return[$n]["theme"]))
			{
			$theme="";
			if ($return[$n]["theme"]!="") {$theme=$return[$n]["theme"];}
			if ($return[$n]["theme2"]!="") {$theme.=" / " . $return[$n]["theme2"];}
			if ($return[$n]["theme3"]!="") {$theme.=" / " . $return[$n]["theme3"];}
			$return[$n]["theme"]=$theme;
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
		sql_query("delete from resource_related where resource='$ref'");  
		sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$array) . ")");
	}		

function get_exiftool_fields()
	{
	# Returns a list of exiftool fields, which are basically fields with an 'exiftool field' set.
	return sql_query("select ref,exiftool_field from resource_type_field where length(exiftool_field)>0 order by exiftool_field");
	}

function write_metadata($path,$ref)
	{
	global $exiftool_path,$exiftool_remove_existing;
	if (isset($exiftool_path))
		{
		if (file_exists(stripslashes($exiftool_path) . "/exiftool"))
			{
		      if(!is_dir("filestore/tmp")){mkdir("filestore/tmp",0777);}
				$filename = pathinfo($path);
				$filename = $filename['basename'];	
				$tmpfile="filestore/tmp/".$filename;				
				copy($path,$tmpfile);
				
				#Now that we have already copied the original file, we can use exiftool's overwrite_original on the tmpfile
				$command=$exiftool_path."/exiftool -overwrite_original ";
				if ($exiftool_remove_existing) {$command.="-EXIF:all -XMP:all= -IPTC:all= ";}
				$write_to=get_exiftool_fields();
				for($i=0;$i< count($write_to);$i++)
					{
					$field=explode(",",$write_to[$i]['exiftool_field']);
					foreach ($field as $field)
						{
						$command.="-".$field."=\"". str_replace("\"","\\\"",get_data_by_field($ref,$write_to[$i]['ref'])) . "\" " ;
						}
					}
			$command.=" $tmpfile";
 
			$output=shell_exec($command) or die("Problem writing metadata: $output <br />Command was: $command");
			return $tmpfile;
			}
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
	$temp=get_resource_path($r,"pre",true,$extension);	
	create_previews($r,false,$extension);

	# Pass back the newly created resource ID.
	return $r;
	}

?>