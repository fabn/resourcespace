<?php 

function HookAnnotateAllModifyselect(){
return (" ,r.annotation_count ");

}


function HookAnnotateAllRemoveannotations(){
	global $ref;
	sql_query("delete from annotate_notes where ref='$ref'");
	sql_query("update resource set annotation_count=0 where ref='$ref'");
}
?>
