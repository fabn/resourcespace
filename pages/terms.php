<?php
include "../include/db.php";
# External access support (authenticate only if no key provided)
# No need to check access key for this page as it merely redirects to other pages
$k=getvalescaped("k","");if ($k=="") {include "../include/authenticate.php";}
include "../include/general.php";

$url=getval("url","pages/home.php");

if ($terms_download==false && getval("noredir","")=="") {redirect($url);}

if (getval("save","")!="")
	{
	if (strpos($url,"http")!==false)
		{
		header("Location: " . $url);
		exit();
		}
	else
		{
		redirect($url);
		}
	}
include "../include/header.php";
?>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["termsandconditions"]?></h1>
  <p><?php echo text("introtext")?></p>
  
 	<div class="Question">
	<label><?php echo $lang["termsandconditions"]?></label>
	<textarea readonly class="stdwidth" style="width:70%" rows=20 cols=50><?php echo text("terms")?></textarea>	
	<div class="clearerleft"> </div>
	</div>
	
	<form method="post">
	<input type=hidden name="url" value="<?php echo $url?>">
	<div class="QuestionSubmit">
	<label for="buttons"> </label>		
	<input name="decline" type="button" value="&nbsp;&nbsp;<?php echo $lang["idecline"]?>&nbsp;&nbsp;" onClick="history.go(-1);return false;"/>

	<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["iaccept"]?>&nbsp;&nbsp;" />
	</div>
	</form>
	
</div>

<?php
include "../include/footer.php";
?>