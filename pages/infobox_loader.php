<?
include "../include/db.php";
# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key(getvalescaped("ref",""),$k))) {include "../include/authenticate.php";}

include "../include/general.php";
include "../include/search_functions.php";
include "../include/resource_functions.php";

$ref=getvalescaped("ref","");

# Load resource data
$resource=get_resource_data($ref);

# Load access level
$access=$resource["access"];
if (checkperm("v"))
	{
	$access=0; # Permission to access all resources
	}
else
	{
	if ($k!="")
		{
		#if ($access==3) {$access=2;} # Can't support custom group permissions for non-users
		if ($access==3) {$access=0;}
		}
	elseif ($access==3)
		{
		# Load custom access level
		$access=get_custom_access($ref,$usergroup);
		}
	}

# check permissions (error message is not pretty but they shouldn't ever arrive at this page unless entering a URL manually)
if ($access==2) 
		{
		exit("This is a confidential resource.");
		}
?>
<? if (!hook("infoboxreplace")) { ?>

<h2><?=htmlspecialchars(i18n_get_translated($resource["title"]))?></h2>

<?
# Display fields
for ($n=0;$n<count($infobox_fields);$n++)
	{
	$field=$infobox_fields[$n];
	if ((checkperm("f" . $field) || checkperm("f*"))
	&& !checkperm("f-" . $field))
		{
		$value=trim(get_data_by_field($ref,$field));
		if ($value!="")
			{
			$value=nl2br(htmlspecialchars(TidyList(i18n_get_translated($value))));
			?>
			<p><?=$value?></p>
			<?	
			}
		}
	}
?>

<? } ?>