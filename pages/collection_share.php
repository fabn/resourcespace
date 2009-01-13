<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/collections_functions.php";

# Fetch vars
$ref=getvalescaped("ref","");

include "../include/header.php";
?>


<div class="BasicsBox"> 

  <h1><?php echo $lang["sharecollection"]?></h1>
  
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
		
		<p><input class="URLDisplay" type="text" value="<?php echo $baseurl?>/?c=<?php echo $ref?>&k=<?php echo generate_collection_access_key($ref)?>">
				
		<?php
		}
	?>
	
	<?php hook("collectionshareoptions") ?>
	
	</ul>
	</div>

</div>

<?php
include "../include/footer.php";
?>