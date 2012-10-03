<?php
function HookAnnotatePreviewReplacepreviewbacktoview(){
	global $baseurl,$lang,$ref,$search,$offset,$order_by,$sort,$archive,$k;?>
<p style="margin:7px 0 7px 0;padding:0;"><a href="<?php echo $baseurl?>/pages/view.php?<?php if (getval("annotate","")=="true"){?>annotate=true&<?php } ?>ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&lt; <?php echo $lang["backtoview"]?></a>
<?php return true;
} 

function HookAnnotatePreviewPreviewimage2 (){
global $ext,$baseurl,$ref,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl,$url,$path,$path_orig,$annotate_ext_exclude,$annotate_rt_exclude,$annotate_public_view,$annotate_pdf_output;
if (getval("alternative","")!=""){return false;}

$resource=get_resource_data($ref);

if (in_array($resource['file_extension'],$annotate_ext_exclude)){return false;}
if (in_array($resource['resource_type'],$annotate_rt_exclude)){return false;}
if (!($k=="") && !$annotate_public_view){return false;}

if (!file_exists($path) && !file_exists($path_orig)){return false;}
if (!file_exists($path)){
	$sizes = getimagesize($path_orig);}
else{
	$sizes = getimagesize($path);
}

$w = $sizes[0];
$h = $sizes[1];

?>
<style type="text/css" media="all">@import "<?php echo $baseurl?>/plugins/annotate/lib/jquery/css/annotation.css";</style>

<script type="text/javascript" src="<?php echo $baseurl?>/plugins/annotate/lib/jquery/js/jquery.annotate.js"></script>
<script type="text/javascript">
    button_ok = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["ok"])) ?>";
    button_cancel = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["cancel"])) ?>";
    button_delete = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["action-delete"])) ?>";
    button_add = "&gt&nbsp;<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["action-add_note"])) ?>";		
    error_saving = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["error-saving"])) ?>";
    error_deleting = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["error-deleting"])) ?>";
</script>
<script>
     jQuery.noConflict();
</script>

<div id="wrapper" style="display:block;clear:none;float:left;margin: 0px 10px 10px 0px;">
<div>
		<img id="toAnnotate" src="<?php echo $url?>" id="previewimage" class="Picture" GALLERYIMG="no" style="display:block;"   />
	</div>

<div style="padding-top:10px;">
     <?php
     // MAGICTOUCH PLUGIN COMPATIBILITY
     global $magictouch_account_id;
     if ($magictouch_account_id!=""){
        global $plugins;global $magictouch_rt_exclude;global $magictouch_ext_exclude;if (in_array("magictouch",$plugins)&& !in_array($resource['resource_type'],$magictouch_rt_exclude) && !in_array($resource['file_extension'],$magictouch_ext_exclude) && !defined("MTFAIL")){?>&nbsp;&nbsp;<a style="display:inline;" href="<?php echo ((getval("from","")=="search")?"search.php?":"preview.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang['zoom']?></a><?php }
     }
     ///////////////
     ?>	
     <?php if ($annotate_pdf_output){?>
     &nbsp;&nbsp;<a style="display:inline;float:right;" href="<?php echo $baseurl?>/plugins/annotate/pages/annotate_pdf_config.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&gt;&nbsp;<?php echo $lang["pdfwithnotes"]?></a>
     <?php } ?>
     	</div></div>
<script language="javascript">
jQuery("#toAnnotate").load(function(){
	jQuery("#toAnnotate").annotateImage({
		getUrl: "<?php echo $baseurl?>/plugins/annotate/pages/get.php?ref=<?php echo $ref?>&k=<?php echo $k ?>&pw=<?php echo $w?>&ph=<?php echo $h?>",
		saveUrl: "<?php echo $baseurl?>/plugins/annotate/pages/save.php?ref=<?php echo $ref?>&k=<?php echo $k ?>&pw=<?php echo $w?>&ph=<?php echo $h?>",
		deleteUrl: "<?php echo $baseurl?>/plugins/annotate/pages/delete.php?ref=<?php echo $ref?>&k=<?php echo $k ?>",
		useAjax: true,
		<?php  if ($k==""){?> editable: true <?php }
			else
		{ ?> editable: false <?php } ?>  
	});
});
</script>
     
     
     <?php

return true;	
}


