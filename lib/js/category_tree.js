
var TreeParents		= new Object();
var TreeNames		= new Object();
var TreeExpand		= new Object();
var TreeID			= new Object();
var TreeClickable	= new Object();
var TreeChecked		= new Object();
var TreeDrawn		= false;


function DrawFromNode( field, node, inner )
{
	var result			= "";
    var width			= 10;
    var numChildren		= 0;
    
    if( node == -1 ) {
    	width = 0;
    }

	if( !inner ) {
		result += "<table border=0 cellpadding=0 cellspacing=1><td width=" + width + ">&nbsp;</td><td class='treetext' id='" + field + "_node" + node + "'>";
	}
	
	//loop through all nodes with <node> as the parent
	for( var i = 0; i < TreeParents[field].length; i++ ) {
        if( TreeParents[field][i] == node ) {
            if( HasTickedDescendants( field, i ) ) { 
            	if( !TreeDrawn ) {
            		TreeExpand[field][i] 	= true; 
            	}
				TreeChecked[field][i] 	= 1;
			}
			
            if( TreeExpand[field][i] ) {
				icon = "<img xalign=middle width=11 height=11 hspace=3 src=admin/gfx/node_ex.gif";
			} else {
				icon = "<img xalign=middle hspace=3 width=11 height=11 src=admin/gfx/node_unex.gif";
			}
    
    		numChildren = CountChildren( field, i );
    		
            if( numChildren == 0 ) {
            	icon = "<img xalign=top src=admin/gfx/sp.gif width=11 height=11 hspace=3>";
            } else {
            	icon += " onClick=\"ToggleNode('" + field + "'," + i + ");\">";
            }
            
        	result	+= icon;
            checked	= "";
            
            if( TreeChecked[field][i] == 1 ) {
            	checked = "checked";
            }
            
            if( TreeClickable[field][i] ) {
            	result += "<input name='' type=checkbox id=checkbox" + i + " " + checked + " onclick=\"CheckNode('" + field + "'," + i + ");\">";
            }
            
            result += DePath( TreeNames[field][i] );
            result += "<br>";
            
            if( TreeExpand[field][i] && numChildren > 0 ) {
            	result += DrawFromNode( field, i, false );
            }
		}
	}
	
	if( !inner ) {
		result += "</td></tr></table>";
	}
	
	if( node == -1 ) {
    	TreeDrawn = true;
    }

	return( result );
}


function CheckNode( field, node )
{
	if( TreeChecked[field][node] == 0 )  {
		TreeChecked[field][node] = 1;
		
		//Make sure all parents are ticked
				
		var p = TreeParents[field][node];
		
		if( p > -1 ) {
			if( TreeChecked[field][p] == 0 ) {
				CheckNode( field, p );
				unode = TreeParents[field][p];
				if( unode == -1 ) {
					DrawTree( field );
				} else {
					UpdateNode( field, unode );
				}
			}
		}
	} else {
		TreeChecked[field][node] = 0;
		ResetChildren( field, node );
		UpdateNode( field, node );
	}
	
	UpdateStatusBox( field );
	UpdateHiddenField( field );
}
	

function UpdateStatusBox( field )
{
	var nodes = "";
	
	for( var i = 0; i < TreeParents[field].length; i++ ) {
		if( TreeChecked[field][i] == 1 ) {
			var c = CountTickedChildren( field, i );
			if( c == 0 ) {
				nodes += DescribeNode( field, i ) + "<br/>";
			}	
		}	
	}
	
	if( nodes == "" ) {
		document.getElementById( field + "_statusbox" ).innerHTML = "<b>" + nocategoriesmessage + "</b>";
	} else {
		document.getElementById( field + "_statusbox" ).innerHTML = nodes;
	}
}
	

function StatusReset( field )
{
	//Set the status box to empty
	document.getElementById( field + "_statusbox" ).innerHTML="";
}


function DescribeNode( field, node )
{
	// Returns a string containing the node's 'path'
	var path	= DePath( TreeNames[field][node] );
	var p		= TreeParents[field][node];
	
	while( p > -1 ) {
		path = DePath( TreeNames[field][p] ) + " / " + path;
		var  p = TreeParents[field][p];
	}
	
	return( path );
}
	

function ResetChildren( field, node )
{
    for( var p = 0; p < TreeParents[field].length; p++ ) {
		if( TreeParents[field][p] == node ) {
			TreeChecked[field][p] = 0;
			ResetChildren( field ,p );
			UpdateNode( field, p );
		}	
	}
}


function ToggleNode( field, node )
{ 
	TreeExpand[field][node] =! TreeExpand[field][node];
	UpdateNode( field, TreeParents[field][node] );
}


function UpdateNode( field, node )
{
    if( document.getElementById( field + "_node" + node ) ) {
        document.getElementById( field + "_node" + node ).innerHTML = DrawFromNode( field, node, true );
    }
}

    
function CountChildren( field, node )
{
	var count	=	0;
	
	for( var i = 0; i < TreeParents[field].length; i++ ) {
		if( TreeParents[field][i] == node ) {
			count++;
		}	
	}
	
	return( count );
}


function CountTickedChildren( field, node )
{
	var count	= 0;
	
	for( var i = 0; i < TreeParents[field].length; i++ ) {
		if( ( TreeParents[field][i] == node ) && ( TreeChecked[field][i] == 1 ) ) {
			count++;
		}	
	}
	
	return( count );
}


function HasTickedDescendants( field, node )
{
		var hasTickedDescendants = false;

		for( var i = 0; i < TreeParents[field].length; i++ ) {
			if( ( TreeParents[field][i] == node ) && ( TreeChecked[field][i] == 1 ) ) {
				hasTickedDescendants = true; 
				break;	
			} else {
				if( TreeParents[field][i] == node ) {
					hasTickedDescendants = HasTickedDescendants( field, i );
					 if( hasTickedDescendants ) { 
						break; 
					 } 
				}
			}
		}

	return( hasTickedDescendants );
}


function CountCheckedRootLevels( field )
{
	var count	= 0;
	
	for( var i = 0; i < TreeParents[field].length; i++ ) {
		if( ( TreeParents[field][i] == -1 ) && ( TreeChecked[field][i] == 1 ) ) {
		count++;
		}	
	}
	
	return( count );
}


function DeselectAll( field )
{
	for( var i = 0; i < TreeParents[field].length; i++ ) {
		TreeChecked[field][i] = 0;
	}
	
	DrawTree( field );
	UpdateStatusBox( field );
	UpdateHiddenField( field );
}


function DrawTree( field )
{
	document.getElementById( field + "_tree" ).innerHTML = DrawFromNode( field, -1, false );
}


function AddNode( field, nodeparent, nodeid, nodename, nodeclickable, nodechecked, nodeexpand )
	{
    //try to find an empty space first

    var found	= false;
    
	for( var c = 0; c < TreeParents[field].length; c++ ) {
		if( TreeParents[field][c] == -100 ) {
			found = true;
			break;
		}	
	}
	
    if( found == false ) {
    	c = TreeParents[field].length;
    }
    
	TreeParents[field][c]		= nodeparent;    
    TreeID[field][c]			= nodeid;
	TreeNames[field][c]			= nodename;
	TreeExpand[field][c]		= false;
    TreeClickable[field][c]		= nodeclickable;
    TreeChecked[field][c]		= nodechecked;
    TreeExpand[field][c]		= false;
}


function ResolveParents( field )
{
	for( var c = 0; c < TreeParents[field].length; c++ ) {
    	//resolve nodeparent to internal node id
	    found  = false;
	    
	    for( var p = 0; p < TreeID[field].length; p++ ) {
			if ( TreeID[field][p] == TreeParents[field][c] ) {
				found = true;
				break;
			}	
		}
		
	    if( found ) {
	    	TreeParents[field][c] = p;
	    }
	    
	    if( TreeParents[field][c] == -1 ) {
	    	TreeParents[field][c] = -1;
	    }
	}
}


function UpdateHiddenField( field )
{
	var f = "";
	
	for( var p = 0; p < TreeID[field].length; p++ ) {
		if ( TreeChecked[field][p] == 1 ) {
			f += "," + TreeNames[field][p];
		}	
	}
	
	document.getElementById( field + "_category" ).value = f;
	
	// Update the result counter, if the function is available (e.g. on Advanced Search).
	if( typeof( UpdateResultCount ) != 'undefinied' && typeof( UpdateResultCount ) == 'function' ) {
		UpdateResultCount();
	}
}
	

function DePath( path )
{
	// Returns the last element in a tilda-separated path as produced by StaticSync for the various tree levels.
	return( path.split( '~' ).last() );
}
