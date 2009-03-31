<?php

include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";

$width=700;
$height=350;

$graph = imagecreatetruecolor($width,$height);

if (function_exists("imageantialias")) {imageantialias($graph,true);}

# Read values
$activity_type=getvalescaped("activity_type","Search");
$year=getvalescaped("year",2006);
$month=getvalescaped("month","");

# Group handling
$groupselect=getvalescaped("groupselect","");
$groups=getvalescaped("groups","");$groups=explode("_",$groups);
if ($groupselect=="select") {$sql="and usergroup in (" . join(",",$groups) . ")";} else {$sql="";}

# Monthly graph? Add month filter
if ($month!="")
	{
	$sql.=" and month='$month'";
	}


# Fetch results
$results=sql_query("select year,month,day,sum(count) c from daily_stat where activity_type='$activity_type' and year='$year' $sql group by year,month,day;");

$days=array();
$max=-1;
$minx=-1;
$maxx=0;
$margin=120; # Vertical margin (space below graph)
$font="../gfx/fonts/vera.ttf";
$total=0;
$xoffset=60; # Position of Y axis (space to left of graph)
$black=imagecolorallocate($graph,0,0,0);

for ($n=0;$n<count($results);$n++)
	{
	$date=getdate(mktime(0,0,0,$results[$n]["month"],$results[$n]["day"],$results[$n]["year"]));
	if ($month=="")
		{
		# Yearly graph. X axis is day of year.
		$day=$date["yday"];
		}
	else
		{
		# Monthly graph. X axis is day of month.
		$day=$date["mday"]-1;
		}

	if (($day<$minx) || ($minx==-1)) {$minx=$day;}
	if ($day>$maxx) {$maxx=$day;}
	
	$days[$day]=$results[$n]["c"];
	$total+=$results[$n]["c"];
	if ($results[$n]["c"]>$max) {$max=$results[$n]["c"];}
	}

# Fix the range for now...
if ($month=="")
	{
	# Yearly graph
	$minx=0;
	$maxx=365;
	$rightmargin=0;
	}
else
	{
	# Monthly graph. Work out number of days in this month
	$minx=1;
	$maxx=date("t",mktime(0,0,0,$month,1,$year));
	$rightmargin=10;
	}


#white background
$col=imagecolorallocate($graph,255,255,255);
imagefilledrectangle($graph,0,0,$width,$height,$col);


# Draw left hand axis

# Calculate units to use.
$units=max(pow(10,strlen(floor($max/2))-1),1);

# Draw horizontal lines / axis text
$col=imagecolorallocate($graph,230,230,230);
for ($n=0;$n<$max;$n+=$units)
	{
	$y=($height-$margin)-(floor((($height-$margin)/($max*1)) * $n));
	imageline($graph,$xoffset,floor($y),$width-$rightmargin,floor($y),($n==0)?$black:$col);

	$bbox=imagettfbbox(9,0,$font,$n); # Get bounding box to right-align text.
	imagettftext($graph,9,0,$xoffset-$bbox[2]-$bbox[0]-5,$y+5,$black,$font,$n);
	}



# Vertical lines
$col=imagecolorallocate($graph,230,230,230);
if ($month=="")
	{
	# Yearly graph. 12 lines.
	for ($n=0;$n<=12;$n++)
		{
		$x=$xoffset+($n/12*($width-$xoffset));
		imageline($graph,$x,0,$x,$height-$margin+16,($n==0)?$black:$col);
		}
	}
else
	# Monthly graph. Max-X lines.
	for ($n=$minx;$n<=$maxx;$n++)
		{
		$factor=($n-$minx)/($maxx-$minx);
		$x=$xoffset+($factor*($width-$xoffset-$rightmargin));
		imageline($graph,$x,0,$x,$height-$margin,($n==$minx)?$black:$col);
		}



	
# Plot graph
$oldx=-1;$oldy=-1;$lastplot=0;
$thisyear=date("Y");$thisday=date("z");
for ($n=$minx;($n<=$maxx && (   ($year<$thisyear) || ($n<=$thisday)  ));$n++)
	{
	if (array_key_exists($n,$days)) {$val=$days[$n];} else {$val=0;}
	
	$x=$xoffset + floor((($width-$xoffset-$rightmargin)/($maxx-$minx))*($n-$minx));
	$y=($height-$margin)-(floor((($height-$margin)/($max*1)) * $val));
		
	if ($oldx!=-1)
		{
		$col=imagecolorallocate($graph,255,0,0);
		imageline($graph,$oldx,$oldy,$x,$y,$col);
		}

	$oldx=$x;$oldy=$y;
	}

# Mark end of line
if ($year==$thisyear) {imagefilledellipse ($graph,$oldx,$oldy,4,4,$col);}

# Add text
$col=imagecolorallocate($graph,0,0,0);
$text=$lang["stat-" . strtolower(str_replace(" ","",$activity_type))] . " " . $lang["summary"] . ", " . ($month==""?"":$lang["months"][$month-1] . " ") . $year . "\n\n";
if ($max!=-1) {$text.=$lang["mostinaday"] . " = " . number_format($max) . "\n" . ($month==""?$lang["totalfortheyear"]:$lang["totalforthemonth"]) . " = " . number_format($total) . "\n" . $lang["dailyaverage"] . " = " . number_format(round($total/count($results),1));}
else {$text=$lang["nodata"];}

imagettftext($graph,9,0,5,$height-$margin+40,$col,$font,$text);
#imagettftext($graph,9,0,5,12,$col,$font,$lang["max"] . "=" . number_format($max));


if ($month=="")
	{
	# Annual graph, draw months
	for ($n=1;$n<=12;$n++)
		{
		$x=$xoffset+(($n-1)/12*($width-$xoffset))+14;
		$text=substr($lang["months"][$n-1],0,3);
		imagettftext($graph,9,0,$x,$height-$margin+14,$col,$font,$text);
		}
	}
else
	{
	# Monthly graph, draw days
	for ($n=$minx;$n<=$maxx;$n++)
		{
		$factor=($n-$minx)/($maxx-$minx);
		$x=$xoffset+($factor*($width-$xoffset-$rightmargin))-2+(2-strlen($n)*3);
		imagettftext($graph,9,0,$x,$height-$margin+14,$col,$font,$n);
		}
	}
	

header("Content-type: image/png");
imagepng($graph);
?>