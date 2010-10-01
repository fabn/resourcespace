<?php

function get_resource_of_the_day()
	{
	global $rotd_field;
	$rotd=sql_value("select resource value from resource_data where resource_type_field=$rotd_field and value like '" . date("Y-m-d") . "%' limit 1;",0);
	if ($rotd==0)
		{
		# No resource of the day today. Pick one at random, using today as a seed so the same image will be used all of the day.
		$rotd=sql_value("select resource value from resource_data where resource_type_field=$rotd_field and length(value)>0 order by rand(" . date("d") . ") limit 1;",0);		
		return $rotd;
		}

	if ($rotd==0) # Still no resource?
		{
		# No resource of the day fields are set. Return to default slideshow functionality.
		return false;
		}
	}

?>