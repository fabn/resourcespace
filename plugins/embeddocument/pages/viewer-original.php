<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/resource_functions.php";

# Get variables and check key is valid.
$ref=getvalescaped("ref","");
$key=getvalescaped("key","");
if ($key!=md5($scramble_key . $ref)) {exit("Invalid key.");}

# Load resource data
$resource=get_resource_data($ref);

# Load watermark settings
$use_watermark=check_use_watermark();

# Work out if we're allowing download by validating the download key.
$download=false;
$downloadkey=getvalescaped("downloadkey","");
if ($downloadkey==md5($scramble_key . $ref . "download")) {$download=true;}

?>
<html>
<head>
<link href="../css/embeddocument.css" rel="stylesheet" type="text/css" media="screen,projection,print" /> 
</head>
<body style="padding:0;margin:0;">

<div class="embeddocument_main">
<div class="embeddocument_preview" id="embeddocument_preview_<?php echo $ref ?>"> </div>

<div class="embeddocument_buttons_left">
<?php echo $lang["page"] ?>:
<input type="text" id="embeddocument_page_box" size="2" />
<button onClick="embeddocument_auto=false;embeddocument_ShowPage_<?php echo $ref ?>(document.getElementById('embeddocument_page_box').value);">JUMP</button>
<button class="embeddocument_begn" onClick="embeddocument_auto=false;embeddocument_ShowPage_<?php echo $ref ?>(1);">BEGN</button>
<button class="embeddocument_prev" onClick="embeddocument_auto=false;embeddocument_ShowPage_<?php echo $ref ?>(embeddocument_page-1);">PREV</button>
<button class="embeddocument_auto" onClick="embeddocument_auto=!embeddocument_auto;if (embeddocument_auto) {embeddocument_ShowPage_<?php echo $ref ?>(embeddocument_page);} else {clearTimeout(timer);}">AUTO</button>
<button class="embeddocument_next" onClick="embeddocument_auto=false;embeddocument_ShowPage_<?php echo $ref ?>(embeddocument_page+1);">NEXT</button>
<button class="embeddocument_end" onClick="embeddocument_auto=false;embeddocument_ShowPage_<?php echo $ref ?>(embeddocument_pages.length-1);">END</button>
</div>

<?php if ($download) { 
$full_path=get_resource_path($ref,false,"",false,$resource["file_extension"]);
?>
<div class="embeddocument_buttons_right">
<button class="embeddocument_end" onClick="top.location.href='<?php echo $full_path ?>';">DNLD</button>
</div>
<?php } ?>
</div>

<script type="text/javascript">
// Load pages
var embeddocument_page=1;
var embeddocument_pages =  new Array();
var embeddocument_auto=false;
var timer;
<?php
$page=1;
while (true)
	{
	$file_path=get_resource_path($ref,true,"scr",false,$resource["preview_extension"],-1,$page,$use_watermark); 
	$preview_path=get_resource_path($ref,false,"scr",false,$resource["preview_extension"],-1,$page,$use_watermark);

	# No more pages? End the loop.
	if (!file_exists($file_path)) {break;}
	
	# Work out height and width
	$ratio=$resource["thumb_width"]/$resource["thumb_height"];
	$width=getvalescaped("width","");
	$height=floor($width / $ratio);
	
	?>
	embeddocument_pages[<?php echo $page ?>]='<a href="#" onClick="embeddocument_ShowPage_<?php echo $ref ?>(" . ($page + 1) . ");"><img border="0" width=<?php echo $width ?> height=<?php echo $height ?> src="<?php echo $preview_path ?>"></a>';
	<?php

	$page++;
	}
?>

function embeddocument_ShowPage_<?php echo $ref ?>(page_set,from_auto)
	{
	if (!embeddocument_auto && from_auto) {return false;} // Auto switched off but timer still running. Terminate.
	
	embeddocument_page=page_set;
	if (embeddocument_page>(embeddocument_pages.length-1)) {embeddocument_page=embeddocument_pages.length-1;} // back to first page
	if (embeddocument_page<1) {embeddocument_page=1;} // to last page
	
	document.getElementById("embeddocument_preview_<?php echo $ref ?>").innerHTML=embeddocument_pages[embeddocument_page];
	
	if (embeddocument_auto) {timer = setTimeout("embeddocument_ShowPage_<?php echo $ref ?>(embeddocument_page+1,true);",4000);} else {clearTimeout(timer);}
	
	document.getElementById('embeddocument_page_box').value=embeddocument_page;
	}


embeddocument_ShowPage_<?php echo $ref ?>(1);
</script>

</body></html>


