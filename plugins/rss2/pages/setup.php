<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

if (getval("submit","")!="")
	{
	$rss_ttl = getvalescaped("rss_ttl","");
	$rss_limits = getvalescaped("rss_limits","");
	$rss_fields=explode(",",getvalescaped("rss_fields",""));
	
	$config=array();
	
	$config['rss_ttl']=$rss_ttl;
	$config['rss_limits']=$rss_limits;
	$config['rss_fields']=$rss_fields;	
	set_plugin_config("rss2",$config);
	
	redirect("pages/team/team_home.php");
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>RSS2 Configuration</h1>

<form id="form1" name="form1" method="post" action="">

<?php echo config_boolean_field("rss_limits","rss_limits",$rss_limits);?>
<?php echo config_text_field("rss_fields","rss_fields",implode(',',$rss_fields));?>
<?php echo config_text_field("rss_ttl","rss_ttl",$rss_ttl);?>

<div class="Question">  
<label for="submit"></label> 
<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   
</div><div class="clearerleft"></div>

</form>
</div>	
<?php include "../../../include/footer.php";
