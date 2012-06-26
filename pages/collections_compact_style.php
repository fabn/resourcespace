<?php
// this page is a little complicated, but it is a single list of tools for max/min collections, Search view on a collection, view_resource_collections in View Page, and Collection Manager, so in that sense it should be easier to maintain consistency.

// value for each option provides the action to perform in detail:

// ref - for multiselector pages, colactionselect needs to have a collection number suffixed.
// confirmation [0 or string] - accept or reject the action (should be a valid lang)
// actionpage - [0 or string] - this page will be executed via ajax (optional, only if you need a background action)
// redirect - [0 or string] - redirect to this page after completion of the action
// frame [main or collections] - which frame to redirect to (main or collections)
// refresh [collections, main, both, or false]

include_once("../include/search_functions.php");
include_once("../include/resource_functions.php");
include_once("../include/collections_functions.php");
global $baseurl;

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$col_order_by=getvalescaped("col_order_by","name");
$order_by=getvalescaped("order_by","relevance");
$sort=getval("sort","ASC");
$main_pages=array("search","collection_manage","collection_public","themes");


// get collection information (different pages need different treatment) 
if (($pagename=="search" || $pagename=="preview_all") && isset($search) && substr($search,0,11)=="!collection"){
    $collection=explode(",",substr($search,11));
    $collection=$collection[0]; 
    $colresult=do_search("!collection" . $collection);
	}
else if ($pagename=="collection_manage" || $pagename=="collection_public" || $pagename=="view"){
    $collection=$collections[$n]['ref'];
    $colresult=do_search("!collection" . $collection);
    $cinfo=get_collection($collection);
    $feedback=$cinfo["request_feedback"];    
    }
elseif ($pagename=="themes"){
    $n=$m;
    $collections=$getthemes;
    $collection=$getthemes[$m]["ref"];
    $colresult=do_search("!collection" . $collection);
    $cinfo=get_collection($collection);
    $feedback=$cinfo["request_feedback"];
    $k="";
    }    
else if ($pagename=="collections"){
    $collection=$usercollection;$colresult=$result;
}
if ($pagename=="search" && isset($resources) && is_array($resources)){$colresult=$resources;$cinfo=get_collection($collections[$n]['ref']);$feedback=$cinfo["request_feedback"];$collection_results=true;$collection=$collections[$n]['ref'];}

$count_result=count($colresult);
// check editability

$col_editable=false;
if (count($colresult)>0 && checkperm("e" . $colresult[0]["archive"]) && allow_multi_edit($colresult)){
	$col_editable=true;
}


if ($pagename!="collection_manage" && $pagename!="collection_public" && $pagename!="themes"){?>
<form method="get" name="colactions" id="colactions">
<?php } 


if ($pagename=="search" || $pagename=="collections"){?>

<?php hook("beforecollectiontoolscolumn");?>
<?php if (!hook("modifycompacttoolslabel")){ echo $lang['tools'].":";} ?> <?php if (getval("thumbs","")=="show" && $pagename=="collections"){?><br><?php } ?>
<?php } ?>


<select <?php if ($pagename=="collections"){
	if ($thumbs=="show"){
		?>style="padding:0;margin:0px;"<?php 
	} if ($collection_dropdown_user_access_mode){
		?>class="SearchWidthExp" style="margin:0;"<?php 
		} else { 
			?> class="SearchWidth" style="margin:0;"<?php 
		} 
	}
if ($pagename=='collection_manage' || $pagename=='collection_public' || $pagename==='themes' || $pagename=="view" || isset($collection_results)){ $colvalue="colactionselect".$collections[$n]['ref'].".value"; } else { $colvalue="colactions.colactionselect.value"; }?> class="ListDropdown" name="colactionselect<?php if ($pagename=='collection_manage' || $pagename=='collection_public' || $pagename=='themes' || $pagename=="view" || isset($collection_results)){echo $collections[$n]['ref'];}?>" <?php if ($pagename=="search" && $display=="xlthumbs"){?>style="margin:-5px 0px 0px 5px"<?php } ?> <?php if ($pagename=="search" && ( $display=="thumbs" || $display=="smallthumbs")){?>style="margin:-5px 0px 0px 4px "<?php } ?> onchange="colAction(<?php echo $colvalue?>);<?php echo $colvalue?>='';">

 
<option id="resetcolaction" value=""><?php echo $lang['select'];?></option>
 
<?php ob_start();?>

<!-- viewall *-->
<?php if ($pagename!="search" && $count_result>0){
    ?><option value="<?php echo $collection?>|0|0|<?php echo $baseurl?>/pages/search.php?search=<?php echo urlencode("!collection" . $collection)?>|main|false">&gt;&nbsp;<?php echo $lang["viewall"]?></option>
<?php } ?>
<!-- end viewall -->

<!-- preview all *-->
<?php if ($preview_all && $count_result>0){?>
<option value="<?php echo $collection?>|0|0|preview_all.php?ref=<?php echo $collection?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>&backto=<?php if (in_array($pagename,$main_pages)){echo $pagename;}?>|main|false">&gt;&nbsp;<?php echo $lang["preview_all"]?></option>
<?php } ?>
<!-- end preview_all -->

<?php 
hook("collectiontoolcompact","",array("collection"=>$collection,"count_result"=>$count_result,"cinfo"=>$cinfo));
?>

<?php 
hook("collectiontoolcompact2","",array("collection"=>$collection,"count_result"=>$count_result,"cinfo"=>$cinfo));
?>

<?php if (strpos(ob_get_contents(),"option")!==false){?>
<option id="compacttoolsspacer"></option>
<?php ob_start();} ?>

<!-- select collection *-->
<?php if (!checkperm("b") &&  $pagename!="collections"){?><option id="selectcollection" value="<?php echo $collection?>|0|0|collections.php?collection=<?php echo $collection?>|collections|false">&gt;&nbsp;<?php echo $lang['selectcollection'];?></option>
<?php } ?>
<!-- end select collection -->

 <!-- add to my collections (for public and themed) *-->
<?php if (!checkperm("b") && $userref!=$cinfo["user"] && ($pagename=="collection_public" || $pagename=="themes" || $pagename=="themes"))	{?>&nbsp;<option id="addcollection" value="<?php echo $collection?>|0|0|collections.php?addcollection=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|collections|false">&gt;&nbsp;<?php echo $lang["addtomycollections"]?></option>
<?php } ?>

 <!--remove -->
<?php if (!checkperm("b") && $userref!=$cinfo["user"] && ($pagename=="collection_manage" || $pagename=="collections" || $pagename=="search"))	{?>&nbsp;<option id="remove" value="<?php echo $collection?>|0|collection_manage.php?remove=<?php echo $collection?>|<?php if (in_array($pagename,$main_pages)){echo $pagename.'.php?offset='.$offset.'&col_order_by='.$col_order_by.'&sort='.$sort.'&find='.urlencode($find);} else { echo 'collections.php';}?>|<?php if (in_array($pagename,$main_pages)){echo 'main';} else { echo 'collections';}?>|collections">&gt;&nbsp;<?php echo $lang["action-remove"]?></option>
<?php } ?>
<!-- end remove -->

<!-- feedback -->
<?php if ($feedback) {?>
<option value="<?php echo $collection?>|0|0|collection_feedback.php?collection=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|main|false">&gt;&nbsp;<?php echo $lang["sendfeedback"]?>...</option>
<?php } ?>
<!-- end feedback -->

<!-- request all -->    
<?php if (checkperm("q") && $count_result>0 )
    { 
    # Ability to request a whole collection (only if user has restricted access to any of these resources)
    $min_access=collection_min_access($colresult);
    if ($min_access!=0)
        {
        ?>
        <option value="<?php echo $collection?>|0|0|collection_request.php?ref=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|main|false">&gt;&nbsp;<?php echo 	$lang["requestall"]?>...</option>
        <?php
        }
    }
?>
<!-- end request all -->

<!-- share -->
<?php if ($allow_share && $count_result>0) { ?>
<option value="<?php echo $collection?>|0|0|collection_share.php?ref=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|main|false">&gt;&nbsp;<?php echo $lang["sharecollection"]?>...</option>
<?php } ?>
<!-- end share -->

<!-- collection download -->
    <?php if ((isset($zipcommand) || $collection_download) && $count_result>0) { ?>
    <option value="<?php echo $collection?>|0|0|terms.php?url=<?php echo urlencode("pages/collection_download.php?collection=" .  $collection )?>|main">&gt;&nbsp;<?php echo $lang["zipall"]?>...</option>
    <?php } ?>
<!-- end collection download -->

<!-- upload *-->
<?php global $top_nav_upload_type;if (checkperm("c")) {?>&nbsp;<option id="uploadtocollection" value="<?php echo $collection?>|0|0|edit.php?ref=-<?php echo $userref?>&<?php echo $top_nav_upload_type ?>=true&collection_add=<?php echo $collection?>|main|collections">&gt;&nbsp;<?php echo $lang["action-upload"]?>...</option>
<?php } ?>
<!-- end upload-->

<!-- edit collection -->
<?php if (!checkperm("b") && (!collection_is_research_request($collection)) || (!checkperm("r"))) { ?>
    <?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><option value="<?php echo $collection?>|0|0|collection_edit.php?pagename=<?php echo $pagename?>&ref=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|main|false">&gt;&nbsp;<?php echo $lang["editcollection"]?>...</option><?php } ?>
    <?php } else {
    $research=sql_value("select ref value from research_request where collection='$collection'",0);	
	?>
    <option value="<?php echo $collection?>|0|0|team/team_research.php|main|false">&gt;&nbsp;<?php echo $lang["manageresearchrequests"]?>...</option>    
    <option value="<?php echo $collection?>|0|0|team/team_research_edit.php?ref=<?php echo $research?>|main|false">&gt;&nbsp;<?php echo $lang["editresearchrequests"]?>...</option>    
<?php } ?>
<!-- end edit collection -->

<!-- delete -->
<?php if (!checkperm("b") && (($userref==$cinfo["user"]) || checkperm("h")) && ($cinfo["cant_delete"]==0)) {?>&nbsp;<option id="delete" value="<?php echo $collection?>|<?php echo $lang["collectiondeleteconfirm"]?>|collection_manage.php?delete=<?php echo $collection?>|<?php if (in_array($pagename,$main_pages)){echo $pagename.'.php?offset='.$offset.'&col_order_by='.$col_order_by.'&sort='.$sort.'&find='.urlencode($find);} else { echo 'collections.php';}?>|<?php if (in_array($pagename,$main_pages)){echo 'main';} else { echo 'collections';}?>|both">&gt;&nbsp;<?php echo $lang["action-deletecollection"];?>...</option>
<?php } ?>
<!-- end delete and remove-->

<?php if (strpos(ob_get_contents(),"option")!==false){?>
<option id="compacttoolsspacer"></option>
<?php ob_start();} ?>

<!-- edit metadata -->    
<?php # If this collection is (fully) editable, then display an extra edit all link
if ($show_edit_all_link && $count_result>0 && $col_editable) { ?>
    <option value="<?php echo $collection?>|0|0|edit.php?collection=<?php echo $collection?>|main|false">&gt;&nbsp;<?php echo $lang["action-editall"]?>...</option>
<?php } ?>
<!-- end edit metadata -->

<!-- edit previews -->
<?php if ($count_result>0 && $col_editable) { ?>
<option value="<?php echo $collection?>|0|0|collection_edit_previews.php?ref=<?php echo $collection?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>&backto=<?php if (in_array($pagename,$main_pages)){echo $pagename;}?>|main|false">&gt;&nbsp;<?php echo $lang['editcollectionresources']?>...</option>
<?php } ?>
<!-- end previews  -->

<!-- empty *-->
<?php if (!checkperm("b") && $cinfo['savedsearch']=='' && (($userref==$cinfo["user"]) || checkperm("h"))  && $count_result>0) {?>&nbsp;<option id="removeall" value="<?php echo $collection?>|<?php echo $lang["emptycollectionareyousure"]?>|collection_manage.php?removeall=<?php echo $collection?>|<?php if (in_array($pagename,$main_pages)){echo $pagename.'.php|main|both';}else {echo 'collections.php|collections|both';}?>">&gt;&nbsp;<?php echo $lang["emptycollection"]?>...</option>
<?php } ?>
<!-- end empty-->

<!-- delete resources *-->
<?php if ((checkperm("e0") || checkperm("e1") || checkperm("e2")) && !checkperm("D") && $count_result>0) {?>&nbsp;<option id="removeall" value="<?php echo $collection?>|<?php echo $lang["deleteallsure"]?>|collection_manage.php?deleteall=<?php echo $collection?>|<?php if (in_array($pagename,$main_pages)){echo $pagename.'.php|main|collections';}else {echo 'collections.php|collections|main';}?>">&gt;&nbsp;<?php echo $lang["deleteresources"]?>...</option>
<?php } ?>
<!-- end delete resources-->

<!-- purge -->
<?php if (!checkperm("b") && $collection_purge && $count_result>0){ 
    if (checkperm("e0") && $cinfo["cant_delete"] == 0) {
        ?><option id="purge" value="<?php echo $collection?>|<?php echo $lang["purgecollectionareyousure"]?>|collection_manage.php?purge=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|<?php if (in_array($pagename,$main_pages)){echo $pagename.'.php|main|collections';}else {echo 'collections.php|collections|main';}?>">&gt;&nbsp;<?php echo $lang["purgeanddelete"]?>...</option><?php 
    } 
} ?>
<!-- end purge -->

<?php if (strpos(ob_get_contents(),"option")!==false){?>
<option id="compacttoolsspacer"></option>
<?php ob_start();} ?>

<!-- contactsheet -->
<?php if ($contact_sheet==true && $count_result>0) { ?>
<option value="<?php echo $collection?>|0|0|contactsheet_settings.php?ref=<?php echo $collection?>|main|false">&gt;&nbsp;<?php echo $lang["contactsheet"]?>...</option>
<?php } ?>
<!-- end contactsheet -->

<?php hook("collectiontoolcompact1","",array("collection"=>$collection,"count_result"=>$count_result,"cinfo"=>$cinfo)); ?>


<?php if (strpos(ob_get_contents(),"option")!==false){?>
<option id="compacttoolsspacer"></option>
<?php ob_start();} ?>

<!-- log -->
<?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?>
    <option value="<?php echo $collection?>|0|0|collection_log.php?ref=<?php echo $collection?>&offset=<?php echo $offset?>&col_order_by=<?php echo $col_order_by?>&sort=<?php echo $sort?>&find=<?php echo urlencode($find)?>|main|false">&gt;&nbsp;<?php echo $lang["action-log"]?></option>
<?php } ?>
<!-- end log -->


    </select>
    <?php if ($pagename=="collections"){?><?php if ($thumbs=="show") { ?><br /><br /><a href="collections.php?thumbs=hide" onClick="ToggleThumbs();">&gt;&nbsp;<?php echo $lang["hidethumbnails"]?></a><?php } ?><?php if ($thumbs=="hide") { ?>&nbsp;&nbsp;&nbsp;<a href="collections.php?thumbs=show" onClick="ToggleThumbs();">&gt;&nbsp;<?php echo $lang["showthumbnails"]?></a><?php } ?></div><?php } ?>
<?php if ($pagename!="collection_manage" && $pagename!="collection_public" && $pagename!="themes"){?>

</form>

<?php } ?>
