<?php
#
# PDF Contact Sheet Functionality
# Contributed by Tom Gleason
#

include('fpdf/fpdf.php');
include('include/general.php');
include('include/db.php');
include('include/collections_functions.php');
include('include/resource_functions.php');
include('include/image_processing.php');

$collection=getval("c","");

#configuring the sheet:
$columns=5;
$pagewidth=$pagesize[0]="8.5";
$pageheight=$pagesize[1]="11";
$date= date("m-d-Y h:i a");

#calculating sizes of cells, images, and number of rows:
$cellsize=($pagewidth-1.7)/$columns;
$imagesize=$cellsize-0.3;
$rowsperpage=($pageheight-1-$cellsize)/$cellsize;

#Get data
$collectiondata= get_collection($collection);
$collectionresources= get_collection_resources($collection);
$test=array_reverse($collectionresources);
$user= get_user($collectiondata['user']);

#Start PDF, set metadata, etc.
$pdf=new FPDF("P","in",$pagesize);
$pdf->SetTitle($collectiondata['name']." ".$date);
$pdf->SetAuthor($user['fullname']." ".$user['email']);
$pdf->SetSubject($applicationname." Contact Sheet");
$keywords="";
$pdf->SetMargins(1,1,.7);
$pdf->SetAutoPageBreak(true,0);
$pdf->AddPage();

#Title on sheet
$pdf->SetFont('helvetica','',10);
$title = $applicationname." - ". $collectiondata['name']." - ".$date;
$pdf->Text(1,.6,$title,0,0,"L");$pdf->ln();

$pdf->SetFontSize(8);

#Begin loop through resources, collecting Keywords too.
$i=0;
$j=0;
foreach ($collectionresources as $resource)
{
    $i++;
		
		
		$resourcedata=get_resource_data($resource);

		# Try to find a suitable image to use.
		$resourcethumb=get_resource_path($resource,"scr",false,$resourcedata["preview_extension"]);
		if (!file_exists($resourcethumb)) {$resourcethumb=		$resourcethumb=get_resource_path($resource,"pre",false,$resourcedata["preview_extension"]);}

		if (file_exists($resourcethumb) && ($resourcedata["preview_extension"]=="jpg" || $resourcedata["preview_extension"]=="jpeg"))
		{
			
			$keywords.=$resourcedata['ref'].", ";	
			
			# Two ways to size image to cell, either by height or by width.
			$thumbsize=getimagesize($resourcethumb);
				if ($thumbsize[0]>$thumbsize[1]){
				
					$pdf->Text($pdf->Getx(),$pdf->Gety()-.05,$resourcedata['ref']);		
					$pdf->Cell($cellsize,$cellsize,$pdf->Image($resourcethumb,$pdf->GetX(),$pdf->GetY(),$imagesize,0,"jpg",$baseurl. "/?r=" . $resource),2,0);
				
				}
				
				else{
					
					$pdf->Text($pdf->Getx(),$pdf->Gety()-.05,$resourcedata['ref']);		
					$pdf->Cell($cellsize,$cellsize,$pdf->Image($resourcethumb,$pdf->GetX(),$pdf->GetY(),0,$imagesize,"jpg",$baseurl. "/?r=" . $resource),0,0);
					
				}
		
				if ($i == $columns){
				
					$pdf->ln(); $i=0;$j++;
						
						if ($j > $rowsperpage){
						
						$j=0; $pdf->AddPage();
						
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
		foreach ($test as $relation){
			sql_query("insert into resource_related(resource,related) values ($newresource,$relation)");
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