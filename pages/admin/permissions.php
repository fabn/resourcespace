<?php
include "../../include/db.php";
include "../../include/general.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}

$ref=getvalescaped("ref","");

if (getval("saveform","")!="")
	{
	$perms=array();
	foreach ($_POST as $key=>$value)
		{
		if (substr($key,0,11)=="permission_")
			{
			# Found a permisison.
			$reverse=($value=="reverse");
			$key=substr($key,11);

			if ((!$reverse && getval("checked_" . $key,"")!="") || ($reverse && !getval("checked_" . $key,"")!=""))
				{
				$perms[]=urldecode($key);
				}
			}
		}	
	if (getval("other","")!="") {$perms[]=getvalescaped("other","");}
	sql_query("update usergroup set permissions='" . join(",",$perms) . "' where ref='$ref'");
	echo "<font color=white>" . join(",",$perms) . "</font>";
	}


function DrawOption($permission,$description,$reverse=false,$reload=false)
	{
	global $permissions,$permissions_done;
	$checked=(in_array($permission,$permissions));
	if ($reverse) {$checked=!$checked;}
	?>
	<input type="hidden" name="permission_<?php echo urlencode($permission)?>" value="<?php echo ($reverse)?"reverse":"normal" ?>">
	<tr>
	<td nowrap width="3%"><?php if ($reverse) {?><i><?php } ?><?php echo $permission?><?php if ($reverse) {?></i><?php } ?></td>
	<td><?php echo $description?></td>
	<td width="20%"><input type="checkbox" name="checked_<?php echo urlencode($permission) ?>" <?php if ($checked) { ?> checked <?php } ?><?php if ($reload) { ?> onChange="document.forms['permform'].submit();" <?php } ?>></td>
	</tr>
	<?php

	$permissions_done[]=$permission;
	}



# Load group data / permissions
$group=get_usergroup($ref);
$permissions=trim_array(explode(",",$group["permissions"]));
$permissions_done=array();

include "include/header.php";
?>
<style>
.permissionstable {border-collapse: collapse;}
.permissionstable td {border:1px solid #999;padding:4px;}
.permheader, .permheader td {background-color:#ddd;font-weight:bold;}
</style>
<body style="background-position:0px -85px;margin:0;padding:10px;">
<div class="proptitle">Permissions Manager: <?php echo $group["name"] ?></div>

<div class="propbox" id="propbox">
<p><a href="<?php echo $baseurl?>/pages/admin/properties.php?id=-1-4:<?php echo $ref?>&parent=8&gparent=1&name=<?php echo urlencode($group["name"])?>">&lt; Back to Group Management</a></p>

<form method="post" id="permform">
<input type="hidden" name="saveform" value="true">
<table width="100%" class="permissionstable">

<tr><td colspan=3 class="permheader">Searching / Access</td></tr>

<?php
DrawOption ("s","Search capability");
DrawOption ("v","Can download restricted resources and view confidential resources (normally admin only)",false);
DrawOption ("g","Restrict access to all available resources",true);
DrawOption ("q","Can make resource requests",true);
DrawOption ("w","Show watermarked previews/thumbnails");

?><tr><td colspan=3 class="permheader">Metadata Fields</td></tr><?php

# ------------ View access to fields
DrawOption ("f*","Can see all fields?",false,true);
$fields=sql_query("select * from resource_type_field order by order_by");
foreach ($fields as $field)
	{
	if (!in_array("f*",$permissions))
		{
		DrawOption ("f" . $field["ref"],"&nbsp;&nbsp; - Can see field '" . i18n_get_translated($field["title"]) . "'");
		}
	else
		{
		# Add it to the 'done' list so it is discarded.
		$permissions_done[]="f" . $field["ref"];
		}
	}

DrawOption ("F*","Can edit all fields? (for editable resources)",true,true);
$fields=sql_query("select * from resource_type_field order by order_by");
foreach ($fields as $field)
	{
	if (in_array("F*",$permissions))	
		{
		DrawOption ("F-" . $field["ref"],"&nbsp;&nbsp; - Can edit field '" . i18n_get_translated($field["title"]) . "'",true);
		}
	else
		{
		# Add it to the 'done' list so it is discarded.
		$permissions_done[]="F-" . $field["ref"];
		}
	}



?><tr><td colspan=3 class="permheader">Resource Types</td></tr><?php

# ------------ View access to resource types
$rtypes=sql_query("select * from resource_type order by name");
foreach ($rtypes as $rtype)
	{
	DrawOption ("T" . $rtype["ref"],"Can see resource type '" . i18n_get_translated($rtype["name"]) . "'",true);
	}


# ------------ Restricted access to resource types
$rtypes=sql_query("select * from resource_type order by name");
foreach ($rtypes as $rtype)
	{
	DrawOption ("X" . $rtype["ref"],"Restricted access only to resource type '" . i18n_get_translated($rtype["name"]) . "'",false);
	}



?><tr><td colspan=3 class="permheader">Resource Creation / Management</td></tr><?php

# ------------ Edit access to workflow states
for ($n=-2;$n<=3;$n++)
	{
	DrawOption ("e" . $n,"Edit access to workflow state '" . $lang["status" . $n] . "'",false);
	}

DrawOption ("c","Can create resources / upload files (admin users; resources go to 'Live' state)");
DrawOption ("d","Can create resources / upload files (normal users; resources go to 'Pending Submission' state via My Contributions)");

DrawOption ("D","Can delete resources (to which the user has write access)",true);

DrawOption ("i","Can manage archive resources");
DrawOption ("n","Can tag resources using 'Speed Tagging' (if enabled in the configuration)");


?><tr><td colspan=3 class="permheader">Themes / Collections</td></tr><?php

DrawOption ("b","Enable bottom collection bar ('Lightbox')",true);
DrawOption ("h","Can publish collections as themes");

# ------------ Access to theme categories
DrawOption ("j*","Can see all theme categories",false,true);
$themes=sql_array("select distinct theme value from collection where length(theme)>0 order by theme");
foreach ($themes as $theme)
	{
	if (!in_array("j*",$permissions))
		{
		DrawOption ("j" . $theme,"&nbsp;&nbsp; - Can see theme category '" . i18n_get_translated($theme) . "'",false);
		}
	else
		{
		# Add it to the 'done' list so it is discarded.
		$permissions_done[]="j" . $theme;
		}
	}
	
DrawOption ("J","When searching, display only resources that exist within themes to which the user has access");



?><tr><td colspan=3 class="permheader">Administration</td></tr><?php

DrawOption ("t","Can access the Team Centre area",false,true);
if (in_array("t",$permissions))
	{
	# Team Centre options	
	DrawOption ("r","Can manage research requests");
	DrawOption ("R","Can manage resource requests");
	DrawOption ("o","Can manage content (intro/help text)");
	DrawOption ("m","Can bulk-mail users");
	DrawOption ("u","Can manage users");
	DrawOption ("k","Can manage keywords");
	DrawOption ("a","Can access the System Setup area");
	}
else
	{
	$permissions_done[]="r";
	$permissions_done[]="R";
	$permissions_done[]="o";
	$permissions_done[]="m";
	$permissions_done[]="u";
	$permissions_done[]="k";
	$permissions_done[]="a";	
	}


?><tr><td colspan=3 class="permheader">Other</td></tr><?php

DrawOption ("p","Can change own account password",true);
DrawOption ("U","Can manage users in children groups to the user's group only");
DrawOption ("E","Can email resources to users in the user's own group, children groups and parent group only.");

?>
</table>

<p>Custom Permissions:</p>
<?php $not_handled=array_diff($permissions,$permissions_done); ?>
<textarea name="other" style="width:100%;"><?php echo join(",",$not_handled) ?></textarea>

<p align="right"><input type="submit" name="save" value="&nbsp;&nbsp;&nbsp;Save&nbsp;&nbsp;&nbsp;"></p>
</form>

</div>
</div>
</body>