<?

# Time list filter.
# Produce a video time list based on the value that has been set.

$new="<table width=\"100%\" cellpadding=10 cellspacing=0 class=\"infotable\">";
#$new.="<tr><td width=10%>&nbsp;</td><td></td><th align=right width=10% nowrap>HH:MM:SS:FF - HH:MM:SS:FF</th>";
#$new.="</tr>";
			
# Windows/UNIX safe split of value into lines
$value=str_replace("\r","\n",$value);
$value=str_replace("\n\n","\n",$value);
$vs=explode("\n",$value);

for ($m=0;$m<count($vs);$m++)
	{
	$line=trim($vs[$m]);
	if ($line!="")
		{
		$ls=explode(";",$line);
		if (count($ls)==3)
			{
			$new.="<tr><td width=10%><img src=\"timelist_outimage.php?ref=" . urlencode($ref) . "&timecode=" . urlencode($ls[0]) . "\"></td><td>" . $ls[2] . "</td><th align=right width=10% nowrap>" . $ls[0] . " - " . $ls[1] . "</th>";
			$new.="<td><a target=\"collections\" href=\"collections.php?addtimelist=" . urlencode($ls[0]) . "&addtimelistresource=" . $ref . "&nc=" . time() . "&addtimelistdescription=" . urlencode($ls[2]) . "\">&gt;&nbsp;" . $lang["action-addtocollection"] . "</a></td>";
			$new.="</tr>";
			}
		
		}
	}

$new.="</table>";


$value=$new;
?>