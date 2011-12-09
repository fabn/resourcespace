<?php
include "../include/db.php";
include "../include/general.php";

# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref","",true),$k))) {include "../include/authenticate.php";}

include "../include/search_functions.php";
include "../include/collections_functions.php";
include "../include/resource_functions.php";

$ref=getval("ref","");
$ext=getval("ext","");

$resource=get_resource_data($ref);

if ($ext!="" && $ext!="gif" && $ext!="jpg" && $ext!="png") {$ext="jpg";$border=false;} # Supports types that have been created using ImageMagick

$search=getvalescaped("search","");
$offset=getvalescaped("offset","",true);
$order_by=getvalescaped("order_by","");
$archive=getvalescaped("archive","",true);
$restypes=getvalescaped("restypes","");
$starsearch=getvalescaped("starsearch","");
$page=getvalescaped("page",1);
$alternative=getvalescaped("alternative",-1);
if (strpos($search,"!")!==false) {$restypes="";}

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

# Load access level
$access=get_resource_access($ref);
$use_watermark=check_use_watermark($ref);

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
	$result=do_search($search,$restypes,$order_by,$archive,-1,$sort,false,$starsearch);
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
if (!file_exists(get_resource_path($ref,true,"scr",false,$ext,-1,$previouspage,$use_watermark,"",$alternative))&&!file_exists(get_resource_path($ref,true,"",false,$ext,-1,$previouspage,$use_watermark,"",$alternative))) {$previouspage=-1;}
$nextpage=$page+1;
if (!file_exists(get_resource_path($ref,true,"scr",false,$ext,-1,$nextpage,$use_watermark,"",$alternative))) {$nextpage=-1;}


# Locate the resource

$path=get_resource_path($ref,true,"scr",false,$ext,-1,$page,$use_watermark,"",$alternative);
$path_orig=get_resource_path($ref,true,"",false,$ext,-1,$page,$use_watermark,"",$alternative);

if (file_exists($path) && resource_download_allowed($ref,"scr",$resource["resource_type"]))
	{
	$url=get_resource_path($ref,false,"scr",false,$ext,-1,$page,$use_watermark,"",$alternative);
	}
elseif (file_exists($path_orig) && resource_download_allowed($ref,"",$resource["resource_type"]))
	{
	$url=get_resource_path($ref,false,"",false,$ext,-1,$page,$use_watermark,"",$alternative);
	}
else
	{
	$path=get_resource_path($ref,true,"pre",false,$ext,-1,$page,$use_watermark,"",$alternative);
	if (file_exists($path))
		{
		$url=get_resource_path($ref,false,"pre",false,$ext,-1,$page,$use_watermark,"",$alternative);
		}
	 }	
if (!isset($url))
	{
	$info=get_resource_data($ref);
	$url=$baseurl."/gfx/" . get_nopreview_icon($info["resource_type"],$info["file_extension"],false);
	$border=false;
	}

$resource=get_resource_data($ref);

if ($mp3_player){
	$mp3path=get_resource_path($ref,false,"",false,"mp3");
	$mp3realpath=get_resource_path($ref,true,"",false,"mp3");
}
$pagename="preview";
include "../include/header.php";
?>

<? if(!hook("fullpreviewresultnav")): ?>
<p style="margin:7px 0 7px 0;padding:0;"><a href="view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&lt; <?php echo $lang["backtoview"]?></a>
<?php if ($k=="") { ?>

<?php if (!checkperm("b")) { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php echo add_to_collection_link($ref,$search)?>&gt;&nbsp;<?php echo $lang["action-addtocollection"]?></a><?php } ?>


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="preview.php?from=<?php echo getval("from","")?>&ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&go=previous">&lt;&nbsp;<?php echo $lang["previousresult"]?></a>
|
<a href="search.php<?php if (strpos($search,"!")!==false) {?>?search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?><?php } ?>"><?php echo $lang["viewallresults"]?></a>
|
<a href="preview.php?from=<?php echo getval("from","")?>&ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&go=next"><?php echo $lang["nextresult"]?>&nbsp;&gt;</a>
<?php } ?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php

if (!hook("replacepreviewpager")){
	
if (($nextpage!=-1 || $previouspage!=-1) && $nextpage!=-0){
    $pagecount= get_page_count($resource,$alternative);
    if ($pagecount!=null && $pagecount!=-2){
    ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lang['page'];?>: <select onChange="document.location='preview.php?ref=<?php echo $ref?>&alternative=<?php echo $alternative?>&ext=<?php echo $ext?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&page='+this.value;"><?php 
    for ($n=1;$n<$pagecount+1;$n++){
        if ($n<=$pdf_pages){
            ?><option value="<?php echo $n?>" <?php if ($page==$n){?>selected<?php } ?>><?php echo $n?><?php
            }
        }
    if ($pagecount>$pdf_pages){?><option value="1">...<?php }     
    ?></select><?php
}
}
}
?>


</p>
<? endif; ?>

<?php if (!hook("previewimage")) { ?>
<?php if (!hook("previewimage2")) { ?>
<table cellpadding="0" cellspacing="0">
<tr>

<td valign="middle"><?php if ($resource['file_extension']!="jpg" && $previouspage!=-1 &&resource_download_allowed($ref,"scr",$resource["resource_type"])) { ?><a href="preview.php?ref=<?php echo $ref?>&alternative=<?php echo $alternative?>&ext=<?php echo $ext?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&page=<?php echo $previouspage?>" class="PDFnav">&lt;</a><?php } 
elseif ($nextpage!=-1 &&resource_download_allowed($ref,"scr",$resource["resource_type"]) ) { ?><a href="#" class="PDFnav">&nbsp;&nbsp;&nbsp;</a><?php } ?></td>
<?php $flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
if (!file_exists($flvfile)) {$flvfile=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension);}
if (!(isset($resource['is_transcoding']) && $resource['is_transcoding']==1) && file_exists($flvfile) && (strpos(strtolower($flvfile),".".$ffmpeg_preview_extension)!==false))
	{
	# Include the Flash player if an FLV file exists for this resource.
	$download_multisize=false;
    if(!hook("customflvplay"))
        {
        include "flv_play.php";
        }
    }
    elseif (!(isset($resource['is_transcoding']) && $resource['is_transcoding']==1) && file_exists($mp3realpath) && hook("custommp3player")){
		// leave preview to the custom mp3 player
	}	
    else{?>
<td><a href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><img class="Picture" src="<?php echo $url?>" alt=""/></a></td>

<?php } ?>

<td valign="middle"><?php if ($nextpage!=-1 &&resource_download_allowed($ref,"scr",$resource["resource_type"])) { ?><a href="preview.php?ref=<?php echo $ref?>&alternative=<?php echo $alternative?>&ext=<?php echo $ext?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&page=<?php echo $nextpage?>" class="PDFnav">&gt;</a><?php } ?></td>
</tr></table>

<?php } // end hook previewimage2 ?>
<?php } // end hook previewimage ?>
<div id="CollectionFramelessCount" style="display:none;"> </div>

<?php

if ($show_resource_title_in_titlebar){
	$title =  htmlspecialchars(i18n_get_translated(get_data_by_field($ref,$view_title_field)));
	if (!$frameless_collections){$parentword = 'parent.';} else { $parentword = ''; }
	if (strlen($title) > 0){
		echo "<script language='javascript'>\n";
		echo $parentword . "document.title = \"$applicationname - $title\";\n";
		echo "</script>";
	}
}

include "../include/footer.php";
?>
