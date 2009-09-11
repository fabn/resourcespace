<?php
include "../../include/db.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
include "include/header.php";
?>
<body style="background-image: none;background-color: white;color: black;">
<style>
.backline
	{
	background-image:url(gfx/line.gif);
	background-position:9px 0px;
	background-repeat:repeat-y;
	}
.treetext
	{
	font-size:11px;
	}
</style>
<script type="text/javascript">

var TreeParents=new Array();TreeParents[0]=-1;
var TreeNames=new Array();TreeNames[0]='Root';
var TreeExpand=new Array();TreeExpand[0]=false;
var TreeFolder=new Array();TreeFolder[0]=true;
var TreeID=new Array();TreeID[0]="";
var TreeIcon=new Array();TreeIcon[0]="global";
var TreeClickable=new Array();TreeClickable[0]=false;
var TreeSearch=new Array();
var TreeReorder=new Array();
var lastclick=0;
var nocache=0;

function DrawFromNode(node,inner)
	{

	var result="";
 	var i=0;
    var width=10;
    var blclass="backline";
    var blclass2="backline";
    if (node==0) {width=11;blclass="";}
    if (node==-1){width=1;blclass="";blclass2="";}
    
	if (!inner) {result+="<table border=0 cellpadding=0 cellspacing=0><td class='" + blclass + "' width=" + width + "><img src=gfx/sp.gif width=" + width + " height=11 hspace=4></td><td class='treetext' id='node" + node + "'>";}
	//loop through all nodes with <node> as the parent
	for (i=0;i<TreeParents.length;i++)
		{
        if (TreeParents[i]==node)
            {
            if (TreeNames[i]=="Search:")
            	{
            	result+="<table cellpadding=0 cellspacing=0 valign=middle style='margin-bottom:0;padding:0;'><tr><td valign=middle class='" + blclass2 + "' style='padding-left:5px;'><form class='searchform' target='treeloader' method=post action='treeloader.php?node=" + TreeParents[i] + "&id=" + TreeID[TreeParents[i]] + "&reloadnode=true'><span class=treetext>Search:</span><input type=text name=search value='" + TreeSearch[TreeParents[i]] + "'><input type=submit name=submit value='Go'></form></td></tr></table>";
            	}
            else
            	{
            	var ifile=TreeIcon[i];
				if (TreeExpand[i]) {icon="<img width=11 height=11 hspace=4 src=gfx/node_ex.gif";ifile=ifile.replace('folder','folder_ex');} else {icon="<img hspace=4 width=11 height=11 src=gfx/node_unex.gif";}
				if (!TreeFolder[i])
					{icon="<img src=gfx/sp.gif width=11 height=11 hspace=4>";}
				else
					{icon+=" onClick=\"ToggleNode(" + i + ");\">";}
				icon2="<img src=\"gfx/icons/" + ifile + ".gif\" width=16 height=16 vspace=1>";
			
				result+="<table cellpadding=0 cellspacing=0 valign=middle style='margin-bottom:0;padding:0;'><tr><td valign=middle class='" + blclass2 + "'>" + icon + "</td><td valign=middle>" + icon2 + "</td><td width=3>&nbsp;</td><td valign=middle nowrap class='treetext'>";
				if (TreeClickable[i]) {result+="<a target=right href='properties.php?id=" + TreeID[i] + "&parent=" + TreeParents[i] +  "&gparent=" + TreeParents[TreeParents[i]] + "&name=" + escape(TreeNames[i]) + "'>";}
				result+=TreeNames[i];
				if (TreeClickable[i]) {result+="</a>";}
				result+="</font></td>";
				if ((TreeReorder[i]==1) && (TreeParents[i]!=0)) {result+="<td valign=middle nowrap>&nbsp;<img src='gfx/1downarrow.gif' width=9 height=9 vspace=3 onClick='SwapNodes(" + i + "," + NextInFolder(i) + ");'><img src='gfx/1uparrow.gif' width=9 hspace=1 vspace=3 height=9 onClick='SwapNodes(" + i + "," + PreviousInFolder(i) + ");'>";}
				result+="</td></tr></table>";
				if (TreeExpand[i]) {result+=DrawFromNode(i);}
				}
            }
		}
	result+="</td></tr></table>";	

	return result;
	}

function SwapNodes(node1,node2)
	{
	if (TreeParents[node1]!=TreeParents[node2]) {return false;}
	if (TreeReorder[node2]==0) {return false;}
	//This is used for ordering within a single query only so we only need to swap node ID and name (other fields will be the same)
	var name=TreeNames[node1];
	var id=TreeID[node1];
	var icon=TreeIcon[node1];
	var folder=TreeFolder[node1];
	var expand=TreeExpand[node1];
	TreeNames[node1]=TreeNames[node2];
	TreeID[node1]=TreeID[node2];
	TreeIcon[node1]=TreeIcon[node2];
	TreeFolder[node1]=TreeFolder[node2];
	TreeExpand[node1]=TreeExpand[node2];
	TreeNames[node2]=name;
	TreeID[node2]=id;
	TreeIcon[node2]=icon;
	TreeFolder[node2]=folder;
	TreeExpand[node2]=expand;
	
	// Swap the children nodes too.
	for (i=0;i<TreeParents.length;i++)
		{
        if (TreeParents[i]==node1)
            {
			TreeParents[i]=-999;
            }
        }
	for (i=0;i<TreeParents.length;i++)
		{
        if (TreeParents[i]==node2)
            {
			TreeParents[i]=node1;
            }
        }
	for (i=0;i<TreeParents.length;i++)
		{
        if (TreeParents[i]==-999)
            {
			TreeParents[i]=node2;
            }
        }

	UpdateNode(TreeParents[node1]);
	
	var reorder="";
	for (i=0;i<TreeParents.length;i++)
		{
        if (TreeParents[i]==TreeParents[node1])
            {
            if (TreeReorder[i]==1)
            	{
	            if (reorder!="") {reorder+=",";}
	            reorder+=TreeID[i];
	            }
            }
        }
	document.getElementById("treeloader").src="treeloader.php?node=" + node1 + "&id=" + TreeID[node1] + "&reorder=" + escape(reorder);
	}

function ToggleNode(node)
	{ 
    lastclick=node;
	TreeExpand[node]=!TreeExpand[node];

	UpdateNode(TreeParents[node]);
    
    //tree loader
    //if ((TreeExpand[node]==true) && (CountChildren(node)==0))
    if (TreeExpand[node]==true) // modified to always reload nodes.
        {
        EmptyNode(node);
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
    var url="";
    if (TreeSearch[node]!="") {url="&search=" + escape(TreeSearch[node]) + "&submit=true";}
    document.getElementById("treeloader").src="treeloader.php?nocache=" + nocache + "&node=" + node + "&id=" + TreeID[node] + url;
    nocache++;
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

function AddNode(nodeparent,nodeid,nodename,nodefolder,nodeclickable,nodeicon,nodereorder)
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
    TreeSearch[c]="";
    TreeReorder[c]=nodereorder;
	}
	
function NextInFolder(i)
	{
	// Returns next node in folder, used for ordering
	var c=0;
	for (c=i+1;c<TreeParents.length;c++)
		{
		if (TreeParents[c]==TreeParents[i]) {return c;}	
		}
	return TreeParents.length;
	}

function PreviousInFolder(i)
	{
	// Returns previous node in folder, used for ordering
	var c=0;
	for (c=i-1;c>=0;c--)
		{
		if (TreeParents[c]==TreeParents[i]) {return c;}	
		}
	return TreeParents.length;
	}


</script>

<!--<img align=right vspace=40 src="gfx/icons/reload.gif" width=32 height=32 onClick="EmptyNode(lastclick);ReloadNode(lastclick);">-->

<div id="tree"></div>

<iframe src="../blank.html" width="100%" height="100" frameborder=0 border=0 name="treeloader" id="treeloader" xstyle="visibility:hidden;"></iframe>

<script type="text/javascript">
DrawTree();
ToggleNode(0);
</script>

</body>
</html>
