<?php
// this page is a little complicated, but it is a single list of tools for max/min collections, Search view on a collection, view_resource_collections in View Page, and Collection Manager, so in that sense it should be easier to maintain consistency.

include_once("../include/search_functions.php");

// create a compact collections actions selector
if ($pagename=="search" && isset($search) && substr($search,0,11)=="!collection"){
    $collection=substr($search,11);
} else if ($pagename=="collection_manage" || $pagename=="view"){
    $collection=$collections[$n]['ref'];
    if ($show_edit_all_link){$result=do_search("!collection" . $collection);
        $count_result=count($result);
        }
    $cinfo=get_collection($collection);
    $feedback=$cinfo["request_feedback"];    
    }
elseif ($pagename=="themes"){
    $n=$m;
    $collections=$getthemes;
    $collection=$getthemes[$m]["ref"];
    if ($show_edit_all_link){$result=do_search("!collection" . $collection);
        $count_result=count($result);
        }
    $cinfo=get_collection($collection);
    $feedback=$cinfo["request_feedback"];
    }    
else {
    $collection=$usercollection;
}

if ($pagename!="collection_manage" && $pagename!="themes"){?>
<form method="get" name="colactions" id="colactions">
<?php } ?><?php if ($pagename=="search" || $pagename=="collections"){?>
<?php echo $lang['tools']?>: <?php if (getval("thumbs","")=="show" && $pagename=="collections"){?><br><?php } ?>
<?php } ?>
<?php hook("beforecompactstyle");?>

<?php if ($pagename=="collections"){
    ?><select <?php if ($thumbs=="show"){?>style="padding:0;margin:0px;"<?php } ?> <?php if ($collection_dropdown_user_access_mode){?>class="SearchWidthExp"<?php } else { ?> class="SearchWidth"<?php } ?> name="colactionselect" onchange="if (colactions.colactionselect.options[selectedIndex].id=='purge'){ if (!confirm('<?php echo $lang["purgecollectionareyousure"]?>')){colactions.colactionselect.value='';return false;}} if (colactions.colactionselect.options[selectedIndex].value!=''){top.main.location.href=colactions.colactionselect.options[selectedIndex].value;} colactions.colactionselect.value=''";>
    <?php }
else if ($pagename=="search"){ ?>
 <select class="ListDropdown" name="colactionselect" onchange="if (colactions.colactionselect.options[selectedIndex].id=='purge'){ if (!confirm('<?php echo $lang["purgecollectionareyousure"]?>')){colactions.colactionselect.value='';return false;}}if (colactions.colactionselect.options[selectedIndex].value!=''){if (colactions.colactionselect.options[selectedIndex].id=='selectcollection'){parent.collections.location.href=colactions.colactionselect.options[selectedIndex].value;}else {top.main.location.href=colactions.colactionselect.options[selectedIndex].value;} } colactions.colactionselect.value='';">
 <?php }
 else { ?>
 <select class="ListDropdown" name="colactionselect<?php echo $collections[$n]['ref']?>" onchange="if (colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].id=='purge'){ if (!confirm('<?php echo $lang["purgecollectionareyousure"]?>')){colactionselect<?php echo $collections[$n]['ref']?>.value='';return false;}}if (colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].id=='delete'){ if (!confirm('<?php echo $lang["collectiondeleteconfirm"]?>')){colactionselect<?php echo $collections[$n]['ref']?>.value='';return false;}}if (colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].id=='remove'){ if (!confirm('<?php echo $lang["removecollectionareyousure"]?>')){colactionselect<?php echo $collections[$n]['ref']?>.value='';return false;}}if (colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].value!=''){if (colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].id=='selectcollection'){parent.collections.location.href=colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].value;}else {top.main.location.href=colactionselect<?php echo $collections[$n]['ref']?>.options[selectedIndex].value;} } colactionselect<?php echo $collections[$n]['ref']?>.value='';">
 <?php }?>
<option id="resetcolaction" value=""><?php echo $lang['select'];?></option>
<!-- select collection -->
<?php if ($pagename=="search"||$pagename=="collection_manage"){?><option id="selectcollection" value="collections.php?collection=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang['selectcollection'];?></option><?php } ?>
<!-- end select collection -->


<!-- viewall -->
<?php if ($pagename!="search"){
    ?><option value="search.php?search=<?php echo urlencode("!collection" . $collection)?>">&gt;&nbsp;<?php echo $lang["viewall"]?></option>
<?php } ?>
<!-- end viewall -->


<!-- preview all -->
<?php if ($preview_all){?>
<option value="preview_all.php?ref=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["preview_all"]?></option>
<?php } ?>
<!-- end preview_all -->


<!-- zipall -->
   	<?php if (isset($zipcommand)) { ?>
    <option value="terms.php?url=<?php echo urlencode("pages/collection_download.php?collection=" .  $collection )?>">&gt;&nbsp;<?php echo $lang["action-download"]?>...</option>
	<?php } ?>
<!-- end zipall -->


<!-- edit metadata -->    
<?php # If this collection is (fully) editable, then display an extra edit all link
if ((($pagename!="collection_manage" && $pagename!="view") || ($show_edit_all_link && ($pagename=="collection_manage" || $pagename=="view"))) && (count($result)>0) && checkperm("e" . $result[0]["archive"]) && allow_multi_edit($collection)) { ?>
    <option value="edit.php?collection=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["action-editall"]?>...</option>
<?php } ?>
<!-- end edit metadata -->


<!-- edit collection -->
<?php if ((!collection_is_research_request($collection)) || (!checkperm("r"))) { ?>
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><option value="collection_edit.php?ref=<?php echo $collection?>">&gt;&nbsp;<?php echo $allow_share?$lang["action-edit"]:$lang["editcollection"]?>...</option><?php } ?>
    <?php } else {
    $research=sql_value("select ref value from research_request where collection='$collection'",0);	
	?>
    <option value="team/team_research.php">&gt;&nbsp;<?php echo $lang["manageresearchrequests"]?>...</option>    
    <option value="team/team_research_edit.php?ref=<?php echo $research?>">&gt;&nbsp;<?php echo $lang["editresearchrequests"]?>...</option>    
<?php } ?>
<!-- end edit collection -->


<!-- contactsheet -->
<?php if ($contact_sheet==true && $collections_compact_style) { ?>
<option value="contactsheet_settings.php?ref=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?>...</option>
<?php } ?>
<!-- end contactsheet -->


<?php hook("collectiontoolcompact");?>


<!-- share -->
<?php if ($allow_share) { ?>
<option value="collection_share.php?ref=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["share"]?>...</option>
<?php } ?>
<!-- end share -->


<!-- feedback -->
<?php if ($feedback) {?>
<option value="collection_feedback.php?collection=<?php echo $collection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["sendfeedback"]?>...</option>
<?php } ?>
<!-- end feedback -->


<!-- request all -->    
<?php if (($pagename=="collection_manage" || $pagename=="view") || $count_result>0 )
    { 
    # Ability to request a whole collection (only if user has restricted access to any of these resources)
    $min_access=collection_min_access($collection);
    if ($min_access!=0)
        {
        ?>
        <option value="collection_request.php?ref=<?php echo $collection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo 	$lang["requestall"]?>...</option>
        <?php
        }
    }
?>
<!-- end request all -->



<!--collection manage only delete and remove-->
<?php if ($pagename=="collection_manage" && $userref!=$cinfo["user"])	{?>&nbsp;<option id="remove" value="collection_manage.php?remove=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["action-remove"]?></a><?php } ?>

<?php if ((($pagename=="collection_manage" && $userref==$cinfo["user"]) || checkperm("h")) && ($cinfo["cant_delete"]==0)) {?>&nbsp;<option id="delete" value="collection_manage.php?delete=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["action-delete"]?></a><?php } ?>
<!-- end collection manage only-->



<!-- purge -->
<?php if ($collection_purge){ 
    if (checkperm("e0") && $cinfo["cant_delete"] == 0) {
        ?><option id="purge" value="collection_manage.php?purge=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["purgeanddelete"]?>...</option><?php 
    } 
} ?>
<!-- end purge -->


<!-- log -->
<?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?>
    <option value="collection_log.php?ref=<?php echo $collection?>">&gt;&nbsp;<?php echo $lang["log"]?></option>
<?php } ?>
<!-- end log -->


    </select><?php if ($pagename=="collections"){?><?php if ($thumbs=="show") { ?><br /><br /><a href="collections.php?thumbs=hide" onClick="ToggleThumbs();">&gt;&nbsp;<?php echo $lang["hidethumbnails"]?></a><?php } ?><?php if ($thumbs=="hide") { ?>&nbsp;&nbsp;&nbsp;<a href="collections.php?thumbs=show" onClick="ToggleThumbs();">&gt;&nbsp;<?php echo $lang["showthumbnails"]?></a><?php } ?></div><?php } ?>
<?php if ($pagename!="collection_manage"){?>
</form>
<?php } ?>
