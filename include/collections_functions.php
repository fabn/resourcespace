<?php
# Collections functions
# Functions to manipulate collections

if (!function_exists("get_user_collections")){
function get_user_collections($user,$find="",$order_by="name",$sort="ASC",$fetchrows=-1,$auto_create=true)
	{
	# Returns a list of user collections.
	$sql="";
	if (strlen($find)>1) {$sql="(name like '%$find%' or u.username like '%$find%' or c.ref like '$find')";}
	if (strlen($find)==1) {$sql="(name like '$find%' or c.ref like '$find')";}
   
    # Include themes in my collecions? 
    # Only filter out themes if $themes_in_my_collections is set to false in config.php
   	global $themes_in_my_collections;
   	if (!$themes_in_my_collections)
   		{
   		if ($sql!="") {$sql.=" and ";}
   		$sql.="(length(c.theme)=0 or c.theme is null) ";
   		}
	if ($sql!="") {$sql="where " . $sql;}
   
	$return=sql_query ("select * from (select c.*,u.username,count(r.resource) count from user u join collection c on u.ref=c.user and c.user='$user' left outer join collection_resource r on c.ref=r.collection $sql group by c.ref
	union
	select c.*,u.username,count(r.resource) count from user_collection uc join collection c on uc.collection=c.ref and uc.user='$user' and c.user<>'$user' left outer join collection_resource r on c.ref=r.collection left join user u on c.user=u.ref $sql group by c.ref) clist order by $order_by $sort");
	
	// To keep My Collection creation consistent: Check that user has at least one collection of his/her own  (not if collection result is empty, which may include shares), 
	$hasown=false;
	for ($n=0;$n<count($return);$n++){
		if ($return[$n]['user']==$user){
			$hasown=true;
		}
	}
	if ($find!=""){$hasown=true;} // if doing a search in collections, assume My Collection already exists (to avoid creating new collections due to an empty search result).
	
	if (!$hasown && $auto_create)
		{
		# No collections of one's own? The user must have at least one My Collection
		global $usercollection;
		$name=get_mycollection_name($user);
		$usercollection=create_collection ($user,$name,0,1); // make not deletable
		set_user_collection($user,$usercollection);
		
		# Recurse to send the updated collection list.
		return get_user_collections($user,$find,$order_by,$sort,$fetchrows,false);
		}
	
	return $return;
	}
}	

function get_collection($ref)
	{
	# Returns all data for collection $ref
	$return=sql_query("select *,theme2,theme3,keywords from collection where ref='$ref'");
	if (count($return)==0) {return false;} else 
		{
		$return=$return[0];
		$return["users"]="";
		if ($return["public"]==0)
			{
			# If private, also return a list of users with access to this collection
			$return["users"]=join(", ",sql_array("select u.username value from user u,user_collection c where u.ref=c.user and c.collection='$ref' order by u.username"));
			}
			
		global $userref,$k;
		$request_feedback=0;
		if ($return["user"]!=$userref)
			{
			# If this is not the user's own collection, fetch the user_collection row so that the 'request_feedback' property can be returned.
			$request_feedback=sql_value("select request_feedback value from user_collection where collection='$ref' and user='$userref'",0);
			}
		if ($k!="")
			{
			# If this is an external user (i.e. access key based) then fetch the 'request_feedback' value from the access keys table
			$request_feedback=sql_value("select request_feedback value from external_access_keys where access_key='$k' and request_feedback=1",0);
			}
		
		$return["request_feedback"]=$request_feedback;
		return $return;}
	}

function get_collection_resources($collection)
	{
	# Returns all resources in collection
	# For many cases (e.g. when displaying a collection for a user) a search is used instead so permissions etc. are honoured.
	return sql_array("select resource value from collection_resource where collection='$collection' order by date_added desc"); 
	}
	
function add_resource_to_collection($resource,$collection,$smartadd=false)
	{
	if (collection_writeable($collection)||$smartadd)
		{	
		sql_query("delete from collection_resource where resource='$resource' and collection='$collection'");
		sql_query("insert into collection_resource(resource,collection) values ('$resource','$collection')");
		
		#log this
		collection_log($collection,"a",$resource);
		
		# Check if this collection has already been shared externally. If it has, we must add a further entry
		# for this specific resource, and warn the user that this has happened.
		$keys=get_collection_external_access($collection);
		if (count($keys)>0)
			{
			# Set the flag so a warning appears.
			global $collection_share_warning;
			$collection_share_warning=true;
			
			for ($n=0;$n<count($keys);$n++)
				{
				# Insert a new access key entry for this resource/collection.
				global $userref;
				sql_query("insert into external_access_keys(resource,access_key,user,collection,date) values ('$resource','" . escape_check($keys[$n]["access_key"]) . "','$userref','$collection',now())");
				#log this
				collection_log($collection,"s",$resource, '#new_resource');
				}
			
			}

		return true;
		}
	else
		{
		return false;
		}
	}

function remove_resource_from_collection($resource,$collection,$smartadd=false)
	{
	if (collection_writeable($collection)||$smartadd)
		{	
		sql_query("delete from collection_resource where resource='$resource' and collection='$collection'");
		sql_query("delete from external_access_keys where resource='$resource' and collection='$collection'");
		
		#log this
		collection_log($collection,"r",$resource);
		return true;
		}
	else
		{
		return false;
		}
	}
	
function collection_writeable($collection)
	{
	# Returns true if the current user has write access to the given collection.
	$collectiondata=get_collection($collection);
	global $userref;
	global $allow_smart_collections;
	if ($allow_smart_collections){ 
		if (isset($collectiondata['savedsearch'])&&$collectiondata['savedsearch']!=null){
			return false; // so "you cannot modify this collection"
			}
	}
	return $userref==$collectiondata["user"] || $collectiondata["allow_changes"]==1 || checkperm("h");
	}
	
function set_user_collection($user,$collection)
	{
	global $usercollection;
	sql_query("update user set current_collection='$collection' where ref='$user'");
	$usercollection=$collection;
	}
	
if (!function_exists("create_collection")){	
function create_collection($userid,$name,$allowchanges=0,$cant_delete=0)
	{
	# Creates a new collection and returns the reference
	sql_query("insert into collection (name,user,created,allow_changes,cant_delete) values ('" . escape_check($name) . "','$userid',now(),'$allowchanges','$cant_delete')");
	return sql_insert_id();
	}	
}
	
function delete_collection($ref)
	{
	# Deletes the collection with reference $ref
	sql_query("delete from collection where ref='$ref'");
	sql_query("delete from collection_resource where collection='$ref'");
		#log this
	collection_log($ref,"X",0, "");
	}
	
function refresh_collection_frame($collection="")
	{
	# Refresh the collections frame
	# Only works when we are using a frameset.
	global $frameless_collections;
	if (!$frameless_collections)
		{
		global $headerinsert,$baseurl;
		$headerinsert.="<script  type=\"text/javascript\">
		parent.collections.location.href=\"" . $baseurl . "/pages/collections.php" . ((getval("k","")!="")?"?collection=" . getval("collection",$collection) . "&k=" . getval("k","") . "&":"?") . "nc=" . time() . "\";
		</script>";
		}
	}
	
function search_public_collections($search="", $order_by="name", $sort="ASC", $exclude_themes=true, $exclude_public=false, $include_resources=false, $override_group_restrict=false)
	{
	# Performs a search for themes / public collections.
	# Returns a comma separated list of resource refs in each collection, used for thumbnail previews.
	$sql="";
	# Keywords searching?
	if (strlen($search)==1)
		{
		# A-Z search
		$sql="and c.name like '$search%'";
		}
	elseif (strlen($search)>1)
		{
		$keywords=split_keywords($search,true);
		$keyrefs=array();
		for ($n=0;$n<count($keywords);$n++)
			{
			$keyref=resolve_keyword($keywords[$n],false);
			if ($keyref!==false) {$keyrefs[]=$keyref;}
			}
		if (count($keyrefs)>0)
			{
			$keysql="or keyword in (" . join (",",$keyrefs) . ")";
			}
		else
			{
			$keysql="";
			}
		$sql.="and (c.name rlike '$search' or u.username='$search' or c.ref='$search' $keysql)";
		}
	
	if ($exclude_themes) # Include only public collections.
		{
		$sql.=" and (length(c.theme)=0 or c.theme is null)";
		}
	
	if ($exclude_public) # Exclude public only collections (return only themes)
		{
		$sql.=" and length(c.theme)>0";
		}
		
	# Restrict to parent, child and sibling groups?
	global $public_collections_confine_group,$userref,$usergroup;
	if ($public_collections_confine_group && !$override_group_restrict)
		{
		# Form a list of all applicable groups
		$groups=array($usergroup); # Start with user's own group
		$groups=array_merge($groups,sql_array("select ref value from usergroup where parent='$usergroup'")); # Children
		$groups=array_merge($groups,sql_array("select parent value from usergroup where ref='$usergroup'")); # Parent
		$groups=array_merge($groups,sql_array("select ref value from usergroup where parent=(select parent from usergroup where ref='$usergroup')")); # Siblings (same parent)
		
		$sql.=" and u.usergroup in ('" . join ("','",$groups) . "')";
		}
	
	# Run the query
	if ($include_resources)
		{
            return sql_query("select c.*,u.username,group_concat(distinct cr.resource order by cr.rating desc,cr.date_added) resources from collection c left join collection_resource cr on c.ref=cr.collection left outer join user u on c.user=u.ref left outer join collection_keyword k on c.ref=k.collection where c.public=1 $sql group by c.ref order by $order_by $sort");
		}
	else
		{
		    return sql_query("select c.*,u.username from collection c left outer join user u on c.user=u.ref left outer join collection_keyword k on c.ref=k.collection where c.public=1 $sql group by c.ref order by $order_by $sort");
		}
	}

function add_collection($user,$collection)
	{
	# Add a collection to a user's 'My Collections'
	
	# Remove any existing collection first
	remove_collection($user,$collection);
	# Insert row
	sql_query("insert into user_collection(user,collection) values ('$user','$collection')");
			#log this
	collection_log($collection,"S",0, sql_value ("select username as value from user where ref = $user",""));

	}

function remove_collection($user,$collection)
	{
	# Remove someone else's collection from a user's My Collections
	sql_query("delete from user_collection where user='$user' and collection='$collection'");
			#log this
	collection_log($collection,"T",0, sql_value ("select username as value from user where ref = $user",""));
	}

function index_collection($ref,$index_string='')
	{
	# Update the keywords index for this collection
	sql_query("delete from collection_keyword where collection='$ref'"); # Remove existing keywords
	# Define an indexable string from the name, themes and keywords.

	global $index_collection_titles;

	if ($index_collection_titles)
		{
			$indexfields = 'name,keywords';
		} else {
			$indexfields = 'keywords';
		}

	// if an index string wasn't supplied, generate one
	if (!strlen($index_string) > 0){
		$indexarray = sql_query("select $indexfields from collection where ref = '$ref'");
		for ($i=0; $i<count($indexarray); $i++){
			$index_string = implode(' ',$indexarray[$i]);
		} 
	}

	$keywords=split_keywords($index_string,true);
	for ($n=0;$n<count($keywords);$n++)
		{
		$keyref=resolve_keyword($keywords[$n],true);
		sql_query("insert into collection_keyword values ('$ref','$keyref')");
		}
	// return the number of keywords indexed
	return $n;
	}


function save_collection($ref)
	{
	$theme=getvalescaped("theme","");
	if (getval("newtheme","")!="") {$theme=trim(getvalescaped("newtheme",""));}

	$theme2=getvalescaped("theme2","");
	if (getval("newtheme2","")!="") {$theme2=trim(getvalescaped("newtheme2",""));}

	$theme3=getvalescaped("theme3","");
	if (getval("newtheme3","")!="") {$theme3=trim(getvalescaped("newtheme3",""));}
	
	$allow_changes=(getval("allow_changes","")!=""?1:0);
	
	# Next line disabled as it seems incorrect to override the user's setting here. 20071217 DH.
	#if ($theme!="") {$allow_changes=0;} # lock allow changes to off if this is a theme
	
	# Update collection with submitted form data
	sql_query("update collection set
				name='" . getvalescaped("name","") . "',
				keywords='" . getvalescaped("keywords","") . "',
				public='" . getvalescaped("public","",true) . "',
				theme='" . $theme . "',
				theme2='" . $theme2 . "',
				theme3='" . $theme3 . "',
				allow_changes='" . $allow_changes . "'
	where ref='$ref'");
	
    	$index_string=getvalescaped("keywords","");
	
	global $index_collection_titles;
	if ($index_collection_titles){
		$index_string .= ' '.getvalescaped('name','');
	}

	index_collection($ref,$index_string);
	
	# Reset archive status if specified
	if (getval("archive","")!="")
		{
		sql_query("update resource set archive='" . getvalescaped("archive",0) . "' where ref in (select resource from collection_resource where collection='$ref')");
		}
		
	# If 'users' is specified (i.e. access is private) then rebuild users list
	$users=getvalescaped("users",false);
	if ($users!==false)
		{
		sql_query("delete from user_collection where collection='$ref'");
		#log this
		collection_log($ref,"T",0, '#all_users');

		if (($users)!="")
			{
			# Build a new list and insert
			$users=resolve_userlist_groups($users);
			$ulist=array_unique(trim_array(explode(",",$users)));
			$urefs=sql_array("select ref value from user where username in ('" . join("','",$ulist) . "')");
			if (count($urefs)>0)
				{
				sql_query("insert into user_collection(collection,user) values ($ref," . join("),(" . $ref . ",",$urefs) . ")");
				}
			#log this
			collection_log($ref,"S",0, join(", ",$ulist));
			}
		}
		
	# Relate all resources?
	if (getval("relateall","")!="")
		{
		$rlist=get_collection_resources($ref);
		for ($n=0;$n<count($rlist);$n++)
			{
			for ($m=0;$m<count($rlist);$m++)
				{
				if ($rlist[$n]!=$rlist[$m]) # Don't relate a resource to itself
					{
					sql_query("delete from resource_related where resource='" . $rlist[$n] . "' and related='" . $rlist[$m] . "'");
					sql_query("insert into resource_related (resource,related) values ('" . $rlist[$n] . "','" . $rlist[$m] . "')");
					}
				}
			}
		}
	
	
	# Remove all resources?
	if (getval("removeall","")!="")
		{
		sql_query("delete from collection_resource where collection='$ref'");
		collection_log($ref,"R",0);
		}
		
	# Delete all resources?
	if (getval("deleteall","")!="" && !checkperm("D"))
		{
		$resources=do_search("!collection" . $ref);
		for ($n=0;$n<count($resources);$n++)
			{
			if (checkperm("e" . $resources[$n]["archive"]))
				{
				delete_resource($resources[$n]["ref"]);	
				collection_log($ref,"D",$resources[$n]["ref"]);
				}
			}
		}
	}

function get_theme_headers($theme1="",$theme2="")
	{
	# Return a list of theme headers, i.e. theme categories
	#return sql_array("select theme value,count(*) c from collection where public=1 and length(theme)>0 group by theme order by theme");
		
	# Work out which theme category level we are selecting based on the higher selected levels provided.
	$selecting="theme";
	if ($theme1=="" && $theme2=="") {$selecting="theme";}
	if ($theme1!="" && $theme2=="") {$selecting="theme2";}
	if ($theme1!="" && $theme2!="") {$selecting="theme3";}
	
	$sql="";
	if ($theme1!="") {$sql.=" and theme='" . escape_check($theme1) . "'";}
	if ($theme2!="") {$sql.=" and theme2='" . escape_check($theme2) . "'";}
	
	$return=array();
	$themes=sql_query("select * from collection where public=1 and $selecting is not null and length($selecting)>0 $sql order by $selecting");
	for ($n=0;$n<count($themes);$n++)
		{
		if ((!in_array($themes[$n][$selecting],$return)) && (checkperm("j*") || checkperm("j" . $themes[$n]["theme"]))) {$return[]=$themes[$n][$selecting];}
		}
	return $return;
	}

function get_themes($theme,$theme2="",$theme3="")
	{
	# Return a list of themes under a given header (theme category).
	return sql_query("select *,(select count(*) from collection_resource cr where cr.collection=c.ref) c from collection c  where c.theme='" . escape_check($theme) . "' " . 
	(($theme2!="")?" and theme2='" . escape_check($theme2) . "' ":" and (theme2='' or theme2 is null) ") . 
	(($theme3!="")?" and theme3='" . escape_check($theme3) . "' ":" and (theme3='' or theme3 is null) ")
	. " and c.public=1 order by c.name;");
	}

function get_smart_theme_headers()
	{
	# Returns a list of smart theme headers, which are basically fields with a 'smart theme name' set.
	return sql_query("select ref,name,smart_theme_name from resource_type_field where length(smart_theme_name)>0 order by smart_theme_name");
	}

if (!function_exists("get_smart_themes")){	
function get_smart_themes($field)
	{
	# Returns a list of smart themes (which are really field options).
	# The results are filtered so that only field options that are in use are returned.
	
	# Fetch field info
	$fielddata=sql_query("select * from resource_type_field where ref='$field'");
	if (count($fielddata)>0) {$fielddata=$fielddata[0];} else {return false;}
	
	# Return a list of keywords that are in use for this field
	$inuse=sql_array("select distinct k.keyword value from keyword k join resource_keyword rk on k.ref=rk.keyword where 
		resource_type_field='$field' and resource>0");

	if ($fielddata["type"]==7)
		{
		# Category tree style view
		$tree=explode("\n",$fielddata["options"]);

		$return=array();	
		$return=populate_smart_theme_tree_node($tree,0,$return,0);
		
		# For each option, if it is in use, add it to the return list.
		$out=array();
		for ($n=0;$n<count($return);$n++)
			{
			# Prepare a 'tidied' local language version of the name to use for the comparison
			# Only return items that are in use.
			$tidy=escape_check(trim(strtolower(str_replace("-"," ",i18n_get_translated($return[$n]["name"])))));
			
			if (in_array($tidy,$inuse))
				{
				$c=count($out);
				$out[$c]["indent"]=$return[$n]["indent"];
				$out[$c]["name"]=trim(i18n_get_translated($return[$n]["name"]));
				}
			}
		return $out;
		}
	else
		{
		# Standard checkbox list or drop-down box
		
		# Fetch raw options list
		$options=explode(",",$fielddata["options"]);
		
		# Tidy list so it matches the storage format used for keywords.
		# The translated version is fetched as each option will be indexed in the local language version of each option.
		$options_base=array();
		for ($n=0;$n<count($options);$n++) {$options_base[$n]=escape_check(trim(strtolower(i18n_get_translated($options[$n]))));}
		
		# For each option, if it is in use, add it to the return list.
		$return=array();
		for ($n=0;$n<count($options);$n++)
			{
			#echo "<li>Looking for " . $options_base[$n] . " in " . join (",",$inuse);
			if (in_array(str_replace("-"," ",$options_base[$n]),$inuse)) 		
				{
				$c=count($return);
				$return[$c]["name"]=trim(i18n_get_translated($options[$n]));
				$return[$c]["indent"]=0;
				}
			}
		return $return;
		}
	}
}

function populate_smart_theme_tree_node($tree,$node,$return,$indent)
	{
	# When displaying category trees as smart themes, this function is used to recursively
	#�parse each node adding items sequentially with an appropriate indent level.
	for ($n=0;$n<count($tree);$n++)
		{
		$s=explode(",",$tree[$n]);
		if ($s[1]==$node)
			{
			# Add this node
			$c=count($return);
			$return[$c]["indent"]=$indent;
			$return[$c]["name"]=$s[2];
			$return=populate_smart_theme_tree_node($tree,$n+1,$return,$indent+1);
			}
		}
	return $return;
	}

if (!function_exists("email_collection")){
function email_collection($colrefs,$collectionname,$fromusername,$userlist,$message,$feedback,$access=-1,$expires="",$useremail="",$from_name="",$cc="")
	{
	# Attempt to resolve all users in the string $userlist to user references.
	# Add $collection to these user's 'My Collections' page
	# Send them an e-mail linking to this collection
	#  handle multiple collections (comma seperated list)
	global $baseurl,$email_from,$applicationname,$lang,$userref, $email_multi_collections ;
	
	if ($useremail==""){$useremail=$email_from;}
	
	if (trim($userlist)=="") {return ($lang["mustspecifyoneusername"]);}
	$userlist=resolve_userlist_groups($userlist);
	$ulist=trim_array(explode(",",$userlist));
	$emails=array();
	$key_required=array();
	if ($feedback) {$feedback=1;} else {$feedback=0;}
	$reflist=trim_array(explode(",",$colrefs));
	
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

	# Add the collection(s) to the user's My Collections page
	$urefs=sql_array("select ref value from user where username in ('" . join("','",$ulist) . "')");
	if (count($urefs)>0)
		{
		#�Delete any existing collection entries
		sql_query("delete from user_collection where collection in ('" .join("','", $reflist) . "') and user in ('" . join("','",$urefs) . "')");
		
		# Insert new user_collection row(s)
		#loop through the collections
			for ($nx1=0;$nx1<count($reflist);$nx1++)
			{	#loop through the users
				for ($nx2=0;$nx2<count($urefs);$nx2++)
				{
		sql_query("insert into user_collection(collection,user,request_feedback) values ($reflist[$nx1], $urefs[$nx2], $feedback )");
					#log this
		collection_log($reflist[$nx1],"S",0, sql_value ("select username as value from user where ref = $urefs[$nx2]",""));

				}
			}
		}
	
	# Send an e-mail to each resolved user
	
	# htmlbreak is for composing list
	$htmlbreak="";
	global $use_phpmailer;
	if ($use_phpmailer){$htmlbreak="<br><br>";} 
	
	if ($fromusername==""){$fromusername=$applicationname;} // fromusername is used for describing the sender's name inside the email
	if ($from_name==""){$from_name=$applicationname;} // from_name is for the email headers, and needs to match the email address (app name or user name)
	
	$templatevars['message']=str_replace(array("\\n","\\r","\\"),array("\n","\r",""),$message);	
	$templatevars['fromusername']=$fromusername;
	$templatevars['from_name']=$from_name;
	
	if(count($reflist)>1){$subject=$applicationname.": ".$lang['mycollections'];}
	else { $subject=$applicationname.": ".$collectionname;}
	
	if ($fromusername==""){$fromusername=$applicationname;}
		
	##  loop through recipients
	for ($nx1=0;$nx1<count($emails);$nx1++)
		{
		## loop through collections
		$list="";
		for ($nx2=0;$nx2<count($reflist);$nx2++)
			{
			$url="";
			$key="";
			# Do we need to add an external access key for this user (e-mail specified rather than username)?
			if ($key_required[$nx1])
				{
				$k=generate_collection_access_key($reflist[$nx2],$feedback,$emails[$nx1],$access,$expires);
				$key="&k=". $k;
				}
			$url=$baseurl . 	"/?c=" . $reflist[$nx2] . $key;
			
			if ($use_phpmailer){
			$collection_name="";
			$collection_name=sql_value("select name value from collection where ref='$reflist[$nx2]'","$reflist[$nx2]");
			$url="<a href=\"$url\">$collection_name</a>";}	
			
			$list .= $htmlbreak.$url."\n\n";
					#log this
			collection_log($reflist[$nx2],"E",0, $emails[$nx1]);
			}
		$list.=$htmlbreak;	
		$templatevars['list']=$list;
		$templatevars['from_name']=$from_name;
		
		$body=$templatevars['fromusername']." " . ((count($reflist)>1)?$lang["emailcollectionmessageexternal"]:$lang["emailcollectionmessage"]) . "\n\n" . $lang["message"] . ": " .$templatevars['message']."\n\n" . $lang["clicklinkviewcollection"] ."\n\n".$templatevars['list'];
		send_mail($emails[$nx1],$subject,$body,$fromusername,$useremail,"emailcollection",$templatevars,$from_name,$cc);
		}
		
	# Return an empty string (all OK).
	return "";
	}
}	

function generate_collection_access_key($collection,$feedback=0,$email="",$access=-1,$expires="")
	{
	# For each resource in the collection, create an access key so an external user can access each resource.
	global $userref;
	$k=substr(md5($collection . "," . time()),0,10);
	$r=get_collection_resources($collection);
	for ($m=0;$m<count($r);$m++)
		{
		# Add the key to each resource in the collection
		sql_query("insert into external_access_keys(resource,access_key,collection,user,request_feedback,email,date,access,expires) values ('" . $r[$m] . "','$k','$collection','$userref','$feedback','" . escape_check($email) . "',now(),$access," . (($expires=="")?"null":"'" . $expires . "'"). ");");
		}
	return $k;
	}
	
function get_saved_searches($collection)
	{
	return sql_query("select * from collection_savedsearch where collection='$collection' order by created");
	}

function add_saved_search($collection)
	{
	sql_query("insert into collection_savedsearch(collection,search,restypes,archive) values ('$collection','" . getvalescaped("addsearch","") . "','" . getvalescaped("restypes","") . "','" . getvalescaped("archive","",true) . "')");
	}

function remove_saved_search($collection,$search)
	{
	sql_query("delete from collection_savedsearch where collection='$collection' and ref='$search'");
	}

function add_smart_collection($collection)
	{
	global $userref;
	$newcollection=create_collection($userref,getvalescaped("addsmartcollection",""),1);	
	sql_query("insert into collection_savedsearch(collection,search,restypes,archive) values ('$newcollection','" . getvalescaped("addsmartcollection","") . "','" . getvalescaped("restypes","") . "','" . getvalescaped("archive","",true) . "')");
	$savedsearch=mysql_insert_id();
	sql_query("update collection set savedsearch=$savedsearch where ref=$newcollection"); 
	set_user_collection($userref,$newcollection);
	}

function add_saved_search_items($collection)
	{
	# Adds resources from a search to the collection.
	$results=do_search(getvalescaped("addsearch",""), getvalescaped("restypes",""), "relevance", getvalescaped("archive","",true));
	if (is_array($results))
		{
		for ($n=0;$n<count($results);$n++)
			{
			$resource=$results[$n]["ref"];
			sql_query("delete from collection_resource where resource='$resource' and collection='$collection'");
			sql_query("insert into collection_resource(resource,collection) values ('$resource','$collection')");
			}
		}

	# Check if this collection has already been shared externally. If it has, we must add a further entry
	# for this specific resource, and warn the user that this has happened.
	$keys=get_collection_external_access($collection);
	if (count($keys)>0)
		{
		# Set the flag so a warning appears.
		global $collection_share_warning;
		$collection_share_warning=true;
		
		for ($n=0;$n<count($keys);$n++)
			{
			# Insert a new access key entry for this resource/collection.
			global $userref;
			
			for ($r=0;$r<count($results);$r++)
				{
				$resource=$results[$r]["ref"];
				sql_query("insert into external_access_keys(resource,access_key,user,collection,date) values ('$resource','" . escape_check($keys[$n]["access_key"]) . "','$userref','$collection',now())");
				#log this
				collection_log($collection,"s",$resource, '#new_resource');
				}
			}
		}

	}

if (!function_exists("allow_multi_edit")){
function allow_multi_edit($collection)
	{
	# Returns true or false, can this collection be edited as a multi-edit?
	# All the resources must be of the same type and status for this to work.
	
	# Updated: 2008-01-21: Edit all now supports multiple types, so always return true.
	return (true);
	
	/*
	$types=sql_query("select distinct r.resource_type from collection_resource c left join resource r on c.resource=r.ref where c.collection='$collection'");
	if (count($types)!=1) {return false;}
	
	$status=sql_query("select distinct r.archive from collection_resource c left join resource r on c.resource=r.ref where c.collection='$collection'");
	if (count($status)!=1) {return false;}	
	
	return true;
	*/
	}
}	

function get_theme_image($theme,$theme2="",$theme3="")
	{
	# Returns an array of resource references that can be used as theme category images.
	global $theme_images_number;
	
	# First try to find resources that have been specifically chosen using the option on the collection comments page.
	$chosen=sql_array("select r.ref value from collection c join collection_resource cr on c.ref=cr.collection join resource r on cr.resource=r.ref where c.theme='" . escape_check($theme) . "' " .
	(($theme2!="")?" and theme2='" . escape_check($theme2) . "' ":" and (theme2='' or theme2 is null) ") . 
	(($theme3!="")?" and theme3='" . escape_check($theme3) . "' ":" and (theme3='' or theme3 is null) ")
	. " and r.has_image=1 and cr.use_as_theme_thumbnail=1 order by r.ref desc",0);
	if (count($chosen)>0) {return $chosen;}
	
	# No chosen images? Manually choose a single image based on hit counts.
	$images=sql_array("select r.ref value from collection c join collection_resource cr on c.ref=cr.collection join resource r on cr.resource=r.ref where c.theme='" . escape_check($theme) . "' " .
	(($theme2!="")?" and theme2='" . escape_check($theme2) . "' ":" and (theme2='' or theme2 is null) ") . 
	(($theme3!="")?" and theme3='" . escape_check($theme3) . "' ":" and (theme3='' or theme3 is null) ")
	. " and r.has_image=1 order by r.hit_count desc limit " . $theme_images_number,0);
	if (count($images)>0) {return $images;}
	return false;
	}

function swap_collection_order($resource1,$resource2,$collection)
	{
	# Inserts $resource1 into the position currently occupied by $resource2 

	$existingorder=sql_query("select resource,date_added  from collection_resource where collection='$collection' order by date_added desc");
	#find dates for 1 and 2
	for ($n=0;$n<count($existingorder);$n++){
		if ($existingorder[$n]['resource']==$resource1){
			$firstdate=strtotime($existingorder[$n]['date_added']);
			}
		if ($existingorder[$n]['resource']==$resource2){
			$seconddate=strtotime($existingorder[$n]['date_added']);
			}
	}
	if ($firstdate>$seconddate){$reverse=1;}else{$reverse=0;}		
	
	$neworder=array();
	if ($reverse){
		for ($n=0;$n<count($existingorder);$n++)
			{
			if ($existingorder[$n]['resource']==$resource1){}
				if ($existingorder[$n]['resource']==$resource2)
				{
				$neworder[]=$resource2;	
				$neworder[]=$resource1;
				}	
				if ($existingorder[$n]['resource']!=$resource1&&$existingorder[$n]['resource']!=$resource2)
				{
				$neworder[]=$existingorder[$n]['resource'];
				}	
			}
		}
	else{
	
		for ($n=0;$n<count($existingorder);$n++)
			{
			if ($existingorder[$n]['resource']==$resource2)
				{
				$neworder[]=$resource1;
				}
			if ($existingorder[$n]['resource']!=$resource1)
				{
				$neworder[]=$existingorder[$n]['resource'];
				}
			}
		}
	#echo " to " . join(",",$neworder);
	for ($n=0;$n<count($neworder);$n++)
		{
		$newdate=date("Y-m-d H:i:s",(time()-$n));
		#echo "<br>" . $newdate;
		sql_query("update collection_resource set date_added='$newdate' where collection='$collection' and resource='" . $neworder[$n] . "'");
		}	
	}

function get_collection_resource_comment($resource,$collection)
	{
	$data=sql_query("select *,use_as_theme_thumbnail from collection_resource where collection='$collection' and resource='$resource'","");
	return $data[0];
	}
	
function save_collection_resource_comment($resource,$collection,$comment,$rating)
	{
	# get data before update so that changes can be logged.	
	$data=sql_query("select comment,rating from collection_resource where resource='$resource' and collection='$collection'");
	sql_query("update collection_resource set comment='" . escape_check($comment) . "',rating=" . (($rating!="")?"'$rating'":"null") . ",use_as_theme_thumbnail='" . (getval("use_as_theme_thumbnail","")==""?0:1) . "' where resource='$resource' and collection='$collection'");
	
	# log changes
	if ($comment!=$data[0]['comment']){collection_log($collection,"m",$resource);}
	if ($rating!=$data[0]['rating']){collection_log($collection,"*",$resource);}
	return true;
	}

function relate_to_collection($ref,$collection)	
	{
	# Relates every resource in $collection to $ref
		$colresources = get_collection_resources($collection);
		sql_query("delete from resource_related where resource='$ref' and related in ('" . join("','",$colresources) . "')");  
		sql_query("insert into resource_related(resource,related) values ($ref," . join("),(" . $ref . ",",$colresources) . ")");
	}	
	
function get_mycollection_name($userref)
	{
	# Fetches the next name for a new My Collection for the given user (My Collection 1, 2 etc.)
	global $lang;
	for ($n=1;$n<500;$n++)
		{
		# Construct a name for this My Collection.
		if ($n==1)
			{
			$name=$lang["mycollection"];
			}
		else
			{
			$name=$lang["mycollection"] . " " . $n;
			}
		$ref=sql_value("select ref value from collection where user='$userref' and name='$name'",0);
		if ($ref==0)
			{
			# No match!
			return $name;
			}
		}
	# Tried nearly 500 names(!) so just return a standard name 
	return $lang["mycollection"];
	}
	
function get_collection_comments($collection)
	{
	return sql_query("select * from collection_resource where collection='$collection' and length(comment)>0 order by date_added");
	}

function send_collection_feedback($collection,$comment)
	{
	# Sends the feedback to the owner of the collection.
	global $applicationname,$lang,$userfullname,$userref,$k,$feedback_resource_select;
	
	$cinfo=get_collection($collection);if ($cinfo===false) {exit("Collection not found");}
	$user=get_user($cinfo["user"]);
	$body=$lang["collectionfeedbackemail"] . "\n\n";
	
	if (isset($userfullname))
		{
		$body.=$lang["user"] . ": " . $userfullname . "\n";
		}
	else
		{
		# External user.
		$body.=$lang["fullname"] . ": " . getval("name","") . "\n";
		$body.=$lang["email"] . ": " . getval("email","") . "\n";
		}
	$body.=$lang["message"] . ": " . stripslashes(str_replace("\\r\\n","\n",trim($comment)));

	$f=get_collection_comments($collection);
	for ($n=0;$n<count($f);$n++)
		{
		$body.="\n\n" . $lang["resourceid"] . ": " . $f[$n]["resource"];
		$body.="\n" . $lang["comment"] . ": " . trim($f[$n]["comment"]);
		$body.="\n" . $lang["rating"] . ": " . substr("**********",0,$f[$n]["rating"]);
		}
	
	if ($feedback_resource_select)
		{
		$body.="\n\n" . $lang["selectedresources"] . ": ";
		$file_list="";
		$result=do_search("!collection" . $collection);
		for ($n=0;$n<count($result);$n++)
			{
			$ref=$result[$n]["ref"];
			if (getval("select_" . $ref,"")!="")
				{
				global $filename_field;
				$filename=get_data_by_field($ref,$filename_field);
				$body.="\n" . $ref . " : " . $filename;

				# Append to a file list that is compatible with Adobe Lightroom
				if ($file_list!="") {$file_list.=", ";}
				$s=explode(".",$filename);
				$file_list.=$s[0];
				}
			}
		# Append Lightroom compatible summary.
		$body.="\n\n" . $lang["selectedresourceslightroom"] . "\n" . $file_list;
		}	
	
	
	send_mail($user["email"],$applicationname . ": " . $lang["collectionfeedback"] . " - " . $cinfo["name"],$body);
	
	# Cancel the feedback request for this resource.
	/* - Commented out - as it may be useful to leave the feedback request in case the user wishes to leave
	     additional feedback or make changes.
	     
	if (isset($userref))
		{
		sql_query("update user_collection set request_feedback=0 where collection='$collection' and user='$userref'");
		}
	else
		{
		sql_query("update external_access_keys set request_feedback=0 where access_key='$k'");
		}
	*/
	}

function copy_collection($copied,$current,$remove_existing=false)
	{	
	# Get all data from the collection to copy.
	$copied_collection=sql_query("select * from collection_resource where collection='$copied'","");
	
	if ($remove_existing)
		{
		#delete all existing data in the current collection
		sql_query("delete from collection_resource where collection='$current'");
		collection_log($current,"R",0);
		}
	
	#put all the copied collection records in
	foreach($copied_collection as $col_resource)
		{
		# Use correct function so external sharing is honoured.
		add_resource_to_collection($col_resource['resource'],$current,true);
		}
	}

if (!function_exists("collection_is_research_request")){
function collection_is_research_request($collection)
	{
	# Returns true if a collection is a research request
	return (sql_value("select count(*) value from research_request where collection='$collection'",0)>0);
	}
}	

if (!function_exists("add_to_collection_link")){
function add_to_collection_link($resource,$search="",$extracode="")
	{
	# Generates a HTML link for adding a resource to a collection
	global $frameless_collections,$lang;
	if ($frameless_collections)
		{
		return "<a href=\"#\" onClick=\"AddResourceToCollection('" . $resource . "');" . $extracode . "return false;\">";
		}
	else
		{
		return "<a href=\"collections.php?add=" . $resource . "&nc=" . time() . "&search=" . urlencode($search) . "\" target=\"collections\" onClick=\"" . $extracode . "\" title=\"" . $lang["addtocurrentcollection"] . "\">";
		}
	}
}	
	
function remove_from_collection_link($resource,$search="")
	{
	# Generates a HTML link for removing a resource to a collection
	global $frameless_collections,$lang,$pagename;
	if ($frameless_collections)
		{
		return "<a href=\"#\" onClick=\"RemoveResourceFromCollection('" . $resource . "','" . $pagename . "');return false;\">";
		}
	else
		{
		return "<a href=\"collections.php?remove=" . $resource . "&nc=" . time() . "&search=" . urlencode($search) . "\" target=\"collections\" title=\"" . $lang["removefromcurrentcollection"] . "\">";
		}
	}
	
function change_collection_link($collection)
	{
	# Generates a HTML link for adding a changing the current collection
	global $frameless_collections,$lang;
	if ($frameless_collections)
		{
		return "<a href=\"#\" onClick=\"ChangeCollection('" . $collection . "');return false;\">";
		}
	else
		{
		return "<a href=\"collections.php?collection=" . $collection. "\" target=\"collections\">";
		}
	}
	
function get_collection_external_access($collection)
	{
	# Return all external access given to a collection.
	# Users, emails and dates could be multiple for a given access key, an in this case they are returned comma-separated.
	return sql_query("select access_key,group_concat(DISTINCT user ORDER BY user SEPARATOR ', ') users,group_concat(DISTINCT email ORDER BY email SEPARATOR ', ') emails,max(date) maxdate,max(lastused) lastused,access,expires from external_access_keys where collection='$collection' group by access_key order by date");
	}
	
function delete_collection_access_key($collection,$access_key)
	{
	# Get details for log
	$users = sql_value("select group_concat(DISTINCT email ORDER BY email SEPARATOR ', ') value from external_access_keys where collection='$collection' and access_key = '$access_key' group by access_key ", "");
	# Deletes the given access key.
	sql_query("delete from external_access_keys where access_key='$access_key' and collection='$collection'");
	# log changes
	collection_log($collection,"t","",$users);

	}
	
function collection_log($collection,$type,$resource,$notes = "")
	{
	global $userref;
	sql_query("insert into collection_log(date,user,collection,type,resource, notes) values (now()," . (($userref!="")?"'$userref'":"null") . ",'$collection','$type','$resource', '$notes')");
	}
/*  Log entry types  
$lang["collectionlog-r"]="Removed resource";
$lang["collectionlog-R"]="Removed all resources";
$lang["collectionlog-D"]="Deleted all resources";
$lang["collectionlog-d"]="Deleted resource"; // this shows external deletion of any resources related to the collection.
$lang["collectionlog-a"]="Added resource";
$lang["collectionlog-c"]="Added resource (copied)";
$lang["collectionlog-m"]="Added resource comment";
$lang["collectionlog-*"]="Added resource rating";
$lang["collectionlog-S"]="Shared collection with "; //  + notes field
$lang["collectionlog-E"]="E-mailed collection to ";//  + notes field
$lang["collectionlog-s"]="Shared Resource with ";//  + notes field
$lang["collectionlog-T"]="Stopped sharing collection with ";//  + notes field
$lang["collectionlog-t"]="Stopped access to resource by ";//  + notes field
$lang["collectionlog-X"]="Collection deleted";
*/
function get_collection_log($collection)
	{
	global $view_title_field;	
	return sql_query("select c.date,u.username,u.fullname,c.type,r.field".$view_title_field." title,c.resource, c.notes from collection_log c left outer join user u on u.ref=c.user left outer join resource r on r.ref=c.resource where collection='$collection' order by c.date");
	}
	
function get_collection_videocount($ref)
	{
	global $videotypes;
    #figure out how many videos are in a collection. if more than one, can make a playlist
	$resources = do_search("!collection" . $ref);
	$videocount=0;
	foreach ($resources as $resource){if (in_array($resource['resource_type'],$videotypes)){$videocount++;}}
	return $videocount;
	}
	
function collection_max_access($collection)	
	{
	# Returns the maximum access (the most permissive) that the current user has to the resources in $collection.
	$maxaccess=2;
	$result=do_search("!collection" . $collection);
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=get_resource_access($result[$n]);
		if ($access<$maxaccess) {$maxaccess=$access;}
		}
	return $maxaccess;
	}

function collection_min_access($collection)	
	{
	# Returns the minimum access (the least permissive) that the current user has to the resources in $collection.
	$minaccess=0;
	$result=do_search("!collection" . $collection);
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=get_resource_access($result[$n]);
		if ($access>$minaccess) {$minaccess=$access;}
		}
	return $minaccess;
	}

	
?>
