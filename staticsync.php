<?
include "include/db.php";
include "include/general.php";
include "include/resource_functions.php";
include "include/image_processing.php";

set_time_limit(60*60*40);

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


function ProcessFolder($folder)
	{
	#echo "<br>processing folder $folder";
	global $syncdir,$nogo,$type,$max,$count,$done,$modtimes,$lastsync;
	
	$collection=0;
	
	# List all files in this folder.
	$dh=opendir($folder);
	while (($file = readdir($dh)) !== false)
		{
		$filetype=filetype($folder . "/" . $file);
		
		# -----FOLDERS-------------
		if ((($filetype=="dir") || $filetype=="link") && ($file!=".") && ($file!="..") && (strpos($nogo,"[" . $file . "]")===false))
			{
			# Recurse
			#echo "\n$file : " . filemtime($folder . "/" . $file) . " > " . $lastsync;
			if (true || (strlen($lastsync)=="") || (filemtime($folder . "/" . $file)>($lastsync-26000)))
				{
				ProcessFolder($folder . "/" . $file);
				echo ".";
				}
			}
			
		# -------FILES---------------
		if (($filetype=="file") && (substr($file,0,1)!=".") && (strtolower($file)!="thumbs.db"))
			{
			$fullpath=$folder . "/" . $file;
			$shortpath=str_replace($syncdir . "/","",$fullpath);
			
			# Already exists?
			if (!in_array($shortpath,$done))
				{
				$count++;if ($count>$max) {return(true);}

				echo "Processing file $fullpath\n";
				
				if ($collection==0)
					{
					# Make a new collection for this folder.
					$e=explode("/",$shortpath);
					$theme=ucwords($e[0]);
					$name=(count($e)==1?"":$e[count($e)-2]);
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

				if (($extension=="mov") || ($extension=="3gp") || ($extension=="avi") || ($extension=="mpg") || ($extension=="mp4"))	{$type=3;}
				elseif (($extension=="flv")) {$type=4;} 
				else {$type=1;}
				
				# Formulate a title
				$title=str_ireplace("." . $extension,"",str_replace("/"," - ",$shortpath));
				$title=ucfirst(str_replace("_"," ",$title));
				
				# Import this file
				$r=import_resource($shortpath,$type,$title);
				
				# Add to collection
				sql_query("insert into collection_resource(collection,resource,date_added) values ('$collection','$r',now())");
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

echo "...done. Looking for deleted files...";
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

sql_query("update sysvars set value=now() where name='lastsync'");

?>
