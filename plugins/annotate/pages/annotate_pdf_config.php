<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; 
include "../../../include/general.php";
include("../../../include/resource_functions.php");
include("../../../include/collections_functions.php");
include("../../../include/search_functions.php");
include("../include/general.php");

$ref=getvalescaped("ref","");
$col=getvalescaped("col","");

if ($col!=""){
	$is_collection=true;
	$collection=get_collection($col);
	$resources=do_search("!collection".$col);
	set_user_collection($userref,$col);
	refresh_collection_frame();
	$ref="C".$col;$realref=$col; // C allows us to distinguish a collection from a resource in the JS without adding extra params.
} 
else { 
	$is_collection=false;
	$resources=do_search("!list".$ref);
	$realref=$ref;
}

// prune unnannotated resources if necessary
	$annotate=true;
	if ($annotate_pdf_output_only_annotated){
		$resources_modified=array();
		$x=0;
		for ($n=0;$n<count($resources);$n++){
			unset($notes);
			if ($annotate_pdf_output_only_annotated && $resources[$n]['annotation_count']!=0){
				$resources_modified[$x]=$resources[$n];
				$x++;
			} 
		}
		$resources=$resources_modified;
	}
	if (count($resources)==0){$annotate=false;}


# Fetch search details (for next/back browsing and forwarding of search params)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

# Include a couple functions for the Ajax contactsheet update
if ($annotate){
$bodyattribs="onload=\"jQuery().annotate('preview');\"";
}

include "../../../include/header.php";

// a unique id allows us to isolate this page's temporary files. 	
$uniqid=uniqid($ref."-");
$jpghttppath=get_annotate_file_path($realref,false,"jpg");


?>

<?php if ($annotate){?>
<script type="text/javascript" language="JavaScript">
var annotate_previewimage_prefix = "";

(function($) {
	
	 var methods = {
		
		preview : function() { 
			var url = 'annotate_pdf_gen.php';

			var formdata = $('#annotateform').serialize() + '&preview=true'; 

			$.ajax(url,{
			data: formdata,
			success: function(response) {$(this).annotate('refresh',response);},
			complete: function(response) {
				$.ajax("annotate_pdf_gen.php?cleartmp=true&ref=<?php echo $ref?>&uniqid=<?php echo $uniqid?>",{complete: function(response){ $('#error2').html(response.responseText);}});
				$('#error').html(response.responseText);
				if (response.responseText=="nothing"){
					$('#heading').hide();
					$('#configform').hide();
					$('#previewdiv').hide();
					$('#introtext').hide();
					$('#noannotations').show();
					$('#noannotations').html("There are no annotations.");
				} 
			},
			beforeSend: function(response) {loadIt();}
			});
		},
		
		refresh : function( pagecount ) { 

			document.previewimage.src = '<?php echo $jpghttppath;?>?'+ Math.random();
			
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
			$(this).annotate('preview');
		}
	};

  $.fn.annotate = function( method ) {
    
    // Method calling logic
    if ( methods[method] ) {

      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    }  
  
  };
	

})(jQuery)
</script>
<script>
function loadIt() {
   document.previewimage.src = '../../../gfx/images/ajax-loader-on-sheet.gif';}
</script>
<?php } ?>

<div class="BasicsBox" >

<?php if (!$is_collection){?>
<p><a href="../../../pages/view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&annotate=true">&lt; <?php echo $lang["backtoresourceview"]?></a></p>
<?php } else {?>
<p><a href="../../../pages/search.php?search=!collection<?php echo substr($ref,1)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt; <?php echo $lang["backtoresults"]?></a></p>
<?php } ?>

<h1><?php echo $lang["annotatepdfconfig"]?></h1>

<?php if ($annotate){?>
<div id="heading" class="BasicsBox" style="float:left;margin-bottom:0;" >
<p id="introtext"><?php echo $lang["annotatepdfintrotext"]?></p>

</div>
<div style="clear:left;"></div>

<div id="configform" class="BasicsBox" style="width:450px;float:left;margin-top:0;" >

<form method=post name="annotateform" id="annotateform" action="annotate_pdf_gen.php" >
<input type=hidden name="ref" value="<?php echo $ref?>">
<input type=hidden name="uniqid" value="<?php echo $uniqid?>">

<?php if ($is_collection){?>
<div class="Question">
<label><?php echo $lang["collection"]?></label><div class="Fixed"><?php echo $collection['name']?></div>
<div class="clearerleft"> </div>
</div>

<?php } else { ?>
<div class="Question">
<label><?php echo $lang["resourcetitle"]?></label><div class="Fixed"><?php echo $resources[0]['field'.$view_title_field]?></div>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="Question">
<label><?php echo $lang["size"]?></label>
<select class="shrtwidth" name="size" id="size" onChange="jQuery().annotate('preview');	"><?php echo $papersize_select ?>
</select>
<div class="clearerleft"> </div>
</div>

<div name="previewPageOptions" id="previewPageOptions" class="Question" style="display:none">
<label><?php echo $lang['previewpage']?></label>
<select class="shrtwidth" name="previewpage" id="previewpage" onChange="jQuery().annotate('preview');	">
</select>
</div>
<?php if ($annotate_debug){?><div name="error" id="error"></div><?php } ?>
<?php if ($annotate_debug){?><div name="error2" id="error2"></div><?php } ?>
<div class="QuestionSubmit">
<label for="buttons"> </label>	
<input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["preview"]?>&nbsp;&nbsp;" onClick="jQuery().annotate('preview');	"/>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" />
</div>
</form>

</div>

<div id="previewdiv" style="float:left;padding:0px -50px 15px 0;height:425px;margin-right:-50px">
	<img id="previewimage" name="previewimage"/>
</div>

<?php }
 ?>
<div <?php if ($annotate){?>style="display:none;"<?php } ?> id="noannotations"><?php if (!$annotate){?>There are no annotations.<?php } ?></div></div>


<?php		
include "../../../include/footer.php";
?>
