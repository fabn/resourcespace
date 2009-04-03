<?php
include "../../include/db.php";
include "../../include/general.php";
set_time_limit(60*30);
copy_hitcount_to_live();

if ($send_statistics) {send_statistics();}

?>
Copy done - <?php echo date("d M Y")?>
