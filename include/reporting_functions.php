<?php
# Reporting functions

function get_reports()
{
    # Returns an array of reports. The standard reports are translated using $lang. Custom reports are i18n translated.
    # The reports are always listed in the same order - regardless of the used language. 
    
    # Executes query.
    $r = sql_query("select * from report order by name");
    
    # Translates report names in the newly created array.
    $return = array();
    for ($n = 0;$n<count($r);$n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"], "report-");
        $return[] = $r[$n]; # Adds to return array.
    }
    return $return;
}

function do_report($ref,$from_y,$from_m,$from_d,$to_y,$to_m,$to_d,$download=true,$add_border=false)
	{
	# Run report with id $ref for the date range specified. Returns a result array.
	global $lang;

	$report=sql_query("select * from report where ref='$ref'");$report=$report[0];

    # Translates the report name.
    $report["name"]=lang_or_i18n_get_translated($report["name"], "report-");

	if ($download)
		{
		$filename=str_replace(array(" ","(",")","-","/"),"_",$report["name"]) . "_" . $from_y . "_" . $from_m . "_" . $from_d . "_" . $lang["to"] . "_" . $to_y . "_" . $to_m . "_" . $to_d . ".csv";
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
					echo "\"" . lang_or_i18n_get_translated($key,"columnheader-") . "\"";
					}
				echo "\n";
				}
			$f=0;
			foreach ($result as $key => $value)
				{
				$f++;
				if ($f>1) {echo ",";}
				echo "\"" . lang_or_i18n_get_translated($value, "usergroup-") . "\"";
				}
			echo "\n";
			}
		}
	else
		{
		# Not downloading - output a table
		$border="";
		if ($add_border) {$border="border=\"1\"";}
		$output="<br /><style>.InfoTable td {padding:5px;}</style><table $border class=\"InfoTable\">";
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
					$output.="<td><strong>" . lang_or_i18n_get_translated($key,"columnheader-") . "</strong></td>";
					}
				$output.="</tr>";
				}
			$f=0;
			$output.="<tr>";
			foreach ($result as $key => $value)
				{
				$f++;
				$output.="<td>" . lang_or_i18n_get_translated($value, "usergroup-") . "</td>";
				}
			$output.="</tr>";
			}
		$output.="</table>";
		if (count($results)==0) {$output.=$lang["reportempty"];}
		return $output;
		}
		
	exit();
	}
	
function create_periodic_email($user,$report,$period,$email_days)
	{
	# Creates a new automatic periodic e-mail report.
#	echo ("user=$user, report=$report, period=$period, email_days=$email_days");

	# Delete any matching rows for this report/period.
	sql_query("delete from report_periodic_emails where user='$user' and report='$report' and period='$period'");

	# Insert a new row.
	sql_query("insert into report_periodic_emails(user,report,period,email_days) values ('$user','$report','$period','$email_days')");
	
	# Return
	return true;
	}
	
	
function send_periodic_report_emails()
	{
	# For all configured periodic reports, send a mail if necessary.
	global $lang,$baseurl;
	
	# Query to return all 'pending' report e-mails, i.e. where we haven't sent one before OR one is now overdue.
	$reports=sql_query("select pe.*,u.email,r.name from report_periodic_emails pe join user u on pe.user=u.ref join report r on pe.report=r.ref where pe.last_sent is null or date_add(pe.last_sent,interval pe.email_days day)<=now()");
	foreach ($reports as $report)
		{
		$start=time()-(60*60*24*$report["period"]);
		
		$from_y = date("Y",$start);
		$from_m = date("m",$start);
		$from_d = date("d",$start);
			
		$to_y = date("Y");
		$to_m = date("m");
		$to_d = date("d");

		# Translates the report name.		
		$report["name"] = lang_or_i18n_get_translated($report["name"]);

		# Generate remote HTML table.
		$output=do_report($report["report"], $from_y, $from_m, $from_d, $to_y, $to_m, $to_d,false,true);

		# Append the unsubscribe link.
		$output.="<br>" . $lang["unsubscribereport"] . "<br>" . $baseurl . "/?ur=" . $report["ref"];

		# Formulate a title
		$title = $report["name"] . ": " . str_replace("?",$report["period"],$lang["lastndays"]);
				
		# Send mail.
		echo $lang["sendingreportto"] . " " . $report["email"] . "<br>";
		send_mail($report["email"],$title,$output,"","","",null,"","",true);
	
		# Mark as done.
		sql_query("update report_periodic_emails set last_sent=now() where ref='" . $report["ref"] . "'");
		}
	}

function unsubscribe_periodic_report($unsubscribe)
	{
	global $userref;
	sql_query("delete from report_periodic_emails where user='$userref' and ref='$unsubscribe'");
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>
