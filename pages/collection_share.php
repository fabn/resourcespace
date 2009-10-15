<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/search_functions.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";

# Fetch vars
$ref=getvalescaped("ref","",true);
# if bypass sharing page option is on, redirect to e-mail
if ($bypass_share_screen)
	{
	header( 'Location:collection_email.php?ref='.$ref ) ;
	}

$collection=get_collection($ref);

# Process deletion of access keys
if (getval("deleteaccess","")!="")
	{
	delete_collection_access_key($ref,getvalescaped("deleteaccess",""));
	}
	
# Get min access to this collection
$minaccess=collection_min_access($ref);

if ($minaccess>=1 && !$restricted_share) # Minimum access is restricted or lower and sharing of restricted resources is not allowed. The user cannot share this collection.
	{
	?>
	<script type="text/javascript">
	alert("<?php echo $lang["restrictedsharecollection"]?>");
	history.go(-1);
	</script>
	<?php
	exit();
	}
	
if (count(get_collection_resources($ref))==0) # Sharing an empty collection?
	{
	?>
	<script type="text/javascript">
	alert("<?php echo $lang["cannotshareemptycollection"]?>");
	history.go(-1);
	</script>
	<?php
	exit();
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
	
	<?php
	$access=getvalescaped("access","");
	$expires=getvalescaped("expires","");
	if ($access=="")
		{
		?>
		<p><?php echo $lang["selectgenerateurlexternal"] ?></p>
		
		<div class="Question" id="question_access">
		<label for="archive"><?php echo $lang["access"]?></label>
		<select class="stdwidth" name="access" id="access">
		<?php
		# List available access levels. The highest level must be the minimum user access level.
		for ($n=$minaccess;$n<=1;$n++) { ?>
		<option value="<?php echo $n?>"><?php echo $lang["access" . $n]?></option>
		<?php } ?>
		</select>
		<div class="clearerleft"> </div>
		</div>
		
		<div class="Question">
		<label><?php echo $lang["expires"]?></label>
		<select name="expires" class="stdwidth">
		<option value=""><?php echo $lang["never"]?></option>
		<?php for ($n=1;$n<=150;$n++)
			{
			$date=time()+(60*60*24*$n);
			?><option <?php $d=date("D",$date);if (($d=="Sun") || ($d=="Sat")) { ?>style="background-color:#cccccc"<?php } ?> value="<?php echo date("Y-m-d",$date)?>"><?php echo nicedate(date("Y-m-d",$date),false,true)?></option>
			<?php
			}
		?>
		</select>
		<div class="clearerleft"> </div>
		</div>
		
		<div class="QuestionSubmit" style="padding-top:0;margin-top:0;">
		<label for="buttons"> </label>
		<input name="generateurl" type="submit" value="&nbsp;&nbsp;<?php echo $lang["generateurl"]?>&nbsp;&nbsp;" />
		</div>
		<?php
		}
	else
		{
		# Access has been selected. Generate a URL.
		?>
		<p><?php echo $lang["generateurlexternal"]?></p>
	
		<p><input class="URLDisplay" type="text" value="<?php echo $baseurl?>/?c=<?php echo $ref?>&k=<?php echo generate_collection_access_key($ref,0,"URL",$access,$expires)?>">
		<?php
		}
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
		<td><?php echo $lang["expires"];?></td>
		<td><?php echo $lang["access"];?></td>
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
			<td><?php echo ($keys[$n]["expires"]=="")?$lang["never"]:nicedate($keys[$n]["expires"],false)?></td>
			<td><?php echo ($keys[$n]["access"]==-1)?"":$lang["access" . $keys[$n]["access"]]; ?></td>
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