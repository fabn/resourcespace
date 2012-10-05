Upload API

Usage:
http://url/plugins/api_upload/  [needs POST variables as set below]

Parameters:
key=[string]            auth key
userfile=[@file]        set the file path
resourcetype=[integer]  the Resource Type
archive=[integer]       archive status (default 0 active)

For example, a watched "upload" folder could use a mac hotfolder with an custom upload script.

On Linux an incrontab establishes "hot folders" similarly. For example, 
To set up an rsupload hotfolder, make an incrontab:

/home/tom/Desktop/rsupload IN_MOVED_TO /home/tom/scripts/rsupload $@/$# 

The content of /home/tom/scripts/rsupload would be:
 #!/bin/bash
 curl --form userfile=@$1 "https://server/resourcespace/plugins/api_upload/?key=[yourkey]";
 mv $1 /home/tom/Desktop/Uploaded; #moves the uploaded file into the 'Uploaded' folder
