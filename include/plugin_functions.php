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
function activate_plugin($name)
    {
    $plugins_dir = dirname(__FILE__).'/../plugins/';
    $plugin_dir = $plugins_dir.$name;
    if (file_exists($plugin_dir))
        {
        $plugin_yaml = get_plugin_yaml("$plugin_dir/$name.yaml", false);
        # If no yaml, or yaml file but no description present, attempt to read an 'about.txt' file
        if ($plugin_yaml['desc']=='')
            {
            $about=$plugins_dir . $name.'/about.txt';
            if (file_exists($about)) {$plugin_yaml['desc']=substr(file_get_contents($about),0,95) . '...';}
            }
    # escape the plugin information
    $plugin_yaml_esc = array();
    foreach (array_keys($plugin_yaml) as $thekey)
        {
        $plugin_yaml_esc[$thekey] = escape_check($plugin_yaml[$thekey]);
        }


        # Add/Update plugin information.
        # Check if the plugin is already in the table.
        $c = sql_value("SELECT name as value FROM plugins WHERE name='$name'",'');
        if ($c == '')
            {
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
    else
        {
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
function deactivate_plugin($name)
    {
    $inst_version = sql_value("SELECT inst_version as value FROM plugins WHERE name='$name'",'');
    if ($inst_version>=0)
        {
        # Remove the version field. Leaving the rest of the plugin information.  This allows for a config column to remain (future).
        sql_query("UPDATE plugins set inst_version=NULL WHERE name='$name'");

        }
    }
/**
 * Purge configuration of a plugin.
 *
 * Replaces config value in plugins table with NULL.  Note, this function
 * will operate on an activated plugin as well so its configuration can
 * be 'defaulted' by the plugin's configuration page.
 *
 * @param string $name Name of plugin to purge configuration.
 * @category PluginAuthors
 */
function purge_plugin_config($name)
    {
    sql_query("UPDATE plugins SET config=NULL, config_json=NULL where name='$name'");
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
function get_plugin_yaml($path, $validate=true)
    {
    #We're not using a full YAML structure, so this parsing function will do
    #If validate is false, this function will return an array of blank values if a yaml isn't available
    $yaml_file_ptr = @fopen($path, 'r');
    $plugin_yaml['author'] = '';
    $plugin_yaml['info_url'] = '';
    $plugin_yaml['update_url'] = '';
    $plugin_yaml['config_url'] = '';
    $plugin_yaml['desc'] = '';
    $plugin_yaml['default_priority'] = '999';
    if ($yaml_file_ptr!=false)
        {
        while (($line = fgets($yaml_file_ptr))!='')
            {
            if($line[0]!='#') #Exclude comments from parsing
                {
                if (($pos=strpos($line,':'))!=false)
                    {
                    $plugin_yaml[trim(substr($line,0,$pos))] = trim(substr($line, $pos+1));
                    }
                }
            }
        if ($plugin_yaml['config_url']!='' && $plugin_yaml['config_url'][0]=='/') # Strip leading spaces from the config url.
            {
            trim($plugin_yaml['config_url'], '/');
            }
        fclose($yaml_file_ptr);
        if ($validate)
            {
            if (isset($plugin_yaml['name']) && $plugin_yaml['name']==basename($path,'.yaml') && isset($plugin_yaml['version']))
                {
                return $plugin_yaml;
                }
            else return false;
            }
        }
    elseif ($validate)
        {
        return false;
        }
    if (!isset($plugin_yaml['name']))
        {
        $plugin_yaml['name'] = basename($path,'.yaml');
        }
    if (!isset($plugin_yaml['version']))
        {
        $plugin_yaml['version'] = '0';
        }
    return $plugin_yaml;
    }

/**
 * A subset json_encode function that only works on $config arrays but has none
 * of the version-to-version variability and other "unusual" behavior of PHP's.
 * implementation.
 *
 * @param $config mixed a configuration variables array. This *must* be an array
 *      whose elements are UTF-8 encoded strings, booleans, numbers or arrays
 *      of such elements and whose keys are either numbers or UTF-8 encoded
 *      strings.
 * @return json encoded version of $config or null if $config is beyond our
 *         capabilities to encode
 */
function config_json_encode($config)
    {
    debug('config_json_encode - config:' . print_r($config, true));
    $i=0;
    $simple_keys = true;
    foreach ($config as $name => $value)
        {
        debug('config_json_encode - name: ' . print_r($name, true) . ' i: ' . print_r($i, true). ' equality: '. print_r($name == $i, true));
        if (!is_numeric($name) || ($name != $i++))
            {
            debug('config_json_encode - not simple key.');
            $simple_keys = false;
            break;
            }
        }
    $output = $simple_keys?'[':'{';
    foreach ($config as $name => $value)
        {
        if (!$simple_keys)
            {
            $output .= '"' . config_encode($name) . '":';
            }
        if (is_string($value))
            {
            $output .= '"' . config_encode($value) . '"';
            }
        elseif (is_bool($value))
            {
            $output .= $value?'true':'false';
            }
        elseif (is_numeric($value))
            {
            $output .= strval($value);
            }
        elseif (is_array($value))
            {
            $output .= config_json_encode($value);
            }
        else
            {
            return NULL; // Give up; beyond our capabilities
            }
        $output .= ', ';
        }
    if (substr($output, -2) == ', ')
        {
        $output = substr($output, 0, -2);
        }
    return $output . ($simple_keys?']':'}');
    }

/**
 * Utility function to encode the passed string to something that conforms to
 * the json spec for a string.  Json doesn't allow strings with double-quotes,
 * backslashes or control characters in them. For double-quote and backslash,
 * the encoding is '\"' and '\\' respectively.  The encoding for control
 * characters is of the form '\uxxx' where "xxx" is the UTF-8 4-digit hex
 * value of the encoded character.
 *
 * @param $input string the string that needs encoding
 * @return an encoded version of $input
 */
function config_encode($input)
    {
    $output = '';
    for ($i = 0; $i < strlen($input); $i++)
        {
        $char = substr($input, $i, 1);
        if (ord($char) < 32)
            {
            $char = '\\u' . substr('0000' . dechex(ord($char)),-4);
            }
        elseif ($char == '"')
            {
            $char = '\\"';
            }
        elseif ($char == '\\')
            {
            $char = '\\\\';
            }
        $output .= $char;
        }
    return $output;
    }

/**
 * Utility function to "clean" the passed $config. Cleaning consists of two parts:
 *  *  	 Suppressing really simple XSS attacks by refusing to allow strings
 *  	 containing the characters "<script" in upper, lower or mixed case.
 *  *    Unescaping instances of "'" and '"' that have been escaped by the
 *    	 lovely magic_quotes_gpc facility, if it's on.
 *
 * @param $config mixed thing to be cleaned.
 * @return a cleaned version of $config.
 */
function config_clean($config)
    {
    if (is_array($config))
        {
        foreach ($config as &$item)
            {
            $item = config_clean($item);
            }
        }
    elseif (is_string($config))
        {
        if (strpos(strtolower($config),"<script")!==false)
            {
            $config = '';
            }
        if (get_magic_quotes_gpc())
            {
            $config = stripslashes($config);
            }
        }
    return $config;
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
    global $mysql_verbatim_queries, $mysql_charset;

    # Need verbatum queries here
    $mysql_vq = $mysql_verbatim_queries;
    $mysql_verbatim_queries = true;
    $configs = sql_query("SELECT config,config_json from plugins where name='$name'",'');
    $configs = $configs[0];
    $mysql_verbatim_queries = $mysql_vq;
    if (!array_key_exists('config', $configs))
        {
        return null;
        }
    elseif (array_key_exists('config_json', $configs) && function_exists('json_decode'))
        {
        if (!isset($mysql_charset))
            {
            $configs['config_json'] = iconv('ISO-8859-1', 'UTF-8', $configs['config_json']);
            }
            return json_decode($configs['config_json'], true);

        }
    else
        {
    	return unserialize(base64_decode($configs['config']));
    	}
}

/**
 * Store a plugin's configuration in the database.
 *
 * Serializes the $config parameter and stores in the config
 * and config_json columns of the plugins table.
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
function set_plugin_config($plugin_name, $config)
    {
	global $db, $use_mysqli, $mysql_charset;
    $config = config_clean($config);
    $config_ser_bin =  base64_encode(serialize($config));
    $config_ser_json = config_json_encode($config);
    if (!isset($mysql_charset))
        {
        $config_ser_json = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $config_ser_json);
        }
    if ($use_mysqli)
        {
        $config_ser_json = mysqli_real_escape_string($db,$config_ser_json);
        }
    else
        {
        $config_ser_json = mysql_real_escape_string($config_ser_json);
        }
    sql_query("UPDATE plugins SET config='$config_ser_bin', config_json='$config_ser_json' WHERE name='$plugin_name'");
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
function is_plugin_activated($name)
    {
    $activated = sql_query("SELECT name FROM plugins WHERE name='$name' and inst_version IS NOT NULL");
    if (is_array($activated) && count($activated)>0)
        {
        return true;
        }
    else
        {
        return false;
        }
    }

/**
 * Handle the POST for an upload of a plugin configuration (.rsc) file
 *
 * Typically invoked near the beginning of a plugin's setup.php file
 * something like this:
 *
 *  if (getval('upload','')!='')
 *      {
 *      handle_rsc_upload($plugin_name);
 *      )
 *  elseif (getval('submit','')!='')
 *     {
 *     ...
 *     }
 *
 * @param string $plugin_name - the name of the plugin
 * @return string a translated string giving the status of the upload
 */
function handle_rsc_upload($plugin_name)
    {
    global$lang;
    $upload_status=$lang['plugins-goodrsc'];
    if (!function_exists('json_decode'))
        {
        $upload_status = str_replace('%version','5.2',$lang['error-oldphp']);
        }
    elseif (($_FILES['rsc_file']['error'] != 0) || (pathinfo($_FILES['rsc_file']['name'], PATHINFO_EXTENSION)!='rsc') ||
            !is_uploaded_file($_FILES['rsc_file']['tmp_name']) || ($_FILES['rsc_file']['size'] > 32768))
        {
        $upload_status = $lang['plugins-didnotwork'];
        }
    else
        {
        $json = file_get_contents($_FILES['rsc_file']['tmp_name']);
        if (substr($json, 0, 3) == (chr(0xEF) . chr(0xBB) . chr(0xBF))) // Discard UTF-8 BOM if present
            {
            $json = substr($json, 3);
            }
        $tok = "\n";
        $rsc_plugin_name = json_decode(strtok($json, $tok),true);
        if ($rsc_plugin_name['ResourceSpacePlugin'] == $plugin_name)
            {
            $config = json_decode(strtok($tok), true);
            foreach($config as $key=>$value)
                $GLOBALS[$key] = $value;
            }
        elseif ($rsc_plugin_name == '')
            {
            $upload_status = $lang['plugins-badrsc'];
            }
        else
            {
            $upload_status = str_replace('%plugin',$rsc_plugin_name['ResourceSpacePlugin'],$lang['plugins-wrongplugin']);
            }
        }
    return $upload_status;
    }

/**
 * Display hmtl form for uploading a plugin configuration (.rsc) file
 *
 * Typically invoked in a plugins's setup.php file just after the form for setting individual plugin
 * configuration parameters.
 *
 * @param string $upload_status string the status message (or '' if none) resulting from handling
 *          the POST of a .rsc file. Typically the return value from handle_rsc_upload or ''.
 */
function display_rsc_upload($upload_status)
    {
    global $lang;
    if (!function_exists('json_encode')) return; // i.e. if before json support in PHP
?>
  <br />
  <h2><?php echo $lang['plugins-upload-title']?></h2>
  <?php if ($upload_status!='') echo '<p>' . $upload_status . '</p>'?>
  <form id="form2" enctype="multipart/form-data" name="form2" method="post" action="">
    <div class="Question">
      <input type="hidden" name="MAX_FILE_SIZE" value="32768" />
      <label for="rsc_file"><?php echo $lang['plugins-getrsc'] ?></label>
      <input type="file" name="rsc_file" id="rsc_file" size=80 />
      <input type="submit" name="upload" value="<?php echo $lang['plugins-upload'] ?>" />
    </div>
    <div class="clearerleft"></div>
  </form>
<?php
    }

/**
 * Generate the first half of the "guts" of a plugin setup page from a page definition array. This
 * function deals with processing the POST that comes (usually) as a result of clicking on the Save
 * Configuration button.
 *
 * The page definition array is typically constructed by a series of calls to config_add_xxxx
 * functions (see below). See the setup page for the sample plugin for information on how to use
 * this and the associated functions.
 *
 * @param $page_def mixed an array whose elements are generated by calls to config_add_xxxx functions
 *        each of which describes how one of the plugin's configuration variables.
 * @param $plugin_name string the name of the plugin for which the function is being invoked.
 * @return string containing a status message if the post was of an .rsc file.
 */
function config_gen_setup_post($page_def,$plugin_name)
    {
    if (getval('upload','')!='')
        {
        return handle_rsc_upload($plugin_name);
        }
    elseif (getval('submit','')!='')
        {
        $config=array();
        foreach ($page_def as $def)
            {
            $omit = false;
            switch ($def[0])
                {
                case 'section_header':
                    $omit = true;
                    break;
                case 'text_list':
                    $GLOBALS[$def[1]] = explode(',', getval($def[1], ''));
                    break;
                case 'hidden_param':
                    break;
                default:
                    $GLOBALS[$def[1]] = getval($def[1], is_array($GLOBALS[$def[1]])?array():'');
                    break;
                }
            if (!$omit)
                {
                $config[$def[1]]=$GLOBALS[$def[1]];
                }
            }
        set_plugin_config($plugin_name,$config);
        redirect('pages/team/team_plugins.php');
        }
    }

/**
 * Generate the second half of the "guts" of a plugin setup page from a page definition array. The
 * page definition array is typically constructed by a series of calls to config_add_xxxx functions
 * (see below). See the setup page for the sample plugin for information on how to use this and the
 * associated functions.
 *
 * @param $page_def mixed an array whose elements are generated by calls to config_add_xxxx functions
 *          each of which describes how one of the plugin's configuratoin variables.
 * @param $plugin_name string the name of the plugin for which the function is being invoked.
 * @param $upload_status string the status string returned by config_get_setup_post().
 * @param $plugin_page_heading string the heading to be displayed for the setup page for this plugin,
 *          typically a $lang[] variable.
 * @param $plugin_page_frontm string front matter for the setup page in html format. This material is
 *          placed after the page heading and before the form. Default: '' (i.e., no front matter).
 */
function config_gen_setup_html($page_def,$plugin_name,$upload_status,$plugin_page_heading,$plugin_page_frontm='')
    {
    global $lang;
?>
    <div class="BasicsBox">
      <h2>&nbsp;</h2>
      <h1><?php echo $plugin_page_heading ?></h1>
<?php
    if ($plugin_page_frontm!='')
        {
        echo $plugin_page_frontm;
        }
?>
      <form id="form1" name="form1" method="post" action="">
<?php
    foreach ($page_def as $def)
        {
        switch ($def[0])
            {
            case 'section_header':
                 config_section_header($def[1], $def[2]);
                 break;
            case 'text_input':
                config_text_input($def[1], $def[2], $GLOBALS[$def[1]], $def[3], $def[4]);
                break;
            case 'text_list':
                config_text_input($def[1], $def[2], implode(',', $GLOBALS[$def[1]]), $def[3], $def[4]);
                break;
            case 'boolean_select':
                config_boolean_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3], $def[4]);
                break;
            case 'single_select':
                config_single_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3], $def[4], $def[5]);
                break;
            case 'multi_select':
                config_multi_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3], $def[4], $def[5]);
                break;
            case 'single_user_select':
                config_single_user_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3]);
                break;
            case 'multi_user_select':
                config_multi_user_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3]);
                break;
            case 'single_ftype_select':
                config_single_ftype_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3]);
                break;
            case 'multi_ftype_select':
                config_multi_ftype_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3]);
                break;
            case 'single_rtype_select':
                config_single_rtype_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3]);
                break;
            case 'multi_rtype_select':
                config_multi_rtype_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3]);
                break;
            case 'db_single_select':
                config_db_single_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3], $def[4], $def[5], $def[6], $def[7], $def[8]);
                break;
            case 'db_multi_select':
                config_db_multi_select($def[1], $def[2], $GLOBALS[$def[1]], $def[3], $def[4], $def[5], $def[6], $def[7], $def[8]);
                break;
            }
        }
?>
        <div class="Question">
          <label for="submit">&nbsp;</label>
          <input type="submit" name="submit" id="submit" value="<?php echo $lang['plugins-saveconfig']?>">
        </div>
        <div class="clearerleft"></div>
      </form>
<?php
    display_rsc_upload($upload_status);
?>
    </div>
<?php
    }

/**
 * Generate an html text section header
 *
 * @param string $title the title of the section.
 * @param string $description the user text displayed to describe the section. Usually a $lang string.
 */
function config_section_header($title, $description)
    {
?>
   <div class="Question">
	<br /><h2><?php echo $title?></h2>
	<?php if ($description!=""){?>
		<p><?php echo $description?></p>
    <?php } ?>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to add
 * a section header.
 *
 * @param string $title the title of the section.
 * @param string $description Usually a $lang string.
 */
function config_add_section_header($title, $description='')
    {
    return array('section_header',$title,$description);
    }

 /**
 * Generate an html text entry or password block
 *
 * @param string $name the name of the text block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the text block. Usually a $lang string.
 * @param string $current the current value of the config variable being set.
 * @param boolean $password whether this is a "normal" text-entry field or a password-style
 *          field. Defaulted to false.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_text_input($name, $label, $current, $password=false, $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <input name="<?php echo $name?>" id="<?php echo $name?>" type="<?php echo $password?'password':'text' ?>" value="<?php echo htmlspecialchars($current,ENT_QUOTES);?>" style="width:<?php echo $width; ?>px" />
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a text entry configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the text block. Usually a $lang string.
 * @param boolean $password whether this is a "normal" text-entry field or a password-style
 *          field. Defaulted to false.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_text_input($config_var, $label, $password=false, $width=300)
    {
    return array('text_input', $config_var, $label, $password, $width);
    }
/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a comma-separated list text entry configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the text block. Usually a $lang string.
 * @param boolean $password whether this is a "normal" text-entry field or a password-style
 *          field. Defaulted to false.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_text_list_input($config_var, $label, $password=false, $width=300)
    {
    return array('text_list', $config_var, $label, $password, $width);
    }

/**
 * Generate an html boolean select block
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param boolean $current the current value (true or false) of the config variable being set.
 * @param string array $choices array of the text to display for the two choices: False and True. Defaults
 *          to array('False', 'True') in the local language.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_boolean_select($name, $label, $current, $choices='', $width=300)
    {
    global $lang;
    if ($choices=='') $choices=$lang['false-true'];
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>" id="<?php echo $name?>" style="width:<?php echo $width ?>px">
    <option value="1" <?php if ($current=='1') { ?>selected<?php } ?>><?php echo $choices[1] ?></option>
    <option value="0" <?php if ($current=='0') { ?>selected<?php } ?>><?php echo $choices[0] ?></option>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a boolean configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string array $choices array of the text to display for the two choices: False and True. Defaults
 *          to array('False', 'True') in the local language.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_boolean_select($config_var, $label, $choices='', $width=300)
    {
    return array('boolean_select', $config_var, $label, $choices, $width);
    }

/**
 * Generate an html single-select + options block
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string $current the current value of the config variable being set.
 * @param string array $choices the array of the alternatives -- the options in the select block. The keys
 *          are used as the values of the options, and the values are the alternatives the user sees. (But
 *          see $usekeys, below.) Usually a $lang entry whose value is an array of strings.
 * @param boolean $usekeys tells whether to use the keys from $choices as the values of the options. If set
 *          to false the values from $choices will be used for both the values of the options and the text
 *          the user sees. Defaulted to true.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_single_select($name, $label, $current, $choices, $usekeys=true, $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>" id="<?php echo $name?>" style="width:<?php echo $width ?>px">
<?php
    foreach($choices as $key => $choice)
        {
        $value=$usekeys?$key:$choice;
        echo '    <option value="' . $value . '"' . (($current==$value)?' selected':'') . ">$choice</option>";
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a single select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string array $choices the array of the alternatives -- the options in the select block. The keys
 *          are used as the values of the options, and the values are the alternatives the user sees. (But
 *          see $usekeys, below.) Usually a $lang entry whose value is an array of strings.
 * @param boolean $usekeys tells whether to use the keys from $choices as the values of the options. If set
 *          to false the values from $choices will be used for both the values of the options and the text
 *          the user sees. Defaulted to true.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_single_select($config_var, $label, $choices='', $usekeys=true, $width=300)
    {
    return array('single_select', $config_var, $label, $choices, $usekeys, $width);
    }

/**
 * Generate an html multi-select + options block
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string array $current the current value of the config variable being set.
 * @param string array $choices the array of choices -- the options in the select block. The keys are
 *          used as the values of the options, and the values are the choices the user sees. (But see
 *          $usekeys, below.) Usually a $lang entry whose value is an array of strings.
 * @param boolean $usekeys tells whether to use the keys from $choices as the values of the options.
 *          If set to false the values from $choices will be used for both the values of the options
 *          and the text the user sees. Defaulted to true.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_multi_select($name, $label, $current, $choices, $usekeys=true, $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>[]" id="<?php echo $name?>" multiple="multiple" <?php if(count($choices) > 7) echo ' size="7"'?> style="width:<?php echo $width ?>px">
<?php
    foreach($choices as $key => $choice)
        {
        $value=$usekeys?$key:$choice;
        echo '    <option value="' . $value . '"' . ((in_array($value,$current))?' selected':'') . ">$choice</option>";
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a multi select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string array $choices the array of choices -- the options in the select block. The keys are
 *          used as the values of the options, and the values are the choices the user sees. (But see
 *          $usekeys, below.) Usually a $lang entry whose value is an array of strings.
 * @param boolean $usekeys tells whether to use the keys from $choices as the values of the options.
 *          If set to false the values from $choices will be used for both the values of the options
 *          and the text the user sees. Defaulted to true.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_multi_select($config_var, $label, $choices, $usekeys=true, $width=300)
    {
    return array('multi_select', $config_var, $label, $choices, $usekeys, $width);
    }

/**
 * Generate an html single-select block for selecting one of the RS users.
 *
 * The user key (i.e., the value from the "ref" column of the user table) of the selected user is the
 * value posted.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $current the current value of the config variable being set.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_single_user_select($name, $label, $current=array(), $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>" id="<?php echo $name?>" style="width:<?php echo $width ?>px">
<?php
    $users=get_users();
    foreach ($users as $user)
        {
        echo '    <option value="' . $user['ref'] . '"' . (($user['ref']==$current)?' selected':'') . '>' . $user['fullname'] . ' (' . $user['email'] . ')</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a single RS user select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_single_user_select($config_var, $label, $width=300)
    {
    return array('single_user_select', $config_var,$label, $width);
    }

/**
 * Generate an html multi-select block for selecting from among RS users.
 *
 * An array consisting of the user keys (i.e., values from the "ref" column of the user table) for the
 * selected users is the value posted.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer array $current the current value of the config variable being set.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_multi_user_select($name, $label, $current=array(), $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>[]" id="<?php echo $name?>" multiple="multiple" size="7" style="width:<?php echo $width ?>px">
<?php
    $users=get_users();
    foreach ($users as $user)
        {
        echo '    <option value="' . $user['ref'] . '"' . ((in_array($user['ref'],$current))?' selected':'') . '>' . $user['fullname'] . ' (' . $user['email'] . ')</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a multiple RS user select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_multi_user_select($config_var, $label, $width=300)
    {
    return array('multi_user_select', $config_var, $label, $width);
    }

/**
 * Generate an html single-select + options block for selecting one of the RS field types. The
 * selected field type is posted as the value of the "ref" column of the selected field type.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $current the current value of the config variable being set
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_single_ftype_select($name, $label, $current, $width=300)
    {
    global $lang;
    $fields=sql_query('select * from resource_type_field');
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>" id="<?php echo $name?>" style="width:<?php echo $width ?>px">
<?php
    foreach($fields as $field)
        {
        echo '    <option value="'. $field['ref'] . '"' . (($current==$field['ref'])?' selected':'') . '>' . lang_or_i18n_get_translated($field['title'],'fieldtitle-') . '</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a single RS field-type select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_single_ftype_select($config_var, $label, $width=300)
    {
    return array('single_ftype_select', $config_var, $label, $width);
    }

/**
 * Generate an html multi-select + options block for selecting multiple the RS field types. The
 * selected field type is posted as an array of the values of the "ref" column of the selected
 * field types.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer array $current the current value of the config variable being set
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_multi_ftype_select($name, $label, $current, $width=300)
    {
    global $lang;
    $fields=sql_query('select * from resource_type_field');
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>[]" id="<?php echo $name?>" multiple="multiple" size="7" style="width:<?php echo $width ?>px">
<?php
    foreach($fields as $field)
        {
        echo '    <option value="'. $field['ref'] . '"' . (in_array($field['ref'],$current)?' selected':'') . '>' . lang_or_i18n_get_translated($field['title'],'fieldtitle-') . '</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a multiple RS field-type select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_multi_ftype_select($config_var, $label, $width=300)
    {
    return array('multi_ftype_select',$config_var, $label, $width);
    }

/**
 * Generate an html single-select + options block for selecting one of the RS resource types. The
 * selected field type is posted as the value of the "ref" column of the selected resource type.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $current the current value of the config variable being set
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_single_rtype_select($name, $label, $current, $width=300)
    {
    global $lang;
    $rtypes=get_resource_types();
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>" id="<?php echo $name?>" style="width:<?php echo $width ?>px">
<?php
    foreach($rtypes as $rtype)
        {
        echo '    <option value="'. $rtype['ref'] . '"' . (($current==$rtype['ref'])?' selected':'') . '>' . lang_or_i18n_get_translated($rtype['name'],'resourcetype-') . '</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
}

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a single RS resource-type select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_single_rtype_select($config_var, $label, $width=300)
    {
    return array('single_rtype_select',$config_var, $label, $width);
    }

/**
 * Generate an html multi-select check boxes block for selecting multiple the RS resource types. The
 * selected field type is posted as an array of the values of the "ref" column of the selected
 * resource types.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer array $current the current value of the config variable being set
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_multi_rtype_select($name, $label, $current, $width=300)
    {
    global $lang;
    $rtypes=get_resource_types();
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <fieldset id="<?php echo $name?>" style="width:<?php echo $width ?>px">
<?php
    foreach($rtypes as $rtype)
        {
        echo '    <input type="checkbox" value="'. $rtype['ref'] . '" name="' . $name . '[]"' . (in_array($rtype['ref'],$current)?' checked="checked"':'') . '>' . lang_or_i18n_get_translated($rtype['name'],'resourcetype-') . '</option><br />';
        }
?>
    </fieldset>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a multiple RS resource-type select configuration variable to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_multi_rtype_select($config_var, $label, $width=300)
    {
    return array('multi_rtype_select', $config_var, $label, $width);
    }

/**
 * Generate an html single-select + options block for selecting from among rows returned by a
 * database query in which one of the columns is the unique key (by default, the "ref" column) and
 * one of the others is the text to display (by default the "name" column). The value posted is the
 * value at the intersection of the selected rown with the column given by the $ixcol variable.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string $current the current value of the config variable being set.
 * @param array $choices the array of db rows that make up the choices.
 * @param string $ixcol the key in $choices (i.e., the db column) for the value of the choice.
 *          Defaulted to 'ref'.
 * @param string $dispcolA the key in $choices (i.e., the db column) for the text to display to the
 *          user. Defaulted to 'name'.
 * @param string $dispcolB the key in $choices (i.e., the db column) for secondary text to display to
 *          the user. Defaulted to '' indicating that only $dispcolA is to be displayed.
 * @param string $fmt the formatting string for combining $dispcolA and B when both are specified.
 *          Defaulted to $lang['plugin_field_fmt']. $fmt is all literal except for %A and %B which
 *          are replaced with values. In English $fmt is '%A(%B)' which results in the i-th choice
 *          displaying as: $choices[i][$dispcolA] . '(' . $choices[i][$dispcolB] . ')'
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_db_single_select($name, $label, $current, $choices, $ixcol='ref', $dispcolA='name', $dispcolB='', $fmt='', $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>" id="<?php echo $name?>" style="width:<?php echo $width ?>px">
<?php
    foreach($choices as $item)
        {
        if ($dispcolB!='')
            {
            $usertext=str_replace(array('%A','%B'), array($item[$dispcolA],$item[$dispcolB]),$fmt==''?$lang['plugin_field_fmt']:$fmt);
            }
        else
            {
            $usertext=$item[$dispcolA];
            }
        echo '    <option value="' . $item[$ixcol] . '"' . (($item[$ixcol]==$current)?' selected':'') . '>' . $usertext . '</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a single select configuration variable whose value is chosen from among the results of
 * a db query to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param array $choices the array of db rows that make up the choices.
 * @param string $ixcol the key in $choices (i.e., the db column) for the value of the choice.
 *          Defaulted to 'ref'.
 * @param string $dispcolA the key in $choices (i.e., the db column) for the text to display to the
 *          user. Defaulted to 'name'.
 * @param string $dispcolB the key in $choices (i.e., the db column) for secondary text to display to
 *          the user. Defaulted to '' indicating that only $dispcolA is to be displayed.
 * @param string $fmt the formatting string for combining $dispcolA and B when both are specified.
 *          Defaulted to $lang['plugin_field_fmt']. $fmt is all literal except for %A and %B which are
 *          replaced with values. In English $fmt is '%A(%B)' which results in the i-th choice
 *          displaying as: $choices[i][$dispcolA] . '(' . $choices[i][$dispcolB] . ')'
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_db_single_select($config_var, $label, $choices, $ixcol='ref', $dispcolA='name', $dispcolB='', $fmt='', $width=300)
    {
    return array('db_single_select', $config_var, $label, $choices, $ixcol, $dispcolA, $dispcolB, $fmt, $width);
    }

/**
 * Generate an html multi-select + options block for selecting from among rows returned by a
 * database query in which one of the columns is the unique key (by default, the "ref" column) and one
 * of the others is the text to display (by default the "name" column). The value posted is an array
 * of the values of the column given by the $ixcol variable for the rows selected.
 *
 * @param string $name the name of the select block. Usually the name of the config variable being set.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param string array $current the current value of the config variable being set.
 * @param array $choices the array of db rows that make up the choices.
 * @param string $ixcol the key in $choices (i.e., the db column) for the value of the choice
 *          Defaulted to 'ref'.
 * @param string $dispcolA the key in $choices (i.e., the db column) for the text to display to the
 *          user. Defaulted to 'name'.
 * @param string $dispcolB the key in $choices (i.e., the db column) for secondary text to display to
 *          the user.  Defaulted to '' indicating that only $dispcolA is to be displayed.
 * @param string $fmt the formatting string for combining $dispcolA and B when both are specified.
 *          Defaulted to $lang['plugin_field_fmt']. $fmt is all literal except for %A and %B which are
 *          replaced with values. In English $fmt is '%A(%B)' which results in the i-th choice
 *          displaying as: $choices[i][$dispcolA] . '(' . $choices[i][$dispcolB] . ')'
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
 function config_db_multi_select($name, $label, $current, $choices, $ixcol='ref', $dispcolA='name', $dispcolB='', $fmt='', $width=300)
    {
    global $lang;
?>
  <div class="Question">
    <label for="<?php echo $name?>" title="<?php echo str_replace('%cvn', $name, $lang['plugins-configvar'])?>"><?php echo $label?></label>
    <select name="<?php echo $name?>[]" id="<?php echo $name?>" multiple="multiple" size="7" style="width:<?php echo $width ?>px">
<?php
    foreach($choices as $item)
        {
        if ($dispcolB!='')
            {
            $usertext=str_replace(array('%A','%B'), array($item[$dispcolA],$item[$dispcolB]),$fmt==''?$lang['plugin_field_fmt']:$fmt);
            }
            else
            {
            $usertext=$item[$dispcolA];
            }
        echo '    <option value="' . $item[$ixcol] . '"' . (in_array($item[$ixcol],$current)?' selected':'') . '>' . $usertext . '</option>';
        }
?>
    </select>
  </div>
  <div class="clearerleft"></div>
<?php
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a multi select configuration variable whose values are chosen from among the results of
 * a db query to the setup page.
 *
 * @param string $config_var the name of the configuration variable to be added.
 * @param string $label the user text displayed to label the select block. Usually a $lang string.
 * @param array $choices the array of db rows that make up the choices.
 * @param string $ixcol the key in $choices (i.e., the db column) for the value of the choice.
 *          Defaulted to 'ref'.
 * @param string $dispcolA the key in $choices (i.e., the db column) for the text to display to the
 *          user. Defaulted to 'name'.
 * @param string $dispcolB the key in $choices (i.e., the db column) for secondary text to display to
 *          the user. Defaulted to '' indicating that only $dispcolA is to be displayed.
 * @param string $fmt the formatting string for combining $dispcolA and B when both are specified.
 *          Defaulted to $lang['plugin_field_fmt']. $fmt is all literal except for %A and %B which are
 *          replaced with values. In English $fmt is '%A(%B)' which results in the i-th choice
 *          displaying as: $choices[i][$dispcolA] . '(' . $choices[i][$dispcolB] . ')'
 * @param integer $width the width of the input field in pixels. Default: 300.
 */
function config_add_db_multi_select($config_var, $label, $choices, $ixcol='ref', $dispcolA='name', $dispcolB='',  $fmt='', $width=300)
    {
    return array('db_multi_select', $config_var, $label, $choices, $ixcol, $dispcolA, $dispcolB, $fmt, $width);
    }

/**
 * Return a data structure that will instruct the configuration page generator functions to
 * add a hidden configuration variable.
 *
 * @param string $config_var the name of the configuration variable to be added.
 */
function config_add_hidden($config_var)
    {
    return array('hidden_param', $config_var);
    }

/**
 *  Deprecated -- use config_text_input instead
 */
function config_text_field($name, $label, $value, $size='30')
    {
    config_text_input($name, $label, $value, false, $size*10);
    }

/**
 *  Deprecated -- use config_multi_user_select instead
 */
function config_userselect_field($name, $label, $values=array())
    {
    config_multi_user_select($name, $label, $values);
    }

/**
 *  Deprecated -- use config_single_ftype_select instead
 */
function config_field_select($name, $label, $value)
    {
    config_single_ftype_select($name, $label, $value);
    }

/**
 *  Deprecated -- use config_boolean_select instead
 */
function config_boolean_field($name, $label, $value)
    {
    config_boolean_select($name,$label,$value,array('False','True'));
    }

/**
 *  Deprecated -- use config_db_multi_select instead
 */
function config_custom_select_multi($name, $label, $available, $values, $index='ref', $nameindex='name', $additional='')
    {
    config_db_multi_select($name, $label, $values, $available, $index, $nameindex, $additional, '%A(%B)');
    }

/**
 *  Deprecated -- use config_single_select instead
 */
function config_custom_select($name, $label, $available, $value)
    {
    config_single_select($name, $label, $value, $available, false);
    }