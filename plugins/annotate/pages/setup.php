<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";

if (getval("submit","")!="")
	{
	$annotate_ext_exclude=explode(",",getvalescaped("extexclude",""));
	$annotate_debug=getvalescaped("debug","");
	if (isset($_POST['rtexclude'])){
		$annotate_rt_exclude=$_POST['rtexclude'];
	}
	else {
		$annotate_rt_exclude=array();
	}
	$annotate_public_view=getvalescaped("annotate_public_view","");
	$annotate_show_author=getvalescaped("annotate_show_author","");	
	$annotate_font=getvalescaped("annotate_font","");
	
	$config=array();
	$config['annotate_rt_exclude']=$annotate_rt_exclude;
	$config['annotate_ext_exclude']=$annotate_ext_exclude;
	$config['annotate_debug']=$annotate_debug;
	$config['annotate_public_view']=$annotate_public_view;
	$config['annotate_show_author']=$annotate_show_author;
	$config['annotate_font']=$annotate_font;
	set_plugin_config("annotate",$config);
	
	redirect("pages/team/team_home.php");
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1><?php echo $lang["annotate_configuration"];?></h1>

<form id="form1" name="form1" method="post" action="">

<?php echo config_text_field("extexclude",$lang["extensions_to_exclude"],implode(',',$annotate_ext_exclude));?>   
<?php $rtypes=get_resource_types();
echo config_custom_select_multi("rtexclude",$lang["resource_types_to_exclude"],$rtypes,$annotate_rt_exclude);?>
<?php echo config_custom_select("annotate_font",$lang["annotate_font"],array("helvetica","dejavusanscondensed"),$annotate_font);?>
<?php echo config_boolean_field("debug",$lang["annotatedebug"],$annotate_debug);?>
<?php echo config_boolean_field("annotate_public_view",$lang["annotate_public_view"],$annotate_public_view);?>
<?php echo config_boolean_field("annotate_show_author",$lang["annotate_show_author"],$annotate_show_author);?>

<div class="Question">  
<label for="submit"></label> 
<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   
</div><div class="clearerleft"></div>

</form>
</div>	
<?php include "../../../include/footer.php";
