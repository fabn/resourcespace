<?php
include "../../include/db.php";
include "../../include/general.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref","",true),$k))) {include "../../include/authenticate.php";}
include "../../include/search_functions.php";
include "../../include/resource_functions.php";

$ref=getvalescaped("ref","",true);
$image=(getvalescaped("image","")!="");


# Load resource data
$resource=get_resource_data($ref);

# Load access level
$access=get_resource_access($ref);
$use_watermark=check_use_watermark();

if ($image)
	{
	# Image mode. Just display the 'pre' image.
	if ($resource["has_image"]==1)
		{
		$imagepath=get_resource_path($ref,true,"pre",false,$resource["preview_extension"],-1,1,$use_watermark);
		if (!file_exists($imagepath))
			{
			$imageurl=get_resource_path($ref,false,"thm",false,$resource["preview_extension"],-1,1,$use_watermark);
			}
		else
			{
			$imageurl=get_resource_path($ref,false,"pre",false,$resource["preview_extension"],-1,1,$use_watermark);
			}
		}
	else
		{
		$imageurl="../gfx/" . get_nopreview_icon($resource["resource_type"],$resource["file_extension"],false,true);
		}
	
	?>
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td valign="center" align="center"><img src="<?php echo $imageurl ?>"></td></tr></table>
	<?php
	
	exit();
	}


# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if ($access==2) 
		{
		exit("This is a confidential resource.");
		}
?>
<?php if (!hook("infoboxreplace")) {


if ($infobox_display_resource_id)
	{
	# Display resource ID
	?>
	<div style="float:right;padding-right:10px;"><?php echo $lang["resourceid"] ?>: <?php echo $ref?></div>
	<?php
	}

if ($infobox_display_resource_icon && $resource["file_extension"]!="")
	{
	# Display resource type indicator icon (no preview icon)
	?>
	<img style="float:right;clear:right;" src="../gfx/<?php echo get_nopreview_icon($resource["resource_type"],$resource["file_extension"],true,true) ?>">
	<?php
	}
?>

<h2><?php 
$title="";


$title=get_data_by_field($ref,$view_title_field);	
if (isset($metadata_template_title_field) && isset($metadata_template_resource_type))
	{
	if ($resource['resource_type']==$metadata_template_resource_type)
		{
		$title=get_data_by_field($ref,$metadata_template_title_field);
		}	
	}	

echo trim(htmlspecialchars(i18n_get_translated($title)))?></h2>

<?php
# Display fields
for ($n=0;$n<count($infobox_fields);$n++)
	{
	$field=$infobox_fields[$n];
	if ((checkperm("f" . $field) || checkperm("f*"))
	&& !checkperm("f-" . $field))
		{
		$value=trim(get_data_by_field($ref,$field));
		$type = sql_value("select type value from resource_type_field where ref = $field",0);
		if ($value!="")
			{
			if ($type <> 8)
				{
				$value=nl2br(htmlspecialchars(TidyList(i18n_get_translated($value))));
				}
			?>
			<p><?php echo $value?></p>
			<?php	
			}
		}
	}
?>

<?php } ?>
