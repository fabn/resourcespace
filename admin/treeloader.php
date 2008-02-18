<?
include "../include/db.php";
include "../include/general.php";
include "../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
$tree=file("data/tree.txt");

$node=$_GET["node"];
$id=$_GET["id"];

$ids=explode("-",$id);

$transfrom=array();
$transto=array();
for ($n=0;$n<count($ids);$n++)
    {
    $curid=$ids[$n];
    $s=explode(":",$curid);
    if (count($s)>1)
        {
        $transfrom[$n]="%" . $n;
        $transto[$n]=$s[1];
        $curid=$s[0];
        }
    }

#print_r($transfrom);
#print_r($transto);
#echo "'$id' cur='$curid'";
?>
<script language="Javascript">
<?
$debug="<li>curid=" . $curid;
for ($n=1;$n<count($tree);$n++)
    {
    $s=explode(";",trim($tree[$n]).";;;;;;;;;;;;;;;;;;;");
    if ($s[1]==$curid)
        {
        $debug.="<li>matched row $n";
        
        #parse SQL and do
        $query=$s[6];
        if (substr($query,0,2)=="as")
           	{
           	# "as" command means alias another line to this line, replacing certain values
           	$as=explode(" ",$s[6]);
           	$query=explode(";",trim($tree[$as[1]]));$query=$query[6];
           	for ($q=2;$q<count($as);$q+=2)
           		{
           		$query=str_replace($as[$q],$as[$q+1],$query);
           		}
           	}
        
        if ($query!="")
            {
           	# Query
           	$debug.="<li>query=" . $query;
            ?>
            //alert("query=<?=str_replace($transfrom,$transto,$query)?>");
            <?
            $result=sql_query(str_replace($transfrom,$transto,$query));
  	         for ($m=0;$m<count($result);$m++)
             	{
             	?>
             	parent.AddNode(<?=$node?>,"<?=$id."-".$n . ":" . 	$result[$m]["ref"]?>","<?=str_replace(array("\n","\r")," ",($result[$m]["name"]=="")?'(no name)':i18n_get_translated($result[$m]["name"]))?>",<?=$s[3]?>,<?=$s[4]?>,"<?=$s[5]?>");
              	<?
               	}
            }
        else
            {
            $debug.="<li>no query";
            ?>
            parent.AddNode(<?=$node?>,"<?=$id."-".$n?>","<?=(trim($s[2])=="")?"?":$s[2]?>",<?=$s[3]?>,<?=$s[4]?>,"<?=$s[5]?>");
            <?
            }
        }
    }
?>
parent.UpdateNode(<?=$node?>);
</script>
<?
#echo $debug;




?>
