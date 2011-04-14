<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include "../../../include/general.php";


if (defined("PHP_EOL")) {$eol=PHP_EOL;} else {$eol="\r\n";}
if (!isset($magictouch_account_id)) {$magictouch_account_id="";}
if (!isset($magictouch_secure)) {$magictouch_secure="http";}
if (!isset($magictouch_ext_exclude)){$ext_exclude=array("pdf","odt");}
if (!isset($magictouch_rt_exclude)){$rt_exclude=array(3,4,8,13,18,21,24);}
if (!isset($magictouch_view_page_sizes)){$view_sizes=array("lpr","scr");}
if (!isset($magictouch_preview_page_sizes)){$magictouch_preview_page_sizes=array("hpr","lpr");}

if (getval("submit","")!="")
	{
	$accountid=getvalescaped("accountid","");
	$secure=getvalescaped("secure","");
	$ext_exclude="\"".str_replace(",","\",\"",getvalescaped("extexclude",""))."\"";
	$view_sizes="\"".str_replace(",","\",\"",getvalescaped("viewsizes",""))."\"";
	$preview_sizes="\"".str_replace(",","\",\"",getvalescaped("previewsizes",""))."\"";
	if (isset($_POST['rtexclude'])){
		$rt_exclude=$_POST['rtexclude'];
	}
	else {
		$rt_exclude=array(3,4,8,13,18,21,24);
	}
	
	$secure=getvalescaped("secure","");
	$f=fopen("../config/config.php","w");
	fwrite($f,"<?php \$magictouch_account_id='$accountid';$eol\$magictouch_secure='$secure';$eol");
	if (count($rt_exclude)!=0){
		$rt_exclude=implode(",",$rt_exclude);
	}
	fwrite($f,"\$magictouch_rt_exclude=array(".$rt_exclude.");$eol");
	fwrite($f,"\$magictouch_ext_exclude=array(".$ext_exclude.");$eol");
	fwrite($f,"\$magictouch_view_page_sizes=array(".$view_sizes.");$eol");
	fwrite($f,"\$magictouch_preview_page_sizes=array(".$preview_sizes.");$eol");
	fclose($f);
	redirect("pages/team/team_home.php");
	}


include "../../../include/header.php";
?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
  <h1>MagicTouch Configuration</h1>
  <?php
if(!is_writable("../config/config.php")){echo("MagicTouch config.php is not writable: chmod 777 plugins/magictouch/config/config.php<br/><br/> <a href=''>>Click Here to Refresh.</a>");die();}
?>
<?php if ($magictouch_account_id==""){echo "You must set up MagicTouch. To get an account ID <a target='_new' href='http://www.magictoolbox.com/magictouch/signup/'>>Click Here</a>. <br /><br />Configure the account id below, and register your domain with your Magic Touch account.<br /><br />";}?>
  <div class="VerticalNav">
 <form id="form1" name="form1" method="post" action="">

   <p><label for="accountid">Account ID (from URL):</label><input name="accountid" type="text" value="<?php echo $magictouch_account_id; ?>" size="30" /></p>
   <p><label for="secure">Is your site HTTP or HTTPS?:</label>
   <select name="secure">
   <option <?php if ($magictouch_secure=="http") { ?>selected<?php } ?>>http</option>
   <option <?php if ($magictouch_secure=="https") { ?>selected<?php } ?>>https</option>
   </select>
   </p>
   <p><label for="extexclude">Extensions to exclude (comma separated):</label>
   <input name="extexclude" type="text" value="<?php echo implode(',',$magictouch_ext_exclude); ?>" size="30" />
   </p>
   <p><label for="rtexclude[]">Resource Types to exclude (highlight to exclude):</label> <br />
   <?php $rtypes=get_resource_types();?>
   <select name="rtexclude[]" multiple="multiple" size="7">
	<?php foreach($rtypes as $rt){?>
	<option value="<?php echo $rt['ref']?>" <?php if (in_array($rt['ref'],$magictouch_rt_exclude)){?>selected<?php } ?>><?php echo $rt['name']?></option>
	<?php } ?>
   </select>

   <p><label for="viewsizes">View page sizes (in order, which sizes to check for the magictouch larger preview):</label> <br />
   <?php $sizes=sql_query("select * from preview_size");?>
      <input name="viewsizes" type="text" value="<?php echo implode(',',$magictouch_view_page_sizes); ?>" size="30" />
   </p>
   
   <p><label for="previewsizes">Preview page sizes (in order, which sizes to check for the magictouch larger preview):</label> <br />
   <?php $sizes=sql_query("select * from preview_size");?>
      <input name="previewsizes" type="text" value="<?php echo implode(',',$magictouch_preview_page_sizes); ?>" size="30" />
   </p>
   
<input type="submit" name="submit" value="<?php echo $lang["save"]?>">   


</form>
</div>	
