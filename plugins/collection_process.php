<?
# Timelist collection functionality
# Dan Huby for Mediaset, 7 November 2007

if (getval("addtimelist","")!="")
	{
	$timelist=getvalescaped("addtimelist","");
	$resource=getvalescaped("addtimelistresource","");

	# Delete any existing row fist.
	sql_query("delete from collection_timelist where collection='$usercollection' and resource='$resource' and timecode_in='$timelist' limit 1");

	sql_query("insert into collection_timelist (collection,resource,timecode_in,description,added) values ('$usercollection','$resource','$timelist','" . getvalescaped("addtimelistdescription","") . "',now())");
	}


if (getval("removetimelist","")!="")
	{
	$timelist=getvalescaped("removetimelist","");
	$resource=getvalescaped("removetimelistresource","");

	sql_query("delete from collection_timelist where collection='$usercollection' and resource='$resource' and timecode_in='$timelist' limit 1");
	}



?>