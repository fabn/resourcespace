<?
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";

$section=getvalescaped("section","");

include "../include/header.php";
?>

<div class="BasicsBox"> 

<? if ($section=="") { ?>
  <h2>&nbsp;</h2>
  <h1><?=$lang["helpandadvice"]?></h1>
  <p><?=text("introtext")?></p>
  
  <div class="VerticalNav">
  <ul>
  <?
  $sections=get_section_list("help");
  for ($n=0;$n<count($sections);$n++)
  	{
  	?>
  	<li><a href="help.php?section=<?=urlencode($sections[$n])?>"><?=htmlspecialchars($sections[$n])?></a></li>
  	<?
  	}
  ?>
  </ul>
  </div>
  
<? } else { ?>
  <h2>&nbsp;</h2>
  <h1><?=$section?></h1>
  <p><?=text($section)?></p>
  <p><a href="help.php">&gt; <?=$lang["backtohelphome"]?></a></p>
<? } ?>
</div>

<?
include "../include/footer.php";
?>