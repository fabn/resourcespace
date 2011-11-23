<?php
$cropper_default_target_format = 'JPG';
$cropper_debug=false;
$cropper_allowed_extensions = array('TIF','TIFF','JPG','JPEG','PNG','GIF','BMP','PSD'); // file formats that can be transformed
$cropper_formatarray = array('TIF','JPG','PNG'); // output formats allowed for transform operations
$cropper_force_original_format = false;
$cropper_cropsize='pre';
$cropper_custom_filename=true;
$cropper_use_filename_as_title=true;
$cropper_allow_scale_up = true; // if false, scaling parameters that would result in enlargement are ignored
$cropper_rotation = true; // if true, enables flipping and rotation of images
$cropper_transform_original = false;
$cropper_use_repage = true; // use repage feature to remove image geometry after transformation. This is necessary for most ImageMagick-based systems to behave correctly.
$cropper_jpeg_rgb = true; // when creating a jpeg, make sure it is RGB
$cropper_enable_batch = false; // enable batch transform of collections
?>
