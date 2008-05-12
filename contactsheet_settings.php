<?
include "include/db.php";
include "include/authenticate.php"; 
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
<h1><?=$lang["contactsheetconfiguration"]?></h1>

<p><?=$lang["contactsheetintrotext"]?></p>

<form method=post id="contactsheetform" action="contactsheet.php">
<input type=hidden name="c" value="<?=$collection?>">

<div class="Question">
<label><?=$lang["collectionname"]?></label><div class="Fixed"><?=$collectiondata['name']?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?=$lang["display"]?></label>
<select class="stdwidth" name="sheetstyle" id="sheetstyle" onChange="
	if ($('sheetstyle').value=='list')
		{
		Effect.DropOut('ThumbnailOptions',{duration:0.5});
		}
	else
		{
		Effect.Appear('ThumbnailOptions',{duration:0.5});
		}"">
<option value="thumbnails"><?=$lang["thumbnails"]?></option>
<option value="list"><?=$lang["list"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?=$lang["size"]?></label>
<select class="stdwidth" name="size" id="size"">
<option value="a4">A4 - 210mm x 297mm</option>
<option value="a3">A3 - 297mm x 420mm</option>
<option value="letter">US Letter - 8.5" x 11"</option>
<option value="legal">US Legal - 8.5" x 14"</option>
<option value="tabloid">US Tabloid - 11" x 17"</option>

</select>
<div class="clearerleft"> </div>
</div>

<div id="ThumbnailOptions" class="Question">
<label><?=$lang["columns"]?></label>
<select class="stdwidth" name="columns" id="ThumbnailOptions"">
<option value=2>2</option>
<option value=3>3</option>
<option value=4 selected>4</option>
<option value=5>5</option>
<option value=6>6</option>
<option value=7>7</option>
</select>
</div>

<div class="Question">
<label><?=$lang["orientation"]?></label>
<select class="stdwidth" name="orientation" id="orientation">
<option value="portrait"><?=$lang["portrait"]?></option>
<option value="landscape"><?=$lang["landscape"]?></option>
</select>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["create"]?>&nbsp;&nbsp;" />
</div>

</form>
</div>

<?		
include "include/footer.php";
?>