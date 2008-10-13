<?
include "include/db.php";
include "include/collections_functions.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("collection",""),$k))) {include "include/authenticate.php";}

include "include/general.php";
include "include/research_functions.php";
include "include/resource_functions.php";
include "include/search_functions.php";

$collection=getvalescaped("collection","");
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

# Process adding of items
$add=getvalescaped("add","");
if ($add!="")
	{
	hook("preaddtocollection");
	#add to current collection
	if (add_resource_to_collection($add,$usercollection)==false)
		{ ?><script language="Javascript">alert("<?=$lang["cantmodifycollection"]?>");</script><? };
	
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
		{ ?><script type="text/javascript">alert("<?=$lang["cantmodifycollection"]?>");</script><? };

	if (getval("pagename","")=="search")
		{
		# Removing items from the search page - reload the page to refresh the search results.
		?>
		<script type="text/javascript">window.location.reload();</script>
		<?
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
<div id="CollectionFramelessDropTitle"><?=$lang["currentcollection"]?>:&nbsp;</div>				
<div id="CollectionFramelessDrop">

<select id="colselect" name="collection" class="SearchWidth" onchange="ChangeCollection(document.getElementById('colselect').value);">
<?
$found=false;
$list=get_user_collections($userref);
for ($n=0;$n<count($list);$n++)
	{
	?>
	<option value="<?=$list[$n]["ref"]?>" <? if ($usercollection==$list[$n]["ref"]) {?> selected<? $found=true;}?>><?=htmlspecialchars($list[$n]["name"])?></option>
	<?
	}
if ($found==false)
	{
	# Add this one at the end, it can't be found
	$notfound=get_collection($usercollection);
	?>
	<option selected><?=$notfound["name"]?></option>
	<?
	}
?>
<option value="-1">(<?=$lang["createnewcollection"]?>)</option>
</select>
		
</div>

<strong><?=count($result)?></strong>&nbsp;<?=$lang["resourcesincollection"]?>