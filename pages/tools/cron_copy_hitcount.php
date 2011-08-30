<?php
include dirname(__FILE__) . "/../../include/db.php";
include dirname(__FILE__) . "/../../include/general.php";
include dirname(__FILE__) . "/../../include/reporting_functions.php";
include dirname(__FILE__) . "/../../include/resource_functions.php";
set_time_limit(60*30);

# All scheduled tasks are here for now, as older installations still call this file directly instead of batch/cron.php.

copy_hitcount_to_live();
if ($send_statistics) {send_statistics();}

# Send periodic reports also
send_periodic_report_emails();

# Update cron date
sql_query("delete from sysvars where name='last_cron'");
sql_query("insert into sysvars(name,value) values ('last_cron',now())");

?>
Relevance matching hitcount: copy done - <?php echo date("d M Y")?>

<?php include "geo_setcoords_from_country.php";

# Update disk quota column on resource table.
update_disk_usage_cron();


 ?>