<?
include "include/db.php";
include "include/collections_functions.php";

# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("c",""),$k) && !check_access_key(getvalescaped("r",""),$k))) {include "include/authenticate.php";}

$topurl=$use_theme_as_home?"themes.php":"home.php";

$bottomurl="collections.php?k=" . $k;

if (getval("c","")!="")
	{
	# quick redirect to a collection (from e-mails, keep the URL nice and short)
	$c=getvalescaped("c","");
	$topurl="search.php?search=" . urlencode("!collection" . $c);
	$bottomurl="collections.php?collection=" . $c . "&k=" . $k;
	
	if ($k!="")
		{
		# External access user... set top URL to first resource
		$r=get_collection_resources($c);
		$topurl="view.php?ref=" . $r[0] . "&k=" . $k;		
		}
	}

if (getval("r","")!="")
	{
	# quick redirect to a resource (from e-mails)
	$r=getvalescaped("r","");
	$topurl="view.php?ref=" . $r . "&k=" . $k;
	if ($k!="") {$bottomurl="";} # No bottom frame if anon. access for single resource
	}

if (getval("url","")!="")
	{
	# New URL for top section (when the frameset is lost)
	$topurl=getval("url",$topurl);
	}
?>
<html>
<head>
<title><?=htmlspecialchars($applicationname)?></title>

<frameset rows="*<? if ($bottomurl!="") { ?>,3,128<? } ?>" id="topframe" framespacing="0" frameborder="no" border="0">
<frame name="main" id="main" src="<?=$topurl?>" frameborder=no>

<? if ($bottomurl!="") { ?>
<frame src="frame-divider.htm" name="DivideFrame" frameborder="no" scrolling="no" noresize="noresize" marginwidth="0" marginheight="0" id="DivideFrame" />
<frame name="collections" id="collections" src="<?=$bottomurl?>" frameborder=no>
<? } ?>

</frameset>


</head>
</html>