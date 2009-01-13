<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/search_functions.php";

$ref=getvalescaped("ref","");
$resource=get_resource_data($ref);

# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if (!checkperm("v") && ($resource["access"]==2)) 
		{
		exit("This is a confidential resource.");
		}

# Get story text
$fields=get_resource_field_data($ref);
$storyextract="";
for ($n=0;$n<count($fields);$n++)
	{
	if ($fields[$n]["title"]=="Story Extract") {$storyextract=$fields[$n]["value"];}
	}
		
# Log this activity
daily_stat("Print story",$ref);
?>
<html>
<head><title><?php echo $applicationname?> <?php echo $lang["storyextract"]?></title>
<style>
body {font-family:verdana,arial,sans-serif;font-size:12px;}
h1 {font-size:16px;}
h2 {font-size:14px;}
.footer {font-size:10px;margin-top:30px;}
</style>
</head>
<body onLoad="print();">
<h1><?php echo $applicationname?> <?php echo $lang["storyextract"]?></h1>
<h2><?php echo $lang["resourceid"]?> <?php echo $ref?>: <?php echo $resource["title"]?></h2>
<p><?php echo nl2br(htmlspecialchars($storyextract))?></p>
</body>
</html>