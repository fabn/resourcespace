<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

if (getval("submit","")!="")
	{
	$resourcetype=getvalescaped("resourcetype","");
	$videosplice_parent_field_set=getvalescaped("videosplice_parent_field","");
	
	$f=fopen("../config/config.php","w");
	fwrite($f,"<?php \$videosplice_resourcetype='$resourcetype'; \$videosplice_parent_field=$videosplice_parent_field_set; ?>");
	fclose($f);
	redirect("pages/team/team_home.php");
	}

include "../../../include/header.php";

$fields=sql_query("select ref,title from resource_type_field order by resource_type,order_by");
$resource_types=get_resource_types();
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["videospliceconfiguration"]?></h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p><?php echo $lang["specify_resource_type"]?></p>
   <p><label for="resourcetype"><?php echo $lang["video_resource_type"]?>:</label>
   
   <select name="resourcetype">
   <?php foreach ($resource_types as $rt) { ?>
   <option value="<?php echo $rt["ref"] ?>" <?php if ($rt["ref"]==$videosplice_resourcetype) {echo "selected"; } ?>><?php echo $rt["name"] ?></option>
   <?php } ?>
   </select>
	</p>

<p><?php echo $lang["specify_parent_field"]?></p>
   <p><label for="videosplice_parent_field"><?php echo $lang["parent_resource_field"]?>:</label>
   
   <select name="videosplice_parent_field">
   <?php foreach ($fields as $field) { ?>
   <option value="<?php echo $field["ref"] ?>" <?php if ($field["ref"]==$videosplice_parent_field) {echo "selected"; } ?>><?php echo lang_or_i18n_get_translated($field["title"],"fieldtitle-") ?></option>
   <?php } ?>
   </select>
	</p>

<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>