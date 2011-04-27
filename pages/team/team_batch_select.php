<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/collections_functions.php";

$use_local = getvalescaped('use_local', '') !== '';
$resource_type = getvalescaped('resource_type','');
$collection_add = getvalescaped("collection_add","");
$collectionname = getvalescaped("entercolname","");

$allowed_extensions=get_allowed_extensions_by_type($resource_type);

# Create a new collection?
if ($collection_add==-1)
	{
	# The user has chosen Create New Collection from the dropdown.
	if ($collectionname==""){$collectionname=$lang["upload"] . " " . date("ymdHis");}
	$collection_add=create_collection($userref,$collectionname);
	}

if ($collection_add!="")
	{
	# Switch to the selected collection (existing or newly created) and refresh the frame.
 	set_user_collection($userref,$collection_add);
 	refresh_collection_frame($collection_add);
 	}
	
if ($use_local)
	{
	# File list from local upload directory.

	# Define the titles:
	$titleh1 = $lang["addresourcebatchlocalfolder"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("2", $lang["fileupload"]), $lang["header-upload-subtitle"]);
	
	# We compute the folder name from the upload folder option.
	$folder = getAbsolutePath($local_ftp_upload_folder, true);

	if ($groupuploadfolders) // Test if we are using sub folders assigned to groups.
		{
		$folder.= DIRECTORY_SEPARATOR . $usergroup;
		}

	if (!file_exists($folder)) // If the upload folder does not exists, we try to create it.
		{
		mkdir($folder,0777);
		}

	// We list folder contents
	$files = getFolderContents($folder);
	}
else
	{
	# Connect to FTP server for file listing

	# Define the titles:
	$titleh1 = $lang["addresourcebatchftp"];
	$titleh2 = str_replace(array("%number","%subtitle"), array("3", $lang["fileupload"]), $lang["header-upload-subtitle"]);
	
	$ftp=@ftp_connect(getval("ftp_server",""));
	if ($ftp===false) {exit("FTP connection failed.");}
	ftp_login($ftp,getval("ftp_username",""),getval("ftp_password",""));
	ftp_pasv($ftp,true);

	$folder=getval("ftp_folder","");
	if (substr($folder,strlen($folder)-1,1)!="/") {$folder.="/";}
	$files=ftp_nlist($ftp,$folder);
	ftp_close($ftp);
	}
	
include "../../include/header.php";
?>
<div class="BasicsBox">

<h1><?php echo $titleh1 ?></h1>
<h2><?php echo $titleh2 ?></h2>
<p><?php echo $use_local ? $lang["intro-local_upload"] : $lang["intro-ftp_upload"] ?></p>

<form method="post" action="team_batch_upload.php">
<input type="hidden" name="ftp_server" value="<?php echo getval("ftp_server","")?>">
<input type="hidden" name="ftp_username" value="<?php echo getval("ftp_username","")?>">
<input type="hidden" name="ftp_password" value="<?php echo getval("ftp_password","")?>">
<input type="hidden" name="ftp_folder" value="<?php echo getval("ftp_folder","")?>">
<input type="hidden" name="use_local" value="<?php echo getval("use_local","")?>">
<input type="hidden" name="no_exif" value="<?php echo getval("no_exif","")?>">
<input type="hidden" name="autorotate" value="<?php echo getval("autorotate","")?>">
<input type="hidden" name="collection" value="<?php echo $collection_add?>">


<div class="Question"><label><?php echo $use_local ? $lang["local_upload_path"] : $lang["ftp_upload_path"] ?></label><input name="folder" type="text" class="stdwidth" value="<?php echo $use_local ? $folder : getval("ftp_server","") . "/" . $folder?>" readonly="readonly"></div>

<div class="Question"><label><?php echo $lang["foldercontent"] ?></label>
<!--<div class="tickset">-->
<select name="uploadfiles[]" multiple size=20>
<?php 
foreach ($files as $fn){
       if (!$use_local) {
               # FTP - split up path
               $fs=explode("/",$fn);
               if (count($fs)==1) {$fs=explode("\\",$fn);} # Support backslashes
               $fn=$fs[count($fs)-1];
               }
	$show=true;
	if (($fn=="..") || ($fn==".")) {$show=false;}
	if (strpos($fn,".")===false) {$show=false;}
	if ($fn=="pspbrwse.jbf") {$show=false;} # Ignore PSP browse files (often imported by mistake)
	if ($fn==".DS_Store") {$show=false;} # Ignore .DS_Store file on the mac
	
	# omit disallowed extensions
	if ($allowed_extensions!=""){
	    $extension=explode(".",$fn);
		if(count($extension)>1){
    	$extension=trim(strtolower($extension[count($extension)-1]));
		} 
		if (!strstr($allowed_extensions,$extension)){$show=false;}
	}
	
	/* if ($show) { ?><div class="tick"><input type="checkbox" name="uploadfiles[]" value="<?php echo $fn?>" checked /><?php echo $fn?></div><?php } ?>
	*/
	if ($show) { ?><option value="<?php echo $fn?>" selected><?php echo $fn?></option><?php } ?>
	<?php
	}
?>
<!--</div>-->
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="back" type="button" onclick="history.back(-1)" value="&nbsp;&nbsp;<?php echo $lang["back"] ?>&nbsp;&nbsp;" />
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["action-upload"] ?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>
