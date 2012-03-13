<?php
/**
 * Functions related to the management of plugins.
 * 
 * @package ResourceSpace
 * @subpackage Includes
 * @author Brian Adams <wreality@gmail.com>
 */
/**
 * Activate a named plugin.
 * 
 * Parses the plugins directory to look for a pluginname.yaml 
 * file and adds the plugin to the plugins database, setting
 * the inst_version field to the version specified in the yaml file.
 * 
 * @param string $name Name of plugin to be activated.
 * @return bool Returns true if plugin directory was found.
 * @see deactivate_plugin
 */
function activate_plugin($name){
    $plugins_dir = dirname(__FILE__)."/../plugins/";
    $plugin_dir = $plugins_dir.$name;
    if (file_exists($plugin_dir)){
        $plugin_yaml = get_plugin_yaml("$plugin_dir/$name.yaml", false);
        
  		# If no yaml, or yaml file but no description present, attempt to read an 'about.txt' file
       	if ($plugin_yaml["desc"]=="")
       		{
       		$about=$plugins_dir . $name.'/about.txt';
       		if (file_exists($about)) {$plugin_yaml["desc"]=substr(file_get_contents($about),0,95) . "...";}
       		}
	# escape the plugin information
	$plugin_yaml_esc = array();
	foreach (array_keys($plugin_yaml) as $thekey){
		$plugin_yaml_esc[$thekey] = escape_check($plugin_yaml[$thekey]);
	}


        # Add/Update plugin information.
        # Check if the plugin is already in the table.
        $c = sql_value("SELECT name as value FROM plugins WHERE name='$name'",'');
        if ($c == ''){
            sql_query("INSERT INTO plugins(name) VALUE ('$name')");
        }
        sql_query("UPDATE plugins SET config_url='{$plugin_yaml_esc['config_url']}', " .
        		  "descrip='{$plugin_yaml_esc['desc']}', author='{$plugin_yaml_esc['author']}', " .
        		  "inst_version='{$plugin_yaml_esc['version']}', " .
        		  "priority='{$plugin_yaml_esc['default_priority']}', " .
        		  "update_url='{$plugin_yaml_esc['update_url']}', info_url='{$plugin_yaml_esc['info_url']}' " .
        		  "WHERE name='{$plugin_yaml_esc['name']}'");
        return true;
    }
    else {
        return false;
    }
}
/**
 * Deactivate a named plugin.
 * 
 * Blanks the inst_version field in the plugins database, which has the effect
 * of deactivating the plugin while maintaining any configuration that is stored
 * in the database.
 * 
 * @param string $name Name of plugin to be deativated.
 * @return bool Returns true if plugin is deactivated.
 * @see activate_plugin
 */
function deactivate_plugin($name){
    $inst_version = sql_value("SELECT inst_version as value FROM plugins WHERE name='$name'",'');
    if ($inst_version>=0){
        # Remove the version field. Leaving the rest of the plugin information.  This allows for a config column to remain (future).
        sql_query("UPDATE plugins set inst_version=NULL WHERE name='$name'");
 
    }
}
/**
 * Purge configuration of a plugin.
 * 
 * Replaces config value in plugins table with NULL.  Note, this plugin
 * will operate on an activated plugin as well so that configuration can
 * be 'defaulted' by the plugin's configuration page.
 * 
 * @param string $name Name of plugin to purge configuration.
 * @category PluginAuthors
 */
function purge_plugin_config($name){
    sql_query("UPDATE plugins SET config=NULL where name='$name'");
}
/**
 * Load plugin .yaml file.
 * 
 * Load a .yaml file for a plugin and return an array of its
 * values.
 * 
 * @param string $path Path to .yaml file to open.
 * @param bool $validate Check that the .yaml file is complete. [optional, default=false]
 * @return array Associative array of yaml values.
 */
function get_plugin_yaml($path, $validate=true){
	#We're not using a full YAML structure, so this parsing function will do
	#If validate is false, this function will return an array of blank values if a yaml isn't available
	$yaml_file_ptr = @fopen($path, 'r');
	$plugin_yaml['author'] = '';
	$plugin_yaml['info_url'] = '';
	$plugin_yaml['update_url'] = '';
	$plugin_yaml['config_url'] = '';
	$plugin_yaml['desc'] = '';
	$plugin_yaml['default_priority'] = '999';
	if ($yaml_file_ptr!=false){
		while (($line = fgets($yaml_file_ptr))!=''){
			if($line[0]!='#'){ #Exclude comments from parsing
				if (($pos=strpos($line,':'))!=false){
					$plugin_yaml[trim(substr($line,0,$pos))] = trim(substr($line, $pos+1));
				}
			}
		}
		if ($plugin_yaml['config_url']!='' && $plugin_yaml['config_url'][0]=='/') # Strip leading spaces from the config url.
			trim($plugin_yaml['config_url'], '/');
		fclose($yaml_file_ptr);
		if ($validate){
			if (isset($plugin_yaml['name']) && $plugin_yaml['name']==basename($path,'.yaml') && isset($plugin_yaml['version']))
				return $plugin_yaml;
			else return false;
		}	
	}
	elseif ($validate)
		return false;
	if (!isset($plugin_yaml['name']))
		$plugin_yaml['name'] = basename($path,'.yaml');
	if (!isset($plugin_yaml['version']))
		$plugin_yaml['version'] = '0';
	
	return $plugin_yaml;
}
/**
 * Return plugin config stored in plugins table for a given plugin name.
 * 
 * Queries the plugins table for a stored config value and, if found,
 * unserializes the data and returns the result.  If config isn't found
 * returns null.
 * 
 * @param string $name Plugin name
 * @return mixed|null Returns config data or null if no config.
 * @see set_plugin_config
 */
function get_plugin_config($name){
    $config = sql_value("SELECT config as value from plugins where name='$name'",'');
    if ($config=='')
        return null;
    else
    	return unserialize(base64_decode($config));
}
/**
 * Store a plugin's configuration in the database.
 * 
 * Serializes the configuration parameter and stores in the config 
 * column of the plugins table.
 * <code>
 * <?php
 * $plugin_config['a'] = 1;
 * $plugin_config['b'] = 2;
 * set_plugin_config('myplugin', $plugin_config);
 * ?>
 * </code>
 * 
 * @param string $plugin_name Plugin name
 * @param mixed $config Configuration variable to store.
 * @see get_plugin_config
 */
function set_plugin_config($plugin_name, $config){
	$config_ser =  base64_encode(serialize($config));
	sql_query("UPDATE plugins SET config='$config_ser' WHERE name='$plugin_name'");
	return true;
}
/**
 * Check is a plugin is activated.
 * 
 * Returns true is a plugin is activated in the plugins database.
 * 
 * @param $name Name of plugin to check
 * @return bool Returns true is plugin is activated.
 */
function is_plugin_activated($name){
    $activated = sql_query("SELECT name FROM plugins WHERE name='$name' and inst_version IS NOT NULL");
    if (is_array($activated) && count($activated)>0){
        return true;
    }
    else {
        return false;
    }
}

function config_text_field($name,$label,$value,$size='30'){
	if (!is_numeric($size)){ $size = 30; }
	?><div class="Question">
	<label for="<?php echo $name?>"><?php echo $label?>:</label>
	<input name="<?php echo $name?>" type="text" size="<?php echo $size ?>" value='<?php echo htmlspecialchars($value,ENT_QUOTES);?>' />
	</div><div class="clearerleft"></div>
	<?php 
}

function config_userselect_field($name,$label,$values=array()){
	?><div class="Question">
	<label for="<?php echo $name?>[]"><?php echo $label?>:</label> 
	<select name="<?php echo $name?>[]" multiple="multiple" size="7">
	<?php $users=get_users(); 
	foreach ($users as $user){?>
	<option value="<?php echo $user['ref'];?>" <?php if (in_array($user['ref'],$values)){?>selected<?php } ?>><?php echo $user['fullname'];?> (<?php echo $user['email']?>)</option>
	<?php } ?>
	</select>
	</div><div class="clearerleft"></div>
<?php 
}

function config_field_select($name,$label,$value){
	$fields=sql_query("select * from resource_type_field");?>
	<div class="Question">
	<label for="<?php echo $name?>"><?php echo $label?>:</label> 
	<select name="<?php echo $name?>">
	<?php foreach($fields as $field){?>
	<option value="<?php echo $field['ref']?>"  <?php if ($value==$field['ref']){?>selected<?php } ?>><?php echo $field['title']?></option>
	<?php } ?>
	</select>
	</div><div class="clearerleft"></div>
	<?php 
}

function config_boolean_field($name,$label,$value){
	?><div class="Question">
	<label for="<?php echo $name?>"><?php echo $label?></label>
	<select name="<?php echo $name?>">
	<option value="1" <?php if ($value=="1") { ?>selected<?php } ?>>True</option>
	<option value="0" <?php if ($value=="0") { ?>selected<?php } ?>>False</option>
	</select>
	</div><div class="clearerleft"></div>
	<?php 
}

function config_custom_select_multi($name,$label,$available,$values,$index="ref",$nameindex="name",$additional=""){
	?><div class="Question">
	<label for="<?php echo $name?>[]"><?php echo $label?>:</label> 

	<select name="<?php echo $name?>[]" multiple="multiple" size="7">
	<?php foreach($available as $item){?>
	<option value="<?php echo $item[$index]?>" <?php if (in_array($item[$index],$values)){?>selected<?php } ?>><?php echo $item[$nameindex]; if ($additional!=""){echo " (".$item[$additional].")";}?></option>
	<?php } ?>
	</select>
	</div><div class="clearerleft"></div>
	<?php
}

function config_custom_select($name,$label,$available,$value){
	?><div class="Question">
	<label for="<?php echo $name?>"><?php echo $label?></label>
	<select name="<?php echo $name?>">
	<?php foreach($available as $avail){?>
	<option value="<?php echo $avail?>" <?php if ($value==$avail) { ?>selected<?php } ?>><?php echo $avail?></option>
	<?php } ?>
	</select>
	</div><div class="clearerleft"></div>
	<?php 
}
