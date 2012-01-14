<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("n")) {exit("Permission denied");}
include "../../../include/general.php";
include "../../../include/resource_functions.php";
include "../../../include/collections_functions.php";
include "../include/airotek_functions.php";

$project= get_project(getval("project",""));
$field=$project['field'];
$field_options=get_field_options($field);


if (getval("save","")!="")
{
	$ref=getvalescaped("ref","",true);
	if (isset($_POST['fieldselect'])){
		$fieldselect=$_POST['fieldselect'];
	}
	else {$fieldselect=array();
	}

	$keywords=implode(",",$fieldselect);
	$oldval=get_data_by_field($ref,$field);
	update_field($ref,$field,$keywords);
	
	# Write this edit to the log.
	resource_log($ref,'e',$field,"",$oldval,$keywords);
	if (!checkperm("b")){refresh_collection_frame();}
}

$project=getval("project","");
$project=get_project($project);

# Fetch a resource
$ref=sql_value("select r.ref value,count(*) c from resource r left outer join resource_keyword rk on r.ref=rk.resource and rk.resource_type_field='$field' where r.has_image=1 and archive=0 group by r.ref  order by rand() limit 1",0);
if ($ref==0) {exit ("No resources to tag.");}

# Load resource data
$resource=get_resource_data($ref);

# Load existing keywords
#$existing=sql_array("select distinct k.keyword value from resource_keyword rk join keyword k on rk.keyword=k.ref where rk.resource='$ref' and length(k.keyword)>1 and k.keyword not like '%0%' and k.keyword not like '%1%' and k.keyword not like '%2%' and k.keyword not like '%3%' and k.keyword not like '%4%' and k.keyword not like '%5%' and k.keyword not like '%6%' and k.keyword not like '%7%' and k.keyword not like '%8%' and k.keyword not like '%9%' and k.keyword not like '% %' order by k.keyword");
$existing=array();

$words=sql_value("select value from resource_data where resource='$ref' and resource_type_field='$field'","");
$words=explode(",",$words);

/*
# Fetch very rough 'completion status' to give some measure of progress
$complete=sql_value("select count(*) value from resource_data where resource_type_field='$speedtaggingfield' and length(value)>0",0);
$total=sql_value("select count(*) value from resource where has_image=1 and archive=0",0);
$percent=min(100,ceil($complete/max(1,$total)*100));
*/
$percent=0;


include "../../../include/header.php";
?>
<style>#CentralSpaceContainer {margin:0px 25px 0px;padding:0 0px 0 0;text-align:left;} #CentralSpace {margin-left: 0px; padding: 0; }</style>

<div class="BasicsBox"> 
<form method="post" id="mainform">
<input type="hidden" name="ref" value="<?php echo $ref?>">


<?php 
$imagepath=get_resource_path($ref,false,"scr",false,$resource["preview_extension"]);
global $magictouch_preview_page_sizes;
foreach ($magictouch_preview_page_sizes as $mtpreviewsize){
    $largeurl=get_resource_path($ref,false,$mtpreviewsize,false,"jpg",-1,1);
    $largeurl_path=get_resource_path($ref,true,$mtpreviewsize,false,"jpg",-1,1);

    if (file_exists($largeurl_path)){break;}

}
?>
<div class="RecordBox" style="margin-right:-15px;">
<div class="RecordPanel">
<a href="<?php echo $largeurl?>" class="Picture MagicTouch">
<img src="<?php echo $imagepath?>" alt=""  />
</a>
	
<h1><?php $field=get_fields(array($project['field'])); echo "Tag ".$field[0]['title'];?></h1>
<p><?php echo $lang['desc-'.$project['field']]?></p>
<p>Select the images below which describe this image, then click Save and Continue.</p>
<div style="float:left;">
<?php

foreach ($field_options as $option)
		{?>
		<div style="display:block; float:left; margin-right:10px;"><input type="checkbox" style="display:none;" name="fieldselect[]" id="<?php echo $option?>" value="<?php echo $option?>" <?php if (in_array($option,$words)){?>checked='checked' <?php } ?>/><table style="display:inline;"><tr><td><img <?php if (in_array($option,$words)){?>class='checkedimage'<?php } else {?>class='uncheckedimage'<?php } ?> id="image<?php echo $option?>" style="height:200px;width:200px;" onclick="if ($('<?php echo $option?>').checked==true){$('<?php echo $option?>').checked=false;$('image<?php echo $option?>').setAttribute('class', 'uncheckedimage');} else {$('<?php echo $option?>').checked=true;$('image<?php echo $option?>').setAttribute('class', 'checkedimage');}" src="<?php echo $baseurl.'/plugins/airotek/gfx/field_options/'.strtolower($option).'.jpg'?>"/></tr><tr><td><?php echo $option?></td></tr></table></div>
		<?php
		}
	?>
<div style="clear:left;">
<div class="QuestionSubmit">
<input name="save" type="submit" default value="&nbsp;&nbsp;Save and Continue&nbsp;&nbsp;" />
</div>
<br /><br />
<h1>Stats</h1>
<p>Gamification here</p>
<p>Due date <?php echo nicedate($project['due'])?></p>
<table>
<?php /*
$lb=sql_query("select u.fullname,count(*) c from user u join resource_log rl on rl.user=u.ref where rl.resource_type_field='$speedtaggingfield' group by u.ref order by c desc limit 5;");
for ($n=0;$n<count($lb);$n++)
	{
	?>
	<tr><td><?php echo $lb[$n]["fullname"]?></td><td><?php echo $lb[$n]["c"]?></td></tr>
	<?php
	}
	*/ 
?>
</table></p></div>

</div>

<div class="clearerleft"> </div>
</div></div>

<!--<p>Thanks for helping. The speed tagging project is <?php echo $percent?>% complete.</p>-->


</form>
</div>
<?php
include "../../../include/footer.php";
?>
<script src="<?php echo $magictouch_secure ?>://www.magictoolbox.com/mt/<?php echo $magictouch_account_id ?>/magictouch.js" type="text/javascript" defer="defer"></script>
