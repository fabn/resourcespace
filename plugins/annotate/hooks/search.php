<?php

function HookAnnotateSearchIcons(){ 
	global $result,$n;
	if ($result!=null&&$result[$n]['annotation_count']!=null && $result[$n]['annotation_count']!=0 && $result[$n]['file_extension']!="pdf"){
	?>
<div class="clearerleft"></div><div class="ResourcePanelInfo"><span class="IconUserRatingSpace" style="width:0px;"></span><img src="../plugins/annotate/lib/jquery/images/asterisk_yellow.png" height="15px;"/>&nbsp;&nbsp;<?php echo $result[$n]['annotation_count'];?> <?php if ($result[$n]['annotation_count']==1){?>note<?php } else {?>notes<?php }?></div><?php 
} else { ?>
<div class="clearerleft"></div><div class="ResourcePanelInfo"><span class="IconUserRatingSpace"></span></div><?php }
}
?>
