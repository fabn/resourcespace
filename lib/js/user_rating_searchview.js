var UserRatingDone=false;
function UserRatingDisplay(ref,rating,hiclass)
	{
	if (window['UserRatingDone' + ref]) {return false;}
	for (var n=1;n<=5;n++)
		{
		$('RatingStar'+ref+'-'+n).removeClassName('StarEmpty');
		$('RatingStar'+ref+'-'+n).removeClassName('StarCurrent');
		$('RatingStar'+ref+'-'+n).removeClassName('StarSelect');
		if (n<=rating)
			{
			$('RatingStar'+ref+'-'+n).addClassName(hiclass);
			}
		else
			{
			$('RatingStar'+ref+'-'+n).addClassName('StarEmpty');
			}
		}
	}

function UserRatingSet(userref,ref,rating)
	{
	$('RatingStarLink'+ref+'-'+rating).blur(); // removes the white focus box around the star.
	if (window['UserRatingDone' + ref]) {return false;}
	new Ajax.Request("ajax/user_rating_save.php?userref="+userref+"&ref="+ref+"&rating=" + rating,{method: 'post'});
	window['UserRatingDone' + ref]=true;
	}
