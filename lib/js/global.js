<script type="text/javascript">
/* global.js : Functions to support features available globally throughout ResourceSpace */

function SetCookie (cookieName,cookieValue,nDays)
	{
	/* Store a cookie */
	var today = new Date();
	var expire = new Date();
	if (nDays==null || nDays==0) nDays=1;
	expire.setTime(today.getTime() + 3600000*24*nDays);
	document.cookie = cookieName+"="+escape(cookieValue)
       + ";expires="+expire.toGMTString();
	}

/* Keep a global array of timers */
var timers = new Array();


/* AJAX loading of central space contents given a link */
function CentralSpaceLoad (anchor,scrolltop)
	{
	<?php 
	if (checkperm("b")){$window="";} else {$window="top.main.";}
	?>
	/* Handle link normally if the CentralSpace element does not exist */
	if ( !<?php echo $window?>jQuery('#CentralSpace')  ||  <?php echo $window?>jQuery('#noheader').length > 0 )
		{
		<?php echo $window?>location.href=anchor.href;
		return false;
		} 

	/*
	Do not use AJAX when changing folder levels. This is because the content will not load correctly as the browser is using a page from a different level.
	Moving all anchor/image/form etc. URLs to absolute URLs will solve this longer term.
	* 
	* To try to eliminate this condition (which prevents updated urls in the location bar) 
	* Tom added jQuery scripts in header.php and collections.php to try to rewrite relative img.src and a.href's to absolutes
	 */
	//if (anchor.href.split("/").length != location.href.split("/").length)
	//	{
	//	location.href=anchor.href;
	//	return false;
	//	}

	/* Scroll to top if parameter set - used when changing pages */
	if (scrolltop==true) {<?php echo $window?>jQuery('html, body').animate({scrollTop:0}, 'fast');};
	
	if (anchor.href.indexOf("?")!=-1)
		{
		<?php echo $window?>jQuery('#CentralSpace').load(anchor.href + '&ajax=true');
		}
	else
		{
		<?php echo $window?>jQuery('#CentralSpace').load(anchor.href + '?ajax=true');			
		}
		
	// Change the browser URL
	if(typeof(top.history.replaceState)=='function')
		{
		top.history.replaceState("string", "Title", anchor.href);
		}
	
	
		
	return false;
	}



function relToAbs (){
// rewrite relative urls http://aknosis.com/2011/07/17/using-jquery-to-rewrite-relative-urls-to-absolute-urls-revisited/	
// Use jQuerys .each() method to iterate over each link
        jQuery('a').not('[href^="http"],[href^="https"],[href^="mailto:"],[href^="#"]').each(function() {
            jQuery(this).attr('href', function(index, value) {
                if (value.substr(0,1) !== "/") {
                   value = <?php echo $window?>location.pathname.substring(0,<?php echo $window?>location.pathname.lastIndexOf('/')+1) + value;
                }
                return <?php echo $window?>location.protocol+"//"+<?php echo $window?>location.hostname + value;
        });
    });
// do the same with image src tags   
        jQuery('img').not('[src^="http"],[src^="https"],[src^="mailto:"],[src^="#"]').each(function() {
			
            jQuery(this).attr('src', function(index, value) {
                if (value.substr(0,1) !== "/") {
                    value = <?php echo $window?>location.pathname.substring(0,<?php echo $window?>location.pathname.lastIndexOf('/')+1) + value;
                }
                return <?php echo $window?>location.protocol+"//"+<?php echo $window?>location.hostname + value;
        });
    });    
}
	
jQuery(document).load(function(){	
	relToAbs();
}); 

jQuery(document).ajaxComplete(function(event,request, settings){
	relToAbs();
});  
 


/* AJAX posting of a form, result are displayed in the CentralSpace area. */
function CentralSpacePost (form,scrolltop)
	{
	var url=form.action;
	
	/* Scroll to top if parameter set - used when changing pages */
	if (scrolltop==true) {jQuery('html, body').animate({scrollTop:0}, 'fast');};

	if (url.indexOf("?")!=-1)
		{
		url += '&ajax=true';
		}
	else
		{
		url += '?ajax=true';			
		}
	jQuery.post(url,jQuery(form).serialize(),function(data) {jQuery('#CentralSpace').html(data);});
	return false;
	}
</script>
