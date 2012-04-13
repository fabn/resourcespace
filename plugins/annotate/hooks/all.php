<?php 

function HookAnnotateAllModifyselect(){
return (" ,r.annotation_count ");

}


function HookAnnotateAllRemoveannotations(){
	global $ref;
	sql_query("delete from annotate_notes where ref='$ref'");
	sql_query("update resource set annotation_count=0 where ref='$ref'");	
	sql_query("delete from resource_keyword where resource='$ref' and annotation_ref>0");;
}

function HookAnnotateAllCollectiontoolcompact(){
	# Link in collections bar (minimised)
	global $collection,$lang,$pagename,$annotate_pdf_output;
	if (!$annotate_pdf_output){return false;}?>
    <option value="<?php echo $collection?>|0|0|../plugins/annotate/pages/annotate_pdf_config.php?col=<?php echo $collection ?>|main|false">&gt;&nbsp;<?php echo $lang['pdfwithnotes']?></option><?php
}


?>
