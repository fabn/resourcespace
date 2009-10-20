<?php
/**
 * Print statistics page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";

$year=getvalescaped("year",date("Y"));
$groupselect=getvalescaped("groupselect","");
$groups=getvalescaped("groups","");

$title=$applicationname . " - " . $lang["statisticsfor"] . " " . $year;
?>
<html><head>
<style>body {font-family:arial,helvetica;}</style><title><?php echo $title?></title></head>
<body onload="print();">

<h1><?php echo $title?></h1>

<?php $types=get_stats_activity_types(); 
for ($n=0;$n<count($types);$n++)
	{
	?>
	<hr>
	<!--<h2><?php echo $types[$n]?></h2>-->
	<p><img src="../graph.php?activity_type=<?php echo urlencode($types[$n])?>&year=<?php echo $year?>&groupselect=<?php echo $groupselect?>&groups=<?php echo $groups?>" width=600 height=250></p>
	<?php
	}
?>

</body>
</html>
