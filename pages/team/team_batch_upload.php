<?php
include "../../include/db.php";
include "../../include/authenticate.php"; if (!checkperm("c")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/resource_functions.php";
include "../../include/collections_functions.php";
include "../../include/image_processing.php";

set_time_limit(60*60*4);

$use_local = getvalescaped('use_local', '') !== '';

$collection=getvalescaped("collection","");

# Create a new collection?
if ($collection==-1)
	{
	# The user has chosen Create New Collection from the dropdown.
	$collection=create_collection($userref,$lang["upload"] . " " . date("ymdHis"));
	set_user_collection($userref,$collection);
	refresh_collection_frame();
	}

if ($collection!="") {set_user_collection($userref,$collection); refresh_collection_frame();}


include "../../include/header.php";
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
	?><script type="text/javascript">alert("<?php echo $lang["pleaseselectfiles"]?>");history.go(-1);</script><?php
	exit();
	}

if ($use_local) // Test if we fetch files from local upload folder.
	{
	# We compute the folder name from the upload folder option.
	$folder = getAbsolutePath($local_ftp_upload_folder, true);

	if ($groupuploadfolders) // Test if we are using sub folders assigned to groups.
		{
		$folder.= DIRECTORY_SEPARATOR . $usergroup;
		}
	} // Test if we fetch files from local upload folder.

$uploadfiles=$_POST["uploadfiles"];
$done=0;$failed=0;
for ($n=0;$n<count($uploadfiles);$n++)
	{
	if (!$use_local)
		{
		# Connect to FTP server
		$ftp=ftp_connect(getval("ftp_server",""));
		ftp_login($ftp,getval("ftp_username",""),getval("ftp_password",""));
		ftp_pasv($ftp,true);
		}
		
	$path=getval("ftp_folder","") . "/"	. $uploadfiles[$n];
	
	# Copy the resource
	$ref=copy_resource(0-$userref);
	
	# Find and store extension in the database
	$extension=explode(".",$uploadfiles[$n]);
	$extension=trim(strtolower($extension[count($extension)-1]));
	sql_query("update resource set file_extension='$extension',preview_extension='$extension' where ref='$ref'");


	$localpath=get_resource_path($ref,true,"",true,$extension);
	
	$result=false;
	error_reporting(0);

	if ($use_local)
		{
		$result=copy($folder . DIRECTORY_SEPARATOR . $uploadfiles[$n],$localpath);
		}
	else
		{
		$result=ftp_get($ftp,$localpath,$path,FTP_BINARY);
		}

	if ($result) 
		{

		if($enable_thumbnail_creation_on_upload) // Test if thumbnail creation is allowed during upload
			{
			# Create image previews for supported image files only.
			?><script type="text/javascript">document.getElementById('uploadstatus').innerHTML+="<?php echo $lang["resizingimage"]?> <?php echo $n+1?> <?php echo $lang["of"]?> <?php echo count($uploadfiles)?><br/>";</script>
			<?php
			flush();
			create_previews($ref,false,$extension);
			
			} // Test if thumbnail creation is allowed during upload

		# Store original filename in field, if set
		if (isset($filename_field))
			{
			$filename = $uploadfiles[$n];
			if ($use_local)
				{
				$filename = mb_basename($filename);
				}
			update_field($ref,$filename_field, $filename);
			}

		# get file metadata 
		global $exiftool_path;
		extract_exif_comment($ref,$extension);

		$status=$lang["uploaded"] . " " . ($n+1) . " " . $lang["of"] . " " . count($uploadfiles);
		$status.= " - ".$path;
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
			?><script type="text/javascript">top.collections.location.href="../collections.php?add=<?php echo $ref?>&nc=<?php echo time()?>&search=<?php echo urlencode($search)?>";</script>
	<?php
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
	?><script type="text/javascript">document.getElementById('uploadstatus').innerHTML+="<?php echo $status?><br/><br/>";</script>
	<?php
	flush();
	}

if (!$use_local)
	{
	ftp_close($ftp);
	}
?>
<script type="text/javascript">document.getElementById('uploadstatus').innerHTML+="<?php echo $lang["uploadcomplete"]?> <?php echo $done?> <?php echo $lang["resourcesuploadedok"]?>, <?php echo $failed?> <?php echo $lang["failed"]?>. <?php echo $lang["clickviewnewmaterial"]?>";</script>
