var colorthemes_previewimage_prefix = "";

function previewColortheme() {
var url = '../../../plugins/colorthemer/pages/ajax/colortheme_process.php';
var formdata = $('form').serialize() ;
var ajax = new Ajax.Request(url,{method: 'get', parameters:formdata, onSuccess: function(response) {refreshIt();}
});
}

function refreshIt() {
   document.previewimage.src = colorthemes_previewimage_prefix+'/tmp/compositepreview.jpg?'+ Math.random();
}
function loadIt() {
   document.previewimage.src = '../../../gfx/images/ajax-loader.png';}
