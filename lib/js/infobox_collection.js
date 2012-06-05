/*
 *  Infobox.js
 *  Part of ResourceSpace
 *  Displays an information box when the user hovers over resource results.
 *
 *--------------------------------------------------------------------------*/

var InfoBoxWaiting=false;
var InfoBoxVisible=false;
var InfoBoxRef=0;
var InfoBoxTop=0;
var InfoBoxLeft=0;
var InfoBoxTimer=false;

function InfoBoxMM(event)
    {
   	var i=document.getElementById('InfoBoxCollection');
   	if (!i) {return false;} // no object? ignore for now
   	
   	var ii=document.getElementById('InfoBoxCollectionInner');
    var x=event.clientX;
    var y=event.clientY;
    
    var iebody=(document.compatMode && document.compatMode != "BackCompat")? document.documentElement : document.body

	var dsocleft=document.all? iebody.scrollLeft : pageXOffset
	var dsoctop=document.all? iebody.scrollTop : pageYOffset
    
    InfoBoxTop=dsoctop + y - 25;
    if (InfoBoxTop<5) {InfoBoxTop=5;}
    InfoBoxLeft=dsocleft + x + 10;    	

	// Set up the box background / shadow
    // move the box higher up if the cursor is low enough to support this.
    if (x>400)
    	{
    	InfoBoxLeft-=396;
   		i.style.backgroundImage="url('../gfx/interface/infobox_left.png')";
   		ii.style.marginLeft="15px";
   		ii.style.marginRight="50px";
    	}
	else
		{
		i.style.backgroundImage="url('../gfx/interface/infobox_right.png')";
   		ii.style.marginLeft="50px";
  		ii.style.marginRight="15px";
		}
		
    if (InfoBoxRef!=0)
    	{
	    i.style.top=InfoBoxTop + "px";
    	i.style.left=InfoBoxLeft + "px";
    	}
	else
		{
 		i.style.display='none';
    	InfoBoxVisible=false;
		}



	// set a timer for the infobox to appear
    if ((InfoBoxRef!=0) && (InfoBoxWaiting==false) && (InfoBoxVisible==false))
    	{
    	if (InfoBoxTimer) {window.clearTimeout(InfoBoxTimer);}
		window.setTimeout('InfoBoxAppear()',1200);
	    InfoBoxWaiting=true;
		}
    }

function InfoBoxSetResource(ref)
	{
	InfoBoxRef=ref;
	}
	
function InfoBoxAppear()
	{
	// Make sure we are still waiting for a box to appear and that the mouse has not yet moved.
	if ((InfoBoxWaiting) && (InfoBoxRef!=0))
		{
		var i=document.getElementById('InfoBoxCollection');

    	//Ajax loader here
    	document.getElementById('InfoBoxCollectionInner').innerHTML='';
    	jQuery.ajax({
			success:function (data){jQuery('#InfoBoxCollectionInner').html(data);},
			url:'ajax/infobox_loader.php?ref=' + InfoBoxRef
		});

	   	i.style.display='block';
        //new Effect.Opacity('InfoBoxCollection', {duration:0.3, from:1, to:0.8});
	   	
	    i.style.top=InfoBoxTop + "px";
    	i.style.left=InfoBoxLeft + "px";
    	
    	InfoBoxVisible=true;
    	}
    InfoBoxWaiting=false;
	}
