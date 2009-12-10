<?php
include dirname(__FILE__) . "/../../include/db.php";
include dirname(__FILE__) . "/../../include/general.php";
set_time_limit(60*30);

# All scheduled tasks are here for now, as older installations still call this file directly instead of batch/cron.php.

copy_hitcount_to_live();
if ($send_statistics) {send_statistics();}

# Update cron date
sql_query("delete from sysvars where name='last_cron'");
sql_query("insert into sysvars(name,value) values ('last_cron',now())");

?>
Relevance matching hitcount: copy done - <?php echo date("d M Y")?>
