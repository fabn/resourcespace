<?php

function HookAnnotatePreviewPreviewimage2 (){
global $baseurl,$ref,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl,$url,$path;

$resource=get_resource_data($ref);
if ($resource['file_extension']=="pdf"){return false;}

$sizes = getimagesize($url);
$w = $sizes[0];
$h = $sizes[1];

?>
<style type="text/css" media="all">@import "../plugins/annotate/lib/jquery/css/annotation.css";</style>
<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery-ui-1.7.1.js"></script>
<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery.annotate.js"></script>
<script>
     jQuery.noConflict();
</script>
<script language="javascript">
			jQuery(window).load(function() {
				jQuery("#toAnnotate").annotateImage({
					getUrl: "<?php echo $baseurl?>/plugins/annotate/pages/get.php?ref=<?php echo $ref?>&pw=<?php echo $w?>&ph=<?php echo $h?>",
					saveUrl: "<?php echo $baseurl?>/plugins/annotate/pages/save.php?ref=<?php echo $ref?>&pw=<?php echo $w?>&ph=<?php echo $h?>",
					deleteUrl: "<?php echo $baseurl?>/plugins/annotate/pages/delete.php?ref=<?php echo $ref?>",
					editable: true,
					useAjax:true
				});
			});
		</script>
<div id="wrapper" style="display:block;clear:none;float:left;margin: 0px 10px 10px 0px;">
<div>
		<img id="toAnnotate" src="<?php echo $url?>" id="previewimage" class="Picture" GALLERYIMG="no" style="display:block;"   />
	</div>
<br>&nbsp;&nbsp;<a style="display:inline;" href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"><?php echo $lang["backtoresourceview"]?></a>		
		
	</div>
<!--<a style="display:inline;" href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">P</a>-->		<?php
	

	
	
return true;	
}


