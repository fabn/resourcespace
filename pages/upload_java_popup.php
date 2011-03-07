<?php include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/image_processing.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";

$status="";
$resource_type=getvalescaped("resource_type","");
$collection_add=getvalescaped("collection_add","");

$collectiondata=get_collection($collection_add);
$collectionname=$collectiondata['name'];

$allowed_extensions="";
if ($resource_type!="") {$allowed_extensions=get_allowed_extensions_by_type($resource_type);}

$alternative=getval("alternative",""); # Upload alternative resources

# generate AllowedFileExtensions parameter
$allowed="";
if ($allowed_extensions!=""){ $extensions=explode(",",$allowed_extensions); 
foreach ($extensions as $allowed_extension){
	$allowed.=$allowed_extension."/";
	}	
} 


?>
<html>
<!--
ResourceSpace version <?php echo $productversion?>
Copyright Oxfam GB, Montala, WWF International 2006-2010
http://www.resourcespace.org/
-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<title><?php echo htmlspecialchars($applicationname)?></title>
<link rel="icon" type="image/png" href="<?php echo $baseurl?>/gfx/interface/favicon.png" />
<link href="<?php echo $baseurl?>/css/global.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" />
<?php if (!hook("adjustcolortheme")){ ?>
<link href="<?php echo $baseurl?>/css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss",$defaulttheme)?>.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<?php } ?>
<!--[if lte IE 7]> <link href="<?php echo $baseurl?>/css/globalIE.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<!--[if lte IE 5.6]> <link href="<?php echo $baseurl?>/css/globalIE5.css?css_reload_key=<?php echo $css_reload_key?>" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
</head><body>
<div id="height">
<div id="Header">
<div class="BasicsBox" id="uploadbox"> 

</p>
</div><h2>&nbsp;</h2>
<h1><?php echo $lang["fileupload"]?><?php if (isset($collectionname)){?> - <?php echo $lang['collection']?>: <?php echo $collectionname?><?php } ?></h1>
<p><?php echo text("introtext")?></p>

<?php if ($allowed_extensions!=""){
    $allowed_extensions=str_replace(", ",",",$allowed_extensions);
    $list=explode(",",trim($allowed_extensions));
    sort($list);
    $allowed_extensions=implode(",",$list);
    ?><p><?php echo $lang['allowedextensions'].": ". strtoupper(str_replace(",",", ",$allowed_extensions))?></p><?php } ?>

<!---------------------------------------------------------------------------------------------------------
-------------------     A SIMPLE AND STANDARD APPLET TAG, to call the JUpload applet  --------------------- 
----------------------------------------------------------------------------------------------------------->
        <applet
	            code="wjhk.jupload2.JUploadApplet"
	            name="JUpload"
	            archive="../lib/jupload/wjhk.jupload.jar"
	            width="640"
	            height="300"
	            mayscript
	            alt="The java pugin must be installed.">
            <!-- param name="CODE"    value="wjhk.jupload2.JUploadApplet" / -->
            <!-- param name="ARCHIVE" value="wjhk.jupload.jar" / -->
            <!-- param name="type"    value="application/x-java-applet;version=1.5" /  -->
            <param name="postURL" value="upload_java.php?collection_add=<?php echo $collection_add?>&user=<?php echo urlencode($_COOKIE["user"])?>&resource_type=<?php echo $resource_type?>&no_exif=<?php echo getval("no_exif","") ?>&autorotate=<?php echo getval('autorotate','') ?>" />
            <param name="allowedFileExtensions" value="<?php echo $allowed?>">
            <param name="nbFilesPerRequest" value="1">
            <param name="allowHttpPersistent" value="false">
            <param name="debugLevel" value="0">
            <param name="showLogWindow" value="false">
            <param name="lang" value="<?php echo $language?>">
            <param name="maxChunkSize" value="<?php echo $jupload_chunk_size ?>">
         <?php if (isset($jupload_look_and_feel)){ ?>
	    <param name="lookAndFeel" value="<?php echo $jupload_look_and_feel ?>">
	<?php } ?>
            
            <?php if (!$frameless_collections) { 
            # If not using frameless collections, refresh the bottom frame after upload.
            ?>
            <param name="afterUploadTarget" value="collections">
            <param name="afterUploadURL" value="collections.php">
            <?php } ?>

            Java 1.5 or higher plugin required. 
        </applet>
<!-- --------------------------------------------------------------------------------------------------------
----------------------------------     END OF THE APPLET TAG    ---------------------------------------------
---------------------------------------------------------------------------------------------------------- -->
</div><p><a target="_blank" href="http://www.java.com/getjava">&gt; <?php echo $lang["getjava"] ?></a></p> <A href="javascript: self.close ()"><?php echo $lang['closethiswindow']?></A>  
</div>
<script type='text/javascript'>window.moveTo(0,0);
window.resizeTo(690,document.getElementById('height').offsetHeight+100);</script>
