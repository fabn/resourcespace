<?php


function HookAnnotateViewRenderinnerresourcepreview(){
	global $ref,$ffmpeg_preview_extension,$resource,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl,$annotate_ext_exclude,$annotate_rt_exclude;

if (in_array($resource['file_extension'],$annotate_ext_exclude)){return false;}
if (in_array($resource['resource_type'],$annotate_rt_exclude)){return false;}

$download_multisize=true;

$flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
if (file_exists($flvfile)){return false;}

if ($resource["has_image"]==1)
	{
		?><style type="text/css" media="all">@import "../plugins/annotate/lib/jquery/css/annotation.css";</style>
<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery-1.3.2-min.js"></script>
		<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery-ui-1.7.2-min.js"></script>
		<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery.annotate.js"></script>
   <script>
     jQuery.noConflict();
     
</script><?php
	$use_watermark=check_use_watermark($resource['ref']);
	$imagepath=get_resource_path($ref,true,"pre",false,$resource["preview_extension"],-1,1,$use_watermark);
	if (!file_exists($imagepath))
		{
        $imagepath=get_resource_path($ref,true,"thm",false,$resource["preview_extension"],-1,1,$use_watermark);    
		$imageurl=get_resource_path($ref,false,"thm",false,$resource["preview_extension"],-1,1,$use_watermark);
		}
	else
		{
		$imageurl=get_resource_path($ref,false,"pre",false,$resource["preview_extension"],-1,1,$use_watermark);
		}
$sizes = getimagesize($imagepath);

$w = $sizes[0];
$h = $sizes[1];	

	?>
	
	<?php
	if (file_exists($imagepath))
		{ 
		?>	<script language="javascript">
			jQuery(window).load(function() {
				jQuery("#toAnnotate").annotateImage({
					getUrl: "<?php echo $baseurl?>/plugins/annotate/pages/get.php?ref=<?php echo $ref?>&pw=<?php echo $w?>&ph=<?php echo $h?>",
					saveUrl: "<?php echo $baseurl?>/plugins/annotate/pages/save.php?ref=<?php echo $ref?>&pw=<?php echo $w?>&ph=<?php echo $h?>",
					deleteUrl: "<?php echo $baseurl?>/plugins/annotate/pages/delete.php?ref=<?php echo $ref?>",
					editable: true,
					useAjax: true   
				});
			});
		</script>
		<div id="wrapper" style="display:block;clear:none;float:left;margin: 0px 10px 10px 0px;">
<div>
<img id="toAnnotate" src="<?php echo $imageurl?>" id="previewimage" class="Picture" GALLERYIMG="no" style="display:block;"   />
	</div>
	<br>&nbsp;&nbsp;&gt;&nbsp;<a style="display:inline;" href="preview.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" title="<?php echo $lang["fullscreenpreview"]?>"><?php echo $lang["fullscreenpreview"]?></a>
    
     <?php
     // MAGICTOUCH PLUGIN COMPATIBILITY
     global $magictouch_account_id;
     if ($magictouch_account_id!=""){
        global $plugins;global $magictouch_rt_exclude;global $magictouch_ext_exclude;if (in_array("magictouch",$plugins)&& !in_array($resource['resource_type'],$magictouch_rt_exclude) && !in_array($resource['file_extension'],$magictouch_ext_exclude) && !defined("MTFAIL")){?>&nbsp;<a style="display:inline;" href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang['zoom']?></a><?php }
     }
     ///////////////
     ?>
     
<?php /* 
&nbsp;&nbsp;&gt;&nbsp;<a style="display:inline;" href="../plugins/annotate/pages/annotate_pdf_config.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" title="<?php echo $lang["pdfwithnotes"]?>"><?php echo $lang["pdfwithnotes"]?></a>
*/
?>
	</div>
<?php 
		} 
	?><?php
	}
else
	{
	?>
	<img src="../gfx/<?php echo get_nopreview_icon($resource["resource_type"],$resource["file_extension"],false)?>" alt="" class="Picture" style="border:none;" id="previewimage" />
	<?php
	}
	

	
	
return true;	
}

?>
