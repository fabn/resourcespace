<?
include "../include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}
include "../include/general.php";

$url=getval("url","home.php");

if ($terms_download==false) {redirect($url);}

if (getval("save","")!="")
	{
	if (strpos($url,"http")!==false)
		{
		header("Location: " . $url);
		exit();
		}
	else
		{
		redirect("pages/" . $url);
		}
	}
include "../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?=$lang["termsandconditions"]?></h1>
  <p><?=text("introtext")?></p>
  
 	<div class="Question">
	<label><?=$lang["termsandconditions"]?></label>
	<textarea readonly class="stdwidth" style="width:70%" rows=20 cols=50><?=text("terms")?></textarea>	
	<div class="clearerleft"> </div>
	</div>
	
	<form method="post">
	<input type=hidden name="url" value="<?=$url?>">
	<div class="QuestionSubmit">
	<label for="buttons"> </label>		
	<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["iaccept"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?
include "../include/footer.php";
?>