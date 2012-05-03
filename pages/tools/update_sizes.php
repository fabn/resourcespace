<?php
#
#
# Quick 'n' dirty script to update all preview images.
# It's done one at a time via the browser so progress can be monitored.
#
#
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("a")) {exit("Permission denied");}
include "../../include/general.php";
include "../../include/image_processing.php";
include "../../include/resource_functions.php";

$max=sql_value("select max(ref) value from resource",0);
$ref=getvalescaped("ref",1);

$resourceinfo=sql_query("select ref,file_extension from resource where ref='$ref'");
if (count($resourceinfo)>0)
	{
	$extension = $resourceinfo[0]['file_extension'];
	$file=get_resource_path($ref,true,"",false,$extension);
	$filesize = @filesize_unlimited($file);
	if (isset($imagemagick_path))
		{
        # Check ImageMagick identify utility.
        $identify_fullpath = get_utility_path("im-identify");
        if ($identify_fullpath==false) {exit("Could not find ImageMagick 'identify' utility.");}

		$prefix = '';
		# Camera RAW images need prefix
		if (preg_match('/^(dng|nef|x3f|cr2|crw|mrw|orf|raf|dcr)$/i', $extension, $rawext)) { $prefix = $rawext[0] .':'; }

		# Get image's dimensions.
        $identcommand = $identify_fullpath . ' -format %wx%h '. escapeshellarg($prefix . $file) .'[0]';
		$identoutput=run_command($identcommand);
		preg_match('/^([0-9]+)x([0-9]+)$/ims',$identoutput,$smatches);
		@list(,$sw,$sh) = $smatches;
		if (($sw!='') && ($sh!=''))
		  {
			$size_db=sql_query("select 'true' from resource_dimensions where resource = ". $ref);
			if (count($size_db))
				{
				sql_query("update resource_dimensions set width=". $sw .", height=". $sh .", file_size='$filesize' where resource=". $ref);
				}
			else
				{
				sql_query("insert into resource_dimensions (resource, width, height, file_size) values(". $ref .", ". $sw .", ". $sh .", '$filesize')");
				}
			}
		}
	else
		{
		# fetch source image size, if we fail, exit this function (file not an image, or file not a valid jpg/png/gif).
		if (!((@list($sw,$sh) = @getimagesize($file))===false))
		 	{
			$size_db=sql_query("select 'true' from resource_dimensions where resource = ". $ref);
			if (count($size_db))
				{
				sql_query("update resource_dimensions set width=". $sw .", height=". $sh .", file_size='$filesize' where resource=". $ref);
				}
			else
				{
				sql_query("insert into resource_dimensions (resource, width, height, file_size) values(". $ref .", ". $sw .", ". $sh .",'$file_size')");
				}
			}
		}
	?>
	<img src="<?php echo get_resource_path($ref,false,"pre",false)?>">
	<?php
	}
else
	{
	echo "Skipping $ref";
	}

if ($ref<$max && getval("only","")=="")
	{
	?>
	<meta http-equiv="refresh" content="0;url=<?php echo $baseurl?>/pages/tools/update_sizes.php?ref=<?php echo $ref+1?>"/>
	<?php
	}
else
	{
	?>
	Done.	
	<?php
	}
?>
	