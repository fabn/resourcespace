<?php




function sync_flickr($search,$new_only=false,$photoset=0,$photoset_name="",$private=0)
	{
	# For the resources matching $search, synchronise with Flickr.
	
	global $flickr_api_key, $flickr_token, $flickr_caption_field, $flickr_keywords_field, $flickr_prefix_id_title, $lang;
			
	$results=do_search($search);
	
	foreach ($results as $result)
		{
		global $view_title_field;

		# Fetch some resource details.
		$title=i18n_get_translated($result["field" . $view_title_field]);
		$description=sql_value("select value from resource_data where resource_type_field=$flickr_caption_field and resource='" . $result["ref"] . "'","");
		$keywords=sql_value("select value from resource_data where resource_type_field=$flickr_keywords_field and resource='" . $result["ref"] . "'","");
		$photoid=sql_value("select flickr_photo_id value from resource where ref='" . $result["ref"] . "'","");

		# Prefix ID to title?
		if ($flickr_prefix_id_title)
			{
			$title=$result["ref"] . ") " . $title;
			}
			
		if (!$new_only || $photoid=="")
			{
			echo "<li>" . $lang["processing"] . ": " . $title . "\n";
	
			$im=get_resource_path($result["ref"],true,"scr",false,"jpg");
	
			# If replacing, add the photo ID of the photo to replace.
			if ($photoid!="")
				{echo "<li>" . str_replace("%photoid", $photoid, $lang["updating_metadata_for_existing_photoid"]);
				
				# Also resubmit title, description and keywords.
				flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.photos.setTags","auth_token"=>$flickr_token, "photo_id"=>$photoid, "tags"=>$keywords));

				flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.photos.setMeta","auth_token"=>$flickr_token, "photo_id"=>$photoid, "title"=>$title, "description"=>$description));
				
				}

			# New uploads only. Send the photo file.
			if ($photoid=="")	
				{

				$url="http://api.flickr.com/services/upload/";

				# Build paramater list for upload
				$data=array(
				"photo"=>"@" . $im,
				"api_key"=>$flickr_api_key,
				"auth_token" => $flickr_token,
				"title" => $title,
				"description" => $description,
				"tags" => $keywords
				);

				# Add the signature by signing the data...
				$data["api_sig"]=flickr_sign($data,array("photo"),true);
				
				# Use CURL to upload the photo.
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$photoid=flickr_get_response_tag(curl_exec($ch),"photoid");
				
				echo "<li>" . str_replace("%photoid", $photoid, $lang["photo-uploaded"]);

				# Update Flickr tag ID
				sql_query("update resource set flickr_photo_id='" . escape_check($photoid) . "' where ref='" . $result["ref"] . "'");
				}

			$created_new_photoset=false;
			if ($photoset==0)
				{
				# Photoset must be created.
				flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.photosets.create","auth_token"=>$flickr_token, "title"=>$photoset_name, "primary_photo_id"=>$photoid),"","POST");
				global $last_xml;
				#echo htmlspecialchars($last_xml);
				$pos_1=strpos($last_xml,"id=\"");
				$pos_2=strpos($last_xml,"\"",$pos_1+5);
				$photoset=substr($last_xml,$pos_1+4,$pos_2-$pos_1-4);
				echo "<li>" . str_replace(array("%photoset_name", "%photoset"), array($photoset_name, $photoset), $lang["created-new-photoset"]);
				$created_new_photoset=true;
				}

			# Add to photoset
			if (!$created_new_photoset) # If we've just created a photoset then this will already be present within it as the primary photo (added during the create photoset request).
				{
				flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.photosets.addPhoto","auth_token"=>$flickr_token, "photoset_id"=>$photoset, "photo_id"=>$photoid));
				echo "<li>" . str_replace(array("%photoid", "%photoset"), array($photoid, $photoset), $lang["added-photo-to-photoset"]);
				#global $last_xml;echo nl2br(htmlspecialchars($last_xml));
				}
						
			# Set permissions
			echo "<li>" . str_replace("%permission", $private==0 ? $lang["flickr_public"] : $lang["flickr_private"], $lang["setting-permissions"]);
			flickr_api("http://flickr.com/services/rest/",array("api_key"=>$flickr_api_key,"method"=>"flickr.photos.setPerms","auth_token"=>$flickr_token, "photo_id"=>$photoid, "is_public"=>($private==0?1:0),"is_friend"=>0,"is_family"=>0,"perm_comment"=>0,"perm_addmetadata"=>0),"","POST");

			
			}
		}
	echo "<li>" . $lang["done"];
	}
	


function flickr_api($url,$params,$response_tag="",$method="GET")
	{
	# Automatically sign the request, process it, and return it

	# Build query and sign it
	$url.="?" . flickr_sign($params);
	
	# Run query
	
	$opts = array(
	  'http'=>array(
	    'method'=>$method
	  )
	);

	$context = stream_context_create($opts);

	
	$xml=file_get_contents($url,false,$context);
	global $last_xml;$last_xml=$xml;
	
	if ($response_tag=="")
		{
		return true;
		}
	else
		{
		return flickr_get_response_tag($xml,$response_tag);
		}
	}



function flickr_sign($params,$ignore=array(),$output_sig=false)
	{
	global $flickr_api_secret;
	
	ksort($params);
	$string=$flickr_api_secret; 
	foreach ($params as $param=>$value) {if (!in_array($param,$ignore)) {$string.=$param . $value;}}
	if ($output_sig)
		{
		return md5($string);
		}
	else
		{
		return http_build_query($params) . "&api_sig=" . md5($string);
		}
	}



function do_post_request($url, $data, $optional_headers = null)
{
  global $lang;
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
  if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
  }
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    throw new Exception(str_replace(array("%url", "%php_errormsg"), array($url, $php_errormsg), $lang["problem-with-url"]));
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    throw new Exception(str_replace(array("%url", "%php_errormsg"), array($url, $php_errormsg), $lang["problem-reading-data"]));
  }
  return $response;
}

function flickr_get_response_tag($xml,$response_tag)
	{
	$start=strpos($xml,"<" . $response_tag . ">");
	$end=strpos($xml,"</" . $response_tag . ">");	
	
	if ($start===false) {echo "<pre>" . htmlspecialchars($xml) . "</pre>";return false;}
	
	return trim(substr($xml,$start+strlen($response_tag) + 2,$end-$start-strlen($response_tag)-2));
	}


?>