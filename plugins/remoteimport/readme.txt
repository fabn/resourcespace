
=========================================
	
	RemoteImport plugin

	Dan Huby, Montala, 2011-07-13
	
==========================================


This plugin allows ResourceSpace metadata and files to be remotely created and updated.

It uses the following XML format:

<resourceset>
	<resource type="1">

		<keyfield ref="8">556677</keyfield>
		
		<field ref="18">My description of the picture</field>
		<field ref="3">Uganda</field>

		<collection>name of collection</collection> 

		<filename>/dir/dir/filename_of_image.jpg</filename>
	
	</resource>

	<resource type="1">

		<keyfield ref="8">556688</keyfield>
		
		<field ref="18">Example caption</field>
		<field ref="3">Mexico</field>

		<collection>name of collection</collection> 

		<filename>/somewhere/New Bitmap Image.bmp</filename>
		
	</resource>
</resourceset>


The "type" attribute of the resource tag specifies the resource type ID. This will be updated for existing resources.

The "Keyfield" tag specifies the key field used to find the resource and is the key field on the remote system being synchronised with (e.g. Record ID).

"Field" is the field to be updated, the "ref" attribute referring to the field numeric ID in System Setup.

"Filename" is optional and specifies a local file on the system to replace the existing resource file or add a file for new records.

There's no need to activate the plugin in Team Centre because it's stand-along (it doesn't hook in to any existing functionality).

Two paramaters most be POSTed to the script:

 - xml: the XML itself
 - sign: an MD5 hash of the installation's scramble key (from your config.php) concatenated with the above XML

The "sign" part ensures that this can only be used by someone that knows your system's secret scramble key, which is randomised for each installation as part of the automatic setup.

A note about fields. If you are updating a dropdown, checkbox, category tree or dyanamic keywords field (e.g. fixed metadata fields) then you must prefix the field value with a comma in the XML. This notifies the indexing built in to RS that it must index the entire field value as well as the individual keywords.
