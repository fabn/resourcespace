<?php
function HookTransformCollection_editColleditformbottom (){
	global $ref;
	global $lang;
	global $cropper_enable_batch;

	if ($cropper_enable_batch){
	?>
<div class="Question">
<label><?php echo $lang['batchtransform']; ?></label>
<div class="Fixed">
<a href="../plugins/transform/pages/collection_transform.php?collection=<?php echo $ref?>"><?php echo $lang["transform"]?> &gt;</a>
</div>
<div class="clearerleft"> </div>
</div>

	<?php
	}
}

?>
