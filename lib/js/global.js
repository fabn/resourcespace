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
	 */
	if (anchor.href.split("/").length != location.href.split("/").length)
		{
		location.href=anchor.href;
		return false;
		}

	/* Scroll to top if parameter set - used when changing pages */
	if (scrolltop==true) {jQuery('html, body').animate({scrollTop:0}, 'fast');};
	
	if (anchor.href.indexOf("?")!=-1)
		{
		jQuery('#CentralSpace').load(anchor.href + '&ajax=true');
		}
	else
		{
		jQuery('#CentralSpace').load(anchor.href + '?ajax=true');			
		}
		
	// Change the browser URL
	if(typeof(top.history.replaceState)=='function')
		{
		top.history.replaceState("string", "Title", anchor.href);
		}
		
	return false;
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
	jQuery.post(url,jQuery(form).serialize(),function(data) {jQuery('#CentralSpace').html(data);});
	return false;
	}