<?php
/**
 * Display news items  * 
 * @package ResourceSpace
 */

include dirname(__FILE__)."/../../../include/db.php";
include dirname(__FILE__)."/../../../include/authenticate.php";
include dirname(__FILE__)."/../../../include/general.php";
include_once dirname(__FILE__)."/../inc/news_functions.php";
global $baseurl;
$max = get_news_ref("max");
$min = get_news_ref("min");
$maxref = $max[0]["max(ref)"];
$minref = $min[0]["min(ref)"];
If (isset($debugtext)){$debugtext.=" AND MORE: ";}else{$debugtext="Debug: ";}
If (!isset($ref)){$ref=getvalescaped("ref","",true);}

if ((getval("edit","")!="") && (checkperm("o")))
	{
	header("location:".$baseurl."/plugins/news/pages/news_content_edit.php?ref=".$ref);
	}
		
if ((getval("previous","")!=""))
	{
	$ref=getvalescaped("ref","",true);$ref--;
	header("location:".$baseurl."/plugins/news/pages/news.php?ref=".$ref);
	}
if ((getval("next","")!=""))
	{
	$ref=getvalescaped("ref","",true);$ref++;
	header('location: '.$baseurl.'/plugins/news/pages/news.php?ref='.$ref);
	}

if ($ref=="")
	{
	header("location: ".$baseurl."/plugins/news/pages/news.php?ref=".$maxref);
	exit;
	}

$newsdisplay=get_news($ref,"","");
If (!$newsdisplay){
	$debugtext.= " no news found";	
	While (!$newsdisplay)
		{
		if ((getval("next","")!=""))	
			{
			$ref++;
			If ($ref>$maxref){
				$ref=$minref;
				header('location: '.$baseurl.'/plugins/news/pages/news.php?ref='.$ref);
				exit;
				}				
			}
		else
			{
			$ref--;
			If ($ref<$minref){
				$ref=$maxref;	
				header('location: '.$baseurl.'/plugins/news/pages/news.php?ref='.$ref);
				exit;
				}	
			}
		$newsdisplay=get_news($ref,"","");
		}
	header('location: '.$baseurl.'/plugins/news/pages/news.php?ref='.$ref);	
	exit;

	}
	
include dirname(__FILE__)."/../../../include/header.php";

?>
 

	<div>
	<form action="<?php echo $baseurl . '/plugins/news/pages/news.php?ref=' . $ref ?>" method="post">
			<label for="buttons"></label>		
			<input name="previous" type="submit" value="&lt;"/>	
<?php
if (checkperm("o")) 
	{?>
				<label for="buttons"> </label>		
				<input name="edit" type="submit" value="<?php echo $lang["action-edit"]?>"/>
		
	<?php
	}
?>
			<label for="buttons"> </label>		
			<input name="next" type="submit" value="&gt;"/>
	</div>
	</form>	


<div class="BasicsBox" id ="NewsDisplayBox"> 
  <h1><?php echo htmlspecialchars($newsdisplay[0]["title"]);?></h1>
  <hr>
  <div id="NewsBodyDisplay" ><p><?php echo ($newsdisplay[0]["body"]);?></p> </div>
   <h2><?php echo $newsdisplay[0]["date"];?></h2>

</div>






