<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/collections_functions.php";

# Fetch vars
$ref=getvalescaped("ref","");

include "include/header.php";
?>


<div class="BasicsBox"> 

  <h1><?=$lang["sharecollection"]?></h1>
  
	<div class="VerticalNav">
	<ul>
	
	<li><a href="collection_email.php?ref=<?=$ref?>"><?=$lang["emailcollection"]?></a></li>

	<li><a href="collection_share.php?ref=<?=$ref?>&generateurl=true"><?=$lang["generateurl"]?></a></li>

	<? if (getval("generateurl","")!="")
		{
		?>
		<p><?=$lang["generateurlinternal"]?></p>
		
		<p><input class="URLDisplay" type="text" value="<?=$baseurl?>/?c=<?=$ref?>">
		
		<p><?=$lang["generateurlexternal"]?></p>
		
		<p><input class="URLDisplay" type="text" value="<?=$baseurl?>/?c=<?=$ref?>&k=<?=generate_collection_access_key($ref)?>">
				
		<?
		}
	?>
	
	<? hook("collectionshareoptions") ?>
	
	</ul>
	</div>

</div>

<?
include "include/footer.php";
?>