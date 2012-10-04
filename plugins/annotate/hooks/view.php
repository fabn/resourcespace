<?php


function HookAnnotateViewRenderinnerresourcepreview(){
	global $ref,$ffmpeg_preview_extension,$resource,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl,$annotate_ext_exclude,$annotate_rt_exclude,$annotate_public_view,$annotate_pdf_output;

if (in_array($resource['file_extension'],$annotate_ext_exclude)){return false;}
if (in_array($resource['resource_type'],$annotate_rt_exclude)){return false;}

if (!($k=="") && !$annotate_public_view){return false;}
$download_multisize=true;

$flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
if (file_exists($flvfile)){return false;}

if ($resource["has_image"]==1)
	{
	?><style type="text/css" media="all">@import "<?php echo $baseurl?>/plugins/annotate/lib/jquery/css/annotation.css";</style>

	<script type="text/javascript" src="<?php echo $baseurl?>/plugins/annotate/lib/jquery/js/jquery.annotate.js"></script>
	<script type="text/javascript">
		button_ok = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["ok"])) ?>";
		button_cancel = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["cancel"])) ?>";
		button_delete = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["action-delete"])) ?>";
		button_add = "&gt;&nbsp;<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["action-add_note"])) ?>";
		error_saving = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["error-saving"])) ?>";
		error_deleting = "<?php echo preg_replace("/\r?\n/", "\\n", addslashes($lang["error-deleting"])) ?>";
	</script>
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
	if (!file_exists($imagepath)){return false;}	
	$sizes = getimagesize($imagepath);

	$w = $sizes[0];
	$h = $sizes[1];	

	if (file_exists($imagepath))
		{ 
		?>	


		<div id="wrapper" style="display:block;clear:none;float:left;margin: 0px 10px 10px 0px;">
<div>
<img id="toAnnotate" src="<?php echo $imageurl?>" id="previewimage" class="Picture" GALLERYIMG="no" style="display:block;"   />
	</div>
	<div style="padding-top:10px;">
	<a style="display:inline;" href="preview.php?<?php if (getval("annotate","")!=""){?>annotate=true&<?php } ?>ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" title="<?php echo $lang["fullscreenpreview"]?>">&gt;&nbsp;<?php echo $lang["fullscreenpreview"]?></a>
    
     <?php
     // MAGICTOUCH PLUGIN COMPATIBILITY
     global $magictouch_account_id;
     if ($magictouch_account_id!=""){
        global $plugins;global $magictouch_rt_exclude;global $magictouch_ext_exclude;if (in_array("magictouch",$plugins)&& !in_array($resource['resource_type'],$magictouch_rt_exclude) && !in_array($resource['file_extension'],$magictouch_ext_exclude) && !defined("MTFAIL")){?>&nbsp;<a style="display:inline;" href="<?php echo ((getval("from","")=="search")?"search.php?":"view.php?ref=" . $ref . "&")?>search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>" onClick="return CentralSpaceLoad(this);">&gt;&nbsp;<?php echo $lang['zoom']?></a><?php }
     }
     ///////////////
     ?>
     
<?php if ($annotate_pdf_output){?>
&nbsp;&nbsp;<a style="display:inline;float:right;" class="nowrap" href="<?php echo $baseurl?>/plugins/annotate/pages/annotate_pdf_config.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" onClick="return CentralSpaceLoad(this);">&gt;&nbsp;<?php echo $lang["pdfwithnotes"]?></a>
<?php } ?>
</div>

	</div>
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
		} 
	?><?php
	}
else
	{
	?>
	<img src="<?php echo $baseurl?>/gfx/<?php echo get_nopreview_icon($resource["resource_type"],$resource["file_extension"],false)?>" alt="" class="Picture" style="border:none;" id="previewimage" />
	<?php
	}
	

	
	
return true;	
}

?>
