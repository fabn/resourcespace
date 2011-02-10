<?php

function HookRss2AllSearchbartoptoolbar()
{
 	global $baseurl,$userpassword,$username,$api_scramble_key;
 	$skey = md5($api_scramble_key.make_api_key($username,$userpassword)."!last50"); 
?>
<div class="SearchSpace" style="font:9px arial,sans-serif;">
<img src="<?php echo $baseurl?>/plugins/rss2/static/rss.gif" style="vertical-align:middle;" alt="" />&nbsp;&nbsp;<a target="_TOP" href="<?php echo $baseurl?>/plugins/rss2/pages/rssfilter.php?key=<?php echo make_api_key($username,$userpassword);?>&search=!last50&skey=<?php echo urlencode($skey); ?>">&gt;&nbsp;New Content RSS feed</a>
</div>
<?php
}

