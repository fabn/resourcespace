<?php

function HookAnnotatePreviewPreviewimage2 (){
global $ext,$baseurl,$ref,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl,$url,$path,$path_orig,$annotate_ext_exclude,$annotate_rt_exclude;

$resource=get_resource_data($ref);
if (in_array($resource['file_extension'],$annotate_ext_exclude)){return false;}
if (in_array($resource['resource_type'],$annotate_rt_exclude)){return false;}

if (!file_exists($path) && !file_exists($path_orig)){return false;}
if (!file_exists($path)){$sizes = getimagesize($path_orig);}
else{
$sizes = getimagesize($path);
}
$w = $sizes[0];
$h = $sizes[1];
?>
<style type="text/css" media="all">@import "<?php echo $baseurl?>/plugins/annotate/lib/jquery/css/annotation.css";</style>
<script type="text/javascript" src="<?php echo $baseurl?>/plugins/annotate/lib/jquery/js/jquery-1.3.2-min.js"></script>
<script type="text/javascript" src="<?php echo $baseurl?>/plugins/annotate/lib/jquery/js/jquery-ui-1.7.2-min.js"></script>
<script type="text/javascript" src="<?php echo $baseurl?>/plugins/annotate/lib/jquery/js/jquery.annotate.js"></script>
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
<br>

     <?php
     // MAGICTOUCH PLUGIN COMPATIBILITY
     global $plugins;global $magictouch_rt_exclude;global $magictouch_ext_exclude;if (in_array("magictouch",$plugins)&& !in_array($resource['resource_type'],$magictouch_rt_exclude) && !in_array($resource['file_extension'],$magictouch_ext_exclude) && !defined("MTFAIL")){?>&nbsp;<a style="display:inline;" href="<?php echo ((getval("from","")=="search")?"search.php?":"preview.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang['zoom']?></a><?php }
     ///////////////
     ?>		</div>
     <?php
	

	
	
return true;	
}


