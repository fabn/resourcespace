<?php
include "../include/db.php";
include "../include/general.php";
include "../include/authenticate.php"; 
include "../include/header.php";
include "../include/resource_functions.php";

$ref=getval("ref","");
$resource=get_resource_data($ref);
# fetch the current search (for finding simlar matches)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);


?>
<div class="BasicsBox"> 
<p><a href="<?php echo $baseurl?>/pages/view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>
  <h1><?php echo $lang["userratingstatsforresource"]." ".$ref;?></h1>
  
<table class="InfoTable">
<?php

?>

<tr><td><b><?php echo $lang["user"]?></b></td><td><b><?php echo $lang["rating"]?></b></td></tr><?php
$users=get_users(0,"","u.username",true);
$ratings=sql_query("select * from user_rating where ref='$ref'");
for ($n=0;$n<count($ratings);$n++){
	for ($x=0;$x<count($users);$x++){
		if ($ratings[$n]['user']==$users[$x]['ref']){
			$username=$users[$x]['fullname']." (".$users[$x]['username'].")";
		}
	}	
	?>

<tr><td><?php echo $username?></td>
<td><div  class="RatingStars" ><?php for ($y=0;$y<$ratings[$n]['rating'];$y++){?><span class="IconUserRatingStar" style="float:left;display:block;"></span><?php } ?></div><br>

</td></tr>
<?php } ?>

<tr><td><b><?php echo $lang['average']?></b></td><td> <?php for ($y=0;$y<$resource['user_rating'];$y++){?><span class="IconUserRatingStar" style="float:left;display:block;"></span><?php } ?><br> </td></tr>

</table>
</div>

<?php
include "../include/footer.php";
?>
