<?php

# RGB/HSV functions found at http://www.actionscript.org/forums/archive/index.php3/t-50746.html
#
function RGB_TO_HSV ($R, $G, $B) // RGB Values:Number 0-255
{ // HSV Results:Number 0-1
$HSV = array();

$var_R = $R /255;
$var_G = $G /255;
$var_B = $B /255;

$var_Min = min($var_R, $var_G, $var_B);
$var_Max = max($var_R, $var_G, $var_B);
$del_Max = $var_Max - $var_Min;

$V = $var_Max;

if ($del_Max == 0)
{
$H = 0;
$S = 0;
}
else
{
$S = $del_Max / $var_Max;

$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
$del_G = ( ( ( $var_Max  - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
$del_B = ( ( ( $var_Max  - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

if ($var_R == $var_Max) $H = $del_B - $del_G;
else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

if ($H<0) $H++;
if ($H>1) $H--;
}

$HSV['H'] = $H;
$HSV['S'] = $S;
$HSV['V'] = $V;

return $HSV;
}

function HSV_TO_RGB ($H, $S, $V) // HSV Values:Number 0-1
{ // RGB Results:Number 0-255
$RGB = array();

if($S == 0)
{
$R = $G = $B = $V * 255;
}
else
{
$var_H = $H * 6;
$var_i = floor( $var_H );
$var_1 = $V * ( 1 - $S );
$var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) );
$var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) );

if ($var_i == 0) { $var_R = $V ; $var_G = $var_3 ; $var_B = $var_1 ; }
else if ($var_i == 1) { $var_R = $var_2 ; $var_G = $V ; $var_B = $var_1 ; }
else if ($var_i == 2) { $var_R = $var_1 ; $var_G = $V ; $var_B = $var_3 ; }
else if ($var_i == 3) { $var_R = $var_1 ; $var_G = $var_2 ; $var_B = $V ; }
else if ($var_i == 4) { $var_R = $var_3 ; $var_G = $var_1 ; $var_B = $V ; }
else { $var_R = $V ; $var_G = $var_1 ; $var_B = $var_2 ; }

$R = $var_R * 255;
$G = $var_G * 255;
$B = $var_B * 255;
}

$RGB['R'] = $R;
$RGB['G'] = $G;
$RGB['B'] = $B;

return $RGB;
}

# Next two functions found at:
# http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml

function html2rgb($color)
{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}

function rgb2html($r, $g=-1, $b=-1)
{
    if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

    $r = intval($r); $g = intval($g);
    $b = intval($b);

    $r = dechex($r<0?0:($r>255?255:$r));
    $g = dechex($g<0?0:($g>255?255:$g));
    $b = dechex($b<0?0:($b>255?255:$b));

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return '#'.$color;
}

# combined powers of all the above functions. It's not perfect but it makes CSS that is approximately correct.

function convert_html_color ($html){
	
	global $hue,$sat;
	$RGB=html2rgb($html); //convert html to rgb
	$HSV=RGB_TO_HSV($RGB[0],$RGB[1],$RGB[2]); // convert rgb to hsv, which are percentages
	$huechange=($hue+100)/200; //find the percentage change from the imagemagick command
	$newH=$HSV['H']+$huechange;
	if ($newH>1){$newH=$newH-1;}
	$newS=$HSV['S']*($sat/100);
	$newRGB=HSV_TO_RGB($newH,$newS,$HSV['V']);
	$newHTML=rgb2html($newRGB['R'],$newRGB['G'],$newRGB['B']);
	return $newHTML;

}


# The automatic generation of CSS isn't perfect, so this function is used for 
# the cases that need to be very accurate (backgrounds), 
# I mimick the real world method of getting the right color:

function get_bottom_color_from_image($imagepath){
	// get a sample color from the image, for matching CSS color
	
	$image=imagecreatefromgif($imagepath);
	$imageheight=imagesy($image);
	$colorindex=imagecolorat($image,1,$imageheight-1);
	$color=imagecolorsforindex($image,$colorindex);
	$color=rgb2html($color['red'],$color['green'],$color['blue']);
	return $color;
}
