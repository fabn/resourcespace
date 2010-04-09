<?php
# Search functions
# Functions to perform searches (read only)
#  - For resource indexing / keyword creation, see resource_functions.php

if (!function_exists("do_search")) {
function do_search($search,$restypes="",$order_by="relevance",$archive=0,$fetchrows=-1,$sort="desc",$access_override=false)
	{
	# Takes a search string $search, as provided by the user, and returns a results set
	# of matching resources.
	# If there are no matches, instead returns an array of suggested searches.
	# $restypes is optionally used to specify which resource types to search.
	# $access_override is used by smart collections, so that all all applicable resources can be judged regardless of the final access-based results
	
	# resolve $order_by to something meaningful in sql
	$orig_order=$order_by;
	global $date_field;
	$order=array("relevance"=>"score $sort, user_rating $sort, hit_count $sort, field$date_field $sort,r.ref $sort","popularity"=>"user_rating $sort,hit_count $sort,field$date_field $sort,r.ref $sort","rating"=>"r.rating $sort, user_rating $sort, score $sort,r.ref $sort","date"=>"field$date_field $sort,r.ref $sort","colour"=>"has_image $sort,image_blue $sort,image_green $sort,image_red $sort,field$date_field $sort,r.ref $sort","country"=>"country $sort,r.ref $sort","title"=>"title $sort,r.ref $sort","file_path"=>"file_path $sort,r.ref $sort","resourceid"=>"r.ref $sort","resourcetype"=>"resource_type $sort,r.ref $sort","titleandcountry"=>"title $sort,country $sort","random"=>"RAND()");
	if (!in_array($order_by,$order)&&(substr($order_by,0,5)=="field")){
		$order[$order_by]="$order_by $sort";
	}
		
	$modified_order_array=(hook("modifyorderarray"));
	if ($modified_order_array){$order=$modified_order_array;}
	
	$order_by=$order[$order_by];
	$keywords=split_keywords($search);
	$search=trim($search); # remove any trailing or leading spaces
	
	# -- Build up filter SQL that will be used for all queries

	$sql_filter="";
	# append resource type filtering
	if ($restypes!="")
		{
		if ($sql_filter!="") {$sql_filter.=" and ";}
		$sql_filter.="resource_type in ($restypes)";
		}
	
	# append resource type restrictions based on 'T' permission	
	# look for all 'T' permissions and append to the SQL filter.
	global $userpermissions;
	$rtfilter=array();
	for ($n=0;$n<count($userpermissions);$n++)
		{
		if (substr($userpermissions[$n],0,1)=="T")
			{
			$rt=substr($userpermissions[$n],1);
			if (is_numeric($rt)&&!$access_override) {$rtfilter[]=$rt;}
			}
		}
	if (count($rtfilter)>0)
		{
		if ($sql_filter!="") {$sql_filter.=" and ";}
		$sql_filter.="resource_type not in (" . join(",",$rtfilter) . ")";
		}
	
	# append "use" access rights, do not show restricted resources unless admin
	if (!checkperm("v")&&!$access_override)
		{
		if ($sql_filter!="") {$sql_filter.=" and ";}
		$sql_filter.="r.access<>'2'";
		}
		
	# append archive searching (don't do this for collections, archived resources can still appear in collections)
	if (substr($search,0,11)!="!collection")
		{
		global $pending_review_visible_to_all;
		if ($archive==0 && $pending_review_visible_to_all)
			{
			# If resources pending review are visible to all, when listing only live resources include
			# pending review (-1) resources too.
			if ($sql_filter!="") {$sql_filter.=" and ";}
			$sql_filter.="(archive='0' or archive=-1)";
			}
		else
			{
			# Append normal filtering.
			if ($sql_filter!="") {$sql_filter.=" and ";}
			$sql_filter.="archive='$archive'";
			}
		}
	
	
	# append ref filter - never return the batch upload template (negative refs)
	if ($sql_filter!="") {$sql_filter.=" and ";}
	$sql_filter.="r.ref>0";
	
	# ------ Advanced 'custom' permissions, need to join to access table.
	$sql_join="";
	global $k;
	if ((!checkperm("v")) &&!$access_override)
		{
		global $usergroup;global $userref;
		# one extra join (rca2) is required for user specific permissions (enabling more intelligent watermarks in search view)
		# the original join is used to gather group access into the search query as well.
		$sql_join=" left outer join resource_custom_access rca2 on r.ref=rca2.resource and rca2.user='$userref'  and (rca2.user_expires is null or rca2.user_expires>now()) and rca2.access<>2  ";	
		$sql_join.=" left outer join resource_custom_access rca on r.ref=rca.resource and rca.usergroup='$usergroup' and rca.access<>2 ";
		
		if ($sql_filter!="") {$sql_filter.=" and ";}
		# If rca.resource is null, then no matching custom access record was found
		# If r.access is also 3 (custom) then the user is not allowed access to this resource.
		# Note that it's normal for null to be returned if this is a resource with non custom permissions (r.access<>3).
		$sql_filter.=" not(rca.resource is null and r.access=3)";
		}
		
	# Join thumbs_display_fields to resource table 	
	$select="r.ref, r.resource_type, r.has_image, r.is_transcoding, r.hit_count, r.creation_date, r.rating, r.user_rating, r.user_rating_count, r.user_rating_total, r.file_extension, r.preview_extension, r.image_red, r.image_green, r.image_blue, r.thumb_width, r.thumb_height, r.archive, r.access, r.colour_key, r.created_by, r.file_modified, r.file_checksum, r.request_count, r.new_hit_count, r.expiry_notification_sent, r.preview_tweaks, r.file_path ";	
	
	$modified_select=hook("modifyselect");
	if ($modified_select){$select.=$modified_select;}	

	# select group and user access rights if available, otherwise select null values so columns can still be used regardless
	# this makes group and user specific access available in the basic search query, which can then be passed through access functions
	# in order to eliminate many single queries.
	if ((!checkperm("v")) &&!$access_override)
		{
		$select.=",rca.access group_access,rca2.access user_access ";
		}
	else {
		$select.=",null group_access, null user_access ";
	}
	
	# add title and country to select (for older installations)
	global $use_resource_column_data;
	if ($use_resource_column_data){$select.=",title,country";}
	
	# add 'joins' to select (adding them 
	$joins=get_resource_table_joins();
	foreach( $joins as $datajoin)
		{
		$select.=",r.field".$datajoin." ";
		}	
	
	# Prepare SQL to add join table for all provided keywods
	
	$suggested=$keywords; # a suggested search
	$fullmatch=true;
	$c=0;$t="";$t2="";$score="";
	
	$keysearch=true;
	
	 # Do not process if a numeric search is provided (resource ID)
	global $config_search_for_number;
	if ($config_search_for_number && is_numeric($search)) {$keysearch=false;}
	
	if ($keysearch)
		{
		for ($n=0;$n<count($keywords);$n++)
			{
			$keyword=$keywords[$n];
			if (substr($keyword,0,1)!="!")
				{
				global $date_field;
				$field=0;#echo "<li>$keyword<br/>";
				if (strpos($keyword,":")!==false)
					{	
					$kw=explode(":",$keyword);
					if ($kw[0]=="day")
						{
						if ($sql_filter!="") {$sql_filter.=" and ";}
						$sql_filter.="r.field$date_field like '____-__-" . $kw[1] . "%' ";
						}
					elseif ($kw[0]=="month")
						{
						if ($sql_filter!="") {$sql_filter.=" and ";}
						$sql_filter.="r.field$date_field like '____-" . $kw[1] . "-%' ";
						}
					elseif ($kw[0]=="year")
						{
						if ($sql_filter!="") {$sql_filter.=" and ";}
						$sql_filter.="r.field$date_field like '" . $kw[1] . "-%' ";
						}
					else
						{
						$ckeywords=explode(";",$kw[1]);
						
						# Fetch field info

						$fieldinfo=sql_query("select ref,type from resource_type_field where name='" . escape_check($kw[0]) . "'",0);
						if (count($fieldinfo)==0) {return false;} else {$fieldinfo=$fieldinfo[0];}
						
						# Special handling for dates
						if ($fieldinfo["type"]==4 || $fieldinfo["type"]==6) 
							{
							$ckeywords=array(str_replace(" ","-",$kw[1]));
							}
						
						$field=$fieldinfo["ref"];
						
						$c++;
						$sql_join.=" join resource_keyword k" . $c . " on k" . $c . ".resource=r.ref and k" . $c . ".resource_type_field='" . $field . "'";
								
						if ($score!="") {$score.="+";}
						$score.="k" . $c . ".hit_count";
						
						# work through all options in an OR approach for multiple selects on the same field
						# where k.resource=type_field=$field and (k*.keyword=3 or k*.keyword=4) etc

						$keyjoin="";
						for ($m=0;$m<count($ckeywords);$m++)
							{
							$keyref=resolve_keyword($ckeywords[$m]);
							if ($keyref===false) {$keyref=-1;}
							
							if ($m!=0) {$keyjoin.=" or ";}
							$keyjoin.="k" . $c. ".keyword='$keyref'";
							
							# Log this
							daily_stat("Keyword usage",$keyref);
			
							# Also add related.
							$related=get_related_keywords($keyref);
							for ($o=0;$o<count($related);$o++)
								{
								$keyjoin.=" or k" . $c . ".keyword='" . $related[$o] . "'";
								}
							}
						if ($keyjoin!="") {$sql_join.=" and (" . $keyjoin . ")";}
						}
					}
				else
					{
					global $noadd;
					if (!in_array($keyword,$noadd)) # skip common words that are excluded from indexing
						{
							
						# Handle wildcards
						if (strpos($keyword,"*")!==false)
							{
							# Keyword contains a wildcard. Expand.
							$c++;
							
							global $wildcard_expand_limit;
							$wildcards=sql_array("select ref value from keyword where keyword like '" . escape_check(str_replace("*","%",$keyword)) . "' order by hit_count desc limit " . $wildcard_expand_limit);

							# Form join							
							$sql_join.=" join resource_keyword k" . $c . " on k" . $c . ".resource=r.ref and k" . $c . ".keyword in ('" . join("','",$wildcards) . "')";
							#echo $sql_join;
							}
						else		
							{
							# Not a wildcard. Normal matching.
							
							$keyref=resolve_keyword($keyword); # Resolve keyword. Ignore any wildcards when resolving. We need wildcards to be present later but not here.
							if ($keyref===false)
								{
								$fullmatch=false;
								$soundex=resolve_soundex($keyword);
								if ($soundex===false)
									{
									# No keyword match, and no keywords sound like this word. Suggest dropping this word.
									$suggested[$n]="";
									}
								else
									{
									# No keyword match, but there's a word that sounds like this word. Suggest this word instead.
									$suggested[$n]="<i>" . $soundex . "</i>";
									}
								}
							else
								{
								# Key match, add to query.
								$c++;
		
								# Add related keywords
								$related=get_related_keywords($keyref);$relatedsql="";
								for ($m=0;$m<count($related);$m++)
									{
									$relatedsql.=" or k" . $c . ".keyword='" . $related[$m] . "'";
									}
								
								# Form join
								$sql_join.=" join resource_keyword k" . $c . " on k" . $c . ".resource=r.ref and (k" . $c . ".keyword='$keyref' $relatedsql)";


								if ($score!="") {$score.="+";}
								$score.="k" . $c . ".hit_count";
								
								# Log this
								daily_stat("Keyword usage",$keyref);
								}
							}
						}
					}
				}
			}
		}
	# Could not match on provided keywords? Attempt to return some suggestions.
	if ($fullmatch==false)
		{
		if ($suggested==$keywords)
			{
			# Nothing different to suggest.
			return "";
			}
		else
			{
			# Suggest alternative spellings/sound-a-likes
			$suggest="";
			if (strpos($search,",")===false) {$suggestjoin=" ";} else {$suggestjoin=", ";}
			for ($n=0;$n<count($suggested);$n++)
				{
				if ($suggested[$n]!="")
					{
					if ($suggest!="") {$suggest.=$suggestjoin;}
					$suggest.=$suggested[$n];
					}
				}
			return $suggest;
			}
		}
	# Some useful debug.
	#echo("keywordjoin=" . $sql_join);
	#echo("<br>Filter=" . $sql_filter);
	#echo("<br>Search=" . $search);

	
	# ------ Search filtering: If search_filter is specified on the user group, then we must always apply this filter.
	global $usersearchfilter;
	$sf=explode(";",$usersearchfilter);
	if (strlen($usersearchfilter)>0)
		{
		for ($n=0;$n<count($sf);$n++)
			{
			$s=explode("=",$sf[$n]);
			if (count($s)!=2) {exit ("Search filter is not correctly configured for this user group.");}

			# Find field(s) - multiple fields can be returned to support several fields with the same name.
			$f=sql_array("select ref value from resource_type_field where name='" . escape_check($s[0]) . "'");
			if (count($f)==0) {exit ("Field(s) with short name '" . $s[0] . "' not found in user group search filter.");}
			
			# Find keyword(s)
			$ks=explode("|",strtolower(escape_check($s[1])));
			$kw=sql_array("select ref value from keyword where keyword in ('" . join("','",$ks) . "')");
			#if (count($k)==0) {exit ("At least one of keyword(s) '" . join("', '",$ks) . "' not found in user group search filter.");}
					
			$sql_join.=" join resource_keyword filter" . $n . " on r.ref=filter" . $n . ".resource and filter" . $n . ".resource_type_field in ('" . join("','",$f) . "') and filter" . $n . ".keyword in ('" . join("','",$kw) . "') ";	
			}
		}
	
	# Handle numeric searches when $config_search_for_number=false, i.e. perform a normal search but include matches for resource ID first
	global $config_search_for_number;
	if (!$config_search_for_number && is_numeric($search))
		{
		# Always show exact resource matches first.
		$order_by="(r.ref='" . $search . "') desc," . $order_by;
		}
	
	# --------------------------------------------------------------------------------
	# Special Searches (start with an exclamation mark)
	# --------------------------------------------------------------------------------
	
	# Can only search for resources that belong to themes
	if (checkperm("J"))
		{
		$sql_join.=" join collection_resource jcr on jcr.resource=r.ref join collection jc on jcr.collection=jc.ref and length(jc.theme)>0 ";
		}
		
	# ------ Special searches ------
	# View Last
	if (substr($search,0,5)=="!last") 
		{
		# Replace r2.ref with r.ref for the alternative query used here.
		$order_by=str_replace("r.ref","r2.ref",$order_by);
		if ($orig_order=="relevance") {$order_by="r2.ref desc";}

		# Extract the number of records to produce
		$last=explode(" ",$search);
		$last=str_replace("!last","",$last[0]);
		
		# Fix the order by for this query (special case due to inner query)
		$order_by=str_replace("r.rating","rating",$order_by);

		return sql_query("select distinct *,r2.hit_count score from (select $select from resource r $sql_join  where $sql_filter order by ref desc limit $last ) r2 order by $order_by",false,$fetchrows);
		}
	
	# View Resources With No Downloads
	if (substr($search,0,12)=="!nodownloads") 
		{
		if ($orig_order=="relevance") {$order_by="ref desc";}

		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where $sql_filter and ref not in (select distinct object_ref from daily_stat where activity_type='Resource download') order by $order_by",false,$fetchrows);
		}
	
	# Duplicate Resources (based on file_checksum)
	if (substr($search,0,11)=="!duplicates") 
		{
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where $sql_filter and file_checksum in (select file_checksum from (select file_checksum,count(*) dupecount from resource group by file_checksum) r2 where r2.dupecount>1) order by file_checksum",false,$fetchrows);
		}
	
	# View Collection
	if (substr($search,0,11)=="!collection")
		{
		if ($orig_order=="relevance") {$order_by="c.date_added desc,r.ref";}
		$colcustperm=$sql_join;
		if (getval("k","")!="") {$sql_filter="ref>0";} # Special case if a key has been provided.
		
		# Extract the collection number
		$collection=explode(" ",$search);$collection=str_replace("!collection","",$collection[0]);
		
		# smart collections update
		global $allow_smart_collections;
		if ($allow_smart_collections){
			$smartsearch=sql_value("select savedsearch value from collection where ref=$collection",null);
			if ($smartsearch!=0){
				$smartsearch=sql_query("select * from collection_savedsearch where ref=$smartsearch");
				$smartsearch=$smartsearch[0];
				$results=do_search($smartsearch['search'], $smartsearch['restypes'], "relevance", $smartsearch['archive'],-1,"desc",true);
				# results is a list of the current search without any restrictions
				# we need to compare against the current collection contents to minimize inserts and deletions
				$current=sql_query("select resource from collection_resource where collection=$collection");
				$current_contents=array(); $results_contents=array();
				if (!empty($current)){foreach($current as $current_item){ $current_contents[]=$current_item['resource'];}}
				if (!empty($results)&&is_array($results)){foreach($results as $results_item){ $results_contents[]=$results_item['ref'];}}
				
					for ($n=0;$n<count($results_contents);$n++)
						{
						if (!in_array($results_contents[$n],$current_contents)){ add_resource_to_collection($results_contents[$n],$collection,true);}
						}
					

					for ($n=0;$n<count($current_contents);$n++)
						{
						if (!in_array($current_contents[$n],$results_contents)){ remove_resource_from_collection($current_contents[$n],$collection,true);}
						}	
					
				}
			}		
		
		return sql_query("select distinct c.date_added,c.comment,r.hit_count score,length(c.comment) commentset, $select from resource r  join collection_resource c on r.ref=c.resource $colcustperm  where c.collection='" . $collection . "' and $sql_filter group by r.ref order by $order_by;",false,$fetchrows);
		}
	
	# View Related
	if (substr($search,0,8)=="!related")
		{
		# Extract the resource number
		$resource=explode(" ",$search);$resource=str_replace("!related","",$resource[0]);
		$order_by=str_replace("r.","",$order_by); # UNION below doesn't like table aliases in the order by.
		
		return sql_query("select distinct r.hit_count score, $select from resource r join resource_related t on (t.related=r.ref and t.resource='" . $resource . "') $sql_join  where 1=1 and $sql_filter group by r.ref 
		UNION
		select distinct r.hit_count score, $select from resource r join resource_related t on (t.resource=r.ref and t.related='" . $resource . "') $sql_join  where 1=1 and $sql_filter group by r.ref 
		order by $order_by;",false,$fetchrows);
		}
		
	# Similar to a colour
	if (substr($search,0,4)=="!rgb")
		{
		$rgb=explode(":",$search);$rgb=explode(",",$rgb[1]);
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where has_image=1 and $sql_filter group by r.ref order by (abs(image_red-" . $rgb[0] . ")+abs(image_green-" . $rgb[1] . ")+abs(image_blue-" . $rgb[2] . ")) asc limit 500;",false,$fetchrows);
		}
		
	# Similar to a colour by key
	if (substr($search,0,10)=="!colourkey")
		{
		# Extract the colour key
		$colourkey=explode(" ",$search);$colourkey=str_replace("!colourkey","",$colourkey[0]);
		
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where has_image=1 and left(colour_key,4)='" . $colourkey . "' and $sql_filter group by r.ref",false,$fetchrows);
		}
	
	global $config_search_for_number;
	if (($config_search_for_number && is_numeric($search)) || substr($search,0,9)=="!resource")
        {
		$theref = escape_check($search);
		$theref = preg_replace("/[^0-9]/","",$theref);
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where ref='$theref' and $sql_filter group by r.ref");
        }

	# Searching for pending archive
	if (substr($search,0,15)=="!archivepending")
		{
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where archive=1 and ref>0 group by r.ref order by $order_by",false,$fetchrows);
		}
	
	if (substr($search,0,12)=="!userpending")
		{
		if ($orig_order=="rating") {$order_by="request_count desc," . $order_by;}
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where archive=-1 and ref>0 group by r.ref order by $order_by",false,$fetchrows);
		}
		
	# View Contributions
	if (substr($search,0,14)=="!contributions") 
		{
		global $userref;
		
		# Extract the user ref
		$cuser=explode(" ",$search);$cuser=str_replace("!contributions","",$cuser[0]);
		
		if ($userref==$cuser) {$sql_filter="archive='$archive'";$sql_join="";} # Disable permissions when viewing your own contributions - only restriction is the archive status
		$select=str_replace(",rca.access group_access,rca2.access user_access ",",null group_access, null user_access ",$select);
		return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where created_by='" . $cuser . "' and r.ref > 0 and $sql_filter group by r.ref order by $order_by",false,$fetchrows);
		}
	
	# Search for resources with images
	if ($search=="!images") return sql_query("select distinct r.hit_count score, $select from resource r $sql_join  where has_image=1 group by r.ref order by $order_by",false,$fetchrows);

	# Search for resources not used in Collections
	if (substr($search,0,7)=="!unused") 
		{
		
		return sql_query("SELECT $select FROM resource r $sql_join  where r.ref>0 and r.ref not in (select c.resource from collection_resource c) and $sql_filter",false,$fetchrows);
		}	
	
	# Search for a list of resources
	if (substr($search,0,5)=="!list") 
		{
		$resources=explode(" ",$search);$resources=str_replace("!list","",$resources[0]);

		$resources=str_replace(":"," OR r.ref=",$resources);
		return sql_query("SELECT distinct r.hit_count score, $select FROM resource r $sql_join  where r.ref=$resources and $sql_filter order by $order_by",false,$fetchrows);
		}		



	# -------------------------------------------------------------------------------------
	# Standard Searches
	# -------------------------------------------------------------------------------------
	
	# We've reached this far without returning.
	# This must be a standard (non-special) search.
	
	# Construct and perform the standard search query.
	$sql="";
	if ($sql_filter!="")
		{
		if ($sql!="") {$sql.=" and ";}
		$sql.=$sql_filter;
		}

	# Append custom permissions	
	$t.=$sql_join;
	
	if ($score=="") {$score="r.hit_count";} # In case score hasn't been set (i.e. empty search)
	global $max_results;
	if (($t2!="") && ($sql!="")) {$sql=" and " . $sql;}
	
	# Compile final SQL
	$sql="select distinct $score score, $select from resource r" . $t . "  where $t2 $sql group by r.ref order by $order_by limit $max_results";

	# Debug
	debug("\n" . $sql);

	# Execute query
	$result=sql_query($sql,false,$fetchrows);
	if (count($result)>0) {return $result;}
	
	# (temp) - no suggestion for field-specific searching for now - TO DO: modify function below to support this
	if (strpos($search,":")!==false) {return "";}
	
	# All keywords resolved OK, but there were no matches
	# Remove keywords, least used first, until we get results.
	$sql="";
	for ($n=0;$n<count($keywords);$n++)
		{
		if ($sql!="") {$sql.=" or ";}
		$sql.="keyword='" . $keywords[$n] . "'";
		}
	$least=sql_value("select keyword value from keyword where $sql order by hit_count asc limit 1","");
	return trim_spaces(str_replace(" " . $least . " "," "," " . join(" ",$keywords) . " "));
	}
}


function resolve_soundex($keyword)
	{
	# returns the most commonly used keyword that sounds like $keyword, or failing a soundex match,
	# the most commonly used keyword that starts with the same few letters.
	$soundex=sql_value("select keyword value from keyword where soundex=soundex('$keyword') order by hit_count desc limit 1",false);
	if (($soundex===false) && (strlen($keyword)>=4))
		{
		# No soundex match, suggest words that start with the same first few letters.
		return sql_value("select keyword value from keyword where keyword like '" . substr($keyword,0,4) . "%' order by hit_count desc limit 1",false);
		}
	return $soundex;
	}
	
function suggest_refinement($refs,$search)
	{
	# Given an array of resource references ($refs) and the original
	# search query ($search), produce a list of suggested search refinements to 
	# reduce the result set intelligently.
	$in=join(",",$refs);
	$suggest=array();
	# find common keywords
	$refine=sql_query("select k.keyword,count(*) c from resource_keyword r join keyword k on r.keyword=k.ref and r.resource in ($in) and length(k.keyword)>=3 and length(k.keyword)<=15 and k.keyword not like '%0%' and k.keyword not like '%1%' and k.keyword not like '%2%' and k.keyword not like '%3%' and k.keyword not like '%4%' and k.keyword not like '%5%' and k.keyword not like '%6%' and k.keyword not like '%7%' and k.keyword not like '%8%' and k.keyword not like '%9%' group by k.keyword order by c desc limit 5");
	for ($n=0;$n<count($refine);$n++)
		{
		if (strpos($search,$refine[$n]["keyword"])===false)
			{
			$suggest[]=$search . " " . $refine[$n]["keyword"];
			}
		}
	return $suggest;
	}
	
function get_advanced_search_fields($archive=false)
	{
	# Returns a list of fields suitable for advanced searching.	
	$return=array();
	$fields=sql_query("select ref, name, title, type, options ,order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown from resource_type_field where advanced_search=1 and keywords_index=1 and length(name)>0 " . (($archive)?"":"and resource_type<>999") . " order by resource_type,order_by");
	# Apply field permissions
	for ($n=0;$n<count($fields);$n++)
		{
		if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"]))
		&& !checkperm("f-" . $fields[$n]["ref"]) && !checkperm("T" . $fields[$n]["resource_type"]))
		{$return[]=$fields[$n];}
		}
	return $return;
	}

function render_search_field($field,$value="",$autoupdate,$class="stdwidth",$forsearchbar=false)
	{
	# Renders the HTML for the provided $field for inclusion in a search form, for example the
	# advanced search page.
	#
	# $field	an associative array of field data, i.e. a row from the resource_type_field table.
	# $name		the input name to use in the form (post name)
	# $value	the default value to set for this field, if any
	
	global $auto_order_checkbox,$lang,$category_tree_open,$minyear;
	$name="field_" . $field["ref"];
	
	if (!$forsearchbar)
		{
		?>
		<div class="Question">
		<label><?php echo i18n_get_translated($field["title"])?></label>
		<?php
		}
	else
		{
		?>
		<div class="SearchItem">
		<?php echo i18n_get_translated($field["title"])?></br>
		<?php
		}

	switch ($field["type"]) {
		case 0: # -------- Text boxes
		case 1:
		case 5:
		?><input class="<?php echo $class ?>" type=text name="field_<?php echo $field["ref"]?>" value="<?php echo htmlspecialchars($value)?>" <?php if ($autoupdate) { ?>onChange="UpdateResultCount();"<?php } ?> onKeyPress="if (!(updating)) {setTimeout('UpdateResultCount()',2000);updating=true;}"><?php
		break;
	
		case 2: 
		case 3:
		# -------- Show a check list or dropdown for dropdowns and check lists?
		# By default show a checkbox list for both (for multiple selections this enabled OR functionality)
		
		# Translate all options
		$options=trim_array(explode(",",$field["options"]));
		$option_trans=array();
		$option_trans_simple=array();
		for ($m=0;$m<count($options);$m++)
			{
			$trans=i18n_get_translated($options[$m]);
			$option_trans[$options[$m]]=$trans;
			$option_trans_simple[]=$trans;
			}

		if ($auto_order_checkbox) {asort($option_trans);}
		$options=array_keys($option_trans); # Set the options array to the keys, so it is now effectively sorted by translated string		
		
		if ($field["display_as_dropdown"])
			{
			# Show as a dropdown box
			$set=trim_array(explode(";",cleanse_string($value,true)));
			?><select class="<?php echo $class ?>" name="field_<?php echo $field["ref"]?>" <?php if ($autoupdate) { ?>onChange="UpdateResultCount();"<?php } ?>><option value=""></option><?php
			foreach ($option_trans as $option=>$trans)
				{
				if (trim($trans)!="")
					{
					?>
					<option value="<?php echo htmlspecialchars(trim($trans))?>" <?php if (in_array(cleanse_string($trans,true),$set)) {?>selected<?php } ?>><?php echo htmlspecialchars(trim($trans))?></option>
					<?php
					}
				}
			?></select><?php
			}
		else
			{
			# Show as a checkbox list (default)
			
			$set=trim_array(explode(";",cleanse_string($value,true)));
			$wrap=0;
			$l=average_length($option_trans_simple);
			$cols=10;
			if ($l>5)  {$cols=6;}
			if ($l>10) {$cols=4;}
			if ($l>15) {$cols=3;}
			if ($l>25) {$cols=2;}

			$height=ceil(count($options)/$cols);
			
			global $checkbox_ordered_vertically;
			if ($checkbox_ordered_vertically)
				{
				# ---------------- Vertical Ordering (only if configured) -----------
				?><table cellpadding=2 cellspacing=0><tr><?php
				for ($y=0;$y<$height;$y++)
					{
					for ($x=0;$x<$cols;$x++)
						{
						# Work out which option to fetch.
						$o=($x*$height)+$y;
						if ($o<count($options))
							{
							$option=$options[$o];
							$trans=$option_trans[$option];

							$name=$field["ref"] . "_" . urlencode($option);
							if ($option!="")
								{
								?>
								<td valign=middle><input type=checkbox id="<?php echo $name?>" name="<?php echo $name?>" value="yes" <?php if (in_array(cleanse_string($trans,true),$set)) {?>checked<?php } ?> <?php if ($autoupdate) { ?>onClick="UpdateResultCount();"<?php } ?>></td><td valign=middle><?php echo htmlspecialchars($trans)?>&nbsp;&nbsp;</td>
								<?php
								}
							}
						}
					?></tr><tr><?php
					}
				?></tr></table><?php
				}
			else
				{
				# ---------------- Horizontal Ordering (Standard) ---------------------				
				?><table cellpadding=2 cellspacing=0><tr><?php
				foreach ($option_trans as $option=>$trans)
					{
					$wrap++;if ($wrap>$cols) {$wrap=1;?></tr><tr><?php }
					$name=$field["ref"] . "_" . urlencode($option);
					if ($option!="")
						{
						?>
						<td valign=middle><input type=checkbox id="<?php echo $name?>" name="<?php echo $name?>" value="yes" <?php if (in_array(cleanse_string(i18n_get_translated($option),true),$set)) {?>checked<?php } ?> <?php if ($autoupdate) { ?>onClick="UpdateResultCount();"<?php } ?>></td><td valign=middle><?php echo htmlspecialchars($trans)?>&nbsp;&nbsp;</td>
						<?php
						}
					}
				?></tr></table><?php
				}
				
			}
		break;
		
		case 4:
		case 6: # ----- Date types
		$found_year='';$found_month='';$found_day='';
		$s=explode("-",$value);
		if (count($s)>=3)
			{
			$found_year=$s[0];
			$found_month=$s[1];
			$found_day=$s[2];
			}
		?>		
		<select name="<?php echo $name?>_year" class="SearchWidth" style="width:100px;" <?php if ($autoupdate) { ?>onChange="UpdateResultCount();"<?php } ?>>
		  <option value=""><?php echo $lang["anyyear"]?></option>
		  <?php
		  $y=date("Y");
		  for ($d=$minyear;$d<=$y;$d++)
			{
			?><option <?php if ($d==$found_year) { ?>selected<?php } ?>><?php echo $d?></option><?php
			}
		  ?>
		</select>
		<select name="<?php echo $name?>_month" class="SearchWidth" style="width:100px;" <?php if ($autoupdate) { ?>onChange="UpdateResultCount();"<?php } ?>>
		  <option value=""><?php echo $lang["anymonth"]?></option>
		  <?php
		  for ($d=1;$d<=12;$d++)
			{
			$m=str_pad($d,2,"0",STR_PAD_LEFT);
			?><option <?php if ($d==$found_month) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $lang["months"][$d-1]?></option><?php
			}
		  ?>
		</select>
		<select name="<?php echo $name?>_day" class="SearchWidth" style="width:100px;" <?php if ($autoupdate) { ?>onChange="UpdateResultCount();"<?php } ?>>
		  <option value=""><?php echo $lang["anyday"]?></option>
		  <?php
		  for ($d=1;$d<=31;$d++)
			{
			$m=str_pad($d,2,"0",STR_PAD_LEFT);
			?><option <?php if ($d==$found_day) { ?>selected<?php } ?> value="<?php echo $m?>"><?php echo $m?></option><?php
			}
		  ?>
		</select>
		<?php		
		break;
		
		
		case 7: # ----- Category Tree
		$options=$field["options"];
		$set=trim_array(explode(";",cleanse_string($value,true)));
		if ($forsearchbar)
			{
			# On the search bar?
			# Produce a smaller version of the category tree in a single dropdown - max two levels
			?>
			<select class="<?php echo $class ?>" name="field_<?php echo $field["ref"]?>"><option value=""></option><?php
			$class=explode("\n",$options);

			for ($t=0;$t<count($class);$t++)
				{
				$s=explode(",",$class[$t]);
				if (count($s)==3 && $s[1]==0)
					{
					# Found a first level
					?>
					<option <?php if (in_array(cleanse_string($s[2],true),$set)) {?>selected<?php } ?>><?php echo $s[2] ?></option>
					<?php
					
					# Parse tree again looking for level twos at this point
					for ($u=0;$u<count($class);$u++)
						{
						$v=explode(",",$class[$u]);
						if (count($v)==3 && $v[1]==$s[0])
							{
							# Found a first level
							?>
							<option value="<?php echo $s[2] . "," . $v[2] ?>" <?php if (in_array(cleanse_string($s[2],true),$set) && in_array(cleanse_string($v[2],true),$set)) {?>selected<?php } ?>>&nbsp;-&nbsp;<?php echo $v[2] ?></option>
							<?php
							}						
						}
					}
				}			
			?>
			</select>
			<?php
			}
		else
			{
			# For advanced search and elsewhere, include the category tree.
			include "category_tree.php";
			}
		break;
		}
	?>
	<div class="clearerleft"> </div>
	</div>
	<?php
	}

function search_form_to_search_query($fields,$fromsearchbar=false)
	{
	# Take the data in the the posted search form that contained $fields, and assemble
	# a search query string that can be used for a standard search.
	#
	# This is used to take the advanced search form and assemble it into a search query.
	
	global $auto_order_checkbox;
	$search="";
	if (getval("year","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.="year:" . getval("year","");	
		}
	if (getval("month","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.="month:" . getval("month","");	
		}
	if (getval("day","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.="day:" . getval("day","");	
		}
	if (getval("allfields","")!="")
		{
		if ($search!="") {$search.=", ";}
		$search.=join(", ",explode(" ",getvalescaped("allfields",""))); # prepend 'all fields' option
		}
	for ($n=0;$n<count($fields);$n++)
		{
		switch ($fields[$n]["type"])
			{
			case 0: # -------- Text boxes
			case 1:
			case 5:
			$name="field_" . $fields[$n]["ref"];
			$value=getvalescaped($name,"");
			if ($value!="")
				{
				$vs=split_keywords($value);
				for ($m=0;$m<count($vs);$m++)
					{
					if ($search!="") {$search.=", ";}
					$search.=$fields[$n]["name"] . ":" . strtolower($vs[$m]);
					}
				}
			break;
			
			case 2: # -------- Dropdowns / check lists
			case 3:
			if ($fields[$n]["display_as_dropdown"])
				{
				# Process dropdown box
				$name="field_" . $fields[$n]["ref"];
				$value=getvalescaped($name,"");
				if ($value!="")
					{
					/*
					$vs=split_keywords($value);
					for ($m=0;$m<count($vs);$m++)
						{
						if ($search!="") {$search.=", ";}
						$search.=$fields[$n]["name"] . ":" . strtolower($vs[$m]);
						}
					*/
					if ($search!="") {$search.=", ";}
					$search.=$fields[$n]["name"] . ":" . $value;					
					}
				}
			else
				{
				# Process checkbox list
				$options=trim_array(explode(",",$fields[$n]["options"]));
				$p="";
				$c=0;
				for ($m=0;$m<count($options);$m++)
					{
					$name=$fields[$n]["ref"] . "_" . urlencode($options[$m]);
					$value=getvalescaped($name,"");
					if ($value=="yes")
						{
						$c++;
						if ($p!="") {$p.=";";}
						$p.=strtolower(i18n_get_translated($options[$m]));
						}
					}
				if ($c==count($options))
					{
					# all options ticked - omit from the search
					$p="";
					}
				if ($p!="")
					{
					if ($search!="") {$search.=", ";}
					$search.=$fields[$n]["name"] . ":" . $p;
					}
				}
			break;

			case 4:
			case 6:
			$name="field_" . $fields[$n]["ref"];
			$datepart="";
			if (getval($name . "_year","")!="")
				{
				$datepart.=getval($name . "_year","");
				if (getval($name . "_month","")!="")
					{
					$datepart.="-" . getval($name . "_month","");
					if (getval($name . "_day","")!="")
						{
						$datepart.="-" . getval($name . "_day","");
						}
					}
				}
			if ($datepart!="")
				{
				if ($search!="") {$search.=", ";}
				$search.=$fields[$n]["name"] . ":" . $datepart;
				}

			break;

			case 7: # -------- Category tree
			$name="field_" . $fields[$n]["ref"];
			$value=getvalescaped($name,"");
			$selected=trim_array(explode(",",$value));
			$p="";
			for ($m=0;$m<count($selected);$m++)
				{
				if ($selected[$m]!="")
					{
					if ($p!="") {$p.=";";}
					$p.=$selected[$m];
					}
				}
			if ($p!="")
				{
				if ($search!="") {$search.=", ";}
				$search.=$fields[$n]["name"] . ":" . $p;
				}
			break;

			}
		}
	return $search;
	}

?>
