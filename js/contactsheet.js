function previewContactSheet(data) {
var url = 'contactsheet.php';
var ajax = new Ajax.Updater({success: ''},url,{method: 'get', parameters:data, onSuccess: function(response) {refreshIt();}});
}

function refreshIt() {
   document.previewimage.src = 'contactsheet.jpg?'+ Math.random();}
