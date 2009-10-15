<?php
# AJAX ratings save

include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
include "../../include/resource_functions.php";

user_rating_save(getvalescaped("ref","",true),getvalescaped("rating",""));

?>