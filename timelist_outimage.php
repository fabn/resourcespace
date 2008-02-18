<?
$link = mysql_connect('localhost', 'root', '');
mysql_select_db('oasis');
if (!$link) {
die('Could not connect: ' . mysql_error());
}


$ref=addslashes($_GET["ref"]);
$timecode=addslashes($_GET["timecode"]);

$sql = "SELECT thumbnail FROM timelist_thumb where resource='$ref' and timecode_in='$timecode'";
$result = mysql_query($sql);
if (!$result) {
die('Could not query:' . mysql_error());
}
$image = mysql_result($result, 0); 
header('Content-Type: image/jpeg');
echo $image;#base64_decode($image);
?>