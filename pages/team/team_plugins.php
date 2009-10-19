<?php
/**
 * Plugins management interface (part of team center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 * @author Brian Adams <wreality@gmail.com>
 * @todo Manually activated plugins can't be deactivated
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
    $inst_name = getvalescaped('activate','');
    $inst_name = trim($inst_name, '#');
    if ($inst_name!=''){
        activate_plugin($inst_name);   
    }
    redirect('pages/team/team_plugins.php');    # Redirect back to the plugin page so plugin is actually activated. 
    
}
elseif (isset($_REQUEST['deactivate'])){ # Deactivate a plugin
    $remove_name = getvalescaped('deactivate','');
    # Strip the leading hash mark added by javascript.
    $remove_name = trim($remove_name, "#");
    if ($remove_name!=''){
        deactivate_plugin($remove_name); 
    }
    redirect('pages/team/team_plugins.php');    # Redirect back to the plugin page so plugin is actually deactivated.
}
elseif (isset($_REQUEST['purge'])){ # Purge a plugin's configuration (if stored in DB)
    $purge_name = getvalescaped('purge','');
    # Strip the leading hash mark added by javascript.
    $purge_name = trim($purge_name, '#');
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
    $delete_name = getvalescaped('delete','');
    $delete_name = trim($delete_name, '#');
    if ($delete_name!='' && $delete_name!='.' && $delete_name!='..' && strpos($delete_name, '/')===false && strpos($delete_name, '\\')===false){ # Prevent this script being used to delete anything else.
        #Check that the plugin is not activated.
        $c = sql_value("SELECT inst_version as value from plugins WHERE name='$delete_name'",'');
        if ($c=='' || $c == null){
            rcRmdir($plugins_dir.$delete_name);
        }
    }
}
*/
elseif (isset($_REQUEST['submit'])){ # Upload a plugin .rsp file. 
	if (($_FILES['pfile']['error'] == 0) && (pathinfo($_FILES['pfile']['name'], PATHINFO_EXTENSION)=='rsp')){
	    require "../../lib/pcltar/pcltar.lib.php";
	    $tmp_file = dirname(__FILE__).'/../../filestore/tmp/'.basename($_FILES['pfile']['name'].'.tgz');
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
						  'FROM plugins WHERE inst_version>=0');

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
            if (get_plugin_config($file)!=null)
            	$plugins_avail[$file]['config']=true;
           	else
           		$plugins_avail[$file]['config']=false;
        }        
    }
}
closedir($dirh);
?>

<?php include "../../include/header.php"; ?>
<script src="../../lib/js/jquery-1.3.1.min.js" type="text/javascript"> </script>
<script type="text/javascript">
    	$(document).ready(function() {
    		$('a.p-deactivate').click(function() {
    		    var pname = $(this).attr('href');
    		    $('input#anc-input').attr({
    		    						   name: 'deactivate',
    		    						   value: pname
    		    						  });
    		    $('form#anc-post').submit();
    		    return false;
    		});
    		$('a.p-activate').click(function() {
    		    var pname = $(this).attr('href');
    		    $('input#anc-input').attr({
    		        					  name: 'activate',
    		        					  value: pname
    		    						  });
    		   $('form#anc-post').submit();
    		   return false;
    		});
    		$('a.p-purge').click(function() {
    		    var pname = $(this).attr('href');
    		    $('input#anc-input').attr({
    		        					  name: 'purge',
    		        					  value: pname
    		    						  });
    		    $('form#anc-post').submit();
    		    return false;						  
    		});
    		$('a.p-delete').click(function() {
    		    var pname = $(this).attr('href');
    		    $('input#anc-input').attr({
    		        					  name: 'delete',
    		        					  value: pname
    		    						  });
    		    $('form#anc-post').submit();
    		    return false;
    		});
    	});
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
    <td><?php echo $lang['action']; ?></td>
    </tr></thead>
    <tbody>
    
    <?php foreach ($inst_plugins as $p){
        echo '<tr>';
        echo "<td>{$p['name']}</td><td>{$p['descrip']}</td><td>{$p['author']}</td><td>".sprintf("%.1f",$p['inst_version'])."</td>";
        echo '<td>';
        echo '<a href="#'.$p['name'].'" class="p-deactivate">&gt; '.$lang['plugins-deactivate'].'</a> ';
        if ($p['info_url']!='')
            echo '<a href="'.$p['info_url'].'" target="_blank">&gt; '.$lang['plugins-moreinfo'].'</a> ';
        if ($p['config_url']!='')
        	echo '<a href="'.$baseurl.$p['config_url'].'">&gt; '.$lang['options'].'</a> ';
        echo '</td></tr>';
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
    <td><?php echo $lang['action']; ?></td>
    </tr></thead>
    <tbody>
    <?php 
   	foreach($plugins_avail as $p){
        echo '<tr><td>'.$p['name'].'</td><td>'.$p['desc'].'</td><td>'.$p['author'].'</td>';
		if ($p['version'] == 0)
			echo '<td>?</td>';
		else
			echo '<td>'.$p['version'].'</td>';
        echo '<td>';
        echo '<a href="#'.$p['name'].'" class="p-activate">&gt; '.$lang['plugins-activate'].'</a> ';
        echo '<a href="#'.$p['name'].'" class="p-delete">&gt; '.$lang['delete'].'</a> ';
        if ($p['info_url']!='')
            echo '<a href="'.$p['info_url'].'" target="_blank">&gt; '.$lang['plugins-moreinfo'].'</a> ';
        if ($p['config'])
        	echo '<a href="#'.$p['name'].'" class="p-purge">&gt; '.$lang['plugins-purge'].'</a> ';
        echo '</td></tr>';        
    }
    ?>
    </tbody>
    </table>
    </div>
    <?php } else { ?>
    <p><?php echo $lang['plugins-noneavailable']; ?></p>
    <?php } ?>
    <h2><?php echo $lang['plugins-uploadheader']; ?></h2>
    <form enctype="multipart/form-data" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
	<p><?php echo $lang['plugins-uploadtext']; ?><input type="file" name="pfile" /><br /></p>
	<input type="submit" name="submit" value="Upload Plugin" />
	</form>
	<?php if (isset($rejected)&& !$rejected) { ?>	
	    <p><?php echo $lang['plugins-uploadsuccess']; ?></p>
	<?php } ?>
    </div>
    <form id="anc-post" method="post">
    <input type="hidden" id="anc-input" name="" value="" />
    </form>
        

<?php include "../../include/footer.php";
if (isset($rejected) && $rejected){ ?>
	<script>alert("<?php echo $rej_reason.'\\n\\r'.$lang['plugins-rejremedy']; ?>");</script>
<?php } ?>