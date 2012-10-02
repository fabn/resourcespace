<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/image_processing.php";

$ref=getvalescaped("ref","",true);

$search=getvalescaped("search","");
$offset=getvalescaped("offset","",true);
$order_by=getvalescaped("order_by","");
$archive=getvalescaped("archive","",true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}

$default_sort="DESC";
if (substr($order_by,0,5)=="field"){$default_sort="ASC";}
$sort=getval("sort",$default_sort);

$resource=getvalescaped("resource","",true);

# Fetch resource data.
$resourcedata=get_resource_data($resource);

# Not allowed to edit this resource?
if ((!checkperm("e" . $resourcedata["archive"])) && ($resource>0)) {exit ("Permission denied.");}


# Fetch alternative file data
$file=get_alternative_file($resource,$ref);if ($file===false) {exit("Alternative file not found.");}

if (getval("name","")!="")
	{
	hook("markmanualupload");
	# Save file data
	save_alternative_file($resource,$ref);
	hook ("savealternatefiledata");
	redirect ("pages/alternative_files.php?ref=$resource&search=".urlencode($search)."&offset=$offset&order_by=$order_by&sort=$sort&archive=$archive");
	}

	
include "../include/header.php";
?>
<div class="BasicsBox">
<p>
<a href="alternative_files.php?ref=<?php echo $resource?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtomanagealternativefiles"]?></a><br / >
<a href="edit.php?ref=<?php echo $resource?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoeditresource"]?></a><br / >
<a href="view.php?ref=<?php echo $resource?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a>
</p>
<?php if ($alternative_file_resource_preview){ 
		$imgpath=get_resource_path($resourcedata['ref'],true,"col",false);
		if (file_exists($imgpath)){ ?><img src="<?php echo get_resource_path($resourcedata['ref'],false,"col",false);?>"/><?php } 
	} ?>
	<?php if ($alternative_file_resource_title){ 
		echo "<h2>".i18n_get_translated($resourcedata['field'.$view_title_field])."</h2><br/>";
	}?>
	
<h1><?php echo $lang["editalternativefile"]?></h1>


<form method="post" class="form" id="fileform" enctype="multipart/form-data" action="alternative_file.php">
<input type="hidden" name="MAX_FILE_SIZE" value="500000000">
<input type=hidden name=ref value="<?php echo $ref?>">
<input type=hidden name=resource value="<?php echo $resource?>">


<div class="Question">
<label><?php echo $lang["resourceid"]?></label><div class="Fixed"><?php echo $resource?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="name"><?php echo $lang["name"]?></label><input type=text class="stdwidth" name="name" id="name" value="<?php echo $file["name"]?>" maxlength="100">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="name"><?php echo $lang["description"]?></label><input type=text class="stdwidth" name="description" id="description" value="<?php echo $file["description"]?>" maxlength="200">
<div class="clearerleft"> </div>
</div>

<?php
	// if the system is configured to support a type selector for alt files, show it
	if (isset($alt_types) && count($alt_types) > 1){
		echo "<div class='Question'>\n<label for='alt_type'>".$lang["alternatetype"]."</label><select name='alt_type' id='alt_type'>";
		foreach($alt_types as $thealttype){
			//echo "thealttype:$thealttype: / filealttype:" . $file['alt_type'].":";
			if ($thealttype == $file['alt_type']){$alt_type_selected = " selected='selected'"; } else { $alt_type_selected = ''; }
			$thealttype = htmlspecialchars($thealttype,ENT_QUOTES);
			echo "\n   <option value='$thealttype' $alt_type_selected >$thealttype</option>";
		}
		echo "\n</select>\n<div class='clearerleft'> </div>\n</div>";
	}
?>


<div class="Question">
<label for="userfile"><?php echo $file["file_extension"]=="" ? $lang["file"] : $lang["uploadreplacementfile"] ?></label>
<input type=file name=userfile id=userfile size="80">
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?php		
include "../include/footer.php";
?>
