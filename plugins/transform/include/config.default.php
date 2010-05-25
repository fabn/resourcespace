<?php
$annotate_default_target_format = 'JPG';
$annotate_debug=false;
$annotate_formatarray = array('TIF','JPG','PNG'); // output formats allowed for transform operations
$annotate_force_original_format = false;
$annotate_cropsize='pre';
$annotate_custom_filename=true;
$annotate_use_filename_as_title=true;
$annotate_allow_scale_up = true; // if false, scaling parameters that would result in enlargement are ignored
$cropper_rotation = true; // if true, enables flipping and rotation of images
$cropper_transform_original = false;
$cropper_use_repage = true; // use repage feature to remove image geometry after transformation. This is necessary for most ImageMagick-based systems to behave correctly.
$cropper_jpeg_rgb = true; // when creating a jpeg, make sure it is RGB
?>
