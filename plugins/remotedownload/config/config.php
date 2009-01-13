<?php

/**
 * Config Information

If your files is hosted on a different server and you don't want download.php
to transfer it locally, to be able to set the correct headers, to force the
browser to download the files, you can get your storage server to set the
headers.

This plugin contains various options to support downloading files from
another server, by sending adding/modifiend the display url, so the storage
server known when to set the "special" download headers.

WARNING: All of these options require access to the config of the webserver
(eg. Apache or Ligghttpd).

The examples below is based on the following.

Resource space is hosted at (=$baseurl):
http://www.example.com

Your media is hosted at (=$storageurl):
http://media.example.com

=== Solution 1: Prepend a directory ===

Create a symlink "download" in DOCUMENT_ROOT (for media.example.com) to the
current directory:
# ln -s . download

Set $remotedownload_prepend="/download"; in your config.php

Setup the webserver to send the following extra headers,
for the folder /download:
Content-Type: application/octet-stream
Content-Disposition: attachment

=== Solution 2: Use different domainname ===

Setup you webserver at mediadownload.example.com but use the same
DOCUMENT_ROOT as media.example.com

Make the webserver send the following extra headers, for the whole domain:
Content-Type: application/octet-stream
Content-Disposition: attachment

Set the following inside you config.php :
$remotedownload_replace=array(
	array('match'=>'media.example.com','with'=>'mediadownload.example.com')
	);

=== Solution X ===

You can set it up in a lot of different other ways,
just by modifying the options:
$remotedownload_prepend
$remotedownload_append
$remotedownload_replace
$remotedownload_addquery

If that does not do it, it should be fairly simple to create a similar plugin
with you own special algorithm.

*/

if(!isset($remotedownload_prepend)) {$remotedownload_prepend="";}
if(!isset($remotedownload_append)) {$remotedownload_append="";}
if(!isset($remotedownload_replace)) {$remotedownload_replace=array();}
if(!isset($remotedownload_addquery)) {$remotedownload_addquery=array();}

?>