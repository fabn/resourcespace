<?php
/*

Recovery script to allow recovery of resources from an existing filestore
for which the scramble key and/or MySQL database has been lost.

This script is designed to be run from the command line within the 
new (target) ResourceSpace installation. You will need to set the source
filestore location in the config section below.

Note that as of 3/2012, there are substantial weaknesses in the filestore 
copies of data that make it more difficult to fully recover all data. Restoring
data from a MySQL backup would be preferable if possible. Future RS versions
may correct some of these limitations.

Caveats: 
   * This script is designed to flow into an empty ResourceSpace database,
   and to recreate the original ResourceIDs. If there are existing resources
   in the target database, it will not work correctly. (This could be fixed
   by not reusing the old ResourceIDs, but would require some additional
   programming.)

   Note that after using this script you will need to:
	1) Drop columns such as field8, field12, etc. from the 
	   resource table. The system will then recreate them 
	   with the proper data.

	2) Reindex the database using the pages/tools/reindex.php script.

*/

//----------------------CONFIG -------------------------//

// old filestore to recover files from (include trailing slash)
$source_filestore_path = ''; 

// recovery will stop when counter reaches this number
$max_resources_to_recover = 200000;

// first resource to recover
$start_id = 1;


//----------------------BEGIN MAIN SCRIPT -------------------------//

if (!(strlen($source_filestore_path) > 0)){
	echo "\n\nError: You must specify the source filestore by editing \nthe PHP script. Aborting.\n\n";
	exit;
}


include dirname(__FILE__) . "/../../include/db.php";
include dirname(__FILE__) . "/../../include/general.php";
include dirname(__FILE__) . "/../../include/resource_functions.php";
include dirname(__FILE__) . "/../../include/image_processing.php";

$res_skipped = 0;
$res_checked = 0;
$res_found = 0;
$res_imported = 0;

$optionlists = array();

for ($oldid=$start_id; $oldid < $max_resources_to_recover; $oldid++){
	$res_checked++;
	//echo "Trying resource $oldid.\n";
	 $idarr = str_split($oldid);
	 $lastchar = count($idarr)-1;
	 $prepath = $source_filestore_path;
	 for ($i = 0; $i < $lastchar; $i++){
	 	$prepath .= $idarr[$i] . "/";
	 }
	 
	 if (is_dir($prepath)){
		 $candidates = scandir($prepath);
		 $dirfound = false;
		 $thedir = '';
		 foreach ($candidates as $candidate){
			if (substr($candidate,0,2) == $idarr[$lastchar] . "_"){
				$thedir = $candidate;
				$dirfound = true;
				break;
			}
		 }
		 if ($dirfound && is_dir($prepath . $thedir)){
			$finaldir = $prepath . $thedir;	 
			echo "Resource $oldid found at $finaldir\n";
			// ok, we've got a ResourceID and we know where it's stored. Score!
			// now we need to figure out which files to save
			
			$files = scandir($finaldir);
			
			$metadata_file = '';
			$resource_file = '';
			$alternate_files = array();
			
			foreach ($files as $file){
				if ($file == "."|$file == ".."){
					continue;
				} elseif ($file == 'metadump.xml'){
					$metadata_file = $finaldir . '/' . $file;
				} elseif (preg_match("/^" . $oldid . "_alt_(\d+)_.+/",$file,$matches)) {
					// alternate file
					$altid = $matches[1];
					$alternate_files[$altid] = $finaldir . '/' . $file;
				} elseif (substr($file,0,strlen($oldid)+1) == $oldid . '_' ){
					$filext = pathinfo($finaldir . '/' . $file, PATHINFO_EXTENSION);
					if ($resource_file == '' || $filext <> 'jpg'){
						// prefer non-jpeg files if there is a jpeg and a non-jpeg that both 
						// look like original resources. Unfortunately, the system names some previews
						// like jpegs of the first frame of movies as if they were original resources.
						// FIXME -- record extension in the metadump file so that we don't have to 
						// guess at this in the future.
						$resource_file = $finaldir . '/' . $file;
					}
				}

			}
			
			if ($resource_file <> ''){
				$res_found++;
				echo "         resource_file: $resource_file\n";
				echo "         metadata_file: $metadata_file\n";
				foreach ($alternate_files as $key=>$value){
					echo "         alternate_files: $key / $value\n";			
				}
			
				recover_resource_files($oldid,$resource_file,$metadata_file,$alternate_files);
			}elseif ($metadata_file<>'' || count($alternate_files) > 0){
				echo "WARNING: metadata or alternate files found without associated original for resource $oldid.\n";
			}

		 }
	 }
}


// deal with fields
echo "FIELDS\n";
foreach (array_keys($fields_title) as $ref){

	echo "$ref | ";
	echo $fields_title[$ref] . " | ";
	echo $fields_embeddedequiv[$ref] . " | ";
	echo $fields_type[$ref] . " | ";
	echo "\n";

}

//print_r($optionlists);
foreach(array_keys($optionlists) as $thekey){
	echo "field $thekey:\n";
	$valuelist = '';
	foreach($optionlists[$thekey] as $theval){
		echo "value: $theval\n";
		$valuelist .= ",$theval";
	}

	$valuelist = escape_check($valuelist);

	$query = "update resource_type_field set options = '$valuelist' where ref = '$thekey'";
	//echo "$query\n\n";
	sql_query($query);

}

echo "Last ID checked was $oldid\n";


function recover_resource_files($id,$res,$meta,$alts){
	// this is where we actually start doing the import
	global $res_skipped;
	$ext = pathinfo($res, PATHINFO_EXTENSION);
	if (sql_value("select count(*) value from resource where ref = '$id'","0") > 0){
		echo "resource $id already exists! Skipping!\n";
		$res_skipped++;
		return false;
	} else {
		echo "new resource\n";

		// 1: photo, 2:document, 3:video, 4: audio
		// going to have to guess at the type for now, since the xml file did not record it. Fixme - xml file should have this.
		global $ffmpeg_supported_extensions, $ffmpeg_audio_extensions,$camera_autorotation_ext, $unoconv_extensions;
		if (in_array($ext,$ffmpeg_supported_extensions)){
			$rtype='3';
		}elseif(in_array($ext,$ffmpeg_audio_extensions)){
			$rtype='4';
		}elseif(in_array($ext,$unoconv_extensions)){
			$rtype='2';
		}elseif(in_array($ext,$camera_autorotation_ext)){
			$rtype='1';
		} else {
			$rtype='null';
		}
			
		$sql = "insert into resource (ref, title, file_extension,resource_type) values ('$id','RECOVERED','$ext','$rtype')";
		sql_query($sql);
		$newpath = get_resource_path($id,true,'',true,$ext);
		if (!copy ($res,$newpath)){
			echo "ERROR copying $res.\n";
			die;
		}
		// fixme: add alternates

		foreach ($alts as $altid=>$altpath){
			$filext = pathinfo($altpath, PATHINFO_EXTENSION);
			$filesize = filesize_unlimited($altpath);
			$newid = add_alternative_file($id,"$altid.$filext",'','',$filext,$filesize);
			$newpath = get_resource_path($id,true,"",false,$filext,-1,1,false,"",$newid);
			if (!copy ($altpath,$newpath)){
                        	echo "ERROR copying $res.\n";
                        	die;
                	}
			echo "previews: " . create_previews($id,false,$filext,false,false,$newid);
			echo "\n\n";
		}

		create_previews($id,false,$ext);
		populate_metadata_from_dump($id,$meta);
	}
}


function populate_metadata_from_dump($id,$meta){
	global $fields_title, $fields_embeddedequiv, $fields_type, $optionlists;

	// read in the metadata file and dump it into the right places in the database
	$metadump = file_get_contents($meta);
	// lazy solution: the resourcespace XML namespace is not formally defined
	// and thus the docs will not validate. For now we're just going to do some
	// regex magic to get rid of the namespaces alltogether. Fixme - would be 
	// nice to make the metadump files validate
	$metadump = preg_replace('/([<\/])([a-z0-9]+):/i','$1$2',$metadump);
	$metadump = preg_replace('/(resourcespace):(resourceid="\d+">)/i','$1$2',$metadump);
	$metadump = stripInvalidXml($metadump);
	//echo $metadump;
	$xml = new SimpleXMLElement($metadump);
	//print_r($xml);
	//echo "\n field ref for title is " . $xml->dctitle['rsfieldref'] . "\n";
	foreach ($xml as $fieldxml){
		if ($fieldxml == ''){
			continue;
		}
		$value = $fieldxml;
		$rsfieldtitle = $fieldxml['rsfieldtitle'];
		$rsembeddedequiv = $fieldxml['rsembeddedequiv'];
		$rsfieldref = $fieldxml['rsfieldref'];
		$rsfieldtype = $fieldxml['rsfieldtype'];
		
		

		echo "\n==========\n";
		echo "   rsfieldtitle: $rsfieldtitle\n";
		echo " rsembeddedequiv: $rsembeddedequiv\n";
		echo "     rsfieldref: $rsfieldref\n";
		echo "    rsfieldtype: $rsfieldtype\n";
		echo "          value: $value\n";

		$rsfieldtitle=escape_check($rsfieldtitle);

		$newid = sql_value("select ref value from resource_type_field where title = '$rsfieldtitle' and type = '$rsfieldtype'",0);
		if ($newid > 0){
			$finalid = $newid;
		} else {
			if ($rsfieldtype=='7'){
				// category trees are too complicated to construct, so we're going to treat them as text fields for now.
				$rsfieldtype='1';
			}
			$sql = "insert into resource_type_field (title,type,name) values ('$rsfieldtitle','$rsfieldtype','$rsembeddedequiv')";
			$result = sql_query($sql);
			$finalid = sql_insert_id();	
		}



		if ($rsfieldtype == 2 || $rsfieldtype == 3){
			if (!isset($optionlists[$finalid])){
				$optionlists[$finalid] = array();
			}
			if (!in_array($value,$optionlists[$finalid])){
				$optionlists[$finalid][] = $value;	
			}
		}


		$fields_title["$rsfieldref"] = $rsfieldtitle;
		$fields_embeddedequiv["$rsfieldref"] = $rsembeddedequiv;
		$fields_type["$rsfieldref"] = $rsfieldtype;
		
		$value = escape_check($value);

		$sql = "insert into resource_data (resource, resource_type_field, value) values ('$id','$rsfieldref','$value')";
		sql_query($sql);
	}
}







/**
 * Removes invalid XML
 *
 * @access public
 * @param string $value
 * @return string
 * SOURCE: http://stackoverflow.com/questions/3466035/how-to-skip-invalid-characters-in-xml-file-using-php
 */
function stripInvalidXml($value)
{
    $ret = "";
    $current;
    if (empty($value)) 
    {
        return $ret;
    }

    $length = strlen($value);
    for ($i=0; $i < $length; $i++)
    {
        $current = ord($value{$i});
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x20) && ($current <= 0xD7FF)) ||
            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) && ($current <= 0x10FFFF)))
        {
            $ret .= chr($current);
        }
        else
        {
            $ret .= " ";
        }
    }
    return $ret;
}

?>
