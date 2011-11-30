<?php
// not functional, this is a skeleton for annotated PDF printout


foreach ($_GET as $key => $value) {$$key = stripslashes(utf8_decode(trim($value)));}

// create new PDF document
include('../../../include/db.php');
include('../../../include/general.php');
include('../../../include/authenticate.php');
include('../../../include/search_functions.php');
include('../../../include/resource_functions.php');
include('../../../include/image_processing.php');

require_once('../../../lib/tcpdf/tcpdf.php');

# Still making variables manually when not using Prototype: 
$ref=getval("ref","");
$size=getval("size","");
$orientation=getval("orientation","");
if(getval("preview","")!=""){$preview=true;} else {$preview=false;}

$imgsize="pre";
$previewpage=getval("previewpage",1);

if ($preview==true){$imgsize="col";}
if ($size == "a4") {$width=210/25.4;$height=297/25.4;} // convert to inches
if ($size == "a3") {$width=297/25.4;$height=420/25.4;}

if ($size == "letter") {$width=8.5;$height=11;}
if ($size == "legal") {$width=8.5;$height=14;}
if ($size == "tabloid") {$width=11;$height=17;}

#configuring the sheet:
$pagewidth=$pagesize[0]=$width;
$pageheight=$pagesize[1]=$height;
$date= date("m-d-Y h:i a");

if ($orientation=="landscape"){$pagewidth=$pagesize[0]=$height; $pageheight=$pagesize[1]=$width;}

#Get data
$resourcedata= get_resource_data($ref);

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($userfullname);
$pdf->SetTitle($resourcedata['field'.$view_title_field].' '.$date);
$pdf->SetSubject($lang['annotations']);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// ---------------------------------------------------------

// add a page
$pdf->AddPage();

/// draw picture and annotations

#Make AJAX preview?:
	if ($preview==true && isset($imagemagick_path)) 
		{
		if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);}
		if (file_exists($storagedir."/tmp/annotate.jpg")){unlink($storagedir."/tmp/annotate.jpg");}
		if (file_exists($storagedir."/tmp/annotate.pdf")){unlink($storagedir."/tmp/annotate.pdf");}
		$pdf->Output($storagedir.'/tmp/annotate.pdf','F');
		# Set up  
		putenv("MAGICK_HOME=" . $imagemagick_path); 
		putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
		putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . $imagemagick_path . "/bin"); # Path 
		
		$command= get_ghostscript_command();
		$command.= " -sDEVICE=jpeg -dFirstPage=$previewpage -o -r100 -dLastPage=$previewpage -sOutputFile=\"".$storagedir."/tmp/annotaterip.jpg\" \"".$storagedir."/tmp/annotate.pdf\"";
		run_command($command);
		
		$command=$imagemagick_path . "/bin/convert";
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert.exe";}
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
		if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}	
		
		$command.= " -resize 300x300 -quality 90 -colorspace RGB \"".$storagedir."/tmp/annotaterip.jpg\" \"".$storagedir."/tmp/annotate.jpg\"";
		run_command($command);
		exit();
		}


$pdf->Output('test.pdf','D');

