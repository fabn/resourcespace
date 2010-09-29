<?php
include "../../../include/db.php";
include "../../../include/general.php";
include "../../../include/authenticate.php";
include "../../../include/search_functions.php";
include "../inc/flickr_functions.php";

$frameless_collections=true;
include "../../../include/header.php";

$theme=getvalescaped("theme","");

?>
<h1><?php echo $lang["flickr_title"] ?></h1>
<?php

# Handle log out
if (getval("logout","")!="")
	{
	sql_query("update user set flickr_token='',flickr_frob='' where ref='$userref'");
	}

# Does this user have a Flickr token set? If so let's try and use it.
$validtoken=false;
$last_xml="";
$flickr_token=sql_value("select flickr_token value from user where ref='$userref'","");
if ($flickr_token!="")
	{
	# Check the token
	$flickr_token=flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.auth.checkToken","auth_token"=>$flickr_token),"token");	
	if ($flickr_token!==false)
		{
		$validtoken=true;
		$start=strpos($last_xml,"fullname=");
		$end=strpos($last_xml,"\"",$start+10);
		$fullname=substr($last_xml,$start+10,$end-$start-10);
		?>
		<p><?php echo $lang["flickrloggedinas"] . " <strong>" . htmlspecialchars($fullname) . "</strong>" ?> (<a href="sync.php?theme=<?php echo $theme ?>&logout=true"><?php echo $lang["logout"] ?></a>)</p>
		<?php
		}
	}


if (!$validtoken)
	{
	# We must first authenticate this user.

	# Existing frob?
	$flickr_frob=sql_value("select flickr_frob value from user where ref='$userref'","");
	$valid_frob=false;
	if ($flickr_frob!="")
		{
		#echo "check existing frob $flickr_frob<br>";
		$flickr_token=flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.auth.getToken","frob"=>$flickr_frob),"token");	
		if ($flickr_token!==false)	
			{
			$valid_frob=true;
			$validtoken=true;
			sql_query("update user set flickr_token='" . escape_check($flickr_token) . "' where ref='$userref'");
			}
		}
	
	if (!$valid_frob)
		{
		$flickr_frob=flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.auth.getFrob"),"frob");			

		sql_query("update user set flickr_frob='" . escape_check($flickr_frob) . "' where ref='$userref'");

	
		# Authenticate frob
		$auth_url="http://flickr.com/services/auth/?" . flickr_sign(array("api_key"=>$flickr_api_key,"perms"=>"write", "frob"=>$flickr_frob));
		?>
		<p>&gt;&nbsp;<a target=_blank href="<?php echo $auth_url; ?>"><?php echo $lang["flickrnotloggedin"] ?></a></p>
		<p><?php echo $lang["flickronceloggedinreload"] ?></p>
		<form method="post" action="sync.php?theme=<?php echo $theme ?>"><input type="submit" name="reload" value="<?php echo $lang["reload"] ?>"></form>
		<?php
		}
	}


if ($validtoken)
	{
	# Valid token... we have a valid token for this user so we're ready to publish.

	if (getval("publish_all","")!="" || getval("publish_new","")!="")
		{
		#$photoset=0;

		# Make sure a photoset exists for this theme
		flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.photosets.getList","auth_token"=>$flickr_token));
		
		#echo nl2br(htmlspecialchars($last_xml));
		
		# List all photosets.
		$p = xml_parser_create();
		xml_parse_into_struct($p, $last_xml, $vals, $index);
		xml_parser_free($p);
		#echo "<pre>Index array\n";
		#print_r($index);
		#echo "\nVals array\n";
		#print_r($vals);

		$last_photoset_id="";
		$photosets=array();
		for ($n=0;$n<count($vals);$n++)
			{
			if (isset($vals[$n]["tag"]) && $vals[$n]["tag"]=="PHOTOSET" && isset($vals[$n]["attributes"]["ID"]))
				{
				# Read the photoset ID and set, ready for nested title tag later
				$last_photoset_id=$vals[$n]["attributes"]["ID"];
				}
			if (isset($vals[$n]["tag"]) && $vals[$n]["tag"]=="TITLE")
				{
				# Read the title and set
				$photosets[$vals[$n]["value"]]=$last_photoset_id;
				}
			}

		# $photosets now contains a list of all the user's photosets.
		# Look for the name of the current collection.
		$photoset_name=sql_value("select name value from collection where ref='$theme'","");
		if (array_key_exists($photoset_name,$photosets))
			{
			# Name already exists. Just use this photoset ID.
			$photoset=$photosets[$photoset_name];
			}
		else
			{
			# Name does not exist. Set to zero so it is created during sync.
			$photoset=0;
			}		
		}
		
		
	if (getval("publish_all","")!="")
		{
		# Perform sync publishing all (updating any existing)
		sync_flickr("!collection" . $theme,false,$photoset,$photoset_name,getvalescaped("private",""));
		}
	elseif (getval("publish_new","")!="")
		{
		# Perform sync publishing new only.
		sync_flickr("!collection" . $theme,true,$photoset,$photoset_name,getvalescaped("private",""));
		}
	else
		{
		# Display option for sync
		$unpublished=sql_value("select count(*) value from resource join collection_resource on resource.ref=collection_resource.resource where collection_resource.collection='" . $theme . "' and flickr_photo_id is null",		0);
		
		# Count for all resources in selection
		$all=sql_value("select count(*) value from resource join collection_resource on resource.ref=collection_resource.resource where collection_resource.collection='" . $theme . "'",0);

		
		?>

		

		<form method="post">

		<!-- Public/private? -->
		<p><?php echo $lang["flickr_publish_as"] ?>
		<select name="private">
		<option value="0"><?php echo $lang["flickr_public"] ?></option>
		<option value="1" <?php if (getval("private","")==1) { ?>selected<?php } ?>><?php echo $lang["flickr_private"] ?></option>
		</select>
		</p>
		

		<p><?php echo $lang["publish_new_help"] ?></p>		
		<input <?php if ($unpublished==0) { ?>disabled<?php } ?> type="submit" name="publish_new" value="<?php echo str_replace("?",$unpublished,$lang["publish_new"]); ?>">



		<p>&nbsp;</p>
		<?php
		if ($all-$unpublished>0)
			{
			?>
		<p><?php echo $lang["publish_all_help"] ?></p>
		<input <?php if ($unpublished==0 && $all==0) { ?>disabled<?php } ?> type="submit" name="publish_all" value="<?php echo str_replace(array("$","?"),array($unpublished,$all-$unpublished),$lang["publish_all"]); ?>">
			<?php
			}
		?>
		</form>
		<?php
		}
	}












include "../../../include/footer.php";

?>