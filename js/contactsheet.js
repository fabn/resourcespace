function previewContactSheet() {
var url = 'contactsheet.php';
var formdata = $('contactsheetform').serialize() + '&preview=true'; 
var ajax = new Ajax.Request(url,{method: 'get', parameters:formdata, onSuccess: function(response) {refreshIt();}});
}

function refreshIt() {
   document.previewimage.src = 'filestore/tmp/contactsheet.jpg?'+ Math.random();}
