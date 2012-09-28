<?php
# General functions, useful across the whole solution

include_once ("language_functions.php");

$GLOBALS['get_resource_path_fpcache'] = array();
function get_resource_path($ref,$getfilepath,$size,$generate,$extension="jpg",$scramble=-1,$page=1,$watermarked=false,$file_modified="",$alternative=-1,$includemodified=true)
	{
	# returns the correct path to resource $ref of size $size ($size==empty string is original resource)
	# If one or more of the folders do not exist, and $generate=true, then they are generated
	
	    $override=hook("get_resource_path_override","general",array($ref,$getfilepath,$size,$generate,$extension,$scramble,$page,$watermarked,$file_modified,$alternative,$includemodified));
	    if (is_string($override)) {return $override;}

	global $storagedir;

	if ($size=="")
		{
		# For the full size, check to see if the full path is set and if so return that.
		global $get_resource_path_fpcache;
		if (!isset($get_resource_path_fpcache[$ref])) {$get_resource_path_fpcache[$ref]=sql_value("select file_path value from resource where ref='$ref'","");}
		$fp=$get_resource_path_fpcache[$ref];
		
		# Test to see if this nosize file is of the extension asked for, else skip the file_path and return a $storagedir path. 
		# If using staticsync, file path will be set already, but we still want the $storagedir path for a nosize preview jpg.
		# Also, returning the original filename when a nosize 'jpg' is looked for is no good, since preview_preprocessing.php deletes $target.
		
		$test_ext = explode(".",$fp);$test_ext=trim(strtolower($test_ext[count($test_ext)-1]));
		
		if (($test_ext == $extension) && (strlen($fp)>0) && (strpos($fp,"/")!==false) && !($alternative > 0))
			{
				
			if ($getfilepath)
				{
				global $syncdir; 
            	$syncdirmodified=hook("modifysyncdir","all",array($ref)); if ($syncdirmodified!=""){return $syncdirmodified;}	
            	return $syncdir . "/" . $fp;
				}
			else 
				{
				global $baseurl_short, $k;
				return $baseurl_short . "pages/download.php?ref={$ref}&size={$size}&ext={$extension}&noattach=true&k={$k}&page={$page}"; 
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
	
	$folder="";
	#if (!file_exists(dirname(__FILE__) . $folder)) {mkdir(dirname(__FILE__) . $folder,0777);}
	
	for ($n=0;$n<strlen($ref);$n++)
		{
		$folder.=substr($ref,$n,1);
		if (($scramble) && ($n==(strlen($ref)-1))) {$folder.="_" . $scramblepath;}
		$folder.="/";
		#echo "<li>" . $folder;
		if ((!(file_exists($storagedir . "/" . $folder))) && $generate) {mkdir($storagedir . "/" . $folder,0777);chmod($storagedir . "/" . $folder,0777);}
		}
		
	# Add the page to the filename for everything except page 1.
	if ($page==1) {$p="";} else {$p="_" . $page;}
	
	# Add the alternative file ID to the filename if provided
	if ($alternative>0) {$a="_alt_" . $alternative;} else {$a="";}
	
	# Add the watermarked url too
	if ($watermarked) {$p.="_wm";}
	
	# Fetching the file path? Add the full path to the file
	$filefolder=$storagedir . "/" . $folder;
	if ($getfilepath)
	    {
	    $folder=$filefolder;
	    }
	else
	    {
	    global $storageurl;
	    $folder=$storageurl . "/" . $folder;
	    }
	
	if ($scramble)
		{
		$file_old=$filefolder . $ref . $size . $p . $a . "." . $extension;
		$file_new=$filefolder . $ref . $size . $p . $a . "_" . substr(md5($ref . $size . $p . $a . $scramble_key),0,15) . "." . $extension;
		$file=$folder . $ref . $size . $p . $a . "_" . substr(md5($ref . $size . $p . $a . $scramble_key),0,15) . "." . $extension;
		if (file_exists($file_old))
		  	{
			rename($file_old, $file_new);
		  	}
		}
	else
		{
		$file=$folder . $ref . $size . $p . $a . "." . $extension;
		}

# Append modified date/time to the URL so the cached copy is not used if the file is changed.
	if (!$getfilepath && $includemodified)
		{
		if ($file_modified=="")
			{
			$data=get_resource_data($ref);
			$file .= "?v=" . urlencode($data['file_modified']);
			}
		else
			{
			# Use the provided value
			$file .= "?v=" . urlencode($file_modified);
			}
		}
	
	return  $file;
	}
	
$GLOBALS['get_resource_data_cache'] = array();
function get_resource_data($ref,$cache=true)
	{
	if ($ref==""){return false;}
	# Returns basic resource data (from the resource table alone) for resource $ref.
	# For 'dynamic' field data, see get_resource_field_data
	global $default_resource_type, $get_resource_data_cache,$always_record_resource_creator;
	if ($cache && isset($get_resource_data_cache[$ref])) {return $get_resource_data_cache[$ref];}
	$resource=sql_query("select *,mapzoom from resource where ref='$ref'");
	if (count($resource)==0) 
		{
		if ($ref>0)
			{
			return false;
			}
		else
			{
			# For batch upload templates (negative reference numbers), generate a new resource.
			if (isset($always_record_resource_creator) && $always_record_resource_creator)
				{
					global $userref;
                			$user=$userref;
                		} else {$user=-1;}
			$wait=sql_query("insert into resource (ref,resource_type,created_by) values ('$ref','$default_resource_type','$user')");
			$resource=sql_query("select *,mapzoom from resource where ref='$ref'");
			}
		}
	
	$get_resource_data_cache[$ref]=$resource[0];
	return $resource[0];
	}
	
function update_hitcount($ref)
	{
	global $resource_hit_count_on_downloads;
	
	# update hit count if not tracking downloads only
	if (!$resource_hit_count_on_downloads) 
		{ 
		# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability).
		sql_query("update resource set new_hit_count=greatest(hit_count,new_hit_count)+1 where ref='$ref'");
		}
	}	
	
function get_resource_type_field($field)
	{
	# Returns field data from resource_type_field for the given field.
	$return=sql_query("select * from resource_type_field where ref='$field'");
	if (count($return)==0)
		{
		return false;
		}
	else
		{
		return $return[0];
		}
	}
function get_resource_field_data($ref,$multi=false,$use_permissions=true,$originalref=-1,$external_access=false,$ord_by=false)
{
    # Returns field data and field properties (resource_type_field and resource_data tables)
    # for this resource, for display in an edit / view form.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.

    # Find the resource type.
    if ($originalref==-1) {$originalref = $ref;} # When a template has been selected, only show fields for the type of the original resource ref, not the template (which shows fields for all types)
    $rtype = sql_value("select resource_type value from resource where ref='$originalref'",0);

    # If using metadata templates, 
    $templatesql = "";
    global $metadata_template_resource_type;
    if (isset($metadata_template_resource_type) && $metadata_template_resource_type==$rtype) {
        # Show all resource fields, just as with editing multiple resources.
        $multi = true;
    }

    $return = array();
    if ($ord_by)
    {
    	 $fields = sql_query("select d.value,d.resource_type_field,f.exiftool_field,f.value_filter,f.name,f.display_template,f.display_field,f.tab_name,f.options,f.keywords_index,f.resource_column,f.required,f.type,f.title,f.resource_type,f.required frequired,f.ref,f.ref fref, f.help_text,f.partial_index,f.external_user_access,f.hide_when_uploading,f.hide_when_restricted,f.omit_when_copying,f.regexp_filter from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource='$ref' where ( " . (($multi)?"1=1":"f.resource_type=0 or f.resource_type=999 or f.resource_type='$rtype'") . ") order by f.order_by,f.resource_type,f.ref");
    } else {
    $fields = sql_query("select d.value,d.resource_type_field,f.exiftool_field,f.value_filter,f.name,f.display_template,f.display_field,f.tab_name,f.options,f.keywords_index,f.resource_column,f.required,f.type,f.title,f.resource_type,f.required frequired,f.ref, f.ref fref, f.help_text,f.partial_index,f.external_user_access,f.hide_when_uploading,f.hide_when_restricted,f.omit_when_copying,f.regexp_filter from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource='$ref' where ( " . (($multi)?"1=1":"f.resource_type=0 or f.resource_type=999 or f.resource_type='$rtype'") . ") order by f.resource_type,f.order_by,f.ref");
    }
    # Build an array of valid types and only return fields of this type. Translate field titles. 
    $validtypes = sql_array("select ref value from resource_type");
    $validtypes[] = 0; $validtypes[] = 999; # Support archive and global.

    for ($n = 0;$n<count($fields);$n++) {
        if
	(
		(
		!$use_permissions || 
			(
			# Upload only edit access to this field?
			$ref<0 && checkperm("P" . $fields[$n]["fref"])
			)    
		||
			(
				(
				checkperm("f*") || checkperm("f" . $fields[$n]["fref"])
				)
			&& !checkperm("f-" . $fields[$n]["fref"]) && !checkperm("T" . $fields[$n]["resource_type"])
			)
		)
        && in_array($fields[$n]["resource_type"],$validtypes) &&
		(
		!
			(
			$external_access && !$fields[$n]["external_user_access"]
			)
		)
	) {
            $fields[$n]["title"] = lang_or_i18n_get_translated($fields[$n]["title"], "fieldtitle-"); 
            $return[] = $fields[$n];
        }
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
	
function get_resource_types($types="")
	{
	# Returns a list of resource types. The standard resource types are translated using $lang. Custom resource types are i18n translated.
	
	// support getting info for a comma-delimited list of restypes (as in a search)
	if ($types==""){$sql="";} else {$sql=" where ref in ($types) ";}
	
	$r=sql_query("select * from resource_type $sql order by order_by,ref");
	$return=array();
	# Translate names and check permissions
	for ($n=0;$n<count($r);$n++)
		{
		if (!checkperm('T' . $r[$n]['ref']))
			{
			$r[$n]["name"]=lang_or_i18n_get_translated($r[$n]["name"], "resourcetype-");	# Translate name
			$return[]=$r[$n]; # Add to return array
			}
		}
	return $return;
	}

function get_resource_top_keywords($resource,$count)
	{
	# Return the top $count keywords (by hitcount) used by $resource.
	# This is for the 'Find Similar' search.
	# Keywords that are too short or too long, or contain numbers are dropped - they are probably not as meaningful in
	# the contexts of this search (consider being offered "12" or "OKB-34" as an option?)
	return sql_array("select distinct k.ref,k.keyword value from keyword k,resource_keyword r,resource_type_field f where k.ref=r.keyword and r.resource='$resource' and f.ref=r.resource_type_field and f.use_for_similar=1 and length(k.keyword)>=3 and length(k.keyword)<=15 and k.keyword not like '%0%' and k.keyword not like '%1%' and k.keyword not like '%2%' and k.keyword not like '%3%' and k.keyword not like '%4%' and k.keyword not like '%5%' and k.keyword not like '%6%' and k.keyword not like '%7%' and k.keyword not like '%8%' and k.keyword not like '%9%' order by k.hit_count desc limit $count");
	}

if (!function_exists("split_keywords")){
function split_keywords($search,$index=false,$partial_index=false,$is_date=false)
	{
	# Takes $search and returns an array of individual keywords.
	
	global $config_trimchars;

	if ($index && $is_date)
		{
		# Date handling... index a little differently to support various levels of date matching (Year, Year+Month, Year+Month+Day).
		$s=explode("-",$search);
		if (count($s)>=3)
			{
			return (array($s[0],$s[0] . "-" . $s[1],$search));
			}
		else
			{
			return $search;
			}
		}


	# Remove any real / unescaped lf/cr
	$search=str_replace("\r"," ",$search);
	$search=str_replace("\n"," ",$search);
	$search=str_replace("\\r"," ",$search);
	$search=str_replace("\\n"," ",$search);

	$ns=trim_spaces($search);
	
	if ((substr($ns,0,1)==",") ||  ($index==false && strpos($ns,":")!==false)) # special 'constructed' query type, split using comma so
	# we support keywords with spaces.
		{
		$ns=cleanse_string($ns,true,!$index);
		$return=explode(",",$ns);
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
			$return2=trim_array($return2,$config_trimchars);
			if ($partial_index) {return add_partial_index($return2);}
			return $return2;
			}
		else
			{
			return trim_array($return,$config_trimchars);
			}
		}
	else
		{
		# split using spaces and similar chars (according to configured whitespace characters)
		$ns=explode(" ",cleanse_string($ns,false,!$index));
		$ns=trim_array($ns,$config_trimchars);
		if ($index && $partial_index) {
			return add_partial_index($ns);
		}
		return $ns;
		}

	}
}

if (!function_exists("cleanse_string")){
function cleanse_string($string,$preserve_separators,$preserve_hyphen=false)
        {
        # Removes characters from a string prior to keyword splitting, for example full stops
        # Also makes the string lower case ready for indexing.
        global $config_separators;
        $separators=$config_separators;
        
        if ($preserve_hyphen)
        	{
        	# Preserve hyphen - used when NOT indexing so we know which keywords to omit from the search.
			if ((substr($string,0,1)=="-" /*support minus as first character for simple NOT searches */ || strpos($string," -")!==false) && strpos($string," - ")==false)
				{
					$separators=array_diff($separators,array("-")); # Remove hyphen from separator array.
				}
        	}
        if ($preserve_separators)
                {
                return mb_strtolower(trim_spaces(str_replace($separators," ",$string)),'UTF-8');
                }
        else
                {
                # Also strip out the separators used when specifying multiple field/keyword pairs (comma and colon)
                $s=$separators;
                $s[]=",";
                $s[]=":";
                return mb_strtolower(trim_spaces(str_replace($s," ",$string)),'UTF-8');
                }
        }
}

if (!function_exists("resolve_keyword")){
function resolve_keyword($keyword,$create=false)
	{
	# Returns the keyword reference for $keyword, or false if no such keyword exists.
	$return=sql_value("select ref value from keyword where keyword='" . trim(escape_check($keyword)) . "'",false);
	if ($return===false && $create)
		{
		# Create a new keyword.
		sql_query("insert into keyword (keyword,soundex,hit_count) values ('" . escape_check($keyword) . "',soundex('" . escape_check($keyword) . "'),0)");
		$return=sql_insert_id();
		}
	return $return;
	}
}

function add_partial_index($keywords)
	{
	# For each keywords in the supplied keywords list add all possible infixes and return the combined array.
	# This therefore returns all keywords that need indexing for the given string.
	# Only for fields with 'partial_index' enabled.
	$return=array();
	$position=0;
	$x=0;
	for ($n=0;$n<count($keywords);$n++)
		{
		$keyword=trim($keywords[$n]);
		$return[$x]['keyword']=$keyword;
		$return[$x]['position']=$position;
		$x++;
		if (strpos($keyword," ")===false) # Do not do this for keywords containing spaces as these have already been broken to individual words using the code above.
			{
			global $partial_index_min_word_length;
			# For each appropriate infix length
			for ($m=$partial_index_min_word_length;$m<strlen($keyword);$m++)
				{
				# For each position an infix of this length can exist in the string
				for ($o=0;$o<=strlen($keyword)-$m;$o++)
					{
					$infix=substr($keyword,$o,$m);
					$return[$x]['keyword']=$infix;
					$return[$x]['position']=$position; // infix has same position as root
					$x++;
					}
				}
			} # End of no-spaces condition
		$position++; // end of root keyword
		} # End of partial indexing keywords loop
	return $return;
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
		

if (!function_exists("update_resource_keyword_hitcount")){	
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
}
	
function copy_hitcount_to_live()
	{
	# Copy the temporary hit count used for relevance matching to the live column so it's activated (see comment for
	# update_resource_keyword_hitcount())
	sql_query("update resource_keyword set hit_count=new_hit_count");
	
	# Also update the resource table
	# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability)
	sql_query("update resource set hit_count=greatest(hit_count,new_hit_count)");
	}
	
function get_image_sizes($ref,$internal=false,$extension="jpg",$onlyifexists=true)
	{
	# Returns a table of available image sizes for resource $ref. The standard image sizes are translated using $lang. Custom image sizes are i18n translated.
	# The original image file assumes the name of the 'nearest size (up)' in the table

	global $imagemagick_calculate_sizes;

	# Work out resource type
	$resource_type=sql_value("select resource_type value from resource where ref='$ref'","");

	# add the original image
	$return=array();
	$lastname=sql_value("select name value from preview_size where width=(select max(width) from preview_size)",""); # Start with the highest resolution.
	$lastpreview=0;$lastrestricted=0;
	$path2=get_resource_path($ref,true,'',false,$extension);

	if (file_exists($path2) && !checkperm("T" . $resource_type . "_"))
	{ 
		$returnline=array();
		$returnline["name"]=lang_or_i18n_get_translated($lastname, "imagesize-");
		$returnline["allow_preview"]=$lastpreview;
		$returnline["allow_restricted"]=$lastrestricted;
		$returnline["path"]=$path2;
		$returnline["id"]="";
		$dimensions = sql_query("select width,height,file_size,resolution,unit from resource_dimensions where resource=". $ref);
		
		if (count($dimensions))
			{
			$sw = $dimensions[0]['width']; if ($sw==0) {$sw="?";}
			$sh = $dimensions[0]['height']; if ($sh==0) {$sh="?";}
			$filesize=$dimensions[0]['file_size'];
			# resolution and unit are not necessarily available, set to empty string if so.
			$resolution = ($dimensions[0]['resolution'])?$dimensions[0]['resolution']:"";
			$unit = ($dimensions[0]['unit'])?$dimensions[0]['unit']:"";
			}
		else
			{
			global $imagemagick_path;
			$file=$path2;
			$filesize=filesize_unlimited($file);
			
			# imagemagick_calculate_sizes is normally turned off 
			if (isset($imagemagick_path) && $imagemagick_calculate_sizes)
				{
				# Use ImageMagick to calculate the size
				
				$prefix = '';
				# Camera RAW images need prefix
				if (preg_match('/^(dng|nef|x3f|cr2|crw|mrw|orf|raf|dcr)$/i', $extension, $rawext)) { $prefix = $rawext[0] .':'; }

				# Locate imagemagick.
                $identify_fullpath = get_utility_path("im-identify");
                if ($identify_fullpath==false) {exit("Could not find ImageMagick 'identify' utility at location '$imagemagick_path'.");}	
				# Get image's dimensions.
                $identcommand = $identify_fullpath . ' -format %wx%h '. escapeshellarg($prefix . $file) .'[0]';
				$identoutput=run_command($identcommand);
				preg_match('/^([0-9]+)x([0-9]+)$/ims',$identoutput,$smatches);
				@list(,$sw,$sh) = $smatches;
				if (($sw!='') && ($sh!=''))
				  {
					sql_query("insert into resource_dimensions (resource, width, height, file_size) values('". $ref ."', '". $sw ."', '". $sh ."', '" . $filesize . "')");
					}
				}	
			else 
				{
				# check if this is a raw file.	
				$rawfile = false;
				if (preg_match('/^(dng|nef|x3f|cr2|crw|mrw|orf|raf|dcr)$/i', $extension, $rawext)){$rawfile=true;}
					
				# Use GD to calculate the size
				if (!((@list($sw,$sh) = @getimagesize($file))===false)&& !$rawfile)
				 	{		
					sql_query("insert into resource_dimensions (resource, width, height, file_size) values('". $ref ."', '". $sw ."', '". $sh ."', '" . $filesize . "')");
					}
				else
					{
					# Size cannot be calculated.
					$sw="?";$sh="?";
					
					# Insert a dummy row to prevent recalculation on every view.
					sql_query("insert into resource_dimensions (resource, width, height, file_size) values('". $ref ."','0', '0', '" . $filesize . "')");
					}
				}
			}
		if (!is_numeric($filesize)) {$returnline["filesize"]="?";$returnline["filedown"]="?";}
		else {$returnline["filedown"]=ceil($filesize/50000) . " seconds @ broadband";$returnline["filesize"]=formatfilesize($filesize);}
		$returnline["width"]=$sw;			
		$returnline["height"]=$sh;
		$returnline["extension"]=$extension;
		(isset($resolution))?$returnline["resolution"]=$resolution:$returnline["resolution"]="";
		(isset($unit))?$returnline["unit"]=$unit:$returnline["unit"]="";
		$return[]=$returnline;
	}
	# loop through all image sizes
	$sizes=sql_query("select * from preview_size order by width desc");
	for ($n=0;$n<count($sizes);$n++)
		{
		$path=get_resource_path($ref,true,$sizes[$n]["id"],false,"jpg");

		$resource_type=sql_value("select resource_type value from resource where ref='$ref'","");
		if ((file_exists($path) || (!$onlyifexists)) && !checkperm("T" . $resource_type . "_" . $sizes[$n]["id"]))
			{
			if (($sizes[$n]["internal"]==0) || ($internal))
				{
				$returnline=array();
				$returnline["name"]=lang_or_i18n_get_translated($sizes[$n]["name"], "imagesize-");
				$returnline["allow_preview"]=$sizes[$n]["allow_preview"];

				# The ability to restrict download size by user group and resource type.
				if (checkperm("X" . $resource_type . "_" . $sizes[$n]["id"]))
					{
					# Permission set. Always restrict this download if this resource is restricted.
					$returnline["allow_restricted"]=false;
					}
				else
					{
					# Take the restriction from the settings for this download size.
					$returnline["allow_restricted"]=$sizes[$n]["allow_restricted"];
					}
				$returnline["path"]=$path;
				$returnline["id"]=$sizes[$n]["id"];
				if ((list($sw,$sh) = @getimagesize($path))===false) {$sw=0;$sh=0;}
				if (($filesize=@filesize_unlimited($path))===false) {$returnline["filesize"]="?";$returnline["filedown"]="?";}
				else {$returnline["filedown"]=ceil($filesize/50000) . " seconds @ broadband";$filesize=formatfilesize($filesize);}
				$returnline["filesize"]=$filesize;			
				$returnline["width"]=$sw;			
				$returnline["height"]=$sh;
				$returnline["extension"]='jpg';
				$return[]=$returnline;
				}
			}
		$lastname=lang_or_i18n_get_translated($sizes[$n]["name"], "imagesize-");
		$lastpreview=$sizes[$n]["allow_preview"];
		$lastrestricted=$sizes[$n]["allow_restricted"];
		}
	return $return;
	}

function trim_array($array,$trimchars='')
	{
	# removes whitespace from the beginning/end of all elements in an array
	
	for ($n=0;$n<count($array);$n++)
		{
		$array[$n]=trim($array[$n]);
		if (strlen($trimchars) > 0)
			{
			// also trim off extra characters they want gone
			$array[$n]=trim($array[$n],$trimchars);
			}
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
		$text=mb_substr($text,0,$length-3,'utf-8');
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
	
	# Translate all options
	$options=trim_array(explode(",",$options));
	for ($m=0;$m<count($options);$m++)
		{
		$options[$m]=i18n_get_translated($options[$m]);
		}

	global $auto_order_checkbox;
	if ($auto_order_checkbox) {sort($options);}
	
	return $options;
	}
	
function get_data_by_field($resource,$field)
	{
	# Return the resource data for field $field in resource $resource
	return sql_value("select value from resource_data where resource='$resource' and resource_type_field='$field'","");
	}
	
if (!function_exists("get_users")){		
function get_users($group=0,$find="",$order_by="u.username",$usepermissions=false,$fetchrows=-1)
{
    # Returns a user list. Group or search term is optional.
    # The standard user group names are translated using $lang. Custom user group names are i18n translated.

    $sql = "";
    if ($group>0) {$sql = "where usergroup='$group'";}
    if (strlen($find)>1)
      {
      if ($sql=="") {$sql = "where ";} else {$sql.= " and ";}
      $sql .= "(username like '%$find%' or fullname like '%$find%' or email like '%$find%')";
      }
    if (strlen($find)==1)
      {
      if ($sql=="") {$sql = "where ";} else {$sql.= " and ";}
      $sql .= "username like '$find%'";
      }
    if ($usepermissions && checkperm("U")) {
        # Only return users in children groups to the user's group
        global $usergroup;
        if ($sql=="") {$sql = "where ";} else {$sql.= " and ";}
        $sql.= "find_in_set('" . $usergroup . "',g.parent) ";
        $sql.= hook("getuseradditionalsql");
    }
    # Executes query.
    $r = sql_query("select u.*,g.name groupname,g.ref groupref,g.parent groupparent,u.approved,u.created from user u left outer join usergroup g on u.usergroup=g.ref $sql order by $order_by",false,$fetchrows);

    # Translates group names in the newly created array.
    for ($n = 0;$n<count($r);$n++) {
        if (!is_array($r[$n])) {break;} # The padded rows can't be and don't need to be translated.
        $r[$n]["groupname"] = lang_or_i18n_get_translated($r[$n]["groupname"], "usergroup-");
    }

    return $r;

}
}	

function get_users_with_permission($permission)
{
    # Returns all the users who have the permission $permission.
    # The standard user group names are translated using $lang. Custom user group names are i18n translated.	

    # First find all matching groups.
    $groups = sql_query("select ref,permissions from usergroup");
    $matched = array();
    for ($n = 0;$n<count($groups);$n++) {
        $perms = trim_array(explode(",",$groups[$n]["permissions"]));
        if (in_array($permission,$perms)) {$matched[] = $groups[$n]["ref"];}
    }
    # Executes query.
	$r = sql_query("select u.*,g.name groupname,g.ref groupref,g.parent groupparent from user u left outer join usergroup g on u.usergroup=g.ref where g.ref in ('" . join("','",$matched) . "') order by username",false);

    # Translates group names in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
        $r[$n]["groupname"] = lang_or_i18n_get_translated($r[$n]["groupname"], "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    return $return;
}

function get_user_by_email($email)
{
	$r = sql_query("select u.*,g.name groupname,g.ref groupref,g.parent groupparent from user u left outer join usergroup g on u.usergroup=g.ref where u.email like '%$email%' order by username",false);

    # Translates group names in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
        $r[$n]["groupname"] = lang_or_i18n_get_translated($r[$n]["groupname"], "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    return $return;
}

function get_usergroups($usepermissions=false,$find="")
{
    # Returns a list of user groups. The standard user groups are translated using $lang. Custom user groups are i18n translated.
    # Puts anything starting with 'General Staff Users' - in the English default names - at the top (e.g. General Staff).

    # Creates a query, taking (if required) the permissions  into account.
    $sql = "";
    if ($usepermissions && checkperm("U")) {
        # Only return users in children groups to the user's group
        global $usergroup,$U_perm_strict;
        if ($sql=="") {$sql = "where ";} else {$sql.= " and ";}
        if ($U_perm_strict) {
            //$sql.= "(parent='$usergroup')";
            $sql.= "find_in_set('" . $usergroup . "',parent)";
        }
        else {
            //$sql.= "(ref='$usergroup' or parent='$usergroup')";
            $sql.= "(ref='$usergroup' or find_in_set('" . $usergroup . "',parent))";
        }
    }

    # Executes query.
    global $default_group;
    $r = sql_query("select * from usergroup $sql order by (ref='$default_group') desc,name");

    # Translates group names in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"], "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    if (strlen($find)>0) {
        # Searches for groups with names which contains the string defined in $find.
        $initial_length = count($return);
        for ($n = 0;$n<$initial_length;$n++) {
            if (strpos(strtolower($return[$n]["name"]),strtolower($find))===false) {
                unset($return[$n]); # Removes this group.
            }
        }
        $return = array_values($return); # Reassigns the indices.
    }

    return $return;

}    

function get_usergroup($ref)
{
    # Returns the user group corresponding to the $ref. A standard user group name is translated using $lang. A custom user group name is i18n translated.

    $return = sql_query("select * from usergroup where ref='$ref'");
    if (count($return)==0) {return false;}
    else {
        $return[0]["name"] = lang_or_i18n_get_translated($return[0]["name"], "usergroup-");
        return $return[0];
    }
}

if (!function_exists("get_user")){
function get_user($ref)
	{
	# Return a user's credentials.
	$return=sql_query("select * from user where ref='$ref'");
	if (count($return)>0) {return $return[0];} else {return false;}
	}
}
	
if (!function_exists("save_user")){	
function save_user($ref)
	{
	global $lang;
		
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
		if (getval("suggest","")!="")
			{
			$password=make_password();
			}
		elseif ($password!=$lang["hidden"])	
			{
			$message=check_password($password);
			if ($message!==true) {return $message;}
			}
		
		$expires="'" . getvalescaped("account_expires","") . "'";
		if ($expires=="''") {$expires="null";}
		
		$passsql="";
		if ($password!=$lang["hidden"])	
			{
			# Save password.
			if (getval("suggest","")=="") {$password=md5("RS" . getvalescaped("username","") . $password);}
			$passsql=",password='" . $password . "',password_last_change=now()";
			}
			
		$additional_sql=hook("additionaluserfieldssave");
		
		sql_query("update user set username='" . trim(getvalescaped("username","")) . "'" . $passsql . ",fullname='" . getvalescaped("fullname","") . "',email='" . getvalescaped("email","") . "',usergroup='" . getvalescaped("usergroup","") . "',account_expires=$expires,ip_restrict='" . getvalescaped("ip_restrict","") . "',comments='" . getvalescaped("comments","") . "',approved='" . ((getval("approved","")=="")?"0":"1") . "' $additional_sql where ref='$ref'");
		}
		
	if (getval("emailme","")!="")
		{
		email_user_welcome(getval("email",""),getval("username",""),getval("password",""),getvalescaped("usergroup",""));
		}
	return true;
	}
}

function email_user_welcome($email,$username,$password,$usergroup)
	{
	global $applicationname,$email_from,$baseurl,$lang,$email_url_save_user;
	
	# Fetch any welcome message for this user group
	$welcome=sql_value("select welcome_message value from usergroup where ref='" . $usergroup . "'","");
	if (trim($welcome)!="") {$welcome.="\n\n";}
	
	$templatevars['welcome']=$welcome;
	$templatevars['username']=$username;
	$templatevars['password']=$password;
	if (trim($email_url_save_user)!=""){$templatevars['url']=$email_url_save_user;}
	else {$templatevars['url']=$baseurl;}
	
	$message=$templatevars['welcome'] . $lang["newlogindetails"] . "\n\n" . $lang["username"] . ": " . $templatevars['username'] . "\n" . $lang["password"] . ": " . $templatevars['password']."\n\n".$templatevars['url'];
	
	send_mail($email,$applicationname . ": " . $lang["youraccountdetails"],$message,"","","emaillogindetails",$templatevars);
	}


function email_reminder($email)
	{
	if ($email=="") {return false;}
	$details=sql_query("select username from user where email like '$email' and approved=1");
	if (count($details)==0) {return false;}
	$details=$details[0];
	global $applicationname,$email_from,$baseurl,$lang,$email_url_remind_user;
	$password=make_password();
	$password_hash=md5("RS" . $details["username"] . $password);
	
	sql_query("update user set password='$password_hash' where username='" . escape_check($details["username"]) . "'");
	
	$templatevars['username']=$details["username"];
	$templatevars['password']=$password;
    if (trim($email_url_remind_user)!=""){$templatevars['url']=$email_url_remind_user;}
    else {$templatevars['url']=$baseurl;}

	
	$message=$lang["newlogindetails"] . "\n\n" . $lang["username"] . ": " . $templatevars['username'] . "\n" . $lang["password"] . ": " . $templatevars['password'] . "\n\n". $templatevars['url'];
	send_mail($email,$applicationname . ": " . $lang["newpassword"],$message,"","","emailreminder",$templatevars);
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
	global $applicationname,$email_from,$user_email,$baseurl,$email_notify,$lang,$custom_registration_fields,$custom_registration_required;
	
	# Add custom fields
	$c="";
	if (isset($custom_registration_fields))
		{
		$custom=explode(",",$custom_registration_fields);
	
		# Required fields?
		if (isset($custom_registration_required)) {$required=explode(",",$custom_registration_required);}
	
		for ($n=0;$n<count($custom);$n++)
			{
			if (isset($required) && in_array($custom[$n],$required) && getval("custom" . $n,"")=="")
				{
				return false; # Required field was not set.
				}
			
			$c.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n,"") . "\n\n";
			}
		}

	# Required fields (name, email) not set?
	if (getval("name","")=="") {return false;}
	if (getval("email","")=="") {return false;}
	
	# Build a message
	$message=$lang["userrequestnotification1"] . "\n\n" . $lang["name"] . ": " . getval("name","") . "\n\n" . $lang["email"] . ": " . getval("email","") . "\n\n" . $lang["comment"] . ": " . getval("userrequestcomment","") . "\n\n" . $lang["ipaddress"] . ": '" . $_SERVER["REMOTE_ADDR"] . "'\n\n" . $c . "\n\n" . $lang["userrequestnotification2"] . "\n$baseurl";
	
	send_mail($email_notify,$applicationname . ": " . $lang["requestuserlogin"] . " - " . getval("name",""),$message,"",$user_email,"","",getval("name",""));

	return true;
	}

function get_active_users()
	{
	# Returns a list of active users, i.e. users still logged on with a last-active time within the last 2 hours.
	return sql_query("select username,round((unix_timestamp(now())-unix_timestamp(last_active))/60,0) t from user where logged_in=1 and unix_timestamp(now())-unix_timestamp(last_active)<(3600*2) order by t;");
	}

function get_all_site_text($findpage="",$findname="",$findtext="")
	{
	# Returns a list of all available editable site text (content).
	# If $find is specified a search is performed across page, name and text fields.
	global $defaultlanguage;	
	$findname=trim($findname);
	$findpage=trim($findpage);
	$findtext=trim($findtext);
	$sql="site_text s ";
	
	if ($findname!="" || $findpage!="" || $findtext!=""){
		$sql.=" where (";
	}
	
	
	if ($findname!="") {
		$findnamearray=explode(" ",$findname);
		for ($n=0;$n<count($findnamearray);$n++){
		  $sql.=' name like "%'.$findnamearray[$n].'%"';
		  if ($n+1!=count($findnamearray)){$sql.=" and ";}
		}
		
	}
	
	if ($findpage!="") {
		$findpagearray=explode(" ",$findpage);
		if ($findname!=""){$sql.=" and ";}
		for ($n=0;$n<count($findpagearray);$n++){
		  $sql.=' page like "%'.$findpagearray[$n].'%"';
		  if ($n+1!=count($findpagearray)){$sql.=" and ";}
		}
		
	}
	
	if ($findtext!="") {
		$findtextarray=explode(" ",$findtext);
		if ($findname!="" || $findpage!=""){$sql.=" and ";}
		for ($n=0;$n<count($findtextarray);$n++){
		  $sql.=' text like "%'.$findtextarray[$n].'%"';
		  if ($n+1!=count($findtextarray)){$sql.=" and ";}
		}
		
	}
	if ($findname!="" || $findpage!="" || $findtext!=""){
		$sql.=" ) ";
	}

	return sql_query ("select distinct s.page,s.name,(select text from site_text st where st.name=s.name and st.page=s.page order by (language='$defaultlanguage') desc limit 1) text from $sql order by (s.page='all') desc,s.page,name");
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

function check_site_text_custom($page,$name)
	{
	# Check if site text section is custom, i.e. deletable.
	
	$check=sql_query ("select custom from site_text where page='$page' and name='$name'");
	if (isset($check[0]["custom"])){return $check[0]["custom"];}
	}

function save_site_text($page,$name,$language,$group)
	{
	# Saves the submitted site text changes to the database.

	if ($group=="") {$g="null";$gc="is";} else {$g="'" . $group . "'";$gc="=";}
	
	global $custom,$newcustom;
	
	if($newcustom)
		{
		$test=sql_query("select * from site_text where page='$page' and name='$name'");
		if (count($test)>0){return true;}
		}
	if ($custom==""){$custom=0;}
	if (getval("deletecustom","")!="")
		{
		sql_query("delete from site_text where page='$page' and name='$name'");
		}
	elseif (getval("deleteme","")!="")
		{
		sql_query("delete from site_text where page='$page' and name='$name' and specific_to_group $gc $g");
		}
	elseif (getval("copyme","")!="")
		{
		sql_query("insert into site_text(page,name,text,language,specific_to_group,custom) values ('$page','$name','" . getvalescaped("text","") . "','$language',$g,'$custom')");
		}
	elseif (getval("newhelp","")!="")
		{
		global $newhelp;
		$check=sql_query("select * from site_text where page = 'help' and name='$newhelp'");
		if (!isset($check[0])){
			sql_query("insert into site_text(page,name,text,language,specific_to_group) values ('$page','$newhelp','','$language',$g)");
			}
		}	
	else
		{
		$text=sql_query ("select * from site_text where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
		if (count($text)==0)
			{
			# Insert a new row for this language/group.
			sql_query("insert into site_text(page,name,language,specific_to_group,text,custom) values ('$page','$name','$language',$g,'" . getvalescaped("text","") . "','$custom')");
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
	
	global $lang;
	if ($bytes<1024)
		{
		return number_format((double)$bytes) . "&nbsp;".$lang["byte-symbol"];
		}
	elseif ($bytes<pow(1024,2))
		{
		return number_format((double)ceil($bytes/1024)) . "&nbsp;".$lang["kilobyte-symbol"];
		}
	elseif ($bytes<pow(1024,3))
		{
		return number_format((double)$bytes/pow(1024,2),1) . "&nbsp;".$lang["megabyte-symbol"];
		}
	elseif ($bytes<pow(1024,4))
		{
		return number_format((double)$bytes/pow(1024,3),1) . "&nbsp;".$lang["gigabyte-symbol"];
		}
	else
		{
		return number_format((double)$bytes/pow(1024,4),1) . "&nbsp;".$lang["terabyte-symbol"];
		}
	}


function filesize2bytes($str) {
/**
 * Converts human readable file size (e.g. 10 MB, 200.20 GB) into bytes.
 *
 * @param string $str
 * @return int the result is in bytes
 * @author Svetoslav Marinov
 * @author http://slavi.biz
 */
    $bytes = 0;

    $bytes_array = array(
        'B' => 1,
        'kB' => 1024,
        'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024,
        'TB' => 1024 * 1024 * 1024 * 1024,
        'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
    );

    $bytes = floatval($str);

    if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && !empty($bytes_array[$matches[1]])) {
        $bytes *= $bytes_array[$matches[1]];
    }

    $bytes = intval(round($bytes, 2));
	
	#add leading zeroes (as this can be used to format filesize data in resource_data for sorting)
    return sprintf("%010d",$bytes);
} 


function change_password($password)
	{
	# Sets a new password for the current user.
	global $userref,$username,$lang,$userpassword;

	# Check password
	$message=check_password($password);
	if ($message!==true) {return $message;}

	# Generate new password hash
	$password_hash=md5("RS" . $username . $password);
	
	# Check password is not the same as the current
	if ($userpassword==$password_hash) {return $lang["password_matches_existing"];}
	
	sql_query("update user set password='$password_hash',password_last_change=now() where ref='$userref' limit 1");
	return true;
	}
	
function make_password()
	{
	# Generate a password using the configured settings.
	
	global $password_min_length, $password_min_alpha, $password_min_uppercase, $password_min_numeric, $password_min_special;

	$lowercase="abcdefghijklmnopqrstuvwxyz";
	$uppercase="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$alpha=$uppercase . $lowercase;
	$numeric="0123456789";
	$special="!@$%^&*().?";
	
	$password="";
	
	# Add alphanumerics
	for ($n=0;$n<$password_min_alpha;$n++)
		{
		$password.=substr($alpha,rand(0,strlen($alpha)-1),1);
		}
	
	# Add upper case
	for ($n=0;$n<$password_min_uppercase;$n++)
		{
		$password.=substr($uppercase,rand(0,strlen($uppercase)-1),1);
		}
	
	# Add numerics
	for ($n=0;$n<$password_min_numeric;$n++)
		{
		$password.=substr($numeric,rand(0,strlen($numeric)-1),1);
		}
	
	# Add special
	for ($n=0;$n<$password_min_special;$n++)
		{
		$password.=substr($special,rand(0,strlen($special)-1),1);
		}

	# Pad with lower case
	$padchars=$password_min_length-strlen($password);
	for ($n=0;$n<$padchars;$n++)
		{
		$password.=substr($lowercase,rand(0,strlen($lowercase)-1),1);
		}
		
	# Shuffle the password.
	$password=str_shuffle($password);
	
	# Check the password
	$check=check_password($password);
	if ($check!==true) {exit("Error: unable to automatically produce a password that met the criteria. Please check the password criteria in config.php. Generated password was '$password'. Error was: " . $check);}
	
    return $password;
	}


function bulk_mail($userlist,$subject,$text,$html=false)
    {
    global $email_from,$lang,$applicationname;
    
    # Attempt to resolve all users in the string $userlist to user references.
    if (trim($userlist)=="") {return ($lang["mustspecifyoneuser"]);}
    $userlist=resolve_userlist_groups($userlist);
    $ulist=trim_array(explode(",",$userlist));
    
    $emails=resolve_user_emails($ulist);
    $emails=$emails['emails'];
    
    $templatevars['text']=stripslashes(str_replace("\\r\\n","\n",$text));
    $body=$templatevars['text'];

    # Send an e-mail to each resolved user
    for ($n=0;$n<count($emails);$n++)
        {
        if ($emails[$n]!=""){
            send_mail($emails[$n],$subject,$body,$applicationname,$email_from,"emailbulk",$templatevars,$applicationname,"",$html);
            }
        }
        
    # Return an empty string (all OK).
    return "";
    }

function send_mail($email,$subject,$message,$from="",$reply_to="",$html_template="",$templatevars=null,$from_name="",$cc="",$html=false)
	{
	# Send a mail - but correctly encode the message/subject in quoted-printable UTF-8.
	
	# NOTE: $from is the name of the user sending the email,
	# while $from_name is the name that should be put in the header, which can be the system name
	# It is necessary to specify two since in all cases the email should be able to contain the user's name.
	
	# old mail function remains the same to avoid possible issues with phpmailer
	# send_mail_phpmailer allows for the use of text and html (multipart) emails,
	# and the use of email templates in Manage Content 
		
	# Send a mail - but correctly encode the message/subject in quoted-printable UTF-8.
	global $use_phpmailer;
	if ($use_phpmailer){
		send_mail_phpmailer($email,$subject,$message,$from,$reply_to,$html_template,$templatevars,$from_name,$cc,$html); 
		return true;
		}
	
	# No email address? Exit.
	if (trim($email)=="") {return false;}
	
	# Include footer
	global $email_footer;
	global $disable_quoted_printable_enc;
	
	# Work out correct EOL to use for mails (should use the system EOL).
	if (defined("PHP_EOL")) {$eol=PHP_EOL;} else {$eol="\r\n";}
	
	$message.=$eol.$eol.$eol . $email_footer;
	
	if ($disable_quoted_printable_enc==false){
	$message=rs_quoted_printable_encode($message);
	$subject=rs_quoted_printable_encode_subject($subject);
	}
	
	global $email_from;
	if ($from=="") {$from=$email_from;}
	if ($reply_to=="") {$reply_to=$email_from;}
	global $applicationname;
	if ($from_name==""){$from_name=$applicationname;}
	
	if (substr($reply_to,-1)==","){$reply_to=substr($reply_to,0,-1);}
	
	$reply_tos=explode(",",$reply_to);
	
	# Add headers
	$headers="";
	#$headers .= "X-Sender:  x-sender" . $eol;
   	$headers .= "From: ";
   	#allow multiple emails, and fix for long format emails
   	for ($n=0;$n<count($reply_tos);$n++){
		if ($n!=0){$headers.=",";}
		if (strstr($reply_tos[$n],"<")){ 
			$rtparts=explode("<",$reply_tos[$n]);
			$headers.=$rtparts[0]." <".$rtparts[1];
		}
		else {
			mb_internal_encoding("UTF-8");
			$headers.=mb_encode_mimeheader($from_name, "UTF-8") . " <".$reply_tos[$n].">";
		}
 	}
 	$headers.=$eol;
 	$headers .= "Reply-To: $reply_to" . $eol;
 	
	if ($cc!=""){
		global $userfullname;
		#allow multiple emails, and fix for long format emails
		$ccs=explode(",",$cc);
		$headers .= "Cc: ";
		for ($n=0;$n<count($ccs);$n++){
			if ($n!=0){$headers.=",";}
			if (strstr($ccs[$n],"<")){ 
				$ccparts=explode("<",$ccs[$n]);
				$headers.=$ccparts[0]." <".$ccparts[1];
			}
			else {
				mb_internal_encoding("UTF-8");
				$headers.=mb_encode_mimeheader($userfullname, "UTF-8"). " <".$ccs[$n].">";
			}
		}
		$headers.=$eol;
	}
	
	$headers .= "Date: " . date("r") .  $eol;
   	$headers .= "Message-ID: <" . date("YmdHis") . $from . ">" . $eol;
   	#$headers .= "Return-Path: returnpath" . $eol;
   	//$headers .= "Delivered-to: $email" . $eol;
   	$headers .= "MIME-Version: 1.0" . $eol;
   	$headers .= "X-Mailer: PHP Mail Function" . $eol;
   	if (!$html)
   		{
		$headers .= "Content-Type: text/plain; charset=\"UTF-8\"" . $eol;
		}
	else
		{
		$headers .= "Content-Type: text/html; charset=\"UTF-8\"" . $eol;
		}
	$headers .= "Content-Transfer-Encoding: quoted-printable" . $eol;
	mail ($email,$subject,$message,$headers);
	}

if (!function_exists("send_mail_phpmailer")){
function send_mail_phpmailer($email,$subject,$message="",$from="",$reply_to="",$html_template="",$templatevars=null,$from_name="",$cc="",$html=false)
	{
	
	# if ($use_phpmailer==true) this function is used instead.
	# Mail templates can include lang, server, site_text, and POST variables by default
	# ex ( [lang_mycollections], [server_REMOTE_ADDR], [text_footer] , [message]
	
	# additional values must be made available through $templatevars
	# For example, a complex url or image path that may be sent in an 
	# email should be added to the templatevars array and passed into send_mail.
	# available templatevars need to be well-documented, and sample templates
	# need to be available.

	# Include footer
	global $email_footer,$storagedir;
	$phpversion=phpversion();
	if ($phpversion>='5.3') {
	if (file_exists($storagedir."/../lib/phpmailer_v5_1/class.phpmailer.php")){
		include_once($storagedir."/../lib/phpmailer_v5_1/class.phpmailer.php");
		include_once($storagedir."/../lib/phpmailer/class.html2text.php");
		}
	} else {
	// less than 5.3
	if (file_exists($storagedir."/../lib/phpmailer/class.phpmailer.php")){
		include_once($storagedir."/../lib/phpmailer/class.phpmailer.php");
		include_once($storagedir."/../lib/phpmailer/class.html2text.php");
		}
	}
		
	global $email_from;
	if ($from=="") {$from=$email_from;}
	if ($reply_to=="") {$reply_to=$email_from;}
	global $applicationname;
	if ($from_name==""){$from_name=$applicationname;}
	
	#check for html template. If exists, attempt to include vars into message
	if ($html_template!="")
		{
		# Attempt to verify users by email, which allows us to get the email template by lang and usergroup
		$to_usergroup=sql_query("select lang,usergroup from user where email ='$email'","");

		if (count($to_usergroup)!=0)
			{
			$to_usergroupref=$to_usergroup[0]['usergroup'];
			$to_usergrouplang=$to_usergroup[0]['lang'];
			}
		else 
			{
			$to_usergrouplang="";	
			}
			
		if ($to_usergrouplang==""){global $defaultlanguage; $to_usergrouplang=$defaultlanguage;}
			
		if (isset($to_usergroupref))
			{	
			$modified_to_usergroupref=hook("modifytousergroup","",$to_usergroupref);
			if ($modified_to_usergroupref!==null){$to_usergroupref=$modified_to_usergroupref;}

			$results=sql_query("select language,name,text from site_text where page='all' and name='$html_template' and specific_to_group='$to_usergroupref'");
			}
		else 
			{	
			$results=sql_query("select language,name,text from site_text where page='all' and name='$html_template' and specific_to_group is null");
			}
			
		global $site_text;
		for ($n=0;$n<count($results);$n++) {$site_text[$results[$n]["language"] . "-" . $results[$n]["name"]]=$results[$n]["text"];} 
				
		$language=$to_usergrouplang;


		if (array_key_exists($language . "-" . $html_template,$site_text)) 
			{
			$template=$site_text[$language . "-" .$html_template];
			} 
		else 
			{
			global $languages;

			# Can't find the language key? Look for it in other languages.
			reset($languages);
			foreach ($languages as $key=>$value)
				{
				if (array_key_exists($key . "-" . $html_template,$site_text)) {$template= $site_text[$key . "-" . $html_template];break;} 		
				}
			}	
			


		if (isset($template) && $template!="")
			{
			preg_match_all('/\[[^\]]*\]/',$template,$test);
			foreach($test[0] as $variable)
				{
			
				$variable=str_replace("[","",$variable);
				$variable=str_replace("]","",$variable);
			
				
				# get lang variables (ex. [lang_mycollections])
				if (substr($variable,0,5)=="lang_"){
					global $lang;
					$$variable=$lang[substr($variable,5)];
				}
				
				# get server variables (ex. [server_REMOTE_ADDR] for a user request)
				else if (substr($variable,0,7)=="server_"){
					$$variable=$_SERVER[substr($variable,7)];
				}
				
				# [embed_thumbnail] (requires url in templatevars['thumbnail'])
				else if (substr($variable,0,15)=="embed_thumbnail"){
					$thumbcid=uniqid('thumb');
					$$variable="<img style='border:1px solid #d1d1d1;' src='cid:$thumbcid' />";
				}
				
				# embed images (find them in relation to storagedir so that templates are portable)...  (ex [img_storagedir_/../gfx/whitegry/titles/title.gif])
				else if (substr($variable,0,15)=="img_storagedir_"){
					$$variable="<img src='cid:".basename(substr($variable,15))."'/>";
					$images[]=$storagedir.substr($variable,15);
				}
				
				# embed images (ex [img_/var/www/resourcespace/gfx/whitegry/titles/title.gif])
				else if (substr($variable,0,4)=="img_"){
					$$variable="<img src='cid:".basename(substr($variable,4))."'/>";
					$images[]=substr($variable,4);
				}
				
				# attach files (ex [attach_/var/www/resourcespace/gfx/whitegry/titles/title.gif])
				else if (substr($variable,0,7)=="attach_"){
					$$variable="";
					$attachments[]=substr($variable,7);
				}
				
				# get site text variables (ex. [text_footer], for example to 
				# manage html snippets that you want available in all emails.)
				else if (substr($variable,0,5)=="text_"){
					$$variable=text(substr($variable,5));
				}

				# try to get the variable from POST
				else{
					$$variable=getval($variable,"");
				}
				
				# avoid resetting templatevars that may have been passed here
				if (!isset($templatevars[$variable])){$templatevars[$variable]=$$variable;}
				}

			if (isset($templatevars))
				{
				foreach($templatevars as $key=>$value)
					{
					$template=str_replace("[" . $key . "]",nl2br($value),$template);
					}
				}
			$body=$template;	
			}
		}		

	if (!isset($body)){$body=$message;}

	global $use_smtp,$smtp_secure,$smtp_host,$smtp_port,$smtp_auth,$smtp_username,$smtp_password;
	$mail = new PHPMailer();
	// use an external SMTP server? (e.g. Gmail)
	if ($use_smtp) {
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = $smtp_auth;  // authentication enabled/disabled
		$mail->SMTPSecure = $smtp_secure; // '', 'tls' or 'ssl'
		$mail->Host = $smtp_host; // hostname
		$mail->Port = $smtp_port; // port number
		$mail->Username = $smtp_username; // username
		$mail->Password = $smtp_password; // password
	}
	$reply_tos=explode(",",$reply_to);
	// only one from address is possible, so only use the first one:
	if (strstr($reply_tos[0],"<")){
		$rtparts=explode("<",$reply_tos[0]);
		$mail->From = str_replace(">","",$rtparts[1]);
		$mail->FromName = $rtparts[0];
	}
	else {
		$mail->From = $reply_tos[0];
		$mail->FromName = $from_name;
	}
	
	// if there are multiple addresses, that's what replyto handles.
	for ($n=0;$n<count($reply_tos);$n++){
		if (strstr($reply_tos[$n],"<")){
			$rtparts=explode("<",$reply_tos[$n]);
			$mail->AddReplyto(str_replace(">","",$rtparts[1]),$rtparts[0]);
		}
		else {
			$mail->AddReplyto($reply_tos[$n],$from_name);
		}
	}
	
	# modification to handle multiple comma delimited emails
	# such as for a multiple $email_notify
	$emails = $email;
	$emails = explode(',', $emails);
	$emails = array_map('trim', $emails);
	foreach ($emails as $email){
		if (strstr($email,"<")){
			$emparts=explode("<",$email);
			$mail->AddAddress(str_replace(">","",$emparts[1]),$emparts[0]);
		}
		else {
			$mail->AddAddress($email);
		}
	}
	
	if ($cc!=""){
		# modification for multiple is also necessary here, though a broken cc seems to be simply removed by phpmailer rather than breaking it.
		$ccs = $cc;
		$ccs = explode(',', $ccs);
		$ccs = array_map('trim', $ccs);
		global $userfullname;
		foreach ($ccs as $cc){
			if (strstr($cc,"<")){
				$ccparts=explode("<",$cc);
				$mail->AddCC(str_replace(">","",$ccparts[1]),$ccparts[0]);
			}
			else{
				$mail->AddCC($cc,$userfullname);
			}
		}
	}
	$mail->CharSet = "utf-8"; 
	
	if ($html_template!="" || $html) {$mail->IsHTML(true);}  	
	else {$mail->IsHTML(false);}
	
	$mail->Subject = $subject;
	$mail->Body    = $body;
	
	if (isset($embed_thumbnail)&&isset($templatevars['thumbnail'])){
		$mail->AddEmbeddedImage($templatevars['thumbnail'],$thumbcid,$thumbcid,'base64','image/jpeg'); 
		}
	if (isset($images)){
		foreach ($images as $image){	
		$mail->AddEmbeddedImage($image,basename($image),basename($image),'base64','image/gif');}
	}	
	if (isset($attachments)){
		foreach ($attachments as $attachment){
		$mail->AddAttachment($attachment,basename($attachment));}
	}	
	if ($html_template!=""){
		$h2t = new html2text($body); 
		$text = $h2t->get_text(); 
		$mail->AltBody = $text; 
		}	 
	if(!$mail->Send())
		{
		echo "Message could not be sent. <p>";
		echo "Mailer Error: " . $mail->ErrorInfo;
		exit;
		}
	hook("aftersendmailphpmailer","",$email);	
}
}

function rs_quoted_printable_encode($string, $linelen = 0, $linebreak="=\r\n", $breaklen = 0, $encodecrlf = false) {
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


function rs_quoted_printable_encode_subject($string, $encoding='UTF-8')
	{
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

if (!function_exists("highlightkeywords")){
function highlightkeywords($text,$search,$partial_index=false,$field_name="",$keywords_index=1)
	{
	# do not hightlight if the field is not indexed, so it is clearer where results came from.	
	if ($keywords_index!=1){return $text;}	
	# Highlight searched keywords in $text
	# Optional - depends on $highlightkeywords being set in config.php.
	global $highlightkeywords;
	# Situations where we do not need to do this.
	if (!isset($highlightkeywords) || ($highlightkeywords==false) || ($search=="") || ($text=="")) {return $text;}

		# Generate the cache of search keywords (no longer global so it can test against particular fields.
		# a search is a small array so I don't think there is much to lose by processing it.
		$hlkeycache=array();
		$s=split_keywords($search);
		for ($n=0;$n<count($s);$n++)
			{
			if (strpos($s[$n],":")!==false) {
				$c=explode(":",$s[$n]);
				# only add field specific keywords
				if($field_name!="" && $c[0]==$field_name){
					$hlkeycache[]=$c[1];			
				}	
				
			}
			# else add general keywords
			else {
				$hlkeycache[]=$s[$n];	
			}	

		}
		
	# Parse and replace.
	if ($partial_index)
		{
		return str_highlight ($text,$hlkeycache,STR_HIGHLIGHT_SIMPLE);
		}
	else
		{
		return str_highlight ($text,$hlkeycache,STR_HIGHLIGHT_WHOLEWD);
		}
	}
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

	$text=str_replace("_",".{us}.",$text);// underscores are considered part of words, so temporarily replace them for better \b search.
    $text=str_replace("#zwspace;",".{zw}.",$text);
    
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

    usort($needle, "sorthighlights");

    foreach ($needle as $needle_s) {
        $needle_s = preg_quote($needle_s);
        $needle_s = str_replace("#","\\#",$needle_s);
 
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
	$text=str_replace(".{us}.","_",$text);
	$text=str_replace(".{zw}.","#zwspace;",$text);
    return $text;
	}

function sorthighlights($a, $b)
    {
    # fixes an odd problem for str_highlight related to the order of keywords
    if (strlen($a) < strlen($b)) {
        return 0;
        }
    return ($a < $b) ? -1 : 1;
    }

function pager($break=true)
	{
	global $curpage,$url,$totalpages,$offset,$per_page,$lang,$jumpcount,$pager_dropdown;
	$jumpcount++;global $pagename;
    if ($totalpages!=0 && $totalpages!=1){?>     
        <span class="HorizontalWhiteNav"><?php if ($break) { ?>&nbsp;<br /><?php } ?><?php if ($curpage>1) { ?><a href="<?php echo $url?>&offset=<?php echo $offset-$per_page?>"><?php } ?>&lt;&nbsp;<?php echo $lang["previous"]?><?php if ($curpage>1) { ?></a><?php } ?>&nbsp;|

        <?php if ($pager_dropdown){
            $id=rand();?>
            <select id="pager<?php echo $id;?>" class="ListDropdown" style="width:50px;" onChange="var jumpto=document.getElementById('pager<?php echo $id?>').value;if ((jumpto>0) && (jumpto<=<?php echo $totalpages?>)) {document.location='<?php echo $url?>&offset=' + ((jumpto-1) * <?php echo $per_page?>);}">
            <?php for ($n=1;$n<$totalpages+1;$n++){?>
                <option value='<?php echo $n?>' <?php if ($n==$curpage){?>selected<?php } ?>><?php echo $n?></option>
            <?php } ?>
            </select>
        <?php } else { ?>
            <a href="#" title="Jump to page" onClick="p=document.getElementById('jumppanel<?php echo $jumpcount?>');if (p.style.display!='block') {p.style.display='block';document.getElementById('jumpto<?php echo $jumpcount?>').focus();} else {p.style.display='none';}; return false;"><?php echo $lang["page"]?>&nbsp;<?php echo $curpage?>&nbsp;<?php echo $lang["of"]?>&nbsp;<?php echo $totalpages?></a>
        <?php } ?>

        |&nbsp;<?php if ($curpage<$totalpages) { ?><a href="<?php echo $url?>&offset=<?php echo $offset+$per_page?>"><?php } ?><?php echo $lang["next"]?>&nbsp;&gt;<?php if ($curpage<$totalpages) { ?></a><?php } ?>
        </span>
        <?php if (!$pager_dropdown){?>
            <div id="jumppanel<?php echo $jumpcount?>" style="display:none;margin-top:5px;"><?php echo $lang["jumptopage"]?>: <input type="text" size="3" id="jumpto<?php echo $jumpcount?>">&nbsp;<input type="submit" name="jump" value="<?php echo $lang["jump"]?>" onClick="var jumpto=document.getElementById('jumpto<?php echo $jumpcount?>').value;if ((jumpto>0) && (jumpto<=<?php echo $totalpages?>)) {document.location='<?php echo $url?>&offset=' + ((jumpto-1) * <?php echo $per_page?>);}"></div>
        <?php } ?>
    <?php } else { ?><span class="HorizontalWhiteNav">&nbsp;</span><div <?php if ($pagename=="search"){?>style="display:block;"<?php } else { ?>style="display:inline;"<?php }?>>&nbsp;</div><?php } ?>
   	<?php
	}
	
function get_all_image_sizes($internal=false,$restricted=false)
{
    # Returns all image sizes available.
    # Standard image sizes are translated using $lang.  Custom image sizes are i18n translated.

    # Executes query.
    $r = sql_query("select * from preview_size " . (($internal)?"":"where internal!=1") . (($restricted)?" and allow_restricted=1":"") . " order by width asc");

    # Translates image sizes in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"], "imagesize-");
        $return[] = $r[$n];
    }
    return $return;

}
	
function image_size_restricted_access($id)
	{
	# Returns true if the indicated size is allowed for a restricted user.
	return sql_value("select allow_restricted value from preview_size where id='$id'",false);
	}
	
function get_user_log($user, $fetchrows=-1)
	{
    # Returns a user action log for $user.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.

    # Executes query.
    $r = sql_query("select r.ref resourceid,r.title resourcetitle,l.date,l.type,f.title,l.purchase_size,l.purchase_price, l.notes from resource_log l left outer join resource r on l.resource=r.ref left outer join resource_type_field f on f.ref=l.resource_type_field where l.user='$user' order by l.date",false,$fetchrows);

    # Translates field titles in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
		if (is_array($r[$n])) {$r[$n]["title"] = lang_or_i18n_get_translated($r[$n]["title"], "fieldtitle-");}
        $return[] = $r[$n];
    }
    return $return;
	}

function get_breadcrumbs()
	{
	# Returns a HTML breadcrumb trail for display at the top of the screen.

	$breadcrumbs=getvalescaped("rs_breadcrumbs","");
	$bs=explode(",",$breadcrumbs);
	
	global $pagename,$lang,$title;
	$bc="";
	
	
	# Collapse any appropriate levels of the breadcrumbs trail.
	# Certain pages are deemed 'start pages' and reset the breadcrumbs tree.
	if (in_array($pagename,array("search_advanced","collection_manage","themes","team_home"))) {$bs=array();}

	# Drop any existing mentions of this page in the tree.
	$nbs=array();
	for ($n=0;$n<count($bs);$n++)
		{
		$s=explode(":",$bs[$n]);
		if ($s[0]!=$pagename) {$nbs[]=$bs[$n];}
		}
	$bs=$nbs;
	
	# Add the current page to the breadcrumbs
	$bs[]=$pagename. ":" . $_SERVER["QUERY_STRING"];
	

	# Set the breadcrumbs cookie.
	$breadcrumbs=join(",",$bs);
	setcookie("rs_breadcrumbs",$breadcrumbs);
	
	return "You are here: " . $breadcrumbs;
	}
	
function resolve_userlist_groups($userlist)
	{
	# Given a comma separated user list (from the user select include file) turn all Group: entries into fully resolved list of usernames.
	global $lang;
	
	$ulist=explode(",",$userlist);
	$newlist="";
	for ($n=0;$n<count($ulist);$n++)
		{
		$u=trim($ulist[$n]);
		if (strpos($u,$lang["group"] . ": ")===0)
			{
			# Group entry, resolve
			$u=trim(substr($u,strlen($lang["group"] . ": ")));
			$users=sql_array("select u.username value from user u join usergroup g on u.usergroup=g.ref where g.name='$u'");
			if ($newlist!="") {$newlist.=",";}
			$newlist.=join(",",$users);
			}
		else
			{
			# Username, just add as-is
			if ($newlist!="") {$newlist.=",";}
			$newlist.=$u;
			}
		}
	return $newlist;
	}
	
function get_suggested_keywords($search,$ref="")
	{
	# For the given partial word, suggest complete existing keywords.
	global $autocomplete_search_items;
	if ($ref==""){
		return sql_array("select keyword value from keyword where keyword like '" . escape_check($search) . "%' and hit_count > 0 order by hit_count desc limit $autocomplete_search_items");
		}
	else 
		{
		return sql_array("select distinct k.keyword value,rk.resource_type_field from keyword k,resource_keyword rk where k.ref=rk.keyword and k.keyword like '" . escape_check($search) . "%' and rk.resource_type_field='".$ref."' and k.hit_count > 0 order by k.hit_count desc limit $autocomplete_search_items");
		}
	}
	
function check_password($password)
	{
	# Checks that a password conforms to the configured paramaters.
	# Returns true if it does, or a descriptive string if it doesn't.
	global $lang, $password_min_length, $password_min_alpha, $password_min_uppercase, $password_min_numeric, $password_min_special;

	if (strlen($password)<$password_min_length) {return str_replace("?",$password_min_length,$lang["password_not_min_length"]);}

	$uppercase="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$alpha=$uppercase . "abcdefghijklmnopqrstuvwxyz";
	$numeric="0123456789";
	
	$a=0;$u=0;$n=0;$s=0;
	for ($m=0;$m<strlen($password);$m++)
		{
		$l=substr($password,$m,1);
		if (strpos($uppercase,$l)!==false) {$u++;}

		if (strpos($alpha,$l)!==false) {$a++;}
		elseif (strpos($numeric,$l)!==false) {$n++;}
		else {$s++;} # Not alpha/numeric, must be a special char.
		}
	
	if ($a<$password_min_alpha) {return str_replace("?",$password_min_alpha,$lang["password_not_min_alpha"]);}
	if ($u<$password_min_uppercase) {return str_replace("?",$password_min_uppercase,$lang["password_not_min_uppercase"]);}
	if ($n<$password_min_numeric) {return str_replace("?",$password_min_numeric,$lang["password_not_min_numeric"]);}
	if ($s<$password_min_special) {return str_replace("?",$password_min_special,$lang["password_not_min_special"]);}
	
	
	return true;
	}

function get_related_keywords($keyref)
	{
	# For a given keyword reference returns the related keywords
	# Also reverses the process, returning keywords for matching related words
	# and for matching related words, also returns other words related to the same keyword.
	global $keyword_relationships_one_way;
	if ($keyword_relationships_one_way){
		return sql_array(" select related value from keyword_related where keyword='$keyref'");
		}
	else {
		return sql_array(" select keyword value from keyword_related where related='$keyref' union select related value from keyword_related where (keyword='$keyref' or keyword in (select keyword value from keyword_related where related='$keyref')) and related<>'$keyref'");
		}
	}
	
	
function get_grouped_related_keywords($find="",$specific="")
	{
	# Returns each keyword and the related keywords grouped, along with the resolved keywords strings.
	$sql="";
	if ($find!="") {$sql="where k1.keyword='" . escape_check($find) . "' or k2.keyword='" . escape_check($find) . "'";}
	if ($specific!="") {$sql="where k1.keyword='" . escape_check($specific) . "'";}
	
	return sql_query("
		select k1.keyword,group_concat(k2.keyword order by k2.keyword separator ', ') related from keyword_related kr
			join keyword k1 on kr.keyword=k1.ref
			join keyword k2 on kr.related=k2.ref
		$sql
		group by k1.keyword order by k1.keyword
		");
	}

function save_related_keywords($keyword,$related)
	{
	$keyref=resolve_keyword($keyword,true);
	$s=trim_array(explode(",",$related));

	# Blank existing relationships.
	sql_query("delete from keyword_related where keyword='$keyref'");
	if (trim($related)!="")
		{
		for ($n=0;$n<count($s);$n++)
			{
			sql_query("insert into keyword_related (keyword,related) values ('$keyref','" . resolve_keyword($s[$n],true) . "')");
			}
		}
	return true;
	}

function send_statistics()
	{
	# If configured, send two metrics to Montala.
	$last_sent=sql_value("select value from sysvars where name='last_sent_stats'","");
	
	# No need to send stats if already sent in last week.
	if ($last_sent!="" && time()-strtotime($last_sent)<(60*60*24*7)) {return false;}
	
	# Gather stats
	$total_users=sql_value("select count(*) value from user",0);
	$total_resources=sql_value("select count(*) value from resource",0);
	
	# Send stats
	@file("http://www.montala.net/rs_stats.php?users=" . $total_users . "&resources=" . $total_resources);
	
	# Update last sent date/time.
	sql_query("delete from sysvars where name='last_sent_stats'");
	sql_query("insert into sysvars(name,value) values ('last_sent_stats',now())");
	}

function resolve_users($users)
	{
	# For a given comma-separated list of user refs (e.g. returned from a group_concat()), return a string of matching usernames.
	if (trim($users)=="") {return "";}
	$resolved=sql_array("select concat(fullname,' (',username,')') value from user where ref in ($users)");
	return join(", ",$resolved);
	}

function get_simple_search_fields()
{
    # Returns a list of fields suitable for the simple search box.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.

    $sql = "";

    # Include the country field even if not selected?
    # This is to provide compatibility for older systems on which the simple search box was not configurable
    # and had a simpler 'country search' option.
    global $country_search;
    if (isset($country_search) && $country_search) {$sql=" or ref=3";}

    # Executes query.
    $fields = sql_query("select ref, name, title, type, options, order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown, external_user_access, autocomplete_macro, hide_when_uploading, hide_when_restricted, value_filter, exiftool_filter, omit_when_copying, tooltip_text from resource_type_field where (simple_search=1 $sql) and keywords_index=1 order by resource_type,order_by");

    # Applies field permissions and translates field titles in the newly created array.
    $return = array();
    for ($n = 0;$n<count($fields);$n++) {
        if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"]))
        && !checkperm("f-" . $fields[$n]["ref"])) {
            $fields[$n]["title"] = lang_or_i18n_get_translated($fields[$n]["title"], "fieldtitle-");            
            $return[] = $fields[$n];
        }
    }
    return $return;
}
	

function check_access_key($resource,$key)
	{
	# Verify a supplied external access key
	
	# Option to plugin in some extra functionality to check keys
	if (hook("check_access_key","",array($resource,$key))===true) {return true;}
	
	$keys=sql_query("select user,expires from external_access_keys where resource='$resource' and access_key='$key' and (expires is null or expires>now())");
	if (count($keys)==0)
		{
		return false;
		}
	else
		{
		# "Emulate" the user that e-mailed the resource by setting the same group and permissions
		
		$user=$keys[0]["user"];
		$expires=$keys[0]["expires"];
		
		# Has this expired?
		if ($expires!="" && strtotime($expires)<time())
			{
			global $lang;
			?>
			<script type="text/javascript">
			alert("<?php echo $lang["externalshareexpired"] ?>");
			history.go(-1);
			</script>
			<?php
			exit();
			}
		
		global $usergroup,$userpermissions,$userrequestmode,$userfixedtheme;
		$userinfo=sql_query("select u.usergroup,g.permissions,g.fixed_theme from user u join usergroup g on u.usergroup=g.ref where u.ref='$user'");
		if (count($userinfo)>0)
			{
			$usergroup=$userinfo[0]["usergroup"];
			$userpermissions=explode(",",$userinfo[0]["permissions"]);
			if (trim($userinfo[0]["fixed_theme"])!="") {$userfixedtheme=$userinfo[0]["fixed_theme"];} # Apply fixed theme also

			if (hook("modifyuserpermissions")){$userpermissions=hook("modifyuserpermissions");}
			$userrequestmode=0; # Always use 'email' request mode for external users
			}
		
		# Special case for anonymous logins.
		# When a valid key is present, we need to log the user in as the anonymous user so they will be able to browse the public links.
		global $anonymous_login;
		if (isset($anonymous_login))
			{
			global $username;
			$username=$anonymous_login;
			}
		
		# Set the 'last used' date for this key
		sql_query("update external_access_keys set lastused=now() where resource='$resource' and access_key='$key'");
		
		return true;
		}
	}

function check_access_key_collection($collection,$key)
	{
	if ($collection=="" || !is_numeric($collection)) {return false;}
	$r=get_collection_resources($collection);
	for ($n=0;$n<count($r);$n++)
		{
		# Verify a supplied external access key for all resources in a collection
		if (!check_access_key($r[$n],$key)) {return false;}
		}	

	# Set the 'last used' date for this key
	sql_query("update external_access_keys set lastused=now() where collection='$collection' and access_key='$key'");
	return true;
	}
	
if (!function_exists("auto_create_user_account")){
function auto_create_user_account()
	{
	# Automatically creates a user account (which requires approval unless $auto_approve_accounts is true).
	global $applicationname,$user_email,$email_from,$baseurl,$email_notify,$lang,$custom_registration_fields,$custom_registration_required,$user_account_auto_creation_usergroup,$registration_group_select,$auto_approve_accounts,$auto_approve_domains;
	
	# Add custom fields
	$c="";
	if (isset($custom_registration_fields))
		{
		$custom=explode(",",$custom_registration_fields);
	
		# Required fields?
		if (isset($custom_registration_required)) {$required=explode(",",$custom_registration_required);}
	
		for ($n=0;$n<count($custom);$n++)
			{
			if (isset($required) && in_array($custom[$n],$required) && getval("custom" . $n,"")=="")
				{
				return false; # Required field was not set.
				}
			
			$c.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n,"") . "\n\n";
			}
		}

	# Required fields (name, email) not set?
	if (getval("name","")=="") {return $lang['requiredfields'];}
	if (getval("email","")=="") {return $lang['requiredfields'];}
	
	# Work out which user group to set. Allow a hook to change this, if necessary.
	$altgroup=hook("auto_approve_account_switch_group");
	if ($altgroup!==false)
		{
		$usergroup=$altgroup;
		}
	else
		{
		$usergroup=$user_account_auto_creation_usergroup;
		}
			
	if ($registration_group_select)
		{
		$usergroup=getvalescaped("usergroup","",true);
		# Check this is a valid selectable usergroup (should always be valid unless this is a hack attempt)
		if (sql_value("select allow_registration_selection value from usergroup where ref='$usergroup'",0)!=1) {exit("Invalid user group selection");}
		}
	
	$username=escape_check(make_username(getval("name","")));
	
	#check if account already exists
	$check=sql_value("select email value from user where email = '$user_email'","");
	if ($check!=""){return $lang["useremailalreadyexists"];}

	# Prepare to create the user.
	$email=trim(getvalescaped("email","")) ;
	$password=make_password();

	# Work out if we should automatically approve this account based on $auto_approve_accounts or $auto_approve_domains
	$approve=false;
	if ($auto_approve_accounts==true)
		{
		$approve=true;
		}
	elseif (count($auto_approve_domains)>0)
		{
		# Check e-mail domain.
		foreach ($auto_approve_domains as $domain)
			{
			if (substr(strtolower($email),strlen($email)-strlen($domain)-1)==("@" . strtolower($domain)))
				{
				# E-mail domain match.
				$approve=true;
				}
			}
		}
	

	# Create the user
	sql_query("insert into user (username,password,fullname,email,usergroup,comments,approved) values ('" . $username . "','" . $password . "','" . getvalescaped("name","") . "','" . $email . "','" . $usergroup . "','" . escape_check($c) . "'," . (($approve)?1:0) . ")");
	$new=sql_insert_id();

	if ($approve)
		{
		# Auto approving, send mail direct to user
		email_user_welcome($email,$username,$password,$usergroup);
		}
	else
		{
		# Not auto approving.
		# Build a message to send to an admin notifying of unapproved user
		$message=$lang["userrequestnotification1"] . "\n\n" . $lang["name"] . ": " . getval("name","") . "\n\n" . $lang["email"] . ": " . getval("email","") . "\n\n" . $lang["comment"] . ": " . getval("userrequestcomment","") . "\n\n" . $lang["ipaddress"] . ": '" . $_SERVER["REMOTE_ADDR"] . "'\n\n" . $c . "\n\n" . $lang["userrequestnotification3"] . "\n$baseurl?u=" . $new;
		
		
		send_mail($email_notify,$applicationname . ": " . $lang["requestuserlogin"] . " - " . getval("name",""),$message,"",$user_email,"","",getval("name",""));
		}
		
	return true;
	}
} //end function replace hook	

function make_username($name)
	{
	# Generates a unique username for the given name
	
	# First compress the various name parts
	$s=trim_array(explode(" ",$name));
	
	$name=$s[count($s)-1];
	for ($n=count($s)-2;$n>=0;$n--)
		{
		$name=substr($s[$n],0,1) . $name;
		}
	$name=safe_file_name(strtolower($name));
	
	# Check for uniqueness... append an ever-increasing number until unique.
	$unique=false;
	$num=-1;
	while (!$unique)
		{
		$num++;
		$c=sql_value("select count(*) value from user where username='" . escape_check($name . (($num==0)?"":$num)) . "'",0);
		$unique=($c==0);
		}
	return $name . (($num==0)?"":$num);
	}
	
function get_registration_selectable_usergroups()
{
    # Returns a list of  user groups selectable in the registration . The standard user groups are translated using $lang. Custom user groups are i18n translated.

    # Executes query.
    $r = sql_query("select ref,name from usergroup where allow_registration_selection=1 order by name");

    # Translates group names in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"], "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    return $return;

}

function remove_extension($strName)
{
$ext = strrchr($strName, '.');
if($ext !== false)
{
$strName = substr($strName, 0, -strlen($ext));
}
return $strName;
}

function get_fields($field_refs)
	{
	# Returns a list of fields with refs matching the supplied field refs.
	if (!is_array($field_refs)) {print_r($field_refs);exit(" passed to getfields() is not an array. ");}
	$return=array();
	$fields=sql_query("select ref, name, title, type, options ,order_by, keywords_index, partial_index, resource_type, resource_column, display_field, use_for_similar, iptc_equiv, display_template, tab_name, required, smart_theme_name, exiftool_field, advanced_search, simple_search, help_text, display_as_dropdown from resource_type_field where  keywords_index=1 and length(name)>0 and ref in ('" . join("','",$field_refs) . "') order by order_by");
	# Apply field permissions
	for ($n=0;$n<count($fields);$n++)
		{
		if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"]))
		&& !checkperm("f-" . $fields[$n]["ref"]))
		{$return[]=$fields[$n];}
		}
	return $return;
	}
	
function get_category_tree_fields()
	{
	# Returns a list of fields with refs matching the supplied field refs.

	$fields=sql_query("select name from resource_type_field where type=7 and length(name)>0 order by order_by");
	$cattreefields=array();
	foreach ($fields as $field){
		$cattreefields[]=$field['name'];
	}
	return $cattreefields;
	}	

function get_OR_fields()
	{
	# Returns a list of fields that should retain semicolon separation of keywords in a search string

	$fields=sql_query("select name from resource_type_field where type=7 or type=2 or type=3 and length(name)>0 order by order_by");
	$orfields=array();
	foreach ($fields as $field){
		$orfields[]=$field['name'];
	}
	return $orfields;
	}		
	
function get_fields_for_search_display($field_refs)
{
    # Returns a list of fields/properties with refs matching the supplied field refs, for search display setup
    # This returns fewer columns and doesn't require that the fields be indexed, as in this case it's only used to judge whether the field should be highlighted.
    # Standard field titles are translated using $lang.  Custom field titles are i18n translated.

    if (!is_array($field_refs)) {
        print_r($field_refs);
        exit(" passed to getfields() is not an array. ");
    }

    # Executes query.
    $fields = sql_query("select ref, name, type, title, keywords_index, partial_index, value_filter from resource_type_field where ref in ('" . join("','",$field_refs) . "')");

    # Applies field permissions and translates field titles in the newly created array.
    $return = array();
    for ($n = 0;$n<count($fields);$n++) {
        if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"]))
        && !checkperm("f-" . $fields[$n]["ref"])) {
            $fields[$n]["title"] = lang_or_i18n_get_translated($fields[$n]["title"], "fieldtitle-");
            $return[] = $fields[$n];
        }
    }
    return $return;
}

function verify_extension($filename,$allowed_extensions=""){
	# Allowed extension?
	$extension=explode(".",$filename);
    if(count($extension)>1){
    	$extension=trim(strtolower($extension[count($extension)-1]));
		} else { return false;}
		
	if ($allowed_extensions!=""){
		$allowed_extensions=explode(",",strtolower($allowed_extensions));
		if (!in_array($extension,$allowed_extensions)){ return false;}
	}
	
	
	return true;
}

function get_allowed_extensions($ref){
	$type = sql_value("select resource_type value from resource where ref=$ref","");
	$allowed_extensions=sql_value("select allowed_extensions value from resource_type where ref=$type","");
	return $allowed_extensions;
}
function get_allowed_extensions_by_type($resource_type){
	$allowed_extensions=sql_value("select allowed_extensions value from resource_type where ref='$resource_type'","");
	return $allowed_extensions;
}

/**
 * Detect if a path is relative or absolute.
 * If it is relative, we compute its absolute location by assuming it is
 * relative to the application root (parent folder).
 * 
 * @param string $path A relative or absolute path
 * @param boolean $create_if_not_exists Try to create the path if it does not exists. Default to False.
 * @access public
 * @return string A absolute path
 */
function getAbsolutePath($path, $create_if_not_exists = false)
	{
	if(preg_match('/^(\/|[a-zA-Z]:[\\/]{1})/', $path)) // If the path start by a '/' or 'c:\', it is an absolute path.
		{
		$folder = $path;
		}
	else // It is a relative path.
		{
		$folder = sprintf('%s%s..%s%s', dirname(__FILE__), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
		}

	if ($create_if_not_exists && !file_exists($folder)) // Test if the path need to be created.
		{
		mkdir($folder,0777);
		} // Test if the path need to be created.

	return $folder;
	} // getAbsolutePath()



/**
 * Find the files present in a folder, and sub-folder.
 * 
 * @param string $path The path to look into.
 * @param boolean $recurse Trigger the recursion, default to True.
 * @param boolean $include_hidden Trigger the listing of hidden files / hidden directories, default to False.
 * @access public
 * @return array A list of files present in the inspected folder (paths are relative to the inspected folder path).
 */
function getFolderContents($path, $recurse = true, $include_hidden = false)
	{
	if(!is_dir($path)) // Test if the path is not a folder.
		{
			return array();
		} // Test if the path is not a folder.

	$directory_handle = opendir($path);
	if($directory_handle === false) // Test if the directory listing failed.
		{
		return array();
		} // Test if the directory listing failed.

	$files = array();
	while(($file = readdir($directory_handle)) !== false) // For each directory listing entry.
		{
		if(! in_array($file, array('.', '..'))) // Test if file is not unix parent and current path.
			{
			if($include_hidden || ! preg_match('/^\./', $file)) // Test if the file can be listed.
				{
				$complete_path = $path . DIRECTORY_SEPARATOR . $file;
				if(is_dir($complete_path) && $recurse) // If the path is a directory, and need to be explored.
					{
					$sub_dir_files = getFolderContents($complete_path, $recurse, $include_hidden);
					foreach($sub_dir_files as $sub_dir_file) // For each subdirectory contents.
						{
						$files[] = $file . DIRECTORY_SEPARATOR . $sub_dir_file;
						} // For each subdirectory contents.
					}
				elseif(is_file($complete_path)) // If the path is a file.
					{
					$files[] = $file;
					}
				} // Test if the file can be listed.
			} // Test if file is not unix parent and current path.
		} // For each directory listing entry.

	// We close the directory handle:
	closedir($directory_handle);

	// We sort the files alphabetically.
	natsort($files);

	return $files;
	} // getPathFiles()



/**
 * Returns filename component of path
 * This version is UTF-8 proof.
 * Thanks to nasretdinov at gmail dot com
 * @link http://www.php.net/manual/en/function.basename.php#85369
 * 
 * @param string $file A path.
 * @access public
 * @return string Returns the base name of the given path.
 */
function mb_basename($file)
	{
	$exploded_path = preg_split('/[\\/]+/',$file);
	return end($exploded_path);
	} // mb_basename()



/**
 * Remove the extension part of a filename.
 * Thanks to phparadise
 * http://fundisom.com/phparadise/php/file_handling/strip_file_extension
 * 
 * @param string $name A file name.
 * @access public
 * @return string Return the file name without the extension part.
 */
function strip_extension($name)
	{
	$ext = strrchr($name, '.');
	if($ext !== false)
		{
		$name = substr($name, 0, -strlen($ext));
		}
	return $name;
	} // strip_extension()



function get_nopreview_icon($resource_type,$extension,$col_size,$contactsheet=false,$pluginpage=false)
	{
	# Returns the path (relative to the gfx folder) of a suitable folder to represent
	# a resource with the given resource type or extension
	# Extension matches are tried first, followed by resource type matches
	# Finally, if there are no matches then the 'type1' image will be used.
	# set contactsheet to true to cd up one more level.
	
	global $language;
	
	$col=($col_size?"_col":"");
	$folder="../gfx/";
	if ($contactsheet){$folder="../../gfx/";}
	if ($pluginpage){$folder="../../../gfx/";}
	
	# Metadata template? Always use icon for 'mdtr', although typically no file will be attached.
	global $metadata_template_resource_type;
	if (isset($metadata_template_resource_type) && $metadata_template_resource_type==$resource_type) {$extension="mdtr";}


	# Try extension (language specific)
	$try="no_preview/extension/" . $extension . $col . "_" . $language . ".png";
	if (file_exists($folder . $try))
		{
		return $try;
		}
	# Try extension (default)
	$try="no_preview/extension/" . $extension . $col . ".png";
	if (file_exists($folder . $try))
		{
		return $try;
		}
	
	# --- Legacy ---
	# Support the old location for resource type and GIF format (root of gfx folder)
	# Some installations use custom types in this location.
	$try="type" . $resource_type . $col . ".gif";
	if (file_exists($folder . $try))
		{
		return $try;
		}


	# Try resource type (language specific)
	$try="no_preview/resource_type/type" . $resource_type . $col . "_" . $language . ".png";
	if (file_exists($folder . $try))
		{
		return $try;
		}
	# Try resource type (default)
	$try="no_preview/resource_type/type" . $resource_type . $col . ".png";
	if (file_exists($folder . $try))
		{
		return $try;
		}

	# Fall back to the 'no preview' icon used for type 1.
	return "no_preview/resource_type/type1" . $col . ".png";
	}
	
	
function is_process_lock($name)
	{
	# Checks to see if a process lock exists for the given process name.
	global $storagedir,$process_locks_max_seconds;
	
	# Check that tmp/process_locks exists, create if not.
	# Since the get_temp_dir() method does this checking, omit: if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
	if(!is_dir(get_temp_dir() . "/process_locks")){mkdir(get_temp_dir() . "/process_locks",0777);}
	
	# No lock file? return false
	if (!file_exists(get_temp_dir() . "/process_locks/" . $name)) {return false;}
	
	$time=trim(file_get_contents(get_temp_dir() . "/process_locks/" . $name));
	if ((time()-$time)>$process_locks_max_seconds) {return false;} # Lock has expired
	
	return true; # Lock is valid
	}
	
function set_process_lock($name)
	{
	# Set a process lock
	file_put_contents(get_temp_dir() . "/process_locks/" . $name,time());
	// make sure this is editable by the server in case a process lock could be set by different system users
	chmod(get_temp_dir() . "/process_locks/" . $name,0777);
	return true;
	}
	
function clear_process_lock($name)
	{
	# Clear a process lock
	unlink(get_temp_dir() . "/process_locks/" . $name);
	return true;
	}
	
	
function open_access_to_user($user,$resource,$expires)
	{
	# Give the user full access to the given resource.
	# Used when approving requests.
	
	# Delete any existing custom access
	sql_query("delete from resource_custom_access where user='$user' and resource='$resource'");
	
	# Insert new row
	sql_query("insert into resource_custom_access(resource,access,user,user_expires) values ('$resource','0','$user'," . ($expires==""?"null":"'$expires'") . ")");
	
	return true;
	}
	
function remove_access_to_user($user,$resource)
	{
	# Remove any user-specific access granted by an 'approve'.
	# Used when declining requests.
	
	# Delete any existing custom access
	sql_query("delete from resource_custom_access where user='$user' and resource='$resource'");
	
	return true;
	}
	
function user_email_exists($email)
	{
	# Returns true if a user account exists with e-mail address $email
	$email=escape_check(trim(strtolower($email)));
	return (sql_value("select count(*) value from user where email like '$email'",0)>0);
	}

function filesize_unlimited($path)
    {
    # A resolution for PHP's issue with large files and filesize().

    if (PHP_OS=='WINNT')
        {
		$filesystem=new COM('Scripting.FileSystemObject');
		$file=$filesystem->GetFile($path);
		return $file->Size();
        }
    else
        {
        # Attempt to use 'du' utility.
        $f2 = exec("du -k " . escapeshellarg($path));
        $f2s = explode("\t",$f2);
        if (count($f2s)!=2) {return @filesize($path);} # Bomb out, the output wasn't as we expected. Return the filesize() output.
        return $f2s[0] * 1024;
        }
    }

function strip_leading_comma($val)
	{
    # make sure value is numeric if it can be, i.e. for ratings
	# not sure if it's ok to remove commas before any value, since they were explicitly added
	if (is_numeric(str_replace(",","",$val))) {$val=str_replace(",","",$val);}
	return $val;
	}	

// String EnCrypt + DeCrypt function
// Author: halojoy, July 2006
// Modified and commented by: laserlight, August 2006
//
// Exploratory implementation using bitwise ops on strings; Weedpacket September 2006

function convert($text, $key = '') {
    // return text unaltered if the key is blank
    if ($key == '') {
        return $text;
    }

    // remove the spaces in the key
    $key = str_replace(' ', '', $key);
    if (strlen($key) < 8) {
        exit('key error');
    }
    // set key length to be no more than 32 characters
    $key_len = strlen($key);
    if ($key_len > 32) {
        $key_len = 32;
    }

    // A wee bit of tidying in case the key was too long
    $key = substr($key, 0, $key_len);

    // We use this a couple of times or so
    $text_len = strlen($text);

    // fill key with the bitwise AND of the ith key character and 0x1F, padded to length of text.
    $lomask = str_repeat("\x1f", $text_len); // Probably better than str_pad
    $himask = str_repeat("\xe0", $text_len);
    $k = str_pad("", $text_len, $key); // this one _does_ need to be str_pad

    // {en|de}cryption algorithm
    $text = (($text ^ $k) & $lomask) | ($text & $himask);

    return $text;
} 

function make_api_key($username,$password){
	// this is simply an encryption for username and password that will work as an alternative way to log in for remote access pages such as rss and apis
	// this is simply to avoid sending username and password plainly in the url.
	global $api_scramble_key;
    if (extension_loaded('mcrypt') && extension_loaded('hash')){
        $cipher = new Cipher($api_scramble_key);
        return $cipher->encrypt($username."|".$password,$api_scramble_key);
        }
    else{
        return strtr(base64_encode(convert($username."|".$password,$api_scramble_key)), '+/=', '-_,');
        }
	}
	
function decrypt_api_key($key){
	global $api_scramble_key;
    if (extension_loaded('mcrypt') && extension_loaded('hash')){
        $cipher = new Cipher($api_scramble_key);
        $key=$cipher->decrypt($key);
        }
    else{
	$key=convert(base64_decode(strtr($key, '-_,', '+/=')),$api_scramble_key);
        }
	return explode("|",$key);
	}

// alternative encryption using mcrypt extension
//from http://php.net/manual/en/function.mcrypt-encrypt.php
class Cipher {
    private $securekey, $iv;
    function __construct($textkey) {
        $this->securekey = hash('sha256',$textkey,TRUE);
        $this->iv = mcrypt_create_iv(32,MCRYPT_DEV_URANDOM);
    }
    function encrypt($input) {
        return strtr(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv)), '+/=', '-_,');
    }
    function decrypt($input) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode(strtr($input, '-_,', '+/=')), MCRYPT_MODE_ECB, $this->iv));
    }
}
    

function purchase_set_size($collection,$resource,$size,$price)
	{
	// Set the selected size for an item in a collection. This is used later on when the items are downloaded.
	sql_query("update collection_resource set purchase_size='" . escape_check($size) . "',purchase_price='" . escape_check($price) . "' where collection='$collection' and resource='$resource'");
	return true;
	}

function payment_set_complete($collection)
	{
	# Mark items in the collection as paid so they can be downloaded.
	sql_query("update collection_resource set purchase_complete=1 where collection='$collection'");
	
	# For each resource, add an entry to the log to show it has been purchased.
	$resources=sql_query("select * from collection_resource where collection='$collection'");
	foreach ($resources as $resource)
		{
		resource_log($resource["resource"],"p",0,"","","",0,$resource["purchase_size"],$resource["purchase_price"]);
		}
	
	return true;
	}


/**
 * Determines where the tmp directory is.  There are three options here:
 * 1. tempdir - If set in config.php, use this value.
 * 2. storagedir ."/tmp" - If storagedir is set in config.php, use it and create a subfolder tmp.
 * 3. generate default path - use filestore/tmp if all other attempts fail.
 * 4. if a uniqid is provided, create a folder within tmp and return the full path
 * @param bool $asUrl - If we want the return to be like http://my.resourcespace.install/path set this as true.
 * @return string Path to the tmp directory.
 */
function get_temp_dir($asUrl = false,$uniqid="")
{
    global $storagedir, $tempdir;
    // Set up the default.
    $result = dirname(dirname(__FILE__)) . "/filestore/tmp";
	
    // if $tempdir is explicity set, use it.
    if(isset($tempdir))
    {
        // Make sure the dir exists.
        if(!is_dir($tempdir))
        {
            // If it does not exist, create it.
            mkdir($tempdir, 0777);
        }
        $result = $tempdir;
    }
    // Otherwise, if $storagedir is set, use it.
    else if (isset($storagedir))
    {
        // Make sure the dir exists.
        if(!is_dir($storagedir . "/tmp"))
        {
            // If it does not exist, create it.
            mkdir($storagedir . "/tmp", 0777);
        }
        $result = $storagedir . "/tmp";
    }
    else
    {
        // Make sure the dir exists.
        if(!is_dir($result))
        {
            // If it does not exist, create it.
            mkdir($result, 0777);
        }
    }
    
    if ($uniqid!=""){
		$uniqid=str_replace("../","",$uniqid);//restrict to forward-only movements
		$result.="/$uniqid";
		if(!is_dir($result)){
            // If it does not exist, create it.
            mkdir($result, 0777,true);
        }
    }
    
    // return the result.
    if($asUrl==true)
    {
        $result = convert_path_to_url($result);
	$result = str_replace('\\','/',$result);
    }
    return $result;
}

/**
 * Converts a path to a url relative to the installation.
 * @param string $abs_path: The absolute path.
 * @return Url that is the relative path.
 */
function convert_path_to_url($abs_path)
{
    // Get the root directory of the app:
    $rootDir = dirname(dirname(__FILE__));
    // Get the baseurl:
    global $baseurl;
    // Replace the $rootDir with $baseurl in the path given:
    return str_ireplace($rootDir, $baseurl, $abs_path);
}

function run_command($command)
	{
	# Works like system(), but returns the complete output string rather than just the
	# last line of it.
	debug("CLI command: $command");
	$process = @proc_open($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipe, NULL, NULL,
			array('bypass_shell' => true));
	if (is_resource($process)) {
	  $output = trim(stream_get_contents($pipe[1]));
	  debug("CLI output: $output");
	  debug("CLI errors: ". trim(stream_get_contents($pipe[2])));
		return $output;  
	}

	return '';
	}

function run_external($cmd,&$code)
{
# Thanks to dk at brightbyte dot de
# http://php.net/manual/en/function.shell-exec.php
# Returns an array with the resulting output (stdout & stderr). 
    debug("CLI command: $cmd");

    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("pipe", "w") // stderr is a file to write to
    );

    $pipes = array();
    $process = proc_open($cmd, $descriptorspec, $pipes);

    $output = array();

    if (!is_resource($process)) {return false;}

    # Close child's input immediately
    fclose($pipes[0]);

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    while (true)
        {
        $read = array();
        if (!feof($pipes[1])) {$read[] = $pipes[1];}
        if (!feof($pipes[2])) {$read[] = $pipes[2];}

        if (!$read) {break;}

        $write = NULL;
        $ex = NULL;
        $ready = stream_select($read, $write, $ex, 2);

        if ($ready===false)
            {
            break; # Should never happen - something died
            }

        foreach ($read as $r)
            {
            $s = rtrim(fgets($r, 1024),"\r\n"); # Reads a line and strips newline and carriage return from the end. 
            $output[] = $s;
            }
        }

    fclose($pipes[1]);
    fclose($pipes[2]);
    
    debug("CLI output: ". implode("\n", $output));

    $code = proc_close($process);

    return $output;
}

function error_alert($error,$back=true){

	foreach ($GLOBALS as $key=>$value){
		$$key=$value;
	} 
	if ($back){include($storagedir."/../include/header.php");}
	echo "<script type='text/javascript'>
	alert('$error');";
	if ($back){echo "history.go(-1);";}
	echo "</script>";
}

function xml_entities($text, $charset = 'Windows-1252'){
     // Debug and Test
    // $text = "test &amp; &trade; &amp;trade; abc &reg; &amp;reg; &#45;";
   
    // First we encode html characters that are also invalid in xml
    $text = htmlentities($text, ENT_COMPAT, $charset);
   
    // XML character entity array from Wiki
    // Note: &apos; is useless in UTF-8 or in UTF-16
    $arr_xml_special_char = array("&quot;","&amp;","&apos;","&lt;","&gt;");
   
    // Building the regex string to exclude all strings with xml special char
    $arr_xml_special_char_regex = "(?";
    foreach($arr_xml_special_char as $key => $value){
        $arr_xml_special_char_regex .= "(?!$value)";
    }
    $arr_xml_special_char_regex .= ")";
   
    // Scan the array for &something_not_xml; syntax
    $pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";
   
    // Replace the &something_not_xml; with &amp;something_not_xml;
    $replacement = '&amp;${1}';
    return preg_replace($pattern, $replacement, $text);
}

function format_display_field($value){
	
	// applies trim/wordwrap/highlights 
	
	global $results_title_trim,$results_title_wordwrap,$df,$x,$search;
	$string=i18n_get_translated($value);
	$string=TidyList($string);
	$string=tidy_trim($string,$results_title_trim);
	$wordbreaktag="<wbr>"; // $wordbreaktag="&#8203;" I'm having slightly better luck with <wbr>, but this pends more testing.
	// Opera doesn't renders the zero-width space with a small box.
	$extra_word_separators=array("_"); // only underscore is necessary (regex considers underscores not to separate words, 
	// but we want them to); I've based these transformations on an array just in case more characters act this way.
	
	$ews_replace=array();
	foreach($extra_word_separators as $extra_word_separator){
		$ews_replace[]="{".$extra_word_separator." }";
	}

	//print_r($config_separators_replace);
	$string=str_replace($extra_word_separators,$ews_replace,$string);
	$string=wordwrap($string,$results_title_wordwrap,"#zwspace",false);
	$string=str_replace($ews_replace,$extra_word_separators,$string);
	$string=htmlspecialchars($string);
	$string=highlightkeywords($string,$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed']);
	
	$ews_replace2=array();
	foreach($extra_word_separators as $extra_word_separator){
		$ews_replace2[]="{".$extra_word_separator."#zwspace}";
	}
	$ews_replace3=array();
	foreach($extra_word_separators as $extra_word_separator){
		$ews_replace3[]=$wordbreaktag.$extra_word_separator;
	}
	
	$string=str_replace($ews_replace2,$ews_replace3,$string);
	$string=str_replace("#zwspace",$wordbreaktag." ",$string);
	return $string;
}

// found multidimensional array sort function to support the performance footer
// http://www.php.net/manual/en/function.sort.php#104464
 function sortmulti ($array, $index, $order, $natsort=FALSE, $case_sensitive=FALSE) {
        if(is_array($array) && count($array)>0) {
            foreach(array_keys($array) as $key)
            $temp[$key]=$array[$key][$index];
            if(!$natsort) {
                if ($order=='asc')
                    asort($temp);
                else   
                    arsort($temp);
            }
            else
            {
                if ($case_sensitive===true)
                    natsort($temp);
                else
                    natcasesort($temp);
            if($order!='asc')
                $temp=array_reverse($temp,TRUE);
            }
            foreach(array_keys($temp) as $key)
                if (is_numeric($key))
                    $sorted[]=$array[$key];
                else   
                    $sorted[$key]=$array[$key];
            return $sorted;
        }
    return $sorted;
}

if (!function_exists("draw_performance_footer")){
function draw_performance_footer(){
	global $config_show_performance_footer,$querycount,$querytime,$querylog,$pagename;
	if ($config_show_performance_footer){	
	$querylog=sortmulti ($querylog, "time", "desc", FALSE, FALSE);
	# --- If configured (for debug/development only) show query statistics
	?>
	<?php if ($pagename=="collections"){?><br/><br/><br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><div style="float:left;"><?php } else { ?><div style="float:right; margin-right: 10px;"><?php } ?>
	<table class="InfoTable" style="float: right;margin-right: 10px;">
	<tr><td>Query count</td><td><?php echo $querycount?></td></tr>
	<tr><td>Query time</td><td><?php echo round($querytime,4)?></td></tr>
	<?php $dupes=0;
	foreach ($querylog as $query=>$values){
			if ($values['dupe']>1){$dupes++;}
		}
	?>
	<tr><td>Dupes</td><td><?php echo $dupes?></td></tr>
	<tr><td colspan=2><a href="#" onClick="document.getElementById('querylog').style.display='block';return false;">&gt;&nbsp;details</a></td></tr>
	</table>
	<table class="InfoTable" id="querylog" style="display: none; float: <?php if ($pagename=='collections'){?>left<?php } else {?>right<?php }?>; margin: 10px;">
	<?php

		foreach($querylog as $query=>$values){
		if (substr($query,0,7)!="explain" && $query!="show warnings"){
		$show_warnings=false;
		if (strtolower(substr($query,0,6))=="select"){
			$explain=sql_query("explain extended ".$query);
			/*$warnings=sql_query("show warnings");
			$show_warnings=true;*/
		}
		?>
		<tr><td align="left"><div style="word-wrap: break-word; width:350px;"><?php echo $query?><?php if ($show_warnings){ foreach ($warnings as $warning){echo "<br /><br />".$warning['Level'].": ".htmlentities($warning['Message']);}}?></div></td><td>&nbsp;
		<table class="InfoTable">
		<?php if (strtolower(substr($query,0,6))=="select"){
			?><tr>
			<?php
			foreach ($explain[0] as $explainitem=>$value){?>
				<td align="left">   
				<?php echo $explainitem?><br /></td><?php 
				}
			?></tr><?php
			for($n=0;$n<count($explain);$n++){
				?><tr><?php
				foreach ($explain[$n] as $explainitem=>$value){?>
				<td align="left">   
					<?php echo str_replace(",",", ",$value)?></td><?php 
					}
				?></tr><?php	
				}
			}	?>
		</table>
		</td><td><?php echo round($values['time'],4)?></td>
		</td><td><?php echo ($values['dupe']>1)?''.$values["dupe"].'X':'1'?></td></tr>
		<?php	
		}
		}
	?>
	</table>
	</div>
	<?php
	}
}
}

function sql_affected_rows(){
	global $use_mysqli;
	if ($use_mysqli){
		global $db;
		return mysqli_affected_rows($db);
	}
	else {
		return mysql_affected_rows();
	}
}

function get_utility_path($utilityname, &$checked_path = null)
    {
    # !!! Under development - only some of the utilities are implemented!!!

    # Returns the full path to a utility if installed, else returns false.
    # Note that this function doesn't check that the utility is working.

    global $imagemagick_path, $ghostscript_path, $ghostscript_executable, $ffmpeg_path, $exiftool_path, $antiword_path, $pdftotext_path, $blender_path, $archiver_path, $archiver_executable;

    $checked_path = null;

    switch (strtolower($utilityname))
        {
        case "im-convert":
            if (!isset($imagemagick_path)) {return false;} # ImageMagick convert path not configured.
            return get_executable_path($imagemagick_path, array("unix"=>"convert", "win"=>"convert.exe"), $checked_path);
            break;
        case "im-identify":
            if (!isset($imagemagick_path)) {return false;} # ImageMagick identify path not configured.
            return get_executable_path($imagemagick_path, array("unix"=>"identify", "win"=>"identify.exe"), $checked_path);
            break;
        case "im-composite":
            if (!isset($imagemagick_path)) {return false;} # ImageMagick composite path not configured.
            return get_executable_path($imagemagick_path, array("unix"=>"composite", "win"=>"composite.exe"), $checked_path);
            break;
        case "im-mogrify":
            if (!isset($imagemagick_path)) {return false;} # ImageMagick mogrify path not configured.
            return get_executable_path($imagemagick_path, array("unix"=>"mogrify", "win"=>"mogrify.exe"), $checked_path);
            break;
        case "ghostscript":
            if (!isset($ghostscript_path)) {return false;} # Ghostscript path not configured.
            if (!isset($ghostscript_executable)) {return false;} # Ghostscript executable not configured.
            return get_executable_path($ghostscript_path, array("unix"=>$ghostscript_executable, "win"=>$ghostscript_executable), $checked_path, true); # Note that $check_exe is set to true. In that way get_utility_path() becomes backwards compatible with get_ghostscript_command().
            break;
        case "ffmpeg":
            if (!isset($ffmpeg_path)) {return false;} # FFmpeg path not configured.
            return get_executable_path($ffmpeg_path, array("unix"=>"ffmpeg", "win"=>"ffmpeg.exe"), $checked_path);
            break;
        case "exiftool":
            if (!isset($exiftool_path)) {return false;} # Exiftool path not configured.
            return get_executable_path($exiftool_path, array("unix"=>"exiftool", "win"=>"exiftool.exe"), $checked_path);
            break;
        case "antiword":
            break;
        case "pdftotext":
            break;
        case "blender":
            break;
        case "archiver":
            if (!isset($archiver_path)) {return false;} # Archiver path not configured.
            if (!isset($archiver_executable)) {return false;} # Archiver executable not configured.
            return get_executable_path($archiver_path, array("unix"=>$archiver_executable, "win"=>$archiver_executable), $checked_path);
            break;
        }
    }

function get_executable_path($path, $executable, &$checked_path, $check_exe = false)
    {
    global $config_windows;
    $os = php_uname('s');
    if ($config_windows || stristr($os, 'windows'))
        {
        $checked_path = $path . "\\" . $executable["win"];
        if (file_exists($checked_path)) {return escapeshellarg($checked_path);}
        if ($check_exe)
            {
            # Also check the path with a suffixed ".exe".
            $checked_path_without_exe = $checked_path;
            $checked_path = $path . "\\" . $executable["win"] . ".exe"; 
            if (file_exists($checked_path)) {return escapeshellarg($checked_path);}
            $checked_path = $checked_path_without_exe; # Return the checked path without the suffixed ".exe".
            }
        }
    else
        {
        $checked_path = stripslashes($path) . "/" . $executable["unix"];
        if (file_exists($checked_path)) {return escapeshellarg($checked_path);}
        }
    return false; # No path found.
    }

if (!function_exists("resolve_user_emails")){
function resolve_user_emails($ulist){
	// return an array of emails from a list of usernames and email addresses. 
	// with 'key_required' sibling array preserving the intent of internal/external sharing.
	$emails_key_required=array();
	for ($n=0;$n<count($ulist);$n++)
		{
		$uname=$ulist[$n];
		$email=sql_value("select email value from user where username='" . escape_check($uname) . "'",'');
		if ($email=='')
			{
			# Not a recognised user, if @ sign present, assume e-mail address specified
			if (strpos($uname,"@")===false) {
				error_alert($lang["couldnotmatchallusernames"]);die();
			}
			$emails_key_required['unames'][$n]=$uname;
			$emails_key_required['emails'][$n]=$uname;
			$emails_key_required['key_required'][$n]=true;
			}
		else
			{
			# Add e-mail address from user account
			$emails_key_required['unames'][$n]=$uname;
			$emails_key_required['emails'][$n]=$email;
			$emails_key_required['key_required'][$n]=false;
			}
		}
	return $emails_key_required;
}	
}
