<?
include "include/db.php";
include "include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection",""),$k))) {include "include/authenticate.php";}
include "include/general.php";
include "include/search_functions.php";
include "include/resource_functions.php";

$collection=getvalescaped("collection","");
$size=getvalescaped("size","");

if ($size!="")
	{
	$path="";
	
	# Build a list of files to download
	$result=do_search("!collection" . $collection);
	for ($n=0;$n<count($result);$n++)
		{
		$ref=$result[$n]["ref"];
		# Load access level
		$access=$result[$n]["access"];
		if (checkperm("v") || ($k!=""))
			{
			$access=0; # Permission to access all resources
			}
		else
			{
			if ($access==3)
				{
				# Load custom access level
				$access=get_custom_access($ref,$usergroup);
				}
			}
			
		# Only download resources with proper access level
		if ($access==0)
			{
			$p=get_resource_path($ref,$size,false,$result[$n]["file_extension"]);
			if (!file_exists($p))
				{
				# If the file doesn't exist for this size, then the original file must be in the requested size.
				# Try again with the size omitted to get the original.
				$p=get_resource_path($ref,"",false,$result[$n]["file_extension"]);
				}
			if (file_exists($p))
				{
				$path.=" \"" . $p . "\"";
				daily_stat("Resource download",$ref);
				resource_log($ref,'d',0);
				}
			}
		}
	if ($path=="") {exit("Nothing to download.");}	
	
	
	# Create and send the zipfile
	$file="collection_" . $collection . "_" . $size . ".zip";
	exec("$zipcommand /tmp/" . $file . $path);
	$filesize=filesize("/tmp/" . $file);
	
	header("Content-Disposition: attachment; filename=" . $file);
	header("Content-Type: application/zip");
	header("Content-Length: " . $filesize);
	
	set_time_limit(0);
	echo file_get_contents("/tmp/" . $file);
	
	unlink($file);
	exit();
	}
include "include/header.php";
?>
<div class="BasicsBox">
<h1><?=$lang["downloadzip"]?></h1>

<form method=post>
<input type=hidden name="collection" value="<?=$collection?>">

<div class="Question">
<label for="downloadsize"><?=$lang["downloadsize"]?></label>
<div class="tickset">
<div class="Inline"><select name="size" class="shrtwidth" id="downloadsize">
<?
$sizes=get_all_image_sizes();
for ($n=0;$n<count($sizes);$n++)
	{
	?><option value="<?=$sizes[$n]["id"]?>"><?=i18n_get_translated($sizes[$n]["name"])?></option><?
	}
?></select></div>
<div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["download"]?>&nbsp;&nbsp;" /></div>
</div>
<div class="clearerleft"> </div>
</div>
</form>

</div>
<?
include "include/footer.php";
?>

