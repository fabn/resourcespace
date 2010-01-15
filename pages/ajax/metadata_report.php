<?php 
include "../../include/db.php";
include "../../include/authenticate.php"; 
include "../../include/general.php"; 
include "../../include/resource_functions.php"; 


if (file_exists(stripslashes($exiftool_path) . "/exiftool") || file_exists(stripslashes($exiftool_path) . "/exiftool.exe"))
{

	$ref=getval("ref","");
	$resource=get_resource_data($ref);
	$ext=$resource['file_extension'];
	if ($ext==""){die($lang['nometadatareport']);}
	$resource_type=$resource['resource_type'];
	$type_name=get_resource_type_name($resource_type);

	$image=get_resource_path($ref,true,"",false,$ext);
	if (!file_exists($image)) {die($lang['error']);}

	#test if filetype is supported by exiftool
	$command=$exiftool_path."/exiftool -listf";
	$formats=shell_exec($command);
	$ext=strtoupper($ext);
	if (strlen(strstr($formats,$ext))<2){die($ext." ".$lang['notsupported']);}
	if (in_array(strtolower($ext),$exiftool_no_process)) {die($lang['exiftoolprocessingdisabledforfiletype']." ".$ext);}
	
	#build array of writable tags
	$command=$exiftool_path."/exiftool -listw";
	$writable_tags=shell_exec($command);
	$writable_tags=strtolower(str_replace("\n","",$writable_tags));
	$writable_tags_array=explode(" ",$writable_tags);
	
	$command=$exiftool_path."/exiftool -ver";
	$exiftool_version=shell_exec($command);
	
	if($exiftool_version>=7.4){
	#build array of writable formats
	$command=$exiftool_path."/exiftool -listwf";
	$writable_formats=shell_exec($command);
	$writable_formats=str_replace("\n","",$writable_formats);
	$writable_formats_array=explode(" ",$writable_formats);
	$file_writability=in_array($ext,$writable_formats_array); 
	}
	
	$command=$exiftool_path."/exiftool -s -t -G --NativeDigest --History --Directory " . escapeshellarg($image)." 2>&1";
	$report= shell_exec($command);
		          
	# get commands that would be run on download:      

	# I'm commenting out the following line because I'm not sure why it would be used or how to handle it   
	# if ($exiftool_remove_existing) {$command="-EXIF:all -XMP:all= -IPTC:all= ";}
				
	$write_to=get_exiftool_fields($resource_type);
	for($i=0;$i< count($write_to);$i++)
		{
		$fieldtype=$write_to[$i]['type'];	
		$field=explode(",",$write_to[$i]['exiftool_field']);
		 # write datetype fields as ISO 8601 date ("c") 
		 if ($fieldtype=="4"){$writevalue=date("c",strtotime(get_data_by_field($ref,$write_to[$i]['ref'])));}
		 else {$writevalue=get_data_by_field($ref,$write_to[$i]['ref']);}
		foreach ($field as $field)
			{
			$field=strtolower($field);
			$simcommands[$field]['value']=str_replace("\"","\\\"",$writevalue);
			$simcommands[$field]['ref']=$write_to[$i]['ref'];
			}
		} 

	# build report:		
	if(!isset($file_writability)){$file_writability=true;$writability_comment=$lang['notallfileformatsarewritable'];}else{$writability_comment="";}
	($exiftool_write&&$file_writability)?$write_status=$lang['metadatawritewillbeattempted']. $writability_comment:$write_status=$lang['nowritewillbeattempted'];?>
	
	<?php
	echo "<table class=\"InfoTable\">";
	echo "<tr><td colspan=\"5\">".$lang['resourcetype'].": ".$type_name."</td></tr>";
	echo "<tr><td width=\"150\">".$applicationname."</td><td width=\"50\">".$lang['group']."</td><td width=\"150\">".$lang['exiftooltag']."</td><td>".$lang['embeddedvalue']."</td><td>$write_status</td></tr>";
	$fields=explode("\n",$report);
	foreach ($fields as $field)
		{
		echo "<tr>";
		$tag_value=explode("\t",$field); 
		if (count($tag_value)==3)
			{
			$group=$tag_value[0];
			$tag=$tag_value[1];
			$value=trim($tag_value[2]);
			$tag=trim(strtolower($tag));
			$tagprops="";
			if(in_array($tag,$writable_tags_array)&&$file_writability){$tagprops.="w";}
			if ($tagprops!="")$tagprops="($tagprops)";
			
			if(isset($simcommands[$tag]['value']))
				{
				#add notes to mapped fields	
				$RS_field_ref=$simcommands[$tag]['ref'];
				$RS_field_name=sql_query("select title from resource_type_field where ref = $RS_field_ref");
				$RS_field_name=$RS_field_name[0]['title'];
				echo "<td>".$RS_field_ref." - ".i18n_get_translated($RS_field_name)."</td><td>$group</td><td>$tag $tagprops</td>";
				} 
			else 
				{
				echo "<td></td><td>$group</td><td>$tag $tagprops</td>";
				}
				
					
			#add diff arrow to fields that will likely change
			if(isset($simcommands[$tag]['value']))
				{
				if ($value!=$simcommands[$tag]['value'])
					{
					echo "<td>- ".$value."</td><td>+ ".$simcommands[$tag]['value']."</td>";
					}
				else
					{
					echo "<td>".$value."</td><td></td>";
					}	
				}	
				else 
					{
					echo "<td>".$value."</td><td></td>";
					}	
				
			echo "</tr>";
			}
		}	
	echo "</tr></table>";
	}
	else 
		{
		echo "Could not find Exiftool";
		}
