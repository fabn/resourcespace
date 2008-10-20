<?
include "../../include/db.php";
include "../../include/general.php";
set_time_limit(60*30);
copy_hitcount_to_live();
?>
Copy done - <?=date("d M Y")?>
