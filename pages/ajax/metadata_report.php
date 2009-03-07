<?php 
include "../../include/db.php";
include "../../include/authenticate.php"; 
include "../../include/general.php"; 
include "../../include/resource_functions.php"; 



global $exiftool_path;
if (file_exists(stripslashes($exiftool_path) . "/exiftool") || file_exists(stripslashes($exiftool_path) . "/exiftool.exe"))
{

	$ref=getval("ref","");
	$ext=getval("ext","");

	$image=get_resource_path($ref,true,"",false,$ext);
	if (!file_exists($image)) {die("error");}

	#test if filetype is supported by exiftool
	$command=$exiftool_path."/exiftool -listf";
	$formats=shell_exec($command);
	$ext=strtoupper($ext);
	if (strlen(strstr($formats,$ext))<2){die("filetype $ext not supported");}
	
	#build array of supported tags
	$command=$exiftool_path."/exiftool -list";
	$supported_tags=shell_exec($command);
	$supported_tags=strtolower(str_replace("\n","",$supported_tags));
	$supported_tags_array=explode(" ",$supported_tags);
	
	#build array of writable tags
	$command=$exiftool_path."/exiftool -listw";
	$writable_tags=shell_exec($command);
	$writable_tags=strtolower(str_replace("\n","",$writable_tags));
	$writable_tags_array=explode(" ",$writable_tags);
	
	$command=$exiftool_path."/exiftool -s -t --NativeDigest --History --Directory " . escapeshellarg($image)." 2>&1";
	$report= shell_exec($command);
		          
	# get command that would be run on download:      

	# I'm commenting out the following line because I'm not sure why it would be used or how to handle it   
	# if ($exiftool_remove_existing) {$command="-EXIF:all -XMP:all= -IPTC:all= ";}
				
	$write_to=get_exiftool_fields();
	for($i=0;$i< count($write_to);$i++)
		{
		$field=explode(",",$write_to[$i]['exiftool_field']);
		foreach ($field as $field)
			{
			$field=strtolower($field);
			$simcommands[$field]['value']=str_replace("\"","\\\"",get_data_by_field($ref,$write_to[$i]['ref']));
			$simcommands[$field]['ref']=$write_to[$i]['ref'];
			}
		} 
		
	# build report:		 
	($exiftool_write)?$write_status="On":$write_status="Off";
	echo "<table>";
	echo "<tr><td width=\"150\">RESOURCESPACE</td><td width=\"150\">EXIFTOOL</td><td>EMBEDDED VALUE</td><td width=\"40%\">CURRENT DIFF (Exiftool Write: $write_status)</td></tr>";
	echo "<tr><td></td><td></td><td></td><td></td></tr>";
	$fields=explode("\n",$report);
	foreach ($fields as $field)
		{
		echo "<tr>";
		$tag_value=explode("\t",$field); 
		if (count($tag_value)==2)
			{
			$tag=$tag_value[0];
			$value=trim($tag_value[1]);
			$tag=trim(strtolower($tag));
			$tagprops="";
			if(in_array($tag,$supported_tags_array)){$tagprops.="r";}
			if(in_array($tag,$writable_tags_array)){$tagprops.="w";}
			if ($tagprops!="")$tagprops="($tagprops)";
					
			if(isset($simcommands[$tag]['value']))
				{
				#add notes to mapped fields	
				$RS_field_ref=$simcommands[$tag]['ref'];
				$RS_field_name=sql_query("select title from resource_type_field where ref = $RS_field_ref");
				$RS_field_name=$RS_field_name[0]['title'];
				echo "<td>".$RS_field_ref." - ".$RS_field_name."</td>$tag $tagprops</td>";
				} 
			else 
				{
				echo "<td></td>$tag $tagprops</td>";
				}
				
					
			#add diff arrow to fields that will likely change
			if(isset($simcommands[$tag]['value']))
				{
				if ($value!=$simcommands[$tag]['value'])
					{
					echo "<td>".$value."</td><td>--> ".$simcommands[$tag]['value']."</td>";
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
