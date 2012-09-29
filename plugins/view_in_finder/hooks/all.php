<?php

//function hookView_in_finderViewResourceactions()
function hookView_in_finderViewRenderinnerresourcedownloadspace()
{
	global $resource;
	global $afp_server_path;
	global $access;
	global $staticSyncSyncDirField, $staticSyncDirs, $staticSyncUseArray;
	
	$restrictedAccess = false;
	
	$viewInFinder = get_plugin_config("view_in_finder");
	
	/*
	echo "<pre>";
	print_r($viewInFinder);
	echo "</pre>";
	*/
	
	// check to see if we are using permissions, and if yes then do they have access to this resource type?
	
	if ($viewInFinder['afpServerPath'] && $access != 0)
	{	
		$restrictedAccess = true;
	}	
	
	if (!$restrictedAccess)
	{
		//echo "Access Allowed... ";
		if ($resource["file_path"] != "") 
		{
			//echo "Got the file path…. ";
			if ($staticSyncUseArray) {
			
				$syncPath = get_data_by_field($resource['ref'],$staticSyncSyncDirField);	
				
				$found = false;
				$lSyncDir = "";
				
				if ($syncPath != "")
				{
					foreach ($staticSyncDirs as $tDir) {
						if (!$found) {
							if (strpos($syncPath,$tDir['syncdir']) !== false) {
								$found = true;
								$lSyncDir = $tDir['syncdir'];
							}		
						}
					}
					
					if ($found) {
					
						//echo "sync dir found : ". $lSyncDir;
						
						// check the afp path from the config.
						if (array_key_exists($lSyncDir,$viewInFinder['multiafpServerPath']))
						{
							$afp_link=$viewInFinder['multiafpServerPath'][$lSyncDir] . "/".$resource["file_path"];
						} else {
							// use the default	
							$afp_link=$viewInFinder['afpServerPath'] . "/".$resource["file_path"];
						}
						//echo $afp_link;
						
					} 
					
				} else {
					
					// $syncPath is empty or not fouond, use the default	
					$afp_link=$viewInFinder['afpServerPath'] . "/".$resource["file_path"];
					$found = true;
				}
				
			} else {
				if (array_key_exists('afpServerPath',$viewInFinder)) 
				{
					$afp_link=$viewInFinder['afpServerPath'] . "/".$resource["file_path"];
					$found = true;
				} 
			}
				
				if ($found)
				{
					echo "<table>";					echo '<tr class="DownloadDBlend">'; 
					echo '<td>Open Original File In Finder</td>'; 
					$fName = explode ("/",$resource["file_path"]);
					$fid = count($fName) - 1;
					echo '<td class="DownloadButton"><a href="'.$afp_link. '">'.$fName[$fid].'</a></ td>'; 
					echo '</tr>';
					echo "</table>";
				}
		}	
	}
}


?>