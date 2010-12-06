<?php
/**
 * Export data page (part of Team Center)
 * 
 * @package ResourceSpace
 * @subpackage Pages_Team
 */
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/reporting_functions.php";

$type=getvalescaped("type","");

if ($type!="")
	{
	if ($type=="sql") { $param="";$extension="sql";}
	if ($type=="xml") { $param="--xml";$extension="xml";}

	# Check for mysqldump at configured location
	$path=$mysql_bin_path . "/mysqldump";
	if (!file_exists($path)) {$path.=".exe";} # Try windows.
	if (!file_exists($path)) {exit("Error: mysqldump not found at '$mysql_bin_path' - please check config.php");}
	
	# Add options to ignore index tables, which are very large and are easily regenerated (using tools/reindex.php)
	$param.=" --ignore-table=$mysql_db.resource_keyword --ignore-table=$mysql_db.keyword";
	
	# Send them the export.
	header("Content-type: application/octet-stream");
	header("Content-disposition: attachment; filename=RS_Export_" . date("Y_m_d") . "." . $extension . "");
	passthru($path . " -h $mysql_server -u $mysql_username " . ($mysql_password==""?"":"-p'" . $mysql_password . "'") . " $param $mysql_db");
	
	exit();
	}
include "../../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["exportdata"]?></h1>
  
<form method="post">
<div class="Question">
<label for="type"><?php echo $lang["exporttype"]?></label>
<select id="type" name="type" class="stdwidth">
<option value="sql">mysqldump - SQL</option>
<option value="xml">mysqldump - XML</option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["exportdata"]?>&nbsp;&nbsp;" />
</div>
</form>

</div>

<?php
include "../../include/footer.php";
?>