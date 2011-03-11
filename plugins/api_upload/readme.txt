Upload API

Usage:
http://url/plugins/api_upload/  [needs POST variables as set below]

Parameters:
key=[string]            auth key
userfile=[@file]        set the file path
resourcetype=[integer]  the Resource Type
archive=[integer]       archive status (default 0 active)

an example of a curl post to upload a file:
<?php
            $ch = curl_init();
            $post['key']='ZX13fHxpeDA6fTwtYiotIHQjImEzKjgtM3p_LXMgKDVjITosNSA,';
            $post['userfile'] = "@/var/www/test.png";
            $post['resourcetype'] = 1;
            $post['archive'] = 0;
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/r2000/plugins/api_upload/index.php?');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, "API Client"); 
            $responseBody = curl_exec($ch);
            $responseInfo	= curl_getinfo($ch);
            curl_close($ch);

echo $responseBody;
?>

example of an upload form:

<form method="post" enctype="multipart/form-data" action="http://localhost/r2000/plugins/api_upload/index.php" method="POST">
<input type="hidden" name="key" value="ZX13fHxpeDA6fTwtYiotIHQjImEzKjgtM3p_LXMgKDVjITosNSA," />
<input type="hidden" name="resourcetype" value=3/>
Choose a file to upload: <input name="userfile" id="userfile" type="file" /><br />
<input type="submit" value="Upload File" />
</form>
