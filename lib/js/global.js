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
	
	/* Handle link normally if the CentralSpace element does not exist */
	if (!jQuery('#CentralSpace'))
		{
		location.href=anchor.href;
		return false;
		} 
		
	/*
	Do not use AJAX when changing folder levels. This is because the content will not load correctly as the browser is using a page from a different level.
	Moving all anchor/image/form etc. URLs to absolute URLs will solve this longer term.
	* including ajax_url_rewrites.js via $ajax_url_rewrites=true  will remove this condition
	 */
	if ( !rewriteUrls && anchor.href.split("/").length != location.href.split("/").length)
		{
		location.href=anchor.href;
		return false;
		}

	/* Scroll to top if parameter set - used when changing pages */
	if (scrolltop==true) {jQuery('html, body').animate({scrollTop:0}, 'fast');};
	
	var url = anchor.href;
	
	if (url.indexOf("?")!=-1)
		{
		url += '&ajax=true';
		}
	else
		{
		url += '?ajax=true';
		}

	jQuery('#CentralSpace').load(url, function ()
		{
		// Load completed
		if (rewriteUrls){relToAbs("#CentralSpace",url);}
		// Change the browser URL and save the CentralSpace HTML state in the browser's history record.
		if(typeof(top.history.pushState)=='function')
			{
			top.history.pushState(jQuery('#CentralSpace').html(), "ResourceSpace", anchor.href);
			}
			
		});
		
		
	return false;
	}

/* When back button is clicked, reload AJAX content stored in browser history record */
top.window.onpopstate = function(event)
	{
	if (!event.state) {return true;} // No state
	jQuery('#CentralSpace').html(event.state);
	}

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
	jQuery.post(url,jQuery(form).serialize(),function(data)
		{
		jQuery('#CentralSpace').html(data);
		if (rewriteUrls){relToAbs("#CentralSpace",url);}
		// Change the browser URL and save the CentralSpace HTML state in the browser's history record.
		if(typeof(top.history.pushState)=='function')
			{
			top.history.pushState(data, "ResourceSpace", form.action);
			}
		
		
		});
	return false;
	}






function relToAbs(context,url){

// also try binding CentralSpaceLoad automatically?
var context=jQuery(context);

var urlsplit=url.split(baseurl_short);
if (urlsplit.length>1){
	url=baseurl_short+urlsplit[1];   
	}
	
// rewrite relative urls http://aknosis.com/2011/07/17/using-jquery-to-rewrite-relative-urls-to-absolute-urls-revisited/        
context.find('a').not('a:not([href]),[href^="http"],[href^="https"],[href^="mailto:"],[href^="#"]').each(function() {
	jQuery(this).attr('href', function(index, value) {
		if (value.substr(0,1) !== "/") {
			//alert (url.substring(0,url.lastIndexOf('/')+1)+ '   '+value);
			var newvalue = url.substring(0,url.lastIndexOf('/')+1) + value;
			if (rewriteUrlsDebug){console.log('rewrote a.href '+value + ' as '+newvalue);}
			return newvalue;
		}			
    });
});
// do the same with image src tags   
context.find('img').not('[src^="http"],[src^="https"],[src^="mailto:"],[src^="#"]').each(function() {
                        
	jQuery(this).attr('src', function(index, value) {
		if (value.substr(0,1) !== "/") {
			var newvalue = url.substring(0,url.lastIndexOf('/')+1) + value;
			if (rewriteUrlsDebug){console.log('rewrote img '+value + ' as '+newvalue);} 	
			return newvalue;
		}
	});
});   

// do the same with form action   
context.find('form').not('form:not([action]),[action^="http"],[action^="https"],[action^="#"]').each(function() {
	
	jQuery(this).attr('action', function(index, value) {
		if (value.substr(0,1) !== "/") {
			var newvalue = url.substring(0,url.lastIndexOf('/')+1) + value;
			if (rewriteUrlsDebug){console.log('rewrote form action '+value + ' as '+newvalue);}
			return newvalue;
		}
	});
});  

// fix any iframes
context.find('iframe').not('iframe:not([src]),[src^="http"],[src^="https"],[src^="#"]').each(function() {
	
	jQuery(this).attr('src', function(index, value) {
		if (value.substr(0,1) !== "/") {
			var newvalue = url.substring(0,url.lastIndexOf('/')+1) + value;
			if (rewriteUrlsDebug){console.log('rewrote iframe.src '+value + ' as '+newvalue);}
			return newvalue;
		}
	});
});   

// same with script src
context.find('script').not('script:not([src]),[src^="http"],[src^="https"],[src^="#"]').each(function() {
	
	jQuery(this).attr('src', function(index, value) {
		if (value.substr(0,1) !== "/") {
			var newvalue = url.substring(0,url.lastIndexOf('/')+1) + value;
			if (rewriteUrlsDebug){console.log('rewrote script src '+value + ' as '+newvalue);} 
			return newvalue;
		}
	});
});   
  
}

// initial page load
if (rewriteUrls){
	jQuery(document).ready(function(){
		if (rewriteUrlsDebug){console.log("checking "+location.href);}
		relToAbs(this,location.href);
	});
}

