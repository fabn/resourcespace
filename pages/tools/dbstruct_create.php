<?
include "../../include/db.php";

# Use the below to set which tables we will extract data for.
#$datafor=array("usergroup","resource_type_field","site_text","user","collection","user_collection","report","preview_size","resource_type");
$datafor=array();

if (getval("execute","")!="")
	{
	# Fetch all tables
	$tables=sql_query("show tables");
	for ($n=0;$n<count($tables);$n++)
		{
		$table=$tables[$n]["Tables_in_" . $mysql_db];
		
		# Table structure		
		$f=fopen("../../dbstruct/table_" . $table . ".txt","w");
		$describe=sql_query("describe $table");
		for ($m=0;$m<count($describe);$m++)
			{
			fputcsv($f,$describe[$m]);
			}
		fclose($f);
		
		# Indices
		$f=fopen("../../dbstruct/index_" . $table . ".txt","w");
		$index=sql_query("show index from $table");
		for ($m=0;$m<count($index);$m++)
			{
			fputcsv($f,$index[$m]);
			}
		fclose($f);
		
		# Data
		if (in_array($table,$datafor))
			{
			$f=fopen("../../dbstruct/data_" . $table . ".txt","w");
			$index=sql_query("select * from $table");
			for ($m=0;$m<count($index);$m++)
				{
				fputcsv($f,$index[$m]);
				}
			fclose($f);
			}		
		}
	
	}
else
	{
	?>
	<p>This tool is for ResourceSpace developers only. It (re)creates the database structures defined in the 'dbstruct' folder using the current database as a master. Do not run this unless you are sure what it does. Do not commit the changed dbstruct files back to Subversion unless you intend to alter the database structure for all installations.</p>
	<form method="post">
	<input type="submit" name="execute" value="Execute">
	</form>
	<?
	}
?>