<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";
//include "../../../include/config.php";

function displayAfpSinglePath() {
		global $viewInFinder;
		
		echo '<p><label for="afpServerPath">AFP Server Path : </label><input name="afpServerPath" type="text" value="';
    	echo $viewInFinder['afpServerPath']; 
    	echo '" size="60" /></p>';
	
}



$useMultiples = false;

global $staticSyncUseArray, $staticSyncDirs;

//1st thing to check.are we using multiple sync paths?
if (isset($staticSyncUseArray) && $staticSyncUseArray) {
	// yes, we're using myltiple paths!
	//print_r($staticSyncDirs);	
	// get a count of the sync paths!
	$pathCount = count($staticSyncDirs);
	$useMultiples = true;
}


if (getval("submit","")!="") {

	$viewInFinder=array();
	
	
	if ($useMultiples) {
		
		$arrRef = 0;
	
		foreach ($staticSyncDirs as $tDir) {
			$postName = "afpServerPath" . $tDir['syncdir'];
			$viewInFinder['multiafpServerPath'][$tDir['syncdir']] = $_POST[$postName];
			$arrRef++;
		}		
	
	} 
	$viewInFinder['afpServerPath'] = $_POST['afpServerPath'];
	
	
	$viewInFinder['usePerms'] = $_POST['usePerms'];
	
	//print_r($_POST);
	//exit;
	
	set_plugin_config("view_in_finder", $viewInFinder);
	

} else {
	
	$viewInFinder = get_plugin_config("view_in_finder");
	if ($viewInFinder == null){
	    $viewInFinder['afpServerPath'] = "afp://example.com/share_name";
		$viewInFinder['usePerms'] = 0;
	}
	if (!array_key_exists('afpServerPath',$viewInFinder)) {
		$viewInFinder['afpServerPath'] = "afp://example.com/share_name";
	}
	if (!array_key_exists("usePerms",$viewInFinder)) {
		$viewInFinder['usePerms'] = 0;
	}	
	if (!array_key_exists('multiafpServerPath',$viewInFinder)) {
		$viewInFinder['multiafpServerPath'] = array();
	}
}

include "../../../include/header.php";


echo "path count is" . $pathCount;

?>
<div class="BasicsBox"> 

  <h2>&nbsp;</h2>

  <h1>View In Finder Configuration</h1>

  <div class="VerticalNav">

    <form id="form1" name="form1" method="post" action="">
    <?php
    if ($useMultiples) {
    	/* we have multiple paths, so we need to map them to the relevant shares
    	*/
    	$arrRef = 0;
    	foreach ($staticSyncDirs as $tDir) {
    		echo '<p><label for="afpServerPath' . $tDir['syncdir'].'">Map Sync Dir: ' . $tDir['syncdir'] . ' to : </label>';
    		echo '<input name="afpServerPath'. $tDir['syncdir'] .'" type="text" value="';
    		// now test to see if this has alrady been mapped!
    		if (isset($viewInFinder['multiafpServerPath'][$tDir['syncdir']])) {
    			echo $viewInFinder['multiafpServerPath'][$tDir['syncdir']];
    		}
    		echo '" size="60" />';
    		echo "</p>";
    		//echo $tDir['syncdir'] . "</br>";	
    		$arrRef++;
    	}
    	
    	// display the default path;
    	echo "<h2>Please enter a default path for any legacy static sync'd files</h2></br>";
    	displayAfpSinglePath();
    	
    } else {
    	/* we only have one path to deal with!
    	*/
    	displayAfpSinglePath();
    }    
    ?>
    <p><label for="usePerms">Use Restrict Permissions:</label><input name="usePerms" type="checkbox" value="1" 
      <?php 
      	if ($viewInFinder['usePerms'] == 1) {
      		echo " checked=checked ";
      	}
      ?>
      
      size="60" /></p>        
   
        <input type="submit" name="submit" value="<?php echo $lang["save"]?>"/>

    </form>
	</br>
	<p>Enter the server path to the root of the static sync directory, either afp://example.com/sharename or smb://example.com/sharename. There is a problem with NFS shares.</P>
	<p>If you want the plugin to respect restricted access to asset types such as photo etc, tick the Use Restrict Permissions checkbox</p>
  </div>	

