<?php
# AJAX ratings save

include "../../include/db.php";
include "../../include/general.php";
include "../../include/authenticate.php";
include "../../include/resource_functions.php";

user_rating_save(getvalescaped("userref","",true),getvalescaped("ref","",true),getvalescaped("rating",""));

?>
