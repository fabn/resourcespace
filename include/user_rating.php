<?
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

function UserRatingSet(rating)
	{
	$('RatingStarLink'+rating).blur(); // removes the white focus box around the star.
	if (UserRatingDone) {return false;}
	new Ajax.Request("<?=$baseurl?>/pages/ajax/user_rating_save.php?ref=<?=$ref?>&rating=" + rating,{method: 'post'});
	UserRatingDone=true;
	$('RatingCount').style.visibility='hidden';
	$('UserRatingMessage').innerHTML="<?=$lang["ratingthankyou"]?>";
	}

</script>
<table>
<tr class="DownloadDBlend">
<td align="center" id="UserRatingMessage"><?=$lang["ratethisresource"]?></td>
<td width="33%" align="center" class="RatingStars" onMouseOut="UserRatingDisplay(<?=$rating?>,'StarWhite');">
<? for ($n=1;$n<=5;$n++)
	{
	?><a href="#" onMouseOver="UserRatingDisplay(<?=$n?>,'StarSelect');" onClick="UserRatingSet(<?=$n?>);return false;" id="RatingStarLink<?=$n?>"><span id="RatingStar<?=$n?>" class="Star<?=($n<=$rating?"White":"Grey")?>"><img src="../gfx/interface/sp.gif" width="15" height="15"></span></a><?
	#&#9733;
	}
?>

<div class="RatingCount" id="RatingCount"><?=$rating_count?> <?=($rating_count==1?$lang["rating"]:$lang["ratings"])?></div>
</td>
</tr>
</table>
