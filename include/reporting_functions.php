<?
# Reporting functions

function get_reports()
	{
	# Returns all reports in a result array.
	return sql_query("select * from report order by name");
	}

function do_report($ref,$from_y,$from_m,$from_d,$to_y,$to_m,$to_d)
	{
	# Run report with id $ref for the date range specified. Returns a result array.
	$report=sql_query("select * from report where ref='$ref'");$report=$report[0];

	$filename=str_replace(array(" ","(",")","-","/"),"_",$report["name"]) . "_" . $from_y . "_" . $from_m . "_" . $from_d . "_to_" . $to_y . "_" . $to_m . "_" . $to_d . ".csv";
	header("Content-type: application/octet-stream");
	header("Content-disposition: attachment; filename=" . $filename . "");

	
	$sql=$report["query"];
	$sql=str_replace("[from-y]",$from_y,$sql);
	$sql=str_replace("[from-m]",$from_m,$sql);
	$sql=str_replace("[from-d]",$from_d,$sql);
	$sql=str_replace("[to-y]",$to_y,$sql);
	$sql=str_replace("[to-m]",$to_m,$sql);
	$sql=str_replace("[to-d]",$to_d,$sql);
	
	$results=sql_query($sql);
	#echo "\"Number of results: " . count($results) . "\"\n";
	for ($n=0;$n<count($results);$n++)
		{
		$result=$results[$n];
		if ($n==0)
			{
			$f=0;
			foreach ($result as $key => $value)
				{
				$f++;
				if ($f>1) {echo ",";}
				echo "\"" . $key . "\"";
				}
			echo "\n";
			}
		$f=0;
		foreach ($result as $key => $value)
			{
			$f++;
			if ($f>1) {echo ",";}
			echo "\"" . $value . "\"";
			}
		echo "\n";
		}
	exit();
	}
	
	
	
?>