<?php
include '../../../include/db.php';
include '../../../include/authenticate.php'; 
include '../../../include/general.php';
include '../../../include/collections_functions.php';
refresh_collection_frame();
$ref=1;

$headerinsert.="
<script src=\"../../../plugins/colorthemer/js/colorthemer.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
colorthemes_previewimage_prefix = '".addslashes($storageurl)."';
</script>
<script type=\"text/javascript\" src=\"../js/prototype.js\" language=\"javascript\"></script>
<script type=\"text/javascript\" src=\"../js/scriptaculous.js?load=slider\" language=\"javascript\"></script>
";	
$bodyattribs=" onLoad=\"previewColortheme();\"";
include "../../../include/header.php";
?>

<style type="text/css">
			.track {
				background: transparent url(../gfx/slider-images-track-right.png) no-repeat top right;
			}
			.trackcol {
				background: transparent url(../gfx/slider-images-track-right-col.png) no-repeat top right;
			}
</style>

<div class="BasicsBox">
<h1><?php echo $lang["customthemecreation"]?></h1>

<p><?php echo $lang["customthemeintrotext"]?></p>

<div style="float:right;padding:15px 30px 15px 0;"><img id="previewimage" name="previewimage" width=500/></div>

<form method=post name="form" id="form" action="ajax/colortheme_process.php?generate=true" >

<input type=hidden name="hue" id="hue" value=100>
<input type=hidden name="sat" id="sat" value=50>

<div id="StyleOptions" class="Question"  onChange="previewColortheme();">
<label><?php echo $lang["style"]?></label>
<select class="shrtwidth" name="style" id="style" value="greyblu">
<option value="whitegry"><?php echo $lang["white"]?></option>
<option value="greyblu" selected><?php echo $lang["gradient"]?></option>
</select>
</div>

<div id="StyleOptions2" class="Question"  onChange="previewColortheme();">
<label><?php echo $lang["square-rounded"]?></label>
<select class="shrtwidth" name="rounded" id="rounded" value="greyblu">
<option value="true"><?php echo $lang["rounded"]?></option>
<option value="false"><?php echo $lang["square"]?></option>
</select>
</div>

<div class="Question">
<label><?php echo $lang["hue"]?></label>

<div class="Fixed">
<div id="track1" class="trackcol" style="width:250px; height:18px;" >
   <div id="handle1" class="handle" style="width:19px; height:20px;" ><img src="../gfx/slider-images-handle.png" alt="" style="float: left;" /></div>
</div>
<div id="hueval" >100</div>
</div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["saturation"]?></label>

<div class="Fixed">
<div id="track2" class="track" style="width:250px; height:9px;">
   <div id="handle2" class="handle" style="width:19px; height:20px;" ><img src="../gfx/slider-images-handle.png" alt="" style="float: left;" /></div>
</div>
<div id="satval" >50</div>
</div>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>	
<input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["action-preview"]?>&nbsp;&nbsp;" onClick="previewColortheme();"/>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["action-save_colortheme"]?>&nbsp;&nbsp;" />
</div>

</form>
</div> 
<script type="text/javascript">

      
       new Control.Slider('handle1' , 'track1',
      {
           range: $R(0,200),
           sliderValue:100,
          onSlide: function(v) {
               $('hue').value = Math.round(v); $('hueval').innerHTML = Math.round(v);
          },
          onChange: function(v) {
			  previewColortheme();
               $('hue').value = Math.round(v); $('hueval').innerHTML = Math.round(v);
          }
          
       } );
       
       new Control.Slider('handle2' , 'track2',
      {
           range: $R(0,100),
           sliderValue: 50,
          onSlide: function(v) {
               $('sat').value = Math.round(v); $('satval').innerHTML = Math.round(v);
          },
          onChange: function(v) {previewColortheme();
               $('sat').value = Math.round(v); $('satval').innerHTML = Math.round(v);
          }
          
       });
       

</script>    
<?php
include "../../../include/footer.php";
?>
