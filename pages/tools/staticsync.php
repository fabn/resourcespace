<?php
include "../../include/db.php";
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*40);

if ($argc == 2)
{
	if ( in_array($argv[1], array('--help', '-help', '-h', '-?')) )
	{
		echo "To clear the lock after a failed run, ";
  		echo "pass in '--clearlock', '-clearlock', '-c' or '--c'.\n";
  		exit("Bye!");
  	}
	else if ( in_array($argv[1], array('--clearlock', '-clearlock', '-c', '--c')) )
	{
		if ( is_process_lock("staticsync") )
		{
			clear_process_lock("staticsync");
		}
	}
	else
	{
		exit("Unknown argv: " . $argv[1]);
	}
} 


# Check for a process lock
if (is_process_lock("staticsync")) {exit("Process lock is in place. Deferring.");}
set_process_lock("staticsync");

echo "Preloading data...";
$max=10000;
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
	global $syncdir,$nogo,$max,$count,$done,$modtimes,$lastsync, $ffmpeg_preview_extension, $staticsync_autotheme, $staticsync_folder_structure,$staticsync_extension_mapping_default, $staticsync_extension_mapping, $staticsync_mapped_category_tree,$staticsync_title_includes_path, $staticsync_ingest, $staticsync_mapfolders,$staticsync_alternatives_suffix;
	
	$collection=0;
	
	echo "Processing Folder: $folder\n";
	
	# List all files in this folder.
	$dh=opendir($folder);
	while (($file = readdir($dh)) !== false)
		{
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
		if (($filetype=="file") && (substr($file,0,1)!=".") && (strtolower($file)!="thumbs.db"))
			{
			# Already exists?
			if (!in_array($shortpath,$done))
				{
				$count++;if ($count>$max) {return(true);}

				echo "Processing file: $fullpath\n";
				
				if ($collection==0 && $staticsync_autotheme)
					{
					# Make a new collection for this folder.
					$e=explode("/",$shortpath);
					$theme=ucwords($e[0]);
					$themesql="theme='".ucwords(escape_check($e[0]))."'";
					$themecolumns="theme";
					$themevalues="'".ucwords(escape_check($e[0]))."'";
					
					if ($staticsync_folder_structure){
						for ($x=0;$x<count($e)-1;$x++){
							if ($x==0){} else {$themeindex=$x+1;
							global $theme_category_levels;
							if ($themeindex>$theme_category_levels){
								$theme_category_levels=$themeindex;
								if ($x==count($e)-2){echo "\n\nUPDATE THEME_CATEGORY_LEVELS TO $themeindex IN CONFIG!!!!\n\n";}
							}
							$themesql.=" and theme".$themeindex."='".ucwords(escape_check($e[$x]))."'";
							$themevalues.=",'".ucwords(escape_check($e[$x]))."'";
							$themecolumns.=",theme".$themeindex;
							}
						}
					}
					
					$name=(count($e)==1?"":$e[count($e)-2]);
					echo "\nCollection $name, theme=$theme";
					$collection=sql_value("select ref value from collection where name='" . escape_check($name) . "' and " . $themesql ,0);
					if ($collection==0){
						sql_query("insert into collection (name,created,public,$themecolumns,allow_changes) values ('" . escape_check($name) . "',now(),1,".$themevalues.",0)");
						$collection=sql_insert_id();
					}
				}
						

				# Work out extension
				$extension=explode(".",$file);$extension=trim(strtolower($extension[count($extension)-1]));

				# Work out a resource type based on the extension.
				$type=$staticsync_extension_mapping_default;
				reset ($staticsync_extension_mapping);
				foreach ($staticsync_extension_mapping as $rt=>$extensions)
					{
					if (in_array($extension,$extensions)) {$type=$rt;}
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
								
								$aref=add_alternative_file($r,$altfile,str_replace("?",strtoupper($ext),$lang["originalfileoftype"]),$altfile,$ext,filesize($altpath . "/" . $altfile));
								$path=get_resource_path($r, true, "", true, $ext, -1, 1, false, "", $aref);
								rename ($altpath . "/" . $altfile,$path); # Move alternative file
								}
							}	
						}
					
					# Add to collection
					if ($staticsync_autotheme)
						{
						$test="";	
						$test=sql_query("select * from collection_resource where collection='$collection' and resource='$r'");
						if (count($test)==0){
							sql_query("insert into collection_resource(collection,resource,date_added) values ('$collection','$r',now())");
							}
						}
					}
				else
					{
					# Import failed - file still being uploaded?
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

echo "...done.";

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


?>
