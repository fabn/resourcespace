Alternative File API

Usage:
http://url/plugins/api_alt_file/?key=[authkey]&[optional parameters]

Parameters:
resource=[int]           get list of alternative files for a resource
alt_ref=[int]            get a specific alternative file (requires resource)
previewsize=[string]     return a jpg 'preview' url (ex: "thm") for the alt file
content=[string]         Return results as json or xml (default json without json headers)

sample call:
http://localhost/r2000/plugins/api_alt_file/?resource=142&alt_file=9&content=json&previewsize=col&key=ZX13...

sample output:
<?xml version="1.0" encoding="UTF-8"?>
<results>
    <resource>
        <ref>9</ref>
        <name>test</name>
        <description>This is a test file</description>
        <file_name>firefox_wallpaper.png</file_name>
        <file_extension>png</file_extension>
        <file_size>12656355</file_size>
        <creation_date>2011-02-16 09:22:55</creation_date>
        <alt_type></alt_type>
        <file_path>/var/www/r2000/include/../filestore/1/4/2_f8d5a09345a9bf6/142_alt_9_21c53884a43f1f1.png</file_path>
        <preview>/var/www/r2000/include/../filestore/1/4/2_f8d5a09345a9bf6/142col_alt_9_abc9e0cf69c2610.jpg</preview>
    </resource>
</results>


If a signature is required, you must md5([yourhashkey].[querystring]) and submit it as a final parameter called skey.
Your hash key is a shared secret available from plugins/api_core.
The query string you hash this with must not include a leading '?', and must not include an skey parameter.

The simplest example of a signed call is:
url/plugins/api_alt_file/?key=aBCdEf...&skey=<?php echo md5("yourhashkey"."key=aBCdEf...")?>
