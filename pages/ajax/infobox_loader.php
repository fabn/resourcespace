<?php
include "../../include/db.php";
include "../../include/general.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../../include/authenticate.php";}
include "../../include/search_functions.php";
include "../../include/resource_functions.php";

$ref=getvalescaped("ref","");

# Load resource data
$resource=get_resource_data($ref);

# Load access level
$access=get_resource_access($ref);

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

<h2><?php echo htmlspecialchars(i18n_get_translated($resource["title"]))?></h2>

<?php
# Display fields
for ($n=0;$n<count($infobox_fields);$n++)
	{
	$field=$infobox_fields[$n];
	if ((checkperm("f" . $field) || checkperm("f*"))
	&& !checkperm("f-" . $field))
		{
		$value=trim(get_data_by_field($ref,$field));
		if ($value!="")
			{
			$value=nl2br(htmlspecialchars(TidyList(i18n_get_translated($value))));
			?>
			<p><?php echo $value?></p>
			<?php	
			}
		}
	}
?>

<?php } ?>