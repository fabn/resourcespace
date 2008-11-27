<?php

function HookRemotedownloadAllGetdownloadurl($ref,$size,$ext,$page=1,$alternative=-1)
	{
	global $remotedownload_prepend, $remotedownload_append, $remotedownload_replace, $remotedownload_addquery;
	global $storageurl;
	
	$url=get_resource_path($ref,false,$size,false,$ext,-1,$page,($size=="scr" && checkperm("w") && $alternative==-1),"",$alternative);
	
	if(!empty($remotedownload_prepend) && strpos($url, $storageurl) === 0)
		{
		$storageurl_len=strlen($storageurl);
		$url=substr($url,0,$storageurl_len) . $remotedownload_prepend . substr($url,$storageurl_len);
		}
	
	if(!empty($remotedownload_append))
		{
		$url=$url . $remotedownload_append;
		}
		
	foreach($remotedownload_replace as $replace)
		{
		$url=str_replace($replace['match'],$replace['with'],$url);
		}
	
	foreach($remotedownload_addquery as $query)
		{
		$url=$url . (strpos($url,"?")!==FALSE?"?":"&") . $query;
		}
	
	return $url;
	}

?>