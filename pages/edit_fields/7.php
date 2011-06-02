<?php /* -------- Category tree ---------------- */ 

$options=$fields[$n]["options"];

global $lang,$baseurl,$css_reload_key,$category_tree_show_status_window;

?><div class="Fixed">

<?php if (!(isset($treeonly) && $treeonly==true))
	{
	?>
<div id="<?php echo $name?>_statusbox" class="CategoryBox"<?php if (!$category_tree_show_status_window) { ?>style="display:none;"<?php } ?>></div>

<div><a href="#" onclick="if (document.getElementById('<?php echo $name?>_tree').style.display!='block') {document.getElementById('<?php echo $name?>_tree').style.display='block';} else {document.getElementById('<?php echo $name?>_tree').style.display='none';} return false;">&gt; <?php echo $lang["showhidetree"]?></a>
&nbsp;
<a href="#" onclick="if (confirm('<?php echo $lang["clearcategoriesareyousure"]?>')) {DeselectAll('<?php echo $name?>');} return false;">&gt; <?php echo $lang["clearall"]?></a>
</div>

<input type="hidden" name="<?php echo $name?>" id="<?php echo $name?>_category" value="<?php echo $value?>">
<?php } ?>

<div id="<?php echo $name?>_tree" class="CategoryTree" <?php if ($category_tree_open) { ?>style="display:block;"<?php } ?>>&nbsp;</div>

<script type="text/javascript">


TreeParents["<?php echo $name?>"]=new Array();
TreeNames["<?php echo $name?>"]=new Array();
TreeExpand["<?php echo $name?>"]=new Array();
TreeID["<?php echo $name?>"]=new Array();
TreeClickable["<?php echo $name?>"]=new Array();
TreeChecked["<?php echo $name?>"]=new Array();

nocategoriesmessage="<?php echo $lang["nocategoriesselected"] ?>";

<?php
# Load the tree
$checked=explode(",",strtolower($value));
$class=explode("\n",$options);

for ($t=0;$t<count($class);$t++)
	{
	$s=explode(",",$class[$t]);
	if (count($s)==3)
		{
		$nodefolder=1;
		$nodechecked=0;if (in_array(trim(strtolower($s[2])),$checked)) {$nodechecked=1;}
		$nodeexpand=0;if (($nodefolder==1) && ($nodechecked==1)) {$nodeexpand=1;}
		# Add this node0
		?>AddNode("<?php echo $name?>",<?php echo $s[1]-1?>,<?php echo $t?>,"<?php echo str_replace("\"","\\\"",trim($s[2]))?>",1,<?php echo $nodechecked?>,<?php echo $nodeexpand?>);<?php
		}
	}
?>
ResolveParents("<?php echo $name?>");
DrawTree("<?php echo $name?>");
UpdateStatusBox("<?php echo $name?>");
UpdateHiddenField("<?php echo $name?>");

</script>

</div>
