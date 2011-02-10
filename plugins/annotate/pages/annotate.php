<?php

include_once "../../../include/db.php";
include_once "../../../include/authenticate.php";
include_once "../../../include/general.php";
include_once "../../../include/resource_functions.php";
include_once "../../../include/image_processing.php";
include_once "../include/config.default.php";


// verify that the requested ResourceID is numeric.
$ref = $_REQUEST['ref'];
if (!is_numeric($ref)){ echo "Error: non numeric ref."; exit; }
# Fetch search details (for next/back browsing and forwarding of search params)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);
$errors=array(); # The results of the save operation (e.g. required field messages)

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

# Load download access level
$access=get_resource_access($ref);

// if they can't download this resource, they shouldn't be doing this
if ($access!=0){
	include "../../../include/header.php";
	echo "Permission denied.";
	include "../../../include/footer.php";
	exit;
}

# Load edit access level
$edit_access=get_edit_access($ref);
// get resource info
$resource = get_resource_data($ref);
// retrieve path to image and figure out size we're using
if ($resource["has_image"]==1)
        {
        	$imageurl=get_resource_path($ref,false,"scr",false,"jpg",-1,1,false,$resource['file_modified'],-1,false);
        
		} else {
		
			echo $lang['noimagefound'];
			exit;
		}

// retrieve image size
$sizes = getimagesize($imageurl);
$width = $sizes[0];
$height = $sizes[1];





	
include "../../../include/header.php";


?>


<!--<script type="text/javascript" src="../lib/fnclientlib/js/fnclient.js"></script>
 <link rel="stylesheet" type="text/css" href="../lib/fnclientlib/styles/fnclient.css" />-->
<p><a href="<?php echo $baseurl?>/pages/view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresourceview"]?></a></p>
<h1><?php echo $lang['annotateimage'] ?></h1>
<p><?php echo $lang['annotateblurb']; ?></p>

		<style type="text/css" media="all">@import "../lib/jquery/css/annotation.css";</style>
		<script type="text/javascript" src="../lib/jquery/js/jquery-1.3.2.js"></script>
		<script type="text/javascript" src="../lib/jquery/js/jquery-ui-1.7.1.js"></script>
		<script type="text/javascript" src="../lib/jquery/js/jquery.annotate.js"></script>
   <script>
     jQuery.noConflict();
     
</script>
		<script language="javascript">
			jQuery(window).load(function() {
				jQuery("#toAnnotate").annotateImage({
					getUrl: "get.php?ref=<?php echo $ref?>&pw=<?php echo $width?>&ph=<?php echo $height?>",
					saveUrl: "save.php?ref=<?php echo $ref?>&pw=<?php echo $width?>&ph=<?php echo $height?>",
					deleteUrl: "delete.php?ref=<?php echo $ref?>",
					editable: true,
					useAjax: true  
				});
			});
		</script>
		<div>
			<img id="toAnnotate" src="<?php echo $imageurl?>" alt="Trafalgar Square" style="display: block; margin: 0 auto;" />
		</div>

<?php


include "../../../include/footer.php";



?>
