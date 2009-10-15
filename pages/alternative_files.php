<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","",true);

# Fetch resource data.
$resource=get_resource_data($ref);

# Not allowed to edit this resource?
if ((!checkperm("e" . $resource["archive"])) && ($ref>0)) {exit ("Permission denied.");}


# Handle adding a new file
if (getval("newfile","")!="")
	{
	$newfile=add_alternative_file($ref,getvalescaped("newfile",""));
	redirect("pages/alternative_file.php?resource=$ref&ref=$newfile");
	}

# Handle deleting a file
if (getval("filedelete","")!="")
	{
	delete_alternative_file($ref,getvalescaped("filedelete",""));
	}

include "../include/header.php";
?>
<div class="BasicsBox">
<p>
<a href="edit.php?ref=<?php echo $ref?>">&lt;&nbsp;<?php echo $lang["backtoeditresource"]?></a><br / >
<a href="view.php?ref=<?php echo $ref?>">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a>
</p>

<h1><?php echo $lang["managealternativefilestitle"]?></h1>
</div>

<form method=post id="fileform">
<input type=hidden name="filedelete" id="filedelete" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td><?php echo $lang["name"]?></td>
<td><?php echo $lang["description"]?></td>
<td><?php echo $lang["filetype"]?></td>
<td><?php echo $lang["filesize"]?></td>
<td><?php echo $lang["date"]?></td>
<td><div class="ListTools"><?php echo $lang["tools"]?></div></td>
</tr>

<?php
$files=get_alternative_files($ref);
for ($n=0;$n<count($files);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td><?php echo htmlspecialchars($files[$n]["name"])?></td>	
	<td><?php echo htmlspecialchars($files[$n]["description"])?>&nbsp;</td>	
	<td><?php echo ($files[$n]["file_extension"]==""?$lang["notuploaded"]:htmlspecialchars(strtoupper($files[$n]["file_extension"])) . " " . $lang["file"])?></td>	
	<td><?php echo formatfilesize($files[$n]["file_size"])?></td>	
	<td><?php echo nicedate($files[$n]["creation_date"],true)?></td>
	<td><div class="ListTools">
	
	<a href="#" onclick="if (confirm('<?php echo $lang["filedeleteconfirm"]?>')) {document.getElementById('filedelete').value='<?php echo $files[$n]["ref"]?>';document.getElementById('fileform').submit();} return false;">&gt;&nbsp;<?php echo $lang["action-delete"]?></a>

	&nbsp;<a href="alternative_file.php?resource=<?php echo $ref?>&ref=<?php echo $files[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-edit"]?></a>
	
	</td>
	
	</tr>
	<?php
	}
?>
</table>
</div>

<!--Create a new file-->
<div class="BasicsBox">
    <h1><?php echo $lang["addalternativefile"]?></h1>
    <form method="post">
		<div class="Question">
			<label for="newcollection"><?php echo $lang["name"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="newfile" id="newfile" value="" maxlength="100" class="shrtwidth"></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
		<div class="clearerleft"> </div>
		<br />
		<p><a href="upload_java.php?alternative=<?php echo $ref ?>">&gt;&nbsp;<?php echo $lang["alternativebatchupload"] ?></a></p>
	    </div>
	</form>
</div>


</form>

<?php
include "../include/footer.php";
?>
