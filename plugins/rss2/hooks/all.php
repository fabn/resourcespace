<?php

function HookRss2AllSearchbarbeforebottomlinks()
{
 	global $baseurl,$lang,$userpassword,$username,$api_scramble_key;
 	$skey = md5($api_scramble_key.make_api_key($username,$userpassword)."!last50"); 
?>
<p><a target="_TOP" href="<?php echo $baseurl?>/plugins/rss2/pages/rssfilter.php?key=<?php echo make_api_key($username,$userpassword);?>&search=!last50&skey=<?php echo urlencode($skey); ?>">&gt;&nbsp;<?php echo $lang["new_content_rss_feed"]; ?><!--<img src="<?php echo $baseurl?>/plugins/rss2/static/rss.gif" style="vertical-align:middle;" alt="" />&nbsp;&nbsp;--></a></p>
<?php
}

