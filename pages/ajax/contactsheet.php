<?php
#
# PDF Contact Sheet Functionality
# Contributed by Tom Gleason
#
foreach ($_GET as $key => $value) {$$key = stripslashes(utf8_decode(trim($value)));}

// create new PDF document
include('../../include/db.php');
include('../../include/general.php');
include('../../include/authenticate.php');
include('../../include/search_functions.php');
include('../../include/resource_functions.php');
include('../../include/collections_functions.php');
include('../../include/image_processing.php');

require_once('../../lib/tcpdf/tcpdf.php');

# Still making variables manually when not using Prototype: 
$collection=getval("c","");
$size=getval("size","");
$column=getval("columns","");
$order_by=getval("orderby","relevance");
$sort=getval("sort","desc");
$orientation=getval("orientation","");
$sheetstyle=getval("sheetstyle","thumbnails");
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
if ($orientation=="landscape"){
$pagewidth=$pagesize[1]=$height ;
$pageheight=$pagesize[0]=$width;
}else{
$pagewidth=$pagesize[0]=$width;
$pageheight=$pagesize[1]=$height;
}
$date= date("Y-m-d H:i:s");
$leading=2;

# back compatibility  
if (isset($print_contact_title)){
	if ($print_contact_title && empty($config_sheetthumb_fields)){$config_sheetthumb_fields=array(8);}
}



if ($sheetstyle=="thumbnails")
{
$columns=$column;
	#calculating sizes of cells, images, and number of rows:
	$cellsize[0]=$cellsize[1]=($pagewidth-1.7)/$columns;
	$imagesize=$cellsize[0]-.3;
	# estimate rows per page based on config lines
	$extralines=(count($config_sheetthumb_fields)!=0)?count($config_sheetthumb_fields):0;
	if ($config_sheetthumb_include_ref){$extralines++;}
	$rowsperpage=($pageheight-1-($cellsize[1]+($extralines*(($refnumberfontsize+$leading)/72))))/($cellsize[1]+($extralines*(($refnumberfontsize+$leading)/72)));
	$page=1;
}
else if ($sheetstyle=="list")
{ 
	#calculating sizes of cells, images, and number of rows:
	$columns=1;
	$imagesize=1.0;
	$cellsize[0]=$pagewidth-1.7;
	$cellsize[1]=1.2;
	$rowsperpage=($pageheight-1.2-$cellsize[1])/$cellsize[1];
	$page=1;
}

#Get data
$collectiondata= get_collection($collection);
if (is_numeric($order_by)){ $order_by="field".$order_by;}
$result=do_search("!collection" . $collection,"",$order_by,0,-1,$sort);

if ($sheetstyle=="thumbnails"){
$csf="";
for ($m=0;$m<count($config_sheetthumb_fields);$m++)
	{
	$csf_data=sql_query("select name,value_filter from resource_type_field where ref='$config_sheetthumb_fields[$m]'");
	$csf[$m]['name']=$csf_data[0]['name'];
	$csf[$m]['value_filter']=$csf_data[0]['value_filter'];
	}
}
	
if ($sheetstyle=="list"){
$csf="";
for ($m=0;$m<count($config_sheetlist_fields);$m++)
	{
	$csf_data=sql_query("select name,value_filter from resource_type_field where ref='$config_sheetlist_fields[$m]'");
	$csf[$m]['name']=$csf_data[0]['name'];
	$csf[$m]['value_filter']=$csf_data[0]['value_filter'];
	}	
}

$user= get_user($collectiondata['user']);
if ($orientation=="landscape"){$orientation="L";}else{$orientation="P";}

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        global $contact_sheet_font,$titlefontsize,$applicationname,$collectiondata,$date,$subsetting,$lang;
        $this->SetFont($contact_sheet_font,'',$titlefontsize,'',$subsetting);
		$title = $applicationname.' - '. $collectiondata['name'].' - '.nicedate($date,true,true);
		$pagenumber=$this->getAliasNumPage(). " " . $lang["of"] . " " .$this->getAliasNbPages();
		$this->Text(1,.8,$title.'   '.$pagenumber);
    }

    // Page footer
	 public function Footer() {
		 // custom footer avoids linerule
     }
}



$pdf = new MYPDF($orientation , 'in', $pagesize, true, 'UTF-8', false); 
$pdf->SetTitle($collectiondata['name'].' - '.nicedate($date, true, true));
$pdf->SetAuthor($user['fullname'].' '.$user['email']);
$pdf->SetSubject($applicationname . " - " . $lang["contactsheet"]);
$pdf->SetMargins(1,1.2,.7);
$pdf->SetAutoPageBreak(false);
$pdf->SetCellPadding(0); 
$pdf->AddPage(); 
$pdf->SetFont($contact_sheet_font,'','','',$subsetting);


$pdf->ln();$pdf->ln();
$pdf->SetFontSize($refnumberfontsize);

#Begin loop through resources, collecting Keywords too.
$i=0;
$j=0;


for ($n=0;$n<count($result);$n++){
	$ref=$result[$n]["ref"];
	$preview_extension=$result[$n]["preview_extension"];
	$resourcetitle="";
    $i++;
	$currentx=$pdf->GetX();
	$currenty=$pdf->GetY();
	if ($ref!==false){
		# Find image
		# Load access level
		$access=get_resource_access($result[$n]); // feed get_resource_access the resource array rather than the ref, since access is included.
		$use_watermark=check_use_watermark();
		$imgpath = get_resource_path($ref,true,$imgsize,false,$preview_extension,-1,1,$use_watermark);
			$preview_extension="jpeg";
		if (!file_exists($imgpath)){
			$imgpath="../../gfx/".get_nopreview_icon($result[$n]['resource_type'],$result[$n]['file_extension'],false,true); 
			$preview_extension=explode(".",$imgpath);
			if(count($preview_extension)>1){
				$preview_extension=trim(strtolower($preview_extension[count($preview_extension)-1]));
			} 
		}	
		if (file_exists($imgpath)){
			# cells are used for measurement purposes only
			# Two ways to size image, either by height or by width.
			$thumbsize=getimagesize($imgpath);
			if ($thumbsize[0]>$thumbsize[1]){
				if ($sheetstyle=="thumbnails"){
					$topy=$pdf->GetY();	$topx=$pdf->GetX();	
					if ($config_sheetthumb_include_ref){
						$pdf->Cell($imagesize,(($refnumberfontsize+$leading)/72),$ref,0,2,'L',0,'',1);
					}
					for($ff=0; $ff<count($config_sheetthumb_fields); $ff++){
						$value="";
						$value=str_replace("'","\'", $result[$n]['field'.$config_sheetthumb_fields[$ff]]);
							
						$plugin="../../plugins/value_filter_" . $csf[$ff]['name'] . ".php";
						if ($csf[$ff]['value_filter']!=""){
							eval($csf[$ff]['value_filter']);
							}
						else if (file_exists($plugin)) {include $plugin;} 

							
						$value=TidyList($value);
						$pdf->Cell($imagesize,(($refnumberfontsize+$leading)/72),$value,0,2,'L',0,'',1);
					}
					$bottomy=$pdf->GetY();	
					$bottomx=$pdf->GetX();
				}
				else if ($sheetstyle=="list"){
					$pdf->SetXY($currentx,$currenty);
					$pdf->Text($pdf->GetX()+$imagesize+0.1,$pdf->GetY()+0.2,$ref);
					$pdf->SetXY($currentx,$currenty);	
					for($ff=0; $ff<count($config_sheetlist_fields); $ff++){
						$value="";
						$value=str_replace("'","\'", $result[$n]['field'.$config_sheetlist_fields[$ff]]);
							
						$plugin="../../plugins/value_filter_" . $csf[$ff]['name'] . ".php";
						if ($csf[$ff]['value_filter']!=""){
							eval($csf[$ff]['value_filter']);
						}
						else if (file_exists($plugin)) {include $plugin;}
						$value=TidyList($value);
						$pdf->Text($pdf->GetX()+$imagesize+0.1,$pdf->GetY()+(0.2*($ff+2)),$value);					
						
						$pdf->SetXY($currentx,$currenty);
					}		
				}
				$pdf->Image($imgpath,$pdf->GetX(),$pdf->GetY()+.025,$imagesize,0,$preview_extension,$baseurl. '/?r=' . $ref);
				if ($sheetstyle=="thumbnails"){
					$pdf->SetXY($topx,$topy);
					$pdf->Cell($cellsize[0],($bottomy-$topy)+$imagesize+.2,'',0,0);
				}
				else {	
					$pdf->Cell($cellsize[0],$cellsize[1],'',0,0);
				}
			}
					
			else{
				if ($sheetstyle=="thumbnails"){
					$topy=$pdf->GetY();	
					$topx=$pdf->GetX();	
					if ($config_sheetthumb_include_ref){
						$pdf->Cell($imagesize,(($refnumberfontsize+$leading)/72),$ref,0,2,'L',0,'',1);
					}
					for($ff=0; $ff<count($config_sheetthumb_fields); $ff++){
						$value="";
						$value=str_replace("'","\'", $result[$n]['field'.$config_sheetthumb_fields[$ff]]);
							
						$plugin="../../plugins/value_filter_" . $csf[$ff]['name'] . ".php";
						if ($csf[$ff]['value_filter']!=""){
							eval($csf[$ff]['value_filter']);
							}
						else if (file_exists($plugin)) {include $plugin;}
						$value=TidyList($value);

						$pdf->Cell($imagesize,(($refnumberfontsize+$leading)/72),$value,0,2,'L',0,'',1);
					}
					$bottomy=$pdf->GetY();
					$bottomx=$pdf->GetX();
				}
				else if ($sheetstyle=="list"){
					$pdf->SetXY($currentx,$currenty);
					$pdf->Text($pdf->GetX()+$imagesize+0.1,$pdf->GetY()+0.2,$ref);
					$pdf->SetXY($currentx,$currenty);		
					for($ff=0; $ff<count($config_sheetlist_fields); $ff++){
						$value="";
						$value=str_replace("'","\'", $result[$n]['field'.$config_sheetlist_fields[$ff]]);
							
						$plugin="../../plugins/value_filter_" . $csf[$ff]['name'] . ".php";
						if ($csf[$ff]['value_filter']!=""){
							eval($csf[$ff]['value_filter']);
							}
						else if (file_exists($plugin)) {include $plugin;}
						
						$value=TidyList($value);
						
						$pdf->Text($pdf->GetX()+$imagesize+0.1,$pdf->GetY()+(0.2*($ff+2)),$value);					
						
						$pdf->SetXY($currentx,$currenty);
					}			
				}
				$pdf->Image($imgpath,$pdf->GetX(),$pdf->GetY()+.025,0,$imagesize,$preview_extension,$baseurl. '/?r=' . $ref);
				if ($sheetstyle=="thumbnails"){
					$pdf->SetXY($topx,$topy);
					$pdf->Setx($topx);
					$pdf->Cell($cellsize[0],($bottomy-$topy)+$imagesize+.2,'',0,0);
				}
				else {	
					$pdf->Cell($cellsize[0],$cellsize[1],'',0,0);
					}
			}
			$n=$n++;
			if ($i == $columns){
					
				$pdf->ln();
				$i=0;$j++;	
				if ($j > $rowsperpage){
					$j=0; 
							
							
					if ($n<count($result)-1){ //avoid making an additional page if it will be empty							
						$pdf->AddPage();
						$pdf->SetX(1);$pdf->SetY(1.2);
					}
							
							

				}			
			}
		}
	}
}	

#Make AJAX preview?:
	if ($preview==true && isset($imagemagick_path)) 
		{
		# Since get_temp_dir() method does this check, omit: if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);}
		if (file_exists(get_temp_dir() . "/contactsheetrip.jpg")){unlink(get_temp_dir() . "/contactsheetrip.jpg");}
		if (file_exists(get_temp_dir() . "/contactsheet.jpg")){unlink(get_temp_dir() . "/contactsheet.jpg");}
		if (file_exists(get_temp_dir() . "/contactsheet.pdf")){unlink(get_temp_dir() . "/contactsheet.pdf");}
		echo ($pdf->GetPage());
		$pdf->Output(get_temp_dir() . "/contactsheet.pdf","F");
		
		# Set up  
		putenv("MAGICK_HOME=" . $imagemagick_path); 
		putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
		putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . $imagemagick_path . "/bin"); # Path 
		
		$command= $ghostscript_path. "/gs";
		if (!file_exists($command)) {$command= $ghostscript_path. "\gs.exe";}
		$command.= " -sDEVICE=jpeg -dFirstPage=$previewpage -o -r100 -dLastPage=$previewpage -sOutputFile=\"".get_temp_dir() . "/contactsheetrip.jpg\" \"".get_temp_dir() . "/contactsheet.pdf\"";
		shell_exec($command);
		
		$command=$imagemagick_path . "/bin/convert";
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert.exe";}
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
		if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}	
		
		$command.= " -resize ".$contact_sheet_preview_size." -quality 90 -colorspace RGB \"".get_temp_dir() . "/contactsheetrip.jpg\" \"".get_temp_dir() . "/contactsheet.jpg\"";
		shell_exec($command);
		exit();
		}

#check configs, decide whether PDF outputs to browser or to a new resource.
if ($contact_sheet_resource==true){
	$newresource=create_resource(1,0);

	update_field($newresource,8,$collectiondata['name']." ".$date);
	update_field($newresource,$filename_field,$newresource.".pdf");

#Relate all resources in collection to the new contact sheet resource
relate_to_collection($newresource,$collection);	

	#update file extension
	sql_query("update resource set file_extension='pdf' where ref='$newresource'");
	
	# Create the file in the new resource folder:
	$path=get_resource_path($newresource,true,"",true,"pdf");
	
	$pdf->Output($path,'F');

	#Create thumbnails and redirect browser to the new contact sheet resource
	create_previews($newresource,true,"pdf");
	redirect("pages/view.php?ref=" .$newresource);
	}

else{ 
	$out1 = ob_get_contents();
	if ($out1!=""){
	ob_end_clean();
	}
$pdf->Output($collectiondata['name'].".pdf","D");
}
