<?
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","");

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
<a href="edit.php?ref=<?=$ref?>">&lt;&nbsp;<?=$lang["backtoeditresource"]?></a><br / >
<a href="view.php?ref=<?=$ref?>">&lt;&nbsp;<?=$lang["backtoresourceview"]?></a>
</p>

<h1><?=$lang["managealternativefilestitle"]?></h1>
</div>

<form method=post id="fileform">
<input type=hidden name="filedelete" id="filedelete" value="">

<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td><?=$lang["name"]?></td>
<td><?=$lang["description"]?></td>
<td><?=$lang["filetype"]?></td>
<td><?=$lang["filesize"]?></td>
<td><?=$lang["date"]?></td>
<td><div class="ListTools"><?=$lang["tools"]?></div></td>
</tr>

<?
$files=get_alternative_files($ref);
for ($n=0;$n<count($files);$n++)
	{
	?>
	<!--List Item-->
	<tr>
	<td><?=htmlspecialchars($files[$n]["name"])?></td>	
	<td><?=htmlspecialchars($files[$n]["description"])?>&nbsp;</td>	
	<td><?=($files[$n]["file_extension"]==""?$lang["notuploaded"]:htmlspecialchars(strtoupper($files[$n]["file_extension"])) . " " . $lang["file"])?></td>	
	<td><?=formatfilesize($files[$n]["file_size"])?></td>	
	<td><?=nicedate($files[$n]["creation_date"],true)?></td>
	<td><div class="ListTools">
	
	<a href="#" onclick="if (confirm('<?=$lang["filedeleteconfirm"]?>')) {document.getElementById('filedelete').value='<?=$files[$n]["ref"]?>';document.getElementById('fileform').submit();} return false;">&gt;&nbsp;<?=$lang["action-delete"]?></a>

	&nbsp;<a href="alternative_file.php?resource=<?=$ref?>&ref=<?=$files[$n]["ref"]?>">&gt;&nbsp;<?=$lang["action-edit"]?></a>
	
	</td>
	
	</tr>
	<?
	}
?>
</table>
</div>

<!--Create a new file-->
<div class="BasicsBox">
    <h1><?=$lang["addalternativefile"]?></h1>
    <form method="post">
		<div class="Question">
			<label for="newcollection"><?=$lang["name"]?></label>
			<div class="tickset">
			 <div class="Inline"><input type=text name="newfile" id="newfile" value="" maxlength="100" class="shrtwidth"></div>
			 <div class="Inline"><input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["create"]?>&nbsp;&nbsp;" /></div>
			</div>
		<div class="clearerleft"> </div>
	    </div>
	</form>
</div>


</form>

<?
include "../include/footer.php";
?>
