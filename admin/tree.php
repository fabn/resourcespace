<?
include "../include/db.php";
include "../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "include/header.php";
?>
<body bgcolor=white>

<script language="JavaScript">
var TreeParents=new Array();TreeParents[0]=-1;
var TreeNames=new Array();TreeNames[0]='Root';
var TreeExpand=new Array();TreeExpand[0]=false;
var TreeFolder=new Array();TreeFolder[0]=true;
var TreeID=new Array();TreeID[0]="";
var TreeIcon=new Array();TreeIcon[0]="global";
var TreeClickable=new Array();TreeClickable[0]=false;
var lastclick=0;
var nocache=0;

function DrawFromNode(node,inner)
	{

	var result="";
 	var i=0;
    var width=10;
    if (node==-1) {width=0;}
    
	if (!inner) {result+="<table border=0 cellpadding=0 cellspacing=1><td width=" + width + ">&nbsp;</td><td class='treetext' id='node" + node + "'>";}
	//loop through all nodes with <node> as the parent
	for (i=0;i<TreeParents.length;i++)
		{
        if (TreeParents[i]==node)
            {
            if (TreeExpand[i]) {icon="<img align=middle width=11 height=11 hspace=4 src=gfx/node_ex.gif";} else {icon="<img align=middle hspace=4 width=11 height=11 src=gfx/node_unex.gif";}
            if (!TreeFolder[i])
                {icon="<img align=middle src=gfx/sp.gif width=11 height=11 hspace=4>";}
            else
                {icon+=" onClick=\"ToggleNode(" + i + ");\">";}
            icon2="<img src=\"gfx/icons/" + TreeIcon[i] + ".gif\" width=22 height=22 align=middle vspace=2>";
        
            result+=icon + icon2 + "&nbsp;<font size=2>";
            if (TreeClickable[i]) {result+="<a target=right href='properties.php?id=" + TreeID[i] + "&parent=" + TreeParents[i] +  "&name=" + escape(TreeNames[i]) + "'>";}
            result+=TreeNames[i];
            if (TreeClickable[i]) {result+="</a>";}
            result+="</font><br>";
            if (TreeExpand[i]) {result+=DrawFromNode(i);}
            }
		}
	result+="</td></tr></table>";	

	return result;
	}

function ToggleNode(node)
	{ 
    lastclick=node;
	TreeExpand[node]=!TreeExpand[node];

	UpdateNode(TreeParents[node]);
    
    //tree loader
    if ((TreeExpand[node]==true) && (CountChildren(node)==0))
        {
        ReloadNode(node);
        }
	}

function EmptyNode(node)
    {
	var i=0;
	for (i=0;i<TreeParents.length;i++)
		{
		if (TreeParents[i]==node) {TreeParents[i]=-100;EmptyNode(i);}	
		}
    }
    
function ReloadNode(node)
    {
    //load children
    nocache++;
    document.getElementById("treeloader").src="treeloader.php?nocache=" + nocache + "&node=" + node + "&id=" + TreeID[node];
    }

function UpdateNode(node)
    {
    if (document.getElementById("node" + node))
        {
        document.getElementById("node" + node).innerHTML=DrawFromNode(node,true);
        }
    }
    
function CountChildren(node)
	{
	var count=0;
	var i=0;
	for (i=0;i<TreeParents.length;i++)
		{
		if (TreeParents[i]==node) {count++;}	
		}
	return count;
	}

function DrawTree()
	{
	document.getElementById("tree").innerHTML=DrawFromNode(-1,false);
	}

function AddNode(nodeparent,nodeid,nodename,nodefolder,nodeclickable,nodeicon)
	{
    //try to find an empty space first
    var c=0;var found=false;
	for (c=0;c<TreeParents.length;c++)
		{
		if (TreeParents[c]==-100) {found=true;break;}	
		}
    if (found==false) {c=TreeParents.length;}
    
	TreeParents[c]=nodeparent;
    TreeID[c]=nodeid;
	TreeNames[c]=nodename;
	TreeExpand[c]=false;
    TreeFolder[c]=nodefolder;
    TreeIcon[c]=nodeicon;
    TreeClickable[c]=nodeclickable;
	}
</script>

<div id="tree"></div>

<iframe width="100%" height="100" frameborder=0 border=0 id="treeloader" xstyle="visibility:hidden;"></iframe>

<script>
DrawTree();
ToggleNode(0);
</script>

</body>
</html>
