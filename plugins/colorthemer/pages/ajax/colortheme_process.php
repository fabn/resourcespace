<?php

include ('../../../../include/config.php');
include('../../../../include/db.php');
include('../../../../include/general.php');
include('../../../../include/authenticate.php');
include('../../include/colorfunctions.php');

putenv("MAGICK_HOME=" . $imagemagick_path); 
putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path);  

$convert_fullpath = get_utility_path("im-convert");
$composite_fullpath = get_utility_path("im-composite");

// There is only one slot now but future revision to manage multiple themes 
// may be able to use this as a starting point.
// the default colorthemer theme name "colorthemer_1" is based on this ref.
$ref=1;

$sat=getval("sat","100");
$rounded=getval("rounded",false);
$hue=getval("hue","0");
$style=getval("style","greyblu");
$generate=getval("generate",false);

// create a colorthemes folder if necessary
if(!is_dir($storagedir."/colorthemes")){
    mkdir($storagedir."/colorthemes",0777); 
	}

if(!is_dir($storagedir."/colorthemes/$ref")){
    mkdir($storagedir."/colorthemes/$ref",0777); 
}


if ($generate){ 
	# create all the files
	include ('colortheme_generate.php');
}
else { 
	#create preview only

	# also do title.gif
	switch($style){
		
		case "greyblu":
		$path= $storagedir."/../gfx/greyblu/titles/title.gif";
		break;
		
		case "whitegry":
		$path=$storagedir."/../gfx/whitegry/titles/title.gif";
		break;
	}

		# convert title colors
		$command = $convert_fullpath . " -modulate 100,$sat,$hue ".$path." ".$storagedir."/tmp/title.gif";
		run_command($command);

		# convert theme preview colors
		$command = $convert_fullpath . " -modulate 100,$sat,$hue  ".$storagedir."/../plugins/colorthemer/gfx/$style.png ".$storagedir."/tmp/colortheme.png";
		run_command($command);
		
		# add title onto preview
		$command = $composite_fullpath . " -geometry +25+17  ".$storagedir."/tmp/title.gif ".$storagedir."/tmp/colortheme.png ".$storagedir."/tmp/composite.png";
		run_command($command);
		
		# add home image onto preview
		$command = $composite_fullpath . " -geometry +26+108 ".$storagedir."/../".$homeanim_folder."/1.jpg ".$storagedir."/tmp/composite.png ".$storagedir."/tmp/composite.png";
		run_command($command);
		
		# resize
		$command = $convert_fullpath . " -resize x400 ".$storagedir."/tmp/composite.png ".$storagedir."/tmp/compositepreview.jpg";
		run_command($command);
}

?> 
