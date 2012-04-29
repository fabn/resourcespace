<?php
include '../../../include/db.php';
include '../../../include/authenticate.php'; 
include '../../../include/general.php';
include '../../../include/resource_functions.php';
include '../../../include/image_processing.php';

$ref=getvalescaped("ref","");

# Count pages in this file
$page=0;
while(true)
	{
	$page++;
	$size="";if ($page>1) {$size="scr";} # Use screen size for other pages.
	$target=get_resource_path($ref,true,$size,false,"jpg",-1,$page,false,"",false); 
	if (!file_exists($target)) {break;}
	}


# Split action
if (getval("method","")!="")
	{
	$ranges=getval("ranges","");
	$rs=explode(",",$ranges);

	# Original file path
	$file=get_resource_path($ref,true,"",true,"pdf");

	foreach ($rs as $r)
		{
		# For each range
		$s=explode(":",$r);
		$from=$s[0];
		$to=$s[1];

		if (getval("method","")=="alternativefile")
			{
			$aref=add_alternative_file($ref,$lang["pages"] . " " . $from . " - " . $to,"","","pdf");
			
			$copy_path=get_resource_path($ref,true,"",true,"pdf",-1,1,false,"",$aref);
			}
		else
			{
			# Create a new resource based upon the metadata/type of the current resource.
			$copy=copy_resource($ref);
				
			# Find out the path to the original file.
			$copy_path=get_resource_path($copy,true,"",true,"pdf");
			}		
			
		# Extract this one page to a new resource.
		$gscommand = get_ghostscript_command();
		$gscommand2 = $gscommand . " -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($copy_path) . "  -dFirstPage=" . $from . " -dLastPage=" . $to . " " . escapeshellarg($file);
		$output=run_command($gscommand2);


		if (getval("method","")=="alternativefile")
			{
			# Preview creation for alternative files (enabled via config)
			global $alternative_file_previews;
			if ($alternative_file_previews)
				{
				create_previews($ref,false,"pdf",false,false,$aref);
				}
			# Update size.
			sql_query("update resource_alt_files set file_size='" . filesize_unlimited($copy_path) . "' where ref='$aref'");
			}
		else
			{
			# Update the file extension
			sql_query("update resource set file_extension='pdf' where ref='$copy'");
			
			# Create preview for the page.
			create_previews($copy,false,"pdf");
			}
		}
	redirect("pages/view.php?ref=" . $ref);	
	}





include "../../../include/header.php";
?>

<div class="BasicsBox">
<h1><?php echo $lang["splitpdf"]?></h1>

<p><?php echo $lang["splitpdf_pleaseselectrange"]?></p>

<script>

function DrawRanges()
	{
	var ranges_html="";
	var ranges = document.getElementById("ranges").value;
	var rs=ranges.split(",");
	for (var n=0;n<rs.size();n++)
		{
		// for each range
		var range=rs[n].split(":");
		
		// draw some HTML for this range
		ranges_html += '<?php echo $lang["range"] ?> ' + (n+1) + ': <?php echo $lang["pages"] ?> <input onChange="UpdateRanges();return false;" type="text" size="8" id="range' + n + '_from" value="' + range[0] + '"> <?php echo $lang["to-page"]?> <input onChange="UpdateRanges();return false;" type="text" size="8" id="range' + n + '_to" value="' + range[1] + '">';
		// Remove page option for ranges > 1
		if (n>0) {ranges_html+='&nbsp;&nbsp;<a href="#" onClick="RemoveRange('+n+');return false;">&gt;&nbsp;<?php echo $lang["removerange"] ?></a>';}
		ranges_html+='<br/>';
		}

	document.getElementById('ranges_html').innerHTML=ranges_html;
	}

function AddRange()
	{
	document.getElementById("ranges").value+=",1:<?php echo $page ?>";
	DrawRanges();
	}

function RemoveRange(r)
	{
	var ranges = document.getElementById("ranges").value;
	var rs=ranges.split(",");
	var new_ranges="";
	for (var n=0;n<rs.size();n++)
		{
		if (n!=r)
			{
			if (new_ranges!="") {new_ranges+=",";}
			new_ranges+=rs[n];
			}
		}
	document.getElementById("ranges").value=new_ranges;
	DrawRanges();
	}

function UpdateRanges()
	{
	var ranges = document.getElementById("ranges").value;
	var rs=ranges.split(",");
	var new_ranges="";
	for (var n=0;n<rs.size();n++)
		{
		if (new_ranges!="") {new_ranges+=",";}
		
		var rfrom=parseInt(document.getElementById('range' + n + '_from').value);
		var rto=parseInt(document.getElementById('range' + n + '_to').value);		
		
		if (rfrom<1 || rfrom ><?php echo $page ?>) {alert('<?php echo $lang["outofrange"] ?>');DrawRanges();return false;}
		if (rto  <1 || rto   ><?php echo $page ?>) {alert('<?php echo $lang["outofrange"] ?>');DrawRanges();return false;}
		if (rto < rfrom) {alert('<?php echo $lang["invalidrange"] ?>');DrawRanges();return false;}
		
		new_ranges+=rfrom + ':' + rto;
		}
	document.getElementById("ranges").value=new_ranges;
	DrawRanges();
	
	}

</script>

<form method="post" action="pdf_split.php">
<input type="hidden" name="ref" value="<?php echo $ref ?>">
<input type="hidden" name="ranges" id="ranges" value="<?php echo getval("ranges","1:$page") ?>">
<div id="ranges_html">
</div>
<p>&gt;&nbsp;<a href="#" onclick="AddRange();return false;"><?php echo $lang["addrange"] ?></a></p>
<br />
<p>
<input type="radio" name="method" checked value="alternativefile"><?php echo $lang["splitpdf_createnewalternativefile"] ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="method" value="resource"><?php echo $lang["splitpdf_createnewresource"] ?>
</p>
<p><input type="submit" value="<?php echo $lang["splitpdf"] ?>"></p>
</form>








</div>
<?php
include "../../../include/footer.php";
?>
<script>
DrawRanges();
</script>

<?php



/*

	# Create multiple pages.
	for ($n=1;$n<=$pdf_pages;$n++)
		{
		# Set up target file
		$size="";if ($n>1) {$size="scr";} # Use screen size for other pages.
		$target=get_resource_path($ref,true,$size,false,"jpg",-1,$n,false,"",$alternative); 
		if (file_exists($target)) {unlink($target);}

		if ($dUseCIEColor){$dUseCIEColor=" -dUseCIEColor ";} else {$dUseCIEColor="";}
		$gscommand2 = $gscommand . " -dBATCH -r".$resolution." ".$dUseCIEColor." -dNOPAUSE -sDEVICE=jpeg -sOutputFile=" . escapeshellarg($target) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " -dEPSCrop " . escapeshellarg($file);
 		$output=run_command($gscommand2);

    	debug("PDF multi page preview: page $n, executing " . $gscommand2);

	
		# Set that this is the file to be used.
		if (file_exists($target) && $n==1)
			{
			$newfile=$target;
	    	debug("Page $n generated successfully");
			}
			
		# resize directly to the screen size (no other sizes needed)
		 if (file_exists($target)&& $n!=1)
			{
			$command2=$command . " " . $prefix . escapeshellarg($target) . "[0] -quality $imagemagick_quality -resize 850x850 " . escapeshellarg($target); 
			$output=run_command($command2);
				
			# Add a watermarked image too?
			global $watermark;
    			if (isset($watermark) && $alternative==-1)
    				{
				$path=get_resource_path($ref,true,$size,false,"",-1,$n,true,"",$alternative);
				if (file_exists($path)) {unlink($path);}
    				$watermarkreal=dirname(__FILE__). "/../" . $watermark;
    				
				$command2 = $command . " \"$target\"[0] $profile -quality $imagemagick_quality -resize 800x800 -tile " . escapeshellarg($watermarkreal) . " -draw \"rectangle 0,0 800,800\" " . escapeshellarg($path); 
					$output=run_command($command2);
				}
				
			}
		
		# Splitting of PDF files to multiple resources
		global $pdf_split_pages_to_resources;
		if (file_exists($target) && $pdf_split_pages_to_resources)
			{
			# Create a new resource based upon the metadata/type of the current resource.
			$copy=copy_resource($ref);
						
			# Find out the path to the original file.
			$copy_path=get_resource_path($copy,true,"",true,"pdf");
			
			# Extract this one page to a new resource.
			$gscommand2 = $gscommand . " -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($copy_path) . "  -dFirstPage=" . $n . " -dLastPage=" . $n . " " . escapeshellarg($file);
	 		$output=run_command($gscommand2);
 		
 			# Update the file extension
 			sql_query("update resource set file_extension='pdf' where ref='$copy'");
 		
 			# Create preview for the page.
 			$pdf_split_pages_to_resources=false; # So we don't get stuck in a loop creating split pages for the single page PDFs.
 			create_previews($copy,false,"pdf");
 			$pdf_split_pages_to_resources=true;
			}
			
*/