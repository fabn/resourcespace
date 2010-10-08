<?php
# FLV player - plays the FLV file created to preview video resources.

if (file_exists(get_resource_path($ref,true,"",false,"swf")))
	{
	$swfpath=get_resource_path($ref,false,"",false,"swf",-1,1,false,date("now"),-1,true);
	}

# The default is a neutral grey which should be acceptable for most user generated themes.
$theme=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu");
$colour="505050";
if ($theme=="greyblu") {$colour="446693";}

?>

<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0">
<PARAM NAME=movie VALUE="<?php echo $swfpath?>"> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#<?php echo $colour?>> <EMBED src="<?php echo $swfpath?>" quality=high WIDTH="400" HEIGHT="300" NAME="<?php echo basename($swfpath)?>" ALIGN="left" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED> </OBJECT> 
