<?php
# Reporting functions

function get_reports()
	{
	# Returns all reports in a result array.
	return sql_query("select * from report order by name");
	}

function do_report($ref,$from_y,$from_m,$from_d,$to_y,$to_m,$to_d,$download=true)
	{
	# Run report with id $ref for the date range specified. Returns a result array.
	$report=sql_query("select * from report where ref='$ref'");$report=$report[0];

	if ($download)
		{
		$filename=str_replace(array(" ","(",")","-","/"),"_",$report["name"]) . "_" . $from_y . "_" . $from_m . "_" . $from_d . "_to_" . $to_y . "_" . $to_m . "_" . $to_d . ".csv";
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=" . $filename . "");
		}
	
	$sql=$report["query"];
	$sql=str_replace("[from-y]",$from_y,$sql);
	$sql=str_replace("[from-m]",$from_m,$sql);
	$sql=str_replace("[from-d]",$from_d,$sql);
	$sql=str_replace("[to-y]",$to_y,$sql);
	$sql=str_replace("[to-m]",$to_m,$sql);
	$sql=str_replace("[to-d]",$to_d,$sql);
	
	global $view_title_field;
	#back compatibility for three default reports, to replace "title" with the view_title_field.
	#all reports should either use r.title or view_title_field when referencing the title column on the resource table.
	if ($ref==7||$ref==8||$ref==9){
		$sql=str_replace(",title",",field".$view_title_field,$sql);
	}
	
    $sql=str_replace("view_title_field","field".$view_title_field,$sql);
	$sql=str_replace("r.title","field".$view_title_field,$sql);
	
	$results=sql_query($sql);
	#echo "\"Number of results: " . count($results) . "\"\n";
	
	if ($download)
		{
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
		}
	else
		{
		# Not downloading - output a table
		$output="<br /><style>.InfoTable td {padding:5px;}</style><table class=\"InfoTable\">";
		for ($n=0;$n<count($results);$n++)
			{
			$result=$results[$n];
			if ($n==0)
				{
				$f=0;
				$output.="<tr>";
				foreach ($result as $key => $value)
					{
					$f++;
					$output.="<td><strong>" . $key . "</strong></td>";
					}
				$output.="</tr>";
				}
			$f=0;
			$output.="<tr>";
			foreach ($result as $key => $value)
				{
				$f++;
				$output.="<td>" . $value . "</td>";
				}
			$output.="</tr>";
			}
		$output.="</table>";
		return $output;
		}
		
	exit();
	}
	
	
	
?>
