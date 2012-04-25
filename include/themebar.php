<?php
include_once("collections_functions.php");

function DisplayThemeBar($theme1)
        {
        global $lang,$flag_new_themes,$contact_sheet,$theme_images,$allow_share,$n,$baseurl;

        # Work out theme name
        $themename=$theme1;
        $theme_display=getval("theme_$n","off");
       
        $themes=get_themes(array($theme1));
        if (count($themes)>0)
                {
                ?>

<div
onclick="
var theme_display=get_cookie('theme_<?php echo $n?>');
if (theme_display=='off'){var toggle_theme_display='on';} else { var toggle_theme_display='off';}
SetCookie('theme_<?php echo $n?>',toggle_theme_display,1000);
Effect.toggle($('<?php echo str_replace("\"","",$themename)?>'),'blind',{ duration: 0.2 });
return false;">                
               
	<a href='#'><b><?php echo stripslashes(i18n_get_translated(str_replace("*","",$themename)))?></b></a></div>
               
<div id="<?php echo str_replace("\"","",$themename)?>" style="display:<?php if ($theme_display == 'off'){echo 'none';} else {echo '';}?>" >
                <?php
                for ($m=0;$m<count($themes);$m++)
                        { ?><br>
                        &nbsp;&nbsp;&nbsp;<a href="<?php echo $baseurl?>/pages/search.php?search=!collection<?php echo $themes[$m]["ref"]?>&bc_from=themes"  title="<?php echo $lang["collectionviewhover"]?>"><?php echo htmlspecialchars(i18n_get_translated($themes[$m]["name"]))?></a>
                        <?php
                        }
                ?><br><br></div><?php
                }
        }

if (!hook("themebarreplace")){?>

<script type="text/javascript">

function get_cookie ( cookie_name )
{
  var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
  if ( results )
    return ( unescape ( results[2] ) );
  else
    return null;
}

</script>

<div id="ThemeBox">

<div id="ThemeBoxPanel">

        <div class="SearchSpace">

<h2><?php echo $lang["themes"] ?></h2>
<?php

hook("themebartoptoolbar");

# Display all themes
// only works for collections on first level
$headers=get_theme_headers();

for ($n=0;$n<count($headers);$n++)
        {
        DisplayThemeBar($headers[$n]);
        }

hook("themebarbottomtoolbar");



# ------- Smart Themes -------------
   if(!function_exists("get_smart_theme_headers")){include("collections_functions.php");}
        $headers=get_smart_theme_headers();
        for ($n=0;$n<count($headers);$n++)
                {
                if ((checkperm("f*") || checkperm("f" . $headers[$n]["ref"]))
                && !checkperm("f-" . $headers[$n]["ref"]))
                        {
                        $header_name="";
                        $header_name=$headers[$n]["smart_theme_name"];
                        $smart_theme_display="";
                        $smart_theme_display=getval("smart_theme_$n","off");
                        #echo $smart_theme_display;
                        ?>
<div
onclick="
var smart_theme_display=get_cookie('smart_theme_<?php echo $n?>');
if (smart_theme_display=='off'){var toggle_smart_theme_display='on';} else { var toggle_smart_theme_display='off';}
SetCookie('smart_theme_<?php echo $n?>',toggle_smart_theme_display,1000);
Effect.toggle($('<?php echo $header_name?>'),'blind',{ duration: 0.2 });
return false;">

                        <?php echo "<a href='#'><B>".str_replace("*","",i18n_get_translated($headers[$n]["smart_theme_name"]))."</B></a><br>"?></div>
               
<div id="<?php echo $header_name?>" style="display:<?php if ($smart_theme_display == 'off'){echo 'none';} else {echo '';}?>" >
                        <?php
                        $themes=get_smart_themes($headers[$n]["ref"],0,true);

                        for ($m=0;$m<count($themes);$m++)
                                {
                                $s=$headers[$n]["name"] . ":" . $themes[$m]["name"];
               
                                hook("themebartitlesubstitute");
                               
                                # Indent this item?                            
                                $indent=str_pad("",$themes[$m]["indent"]*5," ") . ($themes[$m]["indent"]==0?"":"&#746;") . "&nbsp;";
                                $indent=str_replace(" ","&nbsp;",$indent);

                                ?>
                                <br>

                                <?php echo $indent?><a href="<?php echo $baseurl?>/pages/search.php?search=<?php echo urlencode($s)?>"><?php echo i18n_get_translated($themes[$m]["name"])?></a>
                                <?php
                                }
                        ?><br><br>
</div>

                        <?php
                        }
                }






} // end hook themebarreplace

?>
