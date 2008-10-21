<?
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../include/general.php";
include "../../include/reporting_functions.php";

$type=getvalescaped("type","");

if ($type!="")
	{
	if ($type=="sql") { $param="";$extension="sql";}
	if ($type=="xml") { $param="--xml";$extension="xml";}

	# Check for mysqldump at configured location
	if (!file_exists($mysql_bin_path . "/mysqldump")) {exit("Error: mysqldump not found at '$mysql_bin_path' - please check config.php");}
	
	# Send them the export.
	header("Content-type: application/octet-stream");
	header("Content-disposition: attachment; filename=RS_Export_" . date("Y_m_d") . "." . $extension . "");
	passthru($mysql_bin_path . "/mysqldump -h $mysql_server -u $mysql_username " . ($mysql_password==""?"":"-p'" . $mysql_password . "'") . " $param $mysql_db");
	
	exit();
	}
include "../../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["exportdata"]?></h1>
  
<form method="post">
<div class="Question">
<label for="type"><?=$lang["exporttype"]?></label>
<select id="type" name="type" class="stdwidth">
<option value="sql">mysqldump - SQL</option>
<option value="xml">mysqldump - XML</option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["exportdata"]?>&nbsp;&nbsp;" />
</div>
</form>

</div>

<?
include "../../include/footer.php";
?>