<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/collections_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*4);

include "../../include/header.php";

$use_local=getvalescaped("use_local","");if ($use_local!="") {$use_local=true;} else {$use_local=false;}
	
$collection=getvalescaped("collection","");
if ($collection!="") {set_user_collection($userref,$collection);}

?>
<div class="BasicsBox">
<h1><?php echo $lang["uploadresourcebatch"]?></h1>
<p><?php echo text("introtext")?></p>
</div>

<div class="RecordBox">
<div class="RecordPanel"> 
<div class="RecordResouce">
<div class="Title"><?php echo $lang["uploadinprogress"]?></div>
<p id="uploadstatus"><?php echo $lang["transferringfiles"]?><br/><b><?php echo $lang["donotmoveaway"]?></b><br/><br/></p>
<div class="clearerleft"> </div>
</div>
</div>
<div class="PanelShadow"></div>
</div>

<?php
include "../../include/footer.php";
flush();

# Download files
if (!array_key_exists("uploadfiles",$_POST))
	{
	?><script>alert("<?php echo $lang["pleaseselectfiles"]?>");history.go(-1);</script><?php
	exit();
	}
$uploadfiles=$_POST["uploadfiles"];
$done=0;$failed=0;
for ($n=0;$n<count($uploadfiles);$n++)
	{
	if (!$use_local)
		{
		# Connect to FTP server
		$ftp=ftp_connect(getval("ftp_server",""));
		ftp_login($ftp,getval("ftp_username",""),getval("ftp_password",""));
		}
		
	$path=getval("ftp_folder","") . $uploadfiles[$n];
	echo $path;
	
	# Copy the resource
	$ref=copy_resource(0-$userref,getvalescaped("resource_type",1));
	
    # Find and store extension in the database
    $extension=explode(".",$uploadfiles[$n]);$extension=trim(strtolower($extension[count($extension)-1]));
    sql_query("update resource set file_extension='$extension',preview_extension='$extension' where ref='$ref'");


	$localpath=get_resource_path($ref,true,"",true,$extension);
	
	$result=false;
	error_reporting(0);

	if ($use_local)
		{
		$folder=dirname(__FILE__)."/../../upload";;
		if ($groupuploadfolders) {$folder.="/" . $usergroup;}
		$result=copy($folder . "/" . $uploadfiles[$n],$localpath);
		}
	else
		{
		$result=ftp_get($ftp,$localpath,$path,FTP_BINARY);
		}

	if ($result) 
		{
	    # Create image previews for supported image files only.
    	?><script>document.getElementById('uploadstatus').innerHTML+="<?php echo $lang["resizingimage"]?> <?php echo $n+1?> <?php echo $lang["of"]?> <?php echo count($uploadfiles)?><br/>";</script>
    	<?php
		flush();
    	create_previews($ref,false,$extension);
    	
		# get file metadata 
   		 global $exiftool_path;
   		 extract_exif_comment($ref,$extension);

 	
		# Store original filename in field, if set
		if (isset($filename_field))
			{
			update_field($ref,$filename_field,$uploadfiles[$n]);
			}
    	
		$status=$lang["uploaded"] . " " . ($n+1) . " " . $lang["of"] . " " . count($uploadfiles);
		
		# Show thumb?
		$rd=get_resource_data($ref);$thumb=get_resource_path($ref,true,"thm",false,$rd["preview_extension"]);
		if (file_exists($thumb))
			{
			$status.="<br/><img src='" . get_resource_path($ref,false,"thm",false,$rd["preview_extension"]) . "'>";
			}
		$done++;
		
		# Add to collection?
		if ($collection!="")
			{
			add_resource_to_collection($ref,$collection);
			refresh_collection_frame();
			}
			
		# Log this
		daily_stat("Resource upload",$ref);
		resource_log($ref,'u',0);
		}
	else
		{
		$status=$lang["uploadfailedfor"] . $path;
		sleep(2);$failed++;
		}
	?><script language="Javascript">document.getElementById('uploadstatus').innerHTML+="<?php echo $status?><br/><br/>";</script>
	<?php
	flush();
	}

if (!$use_local)
	{
	ftp_close($ftp);
	}
?>
<script>document.getElementById('uploadstatus').innerHTML+="<?php echo $lang["uploadcomplete"]?> <?php echo $done?> <?php echo $lang["resourcesuploadedok"]?>, <?php echo $failed?> <?php echo $lang["failed"]?>. <?php echo $lang["clickviewnewmaterial"]?>";</script>