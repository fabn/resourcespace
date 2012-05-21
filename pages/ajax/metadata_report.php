<?php 
include "../../include/db.php";
include "../../include/authenticate.php"; 
include "../../include/general.php"; 
include "../../include/resource_functions.php"; 

if (!$metadata_report) {exit("This function is not enabled.");}

$exiftool_fullpath = get_utility_path("exiftool");
if ($exiftool_fullpath==false)
	{
	echo $lang["exiftoolnotfound"];
	}
else
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
	$command=$exiftool_fullpath . " -listf";
	$formats=run_command($command);
	$ext=strtoupper($ext);
	if (strlen(strstr($formats,$ext))<2){die(str_replace_formatted_placeholder("%extension", $ext, $lang['filetypenotsupported']));}
	if (in_array(strtolower($ext),$exiftool_no_process)) {die(str_replace_formatted_placeholder("%extension", $ext, $lang['exiftoolprocessingdisabledforfiletype']));}
	
	#build array of writable tags
	$command=$exiftool_fullpath . " -listw";
	$writable_tags=run_command($command);
	$writable_tags=strtolower(str_replace("\n","",$writable_tags));
	$writable_tags_array=explode(" ",$writable_tags);
	
	$command=$exiftool_fullpath . " -ver";
	$exiftool_version=run_command($command);
	
	if($exiftool_version>=7.4){
	#build array of writable formats
	$command=$exiftool_fullpath . " -listwf";
	$writable_formats=run_command($command);
	$writable_formats=str_replace("\n","",$writable_formats);
	$writable_formats_array=explode(" ",$writable_formats);
	$file_writability=in_array($ext,$writable_formats_array); 
	}
	
	$command=$exiftool_fullpath . " -s -t -G --filename --exiftoolversion --filepermissions --NativeDigest --History --Directory " . escapeshellarg($image)." 2>&1";
	$report= run_command($command);
		          
	# get commands that would be run on download:      

	# I'm commenting out the following line because I'm not sure why it would be used or how to handle it   
	# if ($exiftool_remove_existing) {$command="-EXIF:all -XMP:all= -IPTC:all= ";}
				
	$write_to=get_exiftool_fields($resource_type);
	for($i=0;$i< count($write_to);$i++)
		{
		$fieldtype=$write_to[$i]['type'];	
		$fields=explode(",",$write_to[$i]['exiftool_field']);
		 # write datetype fields as ISO 8601 date ("c") 
		 if ($fieldtype=="4"){$writevalue=date("c",strtotime(get_data_by_field($ref,$write_to[$i]['ref'])));}
		 else {$writevalue=get_data_by_field($ref,$write_to[$i]['ref']);}
		foreach ($fields as $field)
			{
			$field=strtolower($field);
			$simcommands[$field]['value']=str_replace("\"","\\\"",$writevalue);
			$simcommands[$field]['ref']=$write_to[$i]['ref'];
			}
		} 

	# build report:		
	if(!isset($file_writability)){$file_writability=true;$writability_comment=$lang['notallfileformatsarewritable'];}else{$writability_comment="";}
	($exiftool_write&&$file_writability)?$write_status=$lang['metadatawritewillbeattempted']." ".$writability_comment:$write_status=$lang['nowritewillbeattempted'];?>
	
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
			$group=trim(strtolower($group));
			$tagprops="";
			if((in_array($tag,$writable_tags_array)&&$file_writability)){$tagprops.="w";}
			if ($tagprops!="")$tagprops="($tagprops)";
			
			if(isset($simcommands[$tag]['value'])||isset($simcommands[$group.":".$tag]))
				{
				#add notes to mapped fields	
				if (isset($simcommands[$tag]['ref'])){
					$RS_field_ref=$simcommands[$tag]['ref'];
				}
				elseif (isset($simcommands[$group.":".$tag])){
					$RS_field_ref=$simcommands[$group.":".$tag]['ref'];
				}
				$RS_field_name=sql_query("select title from resource_type_field where ref = $RS_field_ref");
				$RS_field_name=$RS_field_name[0]['title'];
				echo "<td>". $RS_field_ref . " - " . lang_or_i18n_get_translated($RS_field_name, "fieldtitle-") . "</td><td>$group</td><td>$tag $tagprops</td>";
				} 
			else 
				{
				echo "<td></td><td>$group</td><td>$tag $tagprops</td>";
				}
				
					
			#add diff arrow to fields that will likely change
			if(isset($simcommands[$tag]['value'])||isset($simcommands[$group.":".$tag]['value']))
				{
				if (isset($simcommands[$tag]['value'])){ $newvalue=	$simcommands[$tag]['value'];}
				else if (isset($simcommands[$group.":".$tag]['value'])){ $newvalue=	$simcommands[$group.":".$tag]['value'];}	

                // remove a leading comma from value in database when comparing.
                if (substr($newvalue,0,1) == ',')
					{
					$newvalue = substr($newvalue,1);
					}
                	
				if ($value!=$newvalue)
					{
					echo "<td>- ".$value."</td><td>+ ".$newvalue."</td>";
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
