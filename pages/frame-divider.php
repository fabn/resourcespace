<?php
include "../include/db.php";
include "../include/general.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Divider</title>
<link href="../css/global.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
<link href="../css/Col-<?php echo (isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu")?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss"/>
<!--[if lte IE 6]> <link href="../css/globalIE.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<!--[if lte IE 5.6]> <link href="../css/globalIE5.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<?php
# Include CSS files for for each of the plugins too (if provided)
for ($n=0;$n<count($plugins);$n++)
	{
	$csspath=dirname(__FILE__)."/../plugins/" . $plugins[$n] . "/css/style.css";
	if (file_exists($csspath))
		{
		?>
		<link href="<?php echo $baseurl?>/plugins/<?php echo $plugins[$n]?>/css/style.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
		<?php
		}
	}
?>
</head>

<body class="CollectDivide">
<p>&nbsp;</p>
</body>
</html>
