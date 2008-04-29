<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("g") && !checkperm("v")) {exit ("Permission denied.");} # Cannot e-mail if can't see hi-res images. To avoid loophole whereby users could email resources to an external address, and hence download hi-res versions.
include "include/general.php";
include('include/collections_functions.php');

$collection=getvalescaped("c","");
$collectiondata= get_collection($collection);


# Include scriptaculous for display options
$headerinsert.="
<script src=\"js/prototype.js\" type=\"text/javascript\"></script>
<script src=\"js/scriptaculous.js\" type=\"text/javascript\"></script>
<script src=\"js/infobox.js\" type=\"text/javascript\"></script>
";

include "include/header.php";
?>
<div class="BasicsBox">
<h1>Contact Sheet Configuration</h1>

<p>Select sheet size and number of columns for your contact sheet</p>

<form method=post id="contactsheetform" action="contactsheet.php">
<input type=hidden name="c" value="<?=$collection?>">

<div class="Question">
<label>Collection Title</label><div class="Fixed"><?=$collectiondata['name']?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label>Style</label>
<select class="clearerleft" name="sheetstyle" id="sheetstyle" onChange="
	if ($('sheetstyle').value=='list')
		{
		Effect.DropOut('ThumbnailOptions',{duration:0.5});
		}
	else
		{
		Effect.Appear('ThumbnailOptions',{duration:0.5});
		}"">
<option value="thumbnails">Thumbnails</option>
<option value="list">List</option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label>Size</label>
<select class="clearerleft" name="size" id="size"">
<option value="letter">Letter - 8.5"x11"</option>
<option value="legal">Legal - 8.5"x14"</option>
<option value="tabloid">Tabloid - 11"x17"</option>

</select>
<div class="clearerleft"> </div>
</div>

<div id="ThumbnailOptions" class="Question">
<label>Columns</label>
<select class="clearerleft" name="columns" id="ThumbnailOptions"">
<option value=2>2</option>
<option value=3>3</option>
<option value=4 selected>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>
</select>
</div>

<div class="Question">
<label>Orientation</label>
<select class="clearerleft" name="orientation" id="orientation"">
<option value="portrait">portrait</option>
<option value="landscape">landscape</option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;Create&nbsp;&nbsp;" />
</div>

</form>
</div>

<?		
include "include/footer.php";
?>