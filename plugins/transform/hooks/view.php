<?php

function HookAnnotateViewAfterresourceactions (){
	global $ref;
	global $access;
	global $lang;
	global $resource;
	global $cropper_allowed_extensions;

        include_once "../plugins/transform/include/config.default.php";
        if (file_exists("../plugins/transform/include/config.php")){
                include_once("../plugins/transform/include/config.php");
        }

	// fixme - for some reason this isn't pulling from config default for plugin even when set as global
	// hack below makes it work, but need to figure this out at some point
	// this is something to do with hook architecture -- think it is now fixed by above includes. But this
	// code isn't hurting anything, so leave it for now. -Dwiggins, 5/2010
	if (!isset($cropper_allowed_extensions)){
		$cropper_allowed_extensions = array('TIF','TIFF','JPG','JPEG','PNG','GIF','BMP','PSD'); // file formats that can be transformed
	} else {
		// in case these have been overriden, make sure these are all in uppercase.
		for($i=0;$i<count($cropper_allowed_extensions);$i++){
			$cropper_allowed_extensions[$i] = strtoupper($cropper_allowed_extensions[$i]);
		}	
	}

/*
	if ($access==0 && $resource['has_image']==1 ){
		echo "&nbsp;&nbsp;<li><a href='../plugins/annotate/pages/annotate.php?ref=$ref'>&gt; ";
		echo $lang['annotate'];
		echo "</a></li>";
	}
*/
}

function HookAnnotateViewRenderinnerresourcepreview(){
	global $ref,$ffmpeg_preview_extension,$resource,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl;
	if ($resource['file_extension']=="pdf"){return false;}
	
$download_multisize=true;

$flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
if (file_exists($flvfile)){return false;}

if ($resource["has_image"]==1)
	{
		?><style type="text/css" media="all">@import "../plugins/annotate/lib/jquery/css/annotation.css";</style>
<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery-1.3.2.js"></script>
		<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery-ui-1.7.1.js"></script>
		<script type="text/javascript" src="../plugins/annotate/lib/jquery/js/jquery.annotate.js"></script>
   <script>
     jQuery.noConflict();
     
</script><?php
	$use_watermark=check_use_watermark($resource['ref']);
	$imagepath=get_resource_path($ref,true,"pre",false,$resource["preview_extension"],-1,1,$use_watermark);
	if (!file_exists($imagepath))
		{
		$imageurl=get_resource_path($ref,false,"thm",false,$resource["preview_extension"],-1,1,$use_watermark);
		}
	else
		{
		$imageurl=get_resource_path($ref,false,"pre",false,$resource["preview_extension"],-1,1,$use_watermark);
		}
$sizes = getimagesize($imageurl);
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
	<br>&nbsp;&nbsp;<a style="display:inline;" href="preview.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" title="<?php echo $lang["fullscreenpreview"]?>"><?php echo $lang["fullscreenpreview"]?></a>

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
