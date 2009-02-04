<?php
//Development Mode:  Uncomment to change the config.php check to devel.config.php and output to devel.config.php instead.  Also displays the config file output in a div at the bottom of the page.
#$develmode = true;
if ($develmode)
	$outputfile = '../include/devel.config.php';
else
	$outputfile = '../include/config.php';
	
if (file_exists("../include/config.default.php")) {include "../include/config.default.php";}


?>
<html>
<head>
<title>ResourceSpace: Initial Configuration</title>
<link href="../css/global.css" rel="stylesheet" type="text/css" media="screen,projection,print" /> 
<link href="../css/Col-greyblu.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" /> 
<script type="text/javascript" src="../lib/js/jquery-1.3.1.min.js"></script> 

<script type="text/javascript"> 
 
$(document).ready(function(){


// $('#showall').click(function(){
	// $('.advsection').slideDown("slow");
	// return false;
// });

$('p.iteminfo').hide();
// $('.advsection').hide();
$('.starthidden').hide();
$('.advlink').click(function(){
	// var currentAdvSection = $(this).attr('href');
	// $(currentAdvSection).slideToggle("slow");
	return false;
});
$('#tabs div.tabs').hide();
$('#tabs div:first').show();
$('#tabs ul li:first').addClass('active');
$('#tabs ul li a').click(function(){
	$('#tabs ul li').removeClass('active'); 
	$(this).parent().addClass('active'); 
	var currentTab = $(this).attr('href');
	$("#tabs div.tabs:visible").slideUp("slow",function(){
		$(currentTab).slideDown("slow"); 
	});
	return false;		
});
$('#configstoragelocations').each(function(){
	if (this.checked != true){
		$('#storageurl').attr("disabled",true);
		$('#storagedir').attr("disabled",true);
	}
	else {
		$('#remstorageoptions').show();
	}
});
$('#configstoragelocations').click(function(){
	if (this.checked == true) {
		$('#storageurl').removeAttr("disabled");
		$('#storagedir').removeAttr('disabled');
		$('#remstorageoptions').slideDown("slow");
	}
	else{
		$('#storageurl').attr("disabled",true);
		$('#storagedir').attr("disabled",true);
		$('#remstorageoptions').slideUp("slow");
	}
});
$('#ldapenable').click(function(){
	if (this.checked == true) {
		$('#ldapoptions').slideDown("slow");
	}
	else{
		$('#ldapoptions').slideUp("slow");
	}
});
$('p.iteminfo').click(function(){
	$('p.iteminfo').hide("slow");
	});
$('.mysqlconn').keyup(function(){
	$('#al-testconn').fadeIn("fast",function(){
		$.ajax({
			url: "dbtest.php",
			async: true,
			dataType: "text",
			data: { mysqlserver: $('#mysqlserver').val(), mysqlusername: $('#mysqlusername').val(), mysqlpassword: $('#mysqlpassword').val(),mysqldb: $('#mysqldb').val() },
			success: function(data,type){
				if (data==200) {
					$('#mysqlserver').removeClass('warn');
					$('#mysqlusername').removeClass('warn');
					$('#mysqlpassword').removeClass('warn');
					$('#mysqldb').addClass('ok');
					$('#mysqlserver').addClass('ok');
					$('#mysqlusername').addClass('ok');
					$('#mysqlpassword').addClass('ok');
					$('#mysqldb').removeClass('warn');
				}
				else if (data==201) {
					$('#mysqlserver').removeClass('warn');
					$('#mysqlusername').removeClass('ok');
					$('#mysqlpassword').removeClass('ok');
					$('#mysqldb').removeClass('ok');
					$('#mysqldb').removeClass('warn');
					$('#mysqlserver').addClass('ok');
					$('#mysqlusername').addClass('warn');
					$('#mysqlpassword').addClass('warn');
				}
				else if (data==203) {
					$('#mysqlserver').removeClass('warn');
					$('#mysqlusername').removeClass('warn');
					$('#mysqlpassword').removeClass('warn');
					$('#mysqldb').removeClass('ok');
					$('#mysqldb').addClass('warn');
					$('#mysqlserver').addClass('ok');
					$('#mysqlusername').addClass('ok');
					$('#mysqlpassword').addClass('ok');
				}
				else{
					$('#mysqlserver').removeClass('ok');
					$('#mysqlusername').removeClass('ok');
					$('#mysqlpassword').removeClass('ok');
					$('#mysqldb').removeClass('ok');
					$('#mysqldb').removeClass('warn');
					$('#mysqlserver').addClass('warn');
					$('#mysqlusername').removeClass('warn');
					$('#mysqlpassword').removeClass('warn');
				}
				$('#al-testconn').hide();
			},
			error: function(){
				alert("Test failed (unable to verify MySQL)");
				$('#mysqlserver').addClass('warn');
				$('#mysqlusername').addClass('warn');
				$('#mysqlpassword').addClass('warn');
				$('#al-testconn').hide();
		}});
	});
});
$('a.iflink	').click(function(){
	$('p.iteminfo').hide("slow");
	var currentItemInfo = $(this).attr('href');
	$(currentItemInfo).show("fast");
	return false;
});
$('#mysqlserver').keyup();

});
</script> 
 
<style type="text/css"> 
 
<!--
#wrapper{ margin:0 auto;width:600px; }
 #intro {  margin-bottom: 40px; font-size:100%; background: #333333; text-align: left; padding: 40px; }
#intro a{ color: #fff; }
#introbottom { padding: 10px; clear: both; text-align:center;}
#preconfig {  float:right; background: #555555; padding: 25px;}
#preconfig h2 { border-bottom: 1px solid #ccc;	width: 100%;}
#preconfig p { font-size:110%; padding:0; margin:0; margin-top: 5px;}
#preconfig p.failure{ color: #f00; font-weight: bold; }

#tabs { font-size: 100%;}
#tabs > ul { float: right; width: 600px; margin:0; padding:0; border-bottom:5px solid #333333; }
#tabs > ul >li { margin: 0; padding:0; margin-left: 8px; list-style: none; background: #777777; }
* html #tabs li { display: inline; /* ie6 double float margin bug */ }
#tabs > ul > li, #tabs  > ul > li a { float: left; }
#tabs > ul > li a { text-decoration: none; padding: 8px; color: #CCCCCC; font-weight: bold; }
#tabs > ul > li.active { background: #CEE1EF; }
#tabs > ul > li.active a { color: #333333; }
#tabs div.tabs { background: #333; clear: both; padding: 20px; text-align: left; }
#tabs div h1 { text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px; }

p.iteminfo{ background: #e3fefa; width: 60%; color: #000; padding: 4px; margin: 10px; clear:both; }
strong { padding:0 5px; color: #F00; font-weight: bold; }
a.iflink { color: #F00; padding: 2px; border: 1px solid #444; margin-left: 4px; } 

input { margin-left: 10px; border: 1px solid #000; }

input.warn { border: 2px solid #f00; }
input.ok{ border:2px solid #0f0; }
input#submit { margin: 30px; font-size:120%; }

div.configitem { padding-top:10px; padding-left:40px; padding-bottom: 5px; border-bottom: 1px solid #555555; }

label { padding-right: 10px; width: 30%; font-weight: bold; }
div.advsection{ margin-bottom: 20px; }
.ajloadicon { padding-left:4px; }
h2#dbaseconfig{  min-height: 32px;}
a#showall { font-size: 70%; text-transform: none; padding-left: 20px; }
.erroritem{ background: #fcc; border: 2px solid #f00; color: #000; padding: 10px; margin: 7px; font-weight:bold;}
.erroritem.p { margin: 0; padding:0px;padding-bottom: 5px;}
#errorheader { font-size: 110%; margin-bottom: 20px; background: #fcc; border: 1px solid #f00; color: #000; padding: 10px; font-weight: bold; }
#configoutput { background: #777; color: #fff; text-align: left; padding: 20px; }
--> 
 
</style> 
</head>
<body>
<div id="Header">

<div id="HeaderNav1" class="HorizontalNav ">&nbsp;</div>
<div id="HeaderNav2" class="HorizontalNav HorizontalWhiteNav">&nbsp;</div>
</div>
<div id="wrapper">
<?php

	function ResolveKB($value)
		{
		$value=trim(strtoupper($value));
		if (substr($value,-1,1)=="K")
			{
			return substr($value,0,strlen($value)-1);
			}
		if (substr($value,-1,1)=="M")
			{
			return substr($value,0,strlen($value)-1) * 1024;
			}
		if (substr($value,-1,1)=="G")
			{
			return substr($value,0,strlen($value)-1) * 1024 * 1024;
			}
		return $value;
	}

	function generatePassword($length=12) {
	    $vowels = 'aeuyAEUY';
	    $consonants = 'bdghjmnpqrstvzBDGHJLMNPQRSTVWXZ23456789';
	    $password = '';
	    $alt = time() % 2;
	    for ($i = 0; $i < $length; $i++) {
	        if ($alt == 1) {
	            $password .= $consonants[(rand() % strlen($consonants))];
	            $alt = 0;
	        } else {
	            $password .= $vowels[(rand() % strlen($vowels))];
	            $alt = 1;
	        }
	    }
		return $password;
	}
	function get_post($key){ //Return santizied input for a given $_REQUEST key
		return filter_var($_REQUEST[$key], FILTER_SANITIZE_STRING);
	}
	function get_post_bool($key){ // Return true or false
		if (isset($_REQUEST[$key]))
			return true;
		else
			return false;
	}
	
	function sslash($data){
		$stripped = rtrim($data);
		$stripped = rtrim($data, '/');
		return $stripped;
	}	

	function url_exists($url)
	{
		$host = parse_url($url, PHP_URL_HOST);
		$path = parse_url($url, PHP_URL_PATH);
		$port = parse_url($url, PHP_URL_PORT);
		if (empty($path)) $path = "/";
		if ($port==0) $port=80;
		// Build HTTP 1.0 request header. Defined in RFC 1945 

		$headers = 	"GET $path HTTP/1.0\r\n" .
					"User-Agent: RS-Installation/1.0\r\n\r\n";

		$fp = @fsockopen($host, $port, $errno, $errmsg, 5); //5 second timeout.  Pretty quick, but we assume that if we can't open the socket connection rather quickly the host or port are probably wrong.
		if (!$fp) {
			return false;
		}
		fwrite($fp, $headers);

		while(!feof($fp)) {
			$resp = fgets($fp, 4096);
			if (strstr($resp, '200 OK')){
				fclose($fp);
				return true;
			}
		}
		fclose($fp);
		return false;
	}	
	
	if (!function_exists('filter_var')){  //If running on PHP without filter_var, define a do-fer function, otherwise use php's filter_var (PHP > 5.2.0)
		define(FILTER_SANITIZE_STRING, 1);
		define(FILTER_SANITIZE_EMAIL, 2);
		define(FILTER_VALIDATE_EMAIL, 3);
		define(FILTER_VALIDATE_URL, 4);
		function filter_var($data, $filter){
			switch ($filter){
			case FILTER_VALIDATE_EMAIL:
				if(preg_match('/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/',$data, $output)>0)
					return true;
				else return false;
				break;
			case FILTER_SANITIZE_STRING:
				//Just do an escape quotes.  We're not doing anything too dangerous here after all
				return addslashes($data);
				break;
			case FILTER_VALIDATE_URL:		
			//Rely on checking the license.txt file to validate URL.  This leaves a minor risk of the script being used to do bad things to other hosts if it is left available (i.e. RS is installed, but never configured)
				return true;
				break;
			}
		}
	}
			
	if (file_exists($outputfile)){
?>
	<div id="errorheader">Your ResourceSpace installation is already configured.  To reconfigure, you may delete <pre>include/config.php</pre> and point your browser to this page again.</div> 
	</body>
	</html>
	<?php
	die(0);
	}
	if (!(isset($_REQUEST['submit']))){ //No Form Submission, lets setup some defaults
		if (!isset($storagedir)) {$storagedir=dirname(__FILE__)."/../filestore";}
		$baseurl = 'http://'.php_uname('n'); //Set the baseurl to the machine hostname. 
		
		//Generate default random keys
		$scramble_key = generatePassword();
		$spider_password = generatePassword();
		//Setup search paths (Currently only Linux)
		if(php_uname('s')=='Linux'){
			$search_paths[]='/usr/bin';
			$search_paths[]='/usr/share/bin';
			$search_paths[]='/usr/local/bin';
		}
		elseif(stristr(php_uname('s'),'windows')){
			$config_windows = true;
		}
		if (isset($search_paths)){
			foreach($search_paths as $path){
				if (file_exists($path.'/convert'))
					$imagemagick_path = $path;
				if (file_exists($path.'/gs'))
					$ghostscript_path = $path;
				if (file_exists($path.'/ffmpeg'))
					$ffmpeg_path = $path;
				if (file_exists($path.'/exiftool'))
					$exiftool_path = $path;
				if (file_exists($path.'/antiword'))
					$antiword_path= $path;
				if (file_exists($path.'/pdftotext'))
					$pdftotext_path = $path;
			}
		}

	}
	else { //Form was submitted, lets do it!
		//Config File Header
		$config_windows = get_post_bool('config_windows');
		$exe_ext = $config_windows==true?'.exe':'';
		$config_output .= "###############################\r\n";
		$config_output .= "## ResourceSpace\r\n";
		$config_output .= "## Local Configuration Script\r\n";
		$config_output .= "###############################\r\n\r\n";
		$config_output .= "# All custom settings should be entered in this file.\r\n";  
		$config_output .= "# Options may be copied from config.default.php and configured here.\r\n\r\n";
			
		//Check mySQL settings
		$mysql_server = get_post('mysql_server');
		$mysql_username = get_post('mysql_username');
		$mysql_password = get_post('mysql_password');
		$mysql_db = get_post('mysql_db');
		//Make a connection to the database using the supplied credentials and see if we can create and drop a table
		if (@mysql_connect($mysql_server, $mysql_username, $mysql_password)){
			$mysqlversion=mysql_get_server_info();
		
			if ($mysqlversion<'5') {
				$errors['databaseversion'] = true;
			}
			else {
				if (@mysql_select_db($mysql_db)){
					if (@mysql_query("CREATE table configtest(test varchar(30))")){
						@mysql_query("DROP table configtest");
					}
					else {$errors['databaseperms'] = true;}
				}
				else {$errors['databasedb'] = true;}
			}
		}
		else {
			switch (mysql_errno()){
				case 1045:
					$errors['databaselogin'] = true;
					break;
				default:
					$errors['databaseserver'] = true;
					break;
			}
		}
		if (isset($errors)){
			$errors['database'] = mysql_error();
			
		}
		else {
			
			//Test passed: Output mySQL config section
			$config_output .= "# MySQL database settings\r\n";
			$config_output .= "\$mysql_server = '$mysql_server';\r\n";
			$config_output .= "\$mysql_username = '$mysql_username';\r\n";
			$config_output .= "\$mysql_password = '$mysql_password';\r\n";
			$config_output .= "\$mysql_db = '$mysql_db';\r\n";
			$config_output .= "\r\n";
		}
		
		//Check mySQL bin path (not required)
		$mysql_bin_path = sslash(get_post('mysql_bin_path'));
		if ((isset($mysql_bin_path)) && ($mysql_bin_path!='')){
			if (!file_exists($mysql_bin_path.'/mysqldump'.$exe_ext))
				$errors['mysqlbinpath'] = true;
			else $config_output .="\$mysql_bin_path = '$mysql_bin_path';\r\n\r\n";
		}

		//Check base url (required)
		$baseurl = sslash(get_post('baseurl'));
		if ((isset($baseurl)) && ($baseurl!='') && ($baseurl!='http://my.site/resourcespace') && (filter_var($baseurl, FILTER_VALIDATE_URL))){
			//Check that the base url is correct by attempting to fetch the license file:
			if (url_exists($baseurl.'/license.txt')){
				$config_output .= "# Base URL of the installation\r\n";
				$config_output .= "\$baseurl = '$baseurl';\r\n\r\n";
			}
			else {
				$errors['baseurlverify']= true;
			}
		}
		else {
			$errors['baseurl'] = true;
		}
		
		//Verify email addresses (currently just verify that they validate as email addresses)
		$config_output .= "# Email settings\r\n";
		$email_from = get_post('email_from');
		if ($email_from != ''){
			if (filter_var($email_from, FILTER_VALIDATE_EMAIL) && ($email_from!='resourcespace@my.site'))
				$config_output .= "\$email_from = '$email_from';\r\n";
			else
				$errors['email_from']=true;
		}
		$email_notify = get_post('email_notify');
		if ($email_notify !=''){
			if (filter_var($email_notify, FILTER_VALIDATE_EMAIL) && ($email_notify!='resourcespace@my.site'))
				$config_output .= "\$email_notify = '$email_notify';\r\n\r\n";
			else
				$errors['email_notify']=true;
		}
		
		//Check the spider_password and scramble_key (required)
		$spider_password = get_post('spider_password');
		if ($spider_password!='')
			$config_output .= "\$spider_password = '$spider_password';\r\n";
		else
			$errors['spider_password']=true;
		$scramble_key = get_post('scramble_key');
		if ($scramble_key!='')
			$config_output .= "\$scramble_key = '$scramble_key';\r\n\r\n";
		else
			$errors['scramble_key']=true;
			
		$config_output .= "# Paths\r\n";
		//Verify paths actually point to a useable binary
		$imagemagick_path = sslash(get_post('imagemagick_path'));
		$ghostscript_path = sslash(get_post('ghostscript_path'));
		$ffmpeg_path = sslash(get_post('ffmpeg_path'));
		$exiftool_path = sslash(get_post('exiftool_path'));
		$antiword_path = sslash(get_post('antiword_path'));
		$pdftotext_path = sslash(get_post('pdftotext_path'));
		if ($imagemagick_path!='')
			if (!file_exists($imagemagick_path.'/convert'.$exe_ext))
				$errors['imagemagick_path'] = true;
			else $config_output .= "\$imagemagick_path = '$imagemagick_path';\r\n";
		if ($ghostscript_path!='')
			if (!file_exists($ghostscript_path.'/gs'.$exe_ext))
				$errors['ghostscript_path'] = true;
			else $config_output .= "\$ghostscript_path = '$ghostscript_path';\r\n";
		if ($ffmpeg_path!='')
			if (!file_exists($ffmpeg_path.'/ffmpeg'.$exe_ext))
				$errors['ffmpeg_path'] = true;
			else $config_output .= "\$ffmpeg_path = '$ffmpeg_path';\r\n";
		if ($exiftool_path!='')
			if (!file_exists($exiftool_path.'/exiftool'.$exe_ext))
				$errors['exiftool_path'] = true;
			else $config_output .= "\$exiftool_path = '$exiftool_path';\r\n";
		if ($antiword_path!='')
			if (!file_exists($exiftool_path.'/antiword'.$exe_ext))
				$errors['antiword_path'] = true;
			else $config_output .= "\$antiword_path = '$antiword_path';\r\n";
		if ($pdftotext_path!='')
			if (!file_exists($pdftotext_path.'/pdftotext'.$exe_ext))
				$errors['pdftotext_path'] = true;
			else $config_output .= "\$pdftotext_path = '$pdftotext_path';\r\n\r\n";
		
		//Deal with some checkboxes
		if ($secure = get_post_bool('secure'))
			$config_output .= "\$secure = true;\r\n";
		if (!$allow_account_request = get_post_bool('allow_account_request'))
			$config_output .= "\$allow_account_request = false;\r\n";
		if (!$allow_password_change = get_post_bool('allow_password_change'))
			$config_output .= "\$allow_password_change = false;\r\n";
		if (!$research_request = get_post_bool('research_request'))
			$config_output .= "\$research_request = false;\r\n";
		if ($use_theme_as_home = get_post_bool('use_theme_as_home'))
			$config_output .= "\$use_theme_as_home = true;\r\n";
		if ($disable_languages = get_post_bool('disable_languages'))
			$config_output .= "\$disable_languages = true;\r\n";
		if ($config_windows)
			$config_output .= "\$config_windows = true;\r\n";
		if (($defaultlanguage = get_post('defaultlanguage'))!='en')
			$config_output .= "\$defaultlanguage = '$defaultlanguage';\r\n";
		
		
			
		
		//Advanced Settings
		if ($_REQUEST['applicationname']!=$applicationname){
			$applicationname = get_post('applicationname');
			$config_output .= "\$applicationname = '$applicationname';\r\n";
		}
		if (get_post_bool('configstoragelocations')){
			$configstoragelocations = true;
			$storagedir = get_post('storagedir');
			$storageurl = get_post('storageurl');
			$config_output .= "\$storagedir = '$storagedir';\r\n";
			$config_output .= "\$storageurl = '$storageurl';\r\n";
		}
		else {
			$storagedir = dirname(__FILE__)."/../filestore";
		}
		$ftp_server = get_post('ftp_server');
		$ftp_username = get_post('ftp_username');
		$ftp_password = get_post('ftp_password');
		$ftp_defaultfolder = get_post('ftp_defaultfolder');
		$config_output .= "\$ftp_server = '$ftp_server';\r\n";
		$config_output .= "\$ftp_username = '$ftp_username';\r\n";
		$config_output .= "\$ftp_password = '$ftp_password';\r\n";
		$config_output .= "\$ftp_defaultfolder = '$ftp_defaultfolder';\r\n";
	}
		
	
	
?>
<?php //Output Section

if ((isset($_REQUEST['submit'])) && (!isset($errors))){
	//Form submission was a success.  Output the config file and refrain from redisplaying the form.
	$fhandle = fopen($outputfile, 'w') or die ("Error opening output file.  (This should never happen, we should have caught this before we got here)");
	fwrite($fhandle, "<?php\r\n".$config_output);
	fclose($fhandle);
	
	?>
	<div id="intro">
		<h1>Congratulations!</h1>
		<p>Your initial ResourceSpace setup is complete.  Be sure to check out 'include/default.config.php' for more configuration options.</p>
		<p>Next steps:</p>
		<ul>
			<li>You can now remove write access to 'include/'.</li>
			<li>Visit the <a href="http://rswiki.montala.net/index.php/Main_Page">ResourceSpace Documentation Wiki</a> for more information about customizing your installation</li>
			<li><a href="<?php echo $baseurl;?>/login.php">Login to <?php echo $applicationname;?></a>
				<ul>
					<li>Username: admin</li>
					<li>Password: admin</li>
				</ul>
			</li>
		</ul>
	</div>
	<?php
}
else{
?>
<form action="setup.php" method="POST">
<?php echo $config_windows==true?'<input type="hidden" name="config_windows" value="true"/>':'' ?>
	<div id="intro">
			<div id="preconfig">
				<h2>Preconfiguration Check</h2>
				<?php 
				$continue=true;
				$phpversion=phpversion();
				if ($phpversion<'4.4') {$result="FAIL: should be 4.4 or greater";$continue=false;} else {$result="OK";}?>
				<p class="<?php echo ($result=='OK'?'':'failure');?>">PHP version: <?php echo $phpversion;?> <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
				<?php
					$gdinfo=gd_info();
					if (is_array($gdinfo))
						{
						$version=$gdinfo["GD Version"];
						$result="OK";
						}
					else
						{
						$version="Not installed.";
						$result="FAIL";
						$continue=false;
						}
				?>
				<p class="<?php echo ($result=='OK'?'':'failure');?>">GD version: <?php echo $version?>  <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
				<?php
					$memory_limit=ini_get("memory_limit");
					if (ResolveKB($memory_limit)<(200*1024)) {$result="WARNING: should be 200M or greater";} else {$result="OK";}
				?>
				<p class="<?php echo ($result=='OK'?'':'failure');?>">PHP.INI value for 'memory_limit': <?php echo $memory_limit?> <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
				<?php
					$post_max_size=ini_get("post_max_size");
					if (ResolveKB($post_max_size)<(100*1024)) {$result="WARNING: should be 100M or greater";} else {$result="OK";}
				?>
				<p class="<?php echo ($result=='OK'?'':'failure');?>">PHP.INI value for 'post_max_size': <?php echo $post_max_size?> <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
				<?php
					$upload_max_filesize=ini_get("upload_max_filesize");
					if (ResolveKB($upload_max_filesize)<(100*1024)) {$result="WARNING: should be 100M or greater";} else {$result="OK";}
				?>
				<p class="<?php echo ($result=='OK'?'':'failure');?>">PHP.INI value for 'upload_max_filesize': <?php echo $upload_max_filesize?> <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
				<?php
					$success=is_writable('../include');
					if ($success===false) {
						$result="FAIL: '/include' not writable. (Only required during setup)";
						$continue=false;
					}	
					else {
						$result="OK";
					}
				?>
					<p class="<?php echo ($result=='OK'?'':'failure');?>">Write access to config directory: <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
				<?php
					$success=is_writable($storagedir);
					if ($success===false) {$result="WARN: '$storagedir' not writable. <br/> (Override location in 'Advanced Settings'.)";} else {$result="OK";}
				?>
					<p class="<?php echo ($result=='OK'?'':'failure');?>">Write access to storage directory: <?php echo($result!='OK'?'<br>':'');?>(<?php echo $result?>)</p>
			</div>
			<h1>Welcome to ResourceSpace. </h1>
			<p>Thanks for choosing ResourceSpace.  This configuration script will help you setup ResourceSpace.  This process only needs to be completed once.<p>
			<p>For more information about ResourceSpace visit the <a href="http://rswiki.montala.net/index.php/Main_Page">ResourceSpace Documentation Wiki</a>.
			<div id="introbottom">
			<?php if ($continue===false) { ?>
			<strong>Pre-configuration errors were detected.<br />  Please resolve these errors and return to this page to continue.</strong>
			<?php } else { ?>
			<script type="text/javascript"> 
			$(document).ready(function(){
				$('#tabs').show();
			});
			</script>
			<?php } ?>
			</div>
	</div>
	<?php if (isset($errors)){ ?>	
		<div id="errorheader">There were errors detected in your configuration.  See below for detailed error messages.</div>
	<?php } ?>	
	<div id="tabs" class="starthidden">
		<ul>
			<li><a href="#tab-1">Basic Settings</a></li>
			<li><a href="#tab-2">Advanced Settings</a></li>
 <?php 			//<li><a href="#tab-3">Development Settings</a></li> ?>
		</ul>
		<div class="tabs" id="tab-1">
			<h1>Basic Settings</h1>
			<p>These settings provide the basic setup for your ResourceSpace installation.  Required items are marked with a <strong>*</strong></p>
			<p class="configsection">
				<h2 id="dbaseconfig">Database Configuration<img class="starthidden ajloadicon" id="al-testconn" src="../gfx/ajax-loader.gif"/></h2>
				<?php if(isset($errors['database'])){?>
					<div class="erroritem">There was an error with your MySQL settings: 
						<?php if(isset($errors['databaseversion'])){ ?>
						MySQL version should be 5 or greater. <?php } ?>
						<?php if(isset($errors['databaseserver'])){?>
						Unable to reach server. <?php }?>
						<?php if(isset($errors['databaselogin'])){?>
						Login failed<?php }?>
						<?php if(isset($errors['databasedb'])){?>
						Unable to access database.<?php } ?>
						<?php if(isset($errors['databaseperms'])){?>
						Check user permissions.  Unable to create tables.<?php }?>
						
						<p><?php echo $errors['database'];?></p>
					</div>
				<?php } ?>
						
				<div class="configitem">
					<label for="mysqlserver">MySQL Server:</label><input class="mysqlconn" type="text" id="mysqlserver" name="mysql_server" value="<?php echo $mysql_server;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-server">?</a>
					<p class="iteminfo" id="if-mysql-server">The IP address or <abbr title="Fully Qualified Domain Name">FQDN</abbr> of your MySQL server installation.  If MySql is installed on the same server as your web server, use 'localhost'.</p>
				</div>
				<div class="configitem">
					<label for="mysqlusername">MySQL Username:</label><input class="mysqlconn" type="text" id="mysqlusername" name="mysql_username" value="<?php echo $mysql_username;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-username">?</a>
					<p class="iteminfo" id="if-mysql-username">The username used to connect to your MySQL server.  This user must have rights to create tables in the database named below.</p>		
				</div>
				<div class="configitem">
					<label for="mysqlpassword">MySQL Password:</label><input class="mysqlconn" type="password" id="mysqlpassword" name="mysql_password" value="<?php echo $mysql_password;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-password">?</a>
					<p class="iteminfo" id="if-mysql-password">The password for the MySQL username entered above</p>
				</div>
				<div class="configitem">
					<label for="mysqldb">MySQL Database:</label><input id="mysqldb" class="mysqlconn" type="text" name="mysql_db" value="<?php echo $mysql_db;?>"/><strong>*</strong><a class="iflink" href="#if-mysql-db">?</a>
					<p class="iteminfo" id="if-mysql-db">The Name of the MySQL database RS will use. (This database must exist.)</p>
				</div>
				
				<div class="configitem">
					<?php if(isset($errors['mysqlbinpath'])){?>
						<div class="erroritem">Unable to verify path.  Leave blank to disable</div>
					<?php } ?>
					<label for="mysqlbinpath">MySQL Binary Path:</label><input id="mysqlbinpath" type="text" name="mysql_bin_path" value="<?php echo $mysql_bin_path;?>"/><a class="iflink" href="#if-mysql-bin-path">?</a>
					<p class="iteminfo" id="if-mysql-bin-path">The path to the MySQL client binaries - e.g. mysqldump. NOTE: This is only needed if you plan to use the export tool.</p>
				</div>
			</p>
			<p class="configsection">
				<h2>General Settings</h2>
				<div class="configitem">
					<?php if(isset($errors['baseurl'])){?>
						<div class="erroritem">Base URL is a required field.</div>
					<?php } ?>
					<?php if(isset($errors['baseurlverify'])){?>
						<div class="erroritem">Base URL does not seem to be correct (could not load license.txt).</div>
					<?php } ?>
					<label for="baseurl">Base URL:</label><input id="baseurl" type="text" name="baseurl" value="<?php echo $baseurl;?>"/><strong>*</strong><a class="iflink" href="#if-baseurl">?</a>
					<p class="iteminfo" id="if-baseurl">The 'base' web address for this installation.  NOTE: No trailing slash.</p>
				</div>
				<div class="configitem">
					<?php if(isset($errors['email_from'])){?>
						<div class="erroritem">Not a valid email address.</div>
					<?php } ?>
					<label for="emailfrom">Email From Address:</label><input id="emailfrom" type="text" name="email_from" value="<?php echo $email_from;?>"/><a class="iflink" href="#if-emailfrom">?</a>
					<p id="if-emailfrom" class="iteminfo">The address that emails from RS appear to come from</p>
				</div>
				<div class="configitem">
					<?php if(isset($errors['email_notify'])){?>
						<div class="erroritem">Not a valid email address.</div>
					<?php } ?>
					<label for="emailnotify">Email Notify:</label><input id="emailnotify" type="text" name="email_notify" value="<?php echo $email_notify;?>"/><a class="iflink" href="#if-emailnotify">?</a>
					<p id="if-emailnotify" class="iteminfo">The email address to which resource/user/research requests are sent.</p>
				</div>
				<div class="configitem">
				<?php if(isset($errors['spider_password'])){?>
						<div class="erroritem">The spider password is a required field.</div>
					<?php } ?>
					<label for="spiderpassword">Spider Password:</label><input id="spiderpassword" type="text" name="spider_password" value="<?php echo $spider_password;?>"/><strong>*</strong><a class="iflink" href="#if-spiderpassword">?</a>
					<p id="if-spiderpassword" class="iteminfo">The password required for spider.php.  IMPORTANT: Randomise this for each new installation. Your resources will be readable by anyone that knows this password.  This field has already been randomised for you, but you can change it to match an existing installation, if necessary.</p>
				</div>
				<div class="configitem">
					<?php if(isset($errors['scramble_key'])){?>
						<div class="erroritem">The scramble key is a required field.</div>
					<?php } ?>
					<label for="scramblekey">Scramble Key:</label><input id="scramblekey" type="text" name="scramble_key" value="<?php echo $scramble_key;?>"/><strong>*</strong><a class="iflink" href="#if-scramblekey">?</a>
					<p id="if-scramblekey" class="iteminfo">To enable scrambling, set the scramble key to be a hard-to-guess string (similar to a password).  If this is a public installation then this is a very wise idea.  Leave this field blank to disable resource path scrambling. This field has already been randomised for you, but you can change it to match an existing installation, if necessary.</p>
				</div>
				<div class="configitem">
					<label for="secure">Secure (https) mode:</label><input id="secure" type="checkbox" name="secure" value="true" <?php echo ($secure==true?'checked="checked"':'');?>/><a class="iflink" href="#if-secure">?</a>
					<p id="if-secure" class="iteminfo">If checked, RS will use https.</p>
				</div>
			</p>
			<p class="configsection">
				<h2>Paths</h2>
				<p>For each path, enter the path without a trailing slash to each binary.  To disable a binary, leave the path blank.  Any auto-detected paths have already been filled in.</p>
				<div class="configitem">
					<?php if(isset($errors['imagemagick_path'])){?>
						<div class="erroritem">Unable to verify location of 'convert'.</div>
					<?php } ?>
					<label for="imagemagickpath">Imagemagick Path:</label><input id="imagemagickpath" type="text" name="imagemagick_path" value="<?php echo $imagemagick_path ?>"/>
				</div>
				<div class="configitem">
					<?php if(isset($errors['ghostscript_path'])){?>
						<div class="erroritem">Unable to verify location of 'gs'.</div>
					<?php } ?>
					<label for="ghostscriptpath">Ghostscript Path:</label><input id="ghostscriptpath" type="text" name="ghostscript_path" value="<?php echo $ghostscript_path; ?>"/>
				</div>
				<div class="configitem">
					<?php if(isset($errors['ffmpeg_path'])){?>
						<div class="erroritem">Unable to verify location of 'ffmpeg'.</div>
					<?php } ?>
					<label for="ffmpegpath">FFMpeg Path:</label><input id="ffmpegpath" type="text" name="ffmpeg_path" value="<?php echo $ffmpeg_path; ?>"/>
				</div>
				<div class="configitem">
					<?php if(isset($errors['exiftool_path'])){?>
						<div class="erroritem">Unable to verify location of 'exiftool'.</div>
					<?php } ?>
					<label for="exiftoolpath">Exiftool Path:</label><input id="exiftoolpath" type="text" name="exiftool_path" value="<?php echo $exiftool_path; ?>"/>
				</div>
				<div class="configitem">
				<?php if(isset($errors['antiword_path'])){?>
						<div class="erroritem">Unable to verify location of 'AntiWord'.</div>
					<?php } ?>
					<label for="antiwordpath">AntiWord Path:</label><input id="antiwordpath" type="text" name="antiword_path" value="<?php echo $antiword_path; ?>"/>
				</div>
				
				<div class="configitem">
					<?php if(isset($errors['pdftotext_path'])){?>
						<div class="erroritem">Unable to verify location of 'pdftotext'.</div>
					<?php } ?>
					<label for="pdftotextpath">PDFtotext Path:</label><input id="pdftotextpath" type="text" name="pdftotext_path" value="<?php echo $pdftotext_path; ?>"/>
				</div>
			</p>
			<p>NOTE: The only <strong>required</strong> settings are on this page.  If you're not interested in checking out the advanced options, you may click below to begin the installation process</p>
		</div>
		<div class="tabs" id="tab-2">
			<h1>Advanced Settings</h2>
			<h2><a class="advlink" href="#generaloptions">&gt; General Options</a></h2>
			<div class="advsection" id="generaloptions">
				<div class="configitem">
					<label for="applicationname">Application Name: </label><input id="applicationname" type="text" name="applicationname" value="<?php echo $applicationname;?>"/><a class="iflink" href="#if-applicationname">?</a>
					<p class="iteminfo" id="if-applicationname">The name of your implementation / installation (e.g. 'MyCompany Resource System')</p>
				</div>
				<div class="configitem">
					<label for="allow_password_change">Allow password change? </label><input id="allow_password_change" type="checkbox" name="allow_password_change" <?php echo ($allow_password_change==true?'checked':'');?>/><a class="iflink" href="#if-allow_password_change">?</a>
					<p class="iteminfo" id="if-allow_password_change">Allow end users to change their passwods</p>
				</div>
				<div class="configitem">
					<label for="allow_account_request">Allow users to request accounts? </label><input id="allow_account_request" type="checkbox" name="allow_account_request" <?php echo ($allow_account_request==true?'checked':'');?>/>
				</div>
				<div class="configitem">
					<label for="research_request">Display the Research Request functionality? </label><input id="research_request" type="checkbox" name="research_request" <?php echo ($research_request==true?'checked':'');?>/><a class="iflink" href="#if-research_request">?</a>
					<p class="iteminfo" id="if-research_request">Allows users to request resources via a form, which is e-mailed.</p>
				</div>
				<div class="configitem">
					<label for="use_theme_as_home"> Use the themes page as the home page? </label><input id="use_theme_as_home" type="checkbox" name="use_theme_as_home" <?php echo ($use_theme_as_home==true?'checked':'');?>/>
				</div>
				
			</div>	
			<h2><a class="advlink" href="#storagelocations">> Remote Storage Locations</a></h2>
			<div class="advsection" id="storagelocations">
				<div class="configitem">
					<label for="configstoragelocations">Use remote storage?</label><input id="configstoragelocations" type="checkbox" name="configstoragelocations" value="true" <?php echo ($configstoragelocations==true?'checked':'');?>/><a class="iflink" href="#if-remstorage">?</a>
					<p class="iteminfo" id="if-remstorage">Check this box to configure remote storage locations for RS. (To use another server for filestore)</p>
				</div>
				<div id="remstorageoptions" class="starthidden">
					<div class="configitem">
						<label for="storagedir">Storage Directory:</label><input id="storagedir" type="text" name="storagedir" value="<?php echo $storagedir;?>"/><a class="iflink" href="#if-storagedir">?</a>
						<p class="iteminfo" id="if-storagedir">Where to put the media files. Can be absolute (/var/www/blah/blah) or relative to the installation. Note: no trailing slash</p>
					</div>
					<div class="configitem">
						<label for="storageurl">Storage URL:</label><input id="storageurl" type="text" name="storageurl" value="<?php echo $storageurl;?>"/><a class="iflink" href="#if-storageurl">?</a>
						<p class="iteminfo" id="if-storageurl"> Where the storagedir is available. Can be absolute (http://files.example.com) or relative to the installation. Note: no trailing slash</p>
					</div>
				</div>
			</div>
			<h2><a class="advlink" href="#languages">&gt; Language Support</a></h2>
			<div class="advsection" id="languages">
				<div class="configitem">
					<label for="defaultlanguage">Default Language:</label><select id="defaultlanguage" name="defaultlanguage">
						<?php
							foreach($languages as $code => $text){
								echo "<option value=\"$code\"";
								if ($code == $defaultlanguage)
									echo ' selected';
								echo ">$text</option>";
							}
						?>
					</select>
				</div>
				<div class="configitem">
					<label for="disable_languages">Disable language selection?</label><input id="disable_languages" name="disable_languages" type="checkbox"/>
				</div>
			</div>
			<h2><a class="advlink" href="#ftpsettings">&gt; FTP Settings</a></h2>
			<div class="advsection" id="ftpsettings">
				<div class="configitem">
					<label for="ftp_server">FTP Server</label><input id="ftp_server" name="ftp_server" type="text" value="<?php echo $ftp_server;?>"/><a class="iflink" href="#if-ftpserver">?</a>
					<p class="iteminfo" id="if-ftpserver">Only necessary if you plan to use the FTP upload feature.</p>
				</div>
				<div class="configitem">
					<label for="ftp_username">FTP Username</label><input id="ftp_username" name="ftp_username" type="text" value="<?php echo $ftp_username;?>"/>
				</div>
								<div class="configitem">
					<label for="ftp_password">FTP Password</label><input id="ftp_password" name="ftp_password" type="text" value="<?php echo $ftp_password;?>"/>
				</div>
				<div class="configitem">
					<label for="ftp_defaultfolder">FTP Default Folder</label><input id="ftp_defaultfolder" name="ftp_defaultfolder" type="text" value="<?php echo $ftp_defaultfolder;?>"/>
				</div>
			</div>

		</div>
		<?php
		// <div class="tabs" id="tab-3">
			// <h1>Development Settings</h2>
			// <h2><a class="advlink" href="#ldapauth">&gt; Active Directory / LDAP Authentication</a></h2>
			// <div class="advsection" id="ldapauth">
				// <div class="configitem">
					// <label for="ldapenable">Enable AD/LDAP Authentication?</label><input name="ldapenable" type="checkbox" id="ldapenable"/><a class="iflink" href="#if-ldapauth">?</a>
					// <p class="iteminfo" id="if-ldapauth">Active Directory / LDAP Authentication allows for RS users to login with their AD/LDAP credentials.  This is an advanced setup process, and is currently still in development.  <strong>Use at your own risk. This may not be completely secure.</strong></p>
				// </div>
				// <div id="ldapoptions" class="starthidden">
					// <div class="configitem">
						// <label for="ldaptype">LDAP Type: </label>
						// <select id="ldaptype"name="ldaptype">
							// <option value="AD">Active Directory</option>
							// <option value="genldap">Generic LDAP</option>
							// <option value="openldap">OpenLDAP</option>
						// </select>
					// </div>
				// </div>
						
			// </div>
		// </div>
		?>
		<input type="submit" id="submit" name="submit" value="Begin Installation!"/>
	</div>

</form>
<?php } ?>
<?php if (($develmode)&& isset($config_output)){?>
		<div id="configoutput">
			<h1>Configuration File Ouput:</h1>
			<pre><?php echo $config_output; ?></pre>
		</div>
	<?php } ?>	
</div>
	</body>

</html>	


			
	