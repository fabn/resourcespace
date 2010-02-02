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

// install default fonts folder and default font definitions

if(!is_dir($storagedir."/fonts")){
    mkdir($storagedir."/fonts",0777); 
	}
	
if(!is_dir($storagedir."/fonts/utils")){
    mkdir($storagedir."/fonts/utils",0777);
    }

 if(!file_exists($storagedir."/fonts/helvetica.php")){ 
    copy("../../lib/tcpdf/fonts/helvetica.php",$storagedir."/fonts/helvetica.php");
    }
    
 if(!file_exists($storagedir."/fonts/times.php")){ 
    copy("../../lib/tcpdf/fonts/times.php",$storagedir."/fonts/times.php");
    }
    
 if (!file_exists($storagedir."fonts/utils/ttf2ufm")){   
 copy("../../lib/tcpdf/fonts/utils/ttf2ufm",$storagedir."/fonts/utils/ttf2ufm"); chmod($storagedir."/fonts/utils/ttf2ufm",0777);}
if (!isset($subsetting)){$subsetting=false;}

# Still making variables manually when not using Prototype: 
$collection=getval("c","");
$size=getval("size","");
$column=getval("columns","");
$order_by=getval("orderby","relevance");
$sort=getval("sort","desc");
$orientation=getval("orientation","");
$sheetstyle=getval("sheetstyle","");
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
$leading=2;

# back compatibility  
if (isset($print_contact_title)){
	if ($print_contact_title && empty($config_sheetthumb_fields)){$config_sheetthumb_fields=array(8);}
}

if ($orientation=="landscape"){$pagewidth=$pagesize[0]=$height; $pageheight=$pagesize[1]=$width;}

if ($sheetstyle=="thumbnails")
{
$columns=$column;

	#calculating sizes of cells, images, and number of rows:
	$cellsize[0]=$cellsize[1]=($pagewidth-1.7)/$columns;
	$imagesize=$cellsize[0]-0.3;
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

$csf="";
for ($m=0;$m<count($config_sheetthumb_fields);$m++)
	{
	$csf[$m]['name']=sql_value("select name value from resource_type_field where ref='$config_sheetthumb_fields[$m]'","");
	}
	
$cslf="";
for ($m=0;$m<count($config_sheetlist_fields);$m++)
	{
	$cslf[$m]['name']=sql_value("select name value from resource_type_field where ref='$config_sheetlist_fields[$m]'","");
	}	

$user= get_user($collectiondata['user']);

# Start PDF, set metadata, etc.
# Store pdf code to be run later, so that we can optionally analyze which glyphs are needed.
$pdfcode="";
$characterset="0123456789-amp";
$pdfcode="
\$pdf = new TCPDF('P', 'in', \$pagesize, true, 'UTF-8', false); 
\$pdf->setPrintHeader(false);
\$pdf->setPrintFooter(false);
\$pdf->SetTitle(\$collectiondata['name'].' '.\$date);

\$pdf->SetAuthor(\$user['fullname'].' '.\$user['email']);
\$pdf->SetSubject(\$applicationname.' Contact Sheet');
\$pdf->SetMargins(1,1.2,.7);
\$pdf->SetAutoPageBreak(false,0);
\$pdf->SetCellPadding(0); 
\$pdf->AddPage(); 
";

#Title on sheet
$pdfcode.="\$pdf->SetFont(\$contact_sheet_font,'',\$titlefontsize);";

$title = $applicationname.' - '. $collectiondata['name'].' - '.$date;
$pdfcode.="\$title = \$applicationname.' - '. \$collectiondata['name'].' - '.\$date;";

$pagenumber = $page;
$pdfcode.="\$pagecount=\$page;";
$pdfcode.="\$pagenumber = '   $page of '.\$pagecount;";

# Whenever outputting text, add the text to the characterset string as well.
$characterset.=$title."of";
$pdfcode.="\$pdf->Text(1,.8,\$title.\$pagenumber,0,0,'L');\$pdf->ln();
\$pdf->SetFontSize(\$refnumberfontsize);";

#Begin loop through resources, collecting Keywords too.
$pdfcode.="\$i=0;";
$i=0;
$pdfcode.="\$j=0;";
$j=0;


for ($n=0;$n<count($result);$n++)			
		{
		$ref=$result[$n]["ref"];
		$pdfcode.="\$ref='".$ref."';";
		$preview_extension=$result[$n]["preview_extension"];
		$pdfcode.="\$preview_extension='".$preview_extension."';";
		$resourcetitle="";
    	$i++;
		$pdfcode.="\$i++;";

		if ($ref!==false)
			{
			# Find image
			$imgpath = get_resource_path($ref,true,$imgsize,false,$preview_extension);
			$pdfcode.="\$imgpath = '".$imgpath."';";
			
			if (!file_exists($imgpath)){
			$imgpath="../../gfx/".get_nopreview_icon($result[$n]['resource_type'],$result[$n]['file_extension'],false,true); 
			$pdfcode.="\$imgpath = '".$imgpath."';";
			    $preview_extension=explode(".",$imgpath);
				if(count($preview_extension)>1){
				$preview_extension=trim(strtolower($preview_extension[count($preview_extension)-1]));
				$pdfcode.="\$preview_extension='".$preview_extension."';";
				} 
			}	
			if (file_exists($imgpath))
			{
				# cells are used for measurement purposes only
				# Two ways to size image, either by height or by width.
				$thumbsize=getimagesize($imgpath);
					if ($thumbsize[0]>$thumbsize[1]){
					
					if ($sheetstyle=="thumbnails")
					{
						$pdfcode.="\$topy=\$pdf->Gety();";	$pdfcode.="\$topx=\$pdf->Getx();";	
						if ($config_sheetthumb_include_ref){$pdfcode.="\$pdf->Cell(\$imagesize,((\$refnumberfontsize+\$leading)/72),\$ref,0,2,'L',0,'',1);\n";}
						for($ff=0; $ff<count($config_sheetthumb_fields); $ff++){
							$pdfcode.="\$ff=".$ff.";";
							$value="";
							$pdfcode.="\$value='';";
							$value=str_replace("'","\'", $result[$n]['field'.$config_sheetthumb_fields[$ff]]);
							
							$plugin="../../plugins/value_filter_" . $csf[$ff]['name'] . ".php";
							if (file_exists($plugin)) {include $plugin;}
							
							$pdfcode.="\$value='".TidyList($value)."';";
							$characterset.=$value;
						    $pdfcode.="\$pdf->Cell(\$imagesize,((\$refnumberfontsize+\$leading)/72),\$value,0,2,'L',0,'',1);\n";
						}
						$pdfcode.="\$bottomy=\$pdf->Gety();";	$pdfcode.="\$bottomx=\$pdf->Getx();";
					}
					else if ($sheetstyle=="list")
					{
						$characterset.=$ref;
						$pdfcode.="\$pdf->Text(\$pdf->Getx()+\$imagesize+0.1,\$pdf->Gety()+0.2,\$ref);\n";	
						for($ff=0; $ff<count($config_sheetlist_fields); $ff++){
							$pdfcode.="\$ff=".$ff.";";
							$value="";
							$pdfcode.="\$value='';";
							$value=str_replace("'","\'", $result[$n]['field'.$config_sheetthumb_fields[$ff]]);
							
							$plugin="../../plugins/value_filter_" . $cslf[$ff]['name'] . ".php";
							if (file_exists($plugin)) {include $plugin;}
							
							$pdfcode.="\$value='".TidyList($value)."';";
							$characterset.=$value;
							$pdfcode.="\$pdf->Text(\$pdf->Getx()+\$imagesize+0.1,\$pdf->Gety()+(0.2*(\$ff+2)),\$value);\n";
						}		
					}
						$pdfcode.="\$pdf->Image(\$imgpath,\$pdf->GetX(),\$pdf->GetY()+.025,\$imagesize,0,\$preview_extension,\$baseurl. '/?r=' . \$ref);\n";
						if ($sheetstyle=="thumbnails"){$pdfcode.="\$pdf->Sety(\$topy);";$pdfcode.="\$pdf->Setx(\$topx);";
							$pdfcode.="\$pdf->Cell(\$cellsize[0],(\$bottomy-\$topy)+\$imagesize+.2,'',0,0);\n";
							}
						else {	
							$pdfcode.="\$pdf->Cell(\$cellsize[0],\$cellsize[1],'',0,0);\n";
						}
					}
					
					else{
						
					if ($sheetstyle=="thumbnails")
					{
						$pdfcode.="\$topy=\$pdf->Gety();";	$pdfcode.="\$topx=\$pdf->Getx();";	
						if ($config_sheetthumb_include_ref){$pdfcode.="\$pdf->Cell(\$imagesize,((\$refnumberfontsize+\$leading)/72),\$ref,0,2,'L',0,'',1);\n";}
						for($ff=0; $ff<count($config_sheetthumb_fields); $ff++){
							$pdfcode.="\$ff=".$ff.";";
							$value="";
							$pdfcode.="\$value='';";
							$value=str_replace("'","\'", $result[$n]['field'.$config_sheetthumb_fields[$ff]]);
							
							$plugin="../../plugins/value_filter_" . $csf[$ff]['name'] . ".php";
							if (file_exists($plugin)) {include $plugin;}
							
							$pdfcode.="\$value='".TidyList($value)."';";
							$characterset.=$value;
						    $pdfcode.="\$pdf->Cell(\$imagesize,((\$refnumberfontsize+\$leading)/72),\$value,0,2,'L',0,'',1);\n";
						}$pdfcode.="\$bottomy=\$pdf->Gety();";	$pdfcode.="\$bottomx=\$pdf->Getx();";
					}
					else if ($sheetstyle=="list")
					{
						$pdfcode.="\$pdf->Text(\$pdf->Getx()+\$imagesize+0.1,\$pdf->Gety()+0.2,\$ref);\n";			
						for($ff=0; $ff<count($config_sheetlist_fields); $ff++){
							$pdfcode.="\$ff=".$ff.";";
							$value="";
							$pdfcode.="\$value='';";
							$value=str_replace("'","\'", $result[$n]['field'.$config_sheetthumb_fields[$ff]]);
							
							$plugin="../../plugins/value_filter_" . $cslf[$ff]['name'] . ".php";
							if (file_exists($plugin)) {include $plugin;}
							
							$pdfcode.="\$value='".TidyList($value)."';";
							$characterset.=$value;
							$pdfcode.="\$pdf->Text(\$pdf->Getx()+\$imagesize+0.1,\$pdf->Gety()+(0.2*(\$ff+2)),\$value);\n";
						}			
					}
						$pdfcode.="\$pdf->Image(\$imgpath,\$pdf->GetX(),\$pdf->GetY()+.025,0,\$imagesize,\$preview_extension,\$baseurl. '/?r=' . \$ref);\n";
						if ($sheetstyle=="thumbnails"){$pdfcode.="\$pdf->Sety(\$topy);";$pdfcode.="\$pdf->Setx(\$topx);";
							$pdfcode.="\$pdf->Cell(\$cellsize[0],(\$bottomy-\$topy)+\$imagesize+.2,'',0,0);\n";
							}
						else {	
							$pdfcode.="\$pdf->Cell(\$cellsize[0],\$cellsize[1],'',0,0);\n";
						}
					}
			$n=$n++;
					if ($i == $columns){
					
						$pdfcode.="\$pdf->ln(); \$i=0;\$j++;";
						$i=0;$j++;	
							if ($j > $rowsperpage){
							$j=0; 
							
							
							if ($n<count($result)-1){ //avoid making an additional page if it will be empty							
								$pdfcode.="\$pdf->AddPage();";
								$page = $page+1;
								}
							
							
							if ($n<count($result)-1){// avoid adding header if this is the last page and the next would be empty
								#When moving to a new page, get current coordinates, place a new page header.
								$pdfcode.="\$pagestartx=\$pdf->GetX();\n";
								$pdfcode.="\$pagestarty=\$pdf->GetY();\n";
								$pdfcode.="\$pdf->SetFont(\$contact_sheet_font,'',\$titlefontsize);\n";
								$pagenumber = $page;
								$characterset.=$pagenumber;
								$pdfcode.="\$pdf->Text(1,.8,\$title.'   $pagenumber of '.\$pagecount,0,0,'L');\$pdf->ln();\n";
								#then restore the saved coordinates and fontsize to continue as usual.
								$pdfcode.="\$pdf->SetFontSize(\$refnumberfontsize);
								\$pdf->Setx(\$pagestartx);
								\$pdf->SetY(\$pagestarty);";
								}
							}			
					}
				}
			}
		}	

#Make AJAX preview?:
	if ($preview==true && isset($imagemagick_path)) 
		{
		if(!is_dir($storagedir."/tmp")){mkdir($storagedir."/tmp",0777);}
		if (file_exists($storagedir."/tmp/contactsheet.jpg")){unlink($storagedir."/tmp/contactsheet.jpg");}
		if (file_exists($storagedir."/tmp/contactsheet.pdf")){unlink($storagedir."/tmp/contactsheet.pdf");}
		$pdfcode.="\$pdf->Output(\$storagedir.'/tmp/contactsheet.pdf','F');"; 
		eval($pdfcode);
		echo $pagecount;// send the page count back for paging links, also the column count so that the page can be reset to one. 
		
		# Set up  
		putenv("MAGICK_HOME=" . $imagemagick_path); 
		putenv("DYLD_LIBRARY_PATH=" . $imagemagick_path . "/lib"); 
		putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path . ":" . $imagemagick_path . "/bin"); # Path 
		
		$command= $ghostscript_path. "/gs";
		if (!file_exists($command)) {$command= $ghostscript_path. "\gs.exe";}
		$command.= " -sDEVICE=jpeg -dFirstPage=$previewpage -r100 -dLastPage=$previewpage -sOutputFile=\"".$storagedir."/tmp/contactsheetrip.jpg\" \"".$storagedir."/tmp/contactsheet.pdf\"";
		shell_exec($command);
		
		$command=$imagemagick_path . "/bin/convert";
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert.exe";}
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
		if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}	
		
		$command.= " -resize 400x400 -quality 90 -colorspace RGB \"".$storagedir."/tmp/contactsheetrip.jpg\" \"".$storagedir."/tmp/contactsheet.jpg\"";
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
	
	$pdfcode.="\$pdf->Output(\$path,'F');";
	eval($pdfcode);
	#Create thumbnails and redirect browser to the new contact sheet resource
	create_previews($newresource,true,"pdf");
	redirect("pages/view.php?ref=" .$newresource);
	}

else

	#to browser and generate subsetted font
	{
	# if a ttf file is specified, use it, and optionally subset it.
	if (isset($ttf_file)){
		if ($subsetting){
		function utf8_to_unicode_code($utf8_string)
			{
			$expanded = iconv("UTF-8", "UTF-32", $utf8_string);
			return unpack("L*", $expanded);
			}
	
			$characters=utf8_to_unicode_code($characterset);
			$characters=array_unique($characters);
			
			#create a hashed name of the unique subsetted font
			# include font name in case the font changes
			
			$fonthash=strtoupper(str_replace(array(0,1,2,3,4,5,6,7,8,9),array("A","B","C","D","E","F","G","H","I","J"),substr(md5(implode(",",$characters)),0,6)))."+".str_replace(".ttf","",strtolower($ttf_file));
			}
		else 
			{
			# if not subsetting, fonthash is just the name of the font
			$fonthash=str_replace(".ttf","",$ttf_file);
			}
	
		$font=$storagedir."/fonts/".$fonthash.".ttf";

		if ($subsetting)
			#subsetting requires scripting fontforge to produce a custom font
			{
			$ff_script="import fontforge\r                               
uf=fontforge.open(\"$storagedir/fonts/".$ttf_file."\")\r                                                  
n=fontforge.font()\r"; 

			foreach ($characters as $character)
				{	
				$ff_script.="
uf.selection.select((\"unicode\",None),".$character.")\r  
uf.copy()\r   
n.createChar(".$character.")\r                       
n.selection.select((\"unicode\",None),".$character.")\r      
n.paste()\r";
				} 

			$ff_script.="
n.fontname=\"".$fonthash."\"\r                    
n.generate(\"".$font."\")\r ";

			$pyfile=$storagedir."/tmp/".$fonthash;
			if (!file_exists($fonthash)){
				$openedfile = fopen($pyfile, "w");	
				fwrite($openedfile,$ff_script);
			}	
		}
	
	# in case former installs had a font in the tcpdf directory, move it to filestore
	if (file_exists($storagedir."/../lib/tcpdf/fonts/".$ttf_file)){
		copy ($storagedir."/../lib/tcpdf/fonts/".$ttf_file,$storagedir."/fonts/".$ttf_file);
		}
	
	#check if the font is already made for TCPDF
	if (!file_exists($storagedir."/fonts/".$fonthash.".z")
		|| !file_exists($storagedir."/fonts/".$fonthash.".ctg.z")
		|| !file_exists($storagedir."/fonts/".$fonthash.".php")){
		#if subsetting, check for fontforge and the font needed
		if ($subsetting)
			{ 	
			if (!file_exists($fontforge_path."/fontforge")){die ("Fontforge not found at $fontforge_path/fontforge");}	
			if (!file_exists($storagedir."/fonts/".$ttf_file)){die ($ttf_file." not found at ".$storagedir."/fonts/".$ttf_file);}	
			shell_exec ("/usr/bin/fontforge -lang=py -script $pyfile");
			unlink ($pyfile);
			}	

		shell_exec($storagedir."/fonts/utils/ttf2ufm -a -F -G afeU $font");
		
		
		$str=implode("\n",file($storagedir."/fonts/".$fonthash.".ufm"));
		$fp=fopen($storagedir."/fonts/".$fonthash.".ufm","w");
		// use proper naming convention for subsetted font.
		$incorrectsubsetname=str_replace("+","-",$fonthash);
		$str=str_replace($incorrectsubsetname,$fonthash,$str);
		fwrite($fp,$str,strlen($str));

		include($storagedir."/../lib/tcpdf/fonts/utils/makefont.php");

		MakeFont($font,$storagedir."/fonts/".$fonthash.".ufm");
		unlink($storagedir."/fonts/".$fonthash.".ufm");
		}

	$pdfcode=str_replace("\$contact_sheet_font","'$fonthash'",$pdfcode);
	}

$pdfcode.="\$pdf->Output(\$collectiondata['name'].'.pdf','D');"; 
#die($pdfcode);
eval ($pdfcode);}

if ($subsetting){ 
				#remove subset font files so they don't accumulate
				unlink ($storagedir."/fonts/".$fonthash.".ttf");
				unlink ($storagedir."/fonts/".strtolower($fonthash).".z");
				unlink ($storagedir."/fonts/".strtolower($fonthash).".ctg.z");
				unlink ($storagedir."/fonts/".strtolower($fonthash).".php");
}
?>
