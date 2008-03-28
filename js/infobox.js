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

function InfoBoxMM(event)
    {
   	var i=$('InfoBox');
   	if (!i) {return false;} // no object? ignore for now
   	
   	var ii=$('InfoBoxInner');
    var x=event.clientX;
    var y=event.clientY;
    
    var iebody=(document.compatMode && document.compatMode != "BackCompat")? document.documentElement : document.body

	var dsocleft=document.all? iebody.scrollLeft : pageXOffset
	var dsoctop=document.all? iebody.scrollTop : pageYOffset
    
 	i.style.display='none';
        
    InfoBoxTop=dsoctop + y + 15;
    InfoBoxLeft=dsocleft + x - 25;
    
	// Set up the box background / shadow
    // move the box higher up if the cursor is low enough to support this.
    if (y>310)
    	{
    	InfoBoxTop-=320;
   		i.style.backgroundImage="url('gfx/interface/infobox_up.png')";
   		ii.style.marginTop="15px";
   		ii.style.marginBottom="70px";
    	}
	else
		{
		i.style.backgroundImage="url('gfx/interface/infobox_down.png')";
   		ii.style.marginTop="70px";
  		ii.style.marginBottom="15px";
		}

	// set a timer for the infobox to appear
    if ((InfoBoxRef!=0) && (InfoBoxWaiting==false))
    	{
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
		var i=$('InfoBox');

    	//Ajax loader here
    	$('InfoBoxInner').innerHTML='';
    	new Ajax.Updater('InfoBoxInner','infobox_loader.php?ref=' + InfoBoxRef,{ method: 'get' });

	   	i.style.display='block';
        new Effect.Opacity('InfoBox', {duration:0.3, from:1, to:0.7});
	   	
	    i.style.top=InfoBoxTop + "px";
    	i.style.left=InfoBoxLeft + "px";
    	
    	}
    InfoBoxWaiting=false;
	}