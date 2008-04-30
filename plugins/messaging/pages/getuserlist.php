<?

include(dirname(__FILE__)."/../../../include/db.php");
include(dirname(__FILE__)."/../../../include/authenticate.php");
include(dirname(__FILE__)."/../../../include/general.php");

?>
<html>
<head>
<link href="<?=$baseurl?>/css/wrdsnpics.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
<link href="<?=$baseurl?>/css/Col-<?=(isset($userfixedtheme) && $userfixedtheme!="")?$userfixedtheme:getval("colourcss","greyblu")?>.css" rel="stylesheet" type="text/css" media="screen,projection,print" id="colourcss" />
<!--[if lte IE 6]> <link href="<?=$baseurl?>/css/wrdsnpicsIE.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->
<!--[if lte IE 5.6]> <link href="<?=$baseurl?>/css/wrdsnpicsIE5.css" rel="stylesheet" type="text/css"  media="screen,projection,print" /> <![endif]-->

</head>
<body>
<script language="javascript">
function setParentText(str) {
	window.opener.document.getElementById('to').value = str + '';
	window.close();
}
</script>
<table>

<?
$users=get_users();
for ($n=0;$n<count($users);$n++)
	{
	$show=true;
	if (checkperm("E") && ($users[$n]["groupref"]!=$usergroup) && ($users[$n]["groupparent"]!=$usergroup) && ($users[$n]["groupref"]!=$usergroupparent)) {$show=false;}
	if ($show)
		{
?>
<tr><td style="border-bottom: 1px solid #ffffff; padding-top: 5px"><a href="#" onclick="javascript:setParentText('<?=$users[$n]["username"]?>');"><?=$users[$n]["username"] . " - " . $users[$n]["fullname"]?></a></td></tr>
<?
		}
	}

?>
</table>
</body>
</html>