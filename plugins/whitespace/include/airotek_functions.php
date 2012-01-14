<?php

function get_projects($order_by="name",$sort="ASC"){
	return sql_query("select * from airotek_projects order by $order_by $sort");
}

function get_user_projects($userref,$order_by="name",$sort="ASC"){
	$userrefpart="where user='$userref'";
	if (checkperm('v')){$userrefpart="";}
	return sql_query ("select * from airotek_projects $userrefpart order by $order_by $sort");
}

function get_project($ref){
$project= sql_query ("select * from airotek_projects where ref=$ref");
	return $project[0];
}

function save_project($ref){
	$name=getvalescaped("name","");
	$field=getvalescaped("field","");echo $field;
	
	$assign=getval("assign","");
	$due=getvalescaped("due","");
	$notes=getvalescaped("notes","");

	sql_query("update airotek_projects set name='".$name."',field=$field,user='".$assign."',due='".$due."',notes='".$notes."' where ref=$ref"); 
}

function create_project($ref,$name){
	sql_query("insert into airotek_projects (name,created) values ('".escape_check($name)."',now())");
	return sql_insert_id();
}

function delete_project($ref){
	sql_query("delete from airotek_projects where ref=$ref");
	}
