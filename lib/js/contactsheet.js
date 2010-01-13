var contactsheet_previewimage_prefix = "";

function previewContactSheet() {
var url = 'ajax/contactsheet.php';
var formdata = $('contactsheetform').serialize() + '&preview=true'; 
var ajax = new Ajax.Updater({success: 'error'},url,{method: 'get', parameters:formdata, onSuccess: function(response) {refreshIt(response.responseText);}
,onCreate: function(response) {loadIt();}});
}

function refreshIt(pagecount) {
   document.previewimage.src = contactsheet_previewimage_prefix+'/tmp/contactsheet.jpg?'+ Math.random();
   if (pagecount>1){
	   $('previewPageOptions').style.display='block'; // display selector  
	   pagecount++;
	   curval=$('previewpage').value;
	   $('previewpage').options.length=0;		
	   for (x=1;x<pagecount;x++){ 
		    selected=false;
			if (x==curval){selected=true;}
			$('previewpage').options[x]=new Option(x+' of '+(pagecount-1),x,selected,selected);
			}
	   }
	else {
		  $('previewPageOptions').style.display='none';
		  }
	   $('previewpage').options[0]=null;
}
function loadIt() {
   document.previewimage.src = '../gfx/images/ajax-loader.png';}
