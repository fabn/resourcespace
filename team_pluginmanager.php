<?
include(dirname(__FILE__)."./include/db.php");
include(dirname(__FILE__)."./include/authenticate.php");
include(dirname(__FILE__)."./include/general.php");
include(dirname(__FILE__)."./include/header.php");

$plugindir = MyNormalizePath(dirname(__FILE__)."/./plugins");
//$plugindir = str_replace("\\","/",$plugindir);
//$plugindir = myrealpath($plugindir);

$pluginfiledir = MyNormalizePath(dirname(__FILE__)."/./dynamic");
//$pluginfiledir = str_replace("\\","/",$pluginfiledir);
//$pluginfiledir = myrealpath($pluginfiledir);

//$plugindir = "c:/AppServ/www/mam/plugins";

function IsPluginActive($pluginname)
{
	global $activeplugins;
	
	for($i=0; $i<count($activeplugins); $i++)
	{
	 	if ($activeplugins[$i]==$pluginname) return true;
	}	
	return false;
}

$allplugins = array();
function LoadAllPlugins()
{
 	global $allplugins;
 	global $plugindir;
 	
	//echo "Reading dir [$plugindir]...<br/>";
	$folder=dir($plugindir); 
	
	$i = 0;
	while($folderEntry=$folder->read())
	{
	 	if (($folderEntry!=".")&&($folderEntry!="..")&&($folderEntry!=".svn"))
	 	{
	 	 	if (is_dir($plugindir.'/'.$folderEntry))
	 	 	{
			 	$allplugins[$i] = $folderEntry;
				$i++;
			}
		}
	} 
	$folder->close(); 	
}

$activeplugins = array();
/*
function LoadActivePlugins()
{
 	global $activeplugins; 
 	global $plugindir;
 	
	$plugindatafile = $plugindir."/plugins.txt";
	//echo "Reading plugin file [$plugindatafile]...<br/>";
	$handle = fopen($plugindatafile, "r"); // Open file 
	if ($handle) {
	 
		$i = 0;
		while (!feof($handle)) // Loop til end of file.
		{
			$buffer = fgets($handle, 4096); // Read a line.
			$buffer = str_replace("\n", "", $buffer);
			$buffer = str_replace("\r", "", $buffer);
			$activeplugins[$i] = $buffer;
			//echo $buffer;
			$i++;
		}
		
		fclose($handle); // Close the file.
	} 
}
*/
function LoadActivePlugins()
{
 	global $activeplugins; 
 	global $plugindir;
 	global $pluginfiledir;
 	
	$plugindatafile = $pluginfiledir."/plugins.php";
	//echo "Reading plugin file [$plugindatafile]...<br/>";
	$handle = fopen($plugindatafile, "r"); // Open file 
	if ($handle) {
	 
		$i = 0;
		while (!feof($handle)) // Loop til end of file.
		{
			$buffer = fgets($handle, 4096); // Read a line.
			$buffer = str_replace("\n", "", $buffer);
			$buffer = str_replace("\r", "", $buffer);
			
			//$pos = strpos($buffer, ']="');
			$pos = strpos($buffer, '###RSPLUGIN=');
			
			if ($pos === false) continue;
			
			//$pluginname = substr($buffer,  $pos+3, count($buffer)-3);
			$pluginname = str_replace("###RSPLUGIN=", "", $buffer);
			
			$activeplugins[$i] = $pluginname;
			//echo $buffer;
			$i++;
		}
		
		fclose($handle); // Close the file.
	} 
}

function ActivePlugin($pluginname)
{
 	global $activeplugins;
 	
	$activeplugins[count($activeplugins)]=$pluginname;
}

function DeactivePlugin($pluginname)
{
 	global $activeplugins;
 	
	for($i=0; $i<count($activeplugins); $i++)
	{
	 	if ($activeplugins[$i]==$pluginname) unset($activeplugins[$i]);
	}	
}

function MyNormalizePath($path)
{
	$path = str_replace("\\","/",$path);
	$path = myrealpath($path);
	return $path;
}

function GetAboutText($pluginname)
{
 	global $plugindir;
 	
 	$abouttext = "";
 	
 	$aboutpath = $plugindir."/".$pluginname."/about.txt";
 	
 	if (file_exists($aboutpath))
 	{
		$handle = fopen($aboutpath, "r"); // Open file 
		if ($handle) 
		{
			while (!feof($handle)) // Loop til end of file.
			{
				$buffer = fgets($handle, 4096); // Read a line.
				$abouttext .= $buffer;
			}
			
			fclose($handle); // Close the file.
		}
		
	}
	
	return $abouttext;
}

/*
function WritePluginTxtFile()
{
 	global $plugindir;
 	global $pluginfiledir;
 	global $activeplugins;

	// Write plugin file
	$pluginincludefile = $pluginfiledir."/plugins.txt";
	//echo "Writing plugin file [$pluginincludefile]...<br/>";
	$handle = fopen($pluginincludefile, "w"); 
	if ($handle) {
		for($i=0; $i<count($activeplugins); $i++)
		{
		 	if ((isset($activeplugins[$i]))&&($activeplugins[$i]!=""))
		 	{
				fwrite($handle, $activeplugins[$i]."\r\n");
			}
		}
	}
}
*/

function WritePluginFile()
{
 	global $plugindir;
 	global $pluginfiledir;
 	global $activeplugins;

	// Write plugin file
	$pluginincludefile = $pluginfiledir."/plugins.php";
	//echo "Writing plugin file [$pluginincludefile]...<br/>";
	$handle = fopen($pluginincludefile, "w"); // Open file 
	if ($handle) {
	 	fwrite($handle, "<?\r\n");
	 	fwrite($handle, "// File generated automatically by the ResourceSpace Plugin Manager - do not edit manually!\r\n");
	 	$j = 0;
		for($i=0; $i<count($activeplugins); $i++)
		{
		 	if ((isset($activeplugins[$i]))&&($activeplugins[$i]!=""))
		 	{
			 	$s = "###RSPLUGIN=".$activeplugins[$i]."\r\n";
				fwrite($handle, $s);
			 	$s = "\$plugins[".$j."]=\"".$activeplugins[$i]."\";\r\n";
				fwrite($handle, $s);
				$j++;
			}
		}
		fwrite($handle, "?>\r\n");
	}
}

function ShowPlugins()
{
 	global $allplugins;
 	
 	echo "<b>Plugins available:</b>";
	echo "<table class=\"InfoTable\">";
	for($i=0; $i<count($allplugins); $i++)
	{
	 	if (IsPluginActive($allplugins[$i])) { $link="deactivate"; $class="#00ff00"; $text="Switch OFF"; }
	 	else { $link="activate"; $class="#ff0000"; $text="Switch ON"; }
	?>		 	
<tr><td style="color: <?=$class?>; background: none;"><?=$allplugins[$i]?></td><td><a href="<?=$_SERVER['PHP_SELF']?>?<?=$link?>=<?=$allplugins[$i]?>"><?=$text?></a></td><td><?=GetAboutText($allplugins[$i])?></td></tr>
	<?			
	}
	echo "</table>";

}

if (!isset($activate)) $activate = "";
if (!isset($deactivate)) $deactivate = "";

LoadAllPlugins();
LoadActivePlugins();

if(($activate!="")||($deactivate!=""))
{
	if($activate!="") ActivePlugin($activate);
	else if($deactivate!="") DeactivePlugin($deactivate);

	//save
	//WritePluginTxtFile();	
	WritePluginFile();	
	
	// reload	
	unset($activeplugins);
	LoadActivePlugins();
?>	
<span style="color: #ff0000">Reload page to apply settings!</span><br/><br/>
<?	
}

ShowPlugins();

include(dirname(__FILE__)."/./include/footer.php");

?>
