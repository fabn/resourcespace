<?php

function get_resource_of_the_day()
    {
    global $rotd_field;

    # Search for today's resource of the day.
    $rotd = sql_value("select resource value from resource_data where resource>0 and resource_type_field=$rotd_field and value like '" . date("Y-m-d") . "%' limit 1;",0);
    if ($rotd!=0) {return $rotd;} # A resource was found?

    # No resource of the day today. Pick one at random, using today as a seed so the same image will be used all of the day.
    $rotd = sql_value("select resource value from resource_data where resource>0 and resource_type_field=$rotd_field and length(value)>0 order by rand(" . date("d") . ") limit 1;",0);
    if ($rotd!=0) {return $rotd;} # A resource was found now?

    # No resource of the day fields are set. Return to default slideshow functionality.
    return false;
    }

?>
