<?php
include "../include/db.php";
include "../include/general.php";

# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref","",true),$k))) {include "../include/authenticate.php";}

include "../include/search_functions.php";
include "../include/collections_functions.php";
include "../include/resource_functions.php";

$colref=getval("ref","");
$collection=getval("ref","");
$ext="jpg";
$height=getval("height",600);
$vertical=getval("vertical","h");
# Load collection info.
$cinfo=get_collection($usercollection);
$skip=false;
# Check to see if the user can edit this collection.
$allow_reorder=false;
if (($k=="") && (($userref==$cinfo["user"]) || ($cinfo["allow_changes"]==1) || (checkperm("h"))))
	{
	$allow_reorder=true;
	}
if ($allow_reorder || $infobox)
	{
	?>
	<script src="../lib/js/prototype.js" type="text/javascript"></script>
	<script src="../lib/js/scriptaculous.js" type="text/javascript"></script>
	<script src="../lib/js/infobox_collection.js" type="text/javascript"></script>
	<script type="text/javascript">
	function ReorderResources(id1,id2,reverse)
		{
		top.main.location.href='preview_all.php?reorder=' + id1 + '-' + id2+'&ref=<?php echo $colref?>&vertical=<?php echo $vertical?>';
		top.collections.location='collections.php?ref=<?php echo $colref?>';
		}
	</script>
	<?php
	
	# Also check for the parameter and reorder as necessary.
	$reorder=getvalescaped("reorder","");
	if ($reorder!="")
		{
		$reverse=getvalescaped("reverse",false);	
		$r=explode("-",$reorder);
		swap_collection_order($r[0],$r[1],$usercollection,$reverse);
		}
	}	
	
$collection=do_search("!collection" . $colref);

$border=true;

$search=getvalescaped("search","");
$offset=getvalescaped("offset","",true);
$order_by=getvalescaped("order_by","");
$archive=getvalescaped("archive","",true);
$restypes=getvalescaped("restypes","");
$page=getvalescaped("page",1);
$alternative=getvalescaped("alternative",-1);
if (strpos($search,"!")!==false) {$restypes="";}

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);
$headerinsert="
	 <!--[if lt IE 7]><link rel='stylesheet' type='text/css' href='../css/ie.css'><![endif]-->
";



include "../include/header.php";?>
<br/>

<table id="preview_all_table"  >
<tr><p style="margin:7px 0 7px 0;padding:0;"><a href="search.php?search=!collection<?php echo $colref?>&offset=<?php echo $offset?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&lt; <?php echo $lang["backtoresults"]?></a>
&nbsp;&nbsp;<a href="preview_all.php?ref=<?php echo $colref?>&vertical=h&offset=<?php echo $offset?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&gt; Horizontal </a>
&nbsp;&nbsp;<a href="preview_all.php?ref=<?php echo $colref?>&vertical=v&offset=<?php echo $offset?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&gt; Vertical </a>
</tr>	<tr>
		<?php
		$n=0;
for ($x=0;$x<count($collection);$x++){
# Load access level
$ref=$collection[$x]['ref'];

$access=get_resource_access($collection[$x]);

# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if ($access==2) 
		{
		exit("Confidential resource.");
		}

# Locate the resource
$path="";
$url="";
	if ($access==1&&(checkperm('w')|| ($k!="" && isset($watermark)))){$watermark=true;} else {$watermark=false;}
$path=get_resource_path($ref,true,"scr",false,$ext,-1,$page,$watermark,$collection[$x]["file_modified"],$alternative,-1,false);

if (file_exists($path) && resource_download_allowed($collection[$x],"scr"))
	{
	$url=get_resource_path($ref,false,"scr",false,$ext,-1,$page,$watermark,$collection[$x]["file_modified"],$alternative,-1,false);
	}
else
	{
	$path=get_resource_path($ref,true,"pre",false,$ext,-1,$page,$watermark,$collection[$x]["file_modified"],$alternative,-1,false);
	if (file_exists($path))
		{
		$url=get_resource_path($ref,false,"pre",false,$ext,-1,$page,$watermark,$collection[$x]["file_modified"],$alternative,-1,false);
		}
	 }	
if (!file_exists($path))
	{
	$info=get_resource_data($ref);
	$url="../gfx/" . get_nopreview_icon($info["resource_type"],$info["file_extension"],false);
	$border=false;
	}

?>

<?php if ($vertical=="v"){?>
<a href="view.php?ref=<?php echo $collection[$x]['ref']?>">&nbsp;<?php echo $collection[$x]['field'.$view_title_field]?></a><?php hook("afterpreviewalltitle")?></tr><tr><?php }else { ?>
<td style="padding:10px;"><?php } ?>
	
	<div class="ResourceShel_" id="ResourceShel_<?php echo $ref?>">
	<?php if ($vertical=="h"){?>&nbsp;<a href="view.php?ref=<?php echo $collection[$x]['ref']?>"><?php echo $collection[$x]['field'.$view_title_field]?></a><?php hook("afterpreviewalltitle")?><br/><?php } ?>
	<?php $imageinfo = getimageSize( $url ); 
	$imageheight=$imageinfo[1];?>
<a href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"></a><img class="image" id="image<?php echo $ref?>" imageheight="<?php echo $imageheight?>" src="<?php echo $url?>" alt="" style="height:<?php echo $height?>px;border:1px solid white;" />
<script type="text/javascript">
var maxheight=window.innerHeight-110; 
if (maxheight><?php echo $imageheight?>){
	
	$('image<?php echo $ref?>').style.height=<?php echo $imageheight?>;}
	else { $('image<?php echo $ref?>').style.height=maxheight;} </script>
</div></div>
<?php if ($collection_reorder_caption && $allow_reorder) { 
		# Javascript drag/drop enabling.
		?>
		<script type="text/javascript">
		new Draggable('ResourceShel_<?php echo $ref?>',{scroll:window,scrollSpeed:200,scrollSensitivity:60,handle: "ResourceShel_", revert: true});
		Droppables.add('ResourceShel_<?php echo $ref?>',{accept: 'ResourceShel_', onDrop: function(element) { ReorderResources(element.id.substring(13),<?php echo $ref?>,1);}, hoverclass: 'ReorderHover'});
		</script>
		<?php } ?>
<?php if ($vertical=="v"){?><tr><?php } else  { ?></td> <?php } ?>
<?php } ?>
<?php $n++;
?>
<div id="CollectionFramelessCount" style="display:none;"> </div>


</tr>
</table>

<script type="text/javascript">
	top.collections.location.href="<?php echo $baseurl ?>/pages/collections.php?ref=<?php echo $ref ?>&thumbs=hide";

	window.onresize=function(event){
	var maxheight=window.innerHeight-110;
	$$('.image').each(function (elem) {

		if (maxheight> elem.getAttribute("imageheight").replace(/px,*\)*/g,"")){elem.style.height=elem.getAttribute("imageheight"); }
		else { elem.style.height=maxheight;} } );}
</script>
</form>
<?php
include "../include/footer.php";
?>
