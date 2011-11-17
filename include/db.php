<?php
/**
 * Database functions, data manipulation functions
 * and generic post/get handling
 * 
 * @author Dan Huby <dan@montala.net> for Oxfam, April 2006
 * @package ResourceSpace
 * @subpackage Includes
 */
#
# db.php - Database functions, data manipulation functions
# and generic post/get handling
#
# Dan Huby (dan@montala.net) for Oxfam, April 2006

# ensure no caching (dynamic site)

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

if (!function_exists('mysql_set_charset'))
	{
	function mysql_set_charset($charset)
		{
		return sql_query(sprintf("SET NAMES '%s'", $charset));
		}
	}

# Error handling
function errorhandler($errno, $errstr, $errfile, $errline)
	{
	global $baseurl,$pagename, $show_report_bug_link,$email_errors;
	if (!error_reporting()) {return true;}
	if (!isset($pagename) || $pagename!="upload_java")
		{
		?>
		</select></table></table></table>
		<div style="border:1px solid black;font-family:verdana,arial,helvetica;position:absolute;top:100px;left:100px; background-color:white;width:400px;padding:20px;border-bottom-width:4px;border-right-width:4px;font-size:15px;color:black;">
		<table cellpadding=5 cellspacing=0><tr><td valign=middle><img src="<?php echo $baseurl?>/pages/admin/gfx/cherrybomb.gif" width="48" height="48"></td><td valign=middle align=left><span style="font-size:22px;">Sorry, an error has occurred.</span></td></tr></table>
		<p style="font-size:11px;color:black;margin-top:20px;">Please <a href="#" onClick="history.go(-1)">go back</a> and try something else.</p>
		<?php global $show_error_messages; if ($show_error_messages) { ?>
		<p style="font-size:11px;color:black;">You can <a href="<?php echo $baseurl?>/pages/check.php">check</a> your installation configuration.</p>
		<hr style="margin-top:20px;"><p style="font-size:11px;color:black;"><?php echo "$errfile line $errline: $errstr"; ?></p>
		<?php } ?>
		</div>
		<?php
		if ($email_errors){
			global $email_notify,$email_from;
			send_mail($email_notify,"Error",$errfile." line ".$errline.": ".$errstr,$email_from,$email_from,"",null,"Error Reporting",false);
			}
		exit();
		}
	else
		{
		# Special error message format for Java uploader, so the error is correctly displayer
		exit("ERROR: Error processing file\\n\\n $errfile line $errline\\n$errstr");
		}
	}
error_reporting(E_ALL);
set_error_handler("errorhandler");

# Set some defaults
$infobox=true;

# *** LOAD CONFIG ***
# Load the default config first, if it exists, so any new settings are present even if missing from config.php
if (file_exists(dirname(__FILE__)."/config.default.php")) {include dirname(__FILE__) . "/config.default.php";}
# Load the real config
if (!file_exists(dirname(__FILE__)."/config.php")) {header ("Location: pages/setup.php" );die(0);}
include (dirname(__FILE__)."/config.php");

# Set time limit
set_time_limit($php_time_limit);

# Set the storage directory and URL if not already set.
if (!isset($storagedir)) {$storagedir=dirname(__FILE__)."/../filestore";}
if (!isset($storageurl)) {$storageurl=$baseurl."/filestore";}

# *** CONNECT TO DATABASE ***
mysql_connect($mysql_server,$mysql_username,$mysql_password);
mysql_select_db($mysql_db);

// If $mysql_charset is defined, we use it
// else, we use the default charset for mysql connection.
if(isset($mysql_charset))
	{
	if($mysql_charset)
		{
		mysql_set_charset($mysql_charset);
		}
	}

# Set MySQL Strict Mode (if configured)
if ($mysql_force_strict_mode)
	{
	sql_query("SET SESSION sql_mode='STRICT_ALL_TABLES'");	
	}

#if (function_exists("set_magic_quotes_runtime")) {@set_magic_quotes_runtime(0);}

# statistics
$querycount=0;
$querytime=0;
$querylog=array();


# -----------LANGUAGES AND PLUGINS-------------------------------
# Include the appropriate language file
$pagename=str_replace(".php","",pagename());

if (isset($defaultlanguage))
	$language=$defaultlanguage;
else
	$language=http_get_preferred_language();

if (isset($_COOKIE["language"])) {$language=$_COOKIE["language"];}
if (isset($_GET["language_set"])) {$language=$_GET["language_set"];setcookie("language",$_GET["language_set"]);}

# Fix due to rename of US English language file
if ($language=="us") {$language="en-US";}

# Make sure the provided language is a valid language
if (!array_key_exists($language,$languages))
	{
		if (isset($defaultlanguage))
			$language=$defaultlanguage;
		else
			$language='en';
	}

# Always include the english pack (in case items have not yet been translated)
include dirname(__FILE__)."/../languages/en.php";
if ($language!="en")
	{
	include dirname(__FILE__)."/../languages/" . safe_file_name($language) . ".php";
	}

# Register all plugins
if ($use_plugins_manager){
	include "plugin_functions.php";
	$legacy_plugins = $plugins; # Make a copy of plugins activated via config.php
	#Check that manually (via config.php) activated plugins are included in the plugins table.
	foreach($plugins as $plugin_name){
		if ($plugin_name!=''){
			if(sql_value("SELECT inst_version AS value FROM plugins WHERE name='$plugin_name'",'')==''){
				#Installed plugin isn't marked as installed in the DB.  Update it now.
				#Check if there's a plugin.yaml file to get version and author info.
				$plugin_yaml_path = dirname(__FILE__)."/../plugins/{$plugin_name}/{$plugin_name}.yaml"; 
				$p_y = get_plugin_yaml($plugin_yaml_path, false);
				#Write what information we have to the plugin DB.
				sql_query("REPLACE plugins(inst_version, author, descrip, name, info_url, update_url, config_url) ".
						  "VALUES ('{$p_y['version']}','{$p_y['author']}','{$p_y['desc']}','{$plugin_name}'," .
						  "'{$p_y['info_url']}','{$p_y['update_url']}','{$p_y['config_url']}')");
			}
		}
	}
	$active_plugins = (sql_query("SELECT name,enabled_groups,config FROM plugins WHERE inst_version>=0"));
	foreach($active_plugins as $plugin){
	
		# Check group access, only enable for global access at this point
		if ($plugin['enabled_groups']=='')
			{
			register_plugin($plugin['name'],$plugin['config']);
			}
	}
}
else {
for ($n=0;$n<count($plugins);$n++)
	{
	register_plugin($plugins[$n]);
	}
}

# Set character set.
if (($pagename!="download") && ($pagename!="graph")) {header("Content-Type: text/html; charset=UTF-8");} // Make sure we're using UTF-8.
#------------------------------------------------------


# Set a base URL part consisting of the part after the server name, i.e. for absolute URLs and cookie paths.
$baseurl=str_replace(" ","%20",$baseurl);
$bs=explode("/",$baseurl);
$bs=array_slice($bs,3);
$baseurl_short="/" . join("/",$bs) . (count($bs)>0?"/":"");



# Pre-load all text for this page.
$site_text=array();
$results=sql_query("select language,name,text from site_text where (page='$pagename' or page='all') and (specific_to_group is null or specific_to_group=0)");
for ($n=0;$n<count($results);$n++) {$site_text[$results[$n]["language"] . "-" . $results[$n]["name"]]=$results[$n]["text"];}

# Blank the header insert
$headerinsert="";

# Initialise hook for plugins
hook("initialise");

function hook($name,$pagename="",$params=array())
	{
	# Plugin architecture. Look for a hook with this name and execute.
	if ($pagename=="") {global $pagename;} # If page name not provided, use global page name.
	global $plugins;
	
	$found=false;
	for ($n=0;$n<count($plugins);$n++)
		{
		# "All" hooks
		$function="Hook" . ucfirst($plugins[$n]) . "All" . ucfirst($name);
		if (function_exists($function)) 
			{
			# Function must return 'true' if successful (so existing functionality is replaced)
			$found=call_user_func_array($function, $params);
			}
	
		# Specific hook	
		$function="Hook" . ucfirst($plugins[$n]) . ucfirst($pagename) . ucfirst($name);
		if (function_exists($function))
			{
			# Function must return 'true' if successful (so existing functionality is replaced)
			$found=call_user_func_array($function, $params);
			}
		}
	return $found;
	}


function sql_query($sql,$cache=false,$fetchrows=-1,$dbstruct=true)
    {
    # sql_query(sql) - execute a query and return the results as an array.
	# Database functions are wrapped in this way so supporting a database server other than MySQL is 
	# easier.
	# $cache is not used at this time - it was intended for disk based results caching which may be added in the future.
    # If $fetchrows is set we don't have to loop through all the returned rows. We
    # just fetch $fetchrows row but pad the array to the full result set size with empty values.
    # This has been added retroactively to support large result sets, yet a pager can work as if a full
    # result set has been returned as an array (as it was working previously).
    global $db,$querycount,$querytime,$config_show_performance_footer,$querylog,$debug_log;
    $counter=0;
    if ($config_show_performance_footer)
    	{
    	# Stats
    	# Start measuring query time
    	$time_start = microtime(true);
   	    $querycount++;
    	}
    	
    if ($debug_log) {debug("SQL: " . $sql);}
    
    # Execute query
    $result=mysql_query($sql);
	
    if ($config_show_performance_footer){
    	# Stats
   		# Log performance data
		$time_end = microtime(true);
		$time_total=($time_end - $time_start);
		if (isset($querylog[$sql])){
			$querylog[$sql]['dupe']=$querylog[$sql]['dupe']+1;
			$querylog[$sql]['time']=$querylog[$sql]['time']+$time_total;
		} 
		else {
			$querylog[$sql]['dupe']=1;
			$querylog[$sql]['time']=$time_total;
		}

		$querytime += $time_total;
	}
		
    $error=mysql_error();
    if ($error!="")
        {
        if ($error=="Server shutdown in progress")
        	{
			echo "<span class=error>Sorry, but this query would return too many results. Please try refining your query by adding addition keywords or search parameters.<!--$sql--></span>";        	
        	}
        elseif (substr($error,0,15)=="Too many tables")
        	{
			echo "<span class=error>Sorry, but this query contained too many keywords. Please try refining your query by removing any surplus keywords or search parameters.<!--$sql--></span>";        	
        	}
        else
        	{
        	# Check that all database tables and columns exist using the files in the 'dbstruct' folder.
        	if ($dbstruct) # should we do this?
        		{
        		CheckDBStruct("dbstruct");
        		global $plugins;
        		for ($n=0;$n<count($plugins);$n++)
        			{
        			CheckDBStruct("plugins/" . $plugins[$n] . "/dbstruct");
        			}
        		
        		# Try again (no dbstruct this time to prevent an endless loop)
        		return sql_query($sql,$cache,$fetchrows,false);
        		exit();
        		}
        	
	        errorhandler("N/A", $error . "<br/><br/>" . $sql, "(database)", "N/A");
	        }
        exit;
        }
    elseif ($result===true)
        {
        # no result set, (query was insert, update etc.)
        }
    else
        {
        $row=array();
        while (($rs=mysql_fetch_array($result)) && (($counter<$fetchrows) || ($fetchrows==-1)))
            {
            while (list($name,$value)=each($rs))
                {
                if (!is_integer($name)) # do not run for integer values (MSSQL returns two keys for each returned column, a numeric and a text)
                    {
                    $row[$counter][$name]=str_replace("\\","",stripslashes($value));
                    }
                }
            $counter++;
            }

		# If we haven't returned all the rows ($fetchrows isn't -1) then we need to fill the array so the count
		# is still correct (even though these rows won't be shown).
		$rows=count($row);
		$totalrows=mysql_num_rows($result);#echo "-- $rows out of $totalrows --";
		if (($fetchrows!=-1) && ($rows<$totalrows)) {$row=array_pad($row,$totalrows,0);}
        return $row;
        }
    }
	
	
function sql_value($query,$default)
    {
    # return a single value from a database query, or the default if no rows
    # The value returned must have the column name aliased to 'value'
    $result=sql_query($query);
    if (count($result)==0) {return $default;} else {return $result[0]["value"];}
    }

function sql_array($query)
	{
	# Like sql_value() but returns an array of all values found.
    # The value returned must have the column name aliased to 'value'
	$return=array();
    $result=sql_query($query);
    for ($n=0;$n<count($result);$n++)
    	{
    	$return[]=$result[$n]["value"];
    	}
    return $return;
	}

function sql_insert_id()
	{
	# Return last inserted ID (abstraction)
	return mysql_insert_id();
	}

function CheckDBStruct($path)
	{
	# Check the database structure against the text files stored in $path.
	# Add tables / columns / data / indices as necessary.
	global $mysql_db;
	
	# Check for path
	$path=dirname(__FILE__) . "/../" . $path; # Make sure this works when called from non-root files..
	if (!file_exists($path)) {return false;}
	
	# Tables first.
	# Load existing tables list
	$ts=sql_query("show tables");
	$tables=array();
	for ($n=0;$n<count($ts);$n++)
		{
		$tables[]=$ts[$n]["Tables_in_" . $mysql_db];
		}
	$dh=opendir($path);
	while (($file = readdir($dh)) !== false)
		{
		if (substr($file,0,6)=="table_")
			{
			$table=str_replace(".txt","",substr($file,6));
			
			# Check table exists
			if (!in_array($table,$tables))
				{
				# Create Table
				$sql="";
				$f=fopen($path . "/" . $file,"r");
				while (($col = fgetcsv($f,5000)) !== false)
					{
					if ($sql.="") {$sql.=", ";}
					$sql.=$col[0] . " " . str_replace("§",",",$col[1]);
					if ($col[4]!="") {$sql.=" default " . $col[4];}
					if ($col[3]=="PRI") {$sql.=" primary key";}
					if ($col[5]=="auto_increment") {$sql.=" auto_increment ";}
					}
				
				sql_query("create table $table ($sql)",false,-1,false);
				
				# Add initial data
				$data=str_replace("table_","data_",$file);
				if (file_exists($path . "/" . $data))
					{
					$f=fopen($path . "/" . $data,"r");
					while (($row = fgetcsv($f,5000)) !== false)
						{
						# Escape values
						for ($n=0;$n<count($row);$n++)
							{
							$row[$n]=escape_check($row[$n]);
							$row[$n]="'" . $row[$n] . "'";
							if ($row[$n]=="''") {$row[$n]="null";}
							}
						sql_query("insert into $table values (" . join (",",$row) . ")",false,-1,false);
						}
					}
				}
			else
				{
				# Table already exists, so check all columns exist
				
				# Load existing table definition
				$existing=sql_query("describe $table",false,-1,false);
				
				##########
				## RS-specific mod:
				# copy needed resource_data into resource for search displays
				if ($table=="resource"){
					$joins=get_resource_table_joins();
					for ($m=0;$m<count($joins);$m++){
						
						# Look for this column in the existing columns.	
						$found=false;

						for ($n=0;$n<count($existing);$n++)
							{
							if ("field".$joins[$m]==$existing[$n]["Field"]) {$found=true;}
							}
						if (!$found)
							{
							# Add this column.
							$sql="alter table $table add column ";
							$sql.="field".$joins[$m] . " VARCHAR(200)";
							sql_query($sql,false,-1,false);
							$values=sql_query("select resource,value from resource_data where resource_type_field=$joins[$m]");
	
							for($x=0;$x<count($values);$x++){
								$value=$values[$x]['value'];
								$resource=$values[$x]['resource'];
								sql_query("update resource set field$joins[$m]='".escape_check($value)."' where ref=$resource");	
						    }	
						}
					}	
				}		
				##########
				
				##########
				## RS-specific mod:
				# add theme columns to collection table as needed.
				global $theme_category_levels;
				if ($table=="collection"){
					for ($m=1;$m<=$theme_category_levels;$m++){
						if ($m==1){$themeindex="";}else{$themeindex=$m;}
						# Look for this column in the existing columns.	
						$found=false;

						for ($n=0;$n<count($existing);$n++)
							{
							if ("theme".$themeindex==$existing[$n]["Field"]) {$found=true;}
							}
						if (!$found)
							{
							# Add this column.
							$sql="alter table $table add column ";
							$sql.="theme".$themeindex . " VARCHAR(100)";
							sql_query($sql,false,-1,false);

						}
					}	
				}		
				
				##########				
								
				$file=$path . "/" . $file;
				if (file_exists($file))
					{
					$f=fopen($file,"r");
					while (($col = fgetcsv($f,5000)) !== false)
						{
						if (count($col)> 1){
							# Look for this column in the existing columns.
							$found=false;
							for ($n=0;$n<count($existing);$n++)
								{
								if ($existing[$n]["Field"]==$col[0]) {$found=true;}
								}
							if (!$found)
								{
								# Add this column.
								$sql="alter table $table add column ";
								$sql.=$col[0] . " " . str_replace("§",",",$col[1]); # Allow commas to be entered using '§', necessary for a type such as decimal(2,10)
								if ($col[4]!="") {$sql.=" default " . $col[4];}
								if ($col[3]=="PRI") {$sql.=" primary key";}
								if ($col[5]=="auto_increment") {$sql.=" auto_increment ";}
								sql_query($sql,false,-1,false);
								}
							}
						}
					}
				}
				
			# Check all indices exist
			# Load existing indexes
			$existing=sql_query("show index from $table",false,-1,false);
					
			$file=str_replace("table_","index_",$file);
			if (file_exists($path . "/" . $file))
				{
				$done=array(); # List of indices already processed.
				$f=fopen($path . "/" . $file,"r");
				while (($col = fgetcsv($f,5000)) !== false)
					{
					# Look for this index in the existing indices.
					$found=false;
					for ($n=0;$n<count($existing);$n++)
						{
						if ($existing[$n]["Key_name"]==$col[2]) {$found=true;}
						}
					if (!$found && !in_array($col[2],$done))
						{
						# Add this index.
						
						# Fetch list of columns for this index
						$cols=array();
						$f2=fopen($path . "/" . $file,"r");
						while (($col2 = fgetcsv($f2,5000)) !== false)
							{
							if ($col2[2]==$col[2]) {$cols[]=$col2[4];}
							}
						
						$sql="create index " . $col[2] . " on $table (" . join(",",$cols) . ")";
						sql_query($sql,false,-1,false);
						$done[]=$col[2];
						}
					}
				}
			}
		}
	}



	
function getval($val,$default,$force_numeric=false)
    {
    # return a value from POST, GET or COOKIE (in that order), or $default if none set
    if (array_key_exists($val,$_POST)) {return ($force_numeric && !is_numeric($_POST[$val])?$default:$_POST[$val]);}
    if (array_key_exists($val,$_GET)) {return ($force_numeric && !is_numeric($_GET[$val])?$default:$_GET[$val]);}
    if (array_key_exists($val,$_COOKIE)) {return ($force_numeric && !is_numeric($_COOKIE[$val])?$default:$_COOKIE[$val]);}
    return $default;
    }

function getvalescaped($val,$default,$force_numeric=false)
    {
    # return a value from get/post, escaped and SQL-safe
    $value=escape_check(getval($val,$default,$force_numeric));
    
    # XSS vulnerability checking
    if (strpos(strtolower($value),"<script")!==false) {return $default;}
    
    return $value;
    }
    
function getuid()
    {
    # generate a unique ID
    return strtr(mysql_escape_string(microtime() . " " . $_SERVER["REMOTE_ADDR"]),". ","--");
    }

function escape_check($text) #only escape a string if we need to, to prevent escaping an already escaped string
    {
    $text=mysql_escape_string($text);
    # turn all \\' into \'
    while (!(strpos($text,"\\\\'")===false))
        {
        $text=str_replace("\\\\'","\\'",$text);
        }

	# Remove any backslashes that are not being used to escape single quotes.
    $text=str_replace("\\'","{bs}'",$text);
    $text=str_replace("\\n","{bs}n",$text);
    $text=str_replace("\\r","{bs}r",$text);

    $text=str_replace("\\","",$text);
    $text=str_replace("{bs}'","\\'",$text);            
    $text=str_replace("{bs}n","\\n",$text);            
    $text=str_replace("{bs}r","\\r",$text);  
                      
    return $text;
    }

function unescape($text) 
    {
	// for comparing escape_checked strings against mysql content because	
	// just doing $text=str_replace("\\","",$text);	does not undo escape_check

	# Remove any backslashes that are not being used to escape single quotes.
    $text=str_replace("\\'","\'",$text);
    $text=str_replace("\\n","\n",$text);
    $text=str_replace("\\r","\r",$text);
    $text=str_replace("\\","",$text);
    

    return $text;
    }


if (!function_exists("nicedate")) {
function nicedate($date,$time=false,$wordy=true)
	{
	# format a MySQL ISO date
	# Always use the 'wordy' style from now on as this works better internationally.
	global $lang;
	$y = substr($date,0,4);
	if (($y=="") || ($y=="0000")) return "-";
	$m = @$lang["months"][substr($date,5,2)-1];
	if ($m=="") return $y;
	$d = substr($date,8, 2);
	if ($d=="" || $d=="00") return $m . " " . $y;
	$t = $time ? (" @ "  . substr($date,11,5)) : "";
	return $d . " " . $m . " " . substr($y, 2, 2) . $t;
	}	
}

function redirect($url)
	{
	global $baseurl;
	if (substr($url,0,1)=="/")
		{
		# redirect to an absolute URL
		header ("Location: " . $url);
		}
	else
		{
		# redirect to a relative URL
		header ("Location: " . $baseurl . "/" . $url);
		}
	exit();
	}
	
function checkperm($perm)
    {
    # check that the user has the $perm permission
    global $userpermissions;
    if (!(isset($userpermissions))) {return false;}
    if (in_array($perm,$userpermissions)) {return true;} else {return false;}
    }
    
function pagename()
	{
	$urlparts=explode("/",$_SERVER["PHP_SELF"]);
    $url=$urlparts[count($urlparts)-1];
    return escape_check($url);
    }
    
function text($name)
	{
	global $config_disable_nohelp_warning;

	# Returns site text with name $name, or failing that returns dummy text.
	global $site_text,$pagename,$language,$languages,$usergroup;
	if (array_key_exists($language . "-" . $name,$site_text)) {return $site_text[$language . "-" .$name];} 
	
	# Can't find the language key? Look for it in other languages.
	reset($languages);foreach ($languages as $key=>$value)
		{
		if (array_key_exists($key . "-" . $name,$site_text)) {return $site_text[$key . "-" . $name];} 		
		}
	if (!array_key_exists('en', $languages))
		{
		if (array_key_exists("en-" . $name,$site_text)) {return $site_text["en-" . $name];}
		}

	return "";
	}
    
function get_section_list($page)
	{
	return sql_array("select name value from site_text where page='$page' and name<>'introtext' order by name");
	}

function resolve_user_agent($agent)
    {
    if ($agent=="") {return "-";}
    $agent=strtolower($agent);
    $bmatches=array( # Note - order is important - first come first matched
                    "firefox"=>"Firefox",
                    "chrome"=>"Chrome",
                    "opera"=>"Opera",
                    "safari"=>"Safari",
                    "applewebkit"=>"Safari",
                    "msie 3."=>"IE3",
                    "msie 4."=>"IE4",
                    "msie 5.5"=>"IE5.5",
                    "msie 5."=>"IE5",
                    "msie 6."=>"IE6",
                    "msie 7."=>"IE7",
                    "msie 8."=>"IE8",
                    "msie 9."=>"IE9",
                    "msie 10."=>"IE10",
                    "msie"=>"IE",
                    "netscape"=>"Netscape",
                    "mozilla"=>"Mozilla"
                    #catch all for mozilla references not specified above
                    );
    $osmatches=array(
                    "iphone"=>"iPhone",                    
                    "nt 6.1"=>"Windows 7",
                    "nt 6.0"=>"Vista",
                    "nt 5.2"=>"WS2003",
                    "nt 5.1"=>"XP",
                    "nt 5.0"=>"2000",
                    "nt 4.0"=>"NT4",
                    "windows 98"=>"98",
                    "linux"=>"Linux",
                    "freebsd"=>"FreeBSD",
                    "os x"=>"OS X",
                    "mac_powerpc"=>"Mac",
                    "sunos"=>"Sun",
                    "psp"=>"Sony PSP",
                    "api"=>"Api Client"
                    );
    $b="???";$os="???";
    foreach($bmatches as $key => $value)
        {if (!strpos($agent,$key)===false) {$b=$value;break;}}
    foreach($osmatches as $key => $value)
        {if (!strpos($agent,$key)===false) {$os=$value;break;}}
    return $os . " / " . $b;
    }
    



function get_ip()
	{
	global $ip_forwarded_for;
	
	if ($ip_forwarded_for)
		{
		# Attempt to read Apache forwarding header instead.
		$headers = @apache_request_headers();
		if (@array_key_exists('X-Forwarded-For', $headers)) {return $headers["X-Forwarded-For"];}
		}
		
	# Returns the IP address for the current user.
	if (array_key_exists("REMOTE_ADDR",$_SERVER)) {return $_SERVER["REMOTE_ADDR"];}


	# Can't find an IP address.
	return "???";
	}
function fixSmartQuotes($string){ 
    $pre = chr(226).chr(128);
    
    $search = array( $pre . chr(152),
                    $pre . chr(153),
                    $pre . chr(156),
                    $pre . chr(157),
                    $pre . chr(147),
                    chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(150),
                    chr(151),
                    chr(130),
                    chr(133),
                    chr(152),
                    chr(154),
                    chr(160)
                    ); 
 
    $replace = array( "'",
                             "'",
                             '"',
                             '"',
                             '-',
                             "'", 
                             "'", 
                             '"', 
                             '"', 
                             '-',
                             '-',
                             "&#8218;",
                             "&#8230;",
                             '-',
                             '\"',
                             ' ' ); 
                    
    return str_replace($search, $replace, $string); 
} 

function safe_file_name($name)
	{
	# Returns a file name stipped of all non alphanumeric values
	# Spaces are replaced with underscores
	$alphanum="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-";
	$name=str_replace(" ","_",$name);
	$newname="";
	for ($n=0;$n<strlen($name);$n++)
		{
		$c=substr($name,$n,1);
		if (strpos($alphanum,$c)!==false) {$newname.=$c;}
		}
	$newname=substr($newname,0,30);
	return $newname;
	}

function daily_stat($activity_type,$object_ref)
	{
	# Update the daily statistics after a loggable event.
	# the daily_stat table contains a counter for each 'activity type' (i.e. download) for each object (i.e. resource)
	# per day.
	$date=getdate();$year=$date["year"];$month=$date["mon"];$day=$date["mday"];
	

    # Set object ref to zero if not set.

    if ($object_ref=="") {$object_ref=0;}

    
	# Find usergroup
	global $usergroup;
	if (!isset($usergroup)) {$usergroup=0;}
	
	# First check to see if there's a row
	$count=sql_value("select count(*) value from daily_stat where year='$year' and month='$month' and day='$day' and usergroup='$usergroup' and activity_type='$activity_type' and object_ref='$object_ref'",0);
	if ($count==0)
		{
		# insert
		sql_query("insert into daily_stat(year,month,day,usergroup,activity_type,object_ref,count) values ('$year','$month','$day','$usergroup','$activity_type','$object_ref','1')");
		}
	else
		{
		# update
		sql_query("update daily_stat set count=count+1 where year='$year' and month='$month' and day='$day' and usergroup='$usergroup' and activity_type='$activity_type' and object_ref='$object_ref'");
		}
	}    
	
function register_plugin($plugin,$config="")
	{
	global $plugins,$language,$pagename,$lang,$applicationname;

	# Add to the plugins array if not already present, to support this function being called
	# later on (i.e. in config_override()).
	if (!in_array($plugin,$plugins)) {$plugins[]=$plugin;}
	
	# Include language file
	$langpath=dirname(__FILE__)."/../plugins/" . $plugin . "/languages/";
	if (file_exists($langpath . "en.php")) {include $langpath . "en.php";}
	if ($language!="en")
		{
		if (file_exists($langpath . $language . ".php")) {include $langpath . $language . ".php";}
		}
		
	# Also include plugin configuration.
	$configpath=dirname(__FILE__)."/../plugins/" . $plugin . "/config/config.php";
	if (file_exists($configpath)) {include $configpath;}
	
	//eval ($config);
	
	# Copy config variables to global scope.
	$vars=get_defined_vars();
	foreach ($vars as $name=>$value)
		{
		global $$name;
		$$name=$value;
		}
	
	# Also include plugin hook file for this page.
	$hookpath=dirname(__FILE__)."/../plugins/" . $plugin . "/hooks/" . $pagename . ".php";
	if (file_exists($hookpath)) {include $hookpath;}
	
	# Support an 'all' hook
	$hookpath=dirname(__FILE__)."/../plugins/" . $plugin . "/hooks/all.php";
	if (file_exists($hookpath)) {include $hookpath;}

	return true;	
	}
	
/**
 * Recursively removes a directory.
 * 
 * Recursively removes a directory.  Currently this is only used by the plugin
 * management interface to permanently delete a plugin.  This function does
 * not check to see that php is <em>allowed</em> to delete the specified
 * path currently.
 * 
 * @todo ADD - Check that PHP has permissions to delete $path
 * @param string $path Directory path to remove.
 */
function rcRmdir ($path){ # Recursive rmdir function.
	if (is_dir($path)){
	    $dirh = opendir($path);
	    while (false !== ($file = readdir($dirh))){
	        if (is_dir($path.'/'.$file)){
	        	if (!((strlen($file)==1 && $file[0]=='.') || (substr($file,0,2)=='..'))){
	        		rcRmdir($path.'/'.$file);
	        	}
	        }
	        else {
	            unlink($path.'/'.$file);
	        }
		}
		closedir($dirh);
		rmdir($path);
	}
}

function get_resource_table_joins(){
	
	global 
	$rating_field,
	$sort_fields,
	$small_thumbs_display_fields,
	$xl_thumbs_display_fields,
	$thumbs_display_fields,
	$list_display_fields,
	$data_joins,
	$metadata_template_title_field,
	$view_title_field,
	$date_field,
	$config_sheetlist_fields,
	$config_sheetthumb_fields;
	
	$joins=array_merge(
	$sort_fields,
	$small_thumbs_display_fields,
	$xl_thumbs_display_fields,
	$thumbs_display_fields,
	$list_display_fields,
	$data_joins,
	$config_sheetlist_fields,
	$config_sheetthumb_fields,
		array(
		$rating_field,
		$metadata_template_title_field,
		$view_title_field,
		$date_field)
	);
	
	$joins=array_unique($joins);
	$n=0;
	foreach ($joins as $join){
		if ($join!=""){
			$return[$n]=$join;
			$n++;
			}
		}
	return $return;
	}
    
function debug($text)
	{
	# Output some text to a debug file.
	# For developers only
	global $debug_log;
	if (!$debug_log) {return true;} # Do not execute if switched off.
	
	# Cannot use the general.php: get_temp_dir() method here since general may not have been included.
	$f=fopen(get_debug_log_dir() . "/debug.txt","a");
	fwrite($f,$text . "\n");
	fclose ($f);
	return true;
	}
	
/**
 * Determines where the debug log will live.  Typically, same as tmp dir (See general.php: get_temp_dir().
 * Since general.php may not be included, we cannot use that method so I have created this one too.
 * @return string - The path to the debug_log directory.
 */
function get_debug_log_dir()
{
    // Set up the default.
    $result = dirname(dirname(__FILE__)) . "/filestore/tmp";

    // if $tempdir is explicity set, use it.
    if(isset($tempdir))
    {
        // Make sure the dir exists.
        if(!is_dir($tempdir))
        {
            // If it does not exist, create it.
            mkdir($tempdir, 0777);
        }
        $result = $tempdir;
    }
    // Otherwise, if $storagedir is set, use it.
    else if (isset($storagedir))
    {
        // Make sure the dir exists.
        if(!is_dir($storagedir . "/tmp"))
        {
            // If it does not exist, create it.
            mkdir($storagedir . "/tmp", 0777);
        }
        $result = $storagedir . "/tmp";
    }
    else
    {
        // Make sure the dir exists.
        if(!is_dir($result))
        {
            // If it does not exist, create it.
            mkdir($result, 0777);
        }
    }
    // return the result.
    return $result;
}
