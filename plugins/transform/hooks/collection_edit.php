<?php
function HookTransformCollection_editColleditformbottom (){
	global $ref;
	global $lang;


        include_once "../plugins/transform/include/config.default.php";
        if (file_exists("../plugins/transform/include/config.php")){
                include_once("../plugins/transform/include/config.php");
        }


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
