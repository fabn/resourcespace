<script type="text/javascript">
<?php 
// value for each option provides the action to perform in detail:

// ref - for multiselector pages, colactionselect needs to have a collection number suffixed. Always include
// confirmation [0 or string] - accept or reject the action (should be a valid lang)
// actionpage - [0 or string] - this page will be executed via ajax (optional, only if you need a background action)
// redirect - [0 or string] - redirect to this page after completion of the action
// frame [main or collections] - which frame to redirect to (main or collections)
// refresh collections [ref]
if (checkperm("b")){$window="";$colwindow="";} else {$window="top.main.";$colwindow="top.collections.";}
?>

function colAction(value){
	var value=value.split("|");
	var confirmaction=value[1]; 
	var confirmed=true;
	var ajaxrequest=value[2]; 
	if (value[1]!="0"){
		if (!confirm(confirmaction)){ alert('true');
			<?php if ($pagename!="collection_manage" && $pagename!="collection_public" && $pagename!="themes"){?>colactions.<?php } ?>colactionselect.value='';confirmed=false;return false;
		}
		else {
			confirmed=true;
		}
	}
	
	if (confirmed){
		if (value[2]!="0"){
			var wait= new Ajax.Request(value[2]);
		}
		if (value[3]!="0" && value[4]=="main"){
			if (value[3]=="top"){
				<?php echo $window?>location.href=top.main.location.href;
				}
			else{
				<?php echo $window?>location.href=value[3];
				}
			}
		else {
			<?php if (!checkperm("b")){?>
			<?php echo $colwindow?>location.href=value[3];	
			<?php } ?>
			} 
		if (value[5]!='false'){ 
			<?php if (!checkperm("b")){?>
			<?php echo $colwindow?>location.replace("<?php echo $baseurl?>/pages/collections.php?ref="+value[0]);
			<?php } ?>
		}
	}
}			
</script>
