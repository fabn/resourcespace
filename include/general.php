<?
# General functions, useful across the whole solution

function get_resource_path($ref,$size,$generate,$extension="jpg",$scramble=-1,$page=1,$watermarked=false)
	{
	# returns the correct path to resource $ref of size $size ($size==empty string is original resource)
	# If one or more of the folders do not exist, and $generate=true, then they are generated

	if ($size=="")
		{
		# For the full size, check to see if the full path is set and if so return that.
		$fp=sql_value("select file_path value from resource where ref='$ref'","");
		
		# Test to see if this nosize file is of the extension asked for, else skip the file_path and return a filestore path. 
		# If using staticsync, file path will be set already, but we still want the filestore path for a nosize preview jpg.
		# Also, returning the original filename when a nosize 'jpg' is looked for is no good, since imagemagick.php deletes $target.
		
		$test_ext = explode(".",$fp);$test_ext=trim(strtolower($test_ext[count($test_ext)-1]));
		
		if (($test_ext == $extension)){
		
		if ((strlen($fp)>0) && (strpos($fp,"/")!==false))
			{
			global $syncdir;  
            return $syncdir . "/" . $fp;
            }
		}
		}

	global $scramble_key;	
	if ($scramble===-1)
		{
		# Find the system default scramble setting if not specified
		if (isset($scramble_key) && ($scramble_key!="")) {$scramble=true;} else {$scramble=false;}
		}
	
	if ($scramble)
		{
		# Create a scrambled path using the scramble key
		# It should be very difficult or impossible to work out the scramble key, and therefore access
		# other resources, based on the scrambled path of a single resource.
		$scramblepath=substr(md5($ref . "_" . $scramble_key),0,15);
		}
	
	if ($extension=="") {$extension="jpg";}
	$folder="filestore/";
	#if (!file_exists(dirname(__FILE__) . $folder)) {mkdir(dirname(__FILE__) . $folder,0777);}
	
	for ($n=0;$n<strlen($ref);$n++)
		{
		$folder.=substr($ref,$n,1);
		if (($scramble) && ($n==(strlen($ref)-1))) {$folder.="_" . $scramblepath;}
		$folder.="/";
		#echo "<li>" . $folder;
		if ((!(file_exists($folder))) && $generate) {mkdir($folder,0777);chmod($folder,0777);}
		}
		
	# Add the page to the filename for everything except page 1.
	if ($page==1) {$p="";} else {$p="_" . $page;}
		
	# Add the watermarked url too
	if ($watermarked) {$p.="_wm";}
	
	return $folder . $ref . $size . $p . "." . $extension;
	}
	
function get_resource_data($ref)
	{
	# Returns basic resource data (from the resource table alone) for resource $ref.
	# For 'dynamic' field data, see get_resource_field_data
	$resource=sql_query("select * from resource where ref='$ref'");
	if (count($resource)==0) 
		{
		if ($ref>0)
			{
			return false;
			}
		else
			{
			# For batch upload templates (negative reference numbers), generate a new resource.
			sql_query("insert into resource (ref) values ('$ref')");
			$resource=sql_query("select * from resource where ref='$ref'");
			}
		}
	# update hit count
	sql_query("update resource set hit_count=hit_count+1 where ref='$ref'");
	return $resource[0];
	}

function get_resource_field_data($ref,$multi=false)
	{
	# Returns field data and field properties (resource_type_field and resource_data tables)
	# for this resource, for display in an edit / view form.
	$return=array();
	$fields=sql_query("select *,f.required frequired,f.ref fref from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource='$ref' where (f.resource_type=0 or f.resource_type=999 " . (($multi)?"":" or f.resource_type in (select resource_type from resource where ref='$ref')") . ") order by f.order_by,f.ref");
	for ($n=0;$n<count($fields);$n++)
		{
		if (checkperm("f*") || checkperm("f" . $fields[$n]["fref"])) {$return[]=$fields[$n];}
		}
	return $return;
	}

function get_resource_field_data_batch($refs)
	{
	# Returns field data and field properties (resource_type_field and resource_data tables)
	# for all the resource references in the array $refs.
	# This will use a single SQL query and is therefore a much more efficient way of gathering
	# resource data for a list of resources (e.g. search result display for a page of resources).
	if (count($refs)==0) {return array();} # return an empty array if no resources specified (for empty result sets)
	$refsin=join(",",$refs);
	$results=sql_query("select d.resource,f.*,d.value from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource in ($refsin) where (f.resource_type=0 or f.resource_type in (select resource_type from resource where ref=d.resource)) order by d.resource,f.order_by,f.ref");
	$return=array();
	$res=0;
	for ($n=0;$n<count($results);$n++)
		{
		if ($results[$n]["resource"]!=$res)
			{
			# moved on to the next resource
			if ($res!=0) {$return[$res]=$resdata;}
			$resdata=array();
			$res=$results[$n]["resource"];
			}
		#echo "<li>" . $res . " - " . $results[$n]["ref"] . ":" . $results[$n];
		# copy name/value into resdata array
		$resdata[$results[$n]["ref"]]=$results[$n];
		}
	$return[$res]=$resdata;
	return $return;
	}
	
function get_resource_types()
	{
	# Returns a list of resource types.
	$r=sql_query("select * from resource_type order by ref");
	# Translate names
	for ($n=0;$n<count($r);$n++)
		{
		$r[$n]["name"]=i18n_get_translated($r[$n]["name"]);
		}
	return $r;
	}

function get_resource_top_keywords($resource,$count)
	{
	# Return the top $count keywords (by hitcount) used by $resource.
	# This is for the 'Find Similar' search.
	# Keywords that are too short or too long, or contain numbers are dropped - they are probably not as meaningful in
	# the contexts of this search (consider being offered "12" or "OKB-34" as an option?)
	return sql_array("select distinct k.ref,k.keyword value from keyword k,resource_keyword r,resource_type_field f where k.ref=r.keyword and r.resource='$resource' and f.ref=r.resource_type_field and f.use_for_similar=1 and length(k.keyword)>=3 and length(k.keyword)<=15 and k.keyword not like '%0%' and k.keyword not like '%1%' and k.keyword not like '%2%' and k.keyword not like '%3%' and k.keyword not like '%4%' and k.keyword not like '%5%' and k.keyword not like '%6%' and k.keyword not like '%7%' and k.keyword not like '%8%' and k.keyword not like '%9%' order by k.hit_count desc limit $count");
	}

function split_keywords($search,$index=false)
	{
	# Takes $search and returns an array of individual keywords.
	
	# Remove any real / unescaped lf/cr
	$search=str_replace("\r"," ",$search);
	$search=str_replace("\n"," ",$search);
	$search=str_replace("\\r"," ",$search);
	$search=str_replace("\\n"," ",$search);

	/*
	# We used to 'clean' strings but this did not work for multiple languages.
	# This became pointless anyway when splitting using multiple chars later on
	# so this block has been commented.
	$validchars=" abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789nueaeiou:,;-/";
	$ns="";
	for ($n=0;$n<strlen($search);$n++)
		{
		$c=substr($search,$n,1);
		if (strpos($validchars,$c)!==false) {$ns.=$c;}
		}
	*/
	$ns=trim_spaces($search);
	if ((substr($ns,0,1)==",") ||  ($index==false && strpos($ns,":")!==false)) # special 'constructed' query type, split using comma so
	# we support keywords with spaces.
		{
		$ns=str_replace(array("/","_",".","; ","-","(",")","'","\"","\\")," ",$ns);
		$ns=trim_spaces($ns);
		$return=explode(",",strtolower($ns));
		# If we are indexing, append any values that contain spaces.
					
		# Important! Solves the searching for keywords with spaces issue.
		# Consider: for any keyword that has spaces, append to the array each individual word too
		# so for example: South Asia,USA becomes South Asia,USA,South,Asia
		# so a plain search for 'south asia' will match those with the keyword 'south asia' because the resource
		# will also be linked to the words 'south' and 'asia'.
		if ($index)
			{
			$return2=$return;
			for ($n=0;$n<count($return);$n++)
				{
				$keyword=trim($return[$n]);
				if (strpos($keyword," ")!==false)
					{
					# append each word
					$words=explode(" ",$keyword);
					for ($m=0;$m<count($words);$m++) {$return2[]=trim($words[$m]);}
					}
				}
			return trim_array($return2);
			}
		else
			{
			return trim_array($return);
			}
		}
	else
		{
		# split using spaces and similar chars
		$ns=str_replace(array(";",",","_",":","/",".","; ","-","(",")","'","\"","\\")," ",$ns);
		$ns=trim_spaces($ns);
		return trim_array(explode(" ",strtolower($ns)));
		}

	}

function resolve_keyword($keyword)
	{
	# Returns the keyword reference for $keyword, or false if no such keyword exists.
	return sql_value("select ref value from keyword where keyword='" . trim(escape_check($keyword)) . "'",false);
	}
	
function trim_spaces($text)
	{
	# replace multiple spaces with a single space
	while (strpos($text,"  ")!==false)
		{
		$text=str_replace("  "," ",$text);
		}
	return trim($text);
	}	
		

	
function update_resource_keyword_hitcount($resource,$search)
	{
	# For the specified $resource, increment the hitcount for each matching keyword in $search
	# This is done into a temporary column first (new_hit_count) so existing results are not affected.
	# copy_hitcount_to_live() is then executed at a set interval to make this data live.
	$keywords=split_keywords($search);
	$keys=array();
	for ($n=0;$n<count($keywords);$n++)
		{
		$keyword=$keywords[$n];
		if (strpos($keyword,":")!==false)
			{
			$k=explode(":",$keyword);
			$keyword=$k[1];
			}
		$found=resolve_keyword($keyword);
		if ($found!==false) {$keys[]=resolve_keyword($keyword);}
		}	
	if (count($keys)>0) {sql_query("update resource_keyword set new_hit_count=new_hit_count+1 where resource='$resource' and keyword in (" . join(",",$keys) . ")");}
	}
	
function copy_hitcount_to_live()
	{
	# Copy the temporary hit count used for relevance matching to the live column so it's activated (see comment for
	# update_resource_keyword_hitcount())
	sql_query("update resource_keyword set hit_count=new_hit_count");
	}
	
function get_image_sizes($ref,$internal=false,$extension="jpg",$onlyifexists=true)
	{
	# Returns a table of available image sizes for resource $ref.
	# The original image file assumes the name of the 'nearest size (up)' in the table

	# loop through all image sizes
	$sizes=sql_query("select * from preview_size order by width desc");
	$return=array();
	$outputfirst=false;$lastname="";$lastpreview=0;
	for ($n=0;$n<count($sizes);$n++)
		{
		$path=get_resource_path($ref,$sizes[$n]["id"],false,$extension);
		if (file_exists($path) || (!$onlyifexists))
			{
			if ($outputfirst==false)
				{
				# add the original image
				$path2=get_resource_path($ref,'',false,$extension);
				if (file_exists($path2))
					{
					$returnline=array();
					$returnline["name"]=$lastname;
					$returnline["allow_preview"]=$lastpreview;
					$returnline["allow_restricted"]=$lastrestricted;
					$returnline["path"]=$path2;
					$returnline["id"]="";
					if ((list($sw,$sh) = @getimagesize($path2))===false) {$sw=0;$sh=0;}
					if (($filesize=filesize($path2))===false) {$returnline["filesize"]="?";$returnline["filedown"]="?";}
					else {$returnline["filedown"]=ceil($filesize/50000) . " seconds @ broadband";$returnline["filesize"]=formatfilesize($filesize);}
					$returnline["width"]=$sw;			
					$returnline["height"]=$sh;
					$return[]=$returnline;
					$outputfirst=true;
					}
				}
			if (($sizes[$n]["internal"]==0) || ($internal))
				{
				$returnline=array();
				$returnline["name"]=$sizes[$n]["name"];
				$returnline["allow_preview"]=$sizes[$n]["allow_preview"];
				$returnline["allow_restricted"]=$sizes[$n]["allow_restricted"];
				$returnline["path"]=$path;
				$returnline["id"]=$sizes[$n]["id"];
				if ((list($sw,$sh) = @getimagesize($path))===false) {$sw=0;$sh=0;}
				if (($filesize=@filesize($path))===false) {$returnline["filesize"]="?";$returnline["filedown"]="?";}
				else {$returnline["filedown"]=ceil($filesize/50000) . " seconds @ broadband";$filesize=formatfilesize($filesize);}
				$returnline["filesize"]=$filesize;			
				$returnline["width"]=$sw;			
				$returnline["height"]=$sh;
				$return[]=$returnline;
				}
			}
		$lastname=$sizes[$n]["name"];
		$lastpreview=$sizes[$n]["allow_preview"];
		$lastrestricted=$sizes[$n]["allow_restricted"];
		}
	return $return;
	}

function trim_array($array)
	{
	# removes whitespace from the beginning/end of all elements in an array
	for ($n=0;$n<count($array);$n++)
		{
		$array[$n]=trim($array[$n]);
		}
	return $array;
	}

function tidylist($list)
	{
	# Takes a value as returned from a check-list field type and reformats to be more display-friendly.
	# Check-list fields have a leading comma.
	$list=trim($list);
	if (strpos($list,",")===false) {return $list;}
	$list=explode(",",$list);
	if (trim($list[0])=="") {array_shift($list);} # remove initial comma used to identify item is a list
	$op=join(", ",trim_array($list));
	#if (strpos($op,".")!==false) {$op=str_replace(", ","<br/>",$op);}
	return $op;
	}

function tidy_trim($text,$length)
	{
	# Trims $text to $length if necessary. Tries to trim at a space if possible. Adds three full stops
	# if trimmed...
	$text=trim($text);
	if (strlen($text)>$length)
		{
		$text=substr($text,0,$length-3);
		# Trim back to the last space
		$t=strrpos($text," ");
		$c=strrpos($text,",");
		if ($c!==false) {$t=$c;}
		if ($t>5) 
            {
            $text=substr($text,0,$t);
            }
		$text=$text . "...";
		}
	return $text;
	}

function get_related_resources($ref)
	{
	# Return an array of resource references that are related to resource $ref
	return sql_array("select related value from resource_related where resource='$ref' union select resource value from resource_related where related='$ref'");
	}
	
function average_length($array)
	{
	# Returns the average length of the strings in an array
	$total=0;
	for ($n=0;$n<count($array);$n++)
		{
		$total+=strlen(i18n_get_translated($array[$n]));
		}
	return ($total/count($array));
	}
	
function get_field_options($ref)
	{
	# For the field with reference $ref, return a sorted array of options.
	$options=sql_value("select options value from resource_type_field where ref='$ref'","");
	$options=trim_array(explode(",",$options));
	sort($options);
	return $options;
	}
	
function get_data_by_field($resource,$field)
	{
	# Return the resource data for field $field in resource $resource
	return sql_value("select value from resource_data where resource='$resource' and resource_type_field='$field'","");
	}
	
function get_users($group=0,$find="",$order_by="u.username",$usepermissions=false,$fetchrows=-1)
	{
	# Returns a user list. Group or search tearm is optional.
	$sql="";
	if ($group>0) {$sql="where usergroup='$group'";}
	if (strlen($find)>1) {$sql="where (username like '%$find%' or fullname like '%$find%' or email like '%$find%')";}
	if (strlen($find)==1) {$sql="where username like '$find%'";}
	if ($usepermissions && checkperm("U"))
		{
		# Only return users in children groups to the user's group
		global $usergroup;
		if ($sql=="") {$sql="where ";} else {$sql.=" and ";}
		$sql.="g.parent='" . $usergroup . "'";
		}
	return sql_query ("select u.*,g.name groupname,g.ref groupref,g.parent groupparent from user u left outer join usergroup g on u.usergroup=g.ref $sql order by $order_by",false,$fetchrows);
	}

function get_usergroups($usepermissions=false)
	{
	# Returns a list of user groups. Put anything starting with 'General Staff Users' at the top (e.g. General Staff)
	$sql="";
	if ($usepermissions && checkperm("U"))
		{
		# Only return users in children groups to the user's group
		global $usergroup;
		if ($sql=="") {$sql="where ";} else {$sql.=" and ";}
		$sql.="(ref='$usergroup' or parent='$usergroup')";
		}
	return sql_query("select * from usergroup $sql order by (name like 'General%') desc,name");
	}
	
function get_user($ref)
	{
	# Return a user's credentials.
	$return=sql_query("select * from user where ref='$ref'");
	if (count($return)>0) {return $return[0];} else {return false;}
	}
	
function save_user($ref)
	{
	# Save user details, data is taken from the submitted form.
	if (getval("deleteme","")!="")
		{
		sql_query("delete from user where ref='$ref'");
		}
	else
		{
		# Username or e-mail address already exists?
		$c=sql_value("select count(*) value from user where ref<>'$ref' and (username='" . getvalescaped("username","") . "' or email='" . getvalescaped("email","") . "')",0);
		if (($c>0) && (getvalescaped("email","")!="")) {return false;}
		
		$password=getvalescaped("password","");
		if (getval("suggest","")!="") {$password=make_password(8,5);}
		
		$expires="'" . getvalescaped("account_expires","") . "'";
		if ($expires=="''") {$expires="null";}
		
		$passsql="";
		global $lang;
		if ($password!=$lang["hidden"])	
			{
			# Save password.
			if (getval("suggest","")=="") {$password=md5("RS" . getvalescaped("username","") . $password);}
			$passsql=",password='" . $password . "'";
			}
		
		sql_query("update user set username='" . getvalescaped("username","") . "'" . $passsql . ",fullname='" . getvalescaped("fullname","") . "',email='" . getvalescaped("email","") . "',usergroup='" . getvalescaped("usergroup","") . "',account_expires=$expires,ip_restrict='" . getvalescaped("ip_restrict","") . "',comments='" . getvalescaped("comments","") . "' where ref='$ref'");
		}
	if (getval("emailme","")!="")
		{
		global $applicationname,$email_from,$baseurl;
		$message="Your login details for the $applicationname system are as follows:\n\nUsername: " . getval("username","") . "\nPassword: " . getval("password","") . "\n\nVisit the below URL to access ths system.\n$baseurl";
		send_mail(getval("email",""),$applicationname . ": Login Details",$message);
		}
	}

function email_reminder($email)
	{
	if ($email=="") {return false;}
	$details=sql_query("select username from user where email like '$email'");
	if (count($details)==0) {return false;}
	$details=$details[0];
	global $applicationname,$email_from,$baseurl;
	$password=make_password(8,5);
	$password_hash=md5("RS" . $details["username"] . $password);
	sql_query("update user set password='$password_hash' where username='" . escape_check($details["username"]) . "'");
	$message="Your login details for the $applicationname system are as follows:\n\nUsername: " . $details["username"] . "\nPassword: " . $password . "\n\nVisit the below URL to access ths system.\n$baseurl";
	send_mail($email,$applicationname . ": Password Reminder",$message);
	return true;
	}

function new_user($newuser)
	{
	# Username already exists?
	$c=sql_value("select count(*) value from user where username='$newuser'",0);
	if ($c>0) {return false;}
	
	# Create a new user with username $newuser. Returns the created user reference.
	sql_query("insert into user(username) values ('" . escape_check($newuser) . "')");
	
	$newref=sql_insert_id();
	
	# Create a collection for this user
	global $lang;
	$new=create_collection($newref,$lang["mycollection"],0,1);
	# set this to be the user's current collection
	sql_query("update user set current_collection='$new' where ref='$newref'");
	
	return $newref;
	}

function get_stats_activity_types()
	{
	# Returns a list of activity types for which we have stats data (Search, User Session etc.)
	return sql_array("select distinct activity_type value from daily_stat order by activity_type");
	}

function get_stats_years()
	{
	# Returns a list of years for which we have statistics.
	return sql_array("select distinct year value from daily_stat order by year");
	}

function email_resource_request($ref,$details)
	{
	# E-mails a resource request (posted) to the team
	global $applicationname,$email_from,$baseurl,$email_notify,$username,$useremail,$lang;
	$message=$lang["username"] . ": " . $username . "\n";
	
	reset ($_POST);
	foreach ($_POST as $key=>$value)
		{
		if (strpos($key,"_label")!==false)
			{
			# Add custom field
			$message.=$value . ": " . $_POST[str_replace("_label","",$key)] . "\n";
			}
		}
		
	if (trim($details)!="") {$message.=$lang["message"] . ":\n" . newlines($details) . "\n\n";}
	$message.=$lang["clicktoviewresource"] . "\n$baseurl/?r=$ref";
	send_mail($email_notify,$applicationname . ": " . $lang["researchrequest"] . " - $ref",$message,$useremail);
	}

function newlines($text)
	{
	# Replace escaped newlines with real newlines.
	$text=str_replace("\\n","\n",$text);
	$text=str_replace("\\r","\r",$text);
	return $text;
	}

function email_user_request()
	{
	# E-mails the submitted user request form to the team.
	global $applicationname,$email_from,$baseurl,$email_notify;
	$message="The User Login Request form has been completed with the following details:\n\nName: " . getval("name","") . "\nE-mail: " . getval("email","") . "\nComment: " . getval("userrequestcomment","") . "\n\nIf this is a valid request, please visit the system at the URL below and create an account for this user.\n$baseurl";
	send_mail($email_notify,$applicationname . ": Login Request - " . getval("name",""),$message);
	}

function get_active_users()
	{
	# Returns a list of active users, i.e. users still logged on with a last-active time within the last 2 hours.
	return sql_query("select username,round((unix_timestamp(now())-unix_timestamp(last_active))/60,0) t from user where logged_in=1 and unix_timestamp(now())-unix_timestamp(last_active)<(3600*2) order by t;");
	}

function get_all_site_text($find="")
	{
	# Returns a list of all available editable site text (content).
	# If $find is specified a search is performed across page, name and text fields.
	global $defaultlanguage;
	$sql="";
	if ($find!="") {$sql="where (page like '%$find%' or name like '%$find%' or text like '%$find%')";}
	return sql_query ("select distinct page,name,(select text from site_text where name=s.name and page=s.page order by (language='$defaultlanguage') desc limit 1) text from site_text s $sql order by (page='all') desc,page,name");
	}

function get_site_text($page,$name,$language,$group)
	{
	# Returns a specific site text entry.
	if ($group=="") {$g="null";$gc="is";} else {$g="'" . $group . "'";$gc="=";}
	
	$text=sql_query ("select * from site_text where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
	if (count($text)==0)
		{
		$existing=escape_check(sql_value("select text value from site_text where page='$page' and name='$name' limit 1",""));
		return $existing;
		}
	return $text[0]["text"];
	}

function save_site_text($page,$name,$language,$group)
	{
	# Saves the submitted site text changes to the database.

	if ($group=="") {$g="null";$gc="is";} else {$g="'" . $group . "'";$gc="=";}

	if (getval("deleteme","")!="")
		{
		sql_query("delete from site_text where page='$page' and name='$name' and specific_to_group $gc $g");
		}
	elseif (getval("copyme","")!="")
		{
		sql_query("insert into site_text(page,name,text,language,specific_to_group) values ('$page','$name','" . getvalescaped("text","") . "','$language',$g)");
		}
	else
		{
		$text=sql_query ("select * from site_text where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
		if (count($text)==0)
			{
			# Insert a new row for this language/group.
			sql_query("insert into site_text(page,name,language,specific_to_group,text) values ('$page','$name','$language',$g,'" . getvalescaped("text","") . "')");
			}
		else
			{
			# Update existing row
			sql_query("update site_text set text='" . getvalescaped("text","") . "' where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
			}
		}
	}
	
function string_similar($string1,$string2)
	{
	# Returns an integer score based on how similar the two strings are.
	# This was used when importing data for "fuzzy" keyword/option matching.
	$score=0;
	$string1=trim(strtolower($string1));$string2=trim(strtolower($string2));
	if ($string1==$string2) {return 9999;}
	if (substr($string1,0,1)==substr($string2,0,1)) {$score+=10;}
	for ($n=0;$n<strlen($string1)-1;$n++)
		{
		$pair=substr($string1,$n,2);
		for ($m=0;$m<strlen($string2)-1;$m++)
			{
			if ($pair==substr($string2,$m,2)) {$score++;}
			}
		}
	
	return $score;
	}

function formatfilesize($bytes)
	{
	# Return a human-readable string representing $bytes in either KB or MB.
	if ($bytes<pow(1024,2))
		{
		return number_format(floor($bytes/1024)) . "KB";
		}
	elseif ($bytes<pow(1024,3))
		{
		return number_format($bytes/pow(1024,2),1) . "MB";
		}
	elseif ($bytes<pow(1024,4))
		{
		return number_format($bytes/pow(1024,3),1) . "GB";
		}
	else
		{
		return number_format($bytes/pow(1024,4),1) . "TB";
		}
	}

function change_password($password)
	{
	# Sets a new password for the current user.
	global $userref,$username;
	if (strlen($password)<6) {return false;}
	$password_hash=md5("RS" . $username . $password);
	sql_query("update user set password='$password_hash' where ref='$userref' limit 1");
	return true;
	}
	
function make_password($length,$strength=0) {
    $vowels = 'aeiou';
    $consonants = 'bdghjlmnpqrstvwxz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEIOU";
    }
    if ($strength & 4) {
        $consonants .= '123456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%^';
    }
    $password = '';
    $alt = time() % 2;
    srand(time());
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}


function bulk_mail($userlist,$subject,$text)
	{
	global $email_from,$lang;
	
	# Attempt to resolve all users in the string $userlist to user references.
	if (trim($userlist)=="") {return ($lang["mustspecifyoneuser"]);}
	$ulist=trim_array(explode(",",$userlist));
	$urefs=sql_array("select ref value from user where username in ('" . join("','",$ulist) . "')");
	if (count($ulist)!=count($urefs)) {return($lang["couldnotmatchusers"]);}

	# Send an e-mail to each resolved user
	$emails=sql_array("select email value from user where ref in ('" . join("','",$urefs) . "')");
	for ($n=0;$n<count($emails);$n++)
		{
		//send_mail($emails[$n],$subject,str_replace("\\r\\n","\n",$text));
		
		//+ Camillo
		send_mail($emails[$n],$subject,stripslashes(str_replace("\\r\\n","\n",$text)));
		//- Camillo
		}
		
	# Return an empty string (all OK).
	return "";
	}

function i18n_get_translated($text)
	{
	# For field names / values using the i18n syntax, return the version in the current user's language
	# Format is ~en:Somename~es:Someothername
	$text=trim($text);
	
	# For multiple keywords, parse each keyword.
	if ((strpos($text,",")!==false) && (strpos($text,"~")!==false)) {$s=explode(",",$text);$out="";for ($n=0;$n<count($s);$n++) {if ($out!="") {$out.=",";}; $out.=i18n_get_translated(trim($s[$n]));};return $out;}
	
	global $language;
	
	# Split
	$s=explode("~",$text);

	# Not a translatable field?
	if (count($s)<2) {return $text;}

	# Find the current language and return it
	for ($n=1;$n<count($s);$n++)
		{
		if (substr($s[$n],2,1)!=":") {return $text;}
		if (substr($s[$n],0,2)==$language) {return substr($s[$n],3);}
		}	
	
	# No language match, return the first item
	return substr($s[0],3);
	}

function i18n_get_indexable($text)
	{
	# For field names / values using the i18n syntax, return all language versions, as necessary for indexing.
	$text=trim($text);
	
	# For multiple keywords, parse each keyword.
	if ((strpos($text,",")!==false) && (strpos($text,"~")!==false)) {$s=explode(",",$text);$out="";for ($n=0;$n<count($s);$n++) {if ($n>0) {$out.=",";}; $out.=i18n_get_indexable(trim($s[$n]));};return $out;}

	# Split
	$s=explode("~",$text);

	# Not a translatable field?
	if (count($s)<2) {return $text;}

	$out="";
	for ($n=1;$n<count($s);$n++)
		{
		if (substr($s[$n],2,1)!=":") {return $text;}
		if ($out!="") {$out.=",";}
		$out.=substr($s[$n],3);
		}	
	return $out;
	}

function send_mail($email,$subject,$message,$from="")
	{
	# Send a mail - but correctly encode the message/subject in quoted-printable UTF-8.
	
	# Include footer
	global $email_footer;
	global $disable_quoted_printable_enc;
	
	$message.="\r\n\r\n\r\n" . $email_footer;
	
	if ($disable_quoted_printable_enc==false){
	$message=quoted_printable_encode($message);
	$subject=quoted_printable_encode_subject($subject);
	}
	
	global $email_from;
	if ($from=="") {$from=$email_from;}
	mail ($email,$subject,$message,"From: " . $from . "\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=\"UTF-8\"\r\nContent-Transfer-Encoding: quoted-printable");
	}

function quoted_printable_encode($string, $linelen = 0, $linebreak="=\r\n", $breaklen = 0, $encodecrlf = false) {
        // Quoted printable encoding is rather simple.
        // Each character in the string $string should be encoded if:
        //  Character code is <0x20 (space)
        //  Character is = (as it has a special meaning: 0x3d)
        //  Character is over ASCII range (>=0x80)
        $len = strlen($string);
        $result = '';
        for($i=0;$i<$len;$i++) {
                if (($linelen >= 76) && (false)) { // break lines over 76 characters, and put special QP linebreak
                        $linelen = $breaklen;
                        $result.= $linebreak;
                }
                $c = ord($string[$i]);
                if (($c==0x3d) || ($c>=0x80) || ($c<0x20)) { // in this case, we encode...
                        if ((($c==0x0A) || ($c==0x0D)) && (!$encodecrlf)) { // but not for linebreaks
                                $result.=chr($c);
                                $linelen = 0;
                                continue;
                        }
                        $result.='='.str_pad(strtoupper(dechex($c)), 2, '0');
                        $linelen += 3;
                        continue;
                }
                $result.=chr($c); // normal characters aren't encoded
                $linelen++;
        }
        return $result;
}

function quoted_printable_encode_subject($string, $encoding='UTF-8') {
// use this function with headers, not with the email body as it misses word wrapping
       $len = strlen($string);
       $result = '';
       $enc = false;
       for($i=0;$i<$len;++$i) {
        $c = $string[$i];
        if (ctype_alpha($c))
            $result.=$c;
        else if ($c==' ') {
            $result.='_';
            $enc = true;
        } else {
            $result.=sprintf("=%02X", ord($c));
            $enc = true;
        }
       }
       //L: so spam agents won't mark your email with QP_EXCESS
       if (!$enc) return $string;
       return '=?'.$encoding.'?q?'.$result.'?=';
}

function highlightkeywords($text,$search)
	{
	# Highlight searched keywords in $text
	# Optional - depends on $highlightkeywords being set in config.php.
	global $highlightkeywords;
	# Situations where we do not need to do this.
	if (!isset($highlightkeywords) || ($highlightkeywords==false) || ($search=="") || ($text=="") || (substr($search,0,1)=="!")) {return $text;}
	
	global $hlkeycache;
	if (!isset($hlkeycache))
		{
		# Generate the cache of search keywords (this is a global variable, so the next time the function is called we don't need to regenerate the list
		$hlkeycache=array();
		$s=split_keywords($search);
		for ($n=0;$n<count($s);$n++)
			{
			if (strpos($s[$n],":")!==false) {$c=explode(":",$s[$n]);$s[$n]=$c[1];}
			$hlkeycache[]=$s[$n];
			}
		}

	# Parse and replace.
	return str_highlight ($text,$hlkeycache,STR_HIGHLIGHT_WHOLEWD);
	}


# These lines go with str_highlight (next).
define('STR_HIGHLIGHT_SIMPLE', 1);
define('STR_HIGHLIGHT_WHOLEWD', 2);
define('STR_HIGHLIGHT_CASESENS', 4);
define('STR_HIGHLIGHT_STRIPLINKS', 8);

function str_highlight($text, $needle, $options = null, $highlight = null)
	{
	# Thanks to Aidan Lister <aidan@php.net>
	# Sourced from http://aidanlister.com/repos/v/function.str_highlight.php on 2007-10-09
	# License on the website reads: "All code on this website resides in the Public Domain, you are free to use and modify it however you wish."
	# http://aidanlister.com/repos/license/
	
    // Default highlighting
    if ($highlight === null) {
        $highlight = '<span class="highlight">\1</span>';
    }
 
    // Select pattern to use
    if ($options & STR_HIGHLIGHT_SIMPLE) {
        $pattern = '#(%s)#';
        $sl_pattern = '#(%s)#';
    } else {
        $pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
        $sl_pattern = '#<a\s(?:.*?)>(%s)</a>#';
    }
 
    // Case sensitivity
    if (!($options & STR_HIGHLIGHT_CASESENS)) {
        $pattern .= 'i';
        $sl_pattern .= 'i';
    }
 
    $needle = (array) $needle;
    foreach ($needle as $needle_s) {
        $needle_s = preg_quote($needle_s);
 
        // Escape needle with optional whole word check
        if ($options & STR_HIGHLIGHT_WHOLEWD) {
            $needle_s = '\b' . $needle_s . '\b';
        }
 
        // Strip links
        if ($options & STR_HIGHLIGHT_STRIPLINKS) {
            $sl_regex = sprintf($sl_pattern, $needle_s);
            $text = preg_replace($sl_regex, '\1', $text);
        }
 
        $regex = sprintf($pattern, $needle_s);
        $text = preg_replace($regex, $highlight, $text);
    }
 
    return $text;
	}

function pager($break=true)
	{
	global $curpage,$url,$totalpages,$offset,$per_page,$lang,$jumpcount;
	$jumpcount++;
    ?>
	        <span class="HorizontalWhiteNav"><? if ($break) { ?>&nbsp;<br /><? } ?><? if ($curpage>1) { ?><a href="<?=$url?>&offset=<?=$offset-$per_page?>"><? } ?>&lt;&nbsp;<?=$lang["previous"]?><? if ($curpage>1) { ?></a><? } ?>&nbsp;|&nbsp;<a href="#" title="Jump to page" onClick="p=document.getElementById('jumppanel<?=$jumpcount?>');if (p.style.display!='block') {p.style.display='block';document.getElementById('jumpto<?=$jumpcount?>').focus();} else {p.style.display='none';}; return false;"><?=$lang["page"]?>&nbsp;<?=$curpage?>&nbsp;<?=$lang["of"]?>&nbsp;<?=$totalpages?></a>&nbsp;|&nbsp;<? if ($curpage<$totalpages) { ?><a href="<?=$url?>&offset=<?=$offset+$per_page?>"><? } ?><?=$lang["next"]?>&nbsp;&gt;<? if ($curpage<$totalpages) { ?></a><? } ?>
	   	   </span>
	   	   <div id="jumppanel<?=$jumpcount?>" style="display:none;margin-top:5px;"><?=$lang["jumptopage"]?>: <input type="text" size="3" id="jumpto<?=$jumpcount?>">&nbsp;<input type="submit" name="jump" value="<?=$lang["jump"]?>" onClick="var jumpto=document.getElementById('jumpto<?=$jumpcount?>').value;if ((jumpto>0) && (jumpto<=<?=$totalpages?>)) {document.location='<?=$url?>&offset=' + ((jumpto-1) * <?=$per_page?>);}"></div>
   	<?
	}
	
function get_all_image_sizes($internal=false)
	{
	# Returns all image sizes available.
	return sql_query("select * from preview_size " . (($internal)?"":"where internal!=1") . " order by width asc");
	}
	
function get_user_log($user)
	{
	return sql_query("select r.ref resourceid,r.title resourcetitle,l.date,l.type,f.title from resource_log l join resource r on l.resource=r.ref left outer join resource_type_field f on f.ref=l.resource_type_field where l.user='$user' order by l.date");
	}
	
function get_breadcrumbs()
	{
	# Returns a HTML breadcrumb trail for display at the top of the screen.

	# Fetch the variables we need to construct the trail.
	$search=getvalescaped("search","");
	$bc_from=getvalescaped("bc_from","");
	$search=getvalescaped("search","");
	global $pagename,$lang;
	$bc="";
	
	switch($pagename)
		{
		# ------- Themes page
		case "themes":
		$bc="<a href=\"themes.php\">" . $lang["themes"] . "</a>";
		break;
		
		# ------- Search results
		case "search":
		# From themes page?
		if ($bc_from=="themes")
			{$bc="<a href=\"themes.php\">" . $lang["themes"] . "</a>&nbsp;-&gt;&nbsp;";}

		$bc.="<a href=\"search.php?search=" . urlencode($search) . "&bc_from=themes\">" . $lang["searchresults"] . "</a>";
		break;
		}
		
	return "You are here: " . $bc;
	}
?>