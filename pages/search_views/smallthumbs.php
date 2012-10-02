
<div class="ResourcePanelShellSmall" <?php if ($display_user_rating_stars && $k==""){?> <?php } ?>id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanelSmall">	
		<?php if (!hook("renderimagesmallthumb")){;?>
		<?php $access=get_resource_access($result[$n]);
		$use_watermark=check_use_watermark();
		
		# Work out the preview image path
		$col_url=get_resource_path($ref,false,"col",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"]);
		if (isset($result[$n]["col_url"])) {$col_url=$result[$n]["col_url"];} # If col_url set in data, use instead, e.g. by manipulation of data via process_search_results hook
		
		?>
		<table border="0" class="ResourceAlignSmall">
		<?php hook("resourcetop")?>
		<tr><td>
		<a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img  src="<?php echo $col_url ?>" class="ImageBorder"
		<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
		 /><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],true) ?>" 
		<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
		/><?php } ?></a>
		</td>
		</tr></table>				
		<?php } /* end Renderimagesmallthumb */?>
        <?php if ($display_user_rating_stars && $k==""){ ?>
		<?php if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}?>
		
		<div  class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">&nbsp;<?php
	    for ($z=1;$z<=5;$z++)
			{
			?><a href="#" onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"><span id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></a><?php
			}
		?>
		</div>
		<?php } ?>
		<?php if (!hook("replaceicons")) { ?>
		<?php hook("icons");?>
		<?php } //end hook replaceicons ?>

		<?php
		# smallthumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			#value filter plugin -tbd	
			$value=$result[$n]['field'.$df[$x]['ref']];
			$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
			if ($df[$x]['value_filter']!=""){
				eval($df[$x]['value_filter']);
			}
			else if (file_exists($plugin)) {include $plugin;}
			# swap title fields if necessary
			if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field)){
				if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type)){
					$value=$result[$n]['field'.$metadata_template_title_field];
					}
				}
			?>		
			<?php 
			// extended css behavior 
			if ( in_array($df[$x]['ref'],$small_thumbs_display_extended_fields) &&
			( (isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) ){ ?>
			<?php if (!hook("replaceresourcepanelinfosmall")){?>
			<div class="ResourcePanelInfo"><div class="extended">
			<?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link
			echo format_display_field($value);			
			if ($show_extension_in_search) { ?><?php echo " " . str_replace_formatted_placeholder("%extension", $result[$n]["file_extension"], $lang["fileextension"])?><?php } ?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div></div>
			<?php } /* end hook replaceresourcepanelinfolarge */?>
			<?php 

			// normal behavior
			} else if  ( (isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) {?> 
			<div class="ResourcePanelInfo"><?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),28),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed'])?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div><div class="clearer"></div>
			<?php } ?>
			<?php
			}
		?>
		
		
		<?php hook("smallsearchfreeicon");?>
		<div class="ResourcePanelIcons"><?php if ($display_resource_id_in_thumbnail && $ref>0) { echo $ref; } else { ?>&nbsp;<?php } ?></div>	
		<?php hook("smallsearchicon");?>
		<?php if (!hook("replaceresourcetoolssmall")){?>
		<span class="IconPreview">
		<a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"  title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
		
		<?php if (!checkperm("b") && $k=="") { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="") { ?>
		
		<?php if ($search=="!collection".$usercollection){?>
		<span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
		<?php } // end hook replaceresourcetoolssmall ?>
<? hook("smallthumbicon"); ?>	
<div class="clearer"></div></div>	
<div class="PanelShadow"></div></div>
		 
		<?php
		
		
		
		
		