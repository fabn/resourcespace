<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/image_processing.php";

$ref=getvalescaped("ref","",true);

$resource=getvalescaped("resource","",true);

# Fetch resource data.
$resourcedata=get_resource_data($resource);

# Not allowed to edit this resource?
if ((!checkperm("e" . $resourcedata["archive"])) && ($resource>0)) {exit ("Permission denied.");}


# Fetch alternative file data
$file=get_alternative_file($resource,$ref);if ($file===false) {exit("Alternative file not found.");}

if (getval("name","")!="")
	{
	# Save file data
	save_alternative_file($resource,$ref);
	redirect ("pages/alternative_files.php?ref=$resource");
	}

	
include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["editalternativefile"]?></h1>


<form method="post" class="form" id="fileform" enctype="multipart/form-data">
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

<div class="Question">
<label for="userfile"><?php echo $file["file_extension"]==""?$lang["clickbrowsetolocate"]:$lang["uploadreplacementfile"]?></label>
<input type=file name=userfile id=userfile>
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