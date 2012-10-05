<?php
/**
 * Plugins management interface (part of team center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 * @author Brian Adams <wreality@gmail.com>
 * @todo Link to wiki page for config.php activated plugins. (Help text)
 * @todo Fortify plugin delete code
 * @todo Update plugin DB if uploaded plugin is installed (upgrade functionality)
 */
include "../../include/db.php";
/**
 * Only accessable to users with 'a' permission.
 */
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "../../include/general.php";

$plugins_dir = dirname(__FILE__)."/../../plugins/";

$avail_plugins = array();
if (isset($_REQUEST['activate'])){ # Activate a plugin
    $inst_name = trim(getvalescaped('activate',''), '#');
    if ($inst_name!=''){
        activate_plugin($inst_name);   
    }
    redirect($baseurl_short.'pages/team/team_plugins.php');    # Redirect back to the plugin page so plugin is actually activated. 
    
}
elseif (isset($_REQUEST['deactivate'])){ # Deactivate a plugin
    # Strip the leading hash mark added by javascript.
    $remove_name = trim(getvalescaped('deactivate',''), "#");
    if ($remove_name!=''){
        deactivate_plugin($remove_name); 
    }
    redirect($baseurl_short.'pages/team/team_plugins.php');    # Redirect back to the plugin page so plugin is actually deactivated.
}
elseif (isset($_REQUEST['purge'])){ # Purge a plugin's configuration (if stored in DB)
    # Strip the leading hash mark added by javascript.
    $purge_name = trim(getvalescaped('purge',''), '#');
    if ($purge_name!=''){
        purge_plugin_config($purge_name);
    }
    
}
/****
  The delete functionality is commented out of the svn version since it hasn't
  been fully checked to avoid vulnerabilities.  Feel free to add anything to
  help make it more secure.
 ****/
/*
elseif (isset($_REQUEST['delete'])){ # Delete a plugin from the plugins directory.
    $delete_name = trim(getvalescaped('delete',''), '#');
    if ($delete_name!='' && $delete_name!='.' && $delete_name!='..' && strpos($delete_name, '/')===false && strpos($delete_name, '\\')===false){ # Prevent this script being used to delete anything else.
        #Check that the plugin is not activated.
        $c = sql_value("SELECT inst_version as value from plugins WHERE name='$delete_name'",'');
        if ($c=='' || $c == null){
            rcRmdir($plugins_dir.$delete_name);
        }
    }
}
*/
elseif ($enable_plugin_upload && isset($_REQUEST['submit'])){ # Upload a plugin .rsp file. 
	if (($_FILES['pfile']['error'] == 0) && (pathinfo($_FILES['pfile']['name'], PATHINFO_EXTENSION)=='rsp')){
	    require "../../lib/pcltar/pcltar.lib.php";
	    
	    # Create tmp folder if not existing
	    # Since get_temp_dir() method does this, omit: if (!file_exists(dirname(__FILE__).'/../../filestore/tmp')) {mkdir(dirname(__FILE__).'/../../filestore/tmp',0777);}
	    
	    $tmp_file = get_temp_dir() . '/'.basename($_FILES['pfile']['name'].'.tgz');
	    if(move_uploaded_file($_FILES['pfile']['tmp_name'], $tmp_file)==true){
	         $rejected = false;
	         $filelist = PclTarList($tmp_file);
	    	 if(is_array($filelist)){
	    	 	 foreach($filelist as $key=>$value)
	    	 	 { # Loop through the file list to create an array we can use php's functions with.
	    	         $filearray[] = $value['filename'];
	    	 	 }
	    	     # Some security checks.
    	     	 foreach ($filearray as $filename){
    	     	     if ($filename[0]=='/' || $filename[0] =='\\'){ # Paths are absolute.  Reject the plugin.
    	     	         $rejected = true;
	     	 			 $rej_reason = $lang['plugins-rejrootpath'];
	     	 			 break; 
    	     	     }
    	     	 }
	    	     if (array_search('..', $filearray)!==false) {# Archive may contain ../ directories (Security risk)
	    	     	$rejected = true;
	    	     	$rej_reason = $lang['plugins-rejparentpath'];
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
	    	                 $rej_reason = $lang['plugins-rejmultpath'];
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
	    	             $rej_reason = $lang['plugins-rejmetadata'];
	    	         }
	    	         if (!$rejected){
	    	             if (!(is_array(PclTarExtract($tmp_file, '../../plugins/')))){
	    	             	#TODO: If the new plugin is already activated we should update the DB with the new yaml info.
	    	                $rejected = true;
	    	             	$rej_reason = $lang['plugins-rejarchprob'].' '.PclErrorString(PclErrorCode());
	    	             	
	    	             }
	    	         }   	         
	    	     }
	    	 }
	    	 else {
	    	     $rejected = true;
	    	     $rej_reason = $lang['plugins-rejfileprob'];
	    	 }	 
	    }
	}
	else {
	    $rejected = true;
	    $rej_reason  = $lang['plugins-rejfileprob'];
	}
}

$inst_plugins = sql_query('SELECT name, config_url, descrip, author, '.
						  'inst_version, update_url, info_url '.
						  'FROM plugins WHERE inst_version>=0 order by name');
/**
 * Ad hoc function for array_walk through plugins array.
 * 
 * When called from array_walk, steps through each element of the installed 
 * plugins array and checks to see if it was installed via config.php (legacy).
 * If so, sets an addition array key for template to display the link correctly.
 * 
 * @param array $i_plugin Plugin array element. 
 * @param string $key Array key. 
 */
function legacy_check(&$i_plugin, $key){
    global $legacy_plugins;
    if (array_search($i_plugin['name'], $legacy_plugins)!==false)
        $i_plugin['legacy_inst'] = true;
}
array_walk($inst_plugins, 'legacy_check');
# Build an array of available plugins.
$dirh = opendir($plugins_dir);
$plugins_avail = array();
while (false !== ($file = readdir($dirh))) {
    if (is_dir($plugins_dir.$file)&&$file[0]!='.'){
        #Check if the plugin is already activated.
        $status = sql_query('SELECT inst_version, config FROM plugins WHERE name="'.$file.'"');
        if ((count($status)==0) || ($status[0]['inst_version']==null)){
            # Look for a <pluginname>.yaml file.
            $plugin_yaml = get_plugin_yaml($plugins_dir.$file.'/'.$file.'.yaml', false);
            foreach ($plugin_yaml as $key=>$value){
                $plugins_avail[$file][$key] = $value ;
            }
            $plugins_avail[$file]['config']=(sql_value("SELECT config AS value FROM plugins WHERE name='$file'",'') != '');
           		
           	# If no yaml, or yaml file but no description present, 
           	# attempt to read an 'about.txt' file
           	if ($plugins_avail[$file]["desc"]=="")
           		{
           		$about=$plugins_dir.$file.'/about.txt';
           		if (file_exists($about)) {$plugins_avail[$file]["desc"]=substr(file_get_contents($about),0,95) . "...";}
           		}
        }        
    }
}
closedir($dirh);
ksort ($plugins_avail);
?><?php include "../../include/header.php"; ?>
<script type="text/javascript">
(function($) { 
  $(function() {
        function actionPost(action, value){
                $('input#anc-input').attr({
                    name: action,
                    value: value});
                jQuery('form#anc-post').submit();
            }
    	$('#BasicsBox').ready(function() {
    		$('a.p-deactivate').click(function() {
    		    actionPost('deactivate', $(this).attr('href'));
    		    return false;
    		});
    		$('a.p-activate').click(function() {
    		    var pname = $(this).attr('href');
    		    actionPost('activate', $(this).attr('href'));
    		   return false;
    		});
    		$('a.p-purge').click(function() {
    			actionPost('purge', $(this).attr('href'));
    		    return false;						  
    		});
    		$('a.p-delete').click(function() {
    			actionPost('delete', $(this).attr('href'));
    		    return false;
    		});
    	});
  });
})(jQuery);

    </script>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["pluginmanager"]; ?></h1>
  <p><?php echo $lang["plugins-headertext"]; ?></p>
    <h2><?php echo $lang['plugins-installedheader']; ?></h2>
    <?php if (count($inst_plugins)>0){ ?>
    <div class="Listview">
    <table class= "ListviewStyle" cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr class="ListviewTitleStyle">
    <td><?php echo $lang['name']; ?></td>
    <td><?php echo $lang['description']; ?></td>
    <td><?php echo $lang['plugins-author']; ?></td>
    <td><?php echo $lang['plugins-instversion']; ?></td>
    <td><div class="ListTools"><?php echo $lang['tools']; ?></div></td>
    </tr></thead>
    <tbody>
    
    <?php foreach ($inst_plugins as $p){
        # Make sure that the version number is displayed with at least one decimal place.
        # If the version number is 0 the displayed version is $lang["notavailableshort"].
        # (E.g. 0 -> (en:)N/A ; 1 -> 1.0 ; 0.92 -> 0.92)
        if ($p['inst_version']==0)
            {
            $formatted_inst_version = $lang["notavailableshort"];
            }
        else
            {
            if (sprintf("%.1f",$p['inst_version'])==$p['inst_version'])
                {
                $formatted_inst_version = sprintf("%.1f",$p['inst_version']);
                }
            else
                {
                $formatted_inst_version = $p['inst_version'];
                }
            }
        echo '<tr>';
        echo "<td>{$p['name']}</td><td>{$p['descrip']}</td><td>{$p['author']}</td><td>".$formatted_inst_version."</td>";
        echo '<td><div class="ListTools">';
        if (isset($p['legacy_inst']))
            echo '<a class="nowrap" href="#">&gt;&nbsp;'.$lang['plugins-legacyinst'].'</a> '; # TODO: Update this link to point to a help page on the wiki
        else
            echo '<a href="#'.$p['name'].'" class="p-deactivate">&gt;&nbsp;'.$lang['plugins-deactivate'].'</a> ';
        if ($p['info_url']!='')
        	{
            echo '<a class="nowrap" href="'.$p['info_url'].'" target="_blank">&gt;&nbsp;'.$lang['plugins-moreinfo'].'</a> ';
 			}
        echo '<a onClick="return CentralSpaceLoad(this,true);" class="nowrap" href="'.$baseurl_short.'pages/team/team_plugins_groups.php?plugin=' . urlencode($p['name']) . '">&gt;&nbsp;'.$lang['groupaccess'].'</a> ';
        if ($p['config_url']!='')        
			{
        	echo '<a onClick="return CentralSpaceLoad(this,true);" class="nowrap" href="'.$baseurl.$p['config_url'].'">&gt;&nbsp;'.$lang['options'].'</a> ';        
			if (sql_value("SELECT config_json as value from plugins where name='".$p['name']."'",'')!='' && function_exists('json_decode'))
	        	echo '<a class="nowrap" href="'.$baseurl_short.'pages/team/team_download_plugin_config.php?pin='.$p['name'].'">&gt;&nbsp;'.$lang['plugins-download'].'</a> ';
			}
        echo '</div></td></tr>';
    } ?>
    </tbody>
    </table>
    </div>
    <?php } else { ?>
    <p><?php echo $lang['plugins-noneinstalled']; ?></p>
    <?php } ?>
    <h2><?php echo $lang['plugins-availableheader']; ?></h2>
    <?php if (count($plugins_avail)>0) { ?>
    <div class="Listview">
    <table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
    <thead><tr class="ListviewTitleStyle">
    <td><?php echo $lang['name']; ?></td>
    <td><?php echo $lang['description']; ?></td>
    <td><?php echo $lang['plugins-author']; ?></td>
    <td><?php echo $lang['plugins-version']; ?></td>
    <td><div class="ListTools"><?php echo $lang['tools']; ?></div></td>
    </tr></thead>
    <tbody>
    <?php 
   	foreach($plugins_avail as $p){
        echo '<tr><td>'.$p['name'].'</td><td>'.$p['desc'].'</td><td>'.$p['author'].'</td>';
		if ($p['version'] == 0)
			echo '<td>' . $lang["notavailableshort"] . '</td>';
		else
			echo '<td>'.$p['version'].'</td>';
        echo '<td><div class="ListTools">';
        echo '<a href="#'.$p['name'].'" class="p-activate">&gt;&nbsp;'.$lang['plugins-activate'].'</a> ';
        // echo '<a href="#'.$p['name'].'" class="p-delete">&gt;&nbsp;'.$lang["action-delete"].'</a> ';
        if ($p['info_url']!='')
            echo '<a class="nowrap" href="'.$p['info_url'].'" target="_blank">&gt;&nbsp;'.$lang['plugins-moreinfo'].'</a> ';
        if ($p['config'])
            echo '<a href="#'.$p['name'].'" class="p-purge">&gt;&nbsp;'.$lang['plugins-purge'].'</a> ';
        echo '</div></td></tr>';        
    }
    ?>
    </tbody>
    </table>
    </div>
    <?php } else { ?>
    <p><?php echo $lang['plugins-noneavailable']; ?></p>
    <?php } 
    if ($enable_plugin_upload) {
    ?>
    <h2><?php echo $lang['plugins-uploadheader']; ?></h2>
    <form enctype="multipart/form-data" method="post" action="<?php echo $baseurl_short?>pages/team/team_plugins.php">
	<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
	<p><?php echo $lang['plugins-uploadtext']; ?><input type="file" name="pfile" /><br /></p>
	<input type="submit" name="submit" value="<?php echo $lang['plugins-uploadbutton'] ?>" />
	</form>
	<?php if (isset($rejected)&& !$rejected) { ?>	
	    <p><?php echo $lang['plugins-uploadsuccess']; ?></p>
	<?php } 
  }
	?>
    </div>
    <form id="anc-post" method="post" action="<?php echo $baseurl_short?>pages/team/team_plugins.php" onSubmit="return CentralSpacePost(this);">
    <input type="hidden" id="anc-input" name="" value="" />
    </form>
        

<?php include "../../include/footer.php";
if (isset($rejected) && $rejected){ ?>
	<script>alert("<?php echo $rej_reason.'\\n\\r'.$lang['plugins-rejremedy']; ?>");</script>
<?php } 
