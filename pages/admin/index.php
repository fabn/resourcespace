<?php
include "../../include/db.php";
include "../../include/authenticate.php";
?>
<html>
<head><title>Administration</title>

<frameset rows="110,*" frameborder=0>
<frame colspan=2 name="top" id="top" src="../admin_header.php" frameborder=0 scrolling=no>
    <frameset cols="350px,*" frameborder=0>
    <frame name="left" id="left" src="tree.php">
    <frame name="right" id="right" src="blank.php">
    </frameset>
</frameset>

</head>
</html>
