var UserRatingDone=false;
function UserRatingDisplay(ref,rating,hiclass)
	{
	if (window['UserRatingDone' + ref]) {return false;}
	for (var n=1;n<=5;n++)
		{
		jQuery('#RatingStar'+ref+'-'+n).removeClass('StarEmpty');
		jQuery('#RatingStar'+ref+'-'+n).removeClass('StarCurrent');
		jQuery('#RatingStar'+ref+'-'+n).removeClass('StarSelect');
		if (n<=rating)
			{
			jQuery('#RatingStar'+ref+'-'+n).addClass(hiclass);
			}
		else
			{
			jQuery('#RatingStar'+ref+'-'+n).addClass('StarEmpty');
			}
		}
	}

function UserRatingSet(userref,ref,rating)
	{
	jQuery('#RatingStarLink'+ref+'-'+rating).blur(); // removes the white focus box around the star.
	if (window['UserRatingDone' + ref]) {return false;}
	jQuery.ajax(baseurl_short+"pages/ajax/user_rating_save.php?userref="+userref+"&ref="+ref+"&rating=" + rating,{method: 'post'});
 	window['UserRatingDone' + ref]=true;
	}
