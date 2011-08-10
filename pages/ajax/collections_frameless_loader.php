<?php
include "../../include/db.php";
include "../../include/general.php";
include "../../include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection","",true),$k))) {include "../../include/authenticate.php";}

include "../../include/research_functions.php";
include "../../include/resource_functions.php";
include "../../include/search_functions.php";
	
$collection=getvalescaped("collection","",true);
if ($collection!="")
	{
	hook("prechangecollection");
	#change current collection
	
	if ($k=="" && $collection==-1)
		{
		# Create new collection
		$name=get_mycollection_name($userref);
		$new=create_collection ($userref,$name);
		set_user_collection($userref,$new);
		
		# Log this
		daily_stat("New collection",$userref);
		}
	else
		{
		# Switch the existing collection
		if ($k=="") {set_user_collection($userref,$collection);}
		$usercollection=$collection;
		}

	hook("postchangecollection");
	}

if(hook("modifyusercollection")){$usercollection=hook("modifyusercollection");} 

# Process adding of items
$add=getvalescaped("add","");
if ($add!="")
	{
	hook("preaddtocollection");
	#add to current collection
	if (add_resource_to_collection($add,$usercollection)==false)
		{ ?><script type="text/javascript">alert("<?php echo $lang["cantmodifycollection"]?>");</script><?php };
   	# Log this
	daily_stat("Add resource to collection",$add);
	
	# update resource/keyword kit count
	$search=getvalescaped("search","");
	if ((strpos($search,"!")===false) && ($search!="")) {update_resource_keyword_hitcount($add,$search);}
	hook("postaddtocollection");
	}

# Process removal of items
$remove=getvalescaped("remove","");
if ($remove!="")
	{
	hook("preremovefromcollection");
	#remove from current collection
	if (remove_resource_from_collection($remove,$usercollection)==false)
		{ ?><script type="text/javascript">alert("<?php echo $lang["cantmodifycollection"]?>");</script><?php };

	if (getval("pagename","")=="search")
		{
		# Removing items from the search page - reload the page to refresh the search results.
		?>
		<script type="text/javascript">window.location.reload();</script>
		<?php
		}
	hook("postremovefromcollection");
	}

# Load collection info.
$cinfo=get_collection($usercollection);

# Check to see if the user can edit this collection.
$allow_reorder=false;
if (($k=="") && (($userref==$cinfo["user"]) || ($cinfo["allow_changes"]==1) || (checkperm("h"))))
	{
	$allow_reorder=true;
	}

# Return the extra data.
$result=do_search("!collection" . $usercollection);
$feedback=$cinfo["request_feedback"];

?>
<!--Collection Dropdown-->	
<div id="CollectionFramelessDropTitle"><?php echo $lang["currentcollection"]?>:&nbsp;</div>				
<div id="CollectionFramelessDrop">

<select id="colselect" name="collection" class="SearchWidth" onchange="ChangeCollection(document.getElementById('colselect').value);">
<?php
$found=false;
$list=get_user_collections($userref);
for ($n=0;$n<count($list);$n++)
	{
	?>
	<option value="<?php echo $list[$n]["ref"]?>" <?php if ($usercollection==$list[$n]["ref"]) {?> selected<?php $found=true;}?>><?php echo htmlspecialchars($list[$n]["name"])?></option>
	<?php
	}
if ($found==false)
	{
	# Add this one at the end, it can't be found
	$notfound=get_collection($usercollection);
	?>
	<option selected><?php echo $notfound["name"]?></option>
	<?php
	}
?>
<option value="-1">(<?php echo $lang["createnewcollection"]?>)</option>
</select>
		
</div>

<strong><?php echo count($result)?></strong>&nbsp;<?php echo $lang["resourcesincollection"]?>
</div>

<!--Menu-->
<div id="CollectionFramelessNav">
<ul>
<?php
# If this collection is (fully) editable, then display an extra edit all link
if (allow_multi_edit($usercollection)) { ?><li class="clearerleft"><a href="<?php echo $baseurl_short?>pages/search.php?search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo isset($k)?$k:""?>">&gt; <?php echo $lang["viewall"]?></a></li>
<li><a href="<?php echo $baseurl_short?>pages/edit.php?collection=<?php echo $usercollection?>">&gt; <?php echo $lang["action-editall"]?></a></li>
</li>
<?php } else { ?>
<li><a href="<?php echo $baseurl_short?>pages/search.php?search=<?php echo urlencode("!collection" . $usercollection)?>&k=<?php echo isset($k)?$k:""?>">&gt; <?php echo $lang["viewall"]?></a></li>
<?php } ?>
<?php if ((!collection_is_research_request($usercollection)) || (!checkperm("r"))) { ?>
<?php if (checkperm("s")) { ?>
<?php if ($allow_share && (checkperm("v") || checkperm("g"))) { ?><li><a href="<?php echo $baseurl_short?>pages/collection_share.php?ref=<?php echo $usercollection?>">&gt; <?php echo $lang["share"]?></a></li><?php } ?>
<?php if (($userref==$cinfo["user"]) || (checkperm("h"))) {?><li><a href="<?php echo $baseurl_short?>pages/collection_edit.php?ref=<?php echo $usercollection?>">&gt;&nbsp;<?php echo $allow_share?$lang["action-edit"]:$lang["editcollection"]?></a></li><?php } ?>
<?php if ((($userref==$cinfo["user"]) || (checkperm("h"))) && $collection_sorting) {?><li><a href="<?php echo $baseurl_short?>pages/collection_sort.php?collection=<?php echo $usercollection?>">&gt;&nbsp;<?php echo $lang["sort"]?></a></li><?php } ?>
<?php } ?>
<?php if ($feedback) {?><li><a  href="<?php echo $baseurl_short?>pages/collection_feedback.php?collection=<?php echo $usercollection?>&k=<?php echo $k?>">&gt;&nbsp;<?php echo $lang["sendfeedback"]?></a></li><?php } ?>
<?php } else {
$research=sql_value("select ref value from research_request where collection='$usercollection'",0);
?>
<li><a href="<?php echo $baseurl_short?>pages/team_research_edit.php?ref=<?php echo $research?>">&gt;<?php echo $lang["editresearchrequests"]?></a></li>
<li><a href="<?php echo $baseurl_short?>pages/team_research.php">&gt; <?php echo $lang["manageresearchrequests"]?></a></li>
<?php } ?>
<?php if (isset($zipcommand)) { ?>
<li><a href="<?php echo $baseurl_short?>pages/collection_download.php?collection=<?php echo $usercollection?>">&gt; <?php echo $lang["action-download"]?></a></li>
<?php } ?>
<li><a href="<?php echo $baseurl_short?>pages/collection_manage.php">&gt; <?php echo $lang["managemycollections"]?></a></li>
<?php hook("addcollectionsmenu"); ?>
</ul>
</div>
</div>
