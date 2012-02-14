<?php
include "../../include/db.php";
include "../../include/general.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}
$tree=file("data/tree.txt");
hook("treealtercopy");

## one main plugin can replace the System Setup tree completely, with a file in the plugin called: admin/data/tree.txt.
for ($n=0;$n<count($plugins);$n++)
    {
    $alt_tree_path=dirname(__FILE__)."/../../plugins/" . $plugins[$n] . "/admin/data/";
    if (file_exists($alt_tree_path . "tree.txt")) {$tree=file($alt_tree_path . "tree.txt");}
    }

if ($web_config_edit){
	$tree[]="999  ;;config.default.php;false;true;txt;;url fileedit.php?file=../../include/config.default.php";
	$tree[]="999  ;;config.php;false;true;txt;;url fileedit.php?file=../../include/config.php";
}

$node=$_GET["node"];
$id=$_GET["id"];

$ids=explode("-",$id);

$transfrom=array();
$transto=array();
$lastref=0;$recmin=0;
for ($n=0;$n<count($ids);$n++)
    {
    $curid=$ids[$n];
    $s=explode(":",$curid);
    if (count($s)>1)
        {
        $transfrom[$n]="%" . $n;
        $transto[$n]=$s[1];
        $curid=$s[0];
        $recmin=$lastref;$lastref=$s[1];
        }
    }

#print_r($transfrom);
#print_r($transto);
#echo "'$id' cur='$curid'";

echo "<script type=\"text/javascript\">";

if (getval("reloadnode","")!="")
	{
	?>
	EmptyNode(<?php echo $node?>);
	<?php
	}
	
$debug="<li>curid=" . $curid;
for ($n=1;$n<count($tree);$n++)
    {
    $recurse=false;
    if (strpos($tree[$n],"%recurse")!==false) {$recurse=true;}
    $treeline=str_replace("%recurse",$lastref,$tree[$n]);
    $treeline=str_replace("%recmin",$recmin,$treeline);
    $s=explode(";",trim($treeline).";;;;;;;;;;;;;;;;;;;");

    
    if (($n==$curid))
        {   
        $debug.="<li>matched row $n";
        # Reordering query
        if (getval("reorder","")!="")
        	{
        	$reorder=getvalescaped("reorder","");
        	$debug.="reorder={" . $reorder . "}";
        	$query=$s[10];
        	
        	$b=explode(",",$reorder);
        	$nd=0;
        	$count=0;
        	for ($n=0;$n<count($b);$n++)
        		{
        		$id=explode("-",$b[$n]);
        		$alias=$id[0];$id=$id[count($id)-1];
        		$id=explode(":",$id);$im=$id[count($id)-1];
        		
        		# If an alias value is set, this refers to the original position on the tree (before ID aliasing) which is the place we need to grab the reorder query from.
        		if (substr($alias,0,5)=="alias") {$a=substr($alias,5);echo "(alias $a)";$a=explode(".",$a);$im=$a[1];$a=$a[0];$query=explode(";",$tree[$a]);$query=$query[10];}

        		if ($nd==0) {$nd=$id[0];}
        		#$debug.= "<li>id[0]=" . $id[0] . ", curid=" . $curid;
        		
        		# Not sure why this needed a condition, have set to always execute for now.
        		if (true)
        		#if ($id[0]==$nd)
        			{
        			$count++;
        			$sql=str_replace(array("%id","%order"),array($im,($count*10)),$query);
	        		sql_query($sql);
	        		$debug.="<li>Ordered node $nd ID $im sql was {" . $sql . "}";
	        		}
        		}
        	#echo "</script>" . $debug;
        	exit();
        	}
		}
		
    if 
    (((($s[1]==$curid) || (($n==$curid) && $recurse)) && ((getval("reorder","")==""))))
        {
        $debug.="<li>matched parent $n";
        
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
        
        if (($query!="") && (strpos($s[6],"%search")===false))
            {
           	# Query
           	$debug.="<li>query=" . $query;
           	if ($s[10]!="") {$reorder=1;} else {$reorder=0;}
           	
           	if (substr($query,0,8)=="filelist")
           		{
           		# File listing
           		$accesspath=substr($query,9);

				$dirs=array();           		
           		$dh=opendir($accesspath);
				while (($file = readdir($dh)) !== false)
					{
					$dirs[]=$file;
				    }
	
				sort($dirs);
				for ($m=0;$m<count($dirs);$m++)
					{
					$file=$dirs[$m];
				    if (substr($file,0,1)!=".")
				    	{
   						$icon=$s[5];
   						$newid=$id."-". $n . ":" . $file;
				    	?>
						AddNode(<?php echo $node?>,"<?php echo $newid?>","<?php echo str_replace(array("\n","\r")," ",($file=="")?'(no name)':$file)?>",<?php echo $s[3]?>,<?php echo $s[4]?>,"<?php echo $icon?>",<?php echo $reorder?>);
				    	<?php
				    	}
					}
				
           		}
           	else
           		{
           		# Database query
				?>
				//alert("query=<?php echo str_replace($transfrom,$transto,$query)?>");
				<?php
				$result=sql_query(str_replace($transfrom,$transto,$query));
				 for ($m=0;$m<count($result);$m++)
					{
					$icon=$s[5];
					if (array_key_exists("icon",$result[$m])) {$icon=$result[$m]["icon"];}
					$newid=$id."-".$n . ":" . $result[$m]["ref"];
					
					$replaceid=true;
					$folder=$s[3];
					$name=$result[$m]["name"];
					
					# Special case for related items.
					# Globals are prefixed.
					if (array_key_exists("section",$result[$m]) && $result[$m]["section"]!=0 && $n==36) 		
						{
						#$replaceid=false;
						#$folder="false";
						$section=sql_value("select name value from section where ref='" . $result[$m]["section"] . "'","");
						$name="Global " . str_replace(" : "," : " . $section . "/",$name) . " (" . $result[$m]["article2"] . ")";
						}
						
					if ($replaceid && array_key_exists("id",$result[$m])) {$newid=$result[$m]["id"];}
					
					if (!in_array($result[$m]["name"],$user_restrict))
						{
						?>
						AddNode(<?php echo $node?>,"<?php echo $newid?>","<?php echo lang_or_i18n_get_translated(str_replace(array("\n","\r", "\"")," ",($name=="")?$lang["treenode-no_name"]:$name),array("usergroup-", "resourcetype-", "report-", "imagesize-", "fieldtitle-"))?>",<?php echo $folder?>,<?php echo $s[4]?>,"<?php echo $icon?>",<?php echo $reorder?>);
						<?php
						}
					}
				}
            }
       elseif (($query!="") && (strpos($s[6],"%search")!==false))
			{
			?>
            AddNode(<?php echo $node?>,"<?php echo $id."-".$n?>","Search:",<?php echo $s[3]?>,<?php echo $s[4]?>,"<?php echo $s[5]?>");			
			<?php
			if (getval("submit","")!="")
				{
				$search=getvalescaped("search","");
				$debug.="<li>search=" . $search;
				?>
				TreeSearch[<?php echo $node?>]="<?php echo $search?>";
				<?php
	            $result=sql_query(str_replace("%search",$search,str_replace($transfrom,$transto,$s[6])));
    	        for ($m=0;$m<count($result);$m++)
        	        {
        	        $icon=$s[5];
             		if (array_key_exists("icon",$result[$m])) {$icon=$result[$m]["icon"];}
             		$newid=$id."-".$n . ":" . $result[$m]["ref"];
            		if (array_key_exists("id",$result[$m])) {$newid=$result[$m]["id"];}
            	    ?>
                	AddNode(<?php echo $node?>,"<?php echo $newid?>","<?php echo lang_or_i18n_get_translated(str_replace(array("\n","\r","\"")," ",((trim($result[$m]["name"])=="")?"???":$result[$m]["name"])),"usergroup-") ?>",<?php echo $s[3]?>,<?php echo $s[4]?>,"<?php echo $icon?>",0);
                	<?php
                	}
				}
			}
        else
            {
            $debug.="<li>no query";
            
            // empty, no security.php
            $user_restrict=array();
            
            
			if (!in_array(trim($s[2]),$user_restrict))
				{
				$nodename = lang_or_i18n_get_translated($s[2], "treenode-");
				?>            AddNode(<?php echo $node?>,"<?php echo $id."-".$n?>","<?php echo (trim($s[2])=="")?"?":$nodename?>",<?php echo $s[3]?>,<?php echo $s[4]?>,"<?php echo $s[5]?>",0);
				<?php
				}
            }
        }
    }
?>
UpdateNode(<?php echo $node?>);
</script>
<?php
$debug.="<li>time=" . time();
#echo $debug;


?>
