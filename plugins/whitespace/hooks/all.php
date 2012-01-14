<?php
function HookAirotekAllModifyomitsearchbarpages(){
	return array("tag","tag_projects", "tag_project", "tag_project_manage","preview_all","search_advanced","preview","admin_header");
}

function HookAirotekAllReplacetagginglink(){
	global $speedtagging,$target,$baseurl,$lang; 
	if ($speedtagging && checkperm("s") && checkperm("n")) { ?><li class="subnav"><a target="<?php echo $target?>" href="<?php echo $baseurl?>/plugins/airotek/pages/tag_projects.php"><?php echo $lang["tagging"]?>2</a></li><?php } 
	return true;
}
