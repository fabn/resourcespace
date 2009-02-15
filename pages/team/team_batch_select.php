<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/collections_functions.php";

$use_local = getvalescaped('use_local', '') !== '';

if ($use_local)
	{
	# File list from local upload directory.

	# We compute the folder name from the upload folder option.
	if(preg_match('/^(\/|[a-zA-Z]:[\\/]{1})/', $local_ftp_upload_folder)) // If the upload folder path start by a '/' or 'c:\', it is an absolute path.
		{
		$folder = $local_ftp_upload_folder;
		}
	else // It is a relative path.
		{
		$folder = sprintf('%s%s..%s..%s%s', dirname(__FILE__), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $local_ftp_upload_folder);
		}

	if ($groupuploadfolders) // Test if we are using sub folders assigned to groups.
		{
		$folder.= DIRECTORY_SEPARATOR . $usergroup;
		}

	if (!file_exists($folder)) // If the upload folder does not exists, we try to create it.
		{
		mkdir($folder,0777);
		}

	$dh=opendir($folder);
	$files=array();
	while (($file = readdir($dh)) !== false)
		{
		$filetype=filetype($folder . "/" . $file);
	  if ($filetype=="file")
	  	{
	   	$files[]=$file;
	   	}
	  }
	}
else
	{
	# Connect to FTP server for file listing
	$ftp=@ftp_connect(getval("ftp_server",""));
	if ($ftp===false) {exit("FTP connection failed.");}
	ftp_login($ftp,getval("ftp_username",""),getval("ftp_password",""));
	$folder=getval("ftp_folder","");
	if (substr($folder,strlen($folder)-1,1)!="/") {$folder.="/";}
	$files=ftp_rawlist($ftp,$folder);
	ftp_close($ftp);
	}
	
include "../../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["selectfiles"]?></h1>
<p><?php echo text("introtext")?></p>

<form method=post action="team_batch_upload.php">
<input type=hidden name="ftp_server" value="<?php echo getval("ftp_server","")?>">
<input type=hidden name="ftp_username" value="<?php echo getval("ftp_username","")?>">
<input type=hidden name="ftp_password" value="<?php echo getval("ftp_password","")?>">
<input type=hidden name="ftp_folder" value="<?php echo getval("ftp_folder","")?>">
<input type=hidden name="use_local" value="<?php echo getval("use_local","")?>">

<div class="Question">
<label for="resourcetype"><?php echo $lang["resourcetype"]?></label>
<select name="resource_type" id="resourcetype" class="shrtwidth">
<?php
$types=get_resource_types();
for ($n=0;$n<count($types);$n++)
	{
	?><option value="<?php echo $types[$n]["ref"]?>"><?php echo $types[$n]["name"]?></option><?php
	}
?></select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="collection"><?php echo $lang["addtocollection"]?></label>
<select name="collection" id="collection" class="shrtwidth">
<option value=""><?php echo $lang["batchdonotaddcollection"]?></option>
<?php
$list=get_user_collections($userref);
for ($n=0;$n<count($list);$n++)
	{
	?>
	<option value="<?php echo $list[$n]["ref"]?>"><?php echo htmlspecialchars($list[$n]["name"])?></option>
	<?php
	}?></select>
<div class="clearerleft"> </div>
</div>


<div class="Question"><label><?php echo $lang["selectfiles"]?></label>
<!--<div class="tickset">-->
<select name="uploadfiles[]" multiple size=20>
<?php for ($n=0;$n<count($files);$n++)
	{
	if ($use_local) {$fn=$files[$n];} else {$fs=explode(" ",$files[$n]);$fn=$fs[count($fs)-1];}
	$show=true;
	if (($fn=="..") || ($fn==".")) {$show=false;}
	if (strpos($fn,".")===false) {$show=false;}
	if ($fn=="pspbrwse.jbf") {$show=false;} # Ignore PSP browse files (often imported by mistake)
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
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["upload"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../../include/footer.php";
?>
