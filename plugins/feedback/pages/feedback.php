<?php
include "../../../include/db.php";


if (array_key_exists("user",$_COOKIE))
   	{
	# Check to see if this user is logged in.
    $s=explode("|",$_COOKIE["user"]);
    $username=mysql_escape_string($s[0]);
    $session_hash=mysql_escape_string($s[1]);

    $loggedin=sql_value("select count(*) value from user where session='$session_hash' and approved=1 and timestampdiff(second,last_active,now())<(30*60)",0);
    if ($loggedin>0)
        {
        # User is logged in. Proceed to full authentication.
		include "../../../include/authenticate.php";
		}
	}

if (!isset($userref))
	{
	# User is not logged in. Fetch username from posted form value.
	$username=getval("username","");
	$usergroupname="(Not logged in)";
	$userfullname="";
	$anonymous_login=$username;
	$frameless_collections=true;
	$pagename="terms";
	$plugins=array();
	}
	
include "../../../include/general.php";

$error="";
$errorfields=array();
$sent=false;

if (getval("send","")!="")
	{
	$csvline="\"" . date("Y-m-d") . "\"";
	for ($n=1;$n<=count($feedback_questions);$n++)
		{
		$type=$feedback_questions[$n]["type"];	
		
		if ($type!=4) # Do not run for labels
			{
			$value=getval("question_" . $n,"");
			
			# Check required fields
			if ($type==3 && trim($value)=="") {$error=$lang["requiredfields"];$errorfields[]=$n;}
				
			if ($type==5)
				{
				# Multi select: contruct value from options
				$s=explode(",",$feedback_questions[$n]["options"]);
				$value="";
				for ($m=0;$m<count($s);$m++)
					{
					if (getval("question_" .$n . "_" . $m,"")!="") # Option is selected, add to value
						{
						if ($value!="") {$value.=",";}
						$value.=$s[$m];
						}
					}
				}
	
			# Append to CSV line
			if ($csvline!="") {$csvline.=",";}
			$csvline.="\"" . str_replace("\"","'",$value) . "\"";
			}
		}
	
	# Append user name and group to CSV file
	$csvline="\"$username\",\"$usergroupname\"," . $csvline;
	if ($error=="")
		{
		# Write results.
		$sent=true;
		$f=fopen("../data/results.csv","a");
		fwrite($f,$csvline . "\n");
		fclose($f);
		}

	}

include "../../../include/header.php";
?>
<style>
h2 {font-size:18px;}
<?php if (!isset($userref)) { ?>
#SearchBox {display:none;}
<?php } ?>
</style>

<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>User Survey</h1>

<?php if ($sent) { ?><p>Thank you for your feedback.</p><?php 
} else { ?>

<form method=post>
<?php if ($error) { ?><div class="FormError">!! <?php echo $error ?> !!</div><br /><?php } ?>
<?php 
	
for ($n=1;$n<=count($feedback_questions);$n++)
	{
	$type=$feedback_questions[$n]["type"];
	$text=$feedback_questions[$n]["text"];	
	
	if ($type==4)
		{
		# Label type - just output the HTML.
		echo $feedback_questions[$n]["text"];
		}
	else
		{
		if (in_array($n,$errorfields)) {$text="<strong style='color:red;'>" . $text . "</strong>";}	# Highlight fields with errors.
		?>
		<div class="Question" style="border-top:none;">
		<label style="width:250px;padding-right:5px;" for="question_<?php echo $n?>"><?php echo $text;?></label>
		
		<?php if ($type==1) {  # Normal text box
		?>
		<input type=text name="question_<?php echo $n?>" id="question_<?php echo $n?>" class="stdwidth" value="<?php echo htmlspecialchars(getvalescaped("question_" . $n,""))?>">
		<?php } ?>

		<?php if ($type==2) { # Large text box 
		?>
		<textarea name="question_<?php echo $n?>" id="question_<?php echo $n?>" class="stdwidth" rows="5"><?php echo htmlspecialchars(getvalescaped("question_" . $n,""))?></textarea>
		<?php } ?>

		<?php if ($type==3) { # Single Select List
				?>
		<div class="Fixed">
		<?php foreach (explode(",",$feedback_questions[$n]["options"]) as $option)
			{
			?>
			<input type="radio" name="question_<?php echo $n?>" value="<?php echo htmlspecialchars($option);?>" <?php if ($option==getvalescaped("question_" . $n,"")) { ?>checked<?php } ?>><?php echo htmlspecialchars($option);?><br />
			<?php
			}
		?>
		</div>
		<?php } ?>
		
		<?php if ($type==5) { # Multi Select List
		?>
		<div class="Fixed">
		<?php $opt=0;foreach (explode(",",$feedback_questions[$n]["options"]) as $option)
			{
			?>
			<input type="checkbox" name="question_<?php echo $n?>_<?php echo $opt?>" value="yes" <?php if (getvalescaped("question_" . $n . "_" . $opt,"")!="") { ?>checked<?php } ?>><?php echo htmlspecialchars($option);?><br />
			<?php
			$opt++;
			}
		?>
		</div>
		<?php } ?>

		
		<div class="clearerleft"> </div>
		</div>
		<?php
		}
	}

if (!isset($userref))
	{
	# User is not logged in. Ask them for their user name
	?>
	<br><br>
		<div class="Question" style="border-top:none;">
		<label style="width:250px;padding-right:5px;" for="username">Your Full Name</label>
		
		<input type=text name="username" id="username" class="stdwidth" value="<?php echo htmlspecialchars(getvalescaped("username",""))?>">
		<div class="clearerleft"> </div>
		</div>
	<?php
	}
?>

<div class="QuestionSubmit">
<?php if ($error) { ?><div class="FormError">!! <?php echo $error ?> !!</div><br /><?php } ?>
<label style="width:250px;" for="buttons"> </label>			
<input name="send" type="submit" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Send&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />
</div>
</form>
<?php } ?>

</div>
<?php include "../../../include/footer.php"; ?>
