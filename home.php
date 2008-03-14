<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";


# Include scriptaculous for fading slideshow.
$headerinsert="
<script src=\"js/prototype.js\" type=\"text/javascript\"></script>
<script src=\"js/scriptaculous.js\" type=\"text/javascript\"></script>
";

include "include/header.php";
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
	    window.setTimeout("image1.src='gfx/homeanim/gfx/" + next_photo + ".jpg';",1000);
     	flip=1;
     	}
	  else
	  	{
	    // image1.style.visibility='visible';
	    Effect.Appear(image1);
	    setTimeout("image2.style.background='url(gfx/homeanim/gfx/" + next_photo + ".jpg)';",1000);
	    flip=0;
		}	  	
     
      last_photo=cur_photo;
      cur_photo=next_photo;
      window.setTimeout("nextPhoto()", 1000 * photo_delay);
}

window.setTimeout("nextPhoto()", 1000 * photo_delay);
</script>

<div class="HomePicturePanel"><div class="HomePicturePanelIN" id='photoholder' style="background-image:url('gfx/homeanim/gfx/2.jpg');"><img src='gfx/homeanim/gfx/1.jpg' alt='' id='image1' width=517 height=350 style="display:none;"></div>
<div class="PanelShadow"></div>
</div>

<? if (checkperm("s")) { ?>
<div class="HomePanel"><div class="HomePanelIN">
  <h2><a href="themes.php"><?=$lang["themes"]?></a></h2>
	<?=text("themes")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
	
	<div class="HomePanel"><div class="HomePanelIN">
  <h2><a href="collection_manage.php"><?=$lang["mycollections"]?></a></h2>
  	<?=text("mycollections")?>
	</div>
		<div class="PanelShadow">
		</div>
	</div>
	
	<div class="HomePanel"><div class="HomePanelIN">
  <h2><a href="help.php"><?=$lang["helpandadvice"]?></a></h2>
  	<?=text("help")?>
	</div>
		<div class="PanelShadow"></div>
	</div>
	
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

include "include/footer.php";
?>