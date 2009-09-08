<?php
include "../include/db.php";
include "../include/general.php";

# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}

include "../include/search_functions.php";
include "../include/collections_functions.php";
include "../include/resource_functions.php";

$ref=getval("ref","");
$ext=getval("ext","");

$border=true;
if ($ext!="" && $ext!="gif" && $ext!="jpg" && $ext!="png") {$ext="jpg";$border=false;} # Supports types that have been created using ImageMagick

$search=getvalescaped("search","");
$offset=getvalescaped("offset","");
$order_by=getvalescaped("order_by","");
$archive=getvalescaped("archive","");
$restypes=getvalescaped("restypes","");
$page=getvalescaped("page",1);
if (strpos($search,"!")!==false) {$restypes="";}

# Load access level
$access=get_resource_access($ref);

# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if ($access==2) 
		{
		exit("This is a confidential resource.");
		}

# next / previous resource browsing
$go=getval("go","");
if ($go!="")
	{
	# Re-run the search and locate the next and previous records.
	$result=do_search($search,$restypes,$order_by,$archive,72+$offset+1);
	if (is_array($result))
		{
		# Locate this resource
		$pos=-1;
		for ($n=0;$n<count($result);$n++)
			{
			if ($result[$n]["ref"]==$ref) {$pos=$n;}
			}
		if ($pos!=-1)
			{
			if (($go=="previous") && ($pos>0)) {$ref=$result[$pos-1]["ref"];}
			if (($go=="next") && ($pos<($n-1))) {$ref=$result[$pos+1]["ref"];if (($pos+1)>=($offset+72)) {$offset=$pos+1;}} # move to next page if we've advanced far enough
			}
		}
	}

# Next / previous page browsing (e.g. pdfs)
$previouspage=$page-1;
if (!file_exists(get_resource_path($ref,true,"scr",false,$ext,-1,$previouspage,checkperm("w") && $access==1))&&!file_exists(get_resource_path($ref,true,"",false,$ext,-1,$previouspage,checkperm("w") && $access==1))) {$previouspage=-1;}
$nextpage=$page+1;
if (!file_exists(get_resource_path($ref,true,"scr",false,$ext,-1,$nextpage,checkperm("w") && $access==1))) {$nextpage=-1;}


# Locate the resource

$path=get_resource_path($ref,true,"scr",false,$ext,-1,$page,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
$path_orig=get_resource_path($ref,true,"",false,$ext,-1,$page,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);

if (file_exists($path) && resource_download_allowed($ref,"scr"))
	{
	$url=get_resource_path($ref,false,"scr",false,$ext,-1,$page,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
	}
elseif (file_exists($path_orig) && resource_download_allowed($ref,""))
	{
	$url=get_resource_path($ref,false,"",false,$ext,-1,$page,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
	}
else
	{
	$path=get_resource_path($ref,true,"pre",false,$ext,-1,$page,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
	if (file_exists($path))
		{
		$url=get_resource_path($ref,false,"pre",false,$ext,-1,$page,(checkperm("w") || ($k!="" && isset($watermark))) && $access==1);
		}
	 }	
if (!isset($url))
	{
	$info=get_resource_data($ref);
	$url="../gfx/" . get_nopreview_icon($info["resource_type"],$info["file_extension"],false);
	$border=false;
	}


include "../include/header.php";
?>

<p style="margin:7px 0 7px 0;padding:0;"><a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&lt; <?php echo $lang["backtoview"]?></a>
<?php if ($k=="") { ?>

<?php if (!checkperm("b")) { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo add_to_collection_link($ref,$search)?><?php echo $lang["addtocollection"]?></a><?php } ?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="preview.php?from=<?php echo getval("from","")?>&ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&go=previous">&lt;&nbsp;<?php echo $lang["previousresult"]?></a>
|
<a href="search.php<?php if (strpos($search,"!")!==false) {?>?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?><?php } ?>"><?php echo $lang["viewallresults"]?></a>
|
<a href="preview.php?from=<?php echo getval("from","")?>&ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&go=next"><?php echo $lang["nextresult"]?>&nbsp;&gt;</a>
<?php } ?>

</p>

<?php if (!hook("previewimage")) { ?>
<table cellpadding="0" cellspacing="0">
<tr>
<td valign="middle"><?php if ($previouspage!=-1 &&resource_download_allowed($ref,"scr")) { ?><a href="preview.php?ref=<?php echo $ref?>&ext=<?php echo $ext?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&page=<?php echo $previouspage?>" class="PDFnav">&lt;</a><?php } 
elseif ($nextpage!=-1 &&resource_download_allowed($ref,"scr") ) { ?><a href="#" class="PDFnav">&nbsp;&nbsp;&nbsp;</a><?php } ?></td>
<td><a href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><img src="<?php echo $url?>" alt="" <?php if ($border) { ?>style="border:1px solid white;"<?php } ?> /></a></td>
<td valign="middle"><?php if ($nextpage!=-1 &&resource_download_allowed($ref,"scr")) { ?><a href="preview.php?ref=<?php echo $ref?>&ext=<?php echo $ext?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&archive=<?php echo $archive?>&page=<?php echo $nextpage?>" class="PDFnav">&gt;</a><?php } ?></td>
</tr></table>
<?php } ?>

<div id="CollectionFramelessCount" style="display:none;"> </div>

<?php
include "../include/footer.php";
?>
