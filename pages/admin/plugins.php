<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "../../include/general.php";
$plugins_dir = dirname(__FILE__)."/../../plugins/";


$avail_plugins = array();
if (isset($_REQUEST['i'])){ # Activate a plugin
    # Validate the plugin name exists in the plugins directory.
    $new_inst = getvalescaped('i','');
    if ($new_inst!=''){
        $plugin_dir = $plugins_dir.$new_inst;
        if (file_exists($plugin_dir)){
            $plugin_yaml = get_plugin_yaml("$plugin_dir/$new_inst.yaml", false);
            # Add/Update plugin information.
            # Check if the plugin is already in the table.
            $c = sql_value("SELECT name as value FROM plugins WHERE name='$new_inst'",'');
            if ($c == ''){
                sql_query("INSERT INTO plugins(name) VALUE ('$new_inst')");
            }
            sql_query("UPDATE plugins SET config_url='{$plugin_yaml['config_url']}', descrip='{$plugin_yaml['desc']}', author='{$plugin_yaml['author']}', inst_version='{$plugin_yaml['version']}', update_url='{$plugin_yaml['update_url']}', info_url='{$plugin_yaml['info_url']}' WHERE name='{$plugin_yaml['name']}'");
            redirect('/pages/admin/plugins.php');    # Redirect back to the plugin page    
        }
    }
}
elseif (isset($_REQUEST['r'])){ # Deactivate a plugin
    # Validate the plugin is actually installed.
    $remove_name = getvalescaped('r','');
    if ($remove_name!=''){
        $inst_version = sql_value("SELECT inst_version as value FROM plugins WHERE name='$remove_name'",'');
        if ($inst_version>=0){
            # Remove the version field. Leaving the rest of the plugin information.  This allows for a config column to remain (future).
            sql_query("UPDATE plugins set inst_version=NULL WHERE name='$remove_name'");
            redirect('/pages/admin/plugins.php');    # Redirect back to the plugin page.
        }
    }
}
elseif (isset($_REQUEST['p'])){ # Purge a plugin's configuration (if stored in DB)
    $purge_name = getvalescaped('p','');
    if ($purge_name!=''){
        sql_query("UPDATE plugins SET config=NULL where name='$purge_name'");
    }
}
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
    <?php #TODO: Headers should use $lang[] structure ?>
    <tr class="ListviewTitleStyle"><td>Plugin Name</td><td>Description</td><td>Author</td><td>Installed Version</td><td></td></tr>
    </thead>
    <tbody>
    <?php foreach ($inst_plugins as $p){
        echo '<tr>';
        echo "<td>{$p['name']}</td><td>{$p['descrip']}</td><td>{$p['author']}</td><td>".sprintf("%.1f",$p['inst_version'])."</td>";
        echo '<td>';
        echo "<a href=\"plugins.php?r={$p['name']}\">&gt; Deactivate</a> ";
        if ($p['info_url']!='')
            echo '<a href="'.$p['info_url'].'" target="_blank">&gt; More Info</a> ';
        if ($p['config_url']!='')
        	echo '<a href="'.$baseurl.$p['config_url'].'">&gt; Configure</a> ';
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
    $dirh = opendir($plugins_dir);
    while (false !== ($file = readdir($dirh))) {
        if (is_dir($plugins_dir.$file)&&$file[0]!='.'){
            #Check if the plugin is already activated.
            $status = sql_query("SELECT inst_version, config FROM plugins WHERE name='".$file."'");
            if ((count($status)==0) || ($status[0]['inst_version']==null)){
                # Look for a <pluginname>.yaml file.
                $plugin_yaml = get_plugin_yaml($plugins_dir.$file.'/'.$file.'.yaml', false);
                echo '<tr><td>'.$file.'</td><td>'.$plugin_yaml['desc'].'</td><td>'.$plugin_yaml['author'].'</td><td>'.$plugin_yaml['version'].'</td>';
                echo '<td>';
                echo '<a href="plugins.php?i='.$plugin_yaml['name'].'">&gt; Activate</a> ';
                if ($plugin_yaml['info_url']!='')
                    echo '<a href="'.$plugin_yaml['info_url'].'" target="_blank">&gt; More Info</a> ';
                if ((count($status)==1) && ($status[0]['config']!=null))
                	echo '<a href="plugins.php?p='.$plugin_yaml['name'].'">&gt; Purge Configuration</a> ';
                echo '</td></tr>';
                    
            }        
        }
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