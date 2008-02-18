<?
include "include/db.php";
include "include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "include/general.php";

$year=getvalescaped("year",date("Y"));
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
	<p><img xstyle="border:1px solid black;" src="graph.php?activity_type=<?=urlencode($types[$n])?>&year=<?=$year?>" width=600 height=250></p>
	<?
	}
?>

</body>
</html>
