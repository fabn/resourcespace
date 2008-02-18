<?

include "include/db.php";
include "include/authenticate.php";
include "include/general.php";

$width=600;
$height=250;

$graph = imagecreatetruecolor($width,$height);

if (function_exists("imageantialias")) {imageantialias($graph,true);}


$activity_type=getvalescaped("activity_type","Search");
$year=getvalescaped("year",2006);

$results=sql_query("select year,month,day,sum(count) c from daily_stat where activity_type='$activity_type' and year='$year' group by year,month,day;");

$days=array();
$max=-1;
$minx=-1;
$maxx=0;
$margin=120;
$font="gfx/fonts/vera.ttf";
$total=0;

for ($n=0;$n<count($results);$n++)
	{
	$date=getdate(mktime(0,0,0,$results[$n]["month"],$results[$n]["day"],$results[$n]["year"]));
	$day=$date["yday"];

	if (($day<$minx) || ($minx==-1)) {$minx=$day;}
	if ($day>$maxx) {$maxx=$day;}
	
	$days[$day]=$results[$n]["c"];
	$total+=$results[$n]["c"];
	if ($results[$n]["c"]>$max) {$max=$results[$n]["c"];}
	}

# Fix the range for now...
$minx=0;
$maxx=365;

#white background
$col=imagecolorallocate($graph,255,255,255);
imagefilledrectangle($graph,0,0,$width,$height,$col);

# Vertical lines
$col=imagecolorallocate($graph,230,230,230);
for ($n=1;$n<=12;$n++)
	{
	$x=($n/12*$width);
	imageline($graph,$x,0,$x,$height-$margin+16,$col);
	}

# Draw horizontal lines
$col=imagecolorallocate($graph,230,230,230);
for ($y=0;$y<=($height-$margin);$y+=($height-$margin)/10)
	{
	if ($y==($height-$margin)) {$col=imagecolorallocate($graph,0,0,0);}
	imageline($graph,0,floor($y),$width,floor($y),$col);
	}

	
# Plot graph
$oldx=-1;$oldy=-1;$lastplot=0;
$thisyear=date("Y");$thisday=date("z");
for ($n=$minx;($n<=$maxx && (   ($year<$thisyear) || ($n<=$thisday)  ));$n++)
	{
	if (array_key_exists($n,$days)) {$val=$days[$n];} else {$val=0;}
	
	$x=floor((($width)/($maxx-$minx))*($n-$minx));
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
$text=$lang["stat-" . strtolower(str_replace(" ","",$activity_type))] . " " . $lang["summary"] . ", " . $year . "\n\n";
if ($max!=-1) {$text.=$lang["mostinaday"] . " = " . number_format($max) . "\n" . $lang["totalfortheyear"] . " = " . number_format($total) . "\n" . $lang["dailyaverage"] . " = " . number_format(round($total/count($results),1));}
else {$text=$lang["nodata"];}

imagettftext($graph,9,0,5,$height-$margin+46,$col,$font,$text);
imagettftext($graph,9,0,5,12,$col,$font,$lang["max"] . "=" . number_format($max));


# Draw months
for ($n=1;$n<=12;$n++)
	{
	$x=(($n-1)/12*$width)+14;
	$text=substr($lang["months"][$n-1],0,3);
	imagettftext($graph,9,0,$x,$height-$margin+14,$col,$font,$text);
	}

header("Content-type: image/png");
imagepng($graph);
?>