<script type="text/javascript">
<?php 
// value for each option provides the action to perform in detail:

// ref - for multiselector pages, colactionselect needs to have a collection number suffixed. Always include
// confirmation [0 or string] - accept or reject the action (should be a valid lang)
// actionpage - [0 or string] - this page will be executed via ajax (optional, only if you need a background action)
// redirect - [0 or string] - redirect to this page after completion of the action
// frame [main or collections] - which frame to redirect to (main or collections)
// refresh collections [ref]
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
				top.main.location.href=top.main.location.href;
				}
			else{
				top.main.location.href=value[3];
				}
			}
		else {
			top.collections.location.href=value[3];	
			} 
		if (value[5]!='false'){ 
			top.collections.location.replace("<?php echo $baseurl?>/pages/collections.php?ref="+value[0]);
		}
	}
}			
</script>
