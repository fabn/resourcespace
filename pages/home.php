<?php
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
include "../include/resource_functions.php";
include "../include/collections_functions.php";

hook("homeheader");

include "../include/header.php";

if (!hook("replacehome")) { 

if (!hook("replaceslideshow")) { 

# Count the files in the configured $homeanim_folder.
$dir = dirname(__FILE__) . "/../" . $homeanim_folder; 
$filecount = 0; 
$checksum=0; # Work out a checksum which is the total of all the image files in bytes - used in image URLs to force a refresh if any of the images change.
$d = scandir($dir); 
$reslinks=array();
foreach ($d as $f) { 
 if(preg_match("/[0-9]+\.(jpg)/",$f))
 	{ 
 	$filecount++; 
	$checksum+=filesize($dir . "/" . $f);
	$linkfile=substr($f,0,(strlen($f)-4)) . ".txt";
	$reslinks[$filecount]="";
	if(file_exists("../" . $homeanim_folder . "/" . $linkfile))
		{
		$linkref=file_get_contents("../" . $homeanim_folder . "/" . $linkfile);
		$linkaccess = get_resource_access($linkref);
		if (($linkaccess!=="") && (($linkaccess==0) || ($linkaccess==1))){$reslinks[$filecount]=$baseurl . "/pages/view.php?ref=" . $linkref;}
		}	
	}
 } 

$homeimages=$filecount;

if ($filecount>1) { # Only add Javascript if more than one image.
?>
<script type="text/javascript">

var num_photos=<?php echo $homeimages?>;  // <---- number of photos (/images/slideshow?.jpg)
var photo_delay=5; // <---- photo delay in seconds
var link = new Array();

<?php 
$l=1;
foreach ($reslinks as $reslink)
	{
	echo "link[" . $l . "]=\"" .  $reslink . "\";";
	$l++;
	}
?>

var cur_photo=2;
var last_photo=1;
var next_photo=2;

flip=1;

var image1=0;
var image2=0;



function nextPhoto()
    {
    if (!document.getElementById('image1')) {return false;} /* Photo slideshow no longer available (AJAX page move) */
    
      if (cur_photo==num_photos) {next_photo=1;} else {next_photo=cur_photo+1;}
	
	
      image1 = document.getElementById("image1");
      image2 = document.getElementById("photoholder");
      sslink = document.getElementById("slideshowlink");
	  linktarget=link[cur_photo];
	  if (flip==0)
	  	{
	    // image1.style.visibility='hidden';
	    //Effect.Fade(image1);
		jQuery('#image1').fadeOut(1000)
	    window.setTimeout("image1.src='<?php echo $baseurl . "/" . $homeanim_folder?>/" + next_photo + ".jpg?checksum=<?php echo $checksum ?>';if(linktarget!=''){jQuery('#slideshowlink').attr('href',linktarget);}else{jQuery('#slideshowlink').removeAttr('href');}",1000);
     	flip=1;
     	}
	  else
	  	{
	    // image1.style.visibility='visible';
	    //Effect.Appear(image1);
		jQuery('#image1').fadeIn(1000)
	    window.setTimeout("image2.style.background='url(<?php echo $baseurl . "/" .  $homeanim_folder?>/" + next_photo + ".jpg?checksum=<?php echo $checksum ?>)';if(linktarget!=''){jQuery('#slideshowlink').attr('href',linktarget);}else{jQuery('#slideshowlink').removeAttr('href');}",1000);
	    flip=0;
		}	  	
     
      last_photo=cur_photo;
      cur_photo=next_photo;
      timers.push(window.setTimeout("nextPhoto()", 1000 * photo_delay));
}

jQuery(document).ready( function ()
	{ 
    /* Clear all old timers */
    for (var i = 0; i < timers.length; i++)
    	{
	    clearTimeout(timers[i]);
	    }
	timers.push(window.setTimeout("nextPhoto()", 1000 * photo_delay));
	}
	);
	
</script>
<?php } ?>

<div class="HomePicturePanel"

<?php if (isset($home_slideshow_width)) {
	echo "style=\"";
	$slide_width = $home_slideshow_width + 2;
	echo"width:" .  (string)$slide_width ."px; ";
	echo "\" ";
	}
	?>>
	
	<a id="slideshowlink" target="main"
	<?php
	 
	$linkurl="#";
	if(file_exists("../" . $homeanim_folder . "/1.txt"))
		{
		$linkres=file_get_contents("../" . $homeanim_folder . "/1.txt");
		$linkaccess = get_resource_access($linkres);
		if (($linkaccess!=="") && (($linkaccess==0) || ($linkaccess==1))) {$linkurl=$baseurl . "/pages/view.php?ref=" . $linkres;}
		echo "href=\"" . $linkurl ."\" ";
		}	
	
	?>
	\>
	
	<div class="HomePicturePanelIN" id='photoholder' style="
	<?php
	if (isset($home_slideshow_height)){		
		echo"height:" .  (string)$home_slideshow_height ."px; ";
		} 
	?>
		
	background-image:url('<?php echo $baseurl . "/" . $homeanim_folder?>/1.jpg?checksum=<?php echo $checksum ?>');">
	
	
	
	<img src='<?php echo $baseurl . "/" .  $homeanim_folder?>/2.jpg?checksum=<?php echo $checksum ?>' alt='' id='image1' style="display:none;<?php
	if (isset($home_slideshow_width)){
		echo"width:" .  $home_slideshow_width ."px; ";
		}
	if (isset($home_slideshow_height)){
		echo"height:" .  $home_slideshow_height ."px; ";
		} 
	?>">
	</div>
	</a>
	
<div class="PanelShadow"></div>
</div>
<?php } # End of hook replaceslideshow
?>

<?php if (checkperm("s")) {
	hook("homebeforepanels");
?>

<?php if ($home_themeheaders && $enable_themes) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="themes.php"><?php echo $lang["themes"]?></a></h2>
	<?php echo text("themes")?>
	<br />	<br />
	<select style="width:140px;" onChange="window.location=this.value;">
	<option value=""><?php echo $lang["select"] ?></option>
	<?php
	$headers=get_theme_headers();
	for ($n=0;$n<count($headers);$n++)
		{
		?>
		<option value="themes.php?header=<?php echo urlencode($headers[$n])?>"><?php echo i18n_get_translated(str_replace("*","",$headers[$n]))?></option>
		<?php
		}
	?>
	</select>
	<br />&gt;&nbsp;<a href="themes.php"><?php echo $lang["viewall"] ?></a>
	</div>
	<div class="PanelShadow"></div>
	</div>
<?php } ?>


<?php if ($home_themes && $enable_themes) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="themes.php"><?php echo $lang["themes"]?></a></h2>
	<?php echo text("themes")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<?php } ?>
	
<?php if ($home_mycollections && !checkperm("b")) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="collection_manage.php"><?php echo $lang["mycollections"]?></a></h2>
	<?php echo text("mycollections")?>
	</div>
	<div class="PanelShadow">
	</div>
	</div>
<?php } ?>

<?php if ($home_advancedsearch) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="search_advanced.php"><?php echo $lang["advancedsearch"]?></a></h2>
	<?php echo text("advancedsearch")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<?php } ?>

<?php if ($home_mycontributions && (checkperm("d") || (checkperm("c") && checkperm("e0")))) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="contribute.php"><?php echo $lang["mycontributions"]?></a></h2>
	<?php echo text("mycontributions")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<?php } ?>

<?php if ($home_helpadvice) { ?>
	<div class="HomePanel"><div class="HomePanelIN">
	<h2><a href="help.php"><?php echo $lang["helpandadvice"]?></a></h2>
	<?php echo text("help")?>
	</div>
	<div class="PanelShadow"></div>
	</div>
<?php } ?>
	
<?php 
/* ------------ Customisable home page panels ------------------- */
if (isset($custom_home_panels))
	{
	for ($n=0;$n<count($custom_home_panels);$n++)
		{
		if (!hook("panelperm")) { 
		?>
		<div class="HomePanel"><div class="HomePanelIN" <?php if ($custom_home_panels[$n]["text"]=="") {?>style="min-height:0;"<?php } ?>>
		<h2><a href="<?php echo $custom_home_panels[$n]["link"] ?>" <?php if (isset($custom_home_panels[$n]["additional"])){ echo $custom_home_panels[$n]["additional"];} ?>><?php echo i18n_get_translated($custom_home_panels[$n]["title"]) ?></a></h2>
		<?php echo i18n_get_translated($custom_home_panels[$n]["text"]) ?>
		</div>
		<div class="PanelShadow"></div>
		</div>
		<?php
		} // end hook 'panelperm'
		}
	}
?>

	<div class="clearerleft"></div>

<div class="BasicsBox">
    <h1><?php echo text("welcometitle")?></h1>
    <p><?php echo text("welcometext")?></p>
</div>
<?php }  else { ?>
<div class="BasicsBox">
    <h1><?php echo text("restrictedtitle")?></h1>
    <p><?php echo text("restrictedtext")?></p>
</div>
<?php }

} // End of ReplaceHome hook

include "../include/footer.php";
?>
