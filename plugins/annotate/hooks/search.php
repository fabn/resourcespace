<?php

function HookAnnotateSearchIcons(){ 
	global $baseurl,$k,$search,$archive,$sort,$offset,$order_by,$result,$n,$lang,$k,$annotate_public_view;
	if (!($k=="") && !$annotate_public_view){return false;}
    if (!is_array($result)){?><div class="clearerleft"></div><div class="ResourcePanelInfo"><span class="IconUserRatingSpace"></span></div><?php return true;}
	if (isset($result[$n]) && $result[$n]['annotation_count']!=null && $result[$n]['annotation_count']!=0 && $result[$n]['file_extension']!="pdf"){
	?>
<div class="clearerleft"></div><div class="ResourcePanelInfo"><span class="IconUserRatingSpace" style="width:0px;"></span><img src="../plugins/annotate/lib/jquery/images/asterisk_yellow.png" height="10px;"/>&nbsp;&nbsp;<a href="<?php echo $baseurl?>/pages/view.php?annotate=true&ref=<?php echo $result[$n]['ref']?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>"><?php echo $result[$n]['annotation_count']==1 ? $lang["note-1"] : str_replace("%number", $result[$n]['annotation_count'], $lang["note-2"]); ?></a></div><?php 
} else { ?>
<div class="clearerleft"></div><div class="ResourcePanelInfo"><span class="IconUserRatingSpace"></span>&nbsp;&nbsp;</div><?php }
}
?>
