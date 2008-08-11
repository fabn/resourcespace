<div class="Fixed">
<div id="statusbox" class="CategoryBox"></div>

<div><a href="#" onclick="if (document.getElementById('tree').style.display!='block') {document.getElementById('tree').style.display='block';} else {document.getElementById('tree').style.display='none';} return false;">&gt; <?=$lang["showhidetree"]?></a>
&nbsp;
<a href="#" onclick="if (confirm('<?=$lang["clearcategoriesareyousure"]?>')) {DeselectAll();} return false;">&gt; <?=$lang["clearall"]?></a>
</div>

<div id="tree" class="CategoryTree">&nbsp;</div>
<script language="JavaScript">

var TreeParents=new Array();
var TreeNames=new Array();
var TreeExpand=new Array();
var TreeID=new Array();
var TreeClickable=new Array();
var TreeChecked=new Array();
var lastclick=0;

var TermStatus=0;

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
            if (TreeExpand[i]) {icon="<img xalign=middle width=11 height=11 hspace=3 src=admin/gfx/node_ex.gif";} else {icon="<img xalign=middle hspace=3 width=11 height=11 src=admin/gfx/node_unex.gif";}
            if (CountChildren(i)==0)
                {icon="<img xalign=top src=admin/gfx/sp.gif width=11 height=11 hspace=3>";}
            else
                {icon+=" onClick=\"ToggleNode(" + i + ");\">";}
        	icon2="";
            result+=icon + icon2 + "<font size=2>";
            checked="";if (TreeChecked[i]==1) {checked="checked";}
            if (TreeClickable[i]) {result+="<input name='' type=checkbox id=checkbox" + i + " " + checked + " onclick=\"CheckNode(" + i + ");\">";}
            result+=TreeNames[i];
            result+="</font><br>";
            if (TreeExpand[i]) {result+=DrawFromNode(i);}
            }
		}
	result+="</td></tr></table>";	

	return result;
	}

function CheckNode(node)
	{
	if (TreeChecked[node]==0) 
		{
		TreeChecked[node]=1;
		//Make sure all parents are ticked
		var p=TreeParents[node];
		while (p>-1)
			{
			if (TreeChecked[p]==0)
				{
				CheckNode(p);
				unode=TreeParents[p];
				if (unode==-1) {DrawTree();} else {UpdateNode(unode);}
				}
			var p=TreeParents[p];
			}
		}
	else
		{
		TreeChecked[node]=0;ResetChildren(node);
		var p=TreeParents[node];
		if (p!=-1)
			{
			if ((CountTickedChildren(p)==0) && (TreeChecked[p]==1)) {CheckNode(p);UpdateNode(TreeParents[p]);}
			}
		UpdateNode(node);
		}
	UpdateStatusBox();
	UpdateHiddenField();
	}
	
function UpdateStatusBox()
	{
	var nodes="";
	for (i=0;i<TreeParents.length;i++)
		{
		if (TreeChecked[i]==1)
			{
			var c=CountTickedChildren(i);
			if (c==0)
				{
				nodes+=DescribeNode(i) + "<br/>";
				}	
			}	
		}
	if (nodes=="")
		{document.getElementById("statusbox").innerHTML="<b> <?=$lang["nocategoriesselected"]?> </b>";}
	else
		{
		document.getElementById("statusbox").innerHTML=nodes;
		}
	}
	
function StatusReset()
	{
	//Set the status box to empty
	document.getElementById("statusbox").innerHTML="";
	}

function DescribeNode(node)
	{
	// Returns a string containing the node's 'path'
	var path=TreeNames[node];
	var p=TreeParents[node];
	while (p>-1)
		{
		path=TreeNames[p] + " / " + path;
		var p=TreeParents[p];
		}
	return path;
	}
	
function ResetChildren(node)
	{
	var p=0;
    for (p=0;p<TreeParents.length;p++)
		{
		if (TreeParents[p]==node) {TreeChecked[p]=0;ResetChildren(p);UpdateNode(p);}	
		}
	}
	
function ToggleNode(node)
	{ 
    lastclick=node;
	TreeExpand[node]=!TreeExpand[node];

	UpdateNode(TreeParents[node]);
    
    //tree loader
    if ((TreeExpand[node]==true) && (CountChildren(node)==0))
        {
        //ReloadNode(node);
        }
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

function CountTickedChildren(node)
	{
	var count=0;
	var i=0;
	for (i=0;i<TreeParents.length;i++)
		{
		if ((TreeParents[i]==node) && (TreeChecked[i]==1)) {count++;}	
		}
	return count;
	}

function CountCheckedRootLevels()
	{
	var count=0;
	var i=0;
	for (i=0;i<TreeParents.length;i++)
		{
		if ((TreeParents[i]==-1) && (TreeChecked[i]==1)) {count++;}	
		}
	return count;
	}

function DeselectAll()
	{
	var i=0;
	for (i=0;i<TreeParents.length;i++)
		{
		TreeChecked[i]=0;
		}
	DrawTree();
	UpdateStatusBox();
	UpdateHiddenField();
	}

function DrawTree()
	{
	document.getElementById("tree").innerHTML=DrawFromNode(-1,false);
	}

function AddNode(nodeparent,nodeid,nodename,nodeclickable,nodechecked,nodeexpand)
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
    TreeClickable[c]=nodeclickable;
    TreeChecked[c]=nodechecked;
    TreeExpand[c]=nodeexpand;
	}

function ResolveParents()
	{
	for (c=0;c<TreeParents.length;c++)
		{
    	//resolve nodeparent to internal node id
	    var p=0;found=false;
	    for (p=0;p<TreeID.length;p++)
			{
			if (TreeID[p]==TreeParents[c]) {found=true;break;}	
			}
	    if (found) {TreeParents[c]=p;}
	    if (TreeParents[c]==-1) {TreeParents[c]=-1;}
		}
	}

function UpdateHiddenField()
	{
	var f="";
	for (p=0;p<TreeID.length;p++)
		{
		if (TreeChecked[p]==1)
			{
			f+="," + TreeNames[p];
			}	
		}
	document.getElementById("category").value=f;
	if (UpdateResultCount) {UpdateResultCount();}
	}
<?
# Load the tree
$checked=explode(",",$value);
$class=explode("\n",$options);

for ($t=0;$t<count($class);$t++)
	{
	$s=explode(",",$class[$t]);
	if (count($s)==3)
		{
		$nodefolder=1;
		$nodechecked=0;if (in_array(trim($s[2]),$checked)) {$nodechecked=1;}
		$nodeexpand=0;if (($nodefolder==1) && ($nodechecked==1)) {$nodeexpand=1;}
		?>
		AddNode(<?=$s[1]-1?>,<?=$t?>,"<?=str_replace("\"","\\\"",trim($s[2]))?>",1,<?=$nodechecked?>,<?=$nodeexpand?>);	
		<?
		}
	}
?>
ResolveParents();
DrawTree();
UpdateStatusBox();

</script>

<input type=hidden name="<?=$name?>" id="category" value="<?=$value?>">
</div>