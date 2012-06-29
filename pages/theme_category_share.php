<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/search_functions.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";

	
$themes=array();
$themecount=0;
foreach ($_GET as $key => $value) {
	// only set necessary vars
	if (substr($key,0,5)=="theme" && $value!=""){
		$themes[$themecount]=urldecode($value);
		$themecount++;
		}
	}

$header=getvalescaped("header","");
$smart_theme=getvalescaped("smart_theme","");
$showexisting=getvalescaped("showexisting","");

$linksuffix="?";
for ($x=0;$x<count($themes);$x++){
	if ($x!=0){ $linksuffix.="&"; }
	$linksuffix.="theme";
	$linksuffix.=($x==0)?"":$x;
	$linksuffix.="=". urlencode($themes[$x]);
}


include "../include/header.php";

?>
<div class="BasicsBox"> 
<form method=post id="themeform">
<input type="hidden" name="generateurl" id="generateurl" value="">

<div class="VerticalNav">
<?php

// Get collections under the theme
$collectionstoshare=get_themes($themes);

if (count($collectionstoshare)<1) # There are no collections in this theme
		{
		$show_error=true;
		$error=$lang["cannotshareemptythemecategory"];
		}
else
	{
	foreach($collectionstoshare as $collection)
		{			
		$ref=$collection["ref"];		
	
		# Get min access to this collection
		$minaccess=collection_min_access($ref);

		
		if ($minaccess>=1 && !$restricted_share) # Minimum access is restricted or lower and sharing of restricted resources is not allowed. The user cannot share this collection.
			{
			$show_error=true;
			$error="Collection: " . $collection["name"] . "<br>" . $lang["restrictedsharecollection"];
			}
			
		if (count(get_collection_resources($ref))==0) # Sharing an empty collection?
			{
			$show_error=true;
			$error="Collection: " . $collection["name"] . "<br>" . $lang["cannotshareemptycollection"];
			}
		
		}
	
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
		<input name="generateurl" type="submit" value="&nbsp;&nbsp;<?php echo $lang["generateexternalurl"]?>&nbsp;&nbsp;" />
		</div>
		
		</div>

		</form>
		<?php
		
		//Display existing shares for collections in theme
		if ($showexisting=="")
			{ echo "<p><a href=\"theme_category_share.php" . $linksuffix . "&showexisting=true\">> " . $lang["showexistingthemeshares"] . "</a></p>";}
		else 
			{
			foreach($collectionstoshare as $collection)
				{			
				$ref=$collection["ref"];
				$keys=get_collection_external_access($ref);
				?>
				<p>&nbsp;</p>
				<h2><?php echo $lang["externalusersharing"] . " - " . $collection["name"]?></h2>
				<div class="Question">
				<?php
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
						<a href="#" onClick="if (confirm('<?php echo $lang["confirmdeleteaccess"]?>')) {document.getElementById('deleteaccess').value='<?php echo $keys[$n]["access_key"] ?>';document.getElementById('collectionform').submit(); }">&gt;&nbsp;<?php echo $lang["action-delete"]?></a>
						</div></td>
						</tr>
						<?php
						}
					?>
					</table>
					</div>
					</div>
					<?php
					}
				}
			}
		}
	else
		{
		# Access has been selected. Generate a URL.
		?>
		<p><?php echo $lang["generatethemeurlsexternal"]?></p>
		<div class="Listview">
		<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
		<tr class="ListviewTitleStyle"><td><?php echo $lang["collectionname"];?></td>
		<td>URL</td>
		<td><?php echo $lang["expires"];?></td>
		</tr>
		<?php 
		foreach($collectionstoshare as $collection)
			{			
			$ref=$collection["ref"];?>
			<tr><td><?php echo $collection["name"];?></td><td><div class="ListTitle"><?php echo $baseurl?>/?c=<?php echo $ref?>&k=<?php echo generate_collection_access_key($ref,0,"URL",$access,$expires)?></div></td><td><?php echo $expires?></td></tr>
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

if (isset($show_error)){?>
    <script type="text/javascript">
    alert('<?php echo $error;?>');
    	history.go(-1);
    </script><?php
    }
?>

<?php
include "../include/footer.php";
?>
