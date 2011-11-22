<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

// commented out variables that either don't seem to work or I'm unsure how to test -tom

if (getval("submit","")!="")
	{
	//$cropper_default_target_format = getvalescaped("cropper_default_target_format","");
	$cropper_debug = getvalescaped("cropper_debug","");
	$cropper_formatarray=explode(",",getvalescaped("cropper_formatarray",""));
	//$cropper_force_original_format = getvalescaped("cropper_force_original_format","");
	//$cropper_cropsize = getvalescaped("cropper_cropsize","");
	$cropper_custom_filename = getvalescaped("cropper_custom_filename","");
	//$cropper_use_filename_as_title = getvalescaped("cropper_use_filename_as_title","");
	//$cropper_allow_scale_up = getvalescaped("cropper_allow_scale_up","");
	$cropper_rotation = getvalescaped("cropper_rotation","");
	//$cropper_transform_original = getvalescaped("cropper_transform_original","");
	$cropper_use_repage = getvalescaped("cropper_use_repage","");
	//$cropper_jpeg_rgb = getvalescaped("cropper_jpeg_rgb","");
	$cropper_enable_batch = getvalescaped("cropper_enable_batch","");

	$config=array();
	//$config['cropper_default_target_format']=$cropper_default_target_format;
	$config['cropper_debug']=$cropper_debug;
	$config['cropper_formatarray']=$cropper_formatarray;
	//$config['cropper_force_original_format']=$cropper_force_original_format;
	//$config['cropper_cropsize']=$cropper_cropsize;
	$config['cropper_custom_filename']=$cropper_custom_filename;
	//$config['cropper_use_filename_as_title']=$cropper_use_filename_as_title;
	//$config['cropper_allow_scale_up']=$cropper_allow_scale_up;
	$config['cropper_rotation']=$cropper_rotation;
	//$config['cropper_transform_original']=$cropper_transform_original;
	$config['cropper_use_repage']=$cropper_use_repage;
	//$config['cropper_jpeg_rgb']=$cropper_jpeg_rgb;
	$config['cropper_enable_batch']=$cropper_enable_batch;
	
	set_plugin_config("transform",$config);
	
	redirect("pages/team/team_home.php");
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>MagicTouch Configuration</h1>

<form id="form1" name="form1" method="post" action="">

<?php // echo config_text_field("cropper_default_target_format","Default Target Format",$cropper_default_target_format);?>
<?php echo config_boolean_field("cropper_debug","Cropper Debug",$cropper_debug);?>
<?php echo config_text_field("cropper_formatarray","File Formats (comma delimited)",implode(',',$cropper_formatarray));?>
<?php //echo config_boolean_field("cropper_force_original_format","cropper_force_original_format",$cropper_force_original_format);?>
<?php //echo config_text_field("cropper_cropsize","cropper_cropsize","pre");?>
<?php echo config_boolean_field("cropper_custom_filename","Custom Filename",$cropper_custom_filename);?>
<?php //echo config_boolean_field("cropper_use_filename_as_title","Use Filename as Title",$cropper_use_filename_as_title);?>
<?php //echo config_boolean_field("cropper_allow_scale_up","cropper_allow_scale_up",$cropper_allow_scale_up);?>
<?php echo config_boolean_field("cropper_rotation","cropper_rotation",$cropper_rotation);?>
<?php //echo config_boolean_field("cropper_transform_original","cropper_transform_original",$cropper_transform_original);?>
<?php echo config_boolean_field("cropper_use_repage","cropper_use_repage",$cropper_use_repage);?>
<?php //echo config_boolean_field("cropper_jpeg_rgb","cropper_jpeg_rgb",$cropper_jpeg_rgb);?>
<?php echo config_boolean_field("cropper_enable_batch","cropper_enable_batch",$cropper_enable_batch);?> 

<div class="Question">  
<label for="submit"></label> 
<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   
</div><div class="clearerleft"></div>

</form>
</div>	
<?php include "../../../include/footer.php";
