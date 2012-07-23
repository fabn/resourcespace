<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/authenticate.php";if (!checkperm("a")) {exit("Access denied");}
include "../../../include/search_functions.php";

include "../../../include/header.php";

# The access key is used to sign all inbound queries, the remote system must therefore know the access key.
$access_key=md5("resourceconnect" . $scramble_key);

?>
<h1 style="padding-bottom:10px;">ResourceConnect Configuration</h1>

<p>You must give user groups the 'resourceconnect' permission to enable network searching.</p>

<p>The access key for this installation is: <strong><?php echo $access_key ?></strong>. This must be entered into the configuration file for systems that are connecting to this system.</p>


<?php
include "../../../include/footer.php";