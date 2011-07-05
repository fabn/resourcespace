<?php
include dirname(__FILE__) . "/../../../include/db.php";
include dirname(__FILE__) . "/../../../include/authenticate.php";
include dirname(__FILE__) . "/../../../include/general.php";

$field=getvalescaped("field","");
$keyword=getvalescaped("keyword","");

$fielddata=get_resource_type_field($field);

# Append the option and update the field
$options=$fielddata["options"] . ", " . $keyword;
sql_query("update resource_type_field set options='" . escape_check($options) . "' where ref='$field'");

