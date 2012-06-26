<?php
include "../include/db.php";
include "../include/authenticate.php"; 
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/search_functions.php"; 


function save_themename()
	{		
		global $baseurl, $link, $themename, $collection_column;
		$sql="update collection set	" . $collection_column . "='" . getvalescaped("rename","") . "' where " . $collection_column . "='" . $themename . "'";
		sql_query($sql);
		header("location:".$baseurl. "/pages/" . $link);
	}

$themes=array();
$themecount=0;
foreach ($_GET as $key => $value) {
	// only set necessary vars
	if (substr($key,0,5)=="theme" && $value!=""){
		$themes[$themecount]=urldecode($value);
		//echo $key . $value . "<br>";
		$themecount++;		
		}	
	}
	
# Work out theme name and level, also construct back link
$link="themes.php?";
for ($x=0;$x<$themecount;$x++)
	{		
	if (!$x==0){$link.="&";}		
	$link.="theme";	
	If ($x==0)
		{
		$collection_column="theme";		
		$link.= "=";		
		}
	else
		{
		$collection_column="theme" . ($x+1);		
		$link.=$x."=";		
		}
	
	if (isset($themes[$x])&&!isset($themes[$x+1]))
		{
		$themename=i18n_get_translated($themes[$x]);	
		}
	else
		{		
		$link.=urlencode($themes[$x]);			
		}
}

if (getval("rename","")!="")
	{
		# Save theme category
		save_themename();		
	}


include "../include/header.php";

if (!checkperm("t")) {
	echo "You do not have permission to edit theme categories. " ;
	exit;
	} 

?>
<p><a href="<?php echo $baseurl . "/pages/" . $link?>">&lt;&lt; <?php echo $lang["back"]?></a></p>
<?php

?>
<div class="BasicsBox">
<h1><?php echo $lang["action-edit"] . " " . $lang["theme"]?></h1>
<p><?php echo text("introtext")?></p>
	<form method=post id="themeform">
		<div class="Question">
			<label for="rename"><?php echo $lang["name"]?></label><input type=text class="stdwidth" name="rename" id="rename" value="<?php echo $themename?>" maxlength="100" />
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





