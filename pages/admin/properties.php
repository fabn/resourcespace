<?php
include "../../include/db.php";
include "../../include/general.php";
include "../../include/authenticate.php";if (!checkperm("a")) {exit ("Permission denied.");}

# Work out the path to the top of the DOM (for updating the left frame)
if (!$frameless_collections)
	{
	$top_dom="top.main";
	}
else
	{
	$top_dom="top";
	}

$tree=file("data/tree.txt");

hook("treealter");

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

#fetch ID string
$id=$_GET["id"];
$name=$_GET["name"];
$parent=$_GET["parent"];
$ids=explode("-",$id);

$historyview=getval("historyview",-1);
$hist_filename="history/hist" . str_replace(array(":","."),array("_","_"),$id) . ".txt";
$saved=false;

#set up transform array (for queries etc) based on ID string
$transfrom=array();
$transto=array();
$curid=0;$parentid=0;$ref=0;$lastref=0;$recmin=0;
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
        $recmin=$lastref;$lastref=$s[1];
        } else {$ref=0;}
    }

#fetch tree data for current ID
$treeline=$tree[$curid];
$t=explode(";",trim($treeline));
if (substr($t[7],0,2)!="as")
    {
    $treeline=str_replace("%recurse",$lastref,$treeline);#echo $treeline;
    $treeline=str_replace("%recmin",$recmin,$treeline);#echo $treeline;
    }
$t=explode(";",trim($treeline));
    
if (substr($t[7],0,2)=="as")
    {
    #echo "Parsing...";
    # "as" command means alias another line to this line, replacing certain values
    $as=explode(" ",$t[7]);
    $query=trim($tree[$as[1]]);
    for ($q=2;$q<count($as);$q+=2)
        {
    #echo "replace " . $as[$q] . " with " . $as[$q+1] . "<br><br>";
        $query=str_replace($as[$q],$as[$q+1],$query);
    #echo $query . "<br><br>";
        }
    $q=explode(";",$query);
    for ($u=6;$u<count($q);$u++)
        {
        $t[$u]=$q[$u];
        }
    #echo $t[6];
    }
    
# A special case for global relationships.
# If clicked, these should display the original (unalised) 'delete relationship' option
# instead of the aliased article properties.
if (substr($id,0,5)=="alias") {
    $a=substr($id,5);
    $a=explode(".",$a);        $im=$a[1];    $a=$a[0];
    if ($a==36)
        {
        # Is this a global panel?
        $panel=explode("-",$id);$panel=$panel[count($panel)-1];
        $line=explode(":",$panel);
        $panel=@$line[1];
        $line=$line[0];

        if ($line==15)
            {
            $section=sql_value("select section value from article where ref='$panel'","");
            if ($section!=0)
                {
                $query=explode(";",$tree[$a]);$query=$query[7];
                $t=explode(";",trim($tree[$a]));
                $relation=explode("-",$im);$relation=$relation[0];
                $transfrom[]="%4";
                $transto[]=$relation;
                }
            }
        }
    } 


if (substr($t[7],0,3)=="url")
    {
    header ("Location: " . str_replace($transfrom,$transto,substr($t[7],4)) . "&parent=" . $parent . "&gparent=" . getval("gparent",""));
    exit();
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
        if (file_exists($filename)) {unlink($filename);} else {echo "<div class=propbox>" . $lang["file_too_large"] . "." . "</div><br><br>";}
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
        $value=str_replace("&AMP;","&",$value);
        
        # Fix quote issue. English language only as this breaks extended UTF-8 characters (such as Chinese).
        if ($language=="en" || $language=="en-US")
        	{
	        $value=fixSmartQuotes($value);
	        }
        
        $query=str_replace("[" . $key . "]",escape_check($value),$query);
        $history.="&$key=" . urlencode($value);
        }
    #echo "<li>$parentid<li>$query";
	
	# Handle null values.
	$query=str_replace("''","null",$query);
	
    sql_query($query);
    
    if (array_key_exists("newredirect",$_POST))
        {
           header ("Location: properties.php?id=" . getval("newredirect","") . sql_insert_id() . "&name=Enter+New+Data&parent=" . $parent);
           exit();
        }
    
    # now add this to the history for this object
    /*
    if (file_exists($hist_filename))
        {$file=file($hist_filename);}
    else
        {$file=array();}
    if (count($file)>50) {array_shift($file);}
    array_push($file,$history . "\n");
    $fp=fopen($hist_filename,"w");fwrite($fp,join("",$file));fclose($fp);
    */
    $saved=true;
    ?>
    <script type="text/javascript">
    <?php echo $top_dom ?>.left.EmptyNode(<?php echo $parent?>);
    <?php echo $top_dom ?>.left.ReloadNode(<?php echo $parent?>);
    </script>
    <?php
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
#exit($query);
    $q=explode(" then ",$query);
    for ($n=0;$n<count($q);$n++)
        {
        sql_query($q[$n]);
        }
    ?>
    <script type="text/javascript">
    <?php echo $top_dom ?>.left.EmptyNode(<?php echo $parent?>);
    <?php echo $top_dom ?>.left.ReloadNode(<?php echo $parent?>);
    </script>
    <?php
    }
	?>
<script type="text/javascript">

function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

document.onkeypress = stopRKey;

</script>

<?php 
include "include/header.php";
?>
<body style="background-position:0px -85px;margin:0;padding:10px;padding-top:0px;padding-right:0px;">
<div class="proptitle"><?php echo (($t[2]==$name)?$name:lang_or_i18n_get_translated($t[2],array("treenode-","treeobjecttype-"))) . (($ref==0)?"":" #" . $ref) . (($t[2]==$name)?"":" :: " . $name)?></div>

<div class="propbox" id="propbox">

<?php if ($saved) { ?>
<table width=100% style="border:1px solid black;">
<tr><td width=40><img src="gfx/icons/apply.gif" width=32 height=32></td><td valign=middle align=left><?php echo $lang["field_updated"] ?></td></tr>
</table>
<?php } ?>

<?php

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
        <p align=center><a href="<?php echo $filename?>" target="_blank"><img src="<?php echo $filename?>?<?php echo time()?>" width="<?php echo $width?>" height="<?php echo $height?>" border=0></a><br><?php echo $lang["zoom"] . ": " . floor($scale)?>%</p>
        <p><?php echo $lang["deletion_instruction"] ?></p>
        <?php
        }
    #echo "$filename<br>../assets/B1B3A2005B4A09B5A1B6.jpg";
    ?>
    <form enctype="multipart/form-data" method="post">
    <p>
    <input type="hidden" name="MAX_FILE_SIZE" value="200000">
    <input type="hidden" name="extension" value="<?php echo $extension?>">
    <label for="uploader"><?php echo $lang["upload_file"] ?></label>
    <input id="uploader" name="userfile" type="file" size=55>
    </p>
    <?php
    }
else
    {
    ?>
    <form method="post">
    <?php
    #echo $t[7];
    $result=sql_query(str_replace($transfrom,$transto,$t[7]));
    if (count($result)==0) {exit($lang["item_deleted"] . ".</div></body></html>");}
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
            <input type="hidden" name="historyview" value="<?php echo $historyview?>">
            <p><b style="color:red"><?php echo $lang["viewing_version_created_by"] . " " . $history["username"] . " " . $lang["on_date"] ." " . $history["datetime"]?></b></p>
            <?php
            }

        }
        
    foreach ($result as $key=>$value)
        {
        if (substr($value,0,5)=="URL::") {header("Location: " . substr($value,5) . "&parent=" . $parent . "&gparent=" . getval("gparent",""));exit();}

        $value=str_replace("&","&AMP;",$value);

        
        $type=substr($key,0,3);
        $key=str_replace($type . "_","",$key);
        
        #replace value with history value
        if ($historyview!=-1)
            {
            if (array_key_exists($key,$history)) {$value=$history[$key];}
            }
            
        $label=$key;
        if (preg_match('/\[lang_[^\]]*\]/',$label))
            {
            $label=substr($label,6,-1); # Removes "[lang_" and "]".
            $langindex=true;
            }
        else
            {
            $langindex=false;
            if (strpos($label,"<")===false)
                {
                # Formatting of label texts.
                # (Needed for texts not included in the language files.
                # The function lang_or_i18n_get_translated will by its design
                # revert these changes when looking for a $lang index.)
                $label=ucfirst(str_replace("_"," ",$label));
                }
            else    
                {
                $label=str_replace($transfrom,$transto,$label);
                $label=str_replace("[" . $key . "]",$value,$label);
                }
            }
        if ($key=="newredirect") {?><input type=hidden name="newredirect" value="<?php echo $value?>"><?php } else {
        ?>
        <p>
        <?php if (!(is_numeric($key))) { echo  ($langindex==true ? $lang[$label] : lang_or_i18n_get_translated($label,"property-")) . "<br>"; }
        # include plugin
        if (file_exists("plugins/" . $curid . "_" . $key . ".php")) {include ("plugins/" . $curid . "_" . $key . ".php");}
        
        if ($curid=4 && $key=="permissions")
        	{
        	# Special case to include permissions manager
        	?>
       		<button style="width:100%" onClick="document.location.href='permissions.php?ref=<?php echo $ref?>';return false;"><?php echo $lang["launchpermissionsmanager"] ?></button>
       		<?php
        	}
        
        if (!hook("field" . $curid . "_" . $key)) # Hook to optionally replace this field (if hook returns true) or add HTML above field (if hook returns false)
        	{
	        switch ($type)
	            {
	            #-------------------------------------------------------------------------
	            case "txt":
	            #Normal Text
	            ?>
	            <input type="text" style="width:100%" id="<?php echo $key?>" name="<?php echo $key?>" value="<?php echo $value?>">
	            <?php
	            break;
	            #-------------------------------------------------------------------------
	            case "btx":
	            #Big Text
	            ?>
	            <textarea style="width:100%" rows="26" id="<?php echo $key?>" name="<?php echo $key?>"><?php echo $value?></textarea>
	            <?php
	            break;
	            #-------------------------------------------------------------------------
	            case "mtx":
	            #Medium Text
	            ?>
	            <textarea style="width:100%" rows="8" id="<?php echo $key?>" name="<?php echo $key?>"><?php echo $value?></textarea>
	            <?php
	            break;
	            #-------------------------------------------------------------------------
	            case "upl":
	            #In-line file uploader
	            ?>
	            <input type="text" style="width:100%;background-color:#eeeeee" id="<?php echo $key?>" name="<?php echo $key?>" value="<?php echo $value?>">
	            <iframe width="100%" height="70" scrolling="no" src="upload.php?callback=<?php echo $key?>"></iframe>
	            <?php
	            break;
	            #-------------------------------------------------------------------------
	            case "bit":
	            #Yes or no
	            ?>
	            <select id="<?php echo $key?>" name="<?php echo $key?>" style="width:100%;">
	            <option <?php echo ($value==0)?" selected":""?> value="0"><?php echo $lang["no"] ?></option>
	            <option <?php echo ($value==1)?" selected":""?> value="1"><?php echo $lang["yes"] ?></option>
	            </select>
	            <?php
	            break;
	            #-------------------------------------------------------------------------
	            case "drp":
	            case "dr2":
	            case "dr3":
	            #Dropdown
	            #find query
	            $query=explode(";",$tree[$key]);$query=str_replace($transfrom,$transto,$query[6]);
	            $query=str_replace("%search","",$query);
	            $query=str_replace("%recurse",$lastref,$query);
	            $drop=sql_query($query);reset($drop);
	            if ($type!="drp") {$key=$type . "_" . $key;}
	            ?>
	            <select id="<?php echo $key?>" name="<?php echo $key?>" style="width:100%;"><option value=""><?php echo $lang["select"] ?></option>
	            <?php
	            foreach ($drop as $item)
	                {
	                ?>
	                <option <?php echo ($value==$item["ref"])?" selected":""?> value="<?php echo $item["ref"]?>"><?php echo lang_or_i18n_get_translated($item["name"], array("resourcetype-", "requesttype-", "fieldtype-"))?></option>
	                <?php
	                }
	               ?>
	            </select>
	            <?php
	            break;
	            #-------------------------------------------------------------------------
	            case "lbl":
	            # label
	            ?>
	            <?php echo str_replace("%ref", $ref, lang_or_i18n_get_translated($value, "information-"))?>
	            <?php
	            break;
	            }
	        }
        }
        ?>
        </p>
        <?php
        }
    }
?>
<table width="100%" cellpadding=0 cellspacing=0 style="margin-top:5px;">
<tr><td align=left>
<!--
<?php if ((substr($t[7],0,6)!="upload") && ($t[8]!=""))  { ?>
Version
<input type=submit style="width:30px;" name="history" value="&lt;" <?php if (!((($historyview>0) || ($historyview==-1)) && (file_exists($hist_filename)))) {?>disabled="true"<?php }?>>

<input type=submit style="width:30px;" name="history" value="&gt;" <?php if ($historyview==-1) {?>disabled="true"<?php } ?>>
<?php } ?>
-->
</td>
<td align=right>
<?php if (isset($t[9])&&$t[9]!="") {?><input type="submit" name="delete" value="<?php echo $lang["action-delete"] ?>" style="width:100px;" onclick="return confirm('<?php echo $lang["confirm-deletion"] ?>');"><?php } ?>
<?php if (isset($t[8])&&($t[8]!="") || (substr($t[7],0,6)=="upload")) {?><input type="submit" name="submit" value="<?php echo ($historyview==-1)?$lang["save"]:$lang["revert"] ?>" style="width:100px;"><?php } ?>
</td></tr>
</table>
</form>

</div>
</body>
</html>
