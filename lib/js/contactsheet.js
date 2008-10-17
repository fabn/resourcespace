function previewContactSheet() {
var url = 'contactsheet.php';
var formdata = $('contactsheetform').serialize() + '&preview=true'; 
var ajax = new Ajax.Request(url,{method: 'get', parameters:formdata, onSuccess: function(response) {refreshIt();}
,onCreate: function(response) {loadIt();}});
}

function refreshIt() {
   document.previewimage.src = '../filestore/tmp/contactsheet.jpg?'+ Math.random();}
function loadIt() {
   document.previewimage.src = '../gfx/images/ajax-loader.png';}