<?
include "include/db.php";
include "include/authenticate.php";
include "include/general.php";
include "include/image_processing.php";
include "include/resource_functions.php";
$status="";

$maxsize="200000000";

#handle posts
if (array_key_exists("Filedata",$_FILES))
    {
    # New resource
	$ref=copy_resource(0-$userref); # Copy from user template
	
   	# Log this			
	daily_stat("Resource upload",$ref);
	resource_log($ref,"u",0);

	$status=upload_file($ref);
	exit();
    }
    
$headerinsert="
	<script type=\"text/javascript\" src=\"fancyupload/mootools-release-1.11.js\"></script>
	<script type=\"text/javascript\" src=\"fancyupload/Swiff.Base.js\"></script>
	<script type=\"text/javascript\" src=\"fancyupload/Swiff.Uploader.js\"></script>
	<script type=\"text/javascript\" src=\"fancyupload/FancyUpload.js\"></script>
	
<style>
.photoupload-queue {margin:10px 0 0 0;}
.photoupload-queue,.queue-file,.queue-size,.queue-loader,.queue-subloader {color: black;}
.photoupload-queue .input-delete
		{
			width:					16px;
			height:					16px;
			background:				url(fancyupload/delete.png) no-repeat 0 0;
			text-decoration:		none;
			border:					none;
			float:					right;
		}
.photoupload-queue
		{
			list-style:				none;
		}
.photoupload-queue li
		{
			background:				none;
			padding:				5px;
			border-bottom: 1px solid black;
		}
				.photoupload-queue .queue-file
		{
			font-weight:			bold;
		}

.photoupload-queue .queue-size
		{
			color:					#aaa;
			margin-left:			1em;
			font-size:				0.9em;
		}

.photoupload-queue .queue-loader
		{
			position:				relative;
			margin:					3px 15px;
			font-size:				0.9em;
			background-color:		#ddd;
			color:					#fff;
			border:					1px inset #ddd;
		}
.photoupload-queue .queue-subloader
		{
			text-align:				center;
			position:				absolute;
			background-color:		#81B466;
			height:					100%;
			width:					0%;
			left:					0;
			top:					0;
		}
</style>
";

include "include/header.php";
?>

<script type="text/javascript">
		//<![CDATA[

		/**
		 * Sample Data
		 */

		window.addEvent('load', function()
		{
			window.setTimeout('AddFancy()',1000);
		});
		
		function AddFancy()
		{
		
			/**
			 * We take the first input with this class we can find ...
			 */
			var input = $('userfile');

			/**
			 * Simple and easy
			 * 
			 * swf: the path to the swf
			 * container: the object is embedded in this container (default: document.body)
			 * 
			 * NOTE: container is only used for the first uploader u create, all others depend
			 * on the same swf in that container, so the container option for the other uploaders
			 * will be ignored.
			 * 
			 */
			var uplooad = new FancyUpload(input, {
				swf: 'fancyupload/Swiff.Uploader.swf',
				queueList: 'photoupload-queue',
				types: {'All files': '*.*'},	
				container: $E('h1')
			});

			/**
			 * We create the clear-queue link on-demand, since we don't know if the user has flash/javascript.
			 * 
			 * You can also create the complete xhtml structure thats needed for the queue here, to be sure
			 * that its only in the document when the user has flash enabled.
			 */
			$('photoupload-status').adopt(new Element('a', {
				href: 'javascript:void(null);',
				events: {
					click: uplooad.clearList.bind(uplooad, [false])
				}
			}).setHTML('&gt;&nbsp;Clear Completed'));

			// Display block now everything is ready.
			$('uploadbox').style.display='block';


		}

		//]]>
	</script>

<div class="BasicsBox" id="uploadbox" style="display:none;"> 
<h2>&nbsp;</h2>
<h1><?=$lang["fileupload"]?></h1>
<p><?=text("introtext")?></p>

<form id="mainform" method="post" class="form" enctype="multipart/form-data" action="<?=$baseurl?>/upload_fancy.php?user=<?=urlencode($_COOKIE["user"])?>">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$maxsize?>">

<br/>
<? if ($status!="") { ?><?=$status?><? } ?>
</td></tr>

<div class="Question">
<label for="userfile"><?=$lang["clickbrowsetolocate"]?></label>
<input type=file name=userfile id=userfile>
<div class="clearerleft"> </div>
</div>

				<fieldset>
					<legend>Upload Queue</legend>

					<div class="note" id="photoupload-status">
						Check the selected files and start uploading.
					</div>

					<ul class="photoupload-queue" id="photoupload-queue">
						<li style="display: none" />
					</ul>
				</fieldset>

<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?=$lang["fileupload"]?>&nbsp;&nbsp;" />
</div>


</form>
</div>

<?
include "include/footer.php";
?>