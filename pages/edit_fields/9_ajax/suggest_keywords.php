<?php
include dirname(__FILE__) . "/../../../include/db.php";
include dirname(__FILE__) . "/../../../include/authenticate.php";
include dirname(__FILE__) . "/../../../include/general.php";

$field=getvalescaped("field","");
$keyword=getvalescaped("term","");

$fielddata=get_resource_type_field($field);
$readonly=getval("readonly","");
?>[ <?php

# Return matches
$first=true;
$exactmatch=false;
$options=trim_array(explode(",",$fielddata["options"]));
for ($m=0;$m<count($options);$m++)
	{
	$trans=i18n_get_translated($options[$m]);
	if ($trans!="" && substr(strtolower($trans),0,strlen($keyword))==strtolower($keyword))
		{
		if (!$first) { ?>, <?php }
		$first=false;
		
		if (strtolower($trans)==strtolower($keyword)) {$exactmatch=true;}
		?>"<?php echo $trans ?>"<?php
		}
	}
	
if (!$exactmatch && !$readonly)
	{
	if (!$first) { ?>, <?php }
	$first=false;

	?>
	"<?php echo $lang["createnewentryfor"] ?> <?php echo $keyword ?>"
	<?php
	}
?>
]

