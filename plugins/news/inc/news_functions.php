<?php


function get_news($ref="",$recent="",$findtext="")
	{
	# Returns a list of all news items.
	# If $find is specified a search is performed across title and body fields
	
	$findtext=trim($findtext);
	debug ($recent);
	$sql="news n ";
	
	if ($ref!="" || $findtext!=""){
		$sql.=" where (";
	}
	
	if ($ref!=""){$sql.="ref='$ref'";}
	
	if ($findtext!="") {
		$findtextarray=explode(" ",$findtext);
		if ($ref!=""){$sql.=" and (";}
		for ($n=0;$n<count($findtextarray);$n++){
		  $sql.=' body like "%'.$findtextarray[$n].'%"';
		  if ($n+1!=count($findtextarray)){$sql.=" and ";}
		}
		$sql.=") or (";
		for ($n=0;$n<count($findtextarray);$n++){
		  $sql.=' title like "%'.$findtextarray[$n].'%"';
		  if ($n+1!=count($findtextarray)){$sql.=" and ";}
		}
		if ($ref!=""){$sql.=")";}
		}
		
	if ($ref!="" || $findtext!=""){
		$sql.=" ) ";
	}
	
	$sql.=" order by date desc, ref desc";
	if ($recent!=""){$sql.=" limit 0,$recent";}
	
	return sql_query ("select distinct ref, date, title, body from $sql");
	}
	
function get_news_headlines($ref="",$recent="")
	{
	# Returns a list of news headlines.	
	$sql="news n ";
	
	if ($ref!=""){
		$sql.=" where ref='$ref'";
	}
					
	$sql.=" order by date desc, ref desc";
	if ($recent!=""){$sql.=" limit 0,$recent";}	

	return sql_query ("select distinct ref, date, title, body from $sql");
	}
	
	
function get_news_ref($maxmin)
	{
	# Returns a reference to the latest or oldest news headline.	
	$sql="news n";
	return sql_query ("select " . $maxmin ."(ref) from $sql");
	}
	
function delete_news($ref)
	{
	# Deletes the news item with reference $ref
	sql_query("delete from news where ref='$ref'");
	}
	
function add_news($date,$title,$body)
	{
	# Saves the news item with reference $ref
	sql_query("insert into news (title,body,date) values ('" . escape_check($title) . "','" . escape_check($body) . "','" . escape_check($date) . "')");
	}
	
function update_news($ref,$date,$title,$body)
	{
	# Updates the news item with reference $ref
	sql_query("update news set title='" . escape_check($title) . "', body='" . escape_check($body) . "', date='" . escape_check($date) . "' where ref='$ref'");
	}
?>