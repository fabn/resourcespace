<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "include/general.php";
include "include/collections_functions.php";

$use_local=getvalescaped("use_local","");if ($use_local!="") {$use_local=true;} else {$use_local=false;}

if ($use_local)
	{
	# File list from local upload directory.
	$folder="upload";
	if ($groupuploadfolders) {$folder.="/" . $usergroup;}
	if (!file_exists($folder)) {mkdir($folder,0777);}
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
	
include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["selectfiles"]?></h1>
<p><?=text("introtext")?></p>

<form method=post action="team_batch_upload.php">
<input type=hidden name="ftp_server" value="<?=getval("ftp_server","")?>">
<input type=hidden name="ftp_username" value="<?=getval("ftp_username","")?>">
<input type=hidden name="ftp_password" value="<?=getval("ftp_password","")?>">
<input type=hidden name="ftp_folder" value="<?=getval("ftp_folder","")?>">
<input type=hidden name="use_local" value="<?=getval("use_local","")?>">

<div class="Question">
<label for="resourcetype"><?=$lang["resourcetype"]?></label>
<select name="resource_type" id="resourcetype" class="shrtwidth">
<?
$types=get_resource_types();
for ($n=0;$n<count($types);$n++)
	{
	?><option value="<?=$types[$n]["ref"]?>"><?=$types[$n]["name"]?></option><?
	}
?></select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="collection"><?=$lang["addtocollection"]?></label>
<select name="collection" id="collection" class="shrtwidth">
<option value=""><?=$lang["batchdonotaddcollection"]?></option>
<?
$list=get_user_collections($userref);
for ($n=0;$n<count($list);$n++)
	{
	?>
	<option value="<?=$list[$n]["ref"]?>"><?=htmlspecialchars($list[$n]["name"])?></option>
	<?
	}?></select>
<div class="clearerleft"> </div>
</div>


<div class="Question"><label><?=$lang["selectfiles"]?></label>
<!--<div class="tickset">-->
<select name="uploadfiles[]" multiple size=20>
<? for ($n=0;$n<count($files);$n++)
	{
	if ($use_local) {$fn=$files[$n];} else {$fs=explode(" ",$files[$n]);$fn=$fs[count($fs)-1];}
	$show=true;
	if (($fn=="..") || ($fn==".")) {$show=false;}
	if (strpos($fn,".")===false) {$show=false;}
	if ($fn=="pspbrwse.jbf") {$show=false;} # Ignore PSP browse files (often imported by mistake)
	/* if ($show) { ?><div class="tick"><input type="checkbox" name="uploadfiles[]" value="<?=$fn?>" checked /><?=$fn?></div><? } ?>
	*/
	if ($show) { ?><option value="<?=$fn?>" selected><?=$fn?></option><? } ?>
	<?
	}
?>
<!--</div>-->
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["upload"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "include/footer.php";
?>