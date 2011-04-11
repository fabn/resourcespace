<?php

# Perform the search
if (!isset($collections)){
    $collections=search_public_collections($search,"theme","ASC",!$search_includes_themes,!$search_includes_public_collections,false);
}

if (substr($search,0,11)!="!collection") {
    
for ($n=0;$n<count($collections);$n++)
	{
	$pub_url="search.php?search=" . urlencode("!collection" . $collections[$n]["ref"]);
	if ($display=="thumbs")
		{
		?>
		<div class="ResourcePanelShell" id="ResourceShell">
		<div class="ResourcePanel">
	
		<table  border="0" class="ResourceAlign"><tr><td>
		
		<div style="position: relative;height:140px;">
		<a href="<?php echo $pub_url?>" title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($collections[$n]["name"])))?>">
		
		<?php 
		$resources=do_search("!collection".$collections[$n]['ref']);
		$images=0;
		for ($m=0;$m<count($resources) && $images<=4;$m++)
            {
            $border=true;    
			$ref=$resources[$m]['ref'];
            $previewpath=get_resource_path($ref, true, "col", false, "jpg", -1, 1, false);
            if (file_exists($previewpath)){
                $previewpath=get_resource_path($ref,false,"col",false,"jpg",-1,1,false,$resources[$m]["file_modified"]);
            }
            else {
                $previewpath="../gfx/".get_nopreview_icon($resources[$m]["resource_type"],$resources[$m]["file_extension"],"col");$border=false;
            }
            $images++;
            $space=3+($images-1)*18;
            ?>
            <img style="position: absolute; top:<?php echo $space ?>px;left:<?php echo $space ?>px" src="<?php echo $previewpath?>" <?php if ($border){?>class="ImageBorder"<?php } ?>>
            <?php				
			}
		?>
		</a>
		</div>
		</td>
		</tr></table>
        <?php // for spacing
        if ($display_user_rating_stars && $k==""){ ?>
            <div  class="RatingStars">&nbsp;</div>
        <?php } ?>
        <?php hook("icons"); //for spacing ?>
        <?php //add spacing for display fields to even out the box size
        for ($x=0;$x<count($df);$x++){
            ?><div class="ResourcePanelInfo">
            <?php if (in_array($df[$x]['ref'],$thumbs_display_extended_fields)){
                ?><div class="extended">
            <?php } ?>
            <?php if ($x==count($df)-1){?><a href="<?php echo $pub_url?>" title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($collections[$n]["name"])))?>"><?php echo highlightkeywords(htmlspecialchars(tidy_trim(i18n_get_translated($collections[$n]["name"]),32)),$search)?></a><?php } ?>&nbsp;<?php if (in_array($df[$x]['ref'],$thumbs_display_extended_fields)){ ?></div>
            <?php }
        ?></div><?php } ?>

        <div class="ResourcePanelCountry" style="float:right;">&gt;&nbsp;<a target="collections" href="collections.php?collection=<?php echo $collections[$n]["ref"]?>"><?php echo $lang["action-select"]?></a>&nbsp;&nbsp;&nbsp;&gt;&nbsp;<a href="<?php echo $pub_url?>"><?php echo $lang["viewall"]?></a></div>		

		<div class="clearer"></div>
		</div>
		<div class="PanelShadow"></div>
		</div>
	<?php } 
	
	
	
		if ($display=="xlthumbs")
		{
		?>
		<div class="ResourcePanelShellLarge" id="ResourceShell">
		<div class="ResourcePanelLarge">
	
		<table  border="0" class="ResourceAlignLarge"><tr><td>
		
		<div style="position: relative;height:330px;">
		<a href="<?php echo $pub_url?>" title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($collections[$n]["name"])))?>">

		<?php 
		$resources=do_search("!collection".$collections[$n]['ref']);
		$images=0;
		for ($m=0;$m<count($resources) && $images<=4;$m++)
            {
            $border=true;    
            $ref=$resources[$m]['ref'];
            $previewpath=get_resource_path($ref, true, "thm", false, "jpg", -1, 1, false);
            if (file_exists($previewpath)){
                $previewpath=get_resource_path($ref,false,"thm",false,"jpg",-1,1,false,$resources[$m]["file_modified"]);
            }
            else {
                $previewpath="../gfx/".get_nopreview_icon($resources[$m]["resource_type"],$resources[$m]["file_extension"],"");$border=false;
            }
            $images++;
            $space=3+($images-1)*45;
            ?>
            <img style="position: absolute; top:<?php echo $space ?>px;left:<?php echo $space ?>px" src="<?php echo $previewpath?>" <?php if ($border){?>class="ImageBorder"<?php } ?>>
            <?php				
			}
		?>
		</a>
		</div>
		</td>
		</tr></table>
        <?php // for spacing
        if ($display_user_rating_stars && $k==""){ ?>
            <div  class="RatingStars">&nbsp;</div>
        <?php } ?>
        <?php hook("icons"); //for spacing ?>
        <?php //add spacing for display fields to even out the box size
        for ($x=0;$x<count($df);$x++){
            ?><div class="ResourcePanelInfo">
            <?php if (in_array($df[$x]['ref'],$xl_thumbs_display_extended_fields)){
                ?><div class="extended">
            <?php } ?>
            <?php if ($x==count($df)-1){?><a href="<?php echo $pub_url?>" title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($collections[$n]["name"])))?>"><?php echo highlightkeywords(htmlspecialchars(tidy_trim(i18n_get_translated($collections[$n]["name"]),32)),$search)?></a><?php } ?>&nbsp;<?php if (in_array($df[$x]['ref'],$xl_thumbs_display_extended_fields)){ ?></div>
            <?php }
        ?></div><?php } ?>
		<div class="ResourcePanelCountry" style="float:right;">&gt;&nbsp;<a target="collections" href="collections.php?collection=<?php echo $collections[$n]["ref"]?>"><?php echo $lang["action-select"]?></a>&nbsp;&nbsp;&nbsp;&gt;&nbsp;<a href="<?php echo $pub_url?>"><?php echo $lang["viewall"]?></a></div>		

		<div class="clearer"></div>
		</div>
		<div class="PanelShadow"></div>
		</div>
	<?php } 
	
	
	
	
	if ($display=="smallthumbs")
		{
		?>
		<div class="ResourcePanelShellSmall" id="ResourceShell">
		<div class="ResourcePanelSmall">
	
		<table  border="0" class="ResourceAlignSmall"><tr><td>
		
		<div style="position: relative;height:70px;">
		<a href="<?php echo $pub_url?>" title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($collections[$n]["name"])))?>">



        
		<?php 
		$resources=do_search("!collection".$collections[$n]['ref']);
		$images=0;

		for ($m=0;$m<count($resources) && $images<=4;$m++)
            {
            $border=true;    
            $ref=$resources[$m]['ref'];
            $previewpath=get_resource_path($ref, true, "col", false, "jpg", -1, 1, false);
            if (file_exists($previewpath)){
                $previewpath=get_resource_path($ref,false,"col",false,"jpg",-1,1,false,$resources[$m]["file_modified"]);
            }
            else {
                $previewpath="../gfx/".get_nopreview_icon($resources[$m]["resource_type"],$resources[$m]["file_extension"],"col");$border=false;
            }
            $images++;
            $space=3+($images-1)*9;
            if (list($sw,$sh) = @getimagesize($previewpath)){
            ?>
            <img width="<?php echo floor($sw/2)?>" height="<?php echo floor($sh/2)?>" style="position: absolute; top:<?php echo $space ?>px;left:<?php echo $space ?>px" src="<?php echo $previewpath?>" <?php if ($border){?>class="ImageBorder"<?php } ?>>
            <?php
            }
			}
		?>
		</a>
		</div>
		</td>
		</tr></table>	
        <?php // for spacing
        if ($display_user_rating_stars && $k==""){ ?><div  class="RatingStars">&nbsp;&nbsp;</div>
        <?php } ?><?php //add spacing for display fields to even out the box size
        for ($x=0;$x<count($df);$x++){
            ?><div class="ResourcePanelInfo">
            <?php if (in_array($df[$x]['ref'],$small_thumbs_display_extended_fields)){
                ?><div class="extended"><?php } ?><?php if ($x==count($df)-1){?><a href="<?php echo $pub_url?>" title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($collections[$n]["name"])))?>"><?php echo highlightkeywords(htmlspecialchars(tidy_trim(i18n_get_translated($collections[$n]["name"]),32)),$search)?></a><?php } ?>&nbsp;<?php if (in_array($df[$x]['ref'],$small_thumbs_display_extended_fields)){ ?></div>
            <?php }
        ?></div><?php } ?><div class="ResourcePanelInfo" style="font-size:9px;">&gt;&nbsp;<a target="collections" href="collections.php?collection=<?php echo $collections[$n]["ref"]?>"><?php echo $lang["action-select"]?></a>&nbsp;&nbsp;&gt;&nbsp;<a href="<?php echo $pub_url?>"><?php echo $lang["viewall"]?></a></div>		

		<div class="clearer"></div>
		</div>
		<div class="PanelShadow"></div>
		</div>
	<?php } 
	
	if ($display=="list")
		{
		?>
		<tr <?php hook("collectionlistrowstyle");?>>
		<td nowrap><div class="ListTitle"><a href="<?php echo $pub_url?>"><?php echo $lang["collection"] . ": " . highlightkeywords(tidy_trim(i18n_get_translated($collections[$n]["name"]),45),$search)?></a></div></td>
		<?php 

		for ($x=0;$x<count($df)-1;$x++){
			?><td>&nbsp;</td><?php
			}
				
		?>
		<td>&nbsp;</td>
		<?php if ($id_column){?><td>&nbsp;</td><?php } ?>
		<?php if (!isset($collections[$n]['savedsearch'])||(isset($collections[$n]['savedsearch'])&&$collections[$n]['savedsearch']==null)){ $collection_tag=$lang['collection'];} else {$collection_tag=$lang['smartcollection'];}?>
		<?php if ($resource_type_column){?><td><?php echo $collection_tag?></td><?php } ?>
		<?php if ($date_column){?><td><?php echo nicedate($collections[$n]["created"],false,true)?></td><?php } ?>
        <?php hook("addlistviewcolumnpublic");?>
		<td><div class="ListTools"><a target="collections" href="collections.php?collection=<?php echo $collections[$n]["ref"]?>">&gt;&nbsp;<?php echo $lang["action-select"]?></a>&nbsp;&nbsp;<a href="<?php echo $pub_url?>">&gt;&nbsp;<?php echo $lang["viewall"]?></a></div></td>
		</tr>
	<?php } ?>		
	
<?php } ?>
<?php } /* end if not a collection search */ ?>
