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
$width=400;
$height=300;
if ($pagename=="search"){$width=355;$height=267;}
?>
<div class="Picture">
<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" >
<PARAM NAME=movie VALUE="<?php echo $swfpath?>"><PARAM NAME=quality VALUE=high><PARAM NAME=bgcolor VALUE=#<?php echo $colour?>> <EMBED src="<?php echo $swfpath?>" <?php if ($pagename=="search"){?>play=false<?php }?> menu=true quality=high WIDTH="<?php echo $width?>" HEIGHT="<?php echo $height?>" NAME="<?php echo basename($swfpath)?>" ALIGN="left" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED> </OBJECT> 
</div>
