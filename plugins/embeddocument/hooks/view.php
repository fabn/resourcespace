<?php

function HookEmbeddocumentViewAfterresourceactions()
	{
	global $embeddocument_resourcetype,$resource,$resource_data,$ref,$baseurl,$lang,$scramble_key,$access;
	
	if ($resource["resource_type"]!=$embeddocument_resourcetype) {return false;} # Not the right type.

# filter out resources without previews 	
	$thumbwidth=$resource["thumb_width"];
	$thumbheight=$resource["thumb_height"];
if ($thumbwidth==0)	{return false;} # The resource has no preview.

# Resolve dimensions of documnent viewer		
# Set default viewer widths--subtract 2 pixels for border
	$portait=358; //Default portait width
	$landscape=478; //Default Landscape width

#sets width for either portrait or landscape
	$ratio=$thumbwidth/$thumbheight;
	if ($ratio>1) {$width=$landscape;} else {$width=$portait;} 
	$width_w_border=$width+2; //expands width to display border
	$height=floor($width / $ratio);
	$height+=40; // Enough space for controls

# Create key to allow docviewer to access resource files
	$key=md5($scramble_key . $ref);


# Create download code
	$embed="
	<div id=\"embeddocument_back_" . $ref . "\" style=\"display:none;position:absolute;top:0;left:0;width:100%;height:100%;min-height: 100%;background-color:#000;opacity: .5;filter: alpha(opacity=50);\"></div>
	<div id=\"embeddocument_minimise_" . $ref . "\" style=\"position:absolute;top:5px;left:20px;background-color:white;border:1px solid black;display:none;\"><a href=\"#\" onClick=\"
	var ed=document.getElementById('embeddocument_" . $ref . "');
	ed.width='" . $width . "';
	ed.style.position='relative';
	ed.style.top='0';
	ed.style.left='0';
	ed.src='" . $baseurl . "/plugins/embeddocument/pages/viewer.php?ref=$ref&key=$key&width=" . $width . "';
	document.getElementById('embeddocument_minimise_" . $ref . "').style.display='none';
	document.getElementById('embeddocument_maximise_" . $ref . "').style.display='block';	
	document.getElementById('embeddocument_back_" . $ref . "').style.display='none';
	\">" . $lang["minimise"] . "</a></div>
	<div id=\"embeddocument_maximise_" . $ref . "\" class=\"embeddocument_maximise\"><a href=\"#\" onClick=\"
	var ed=document.getElementById('embeddocument_" . $ref . "');
	ed.width='" . $width*2 . "';
	ed.height='" . $height*2 . "';
	ed.style.position='absolute';
	ed.style.top='20px';
	ed.style.left='20px';
	ed.src='" . $baseurl . "/plugins/embeddocument/pages/viewer.php?ref=$ref&key=$key&width=" . $width*2 . "';
	ed.style.zIndex=999;
	document.getElementById('embeddocument_minimise_" . $ref . "').style.display='block';
	document.getElementById('embeddocument_maximise_" . $ref . "').style.display='none';	
	document.getElementById('embeddocument_back_" . $ref . "').style.display='block';	
	\">" . $lang["maximise"] . "</a></div><iframe id=\"embeddocument_" . $ref . "\" Style=\"background-color:#fff;cursor: pointer;\" width=\"$width_w_border\" height=\"$height\" src=\"" . $baseurl . "/plugins/embeddocument/pages/viewer.php?ref=$ref&key=$key&width=$width\" frameborder=0 scrolling=no>Your browser does not support frames.</iframe>";

	# Compress embed HTML.
	$embed=str_replace("\n"," ",$embed);
	$embed=str_replace("\t"," ",$embed);
	while (strpos($embed,"  ")!==false) {$embed=str_replace("  "," ",$embed);}

# Create a download key and download variant of the embed code
	$downloadkey=md5($scramble_key . $ref . "download");
	$embed_download=str_replace("&key","&downloadkey=" . $downloadkey . "&key",$embed);
	?>


<a href="#" onClick="
if (document.getElementById('embeddocument').style.display=='block') {document.getElementById('embeddocument').style.display='none';} else {document.getElementById('embeddocument').style.display='block';}
if (document.getElementById('embeddocument2').style.display=='block') {document.getElementById('embeddocument2').style.display='none';} else {document.getElementById('embeddocument2').style.display='block';}
 return false;">&gt;&nbsp;<?php echo $lang["embed"]?></a>
<p id="embeddocument2" style="display:none;padding:10px 0 3px 0;"><?php echo $lang["embeddocument_help"] ?><br/>
	<br/>
	
<input type="checkbox" onClick="
if (this.checked)
	{
	document.getElementById('embeddocument').style.display='none';
	document.getElementById('embeddocument_download').style.display='block';
	}
else
	{
	document.getElementById('embeddocument').style.display='block';	
	document.getElementById('embeddocument_download').style.display='none';
	}
"><?php echo $lang["embeddocument_allow_original_download"] ?></p>

<textarea id="embeddocument" style="width:335px;height:120px;display:none;"><?php echo htmlspecialchars($embed); ?></textarea>
<textarea id="embeddocument_download" style="width:335px;height:120px;display:none;"><?php echo htmlspecialchars($embed_download); ?></textarea>

</div>

<div>


	<?php
	return true;
	}

?>
