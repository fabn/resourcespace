<?
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";

$year=getvalescaped("year",date("Y"));
$groupselect=getvalescaped("groupselect","");
$groups=getvalescaped("groups","");

$title=$applicationname . " - " . $lang["statisticsfor"] . " " . $year;
?>
<html><head>
<style>body {font-family:arial,helvetica;}</style><title><?=$title?></title></head>
<body onload="print();">

<h1><?=$title?></h1>

<? $types=get_stats_activity_types(); 
for ($n=0;$n<count($types);$n++)
	{
	?>
	<hr>
	<!--<h2><?=$types[$n]?></h2>-->
	<p><img src="../graph.php?activity_type=<?=urlencode($types[$n])?>&year=<?=$year?>&groupselect=<?=$groupselect?>&groups=<?=$groups?>" width=600 height=250></p>
	<?
	}
?>

</body>
</html>
