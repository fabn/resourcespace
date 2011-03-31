<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";


if (!isset($magictouch_account_id)) {$magictouch_account_id="";}
if (!isset($magictouch_secure)) {$magictouch_secure="http";}

if (getval("submit","")!="")
	{
	$accountid=getvalescaped("accountid","");
	$secure=getvalescaped("secure","");
	
	$f=fopen("../config/config.php","w");
	fwrite($f,"<?php \$magictouch_account_id='$accountid';\$magictouch_secure='$secure'; ?>");
	fclose($f);
	redirect("pages/team/team_home.php");
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>MagicTouch Configuration</h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

   <p><label for="accountid">Account ID (from URL):</label><input name="accountid" type="text" value="<?php echo $magictouch_account_id; ?>" size="30" /></p>
   <p><label for="secure">Is your site HTTP or HTTPS?:</label>
   <select name="secure">
   <option <?php if ($magictouch_secure=="http") { ?>selected<?php } ?>>http</option>
   <option <?php if ($magictouch_secure=="https") { ?>selected<?php } ?>>https</option>
   </select>
   </p>

<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>	