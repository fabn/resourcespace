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
	fwrite($f,"<?php \$embeddocument_resourcetype='$resourcetype'; ?>");
	fclose($f);
	redirect("pages/team/team_home.php");
	}

$resource_types=get_resource_types();

include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>Remote Document Viewer Configuration</h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p>Please specify which resource type the 'embed' functionality should appear for.</p>
   <p><label for="resourcetype">Remote Document Viewer Resource Type:</label>
   
   <select name="resourcetype"?
   <?php foreach ($resource_types as $rt) { ?>
   <option value="<?php echo $rt["ref"] ?>" <?php if ($rt["ref"]==$embeddocument_resourcetype) {echo "selected"; } ?>><?php echo $rt["name"] ?></option>
   <?php } ?>
	</p>

<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>	