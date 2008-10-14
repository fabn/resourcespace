<?
include "../include/db.php";
include "../include/authenticate.php";
include "../include/general.php";
$type=getvalescaped("type","Keyword usage");

function tag_cloud($year=-1,$type="Keyword usage")
	{
	$q="";if ($year!=-1) {$q="and daily_stat.year='$year'";}
	$tags=sql_query("select sum(count) c,keyword from daily_stat left join keyword on object_ref=keyword.ref where activity_type='$type' $q group by object_ref order by c desc limit 150;");
	$t=array();
	for ($n=0;$n<count($tags);$n++)
		{
		$keyword=$tags[$n]["keyword"];
		if (!is_numeric(substr($keyword,0,1))) {$t[$keyword]=$tags[$n]["c"];}
		}
	ksort($t);return ($t);
	}

include "../include/header.php";
?>


<div class="BasicsBox"> 
  <h1>Tag Cloud</h1><p>What have people been searching for? The more a keyword is used the larger it appears.</p>
  <!--<p><a href="tag_cloud.php?type=Keyword+usage">&gt;&nbsp;Keywords searched for</a>
  &nbsp;&nbsp;
  <a href="tag_cloud.php?type=Keyword+added+to+resource">&gt;&nbsp;Keywords added to a resource</a>
  </p>-->
</div>

<div class="RecordBox">
<div class="RecordPanel">  
<div class="RecordResouce">

<?
$tags=tag_cloud(-1,$type);
$max=max($tags);$min=min($tags);$range=$max-$min;if ($range==0) {$range=1;}
foreach($tags as $tag=>$count)
	{
	$fs=10+floor((($count-$min)/$range)*35)
	?><span style="font-size:<?=$fs?>px;padding:1px;"><a href="search.php?search=<?=urlencode($tag)?>&resetrestypes=1"><?=str_replace(" ","&nbsp;",$tag)?></a></span> <?
	}
?>
</div>
</div>
<div class="PanelShadow"></div>
</div>

<?
include "../include/footer.php";
?>