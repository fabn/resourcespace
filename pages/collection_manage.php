<?php 
include "../include/db.php";
include "../include/authenticate.php"; if (checkperm("b")){exit("Permission denied");}
#if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/search_functions.php";
include "../include/resource_functions.php";

$offset=getvalescaped("offset",0);
$find=getvalescaped("find","");
$col_order_by=getvalescaped("col_order_by","created");

$collection_valid_order_bys=array("fullname","name","ref","count","public");
$modified_collection_valid_order_bys=hook("modifycollectionvalidorderbys");
if ($modified_collection_valid_order_bys){$collection_valid_order_bys=$modified_collection_valid_order_bys;}
if (!in_array($col_order_by,$collection_valid_order_bys)) {$col_order_by="created";} # Check the value is one of the valid values (SQL injection filter)

$sort=getval("sort","ASC");
$revsort = ($sort=="ASC") ? "DESC" : "ASC";
# pager
$per_page=getvalescaped("per_page_list",$default_perpage_list);setcookie("per_page_list",$per_page);

if (array_key_exists("find",$_POST)) {$offset=0;} # reset page counter when posting

$name=getvalescaped("name","");
if ($name!="")
	{
	# Create new collection
	$new=create_collection ($userref,$name);
	set_user_collection($userref,$new);
	refresh_collection_frame();
	
	# Log this
	daily_stat("New collection",$userref);
	
	redirect("pages/collection_edit.php?ref=" . $new);
	}

$delete=getvalescaped("delete","");
if ($delete!="")
	{
	# Delete collection
	delete_collection($delete);

	# Get count of collections
	$c=get_user_collections($userref);
	
	# If the user has just deleted the collection they were using, select a new collection
	if ($usercollection==$delete && count($c)>0)
		{
		# Select the first collection in the dropdown box.
		$usercollection=$c[0]["ref"];
		set_user_collection($userref,$usercollection);
		}

	# User has deleted their last collection? add a new one.
	if (count($c)==0)
		{
		# No collections to select. Create them a new collection.
		$name=get_mycollection_name($userref);
		$usercollection=create_collection ($userref,$name);
		set_user_collection($userref,$usercollection);
		}

	refresh_collection_frame($usercollection);
	}

$removeall=getvalescaped("removeall","");
if ($removeall!=""){
	remove_all_resources_from_collection($removeall);
	refresh_collection_frame($usercollection);
}

$remove=getvalescaped("remove","");
if ($remove!="")
	{
	# Remove someone else's collection from your My Collections
	remove_collection($userref,$remove);
	
	# Get count of collections
	$c=get_user_collections($userref);
	
	# If the user has just removed the collection they were using, select a new collection
	if ($usercollection==$remove && count($c)>0) {
		# Select the first collection in the dropdown box.
		$usercollection=$c[0]["ref"];
		set_user_collection($userref,$usercollection);
	}
	
	refresh_collection_frame();
	}

$add=getvalescaped("add","");
if ($add!="")
	{
	# Add someone else's collection to your My Collections
	add_collection($userref,$add);
	set_user_collection($userref,$add);
	refresh_collection_frame();
	
   	# Log this
	daily_stat("Add public collection",$userref);
	}

$reload=getvalescaped("reload","");
if ($reload!="")
	{
	# Refresh the collection frame (just edited a collection)
	refresh_collection_frame();
	}

$purge=getvalescaped("purge","");
$deleteall=getvalescaped("deleteall","");
if ($purge!="" || $deleteall!="") {
	
	if ($purge!=""){$deletecollection=$purge;}
	if ($deleteall!=""){$deletecollection=$deleteall;}
	
	if (!function_exists("do_search")) {
		include "../include/search_functions.php";
	}
	
	if (!function_exists("delete_resource")) {
		include "../include/resource_functions.php";
	}
	
	# Delete all resources in collection
	if (!checkperm("D")) {
		$resources=do_search("!collection" . $deletecollection);
		for ($n=0;$n<count($resources);$n++) {
			if (checkperm("e" . $resources[$n]["archive"])) {
				delete_resource($resources[$n]["ref"]);	
				collection_log($deletecollection,"D",$resources[$n]["ref"]);
			}
		}
	}
	
	if ($purge!=""){
		# Delete collection
		delete_collection($purge);
		# Get count of collections
		$c=get_user_collections($userref);
		
		# If the user has just deleted the collection they were using, select a new collection
		if ($usercollection==$purge && count($c)>0) {
			# Select the first collection in the dropdown box.
			$usercollection=$c[0]["ref"];
			set_user_collection($userref,$usercollection);
		}
	
		# User has deleted their last collection? add a new one.
		if (count($c)==0) {
			# No collections to select. Create them a new collection.
			$name=get_mycollection_name($userref);
			$usercollection=create_collection ($userref,$name);
			set_user_collection($userref,$usercollection);
		}
	}
	refresh_collection_frame($usercollection);
}

$deleteempty=getvalescaped("deleteempty","");
if ($deleteempty!="") {
		
	$collections=get_user_collections($userref);
	$deleted_usercoll = false;
		
	for ($n = 0; $n < count($collections); $n++) {
		// if count is zero and not My Collection and collection is owned by user:
		if ($collections[$n]['count'] == 0 && $collections[$n]['cant_delete'] != 1 && $collections[$n]['user']==$userref) {
			delete_collection($collections[$n]['ref']);
			if ($collections[$n]['ref'] == $usercollection) {
				$deleted_usercoll = true;
			}
		}
				
	}
		
	# Get count of collections
	$c=get_user_collections($userref);
		
	# If the user has just deleted the collection they were using, select a new collection
	if ($deleted_usercoll && count($c)>0) {
		# Select the first collection in the dropdown box.
		$usercollection=$c[0]["ref"];
		set_user_collection($userref,$usercollection);
	}
	
	# User has deleted their last collection? add a new one.
	if (count($c)==0) {
		# No collections to select. Create them a new collection.
		$name=get_mycollection_name($userref);
		$usercollection=create_collection ($userref,$name);
		set_user_collection($userref,$usercollection);
	}
	
	refresh_collection_frame($usercollection);
}

$removeall=getvalescaped("removeall","");
if ($removeall!=""){
	remove_all_resources_from_collection($removeall);
	refresh_collection_frame($usercollection);
}


include "../include/header.php";
?>
  <div class="BasicsBox">
    <h2>&nbsp;</h2>
    <h1><?php echo $lang["managemycollections"]?></h1>
    <p class="tight"><?php echo text("introtext")?></p><br>
<div class="BasicsBox">
    <form method="post">
		<div class="Question">
			<div class="tickset">
			 <div class="Inline"><input type=text name="find" id="find" value="<?php echo htmlspecialchars(getval("find","")) ?>" maxlength="100" class="shrtwidth" /></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["searchbutton"]?>&nbsp;&nbsp;" /></div>
			</div>
			<div class="clearerleft"> </div>
		</div>
	</form>
</div>
<?php

$collections=get_user_collections($userref,$find,$col_order_by,$sort);

$results=count($collections);
$totalpages=ceil($results/$per_page);
$curpage=floor($offset/$per_page)+1;
$jumpcount=1;

# Create an a-z index
$atoz="<div class=\"InpageNavLeftBlock\">";
if ($find=="") {$atoz.="<span class='Selected'>";}
$atoz.="<a href=\"collection_manage.php?col_order_by=name&find=\">" . $lang["viewall"] . "</a>";
if ($find=="") {$atoz.="</span>";}
$atoz.="&nbsp;&nbsp;&nbsp;&nbsp;";
for ($n=ord("A");$n<=ord("Z");$n++)
	{
	if ($find==chr($n)) {$atoz.="<span class='Selected'>";}
	$atoz.="<a href=\"collection_manage.php?col_order_by=name&find=" . chr($n) . "\">&nbsp;" . chr($n) . "&nbsp;</a> ";
	if ($find==chr($n)) {$atoz.="</span>";}
	$atoz.=" ";
	}
$atoz.="</div>";

$url="collection_manage.php?paging=true&col_order_by=".$col_order_by."&sort=".$sort."&find=".urlencode($find)."";

	?><div class="TopInpageNav"><?php echo $atoz?> <div class="InpageNavLeftBlock"><?php echo $lang["resultsdisplay"]?>:
  	<?php 
  	for($n=0;$n<count($list_display_array);$n++){?>
  	<?php if ($per_page==$list_display_array[$n]){?><span class="Selected"><?php echo $list_display_array[$n]?></span><?php } else { ?><a href="<?php echo $url; ?>&per_page_list=<?php echo $list_display_array[$n]?>"><?php echo $list_display_array[$n]?></a><?php } ?>&nbsp;|
  	<?php } ?>
  	<?php if ($per_page==99999){?><span class="Selected"><?php echo $lang["all"]?></span><?php } else { ?><a href="<?php echo $url; ?>&per_page_list=99999"><?php echo $lang["all"]?></a><?php } ?>
  	</div> <?php pager(false); ?></div><?php	
?>

<form method=post id="collectionform">
<input type=hidden name="delete" id="collectiondelete" value="">
<input type=hidden name="remove" id="collectionremove" value="">
<input type=hidden name="add" id="collectionadd" value="">

<?php

// count how many collections are owned by the user versus just shared, and show at top
$mycollcount = 0;
$othcollcount = 0;
for($i=0;$i<count($collections);$i++){
	if ($collections[$i]['user'] == $userref){
		$mycollcount++;
	} else {
		$othcollcount++;
	}
}

$collcount = count($collections);
echo $collcount==1 ? $lang["total-collections-1"] : str_replace("%number", $collcount, $lang["total-collections-2"]);
echo " " . ($mycollcount==1 ? $lang["owned_by_you-1"] : str_replace("%mynumber", $mycollcount, $lang["owned_by_you-2"])) . "<br />";
# The number of collections should never be equal to zero.
?>

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<tr class="ListviewTitleStyle">
<td><?php if ($col_order_by=="name") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&col_order_by=name&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["collectionname"]?></a><?php if ($col_order_by=="name") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($col_order_by=="fullname") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&col_order_by=fullname&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["owner"]?></a><?php if ($col_order_by=="fullname") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($col_order_by=="ref") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&col_order_by=ref&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["id"]?></a><?php if ($col_order_by=="ref") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($col_order_by=="created") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&col_order_by=created&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["created"]?></a><?php if ($col_order_by=="created") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<td><?php if ($col_order_by=="count") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&col_order_by=count&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["itemstitle"]?></a><?php if ($col_order_by=="count") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td>
<?php if (!$hide_access_column){ ?><td><?php if ($col_order_by=="public") {?><span class="Selected"><?php } ?><a href="collection_manage.php?offset=0&col_order_by=public&sort=<?php echo $revsort?>&find=<?php echo urlencode($find)?>"><?php echo $lang["access"]?></a><?php if ($col_order_by=="public") {?><div class="<?php echo $sort?>">&nbsp;</div><?php } ?></td><?php }?>
<?php hook("beforecollectiontoolscolumnheader");?>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>
<form method="get" name="colactions" id="colactions">
<?php

for ($n=$offset;(($n<count($collections)) && ($n<($offset+$per_page)));$n++)
	{
    $colusername=$collections[$n]['fullname'];

	?><tr <?php hook("collectionlistrowstyle");?>>
	<td><div class="ListTitle">
	<?php if (!isset($collections[$n]['savedsearch'])||(isset($collections[$n]['savedsearch'])&&$collections[$n]['savedsearch']==null)){$collection_tag="";} else {$collection_tag=$lang['smartcollection'].": ";}?>
			<a <?php if ($collections[$n]["public"]==1 && (strlen($collections[$n]["theme"])>0)) { ?>style="font-style:italic;"<?php } ?> href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>"><?php echo $collection_tag. highlightkeywords(i18n_get_translated($collections[$n]["name"]),$find)?></a></div></td>
	<td><?php echo highlightkeywords($colusername,$find)?></td>
	<td><?php echo highlightkeywords($collection_prefix . $collections[$n]["ref"],$find)?></td>
	<td><?php echo nicedate($collections[$n]["created"],true)?></td>
	<td><?php echo $collections[$n]["count"]?></td>
<?php if (! $hide_access_column){ ?>	<td><?php
# Work out the correct access mode to display
if (!hook('collectionaccessmode')) {
	if ($collections[$n]["public"]==0){
		echo $lang["private"];
	}
	else{
		if (strlen($collections[$n]["theme"])>0){
			echo $lang["theme"];
		}
	else{
		echo $lang["public"];
		}
	}
}
?></td><?php
}?>
<?php hook("beforecollectiontoolscolumn");?>
	<td>	
        <div class="ListTools">
        <?php if ($collections_compact_style){
        include("collections_compact_style.php"); } else {
?><a href="search.php?search=<?php echo urlencode("!collection" . $collections[$n]["ref"])?>">&gt;&nbsp;<?php echo $lang["viewall"]?></a>
	&nbsp;<a <?php if ($frameless_collections && !checkperm("b")){ ?>href onclick="ChangeCollection(<?php echo $collections[$n]["ref"]?>);"
		<?php } elseif ($autoshow_thumbs) {?>onclick=" top.document.getElementById('topframe').rows='*<?php if ($collection_resize!=true) {?>,3<?php } ?>,138'; return true;"
		href="collections.php?collection=<?php echo $collections[$n]["ref"]?>&amp;thumbs=show" target="collections"
		<?php } else {?>href="collections.php?collection=<?php echo $collections[$n]["ref"]?>" target="collections"<?php }?>>&gt;&nbsp;<?php echo $lang["action-select"]?></a>
	<?php if (isset($zipcommand) || $collection_download) { ?>
	&nbsp;<a href="collection_download.php?collection=<?php echo $collections[$n]["ref"]?>"
	>&gt;&nbsp;<?php echo $lang["action-download"]?></a>
	<?php } ?>
	
	<?php if ($contact_sheet==true && $manage_collections_contact_sheet_link) { ?>
    &nbsp;<a href="contactsheet_settings.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["contactsheet"]?></a>
	<?php } ?>

	<?php if ($manage_collections_share_link && $allow_share && (checkperm("v") || checkperm ("g"))) { ?> &nbsp;<a href="collection_share.php?ref=<?php echo $collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["share"]?></a><?php } ?>
	
	<?php if ($manage_collections_remove_link && $username!=$collections[$n]["username"])	{?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["removecollectionareyousure"]?>')) {document.getElementById('collectionremove').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-remove"]?></a><?php } ?>

	<?php if ((($username==$collections[$n]["username"]) || checkperm("h")) && ($collections[$n]["cant_delete"]==0)) {?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["collectiondeleteconfirm"]?>')) {document.getElementById('collectiondelete').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-delete"]?></a><?php } ?>

	<?php if ($collection_purge){ 
		if ($n == 0) {
			?><input type=hidden name="purge" id="collectionpurge" value=""><?php 
		}

		if (checkperm("e0") && $collections[$n]["cant_delete"] == 0) {
			?>&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["purgecollectionareyousure"]?>')) {document.getElementById('collectionpurge').value='<?php echo $collections[$n]["ref"]?>';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["purgeanddelete"]?></a><?php 
		} 
	}
	?>
	<?php hook('additionalcollectiontool') ?>

	<?php if (($username==$collections[$n]["username"]) || (checkperm("h"))) {?>&nbsp;<a href="collection_edit.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?></a><?php } ?>

    <?php if ((($username==$collections[$n]["username"]) || (checkperm("h"))) && $collection_sorting) {?>&nbsp;<a href="collection_sort.php?collection=<?php echo $collections[$n]['ref'] ?>">&gt;&nbsp;<?php echo $lang["sort"]?></a><?php } ?>

    <?php     
    # If this collection is (fully) editable, then display an edit all link
    if (($collections[$n]["count"] > 0) && allow_multi_edit($collections[$n]["ref"]) && $show_edit_all_link ) { ?>&nbsp;<a href="edit.php?collection=<?php echo $collections[$n]["ref"]?>" target="main">&gt;&nbsp;<?php echo $lang["action-editall"]?></a>&nbsp;<?php } ?>

	<?php if (($username==$collections[$n]["username"]) || (checkperm("h"))) {?><a href="collection_log.php?ref=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["log"]?></a><?php } ?>

	<?php hook("addcustomtool"); ?>
	
	</td>
	</tr><?php
	}
}

	?>
	<input type=hidden name="deleteempty" id="collectiondeleteempty" value="">
	
	<?php if ($collections_delete_empty){
        $use_delete_empty=false;
        //check if delete empty is relevant
        foreach ($collections as $collection){
            if ($collection['count']==0){$use_delete_empty=true;}
        }
        if ($use_delete_empty){
        ?>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><?php if (!$hide_access_column){?><td>&nbsp;</td><?php } ?><?php hook("addcollectionmanagespacercolumn");?><td><div class="ListTools">&nbsp;<a href="#" onclick="if (confirm('<?php echo $lang["collectionsdeleteemptyareyousure"]?>')) {document.getElementById('collectiondeleteempty').value='yes';document.getElementById('collectionform').submit();} return false;">&gt;&nbsp;<?php echo $lang["collectionsdeleteempty"]?></a></div></td></tr>
        <?php }
    }
?>
</table>
</div>

</form>
<div class="BottomInpageNav"><?php pager(false); ?></div>
</div>

<!--Create a collection-->
<div class="BasicsBox">
    <h1><?php echo $lang["createnewcollection"]?></h1>
    <p class="tight"><?php echo text("newcollection")?></p>
    <form method="post">
		<div class="Question">
			<label for="newcollection"><?php echo $lang["collectionname"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="name" id="newcollection" value="" maxlength="100" class="shrtwidth"></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
		<div class="clearerleft"> </div>
	    </div>
	</form>
</div>

<!--Find a collection-->
<?php if (!$public_collections_header_only){?>
<?php if($enable_public_collections){?>
<div class="BasicsBox">
    <h1><?php echo $lang["findpubliccollection"]?></h1>
    <p class="tight"><?php echo text("findpublic")?></p>
    <p><a href="collection_public.php"><?php echo $lang["findpubliccollection"]?>&nbsp;&gt;</a></p>
</div>
<?php } ?>
<?php } ?>
<?php		
include "../include/footer.php";
?>
