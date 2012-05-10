<?php

function HookEmbeddocumentViewAfterresourceactions()
	{
	global $embeddocument_resourcetype,$resource,$ref,$baseurl,$lang,$scramble_key,$access;
	
	if ($resource["resource_type"]!=$embeddocument_resourcetype) {return false;} # Not the right type.
	
		# Work out height and width
	$ratio=$resource["thumb_width"]/$resource["thumb_height"];
	$width=480;
	$height=floor($width / $ratio);
	$height+=30; // Enough space for controls
	
	$key=md5($scramble_key . $ref);
	
	
	
	$embed="<iframe width=\"$width\" height=\"$height\" src=\"" . $baseurl . "/plugins/embeddocument/pages/viewer.php?ref=$ref&key=$key&width=$width\" frameborder=0 scrolling=no>Your browser does not support frames.</iframe>";

	# Create a download key and download variant of the embed code
	$downloadkey=md5($scramble_key . $ref . "download");
	$embed_download=str_replace("&key","&downloadkey=" . $downloadkey . "&key",$embed);
	?>


<a href="#" onClick="
if (document.getElementById('embeddocument').style.display=='block') {document.getElementById('embeddocument').style.display='none';} else {document.getElementById('embeddocument').style.display='block';}
if (document.getElementById('embeddocument2').style.display=='block') {document.getElementById('embeddocument2').style.display='none';} else {document.getElementById('embeddocument2').style.display='block';}
 return false;">&gt;&nbsp;<?php echo $lang["embed"]?></a>
<p id="embeddocument2" style="display:none;padding:10px 0 3px 0;"><?php echo $lang["embeddocument_help"] ?><br/><br/>
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
"><?php echo $lang["allow_original_download"] ?></p>

<textarea id="embeddocument" style="width:335px;height:120px;display:none;"><?php echo htmlspecialchars($embed); ?></textarea>
<textarea id="embeddocument_download" style="width:335px;height:120px;display:none;"><?php echo htmlspecialchars($embed_download); ?></textarea>

</div>

<div>


	<?php
		
	return true;
	}
	
?>
