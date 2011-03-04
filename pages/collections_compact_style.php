<?php
// create a compact collections actions selector
?>
<input type=hidden name="purge" id="collectionpurge" value="">
<style type="text/css">
#CollectionMinRightNav{float: right;margin: 4px 25px 0px 0px;}
</style>
<form method="get" name="colactions" id="colactions">
<div class="SearchItem" style="padding:0;margin:0;"><?php echo $lang['actions']?>:
<?php if ($pagename!="search"){
    ?><select <?php if ($thumbs=="show"){?>style="padding:0;margin:0px;"<?php } ?> class="SearchWidth" name="colactionselect" onchange="if (colactions.colactionselect.options[selectedIndex].id=='purge'){ if (!confirm('<?php echo $lang["purgecollectionareyousure"]?>')){colactions.colactionselect.value='';return false;}} if (colactions.colactionselect.options[selectedIndex].value!=''){top.main.location.href=colactions.colactionselect.options[selectedIndex].value;} colactions.colactionselect.value=''";>
    <?php }
else { ?>
 <select class="SearchWidth" name="colactionselect" onchange="if (colactions.colactionselect.options[selectedIndex].id=='purge'){ if (!confirm('<?php echo $lang["purgecollectionareyousure"]?>')){colactions.colactionselect.value='';return false;}}if (colactions.colactionselect.options[selectedIndex].value!=''){if (colactions.colactionselect.options[selectedIndex].id=='selectcollection'){parent.collections.location.href=colactions.colactionselect.options[selectedIndex].value;}else {top.main.location.href=colactions.colactionselect.options[selectedIndex].value;} } colactions.colactionselect.value='';">
 <?php }?>
<option id="resetcolaction" value=""><?php echo $lang['select'];?></option>
<?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
	<?php if ($contact_sheet==true && $collections_compact_style) { ?><option value="contactsheet_settings.php?ref=<?php echo $usercollection?>"><?php echo $lang["contactsheet"]?></option><?php } ?>
    <?php if ($allow_share) { ?><option value="collection_share.php?ref=<?php echo $usercollection?>"><?php echo $lang["share"]?></option><?php } ?>
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><option value="collection_edit.php?ref=<?php echo $usercollection?>"><?php echo $allow_share?$lang["action-edit"]:$lang["editcollection"]?></option><?php } ?>
	<?php if ($preview_all){?><option value="preview_all.php?ref=<?php echo $usercollection?>"><?php echo $lang["preview_all"]?></option><?php } ?>
    <?php if ($feedback) {?><option value="collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>"><?php echo $lang["sendfeedback"]?></option><?php } ?>
    <?php } else {
    $research=sql_value("select ref value from research_request where collection='$usercollection'",0);	
	?>
    <option value="team/team_research.php"><?php echo $lang["manageresearchrequests"]?></option>    
    <option value="team/team_research_edit.php?ref=<?php echo $research?>"><?php echo $lang["editresearchrequests"]?></option>    
	<?php } ?>
    
    <?php 
    # If this collection is (fully) editable, then display an extra edit all link
    if ((count($result)>0) && checkperm("e" . $result[0]["archive"]) && allow_multi_edit($usercollection)) { ?>
    <option value="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>"><?php echo $lang["viewall"]?></option>
    <option value="edit.php?collection=<?php echo $usercollection?>"><?php echo $lang["action-editall"]?></option>

    <?php } else { ?>
    <option value="search.php?search=<?php echo urlencode("!collection" . $usercollection)?>"><?php echo $lang["viewall"]?></option>
    <?php } ?>
    
    <?php if ($count_result>0)
    	{ 
		# Ability to request a whole collection (only if user has restricted access to any of these resources)
		$min_access=collection_min_access($usercollection);
		if ($min_access!=0)
			{
		    ?>
		    <option value="collection_request.php?ref=<?php echo $usercollection?>&k=<?php echo $k?>"><?php echo 	$lang["requestall"]?></option>
		    <?php
		    }
	    }
	?>
    
   	<?php if (isset($zipcommand)) { ?>
    <option value="terms.php?k=<?php echo $k?>&url=<?php echo urlencode("pages/collection_download.php?collection=" .  $usercollection . "&k=" . $k)?>"><?php echo $lang["action-download"]?></option>
	<?php } ?>
    <?php if ($collection_purge){ 
        if (checkperm("e0") && $cinfo["cant_delete"] == 0) {
			?><option id="purge" value="collection_manage.php?purge=<?php echo $cinfo['ref']?>"><?php echo $lang["purgeanddelete"]?></option><?php 
		} 
	}
	?>
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><option value="collection_log.php?ref=<?php echo $cinfo["ref"]?>"><?php echo $lang["log"]?></option><?php } ?>
    <?php hook("collectiontoolcompact");?>
    </select>
</form>
    <?php if ($pagename!="search"){?>
        <?php if ($thumbs=="show") { ?><br /><br /><a href="collections.php?thumbs=hide" onClick="ToggleThumbs();">&gt;&nbsp;<?php echo $lang["hidethumbnails"]?></a><?php } ?>
        <?php if ($thumbs=="hide") { ?>&nbsp;&nbsp;&nbsp;<a href="collections.php?thumbs=show" onClick="ToggleThumbs();">&gt;&nbsp; <?php echo $lang["showthumbnails"]?></a><?php } ?></div>
    <?php } ?>
