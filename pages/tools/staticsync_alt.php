<?php
include dirname(__FILE__) . "/../../include/db.php";
include dirname(__FILE__) . "/../../include/general.php";
include dirname(__FILE__) . "/../../include/resource_functions.php";
include dirname(__FILE__) . "/../../include/image_processing.php";

if (file_exists(dirname(__FILE__) . "/staticsync_local_functions.php")){
	include(dirname(__FILE__) . "/staticsync_local_functions.php");
}

if ($staticsync_ingest){
	echo date('Y-m-d H:i:s    ');
	echo "Staticsync is running in ingest mode.\n";
} else {
	echo date('Y-m-d H:i:s    ');
	echo "Staticsync is running in sync mode.\n";
}

$staticsync_alt_suffix_array = array('_alt','_verso','_DNG','_VERSO','_ALT','_dng','_orig','_ORIG','_tp','_TP','_tpv','_TPV','_cov','_COV','_ex','_EX','_scr','_SCR');
$staticsync_alt_suffixes = true;
$numeric_alt_suffixes = 8;
$file_minimum_age = 120; // don't touch files that aren't at least this many seconds old


if ($numeric_alt_suffixes > 0){
	// add numeric suffixes to alt suffix list if we've been told to do that.
	$newsuffixarray = array();
	foreach ($staticsync_alt_suffix_array as $thesuffix){
		array_push($newsuffixarray,$thesuffix);
		for ($i = 1; $i < $numeric_alt_suffixes; $i++){
			array_push($newsuffixarray,$thesuffix.$i);
		}
	}
	$staticsync_alt_suffix_array = $newsuffixarray;
}

// create a timestamp for this run to help someone find all the files later
$staticsync_run_timestamp = "SSTS" . time();
echo date('Y-m-d H:i:s    ');
echo "Timestamp for this run is $staticsync_run_timestamp\n";

set_time_limit(60*60*40);

# Check for a process lock
if (is_process_lock("staticsync")) {
echo date('Y-m-d H:i:s    ');
	echo "Process lock found. Deferring.";
	exit("Process lock is in place. Deferring.");
	}
set_process_lock("staticsync");

echo date('Y-m-d H:i:s    ');
echo "Preloading data...";
$max=350;
$count=0;

$done=sql_array("select file_path value from resource where archive=0 and length(file_path)>0 and file_path like '%/%'");

# Load all modification times into an array for speed
$modtimes=array();
$rd=sql_query("select ref,file_modified,file_path from resource where archive=0 and length(file_path)>0");
for ($n=0;$n<count($rd);$n++)
	{
	$modtimes[$rd[$n]["file_path"]]=$rd[$n]["file_modified"];
	}

$lastsync=sql_value("select value from sysvars where name='lastsync'","");
if (strlen($lastsync)>0) {$lastsync=strtotime($lastsync);} else {$lastsync="";}


echo "...done. Looking for changes...";

# Pre-load the category tree, if configured.
if (isset($staticsync_mapped_category_tree))
	{
	$field=get_field($staticsync_mapped_category_tree);
	$tree=explode("\n",trim($field["options"]));
	}


function touch_category_tree_level($path_parts)
	{
	# For each level of the mapped category tree field, ensure that the matching path_parts path exists
	global $staticsync_mapped_category_tree,$tree;

	$altered_tree=false;
	$parent_search=0;
	$nodename="";
	
	for ($n=0;$n<count($path_parts);$n++)
		{
		# The node name should contain all the subsequent parts of the path
		if ($n>0) {$nodename.="~";}
		$nodename.=$path_parts[$n];
		
		# Look for this node in the tree.		
		$found=false;
		for ($m=0;$m<count($tree);$m++)
			{
			$s=explode(",",$tree[$m]);
			if ((count($s)==3) && ($s[1]==$parent_search) && $s[2]==$nodename)
				{
				# A match!
				$found=true;
				$parent_search=$m+1; # Search for this as the parent node on the pass for the next level.
				}
			}
		if (!$found)
			{
			echo date('Y-m-d H:i:s    ');
			echo "Not found: " . $nodename . " @ level " . $n . "\n";
			# Add this node

			$tree[]=(count($tree)+1) . "," . $parent_search . "," . $nodename;
			$altered_tree=true;
			$parent_search=count($tree); # Search for this as the parent node on the pass for the next level.
			}
		}
	if ($altered_tree)
		{
		# Save the updated tree.
		sql_query("update resource_type_field set options='" . escape_check(join("\n",$tree)) . "' where ref='" . $staticsync_mapped_category_tree . "'");
		}
	}


function ProcessFolder($folder)
	{
	#echo "<br>processing folder $folder";
	global $syncdir,$nogo,$max,$count,$done,$modtimes,$lastsync, $ffmpeg_preview_extension, $staticsync_autotheme, $staticsync_extension_mapping_default, $staticsync_extension_mapping, $staticsync_mapped_category_tree,$staticsync_title_includes_path, $staticsync_ingest, $staticsync_mapfolders,$staticsync_alternatives_suffix,$staticsync_alt_suffixes,$staticsync_alt_suffix_array,$file_minimum_age,$staticsync_run_timestamp;
	
	$collection=0;
	
	echo "Processing Folder: $folder\n";
	
	# List all files in this folder.
	$dh=opendir($folder);
 	echo date('Y-m-d H:i:s    ');
	echo "Reading from $folder\n";
	while (($file = readdir($dh)) !== false)
		{
                // because of alternative processing, some files may disappear during the run
                // that's ok - just ignore it and move on
                if (!file_exists($folder . "/" . $file)){
	  		echo date('Y-m-d H:i:s    ');
			echo "File $file missing. Moving on.\n";
			continue;
		}



		$filetype=filetype($folder . "/" . $file);
		$fullpath=$folder . "/" . $file;
		$shortpath=str_replace($syncdir . "/","",$fullpath);
		
		if ($staticsync_mapped_category_tree)
			{
			$path_parts=explode("/",$shortpath);
			array_pop($path_parts);
			touch_category_tree_level($path_parts);
			}	
		
		# -----FOLDERS-------------
		if ((($filetype=="dir") || $filetype=="link") && ($file!=".") && ($file!="..") && (strpos($nogo,"[" . $file . "]")===false) && strpos($file,$staticsync_alternatives_suffix)===false)
			{
			# Recurse
			#echo "\n$file : " . filemtime($folder . "/" . $file) . " > " . $lastsync;
			if (true || (strlen($lastsync)=="") || (filemtime($folder . "/" . $file)>($lastsync-26000)))
				{
				ProcessFolder($folder . "/" . $file);
				}
			}
			
		# -------FILES---------------
		if (($filetype=="file") && (substr($file,0,1)!=".") && (strtolower($file)!="thumbs.db") && !ss_is_alt($file))
			{

                    // we want to make sure we don't touch files that are too new
                    // so check this

                        if (time() -  filectime($folder . "/" . $file) < $file_minimum_age){
			    echo date('Y-m-d H:i:s    ');
                            echo "   $file too new -- skipping .\n";
                            //echo filectime($folder . "/" . $file) . " " . time() . "\n";
                            continue;
                        }

			# Already exists?
			if (!in_array($shortpath,$done))
				{
				$count++;if ($count>$max) {return(true);}
				echo date('Y-m-d H:i:s    ');
				echo "Processing file: $fullpath\n";
				
				if ($collection==0 && $staticsync_autotheme)
					{
					# Make a new collection for this folder.
					$e=explode("/",$shortpath);
					$theme=ucwords($e[0]);
					$name=(count($e)==1?"":$e[count($e)-2]);
					echo date('Y-m-d H:i:s    ');
					echo "\nCollection $name, theme=$theme";
					$collection=sql_value("select ref value from collection where name='" . escape_check($name) . "' and theme='" . escape_check($theme) . "'",0);
					if ($collection==0)
						{
						sql_query("insert into collection (name,created,public,theme,allow_changes) values ('" . escape_check($name) . "',now(),1,'" . escape_check($theme) . "',0)");
						$collection=sql_insert_id();
						}
					}

				# Work out extension
				$extension=explode(".",$file);$extension=trim(strtolower($extension[count($extension)-1]));

                                // if coming from collections or la folders, assume these are the resource types
                                if (stristr(strtolower($fullpath),'collection services/curatorial')){
                                    $type = 5;
                                } elseif (stristr(strtolower($fullpath),'collection services/conservation')){
                                    $type = 5;
                                } elseif (stristr(strtolower($fullpath),'collection services/library_archives')){
                                    $type = 6;
                                } else {

                                # Work out a resource type based on the extension.
				$type=$staticsync_extension_mapping_default;
				reset ($staticsync_extension_mapping);
				foreach ($staticsync_extension_mapping as $rt=>$extensions)
					{
                                        if ($rt == 5 or $rt == 6){continue;} // we already eliminated those
					if (in_array($extension,$extensions)) {$type=$rt;}
					}
                                }
				
				# Formulate a title
				if ($staticsync_title_includes_path)
					{
					$title=str_ireplace("." . $extension,"",str_replace("/"," - ",$shortpath));
					$title=ucfirst(str_replace("_"," ",$title));
					}
				else
					{
					$title=str_ireplace("." . $extension,"",$file);
					}
				
				# Import this file
				$r=import_resource($shortpath,$type,$title,$staticsync_ingest);
				if ($r!==false)
					{
					# Add to mapped category tree (if configured)
					if (isset($staticsync_mapped_category_tree))
						{
						$basepath="";
						# Save tree position to category tree field
				
						# For each node level, expand it back to the root so the full path is stored.
						for ($n=0;$n<count($path_parts);$n++)
							{
							if ($basepath!="") {$basepath.="~";}
							$basepath.=$path_parts[$n];
							$path_parts[$n]=$basepath;
							}
						
						update_field ($r,$staticsync_mapped_category_tree,"," . join(",",$path_parts));
						#echo "update_field($r,$staticsync_mapped_category_tree," . "," . join(",",$path_parts) . ");\n";
						}			
					
					# StaticSync path / metadata mapping
					# Extract metadata from the file path as per $staticsync_mapfolders in config.php
					if (isset($staticsync_mapfolders))
						{
						foreach ($staticsync_mapfolders as $mapfolder)
							{
							$match=$mapfolder["match"];
							$field=$mapfolder["field"];
							$level=$mapfolder["level"];
							
							if (strpos("/" . $shortpath,$match)!==false)
								{
								# Match. Extract metadata.
								$path_parts=explode("/",$shortpath);
								if ($level<count($path_parts))
									{
									# Save the value
									print_r($path_parts);
									$value=$path_parts[$level-1];
									update_field ($r,$field,$value);
									echo " - Extracted metadata from path: $value\n";
									}
								}
							}
						}

                                        // add the timestamp from this run to the keywords field to help retrieve this batch later
                                        $currentkeywords = sql_value("select value from resource_data where resource = '$r' and resource_type_field = '1'","");
					if (strlen($currentkeywords) > 0){
						$currentkeywords .= ',';
					}
					update_field($r,1,$currentkeywords.$staticsync_run_timestamp);

					if (function_exists('staticsync_local_functions')){
						// if local cleanup functions have been defined, run them
						staticsync_local_functions($r);
					}

					# Add any alternative files
					$altpath=$fullpath . $staticsync_alternatives_suffix;
					if ($staticsync_ingest && file_exists($altpath))
						{
						$adh=opendir($altpath);
						while (($altfile = readdir($adh)) !== false)
							{
							$filetype=filetype($altpath . "/" . $altfile);
							if (($filetype=="file") && (substr($file,0,1)!=".") && (strtolower($file)!="thumbs.db"))
								{
								# Create alternative file
								global $lang;
								
								# Find extension
								$ext=explode(".",$altfile);$ext=$ext[count($ext)-1];
								
								$aref = add_alternative_file($r, $altfile, strtoupper($ext) . " " . $lang["file"], $altfile, $ext, filesize_unlimited($altpath . "/" . $altfile));
								$path=get_resource_path($r, true, "", true, $ext, -1, 1, false, "", $aref);
								rename ($altpath . "/" . $altfile,$path); # Move alternative file
								}
							}	
						}
					
                                        
                                        # check for alt files that match suffix list
					if ($staticsync_alt_suffixes){

                                            $ss_nametocheck = substr($file,0,strlen($file)-strlen($extension)-1);
                                            //review all files still in directory and see if they are alt files matching this one
                                            	$althandle=opendir($folder);
                                                while (($altcandidate = readdir($althandle)) !== false){
                                                    if (($filetype=="file") && (substr($file,0,1)!=".") && (strtolower($file)!="thumbs.db")){
                                                        # Find extension
                                                        $ext=explode(".",$altcandidate);$ext=$ext[count($ext)-1];
                                                        $altcandidate_name = substr($altcandidate,0,strlen($altcandidate)-strlen($ext)-1);
                                                        $altcandidate_validated = false;
                                                        foreach ($staticsync_alt_suffix_array as $sssuffix){
                                                            if ($altcandidate_name == $ss_nametocheck.$sssuffix){
                                                                $altcandidate_validated = true;
								$thisfilesuffix = $sssuffix;
                                                                break;
                                                            }
                                                        }
                                                        if ($altcandidate_validated){
                                                            echo date('Y-m-d H:i:s    ');
							    echo "    Attaching $altcandidate as alternative.\n";
                                                            $filetype=filetype($folder."/".$altcandidate);
                                                            # Create alternative file
                                                            global $lang;
							
								if (preg_match("/^_VERSO[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Verso";
								} elseif(preg_match("/^_DNG[0-9]*/i",$thisfilesuffix)){
									$alt_title = "DNG";
								} elseif(preg_match("/^_ORIG[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Original Scan";
								} elseif(preg_match("/^_TPV[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Title Page Verso";
								} elseif(preg_match("/^_TP[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Title Page";
								} elseif(preg_match("/^_COV[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Cover";
								} elseif(preg_match("/^_SCR[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Inscription";
								} elseif(preg_match("/^_EX[0-9]*/i",$thisfilesuffix)){
									$alt_title = "Enclosure";
								} else {
									$alt_title = $altcandidate;
								}

                                                            $aref = add_alternative_file($r, $alt_title, strtoupper($ext) . " " . $lang["file"], $altcandidate, $ext, filesize_unlimited($folder."/".$altcandidate));
                                                            $path=get_resource_path($r, true, "", true, $ext, -1, 1, false, "", $aref);
                                                            rename ($folder."/".$altcandidate,$path); # Move alternative file

                                                            global $alternative_file_previews;
                                                            if ($alternative_file_previews)
                                                                    {
                                                                    create_previews($r,false,$ext,false,false,$aref);
                                                                    }

                                                        }
                                                    }
                                                }		
                                        }
                                                
					# Add to collection
					if ($staticsync_autotheme)
						{
						sql_query("insert into collection_resource(collection,resource,date_added) values ('$collection','$r',now())");
						}

                                        // fix permissions
                                                
                                        // get directory to fix
                                           global $scramble_key;
                                           $permfixfolder = "/hne/rs/filestore/";
                                           for ($n=0;$n<strlen($r);$n++){
                                            $permfixfolder.=substr($r,$n,1);
                                            if ($n==(strlen($r)-1)) {$permfixfolder.="_" . substr(md5($r . "_" . $scramble_key),0,15);}
                                            $permfixfolder.="/";
                                          }


                                        exec("/bin/chown -R wwwrun $permfixfolder");
                                        exec("/bin/chgrp -R www $permfixfolder");


					}
				else
					{
					# Import failed - file still being uploaded?
					echo date('Y-m-d H:i:s    ');
					echo " *** Skipping file - it was not possible to move the file (still being imported/uploaded?) \n";
					}
				}
			else
				{
				# check modified date and update previews if necessary
				$filemod=filemtime($fullpath);
				if (array_key_exists($shortpath,$modtimes) && ($filemod>strtotime($modtimes[$shortpath])))
					{
					# File has been modified since we last created previews. Create again.
					$rd=sql_query("select ref,has_image,file_modified,file_extension from resource where file_path='" . (escape_check($shortpath)) . "'");
					if (count($rd)>0)
						{
						$rd=$rd[0];
						$rref=$rd["ref"];

						echo date('Y-m-d H:i:s    ');
						echo "Resource $rref has changed, regenerating previews: $fullpath\n";
						create_previews($rref,false,$rd["file_extension"]);
						sql_query("update resource set file_modified=now() where ref='$rref'");
						}
					}
				}
			}	
		}
	}


# Recurse through the folder structure.
ProcessFolder($syncdir);

echo date('Y-m-d H:i:s    ');
echo "...done.\n\n";

if (!$staticsync_ingest)
	{
	# If not ingesting files, look for deleted files in the sync folder and archive the appropriate file from ResourceSpace.
	echo "\nLooking for deleted files...";
	# For all resources with filepaths, check they still exist and archive if not.
	$rf=sql_query("select ref,file_path from resource where archive=0 and length(file_path)>0 and file_path like '%/%'");
	for ($n=0;$n<count($rf);$n++)
		{
		$fp=$syncdir . "/" . $rf[$n]["file_path"];
		if (!file_exists($fp))
			{
			echo "File no longer exists: " . $rf[$n]["ref"] . " (" . $fp . ")\n";
			# Set to archived.
			sql_query("update resource set archive=2 where ref='" . $rf[$n]["ref"] . "'");
			sql_query("delete from collection_resource where resource='" . $rf[$n]["ref"] . "'");
			}
		}
	# Remove any themes that are now empty as a result of deleted files.
	sql_query("delete from collection where theme is not null and length(theme)>0 and (select count(*) from collection_resource cr where cr.collection=collection.ref)=0;");
	
	# also set dates where none set by going back through filename until a year is found, then going forward and looking for month/year.
	/*
	$rf=sql_query("select ref,file_path from resource where archive=0 and length(file_path)>0 and (length(creation_date)=0 or creation_date is null)");
	for ($n=0;$n<count($rf);$n++)
		{
		}
	*/
	echo "...Complete\n";
	}

sql_query("update sysvars set value=now() where name='lastsync'");

clear_process_lock("staticsync");


function ss_is_alt($file){
    global $staticsync_alt_suffixes;
    // if this feature is not enabled, a file is never an alt file
    if(!$staticsync_alt_suffixes){ return false;}
    global $staticsync_alt_suffix_array;

    // strip extension
    $extension=explode(".",$file);
    $extension=trim(strtolower($extension[count($extension)-1]));
    $strippedfile = substr($file,0,strlen($file)-strlen($extension)-1);

    foreach ($staticsync_alt_suffix_array as $thesuffix){
        if (preg_match("/.+$thesuffix\$/", $strippedfile)){
            return true;
            //echo $file . "would return true\n";
            //exit;
        }
    }
    return false;
    //echo $strippedfile . "would return false\n";
    //exit;
}

?>
