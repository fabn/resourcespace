<?php


function get_annotation_file_path($ref,$getfilepath,$is_collection=false,$extension="pdf",$cleanup=false)
	{
	global $storageurl;
	global $storagedir;
	global $scramble_key;	
	
	if (!file_exists($storagedir . "/annotate")){mkdir($storagedir . "/annotate",0777);}
	
	# Fetching the file path? Add the full path to the file
	$filefolder="annotate/".$ref;
	if ($is_collection){$filefolder="annotate/col$ref";}
	if ($getfilepath)
	    {
	    $folder=$storagedir . "/" .$filefolder;
	    }
	else
	    {
	    global $storageurl;
	    $folder=$storageurl . "/" . $filefolder;
	    }
	if (!file_exists($folder)){mkdir($folder,0777);}
	
	if(!isset($_COOKIE['user'])){
		$scramble="restricted";
		}
	else{
		$cookieparts=explode("|",$_COOKIE['user']);
		// don't use actual session cookie:
		$scramble=substr(md5($ref.$scramble_key.$extension.$cookieparts[1]),2,10);
		}
	if (!$cleanup){$scramble=date("m-d-Y_H_i_s-").$scramble;} 
	$file=$folder."/".$scramble.".".$extension;
	
	return  $file;
	}

function create_annotated_pdf($ref,$is_collection=false,$size="letter",$cleanup=false,$preview=false){
	# function to create annotated pdf of resources or collections.
	# This leaves the pdfs and jpg previews in filestore/annotate so that they can be grabbed later.
	# $cleanup will result in a slightly different path that is not cleaned up afterwards.
	
	global $lang,$userfullname,$view_title_field,$baseurl,$imagemagick_path,$ghostscript_path,$previewpage,$storagedir,$annotate_font;
	$date= date("m-d-Y h:i a");
	
	include_once($storagedir.'/../include/search_functions.php');
	include_once($storagedir.'/../include/resource_functions.php');
	include_once($storagedir.'/../include/collections_functions.php');
	include_once($storagedir.'/../include/image_processing.php');
	include_once($storagedir.'/../lib/tcpdf/tcpdf.php');

	$pdfstoragepath=get_annotation_file_path($ref,true,$is_collection,"pdf",$cleanup);
	$jpgstoragepath=get_annotation_file_path($ref,true,$is_collection,"jpg",$cleanup);
	$pdfhttppath=get_annotation_file_path($ref,false,$is_collection,"pdf",$cleanup);
	$jpghttppath=get_annotation_file_path($ref,false,$is_collection,"jpg",$cleanup);
	
	class MYPDF extends TCPDF {
		
		public function MultiRow($left, $right) {
			
			$page_start = $this->getPage();
			$y_start = $this->GetY();
		
			// write the left cell
			$this->MultiCell(.5, 0, $left, 1, 'C', 1, 2, '', '', true, 0);
		
			$page_end_1 = $this->getPage();
			$y_end_1 = $this->GetY();
		
			$this->setPage($page_start);
		
			// write the right cell
			$right=str_replace("<br />","\n",$right);
			$this->MultiCell(0, 0, $right, 1, 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
		
			$page_end_2 = $this->getPage();
			$y_end_2 = $this->GetY();
		
			// set the new row position by case
			if (max($page_end_1,$page_end_2) == $page_start) {
				$ynew = max($y_end_1, $y_end_2);
			} elseif ($page_end_1 == $page_end_2) {
				$ynew = max($y_end_1, $y_end_2);
			} elseif ($page_end_1 > $page_end_2) {
				$ynew = $y_end_1;
			} else {
				$ynew = $y_end_2;
			}
			
			$this->setPage(max($page_end_1,$page_end_2));
			$this->SetXY($this->GetX(),$ynew);
		}

	}
	if ($is_collection){
		$collectiondata=get_collection($ref);$resources=get_collection_resources($ref);
	} 
	else { 
		$resourcedata= get_resource_data($ref);$resources=array($ref);
	}
	
	if ($size == "a4") {$width=210/25.4;$height=297/25.4;} // convert to inches
	if ($size == "a3") {$width=297/25.4;$height=420/25.4;}
	if ($size == "letter") {$width=8.5;$height=11;}
	if ($size == "legal") {$width=8.5;$height=14;}
	if ($size == "tabloid") {$width=11;$height=17;}

	#configuring the sheet:
	$pagewidth=$pagesize[0]=$width;
	$pageheight=$pagesize[1]=$height;
	
	$pdf = new MYPDF("portrait", "in", $size, true, 'UTF-8', false);
	$pdf->SetFont($annotate_font, '', 8);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($userfullname);
	if ($is_collection){ $pdf->SetTitle($collectiondata['name'].' '.$date);}
	else { $pdf->SetTitle($resourcedata['field'.$view_title_field].' '.$date);}
	$pdf->SetSubject($lang['annotations']);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->setMargins(.5,.5,.5);

	// add a page
	for ($n=0;$n<count($resources);$n++){
		$pdf->AddPage();
		
		$resourcedata= get_resource_data($resources[$n]);
		$ref=$resources[$n];
		$access=get_resource_access($resourcedata); // feed get_resource_access the resource array rather than the ref, since access is included.
		$use_watermark=check_use_watermark();

		$imgpath = get_resource_path($ref,true,"hpr",false,"jpg",-1,1,$use_watermark);
		if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"lpr",false,"jpg",-1,1,$use_watermark);}
		if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"scr",false,"jpg",-1,1,$use_watermark);}
		if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"",false,"jpg",-1,1,$use_watermark);}
		if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"pre",false,"jpg",-1,1,$use_watermark);}
		if (!file_exists($imgpath))continue;
		$imagesize=getimagesize($imgpath);
		
		$whratio=$imagesize[0]/$imagesize[1];
		$hwratio=$imagesize[1]/$imagesize[0];

		if ($whratio<1){
		$imageheight=$height-4; // vertical images can take up half the page
		$whratio=$imagesize[0]/$imagesize[1];
		$imagewidth=$imageheight*$whratio;}
		if ($whratio>=1 || $imagewidth>$width+1){
		$imagewidth=$width-1; // horizontal images are scaled to width - 1 in
		$hwratio=$imagesize[1]/$imagesize[0];
		$imageheight=$imagewidth*$hwratio;}
	
		$pdf->Text(.5,.5,$resourcedata['field'.$view_title_field].' '.$date);
		$pdf->Image($imgpath,((($width-1)/2)-($imagewidth-1)/2),1,$imagewidth,$imageheight,"jpg",$baseurl. '/?r=' . $ref);	

		// set color for background
		$pdf->SetFillColor(255, 255, 200);

		$notes=sql_query("select * from annotate_notes where ref='$ref'");
		$style= array('width' => 0.01, 'cap' => 'butt', 'join' => 'round' ,'dash' => '0', 'color' => array(100,100,100));
		$style1 = array('width' => 0.04, 'cap' => 'butt', 'join' => 'round', 'dash' => '0', 'color' => array(255, 255, 0));
		$style2 = array('width' => 0.02, 'cap' => 'butt', 'join' => 'round', 'dash' => '3', 'color' => array(255, 0, 0));
		$ypos=$imageheight+1.5;$pdf->SetY($ypos);
		$m=1;
		foreach ($notes as $note){
			$ratio=$imagewidth/$note['preview_width'];
			$note_y=$note['top_pos']*$ratio;
			$note_x=$note['left_pos']*$ratio;
			$note_width=$note['width']*$ratio;
			$note_height=$note['height']*$ratio;
			$pdf->SetLineStyle($style1);
			$pdf->Rect(((($width-1)/2)-($imagewidth-1)/2)+$note_x,$note_y+1,$note_width,$note_height);
			$pdf->Rect(((($width-1)/2)-($imagewidth-1)/2)+$note_x,$note_y+1,.1,.1,'DF',$style1,array(255,255,0));
			$ypos=$pdf->GetY();
			$pdf->Text(((($width-1)/2)-($imagewidth-1)/2)+$note_x-.01,$note_y+.99,$m,false,false,true,0,0,'L');
			//$pdf->SetLineStyle($style2);
			//$pdf->Rect(((($width-1)/2)-($imagewidth-1)/2)+$note_x,$note_y+1,$note_width,$note_height);
			$pdf->SetY($ypos);
			$note_user=get_user($note['user']);
			$pdf->SetLineStyle($style);
			$noteparts=explode(": ",$note['note'],2);
			$pdf->MultiRow($m,$noteparts[1]." - ".$note_user['fullname']);
			$ypos=$ypos+.5;$m++;
		}	
	}

	// reset pointer to the last page
	$pdf->lastPage();

	#Make AJAX preview?:
	if ($preview==true && isset($imagemagick_path)) 
		{
		if (file_exists($jpgstoragepath)){unlink($jpgstoragepath);}
		if (file_exists($pdfstoragepath)){unlink($pdfstoragepath);}
		echo ($pdf->GetPage()); // for paging
		$pdf->Output($pdfstoragepath,'F');
		# Set up  
		
		putenv("MAGICK_HOME=" . $imagemagick_path); 
		putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
		putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . $imagemagick_path . "/bin"); # Path 
		
		$command= get_ghostscript_command();
		$command.= " -sDEVICE=jpeg -dFirstPage=$previewpage -o -r100 -dLastPage=$previewpage -sOutputFile=\"".$jpgstoragepath."\" \"".$pdfstoragepath."\"";
		run_command($command);
		
		$command=$imagemagick_path . "/bin/convert";
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert.exe";}
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
		if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}	
		
		$command.= " -resize 300x300 -quality 90 -colorspace RGB \"".$jpgstoragepath."\" \"".$jpgstoragepath."\"";
		run_command($command);
		return true;
		}
		
	if (!$is_collection){
		$filename=$lang['annotations']."-".$resourcedata["field".$view_title_field];
	}
	else {
		$filename=$lang['annotations']."-".$collectiondata['name'];
	}
		
	if ($cleanup){
		// cleanup
		if (file_exists($pdfstoragepath)){unlink($pdfstoragepath);}
		if (file_exists($jpgstoragepath)){unlink($jpgstoragepath);}
		$pdf->Output($filename.".pdf",'D');
		}
	else {
		// in this case it's not cleaned up automatically, but rather left in place for later use of the path.
		
		$pdf->Output($pdfstoragepath,'F');
		echo $pdfhttppath;
	}
}
