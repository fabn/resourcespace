<?php
include "../../include/db.php";
include "../../include/authenticate.php";
include "../../include/general.php";
if (!in_array("api_core",$plugins)){die("no access");}
include "../../include/header.php";
?>
<div class="BasicsBox">
<p><a  onClick="return CentralSpaceLoad(this,true);" href="<?php if (getvalescaped("back","")!=""){echo $baseurl_short.getvalescaped("back","");}else{ echo $baseurl_short."pages/change_password.php";}?>">&lt; <?php echo $lang["back"]?></a></p><h1><?php echo $lang["apiaccess"]?></h1>
</div>

<?php if (!$enable_remote_apis || $api_scramble_key=="abcdef123"){echo $lang["remoteapisnotavailable"]; exit();}?>

<?php
$apikey=make_api_key($username,$userpassword);

echo $lang["yourauthkey"];?>

<p><input type="text" size=80 value="<?php echo $apikey?>"></p>

<?php echo $lang["yourhashkey"]; $hashkey=md5($api_scramble_key.$apikey);?>

<p><input type="text" size=35 value="<?php echo $hashkey;?>"></p>

<?php if (extension_loaded('mcrypt') && extension_loaded('hash')){
echo $lang['mcryptenabled'];
} else {echo $lang['mcryptdisabled'];}
?>


<?php if ($_SERVER["SERVER_PORT"] !=443) {echo "<br>".$lang["sslrecommended"];}?>

<br/><br/><br/>
<h1><?php echo $lang["availableapis"]?></h1>
<div class="Listview">
<table border="0" cellspacing="0" cellpadding="0" class="ListviewStyle">
<!--Title row-->	
<tr class="ListviewTitleStyle">
<td width="10%"><?php echo $lang["api"]?></td>
<td width="10%"><?php echo $lang["helpfile"]?></td>
<td width="10%"><?php echo $lang["basecall"]?></td>
</tr>

<?php
// find available api plugins
foreach($plugins as $plugin){
    if (substr($plugin,0,4)=="api_" && $plugin!=="api_core") {?>
       <tr class="ListviewTitleStyle">
       <td width="10%"><?php echo $plugin?></td>
       <td width="10%"><a href="#" onClick="jQuery.ajax('<?php echo $baseurl?>/plugins/<?php echo $plugin?>/readme.txt',{complete:function(data) {jQuery('#CentralSpace').html('<a onClick=\'return CentralSpaceLoad(this,true);\' href=\'<?php echo $baseurl_short?>plugins/api_core/index.php\'>&lt; <?php echo $lang['back']?></a><pre>'+ jQuery('<span>').text(data.responseText).html() +'</pre>');}});">readme.txt</a></td>
       <td width="10%"><?php if (${$plugin}['signed']){
           echo "Signed Request: ";
           ?><a target="_blank" href="<?php echo $baseurl_short?>plugins/<?php echo $plugin?>/?key=<?php echo $apikey;?>&skey=<?php echo md5($hashkey.'key='.$apikey)?>" target="_blank"><?php echo $baseurl_short?>plugins/<?php echo $plugin?>/?key=<?php echo $apikey;?>&skey=<?php echo md5($hashkey.'key='.$apikey)?></a>
           <?php }
           else { ?>
            <a target="_blank" href="<?php echo $baseurl_short?>plugins/<?php echo $plugin?>/?key=<?php echo $apikey;?>" target="_blank"><?php echo $baseurl?>/plugins/<?php echo $plugin?>/?key=<?php echo $apikey;?></a>
           <?php } ?> 
           </td>
       </tr>
<?php
    }
}?>


</table>
</div>
<?php
include "../../include/footer.php";
?>
