<?
include "include/db.php";
include "include/collections_functions.php";

# External access support (authenticate only if no key provided, or if invalid access key provided)
$k=getvalescaped("k","");if (($k=="") || (!check_access_key_collection(getvalescaped("c",""),$k) && !check_access_key(getvalescaped("r",""),$k))) {include "include/authenticate.php";}

$topurl=$use_theme_as_home?"pages/themes.php":"pages/home.php";
$bottomurl="pages/collections.php?k=" . $k;

if (getval("c","")!="")
	{
	# quick redirect to a collection (from e-mails, keep the URL nice and short)
	$c=getvalescaped("c","");
	$topurl="pages/search.php?search=" . urlencode("!collection" . $c) . "&k=" . $k;;
	$bottomurl="pages/collections.php?collection=" . $c . "&k=" . $k;
	
	if ($k!="")
		{
		# External access user... set top URL to first resource
		$r=get_collection_resources($c);
		if (count($r)>0)
			{
			# Fetch collection data
			$cinfo=get_collection($c);if ($cinfo===false) {exit("Collection not found.");}
		
			if ($feedback_resource_select && $cinfo["request_feedback"])
				{
				$topurl="pages/collection_feedback.php?collection=" . $c . "&k=" . $k;		
				}
			else
				{
				$topurl="pages/search.php?search=" . urlencode("!collection" . $c) . "&k=" . $k;		
				}
			}
		else
			{
			$topurl="pages/home.php";
			}
		}
	}

if (getval("r","")!="")
	{
	# quick redirect to a resource (from e-mails)
	$r=getvalescaped("r","");
	$topurl="pages/view.php?ref=" . $r . "&k=" . $k;
	if ($k!="") {$bottomurl="";} # No bottom frame if anon. access for single resource
	}

if (getval("url","")!="")
	{
	# New URL for top section (when the frameset is lost)
	$topurl=getval("url",$topurl);
	}

# If not using framesets, redirect instead.
if (checkperm("b") || $frameless_collections) {redirect($topurl);}

?>
<html>
<head>
<!--
ResourceSpace version <?=$productversion?>

http://www.montala.net/resourcespace.php
Copyright Oxfam GB 2006-2008
-->
<title><?=htmlspecialchars($applicationname)?></title>

<frameset rows="*<? if ($bottomurl!="") { ?><? if ($collection_resize!=true){?>,3<?}?>,138<? } ?>" id="topframe" framespacing="0" <? if ($collection_resize!=true){?>frameborder="no"<?}?>>
<frame name="main" id="main" src="<?=$topurl?>" <? if ($collection_resize!=true){?>frameborder="no"<?}?>>

<? if ($bottomurl!="") { ?>
<? if ($collection_resize!=true){?><frame src="pages/frame-divider.htm" name="DivideFrame" frameborder="no" scrolling="no" noresize="noresize" marginwidth="0" marginheight="0" id="DivideFrame" /><?}?>
<frame name="collections" id="collections" src="<?=$bottomurl?>" frameborder=no>
<? } ?>

</frameset>


</head>
</html>