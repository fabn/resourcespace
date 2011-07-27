var contactsheet_previewimage_prefix = "";

(function($) {
	
	 var methods = {
		
		preview : function() { 
			var url = 'ajax/contactsheet.php';

			var formdata = $('#contactsheetform').serialize() + '&preview=true'; 

			$.ajax({
			url: url,
			data: formdata,
			success: function(response) {$(this).rsContactSheet('refresh',response);},
			beforeSend: function(response) {loadIt();}
			});
		},
		
		refresh : function(pagecount) { 
			document.previewimage.src = contactsheet_previewimage_prefix+'/tmp/contactsheet.jpg?'+ Math.random();
			if (pagecount>1){
				$('#previewPageOptions').show(); // display selector  
				pagecount++;
				curval=$('#previewpage').val();
				$('#previewpage')[0].options.length = 0;
	
				for (x=1;x<pagecount;x++){ 
					selected=false;
					var selecthtml="";
					if (x==curval){selected=true;}
					if (selected){selecthtml=' selected="selected" ';}
					$('#previewpage').append('<option value='+x+' '+selecthtml+'>'+x+'/'+(pagecount-1)+'</option>');
				}
			}
			else {
				$('#previewPageOptions').hide();
			}
		},
		
		
		 
		revert : function() { 
			$('#previewpage')[0].options.length = 0;
			$('#previewpage').append(new Option(1, 1,true,true));
			$('#previewpage').value=1;$('#previewPageOptions').hide();
			$(this).rsContactSheet('preview');
		}
	};

  $.fn.rsContactSheet = function( method ) {
    
    // Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.rsContactSheet' );
    }    
  
  };
	

})(jQuery)


function loadIt() {
   document.previewimage.src = '../gfx/images/ajax-loader-on-sheet.gif';}
