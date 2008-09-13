<?
include "include/db.php";
include "include/authenticate.php"; if (!checkperm("n")) {exit("Permission denied");}
include "include/general.php";
include "include/resource_functions.php";

if (getval("save","")!="")
	{
	$ref=getvalescaped("ref","");
	$keywords=getvalescaped("keywords","");

	update_field($ref,$speedtaggingfield,$keywords);
	
	# Write this edit to the log.
	resource_log($ref,'e',$speedtaggingfield);
	}

# Fetch a resource
$ref=sql_value("select r.ref value,count(*) c from resource r left outer join resource_keyword rk on r.ref=rk.resource and rk.resource_type_field='$speedtaggingfield' where r.has_image=1 and archive=0 group by r.ref  order by c,rand() limit 1",0);
if ($ref==0) {exit ("No resources to tag.");}

# Load resource data
$resource=get_resource_data($ref);

# Load existing keywords
#$existing=sql_array("select distinct k.keyword value from resource_keyword rk join keyword k on rk.keyword=k.ref where rk.resource='$ref' and length(k.keyword)>1 and k.keyword not like '%0%' and k.keyword not like '%1%' and k.keyword not like '%2%' and k.keyword not like '%3%' and k.keyword not like '%4%' and k.keyword not like '%5%' and k.keyword not like '%6%' and k.keyword not like '%7%' and k.keyword not like '%8%' and k.keyword not like '%9%' and k.keyword not like '% %' order by k.keyword");
$existing=array();

$words=sql_value("select value from resource_data where resource='$ref' and resource_type_field='$speedtaggingfield'","");

/*
# Fetch very rough 'completion status' to give some measure of progress
$complete=sql_value("select count(*) value from resource_data where resource_type_field='$speedtaggingfield' and length(value)>0",0);
$total=sql_value("select count(*) value from resource where has_image=1 and archive=0",0);
$percent=min(100,ceil($complete/max(1,$total)*100));
*/
$percent=0;


include "include/header.php";
?>
<div class="BasicsBox"> 

<form method="post" id="mainform">
<input type="hidden" name="ref" value="<?=$ref?>">

<h1><?=$lang["speedtagging"]?></h1>
<p><?=text("introtext")?></p>

<? 
$imagepath=get_resource_path($ref,"pre",false,$resource["preview_extension"]);
?>
<div class="RecordBox"><div class="RecordPanel"><img src="<?=$imagepath?>?nc=<?=time()?>" alt="" class="Picture" />


<!--<div class="Question">
<label for="keywords"><?=$lang["existingkeywords"]?></label>
<div class="Fixed"><?=join(", ",$existing)?></div>
</div>-->

<div class="clearerleft"> </div>

<div class="Question">
<label for="keywords"><?=$lang["extrakeywords"]?></label>
<input type="text" class="stdwidth" rows=6 cols=50 name="keywords" id="keywords" value="<?=htmlspecialchars($words)?>">
</div>

<script type="text/javascript">
document.getElementById('keywords').focus();
</script>

<div class="QuestionSubmit">
<label for="buttons"> </label>
<input name="save" type="submit" default value="&nbsp;&nbsp;<?=$lang["next"]?>&nbsp;&nbsp;" />
</div>

<div class="clearerleft"> </div>
</div></div>

<!--<p>Thanks for helping. The speed tagging project is <?=$percent?>% complete.</p>-->

<p><?=$lang["leaderboard"]?><table>
<?
$lb=sql_query("select u.fullname,count(*) c from user u join resource_log rl on rl.user=u.ref where rl.resource_type_field='$speedtaggingfield' group by u.ref order by c desc limit 5;");
for ($n=0;$n<count($lb);$n++)
	{
	?>
	<tr><td><?=$lb[$n]["fullname"]?></td><td><?=$lb[$n]["c"]?></td></tr>
	<?
	}
?>
</table></p>

</form>
</div>

<?
include "include/footer.php";
?>
