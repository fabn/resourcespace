<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/collections_functions.php";

# Fetch vars
$ref=getvalescaped("ref","");
$collection=get_collection($ref);

# Process deletion of access keys
if (getval("deleteaccess","")!="")
	{
	delete_collection_access_key($ref,getvalescaped("deleteaccess",""));
	}
include "../include/header.php";
?>


<div class="BasicsBox"> 
<form method=post id="collectionform">
<input type="hidden" name="deleteaccess" id="deleteaccess" value="">
<input type="hidden" name="generateurl" id="generateurl" value="">

<h1><?php echo $lang["sharecollection"]?> - <?php echo $collection["name"] ?></h1>

<div class="VerticalNav">
<ul>

<li><a href="collection_email.php?ref=<?php echo $ref?>"><?php echo $lang["emailcollection"]?></a></li>

<li><a href="collection_share.php?ref=<?php echo $ref?>&generateurl=true"><?php echo $lang["generateurl"]?></a></li>

<?php if (getval("generateurl","")!="")
{
	?>
	<p><?php echo $lang["generateurlinternal"]?></p>
	
	<p><input class="URLDisplay" type="text" value="<?php echo $baseurl?>/?c=<?php echo $ref?>">
	
	<p><?php echo $lang["generateurlexternal"]?></p>
	
	<p><input class="URLDisplay" type="text" value="<?php echo $baseurl?>/?c=<?php echo $ref?>&k=<?php echo generate_collection_access_key($ref,0,"URL")?>">
			
	<?php
}
?>
<?php hook("collectionshareoptions") ?>
</ul>
</div>

<?php if (collection_writeable($ref))
	{
	?>
	<h2><?php echo $lang["internalusersharing"]?></h2>
	<div class="Question">
	<label for="users"><?php echo $lang["attachedusers"]?></label>
	<div class="Fixed"><?php echo (($collection["users"]=="")?$lang["noattachedusers"]:htmlspecialchars($collection["users"])); ?><br /><br />
	<a href="collection_edit.php?ref=<?php echo $ref; ?>">&gt;&nbsp;<?php echo $lang["edit"];?></a>
	</div>
	<div class="clearerleft"> </div>
	</div>
	
	<p>&nbsp;</p>
	<h2><?php echo $lang["externalusersharing"]?></h2>
	<div class="Question">

	<?php
	$keys=get_collection_external_access($ref);
	if (count($keys)==0)
		{
		?>
		<p><?php echo $lang["noexternalsharing"] ?></p>
		<?php
		}
	else
		{
		?>
		<div class="Listview">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewTitleStyle">
		<td><?php echo $lang["accesskey"];?></td>
		<td><?php echo $lang["sharedby"];?></td>
		<td><?php echo $lang["sharedwith"];?></td>
		<td><?php echo $lang["lastupdated"];?></td>
		<td><?php echo $lang["lastused"];?></td>
		<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
		</tr>
		<?php
		for ($n=0;$n<count($keys);$n++)
			{
			?>
			<tr>
			<td><div class="ListTitle"><a target="_blank" href="<?php echo $baseurl . "?c=" . $ref . "&k=" . $keys[$n]["access_key"]?>"><?php echo $keys[$n]["access_key"]?></a></div></td>
			<td><?php echo resolve_users($keys[$n]["users"])?></td>
			<td><?php echo $keys[$n]["emails"]?></td>
			<td><?php echo nicedate($keys[$n]["maxdate"],true);	?></td>
			<td><?php echo nicedate($keys[$n]["lastused"],true); ?></td>
			<td><div class="ListTools">
			<a href="#" onClick="if (confirm('<?php echo $lang["confirmdeleteaccess"]?>')) {document.getElementById('deleteaccess').value='<?php echo $keys[$n]["access_key"] ?>';document.getElementById('collectionform').submit(); }">&gt;&nbsp;<?php echo $lang["delete"]?></a>
			</div></td>
			</tr>
			<?php
			}
		?>
		</table>
		</div>
		<?php
		}
	?>
	</div>	
	
	<?php
	}
?>




</form>
</div>

<?php
include "../include/footer.php";
?>