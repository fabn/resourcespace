<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/collections_functions.php";

# Include scriptaculous for fading slideshow.
$headerinsert="
<script src=\"js/prototype.js\" type=\"text/javascript\"></script>
<script src=\"js/scriptaculous.js\" type=\"text/javascript\"></script>
";

include "include/header.php";

if (!hook("replacehome")) { 

# handle a new path for homeanim graphics. Also, count the files in the folder.
if (!isset($homeanim_folder)){$homeanim_folder="gfx/homeanim/gfx/";}

$dir = $homeanim_folder; 
$filecount = 0; 
$d = dir($dir); 
while ($f = $d->read()) { 
 if(preg_match("/[0-9]+\.(jpg)/",$f)) { 
 if(!is_dir($f)) $filecount++; 
 } 
} 
$homeimages=$filecount;

?>

<script language="Javascript">

var num_photos=<?=$homeimages?>;  // <---- number of photos (/images/slideshow?.jpg)
var photo_delay=5; // <---- photo delay in seconds

var cur_photo=2;
var last_photo=1;
var next_photo=2;

flip=1;

var image1=0;
var image2=0;

function nextPhoto()
    {
      if (cur_photo==num_photos) {next_photo=1;} else {next_photo=cur_photo+1;}

      image1 = document.getElementById("image1");
      image2 = document.getElementById("photoholder");

	  if (flip==0)
	  	{
	    // image1.style.visibility='hidden';
	    Effect.Fade(image1);
	    window.setTimeout("image1.src='<?=$homeanim_folder?>" + next_photo + ".jpg';",1000);
     	flip=1;
     	}
	  else
	  	{
	    // image1.style.visibility='visible';
	    Effect.Appear(image1);
	    setTimeout("image2.style.background='url(<?=$homeanim_folder?>" + next_photo + ".jpg)';",1000);
	    flip=0;
		}	  	
     
      last_photo=cur_photo;
      cur_photo=next_photo;
      window.setTimeout("nextPhoto()", 1000 * photo_delay);
}

window.setTimeout("nextPhoto()", 1000 * photo_delay);
</script>

<div class="HomePicturePanel"><div class="HomePicturePanelIN" id='photoholder' style="background-image:url('<?=$homeanim_folder?>2.jpg');"><img src='<?=$homeanim_folder?>1.jpg' alt='' id='image1' width=517 height=350 style="display:none;"></div>
<div class="PanelShadow"></div>
</div>

<? if (checkperm("s")) { ?>

<? if ($home_themeheaders && $enable_themes) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="themes.php"><?=$lang["themes"]?></a></h2>
	<?=text("themes")?>
	<?
	$headers=get_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		?>
		<p>&gt;&nbsp;<a href="themes.php?header=<?=urlencode($headers[$n])?>"><?=i18n_get_translated($headers[$n])?></a></p>
		<?
		}
	?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<? } ?>


<? if ($home_themes && $enable_themes) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="themes.php"><?=$lang["themes"]?></a></h2>
	<?=text("themes")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<? } ?>
	
<? if ($home_mycollections) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="collection_manage.php"><?=$lang["mycollections"]?></a></h2>
	<?=text("mycollections")?>
	</div>
	<div class="PanelShadow">
	</div>
	</div>
<? } ?>


<? if ($home_helpadvice) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="help.php"><?=$lang["helpandadvice"]?></a></h2>
	<?=text("help")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<? } ?>

	
	<div class="clearerleft"></div>

<div class="BasicsBox">
    <h1><?=text("welcometitle")?></h1>
    <p><?=text("welcometext")?></p>
</div>
<? }  else { ?>
<div class="BasicsBox">
    <h1><?=text("restrictedtitle")?></h1>
    <p><?=text("restrictedtext")?></p>
</div>
<? }

} // End of ReplaceHome hook

include "include/footer.php";
?>