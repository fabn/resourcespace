<?php
# General functions, useful across the whole solution

$GLOBALS['get_resource_path_fpcache'] = array();
function get_resource_path($ref,$getfilepath,$size,$generate,$extension="jpg",$scramble=-1,$page=1,$watermarked=false,$file_modified="",$alternative=-1,$includemodified=true)
	{
	# returns the correct path to resource $ref of size $size ($size==empty string is original resource)
	# If one or more of the folders do not exist, and $generate=true, then they are generated
	
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
	# Returns basic resource data (from the resource table alone) for resource $ref.
	# For 'dynamic' field data, see get_resource_field_data
	global $default_resource_type, $get_resource_data_cache,$resource_hit_count_on_downloads,$always_record_resource_creator;
	if ($cache && isset($get_resource_data_cache[$ref])) {return $get_resource_data_cache[$ref];}
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
			if (isset($always_record_resource_creator) && $always_record_resource_creator)
				{
					global $userref;
                			$user=$userref;
                		} else {$user=-1;}

			sql_query("insert into resource (ref,resource_type,created_by) values ('$ref','$default_resource_type','$user')");
			$resource=sql_query("select * from resource where ref='$ref'");
			}
		}

	# update hit count if not tracking downloads only
	if (!$resource_hit_count_on_downloads) { 
		# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability).
		sql_query("update resource set new_hit_count=greatest(hit_count,new_hit_count)+1 where ref='$ref'");
	} 
	$get_resource_data_cache[$ref]=$resource[0];
	return $resource[0];
	}

function get_resource_field_data($ref,$multi=false,$use_permissions=true,$originalref=-1)
	{
	# Returns field data and field properties (resource_type_field and resource_data tables)
	# for this resource, for display in an edit / view form.

	# Find the resource type
	if ($originalref==-1) {$originalref=$ref;} # When a template has been selected, only show fields for the type of the original resource ref, not the template (which shows fields for all types)
	$rtype=sql_value("select resource_type value from resource where ref='$originalref'",0);

	# If using metadata templates, 
	$templatesql="";
	global $metadata_template_resource_type;
	if (isset($metadata_template_resource_type) && $metadata_template_resource_type==$rtype)
		{
		# Show all resource fields, just as with editing multiple resources.
		$multi=true;
		}

	$return=array();
	$fields=sql_query("select *,f.required frequired,f.ref fref,f.help_text,f.partial_index from resource_type_field f left join resource_data d on d.resource_type_field=f.ref and d.resource='$ref' where ( " . (($multi)?"1=1":"f.resource_type=0 or f.resource_type=999 or f.resource_type='$rtype'") . ") order by f.resource_type,f.order_by,f.ref");
	
	# Build an array of valid types and only return fields of this type.
	$validtypes=sql_array("select ref value from resource_type");
	$validtypes[]=0;$validtypes[]=999;# Support archive and global.

	for ($n=0;$n<count($fields);$n++)
		{
		if ((!$use_permissions ||     ((checkperm("f*") || checkperm("f" . $fields[$n]["fref"])) && 
		!checkperm("f-" . $fields[$n]["fref"])))
		&& in_array($fields[$n]["resource_type"],$validtypes) && !checkperm("T" . $fields[$n]["resource_type"])) {$return[]=$fields[$n];}
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
	$return=array();
	# Translate names and check permissions
	for ($n=0;$n<count($r);$n++)
		{
		if (!checkperm('T' . $r[$n]['ref']) && !checkperm('X' . $r[$n]['ref']))
			{
			$r[$n]["name"]=i18n_get_translated($r[$n]["name"]);	# Translate name
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
		$ns=cleanse_string($ns,true);
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

			if ($partial_index) {$return2=add_partial_index($return2);}
			return trim_array($return2,$config_trimchars);
			}
		else
			{
			return trim_array($return,$config_trimchars);
			}
		}
	else
		{
		# split using spaces and similar chars (according to configured whitespace characters)
		$ns=explode(" ",cleanse_string($ns,false));
		if ($index && $partial_index) {$ns=add_partial_index($ns);}
		return trim_array($ns,$config_trimchars);
		}

	}

function cleanse_string($string,$preserve_separators)
	{
	# Removes characters from a string prior to keyword splitting, for example full stops
	# Also makes the string lower case ready for indexing.
	global $config_separators;
	if ($preserve_separators)
		{
		return strtolower(trim_spaces(str_replace($config_separators," ",$string)));
		}
	else
		{
		# Also strip out the separators used when specifying multiple field/keyword pairs (comma and colon)
		$s=$config_separators;
		$s[]=",";
		$s[]=":";
		return strtolower(trim_spaces(str_replace($s," ",$string)));
		}
	}

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

function add_partial_index($keywords)
	{
	# For each keywords in the supplied keywords list add all possible infixes and return the combined array.
	# This therefore returns all keywords that need indexing for the given string.
	# Only for fields with 'partial_index' enabled.
	$return=$keywords;
	for ($n=0;$n<count($keywords);$n++)
		{
		$keyword=trim($keywords[$n]);
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
					$return[]=$infix;
					}
				}
			} # End of no-spaces condition
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
	
	# Also update the resource table
	# greatest() is used so the value is taken from the hit_count column in the event that new_hit_count is zero to support installations that did not previously have a new_hit_count column (i.e. upgrade compatability)
	sql_query("update resource set hit_count=greatest(hit_count,new_hit_count)");
	}
	
function get_image_sizes($ref,$internal=false,$extension="jpg",$onlyifexists=true)
	{
	# Returns a table of available image sizes for resource $ref.
	# The original image file assumes the name of the 'nearest size (up)' in the table

	global $imagemagick_calculate_sizes;

	# add the original image
	$return=array();
	$lastname="";$lastpreview=0;$lastrestricted=0;
	$path2=get_resource_path($ref,true,'',false,$extension);
	if (file_exists($path2))
	{
		$returnline=array();
		$returnline["name"]=$lastname;
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
			$filesize=@filesize($file);
			
			# imagemagick_calculate_sizes is normally turned off 
			if (isset($imagemagick_path) && $imagemagick_calculate_sizes)
				{
				# Use ImageMagick to calculate the size
				
				$prefix = '';
				# Camera RAW images need prefix
				if (preg_match('/^(dng|nef|x3f|cr2|crw|mrw|orf|raf|dcr)$/i', $extension, $rawext)) { $prefix = $rawext[0] .':'; }

				# Locate imagemagick.
				$identcommand=$imagemagick_path . "/bin/identify";
				if (!file_exists($identcommand)) {$identcommand=$imagemagick_path . "/identify";}
				if (!file_exists($identcommand)) {$identcommand=$imagemagick_path . "\identify.exe";}
				if (!file_exists($identcommand)) {exit("Could not find ImageMagick 'identify' utility.'");}	
				# Get image's dimensions.
				$identcommand .= ' -format %wx%h '. escapeshellarg($prefix . $file) .'[0]';
				$identoutput=shell_exec($identcommand);
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
		if (file_exists($path) || (!$onlyifexists))
			{
			if (($sizes[$n]["internal"]==0) || ($internal))
				{
				$returnline=array();
				$returnline["name"]=$sizes[$n]["name"];
				$returnline["allow_preview"]=$sizes[$n]["allow_preview"];

				# The ability to restrict download size by user group and resource type.
				$resource_type=sql_value("select resource_type value from resource where ref='$ref'","");
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
				if (($filesize=@filesize($path))===false) {$returnline["filesize"]="?";$returnline["filedown"]="?";}
				else {$returnline["filedown"]=ceil($filesize/50000) . " seconds @ broadband";$filesize=formatfilesize($filesize);}
				$returnline["filesize"]=$filesize;			
				$returnline["width"]=$sw;			
				$returnline["height"]=$sh;
				$returnline["extension"]='jpg';
				$return[]=$returnline;
				}
			}
		$lastname=$sizes[$n]["name"];
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
	return sql_query ("select u.*,g.name groupname,g.ref groupref,g.parent groupparent,u.approved from user u left outer join usergroup g on u.usergroup=g.ref $sql order by $order_by",false,$fetchrows);
	}
}	

function get_users_with_permission($permission)
	{
	# Returns all the users who have the permission $permission.
	
	# First find all matching groups
	$groups=sql_query("select ref,permissions from usergroup");
	$matched=array();
	for ($n=0;$n<count($groups);$n++)
		{
		$perms=trim_array(explode(",",$groups[$n]["permissions"]));
		if (in_array($permission,$perms)) {$matched[]=$groups[$n]["ref"];}
		}
	return sql_query ("select u.*,g.name groupname,g.ref groupref,g.parent groupparent from user u left outer join usergroup g on u.usergroup=g.ref where g.ref in ('" . join("','",$matched) . "') order by username",false);
	}
	

function get_usergroups($usepermissions=false,$find="")
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
	if (strlen($find)>0)
		{
		if ($sql=="") {$sql="where ";} else {$sql.=" and ";}
		$sql.="name like '%$find%'";
		}
	global $default_group;
	return sql_query("select * from usergroup $sql order by (ref='$default_group') desc,name");
	}
	
function get_user($ref)
	{
	# Return a user's credentials.
	$return=sql_query("select * from user where ref='$ref'");
	if (count($return)>0) {return $return[0];} else {return false;}
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
		
		sql_query("update user set username='" . getvalescaped("username","") . "'" . $passsql . ",fullname='" . getvalescaped("fullname","") . "',email='" . getvalescaped("email","") . "',usergroup='" . getvalescaped("usergroup","") . "',account_expires=$expires,ip_restrict='" . getvalescaped("ip_restrict","") . "',comments='" . getvalescaped("comments","") . "',approved='" . ((getval("approved","")=="")?"0":"1") . "' where ref='$ref'");
		}
	if (getval("emailme","")!="")
		{
		global $applicationname,$email_from,$baseurl,$lang,$email_url_save_user;
		
		# Fetch any welcome message for this user group
		$welcome=sql_value("select welcome_message value from usergroup where ref='" . getvalescaped("usergroup","") . "'","");
		if (trim($welcome)!="") {$welcome.="\n\n";}
		
		$templatevars['welcome']=$welcome;
		$templatevars['username']=getval("username","");
		$templatevars['password']=getval("password","");
		if (trim($email_url_save_user)!=""){$templatevars['url']=$email_url_save_user;}
		else {$templatevars['url']=$baseurl;}
		
		$message=$templatevars['welcome'] . $lang["newlogindetails"] . "\n\n" . $lang["username"] . ": " . $templatevars['username'] . "\n" . $lang["password"] . ": " . $templatevars['password']."\n\n".$templatevars['url'];
		
		send_mail(getval("email",""),$applicationname . ": " . $lang["youraccountdetails"],$message,"","","emaillogindetails",$templatevars);
		}
	return true;
	}
}

function email_reminder($email)
	{
	if ($email=="") {return false;}
	$details=sql_query("select username from user where email like '$email'");
	if (count($details)==0) {return false;}
	$details=$details[0];
	global $applicationname,$email_from,$baseurl,$lang;
	$password=make_password();
	$password_hash=md5("RS" . $details["username"] . $password);
	
	sql_query("update user set password='$password_hash' where username='" . escape_check($details["username"]) . "'");
	
	$templatevars['username']=$details["username"];
	$templatevars['password']=$password;
	$templatevars['url']=$baseurl;
	
	$message=$lang["newlogindetails"] . "\n\n" . $lang["username"] . ": " . $templatevars['username'] . "\n" . $lang["password"] . ": " . $templatevars['password'] . "\n\n". $templatevars['url'];
	send_mail($email,$applicationname . ": " . $lang["passwordreminder"],$message,"","","emailreminder",$templatevars);
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
	
	
	send_mail($email_notify,$applicationname . ": " . $lang["requestuserlogin"] . " - " . getval("name",""),$message,"",$user_email);
	return true;
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
	if ($bytes<1024)
		{
		return number_format($bytes) . "&nbsp;B";		
		}
	elseif ($bytes<pow(1024,2))
		{
		return number_format(ceil($bytes/1024)) . "&nbsp;KB";
		}
	elseif ($bytes<pow(1024,3))
		{
		return number_format($bytes/pow(1024,2),1) . "&nbsp;MB";
		}
	elseif ($bytes<pow(1024,4))
		{
		return number_format($bytes/pow(1024,3),1) . "&nbsp;GB";
		}
	else
		{
		return number_format($bytes/pow(1024,4),1) . "&nbsp;TB";
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
	
	# Check the password
	$check=check_password($password);
	if ($check!==true) {exit("Error: unable to automatically produce a password that met the criteria. Please check the password criteria in config.php. Generated password was '$password'. Error was: " . $check);}
	
    return $password;
	}


function bulk_mail($userlist,$subject,$text)
	{
	global $email_from,$lang;
	
	# Attempt to resolve all users in the string $userlist to user references.
	if (trim($userlist)=="") {return ($lang["mustspecifyoneuser"]);}
	$userlist=resolve_userlist_groups($userlist);
	$ulist=trim_array(explode(",",$userlist));
	$urefs=sql_array("select ref value from user where username in ('" . join("','",$ulist) . "')");
	if (count($ulist)!=count($urefs)) {return($lang["couldnotmatchusers"]);}

	# Send an e-mail to each resolved user
	$emails=sql_array("select email value from user where ref in ('" . join("','",$urefs) . "')");
	for ($n=0;$n<count($emails);$n++)
		{
		send_mail($emails[$n],$subject,stripslashes(str_replace("\\r\\n","\n",$text)));
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
	if ((strpos($text,",")!==false) && (strpos($text,"~")!==false)) {$s=explode(",",$text);$out="";for ($n=0;$n<count($s);$n++) {if ($n>0) {$out.=",";}; $out.=i18n_get_translated(trim($s[$n]));};return $out;}
	
	global $language,$defaultlanguage;
	
	# Split
	$s=explode("~",$text);

	# Not a translatable field?
	if (count($s)<2) {return $text;}

	# Find the current language and return it
	$default="";
	for ($n=1;$n<count($s);$n++)
		{
		# Not a translated string, return as-is
		if (substr($s[$n],2,1)!=":" && substr($s[$n],5,1)!=":") {return $text;}
		
		# Support both 2 character and 5 character language codes (for example en, en-US).
		$p=strpos($s[$n],':');
		if (substr($s[$n],0,$p)==$language) {return substr($s[$n],$p+1);}
		
		if (substr($s[$n],0,$p)==$defaultlanguage) {$default=substr($s[$n],$p+1);}
		}	
	
	# Translation not found? Return default language
	# No default language entry? Then consider this a broken language string and return the string unprocessed.
	if ($default!="") {return $default;} else {return $text;}
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

function send_mail($email,$subject,$message,$from="",$reply_to="",$html_template="",$templatevars=null,$from_name="")
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
		send_mail_phpmailer($email,$subject,$message,$from,$reply_to,$html_template,$templatevars,$from_name); 
		return true;
		}
	
	# No email address? Exit.
	if (trim($email)=="") {return false;}
	
	# Include footer
	global $email_footer;
	global $disable_quoted_printable_enc;
	
	$message.="\r\n\r\n\r\n" . $email_footer;
	
	if ($disable_quoted_printable_enc==false){
	$message=rs_quoted_printable_encode($message);
	$subject=rs_quoted_printable_encode_subject($subject);
	}
	
	global $email_from;
	if ($from=="") {$from=$email_from;}
	if ($reply_to=="") {$reply_to=$email_from;}
	
	# Work out correct EOL to use for mails (should use the system EOL).
	if (defined("PHP_EOL")) {$eol=PHP_EOL;} else {$eol="\r\n";}
	
	# Add headers
	$headers="";
   #	$headers .= "X-Sender:  x-sender" . $eol;
   	$headers .= "From: \"$from_name\" <$reply_to>" . $eol;
 	$headers .= "Reply-To: $reply_to" . $eol;
   	$headers .= "Date: " . date("r") .  $eol;
   	$headers .= "Message-ID: <" . date("YmdHis") . $from . ">" . $eol;
   	#$headers .= "Return-Path: returnpath" . $eol;
   	//$headers .= "Delivered-to: $email" . $eol;
   	$headers .= "MIME-Version: 1.0" . $eol;
   	$headers .= "X-Mailer: PHP Mail Function" . $eol;
	$headers .= "Content-Type: text/plain; charset=\"UTF-8\"" . $eol;
	$headers .= "Content-Transfer-Encoding: quoted-printable" . $eol;
	mail ($email,$subject,$message,$headers);
	}

if (!function_exists("send_mail_phpmailer")){
function send_mail_phpmailer($email,$subject,$message="",$from="",$reply_to="",$html_template="",$templatevars=null,$from_name="")
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
	global $email_footer;

	if (file_exists("../lib/phpmailer/class.phpmailer.php")){
		include_once("../lib/phpmailer/class.phpmailer.php");
		include_once("../lib/phpmailer/class.html2text.php");
		}
	else if (file_exists("../../lib/phpmailer/class.phpmailer.php")){
		# team center
		include_once("../../lib/phpmailer/class.phpmailer.php");
		include_once("../../lib/phpmailer/class.html2text.php");
		}	
	else if (file_exists("../../../lib/phpmailer/class.phpmailer.php")){
		# plugin
		include_once("../../../lib/phpmailer/class.phpmailer.php");
		include_once("../../../lib/phpmailer/class.html2text.php");
		}	
		
	global $email_from;
	if ($from=="") {$from=$email_from;}
	if ($reply_to=="") {$reply_to=$email_from;}

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
					$$variable="<img src='cid:thumbnail' />";
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
					$template=str_replace("[" . $key . "]",$value,$template);
					}
				}
			$body=$template;	
			}
		}		

	if (!isset($body)){$body=$message;}

	$mail = new PHPMailer();
	$mail->From = $reply_to;
	$mail->FromName = $templatevars['from_name'];
	$mail->AddReplyto($reply_to,$from_name);
	$mail->AddAddress($email);
	$mail->CharSet = "utf-8"; 
	
	if ($html_template!="") {$mail->IsHTML(true);}  	
	else {$mail->IsHTML(false);}
	
	$mail->Subject = $subject;
	$mail->Body    = $body;
	
	if (isset($embed_thumbnail)&&isset($templatevars['thumbnail'])){
		$mail->AddEmbeddedImage($templatevars['thumbnail'], 'thumbnail','thumbnail','base64','image/jpeg'); 
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

function highlightkeywords($text,$search,$partial_index=false,$field_name="",$keywords_index=1)
	{
	# do not hightlight if the field is not indexed, so it is clearer where results came from.	
	if ($keywords_index!=1){return $text;}	
	# Highlight searched keywords in $text
	# Optional - depends on $highlightkeywords being set in config.php.
	global $highlightkeywords;
	# Situations where we do not need to do this.
	if (!isset($highlightkeywords) || ($highlightkeywords==false) || ($search=="") || ($text=="") || (substr($search,0,1)=="!")) {return $text;}

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
	        <span class="HorizontalWhiteNav"><?php if ($break) { ?>&nbsp;<br /><?php } ?><?php if ($curpage>1) { ?><a href="<?php echo $url?>&offset=<?php echo $offset-$per_page?>"><?php } ?>&lt;&nbsp;<?php echo $lang["previous"]?><?php if ($curpage>1) { ?></a><?php } ?>&nbsp;|&nbsp;<a href="#" title="Jump to page" onClick="p=document.getElementById('jumppanel<?php echo $jumpcount?>');if (p.style.display!='block') {p.style.display='block';document.getElementById('jumpto<?php echo $jumpcount?>').focus();} else {p.style.display='none';}; return false;"><?php echo $lang["page"]?>&nbsp;<?php echo $curpage?>&nbsp;<?php echo $lang["of"]?>&nbsp;<?php echo $totalpages?></a>&nbsp;|&nbsp;<?php if ($curpage<$totalpages) { ?><a href="<?php echo $url?>&offset=<?php echo $offset+$per_page?>"><?php } ?><?php echo $lang["next"]?>&nbsp;&gt;<?php if ($curpage<$totalpages) { ?></a><?php } ?>
	   	   </span>
	   	   <div id="jumppanel<?php echo $jumpcount?>" style="display:none;margin-top:5px;"><?php echo $lang["jumptopage"]?>: <input type="text" size="3" id="jumpto<?php echo $jumpcount?>">&nbsp;<input type="submit" name="jump" value="<?php echo $lang["jump"]?>" onClick="var jumpto=document.getElementById('jumpto<?php echo $jumpcount?>').value;if ((jumpto>0) && (jumpto<=<?php echo $totalpages?>)) {document.location='<?php echo $url?>&offset=' + ((jumpto-1) * <?php echo $per_page?>);}"></div>
   	<?php
	}
	
function get_all_image_sizes($internal=false,$restricted=false)
	{
	# Returns all image sizes available.
	return sql_query("select * from preview_size " . (($internal)?"":"where internal!=1") . (($restricted)?" and allow_restricted=1":"") . " order by width asc");
	}
	
function image_size_restricted_access($id)
	{
	# Returns true if the indicated size is allowed for a restricted user.
	return sql_value("select allow_restricted value from preview_size where id='$id'",false);
	}
	
function get_user_log($user)
	{
	return sql_query("select r.ref resourceid,r.title resourcetitle,l.date,l.type,f.title from resource_log l left outer join resource r on l.resource=r.ref left outer join resource_type_field f on f.ref=l.resource_type_field where l.user='$user' order by l.date");
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
		return sql_array("select keyword value from keyword where keyword like '" . escape_check($search) . "%' order by hit_count desc limit $autocomplete_search_items");
		}
	else 
		{
		return sql_array("select distinct k.keyword value,rk.resource_type_field from keyword k,resource_keyword rk where k.ref=rk.keyword and k.keyword like '" . escape_check($search) . "%' and rk.resource_type_field='".$ref."' order by k.hit_count desc limit $autocomplete_search_items");
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
	
function i18n_get_translations($value)
	{
	# For a string in the language format, return all translations as an associative array
	# E.g. "en"->"English translation";
	# "fr"->"French translation"
	global $defaultlanguage;
	if (strpos($value,"~")===false) {return array($defaultlanguage=>$value);}
	$s=explode("~",$value);
	$return=array();
	for ($n=1;$n<count($s);$n++)
		{
		$e=explode(":",$s[$n]);
		if (count($e)==2) {$return[$e[0]]=$e[1];}
		}
	return $return;
	}
	
function get_related_keywords($keyref)
	{
	# For a given keyword reference returns the related keywords
	# Also reverses the process, returning keywords for matching related words
	# and for matching related words, also returns other words related to the same keyword.
	return sql_array(" select keyword value from keyword_related where related='$keyref' union select related value from keyword_related where (keyword='$keyref' or keyword in (select keyword value from keyword_related where related='$keyref')) and related<>'$keyref'");
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
	$return=array();
	$sql="";
	
	# Include the country field even if not selected?
	# This is to provide compatibility for older systems on which the simple search box was not configurable
	# and had a simpler 'country search' option.
	global $country_search;
	if (isset($country_search) && $country_search)
		{
		$sql=" or ref=3";
		}
		
	# Return all appropriate fields.
	$fields=sql_query("select * from resource_type_field where (simple_search=1 $sql) and keywords_index=1 and length(name)>0  order by resource_type,order_by");
	for ($n=0;$n<count($fields);$n++)
		{
		# Check permissions
		if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"]))
		&& !checkperm("f-" . $fields[$n]["ref"]))
			{
			$return[]=$fields[$n];
			}
		}
	return $return;
	}
	

function check_access_key($resource,$key)
	{
	# Verify a supplied external access key
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
		
		global $usergroup,$userpermissions,$userrequestmode;
		$userinfo=sql_query("select u.usergroup,g.permissions from user u join usergroup g on u.usergroup=g.ref where u.ref='$user'");
		if (count($userinfo)>0)
			{
			$usergroup=$userinfo[0]["usergroup"];
			$userpermissions=explode(",",$userinfo[0]["permissions"]);
			if (hook("modifyuserpermissions")){$userpermissions=hook("modifyuserpermissions");}
			$userrequestmode=0; # Always use 'email' request mode for external users
			}
		
		# Set the 'last used' date for this key
		sql_query("update external_access_keys set lastused=now() where resource='$resource' and access_key='$key'");
		
		return true;
		}
	}

function check_access_key_collection($collection,$key)
	{
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
	# Automatically creates a non-approved user account
	global $applicationname,$user_email,$email_from,$baseurl,$email_notify,$lang,$custom_registration_fields,$custom_registration_required,$user_account_auto_creation_usergroup,$registration_group_select;
	
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
	
	# Work out which user group to set.
	$usergroup=$user_account_auto_creation_usergroup;
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

	# Create the user
	sql_query("insert into user (username,password,fullname,email,usergroup,comments,approved) values ('" . $username . "','" . make_password() . "','" . getvalescaped("name","") . "','" . getvalescaped("email","") . "','" . $usergroup . "','" . escape_check($c) . "',0)");
	$new=sql_insert_id();
	
	# Build a message
	$message=$lang["userrequestnotification1"] . "\n\n" . $lang["name"] . ": " . getval("name","") . "\n\n" . $lang["email"] . ": " . getval("email","") . "\n\n" . $lang["comment"] . ": " . getval("userrequestcomment","") . "\n\n" . $lang["ipaddress"] . ": '" . $_SERVER["REMOTE_ADDR"] . "'\n\n" . $c . "\n\n" . $lang["userrequestnotification3"] . "\n$baseurl?u=" . $new;
	
	
	send_mail($email_notify,$applicationname . ": " . $lang["requestuserlogin"] . " - " . getval("name",""),$message,"",$user_email);
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
	return sql_query("select ref,name from usergroup where allow_registration_selection=1 order by name");
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
	
function get_fields_for_search_display($field_refs)
	{
	# Returns a list of fields/properties with refs matching the supplied field refs, for search display setup
	# This returns fewer columns and doesn't require that the fields be indexed, as in this case it's only used to judge whether the field should be highlighted.
	if (!is_array($field_refs)) {print_r($field_refs);exit(" passed to getfields() is not an array. ");}
	$return=array();
	$fields=sql_query("select ref, name, title, keywords_index, partial_index from resource_type_field where ref in ('" . join("','",$field_refs) . "')");
	# Apply field permissions
	for ($n=0;$n<count($fields);$n++)
		{
		if ((checkperm("f*") || checkperm("f" . $fields[$n]["ref"]))
		&& !checkperm("f-" . $fields[$n]["ref"]))
		{$return[]=$fields[$n];}
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



function get_nopreview_icon($resource_type,$extension,$col_size,$contactsheet=false)
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


	# --- Legacy ---
	# Support the old location for resource type and GIF format (root of gfx folder)
	# Some installations use custom types in this location.
	$try="type" . $resource_type . $col . ".gif";
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
	if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
	if(!is_dir($storagedir . "/tmp/process_locks")){mkdir($storagedir . "/tmp/process_locks",0777);}	
	
	# No lock file? return false
	if (!file_exists($storagedir . "/tmp/process_locks/" . $name)) {return false;}
	
	$time=trim(file_get_contents($storagedir . "/tmp/process_locks/" . $name));
	if ((time()-$time)>$process_locks_max_seconds) {return false;} # Lock has expired
	
	return true; # Lock is valid
	}
	
function set_process_lock($name)
	{
	# Set a process lock
	global $storagedir;
	
	file_put_contents($storagedir . "/tmp/process_locks/" . $name,time());
	return true;
	}
	
function clear_process_lock($name)
	{
	# Clear a process lock
	global $storagedir;
	
	unlink($storagedir . "/tmp/process_locks/" . $name);
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
	
function debug($text)
	{
	# Output some text to a debug file.
	# For developers only
	global $storagedir,$debug_log;
	if (!$debug_log) {return true;} # Do not execute if switched off.
	
	$f=fopen($storagedir . "/tmp/debug.txt","a");
	fwrite($f,$text . "\n");
	fclose ($f);
	return true;
	}
	
function user_email_exists($email)
	{
	# Returns true if a user account exists with e-mail address $email
	$email=escape_check(trim(strtolower($email)));
	return (sql_value("select count(*) value from user where email like '$email'",0)>0);
	}
