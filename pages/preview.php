<?
include "../include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}
include "../include/general.php";
include "../include/search_functions.php";
include "../include/collections_functions.php";

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
if (!file_exists(dirname(__FILE__) . "/../" . get_resource_path($ref,"scr",false,$ext,-1,$previouspage))) {$previouspage=-1;}
$nextpage=$page+1;
if (!file_exists(dirname(__FILE__) . "/../" . get_resource_path($ref,"scr",false,$ext,-1,$nextpage))) {$nextpage=-1;}

include "../include/header.php";
?>

<p style="margin:7px 0 7px 0;padding:0;"><a href="view.php?ref=<?=$ref?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&k=<?=$k?>">&lt; <?=$lang["backtoview"]?></a>
<? if ($k=="") { ?>

<? if (!checkperm("b")) { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?=add_to_collection_link($ref,$search)?><?=$lang["addtocollection"]?></a><? } ?>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="preview.php?from=<?=getval("from","")?>&ref=<?=$ref?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&go=previous">&lt;&nbsp;<?=$lang["previousresult"]?></a>
|
<a href="search.php<? if (strpos($search,"!")!==false) {?>?search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?><? } ?>"><?=$lang["viewallresults"]?></a>
|
<a href="preview.php?from=<?=getval("from","")?>&ref=<?=$ref?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&go=next"><?=$lang["nextresult"]?>&nbsp;&gt;</a>
<? } ?>

</p>

<? if (!hook("previewimage")) { ?>
<table cellpadding="0" cellspacing="0">
<tr>
<td valign="middle"><? if ($previouspage!=-1) { ?><a href="preview.php?ref=<?=$ref?>&ext=<?=$ext?>&k=<?=$k?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&page=<?=$previouspage?>" class="PDFnav">&lt;</a><? } 
elseif ($nextpage!=-1) { ?><a href="#" class="PDFnav">&nbsp;&nbsp;&nbsp;</a><? } ?></td>
<td><a href="<?=((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&k=<?=$k?>"><img src="download.php?ref=<?=$ref?>&size=scr&ext=<?=$ext?>&noattach=true&k=<?=$k?>&page=<?=$page?>" alt="" <? if ($border) { ?>style="border:1px solid white;"<? } ?> /></a></td>
<td valign="middle"><? if ($nextpage!=-1) { ?><a href="preview.php?ref=<?=$ref?>&ext=<?=$ext?>&k=<?=$k?>&search=<?=urlencode($search)?>&offset=<?=$offset?>&order_by=<?=$order_by?>&archive=<?=$archive?>&page=<?=$nextpage?>" class="PDFnav">&gt;</a><? } ?></td>
</tr></table>
<? } ?>

<div id="CollectionFramelessCount" style="display:none;"> </div>

<?
include "../include/footer.php";
?>