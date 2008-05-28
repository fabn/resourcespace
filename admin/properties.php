<?
include "../include/db.php";
include "../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}

$tree=file("data/tree.txt");

hook("treealter"); # Hook to allow the default tree to be altered with a plugin.

#fetch ID string
$id=$_GET["id"];
$name=$_GET["name"];
$parent=$_GET["parent"];
$ids=explode("-",$id);

$historyview=getval("historyview",-1);
$hist_filename="history/hist" . str_replace(array(":","."),array("_","_"),$id) . ".txt";

#set up transform array (for queries etc) based on ID string
$transfrom=array();
$transto=array();
$curid=0;$parentid=0;$ref=0;
for ($n=0;$n<count($ids);$n++)
    {
    $parentid=$curid;
    $curid=$ids[$n];
    $s=explode(":",$curid);
    if (count($s)>1)
        {
        $transfrom[$n]="%" . $n;
        $transto[$n]=$s[1];
        $curid=$s[0];
        $ref=$s[1];
        } else {$ref=0;}
    }

#fetch tree data for current ID
$t=explode(";",trim($tree[$curid]) . ";;;;;;;;;;;;;;");

if (substr($t[7],0,3)=="url")
	{
	header ("Location: " . str_replace($transfrom,$transto,substr($t[7],4)) . "&parent=" . $parent);
	exit();
	}
	
if (substr($t[6],0,2)=="as")
	{
	#echo "Parsing...";
    # "as" command means alias another line to this line, replacing certain values
    $as=explode(" ",$t[6]);
    $query=trim($tree[$as[1]]);
    for ($q=2;$q<count($as);$q+=2)
        {
        $query=str_replace($as[$q],$as[$q+1],$query);
        }
	$q=explode(";",$query);
	for ($u=6;$u<count($q);$u++)
		{
		$t[$u]=$q[$u];
		}
	#echo $t[6];
	}
	
	
#handle posts
if (array_key_exists("userfile",$_FILES))
    { #file uploads
    $extension=getval("extension","");
    $filename="../assets/" . str_replace(array(":","-"),array("A","B"),$id) . "." . $extension;
    #echo "<li>$filename<li>" . $_FILES['userfile']['tmp_name'];
    #if (strpos($_FILES['userfile']['tmp_name'],".$extension")===false) {exit ("Wrong file type. Expected '$extension'.");}
    $result=move_uploaded_file($_FILES['userfile']['tmp_name'], "$filename");
    if ($result==false)
        {
        if (file_exists($filename)) {unlink($filename);} else {echo "<div class=propbox>File too large.</div><br><br>";}
        }
    }
elseif (array_key_exists("submit",$_POST))
    {
    #return history view to the present
    $historyview=-1;
    
    #normal post
    $query=str_replace($transfrom,$transto,$t[8]);
    $history="datetime=" . urlencode(date("Y-m-d H:i:s"));
    $history.="&username=" . urlencode($username);
    
    # PDF text processing
    if (array_key_exists("filename",$_POST))
    	{
    	$filename=$_POST["filename"];
    	if (substr(strtolower($filename),strlen($filename)-4,4)==".pdf")
    		{
    		#include "include/pdfextract.php";
    		#$pdfcontents=pdf2string("../assets/" . $filename);
    		#echo $pdfcontents;
    		#$query=str_replace("[content_text]",$pdfcontents,$query);
    		#echo $query;
    		}
    	}
    	
    foreach($_POST as $key=>$value)
        {
        $query=str_replace("[" . $key . "]",escape_check($value),$query);
        $history.="&$key=" . urlencode($value);
        }
    #echo "<li>$parentid<li>$query";
    sql_query($query);
    
    # now add this to the history for this object
    if (file_exists($hist_filename))
        {$file=file($hist_filename);}
    else
        {$file=array();}
    if (count($file)>50) {array_shift($file);}
    array_push($file,$history . "\n");
   # $fp=fopen($hist_filename,"w");fwrite($fp,join("",$file));fclose($fp); # version control disabled for the moment
    ?>
    <script>
    top.main.left.EmptyNode(<?=$parent?>);
    top.main.left.ReloadNode(<?=$parent?>);
    </script>
    <?
    }

if (array_key_exists("history",$_POST))
    {
    #handle navigation in time
    if (getval("history","")=="<")
        {
        $historyview--;
        }
    if (getval("history","")==">")
        {
        $historyview++;
        }
    }
    
#deletes
if (array_key_exists("delete",$_POST))
    {
    #normal post
    $query=str_replace($transfrom,$transto,$t[9]);
    foreach($_POST as $key=>$value)
        {
        $query=str_replace("[" . $key . "]",escape_check($value),$query);
        }
    #echo "<li>$parentid<li>$query";
    sql_query($query);
    ?>
    <script>
    top.main.left.EmptyNode(<?=$parent?>);
    top.main.left.ReloadNode(<?=$parent?>);
    </script>
    <?
    }

include "include/header.php";
?>
<body style="margin:15px;padding:0px;background-position:0px -80px;">
<div class="proptitle"><?=(($t[2]==$name)?$name:$t[2]) . (($ref==0)?"":" <!--#" . $ref . "-->") . (($t[2]==$name)?"":" :: " . $name)?></div>
<div class="propbox">
<?
#fetch values
if (substr($t[7],0,6)=="upload")
    {
    #upload type
    $extension=substr($t[7],7);
    $filename="../assets/" . str_replace(array(":","-"),array("A","B"),$id) . ".$extension";
    if (file_exists($filename))
        {
        #show image if it's uploaded
        $size=getimagesize($filename);
        $width=$size[0];$height=$size[1];
        $scale=100;
        while ($width>350) {$width=$width/2;$height=$height/2;$scale=$scale/2;}
        ?>
        <p align=center><a href="<?=$filename?>" target="_blank"><img src="<?=$filename?>?<?=time()?>" width="<?=$width?>" height="<?=$height?>" border=0></a><br>Zoom: <?=floor($scale)?>%</p>
        <p>Leave blank and save to delete the file</p>
        <?
        }
    #echo "$filename<br>../assets/B1B3A2005B4A09B5A1B6.jpg";
    ?>
    <form enctype="multipart/form-data" method="post">
    <p>
    <input type="hidden" name="MAX_FILE_SIZE" value="200000">
    <input type="hidden" name="extension" value="<?=$extension?>">
    <label for="uploader">Upload file</label>
    <input id="uploader" name="userfile" type="file" size=55>
    </p>
    <?
    }
else
    {
    ?>
    <form method="post">
    <?
    $result=sql_query(str_replace($transfrom,$transto,$t[7]));
    if (count($result)==0) {exit("This item can't be edited.</div></body></html>");}
    $result=$result[0];
    
    #if viewing history, load history data
    if (($historyview!=-1) && file_exists($hist_filename))
        {
        $file=file($hist_filename);
        
        if ($historyview==-2) {$historyview=count($file)-2;if ($historyview<0) {$historyview=0;}}
        if ($historyview>=count($file)-1)
            {
            $historyview=-1;
            }
        else
            {
            parse_str($file[$historyview],$history);
            ?>
            <input type="hidden" name="historyview" value="<?=$historyview?>">
            <p><b style="color:red">Viewing version created by <?=$history["username"]?> on <?=$history["datetime"]?></b></p>
            <?
            }

        }
        
    foreach ($result as $key=>$value)
        {
        $type=substr($key,0,3);
        $key=str_replace($type . "_","",$key);
        
        #replace value with history value
        if ($historyview!=-1)
            {
            if (array_key_exists($key,$history)) {$value=$history[$key];}
            }
            
        $label=$key;
        if (strpos($label,"<")===false) {$label=ucfirst(str_replace("_"," ",$label));}
        else	
        	{
        	$label=str_replace($transfrom,$transto,$label);
        	$label=str_replace("[" . $key . "]",$value,$label);
			}
        ?>
        <p>
        <? if (!(is_numeric($key))) { ?><?=$label?><br><? } ?>
        <?
        switch ($type)
            {
            #-------------------------------------------------------------------------
            case "txt":
            #Normal Text
            ?>
            <input type="text" style="width:100%" id="<?=$key?>" name="<?=$key?>" value="<?=$value?>">
            <?
            break;
            #-------------------------------------------------------------------------
            case "btx":
            #Big Text
            ?>
            <textarea style="width:100%" rows="20" id="<?=$key?>" name="<?=$key?>"><?=$value?></textarea>
            <?
            break;
            #-------------------------------------------------------------------------
            case "mtx":
            #Medium Text
            ?>
            <textarea style="width:100%" rows="8" id="<?=$key?>" name="<?=$key?>"><?=$value?></textarea>
            <?
            break;
            #-------------------------------------------------------------------------
            case "upl":
            #In-line file uploader
            ?>
			<input type="text" style="width:100%;background-color:#eeeeee" id="<?=$key?>" name="<?=$key?>" value="<?=$value?>">
            <iframe width="100%" height="70" scrolling="no" src="upload.php?callback=<?=$key?>"></iframe>
            <?
            break;
            #-------------------------------------------------------------------------
            case "bit":
            #Yes or no
            ?>
            <select id="<?=$key?>" name="<?=$key?>" style="width:100%;">
            <option <?=($value==0)?" selected":""?> value="0">NO</option>
            <option <?=($value==1)?" selected":""?> value="1">YES</option>
            </select>
            <?
            break;
            #-------------------------------------------------------------------------
            case "drp":
            case "dr2":
            case "dr3":
            #Dropdown
            #find query
            $query=explode(";",$tree[$key]);$query=str_replace($transfrom,$transto,$query[6]);
            $drop=sql_query($query);reset($drop);
            if ($type!="drp") {$key=$type . "_" . $key;}
            ?>
            <select id="<?=$key?>" name="<?=$key?>" style="width:100%;"><option value="0">Please select:</option>
            <?
            foreach ($drop as $item)
				{
				?>
            	<option <?=($value==$item["ref"])?" selected":""?> value="<?=$item["ref"]?>"><?=$item["name"]?></option>
            	<?
            	}
           	?>
            </select>
            <?
            break;
            #-------------------------------------------------------------------------
            }
        ?>
        </p>
        <?
        }
    }
?>
<table width="100%" cellpadding=0 cellspacing=0 style="margin-top:5px;">
<tr><td align=left>
<? if ((substr($t[7],0,6)!="upload") && ($t[8]!=""))  { ?>
Version
<input type=submit style="width:30px;" name="history" value="&lt;" <?if (!((($historyview>0) || ($historyview==-1)) && (file_exists($hist_filename)))) {?>disabled="true"<?}?>>

<input type=submit style="width:30px;" name="history" value="&gt;" <?if ($historyview==-1) {?>disabled="true"<?}?>>
<? } ?>
</td>
<td align=right>
<?if ($t[9]!="") {?><input type="submit" name="delete" value="delete" style="width:100px;" onclick="return confirm('Are you sure?');"><?}?>
<?if (($t[8]!="") || (substr($t[7],0,6)=="upload")) {?><input type="submit" name="submit" value="<?=($historyview==-1)?"save":"revert"?>" style="width:100px;"><?}?>
</td></tr>
</table>
</form>

</div>
</body>
</html>
