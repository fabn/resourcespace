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
  <h1>Video Splice Configuration</h1>

  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

<p>Please specify which resource type the cut and splice functionality should appear for.</p>
   <p><label for="resourcetype">Video Resource Type:</label>
   
   <select name="resourcetype">
   <?php foreach ($resource_types as $rt) { ?>
   <option value="<?php echo $rt["ref"] ?>" <?php if ($rt["ref"]==$videosplice_resourcetype) {echo "selected"; } ?>><?php echo $rt["name"] ?></option>
   <?php } ?>
   </select>
	</p>

<p>Please specify which field should be used for the parent resource information when splicing/merging.</p>
   <p><label for="videosplice_parent_field">Parent Resource Information Field:</label>
   
   <select name="videosplice_parent_field">
   <?php foreach ($fields as $field) { ?>
   <option value="<?php echo $field["ref"] ?>" <?php if ($field["ref"]==$videosplice_parent_field) {echo "selected"; } ?>><?php echo $field["title"] ?></option>
   <?php } ?>
   </select>
	</p>

<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>