<?php include "../include/db.php";
include "../include/authenticate.php"; #if (!checkperm("s")) {exit ("Permission denied.");}
include "../include/general.php";
include "../include/collections_functions.php";
include "../include/resource_functions.php";
include "../include/search_functions.php";


$ref=getvalescaped("ref","");
# Fetch collection data
if (!is_numeric($ref)) ##  multiple collections may be referenced
	{
	$refArray = explode(',',$ref);
	$collection=get_collection($refArray[0]);if ($collection===false) {exit("Collection not found.");}
	}
else {
$collection=get_collection($ref);if ($collection===false) {exit("Collection not found.");}
	}

$errors="";
if (getval("save","")!="")
	{
	# Email / share collection
	# Build a new list and insert
	$users=getvalescaped("users","");
	$message=getvalescaped("message","");
	$access=getvalescaped("access",-1);
	$expires=getvalescaped("expires","");	
	$feedback=getvalescaped("request_feedback","");	if ($feedback=="") {$feedback=false;} else {$feedback=true;}
	$errors=email_collection($ref,$collection["name"],$userfullname,$users,$message,$feedback,$access,$expires);
	if ($errors=="")
		{
		# Log this			
		daily_stat("E-mailed collection",$ref);
		redirect("pages/done.php?text=collection_email");
		}
	}

# Get min access to this collection
$minaccess=collection_min_access($ref);

if ($minaccess>=1 && !$restricted_share) # Minimum access is restricted or lower and sharing of restricted resources is not allowed. The user cannot share this collection.
	{
	?>
	<script type="text/javascript">
	alert("<?php echo $lang["restrictedsharecollection"]?>");
	history.go(-1);
	</script>
	<?php
	exit();
	}

include "../include/header.php";
?>
<div class="BasicsBox">
<h1><?php echo $lang["emailcollection"]?></h1>

<p><?php echo text("introtext")?></p>

<form name="collectionform" method=post id="collectionform">
<input type=hidden name=redirect id=redirect value=yes>
<input type=hidden name=ref value="<?php echo $ref?>">
<?php if ($email_multi_collections) { ?>
<script type="text/javascript">
   function getSelected(opt) {
      var sel = '';
	  var newref = '';
      var index = 0;
      for (var intLoop=0; intLoop < opt.length; intLoop++) { 
         if (opt[intLoop].selected) 
		 {  sel = sel + ', ' +  '<?php echo $collection_prefix?>' + opt[intLoop].value;
		 	newref = newref + ',' +  opt[intLoop].value;
		 }
      }
	  document.collectionform.ref.value = newref.substring(1, newref.length );
      return sel.substring(2, sel.length );
   }
</script>
<?php } ?>
<div class="Question">
<label><?php echo $lang["collectionname"]?></label><div class="Fixed"><?php 
	if (! $email_multi_collections) { 
		echo $collection["name"] ;
	} else { ##  this select copied from collections.php 
?>
		<select name="collection" multiple="multiple" size="5" class="SearchWidth" 
			onchange="document.getElementById('refDiv').innerHTML = getSelected(this); " >
		<?php
		$list=get_user_collections($userref);
		$found=false;
		for ($n=0;$n<count($list);$n++)
			{
			#show only active collections if a start date is set for $active_collections 
			if (strtotime($list[$n]['created']) > ((isset($active_collections))?strtotime($active_collections):1))
					{ ?>
				<option value="<?php echo $list[$n]["ref"]?>" <?php if ($ref==$list[$n]["ref"]) {?> 	selected<?php $found=true;} ?>><?php echo htmlspecialchars($list[$n]["name"])?></option>
			<?php }
			}
		if ($found==false)
			{
			# Add this one at the end, it can't be found
			$notfound=get_collection($ref);
			if ($notfound!==false)
				{
				?>
				<option selected><?php echo $notfound["name"]?></option>
				<?php
				}
			}
		?>
		</select> <?php } ?>
</div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["collectionid"]?></label><div id="refDiv" class="Fixed"><?php echo $collection_prefix . $collection["ref"]?></div>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label for="message"><?php echo $lang["message"]?></label><textarea class="stdwidth" rows=6 cols=50 name="message" id="message"></textarea>
<div class="clearerleft"> </div>
</div>

<?php if(!hook("replaceemailtousers")){?>
<div class="Question">
<label for="users"><?php echo $lang["emailtousers"]?></label><?php $userstring=getval("users","");include "../include/user_select.php"; ?>
<div class="clearerleft"> </div>
<?php if ($errors!="") { ?><div class="FormError">!! <?php echo $errors?> !!</div><?php } ?>
</div>
<?php } #end hook replaceemailtousers ?>

<?php if(!hook("replaceemailaccessselector")){?>
<div class="Question" id="question_access">
<label for="archive"><?php echo $lang["externalselectresourceaccess"]?></label>
<select class="stdwidth" name="access" id="access">
<?php
# List available access levels. The highest level must be the minimum user access level.
for ($n=$minaccess;$n<=1;$n++) { ?>
<option value="<?php echo $n?>"><?php echo $lang["access" . $n]?></option>
<?php } ?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } # end hook replaceemailaccessselector ?>

<?php if(!hook("replaceemailexpiryselector")){?>
<div class="Question">
<label><?php echo $lang["externalselectresourceexpires"]?></label>
<select name="expires" class="stdwidth">
<option value=""><?php echo $lang["never"]?></option>
<?php for ($n=1;$n<=150;$n++)
	{
	$date=time()+(60*60*24*$n);
	?><option <?php $d=date("D",$date);if (($d=="Sun") || ($d=="Sat")) { ?>style="background-color:#cccccc"<?php } ?> value="<?php echo date("Y-m-d",$date)?>"><?php echo nicedate(date("Y-m-d",$date),false,true)?></option>
	<?php
	}
?>
</select>
<div class="clearerleft"> </div>
</div>
<?php } # end hook replaceemailexpiryselector ?>

<?php if ($collection["user"]==$userref) { # Collection owner can request feedback.
?>
<?php if(!hook("replaceemailrequestfeedback")){?>
<div class="Question">
<label for="request_feedback"><?php echo $lang["requestfeedback"]?></label><input type=checkbox id="request_feedback" name="request_feedback" value="yes">
<div class="clearerleft"> </div>
</div>
<?php } # end hook replaceemailrequestfeedback ?>
<?php } ?>

<?php if(!hook("replaceemailsubmitbutton")){?>
<div class="QuestionSubmit">
<label for="buttons"> </label>			
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["emailcollection"]?>&nbsp;&nbsp;" />
</div>
<?php } # end hook replaceemailsubmitbutton ?>

</form>
</div>

<?php include "../include/footer.php";
?>
