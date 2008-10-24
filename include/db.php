<?
#
# db.php - Database functions, data manipulation functions
# and generic post/get handling
#
# Dan Huby (dan@montala.net) for Oxfam, April 2006

# Set larger time limit
set_time_limit(120);

# ensure no caching (dynamic site)

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: max-age=0");  // HTTP/1.1

#header("Cache-Control: post-check=0, pre-check=0", false);
#header("Pragma: no-cache");   


# Error handling
function errorhandler($errno, $errstr, $errfile, $errline)
	{
	global $baseurl;
	if (error_reporting())
		{
		?>
		</select></table></table></table>
		<div style="border:1px solid black;font-family:verdana,arial,helvetica;position:absolute;top:100px;left:100px; background-color:white;width:400px;padding:20px;border-bottom-width:4px;border-right-width:4px;font-size:15px;color:black;">
		<table cellpadding=5 cellspacing=0><tr><td valign=middle><img src="<?=$baseurl?>/pages/admin/gfx/cherrybomb.gif" width="48" height="48"></td><td valign=middle align=left><span style="font-size:22px;">Sorry, an error has occured.</span></td></tr></table>
		<p style="font-size:11px;color:black;margin-top:20px;">Please <a href="#" onClick="history.go(-1)">go back</a> and try something else.</p>
		<p style="font-size:11px;color:black;">You can <a href="check.php">check</a> your installation configuration.</p>
		<hr style="margin-top:20px;"><p style="font-size:11px;color:black;"><? echo "$errfile line $errline: $errstr"; ?></p>
		</div>
		<?
		# Uncomment next line to send e-mail with error details. Useful for debug.
		# mail ("errors@montala.net","Error", $_SERVER["REQUEST_URI"] . "\n$errfile line $errline: $errstr\n\n\nDumping SERVER:\n" . print_r($_SERVER,true) . "\n\nDumping ENVIRONMENT:\n" . print_r($_ENV,true) . "\n\nDumping GET:\n" . print_r($_GET,true) . "\n\nDumping POST:\n" . print_r($_POST,true));
		exit();
		}
	}
error_reporting(E_ALL);
set_error_handler("errorhandler");

# Set some defaults
$infobox=true;

# *** LOAD CONFIG ***
# Load the default config first, if it exists, so any new settings are present even if missing from config.php
if (file_exists(dirname(__FILE__)."/config.default.php")) {include "config.default.php";}
# Load the real config
if (!file_exists(dirname(__FILE__)."/config.php")) {exit("You must copy 'config.default.php' to 'config.php' in the include directory, and edit the file to alter the settings as appropriate.");}
include "config.php";

# Set the storage directory and URL if not already set.
if (!isset($storagedir)) {$storagedir=dirname(__FILE__)."/../filestore";}
if (!isset($storageurl)) {$storageurl=$baseurl."/filestore";}

# *** CONNECT TO DATABASE ***
mysql_connect($mysql_server,$mysql_username,$mysql_password);
mysql_select_db($mysql_db);
set_magic_quotes_runtime(0);

# statistics
$querycount=0;
$querytime=0;
$queryhist="";

# -----------LANGUAGES AND PLUGINS-------------------------------
# Include the appropriate language file
$pagename=str_replace(".php","",pagename());
if (isset($defaultlanguage)) {$language=$defaultlanguage;} else {$language="en";}
if (isset($_COOKIE["language"])) {$language=$_COOKIE["language"];}

# Always include the english pack (in case items have not yet been translated)
include dirname(__FILE__)."/../languages/en.php";
if ($language!="en")
	{
	include dirname(__FILE__)."/../languages/" . $language . ".php";
	}

/*
if ($config_pluginmanager_enabled)
{
 	unset($plugins);
	include dirname(__FILE__)."/.././dynamic/plugins.php";
}
*/

# Include language files for for each of the plugins too (if provided)
for ($n=0;$n<count($plugins);$n++)
	{
	$langpath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/languages/";
	if (file_exists($langpath . "en.php")) {include $langpath . "en.php";}
	
	if ($language!="en")
		{
		if (file_exists($langpath . $language . ".php")) {include $langpath . $language . ".php";}
		}
		
	# Also include plugin configuration.
	$configpath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/config/config.php";
	if (file_exists($configpath)) {include $configpath;}
	
	# Also include plugin hook file for this page.
	$hookpath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/hooks/" . $pagename . ".php";
	if (file_exists($hookpath)) {include $hookpath;}
	
	# Support an 'all' hook
	$hookpath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/hooks/all.php";
	if (file_exists($hookpath)) {include $hookpath;}
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

function hook($name,$pagename="")
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
			$found=eval ("return " . $function . "();");
			}
	
		# Specific hook	
		$function="Hook" . ucfirst($plugins[$n]) . ucfirst($pagename) . ucfirst($name);
		if (function_exists($function))
			{
			# Function must return 'true' if successful (so existing functionality is replaced)
			$found=eval ("return " . $function . "();");
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
    global $db,$querycount,$querytime,$queryhist;
    $querycount++; #$queryhist.=$sql . "\n\n\n";# stats
    $counter=0;
    $time_start = microtime(true);
    $result=mysql_query($sql);
    $error=mysql_error();
    if ($error!="")
        {
        if ($error=="Server shutdown in progress")
        	{
			echo "<span class=error>Sorry, but this query would return too many results. Please try refining your query by adding addition keywords or search parameters.<!--$sql--></span>";        	
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
        $time_end = microtime(true);
		$querytime += ($time_end - $time_start);
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
        $time_end = microtime(true);
		$querytime += ($time_end - $time_start);
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
					$sql.=$col[0] . " " . $col[1];
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
								
				$file=$path . "/" . $file;
				if (file_exists($file))
					{
					$f=fopen($file,"r");
					while (($col = fgetcsv($f,5000)) !== false)
						{
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
							$sql.=$col[0] . " " . $col[1];
							if ($col[4]!="") {$sql.=" default " . $col[4];}
							if ($col[3]=="PRI") {$sql.=" primary key";}
							if ($col[5]=="auto_increment") {$sql.=" auto_increment ";}
							sql_query($sql,false,-1,false);
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



	
function getval($val,$default)
    {
    # return a value from get/post or a default if neither set
    if (array_key_exists($val,$_POST)) {return $_POST[$val];}
    if (array_key_exists($val,$_GET)) {return $_GET[$val];}
    if (array_key_exists($val,$_COOKIE)) {return $_COOKIE[$val];}
    return $default;
    }

function getvalescaped($val,$default)
    {
    # return a value from get/post, escaped and SQL-safe
    return escape_check(getval($val,$default));
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
    while (!(strpos($text,"\\\\")===false))
        {
        $text=str_replace("\\\\","\\",$text);
        }
    return $text;
    }
    
function nicedate($date,$time=false,$wordy=false)
	{
	# format a MySQL ISO date in the UK style
	if ((strlen($date)==0) || (substr($date,0,4)=="0000")) {return "-";}
	if ($time) {return substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,2,2) . " "  . substr($date,11,5);} else {
	
	if ($wordy) {global $lang;return substr($date,8,2) . " " . @$lang["months"][substr($date,5,2)-1] . " " . substr($date,2,2);
	}
	else {return substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,2,2);}
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
    return $url;
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
                    "opera"=>"Opera",
                    "safari"=>"Safari",
                    "applewebkit"=>"Safari",
                    "msie 3."=>"IE3",
                    "msie 4."=>"IE4",
                    "msie 5.5"=>"IE5.5",
                    "msie 5."=>"IE5",
                    "msie 6."=>"IE6",
                    "msie 7."=>"IE7",
                    "msie"=>"IE",
                    "netscape"=>"Netscape",
                    "mozilla"=>"Mozilla" #catch all for mozilla references not specified above
                    );
    $osmatches=array(
                    "nt 6."=>"Vista",
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
                    "psp"=>"Sony PSP"
                    );
    $b="???";$os="???";
    foreach($bmatches as $key => $value)
        {if (!strpos($agent,$key)===false) {$b=$value;break;}}
    foreach($osmatches as $key => $value)
        {if (!strpos($agent,$key)===false) {$os=$value;break;}}
    return $os . " / " . $b;
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
	
function check_access_key($resource,$key)
	{
	# Verify a supplied external access key
	$user=sql_value("select distinct user value from external_access_keys where resource='$resource' and access_key='$key'",0);
	if ($user==0)
		{
		return false;
		}
	else
		{
		# "Emulate" the user that e-mailed the resource by setting the same group and permissions
		global $usergroup,$userpermissions;
		$userinfo=sql_query("select u.usergroup,g.permissions from user u join usergroup g on u.usergroup=g.ref where u.ref='$user'");
		if (count($userinfo)>0)
			{
			$usergroup=$userinfo[0]["usergroup"];
			$userpermissions=split(",",$userinfo[0]["permissions"]);
			}
		return true;
		}
	}
  
function check_access_key_collection($collection,$key)
	{
	$r=get_collection_resources($collection);
	for ($n=0;$n<count($r);$n++)
		{
		# Verify a supplied external access key for all resources in a collection
		$c=sql_value("select count(*) value from external_access_keys where resource='" . $r[$n] . "' and access_key='$key'",0);
		if ($c==0) {return false;}
		}	
	return true;
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


?>