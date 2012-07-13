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

function HookAnnotateAllCollectiontoolcompact1($collection, $count_result,$cinfo,$colresult){
	# Link in collections bar (minimised)
	global $lang,$pagename,$annotate_pdf_output,$annotate_pdf_output_only_annotated;
	if (!$annotate_pdf_output || $count_result==0){return false;}
	
	// check if this tool should be available based on annotation_counts. 
	$annotations=true;
	if ($annotate_pdf_output_only_annotated){
		// check if there are annotations in this collection
		$annotations=false;
		for($n=0;$n<count($colresult);$n++){
			if ($colresult[$n]['annotation_count']!=0){
				$annotations=true;
				break;
			}
		}
	}
	if (!$annotations){return false;}?>
    
    <option value="<?php echo $collection?>|0|0|../plugins/annotate/pages/annotate_pdf_config.php?col=<?php echo $collection ?>|main|false">&gt;&nbsp;<?php echo $lang['pdfwithnotes']?>...</option><?php
}


?>
