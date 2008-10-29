<?
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","");

$resource=getvalescaped("resource","");

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
<h1><?=$lang["editalternativefile"]?></h1>


<form method="post" class="form" id="fileform" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="500000000">
<input type=hidden name=ref value="<?=$ref?>">
<input type=hidden name=resource value="<?=$resource?>">


<div class="Question">
<label><?=$lang["resourceid"]?></label><div class="Fixed"><?=$resource?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="name"><?=$lang["name"]?></label><input type=text class="stdwidth" name="name" id="name" value="<?=$file["name"]?>" maxlength="100">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="name"><?=$lang["description"]?></label><input type=text class="stdwidth" name="description" id="description" value="<?=$file["description"]?>" maxlength="200">
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="userfile"><?=$file["file_extension"]==""?$lang["clickbrowsetolocate"]:$lang["uploadreplacementfile"]?></label>
<input type=file name=userfile id=userfile>
<div class="clearerleft"> </div>
</div>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["save"]?>&nbsp;&nbsp;" />
</div>
</form>
</div>

<?		
include "../include/footer.php";
?>