<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";


if (!isset($magictouch_account_id)) {$magictouch_account_id="";}
if (!isset($magictouch_secure)) {$magictouch_secure="http";}

if (getval("submit","")!="")
	{
	$resourcetype=getvalescaped("resourcetype","");
	
	$f=fopen("../config/config.php","w");
	fwrite($f,"<?php \$embedvideo_resourcetype='$resourcetype'; ?>");
	fclose($f);
	redirect("pages/team/team_home.php");
	}

$resource_types=get_resource_types();

include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["embed_video_configuration"] ?></h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p><?php echo $lang["specify_resourcetype"] ?></p>
   <p><label for="resourcetype"><?php echo $lang["video_resourcetype"] . ":" ?></label>
   
   <select name="resourcetype"?
   <?php foreach ($resource_types as $rt) { ?>
   <option value="<?php echo $rt["ref"] ?>" <?php if ($rt["ref"]==$embedvideo_resourcetype) {echo "selected"; } ?>><?php echo $rt["name"] ?></option>
   <?php } ?>
	</p>

<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>	