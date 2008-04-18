<?php
#
# PDF Contact Sheet Functionality
# Contributed by Tom Gleason
#

include('fpdf/fpdf.php');
include('include/general.php');
include('include/db.php');
include('include/search_functions.php');
include('include/resource_functions.php');
include('include/collections_functions.php');
include('include/image_processing.php');

$collection=getval("c","");

#configuring the sheet:
$columns=4;
$pagewidth=$pagesize[0]="8.5";
$pageheight=$pagesize[1]="11";
$date= date("m-d-Y h:i a");
$titlefontsize=10;
$refnumberfontsize=8;

#calculating sizes of cells, images, and number of rows:
$cellsize=($pagewidth-1.7)/$columns;
$imagesize=$cellsize-0.3;
$rowsperpage=($pageheight-1.2-$cellsize)/$cellsize;
$page=1;

#Get data
$collectiondata= get_collection($collection);
$result=do_search("!collection" . $collection);

$user= get_user($collectiondata['user']);

#Start PDF, set metadata, etc.
$pdf=new FPDF("P","in",$pagesize);
$pdf->SetTitle($collectiondata['name']." ".$date);
$pdf->SetAuthor($user['fullname']." ".$user['email']);
$pdf->SetSubject($applicationname." Contact Sheet");
$keywords="";
$pdf->SetMargins(1,1.2,.7);
$pdf->SetAutoPageBreak(true,0);
$pdf->AddPage();

#Title on sheet
$pdf->SetFont('helvetica','',$titlefontsize);
$title = $applicationname." - ". $collectiondata['name']." - ".$date;
$pagenumber = " - p.". $page;
$pdf->Text(1,.8,utf8_decode($title.$pagenumber),0,0,"L");$pdf->ln();

$pdf->SetFontSize($refnumberfontsize);

#Begin loop through resources, collecting Keywords too.
$i=0;
$j=0;


for ($n=0;$n<count($result);$n++)			
		{
		$ref=$result[$n]["ref"];
		$preview_extension=$result[$n]["preview_extension"];
    	$i++;

		if ($ref!==false)
			{
			# Find image
			$imgpath = get_resource_path($ref,"pre",false,$preview_extension);
			
			if (!file_exists(myrealpath($imgpath)))
				$imgpath=get_resource_path($ref,"thm",false,$preview_extension);
	
			if (file_exists($imgpath) && ($preview_extension=="jpg" || $preview_extension=="jpeg"))
			{
				
				$keywords.=$ref.", ";	
				# Two ways to size image to cell, either by height or by width.
				$thumbsize=getimagesize($imgpath);
					if ($thumbsize[0]>$thumbsize[1]){
					
						$pdf->Text($pdf->Getx(),$pdf->Gety()-.05,$ref);		
						$pdf->Cell($cellsize,$cellsize,$pdf->Image($imgpath,$pdf->GetX(),$pdf->GetY(),$imagesize,0,"jpg",$baseurl. "/?r=" . $ref),2,0);
					
					}
					
					else{
						
						$pdf->Text($pdf->Getx(),$pdf->Gety()-.05,$ref);	
						$pdf->Cell($cellsize,$cellsize,$pdf->Image($imgpath,$pdf->GetX(),$pdf->GetY(),0,$imagesize,"jpg",$baseurl. "/?r=" . $ref),0,0);
						
					}
			$n=$n++;
					if ($i == $columns){
					
						$pdf->ln(); $i=0;$j++;
							
							if ($j > $rowsperpage){
						    $page = $page+1;
							$j=0; $pdf->AddPage();
							
							#When moving to a new page, get current coordinates, place a new page header.
							$pagestartx=$pdf->GetX();
							$pagestarty=$pdf->GetY();
							$pdf->SetFont('helvetica','',$titlefontsize);
							$pagenumber = " - p.". $page;
							$pdf->Text(1,.8,utf8_decode($title.$pagenumber),0,0,"L");$pdf->ln();
							#then restore the saved coordinates and fontsize to continue as usual.
							$pdf->SetFontSize($refnumberfontsize);
							$pdf->Setx($pagestartx);
							$pdf->SetY($pagestarty);
							
							}			
					}
				}
			}
		}	
#Add Resource Numbers to PDF Metadata - I don't know what the use of it is but why not.	
$pdf->SetKeywords($keywords);

#check configs, decide whether PDF outputs to browser or to a new resource.
if ($contact_sheet_resource==true){
	$newresource=create_resource(1,0);

	update_field($newresource,8,$collectiondata['name']." ".$date);
	update_field($newresource,$filename_field,$newresource.".pdf");

		#Relate resources in collection to new resource
for ($n=0;$n<count($result);$n++)			
		{
		$ref=$result[$n]["ref"];
			sql_query("insert into resource_related(resource,related) values ($newresource,$ref)");
		}

	#update file extension
	sql_query("update resource set file_extension='pdf' where ref='$newresource'");
	
	# Create the file in the new resource folder:
	$path=get_resource_path($newresource,"",true,"pdf");
	$pdf->Output($path,"F");
	
	#Create thumbnails and redirect browser to the new contact sheet resource
	create_previews($newresource,true,"pdf");
	header('Location: index.php?r='.$newresource);
	}

else

	#to browser
	{$pdf->Output($collectiondata['name'].".pdf","I");}


?>