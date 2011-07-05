<?php
include dirname(__FILE__) . "/../../../include/db.php";
include dirname(__FILE__) . "/../../../include/authenticate.php";
include dirname(__FILE__) . "/../../../include/general.php";

$field=getvalescaped("field","");
$keyword=getvalescaped("field_" . $field . "_selector","");

$fielddata=get_resource_type_field($field);

?><ul><?php


# Return matches
$exactmatch=false;
$options=trim_array(explode(",",$fielddata["options"]));
for ($m=0;$m<count($options);$m++)
	{
	$trans=i18n_get_translated($options[$m]);
	if ($trans!="" && substr(strtolower($trans),0,strlen($keyword))==strtolower($keyword))
		{
		if (strtolower($trans)==strtolower($keyword)) {$exactmatch=true;}
		?><li><?php echo $trans ?></li><?php
		}
	}
	
if (!$exactmatch)
	{
	?>
	<li><?php echo $lang["createnewentryfor"] ?> <?php echo $keyword ?></li>
	<?php
	}
?>
</ul>

