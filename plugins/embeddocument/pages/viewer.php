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
<body>

<div class="embeddocument_player">
<div class="embeddocument_preview" id="embeddocument_preview"> </div>

<ul class="embeddocument_controls_standard">	
<li class="embeddocument_begn" Style="cursor: pointer;" onClick="embeddocument_auto=false;embeddocument_ShowPage(1,false,false);"<span>|<</span></li>
<li class="embeddocument_prev" Style="cursor: pointer;" onClick="embeddocument_auto=false;embeddocument_ShowPage(embeddocument_page-1,false,false);"><span><</span></li>
<li class="embeddocument_auto" Style="cursor: pointer;" onClick="embeddocument_auto=!embeddocument_auto;if (embeddocument_auto) {embeddocument_ShowPage(embeddocument_page,false,false_<?php echo $ref ?>);} else {clearTimeout(timer);}"<span>||</span></li>
<li class="embeddocument_next" Style="cursor: pointer;" onClick="embeddocument_auto=false;embeddocument_ShowPage(embeddocument_page+1,false,false);"<span>></span></li>
<li class="embeddocument_end" Style="cursor: pointer;" onClick="embeddocument_auto=false;embeddocument_ShowPage(embeddocument_pages.length-1,false,false);"><span>>|</span></li>
<li class="embeddocument_jump" Style="cursor: pointer;" onClick="embeddocument_auto=false;embeddocument_ShowPage(document.getElementById('embeddocument_page_box').value,false,true);"><span>jump</span></li>
<li class="embeddocument_jump-box" <input type="text" id="embeddocument_page_box" size="1" /> / <span id="page-count">#</span> </li>

<?php if ($download)
	{ 
	$pdf_file_path=get_resource_path($ref,true,"",false,"pdf");
	if (file_exists($pdf_file_path))
		{
		$pdf_url_path=get_resource_path($ref,false,"",false,"pdf");
		?>
		<li class="embeddocument_download" Style="cursor: pointer;" onClick="top.location.href='<?php echo $pdf_url_path ?>';">download PDF</li>
		<?php
		}
	}
?>

</ul>



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
	
	# sets height and width to display 
	$ratio=$resource["thumb_width"]/$resource["thumb_height"];
	$width=getvalescaped("width","");
	$height=floor($width / $ratio);
	
	?>
	embeddocument_pages[<?php echo $page ?>]='<a href="#" onClick="embeddocument_ShowPage(<?php echo ($page + 1) ?>,false,false);"><img border="0" width=<?php echo $width ?> height=<?php echo $height ?> src="<?php echo $preview_path ?>"></a>';
	<?php

	$page++;
	}
?>

function embeddocument_ShowPage(page_set,from_auto,jump)
	{
	if (!embeddocument_auto && from_auto) {return false;} // Auto switched off but timer still running. Terminate.
	
	if (embeddocument_page==page_set && jump) {alert("<?php echo $lang["embeddocument_alreadyonpage"]?>");return false;}
	
	embeddocument_page=page_set;
	if (embeddocument_page>(embeddocument_pages.length-1)) {embeddocument_page=embeddocument_pages.length-1;} // back to first page
	if (embeddocument_page<1) {embeddocument_page=1;} // to last page
	
	document.getElementById("embeddocument_preview").innerHTML=embeddocument_pages[embeddocument_page];
	
	if (embeddocument_auto) {timer = setTimeout("embeddocument_ShowPage(embeddocument_page+1,true,false);",4000);} else {clearTimeout(timer);}
	
	document.getElementById('embeddocument_page_box').value=embeddocument_page;
	}


embeddocument_ShowPage(1,false,false);

// publishes total page count after forward slash next to actual page
function totalPages(){
	var pagecount=embeddocument_pages.length-1
	document.getElementById('page-count').innerHTML = pagecount;
}
totalPages();

</script>

</div>
</body></html>