<?php

function HookResourceofthedayHomeReplaceslideshow ()
	{
	global $rotd_field;
	$rotd=sql_value("select resource value from resource_data where resource_type_field=$rotd_field and value like '" . date("Y-m-d") . "%' limit 1;",0);
	if ($rotd==0)
		{
		# No resource of the day today. Pick one at random, using today as a seed so the same image will be used all of the day.
		$rotd=sql_value("select resource value from resource_data where resource_type_field=$rotd_field and length(value)>0 order by rand(" . date("d") . ") limit 1;",0);		
		}

	if ($rotd==0) # Still no resource?
		{
		# No resource of the day fields are set. Return to default slideshow functionality.
		return false;
		}

	# Fetch resource data
	$resource=get_resource_data($rotd);

	# Fetch title
	$title=sql_value("select value from resource_data where resource='$rotd' and resource_type_field=8","");

	# Fetch caption
	$caption=sql_value("select value from resource_data where resource='$rotd' and resource_type_field=18","");

	# Show resource!
	$pre=get_resource_path($rotd,false,"pre",false,"jpg");
	?>
	<div class="HomePicturePanel RecordPanel" style="width:350px;padding-left:4px;">
	<a href="view.php?ref=<?php echo $rotd ?>"><img class="ImageBorder" style="margin-bottom: 10px;" src="<?php echo $pre ?>" /></a>
	<br />
	<h2 ><?php echo htmlspecialchars($title) ?></h2>
	<?php echo htmlspecialchars($caption) ?>
	</div>
	<?php
	
	return true;
	}


?>