<?php
include "../include/db.php";
include "../include/authenticate.php"; if (!checkperm("c") && !checkperm("d")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/resource_functions.php";


# Archive state
$archive=getvalescaped("archive",0);
if (!checkperm("e" . $archive)) {exit ("Permission denied.");}

$resource_type=getvalescaped("resource_type","");
if ($resource_type!="")
	{
	redirect("pages/edit.php?ref=" . create_resource($resource_type,$archive,$userref));
	}


include "../include/header.php";
?>

<div class="BasicsBox">
<h1><?php echo $lang["createnewresource"]?></h1>

<form method=post>
<input type=hidden name="archive" value="<?php echo $archive?>">

<div class="Question">
<label for="resourcetype"><?php echo $lang["resourcetype"]?></label>
<div class="tickset">
<div class="Inline"><select name="resource_type" class="shrtwidth">
<?php
$types=get_resource_types();
for ($n=0;$n<count($types);$n++)
	{
	?><option value="<?php echo $types[$n]["ref"]?>"><?php echo $types[$n]["name"]?></option><?php
	}
?></select></div>
<div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
</div>
<div class="clearerleft"> </div>
</div>
</form>

</div>

<?php
include "../include/footer.php";
?>