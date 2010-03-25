<?php
#
#
# Provides the User Rating function on the resource view page (if enabled)
# ------------------------------------------------------------------------
#
$rating=$resource["user_rating"];
$rating_count=$resource["user_rating_count"];

if ($rating=="") {$rating=0;}
if ($rating_count=="") {$rating_count=0;}

?>
<br />
<script type="text/javascript">

var UserRatingDone=false;

function UserRatingDisplay(rating,hiclass)
	{
	if (UserRatingDone) {return false;}
	for (var n=1;n<=5;n++)
		{
		$('RatingStar'+n).removeClassName('StarGrey');
		$('RatingStar'+n).removeClassName('StarWhite');
		$('RatingStar'+n).removeClassName('StarSelect');
		if (n<=rating)
			{
			$('RatingStar'+n).addClassName(hiclass);
			}
		else
			{
			$('RatingStar'+n).addClassName('StarGrey');
			}
		}
	}

function UserRatingSet(userref,ref,rating)
	{
	$('RatingStarLink'+rating).blur(); // removes the white focus box around the star.
	if (UserRatingDone) {return false;}
	new Ajax.Request("<?php echo $baseurl?>/pages/ajax/user_rating_save.php?user="+userref+"&ref="+ref+"&rating=" + rating,{method: 'post'});
	UserRatingDone=true;
	$('RatingCount').style.visibility='hidden';
	$('UserRatingMessage').innerHTML="<?php echo $lang["ratingthankyou"]?>";
	}

</script>
<table>
<tr class="DownloadDBlend">
<td align="center" id="UserRatingMessage"><?php echo $lang["ratethisresource"]?></td>
<td width="33%" align="center" class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $rating?>,'StarWhite');">
<?php for ($n=1;$n<=5;$n++)
	{
	?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $n?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $ref?>,<?php echo $n?>);return false;" id="RatingStarLink<?php echo $n?>"><span id="RatingStar<?php echo $n?>" class="Star<?php echo ($n<=$rating?"White":"Grey")?>"><img src="../gfx/interface/sp.gif" width="15" height="15"></span></a><?php
	#&#9733;
	}
?>

<div class="RatingCount" id="RatingCount"><?php if ($user_rating_stats && $user_rating_only_once){?><a href="user_ratings.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>"><?php } ?><?php echo $rating_count?> <?php echo ($rating_count==1?$lang["rating_lowercase"]:$lang["ratings"])?><?php if ($user_rating_stats && $user_rating_only_once){?></a><?php }?></div>
</td>
</tr>
</table>
