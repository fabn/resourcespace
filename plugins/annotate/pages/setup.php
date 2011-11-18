<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

if (getval("submit","")!="")
	{
	$annotate_ext_exclude=explode(",",getvalescaped("extexclude",""));
	if (isset($_POST['rtexclude'])){
		$annotate_rt_exclude=$_POST['rtexclude'];
	}
	else {
		$annotate_rt_exclude=array();
	}

	$config=array();
	$config['annotate_rt_exclude']=$annotate_rt_exclude;
	$config['annotate_ext_exclude']=$annotate_ext_exclude;

	set_plugin_config("annotate",$config);
	
	redirect("pages/team/team_home.php");
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>Annotate Configuration</h1>

<form id="form1" name="form1" method="post" action="">

<?php echo config_text_field("extexclude","Extensions to exclude <br />(comma separated)",implode(',',$annotate_ext_exclude));?>   
<?php $rtypes=get_resource_types();
echo config_custom_select_multi("rtexclude","Resource Types to exclude <br />(highlight to exclude)",$rtypes,$annotate_rt_exclude);?>

<div class="Question">  
<label for="submit"></label> 
<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   
</div><div class="clearerleft"></div>

</form>
</div>	
<?php include "../../../include/footer.php";
