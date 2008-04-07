<?
include "include/db.php";

$url=getval("url","index.php");

# process log in
$error="";
if (array_key_exists("username",$_POST))
    {
    $username=getvalescaped("username","");
    $password=getvalescaped("password","");
    
    if (strlen($password)==32) {exit("Invalid password.");} # Prevent MD5s being entered directly.
    
    $password_hash=md5("RS" . $username . $password);
    $session_hash=md5($password_hash . $username . $password . time());
    
    $valid=sql_query("select count(*) c from user where username='$username' and (password='$password' or password='$password_hash')");$valid=$valid[0]["c"];
      
    if ($valid>=1)
        {
   	    # Account expiry
        $expires=sql_value("select account_expires value from user where username='$username' and password='$password'","");
        if ($expires!="" && $expires!="0000-00-00 00:00:00" && strtotime($expires)<=time())
       		{
       		$valid=0;$error=$lang["accountexpired"];
       		}
       else
       		{
		 	$expires=0;
        	if (getval("remember","")!="") {$expires=time()+(3600*24*100);} # remember login for 100 days

			# Store language cookie
			setcookie("language",getval("language",""),time()+(3600*24*1000));

			# Update the user record. Set the password hash again in case a plain text password was provided.
			sql_query("update user set password='$password_hash',session='$session_hash' where username='$username' and (password='$password' or password='$password_hash')");

	        setcookie("user",$username . "|" . $session_hash,$expires);
	        
	        $accepted=sql_value("select accepted_terms value from user where username='$username' and (password='$password' or password='$password_hash')",0);
	        if (($accepted==0) && ($terms_login)) {redirect ("terms.php?url=" . urlencode("change_password.php"));} else {redirect($url);}
	        }
        }
    else
        {
        $error=$lang["loginincorrect"];
        }
    }

if ((getval("logout","")!="") && array_key_exists("user",$_COOKIE))
    {
    #fetch username and update logged in status
    $s=explode("|",$_COOKIE["user"]);
    $username=mysql_escape_string($s[0]);
    sql_query("update user set logged_in=0,session='' where username='$username'");
        
    #blank cookie
    setcookie("user","");
    
    unset($username);
    }

include "include/header.php";
?>

  <h1><?=text("welcomelogin")?></h1>
  <p><? if ($allow_account_request) { ?><a href="user_request.php">&gt; <?=$lang["nopassword"]?> </a><br/><? } ?>
  <a href="user_password.php">&gt; <?=$lang["forgottenpassword"]?></a></p>
  <? if ($error!="") { ?><div class="FormIncorrect"><?=$error?></div><? } ?>
  <form id="form1" method="post">
  <input type=hidden name=url value="<?=$url?>">
		<div class="Question">
			<label for="name"><?=$lang["username"]?> </label>
			<input type="text" name="username" id="name" class="stdwidth" />
			<div class="clearerleft"> </div>
		</div>
		
		<div class="Question">
			<label for="pass"><?=$lang["password"]?> </label>
			<input type="password" name="password" id="name" class="stdwidth" />
			<div class="clearerleft"> </div>
		</div>
	
		<div class="Question">
			<label for="pass"><?=$lang["language"]?> </label>
			<select class="stdwidth" name="language">
			<? reset ($languages); foreach ($languages as $key=>$value) { ?>
			<option value="<?=$key?>" <? if ($language==$key) { ?>selected<? } ?>><?=$value?></option>
			<? } ?>
			</select>
			<div class="clearerleft"> </div>
		</div>
	
		<div class="Question">
			<label for="remember"><?=$lang["keepmeloggedin"]?></label>
			<input valign=bottom name="remember" id="remember" type="checkbox" value="yes" checked>
			<div class="clearerleft"> </div>
		</div>
		
		<div class="QuestionSubmit">
			<label for="buttons"> </label>			
			<input name="Submit" type="submit" value="&nbsp;&nbsp;<?=$lang["login"]?>&nbsp;&nbsp;" />
		</div>
	</form>
  <p>&nbsp;</p>

<?
include "include/footer.php";
?>
