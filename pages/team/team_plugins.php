<?php
/**
 * Plugins management interface (part of team center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Admin
 * @author Brian Adams <wreality@gmail.com>
 * @todo Language integration
 * @todo Replace get urls with form posts.
 */
include "../../include/db.php";
/**
 * Only accessable to users with 'a' permission.
 */
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "../../include/general.php";

$plugins_dir = dirname(__FILE__)."/../../plugins/";

$avail_plugins = array();
if (isset($_REQUEST['i'])){ # Activate a plugin
    # Validate the plugin name exists in the plugins directory.
    $new_inst = getvalescaped('i','');
    if ($new_inst!=''){
        activate_plugin($new_inst);   
    }
    redirect('pages/team/team_plugins.php');    # Redirect back to the plugin page so plugin is actually activated. 
    
}
elseif (isset($_REQUEST['r'])){ # Deactivate a plugin
    # Validate the plugin is actually installed.
    $remove_name = getvalescaped('r','');
    if ($remove_name!=''){
        deactivate_plugin($remove_name); 
    }
    redirect('pages/team/team_plugins.php');    # Redirect back to the plugin page so plugin is actually deactivated.
}
elseif (isset($_REQUEST['p'])){ # Purge a plugin's configuration (if stored in DB)
    $purge_name = getvalescaped('p','');
    if ($purge_name!=''){
        purge_plugin_config($purge_name);
    }
    redirect('pages/team/team_plugins.php');    # Redirect back to the plugin page. (Remove ?p= from URL (TEMP FIX))
}
/****
  The delete functionality is commented out of the svn version since it hasn't
  been fully checked to avoid vulnerabilities.  Feel free to add anything to
  help make it more secure.
 ****/
/*
elseif (isset($_REQUEST['d'])){ # Delete a plugin from the plugins directory.
    $delete_name = getvalescaped('d','');
    if ($delete_name!='' && $delete_name!='.' && $delete_name!='..' && strpos($delete_name, '/')===false && strpos($delete_name, '\\')===false){ # Prevent this script being used to delete anything else.
        #Check that the plugin is not activated.
        $c = sql_value("SELECT inst_version as value from plugins WHERE name='$delete_name'",'');
        if ($c=='' || $c == null){
            rcRmdir($plugins_dir.$delete_name);
        }
    }
    redirect('pages/team/team_plugins.php');    # Redirect back to the plugin page (Remove ?d= from URL (TEMP FIX))
}
*/
elseif (isset($_REQUEST['submit'])){ # Upload a plugin tar.gz file. 
	if ($_FILES['pfile']['error'] == 0){
	    include "../../lib/pcltar/pcltar.lib.php";
	    $tmp_dir = dirname(__FILE__).'/../../filestore/tmp/';
	    if(move_uploaded_file($_FILES['pfile']['tmp_name'], $tmp_dir.$_FILES['pfile']['name'])==true){
	         $rejected = false;
	         $filelist = PclTarList($tmp_dir.$_FILES['pfile']['name']);
	    	 if(is_array($filelist)){
	    	 	 for($i=0;$i<count($filelist)-1;$i++){ # Loop through the file list to create an array we can use php's functions with.
	    	         $filearray[] = $filelist[$i]['filename'];
	    	 	 }
	    	     # Some security checks.
	    	     # TODO: This should check each file path in the list to make sure one doesn't slip by unnoticed.
	    	     if ($filearray[0][0]=='/' || $filearray[0][0] == '\\') {# Paths are absolute.  Reject the plugin.
	    	     	 $rejected = true;
	    	     	 $rej_reason = 'Archive contains absolute paths.  Unable to process for security reasons.';	
	    	     }
	    	     elseif (array_search('..', $filearray)!==false) {# Archive may contain ../ directories (Security risk)
	    	     	$rejected = true;
	    	     	$rej_reason = 'Archive may contain parent directories in path.  Unable to process for security reasons.';
	    	     }
	    	     if(!$rejected){
	    	         # Locate the plugin name based on highest directory in structure.
	    	         # This loop will also look for the .yaml file (to avoid having to loop twice).
	    	         $exp_path = explode('/',$filearray[0]);
	    	         $yaml_index = false;
	    	         $u_plugin_name = $exp_path[0];
	    	         foreach ($filearray as $key=>$value){
	    	             $test = explode('/',$value);
	    	             if ($u_plugin_name != $test[0]){
	    	                 $rejected = true;
	    	                 $rej_reason = 'Archive contains multiple root paths.  Not valid for automated installation.';
	    	                 break;
	    	             }
	    	             # TODO: This should be a regex to make sure the file is in the right position (<pluginname>/<pluginname>.yaml)
	    	             if (strpos($value,$u_plugin_name.'.yaml')!==false){
	    	                 $yaml_index = $key;
	    	             }
	    	         }
	    	         # TODO: We should extract the yaml file if it exists and validate it.
	    	         if ($yaml_index===false){
	    	             $rejected = true;
	    	             $rej_reason = 'Archive does not contain a meta file.  Not valid for automated installation.';
	    	         }
	    	         if (!$rejected){
	    	             if (PclTarExtract($tmp_dir.$_FILES['pfile']['name'], '../../plugins/')!='1'){
	    	             	#TODO: If the new plugin is already activated we should update the DB with the new yaml info.
	    	                #TODO: Problem with PclTar library.  Not sure what's happening
	    	                /* $rejected = true;
	    	                 echo $error;
	    	             	$rej_reason = 'There was a problem extracting the archive:'.PclErrorString(PclErrorCode());
	    	             	*/
	    	             }
	    	         }   	         
	    	     }
	    	 }
	    	 else {
	    	     $rejected = true;
	    	     $rej_reason = 'Archive format not supported.';
	    	 }	 
	    }
	}
}
$inst_plugins = sql_query("SELECT name, config_url, descrip, author, inst_version, update_url, info_url FROM plugins WHERE inst_version>=0");

# Build an array of available plugins.
$dirh = opendir($plugins_dir);
$plugins_avail = array();
while (false !== ($file = readdir($dirh))) {
    if (is_dir($plugins_dir.$file)&&$file[0]!='.'){
        #Check if the plugin is already activated.
        $status = sql_query("SELECT inst_version, config FROM plugins WHERE name='".$file."'");
        if ((count($status)==0) || ($status[0]['inst_version']==null)){
            # Look for a <pluginname>.yaml file.
            $plugin_yaml = get_plugin_yaml($plugins_dir.$file.'/'.$file.'.yaml', false);
            foreach ($plugin_yaml as $key=>$value){
                $plugins_avail[$file][$key] = $value ;
            }
            if (get_plugin_config($file)!=null)
            	$plugins_avail[$file]['config']=true;
           	else
           		$plugins_avail[$file]['config']=false;
        }        
    }
}
?>
<script src="../../lib/js/jquery-1.3.1.min.js" type="text/javascript"> </script>
<?php include "../../include/header.php"; ?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["pluginmanager"]?></h1>
  <p><?php echo text("introtext")?></p>
    <h2>Currently Installed Plugins</h2>
    <?php if (count($inst_plugins)>0){ ?>
    <div class="Listview">
    <table class= "ListviewStyle" cellspacing="0" cellpadding="0" border="0">
    <thead>
    <?php # TODO: Headers should use $lang[] structure ?>
    <tr class="ListviewTitleStyle"><td>Plugin Name</td><td>Description</td><td>Author</td><td>Installed Version</td><td></td></tr>
    </thead>
    <tbody>
    <?php foreach ($inst_plugins as $p){
        echo '<tr>';
        echo "<td>{$p['name']}</td><td>{$p['descrip']}</td><td>{$p['author']}</td><td>".sprintf("%.1f",$p['inst_version'])."</td>";
        echo '<td>';
        echo "<a href=\"team_plugins.php?r={$p['name']}\">&gt; Deactivate</a> ";
        if ($p['info_url']!='')
            echo '<a href="'.$p['info_url'].'" target="_blank">&gt; More Info</a> ';
        if ($p['config_url']!='')
        	echo '<a href="'.$baseurl.$p['config_url'].'">&gt; '.$lang['options'].'</a> ';
        echo '</td></tr>';
    } ?>
    </tbody>
    </table>
    </div>
    <?php } else { ?>
    <?php #TODO: Language integration. ?>
    <p>No plugins currently installed.</p>
    <?php } ?>
    <h2>Available Plugins</h2>
    <div class="Listview">
    <table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
    <thead>
    <?php #TODO: Headers should use $lang[] structure ?>
    <tr class="ListviewTitleStyle"><td>Plugin Name</td><td>Description</td><td>Author</td><td>Version</td><td></td></tr>
    </thead>
    <tbody>
    <?php 
   	foreach($plugins_avail as $p){
        echo '<tr><td>'.$p['name'].'</td><td>'.$p['desc'].'</td><td>'.$p['author'].'</td>';
		if ($p['version'] == 0)
			echo '<td>?</td>';
		else
			echo '<td>'.$p['version'].'</td>';
        echo '<td>';
        echo '<a href="team_plugins.php?i='.$p['name'].'">&gt; Activate</a> ';
        echo '<a href="team_plugins.php?d='.$p['name'].'">&gt; Delete</a> ';
        if ($p['info_url']!='')
            echo '<a href="'.$p['info_url'].'" target="_blank">&gt; More Info</a> ';
        if ($p['config'])
        	echo '<a href="team_plugins.php?p='.$p['name'].'">&gt; Purge Configuration</a> ';
        echo '</td></tr>';        
    }
    ?>
    </tbody></table>
    </table>
    </div>
    <h2>Upload Plugin</h2>
    <p>Upload a tar.gz file of a plugin to install.  <br /><em>NOTE: Legacy plugins will not work with this install method.</em></p>
    <form enctype="multipart/form-data" method="post"><br>
	<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
	<p>Select a tar.gz plugin file. <input type="file" name="pfile" /><br /></p>
	
	<input type="submit" name="submit" value="Upload Plugin" />
	</form>
    
    	
    <?php if (isset($rejected)) {
    		if($rejected){ 
        	?>	<script>alert("Unable to process plugin: <?php echo $rej_reason ?> \r\nIf you trust this plugin you can install it manually by expanding the archive into your plugins directory.");</script>
    		<?php } else { 
    		?>	<p> Plugin processed succesfully.</p>
    <?php } }?>
    </div>
    
        
    
</div>

<?php include "../../include/footer.php";
?>