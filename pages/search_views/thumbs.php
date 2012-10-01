<?php

			# Work out image to use.
			$access=get_resource_access($result[$n]);
			$use_watermark=check_use_watermark();
			$thm_url=get_resource_path($ref,false,"thm",false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"]);
			
			if (isset($result[$n]["thm_url"])) {$thm_url=$result[$n]["thm_url"];} # Option to override thumbnail image in results, e.g. by plugin using process_Search_results hook above
			?>
		 
<?php if (!hook("renderresultthumb")) { ?>

<!--Resource Panel-->
	<div class="ResourcePanelShell" id="ResourceShell<?php echo $ref?>">
	<div class="ResourcePanel">
	<?php hook ("resourcethumbtop");?>
<?php if (!hook("renderimagethumb")) { ?>			
	<table border="0" class="ResourceAlign<?php if(!hook("replaceresourcetypeicon")){?><?php if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideoLarge<?php } ?><?php } //end hook replaceresoucetypeicon?>">
	<?php hook("resourcetop")?>
	<tr><td>
	<a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"<?php } ?>><?php if ($result[$n]["has_image"]==1) { ?><img <?php if ($result[$n]["thumb_width"]!="" && $result[$n]["thumb_width"]!=0 && $result[$n]["thumb_height"]!="") { ?> width="<?php echo $result[$n]["thumb_width"]?>" height="<?php echo $result[$n]["thumb_height"]?>" <?php } ?> src="<?php echo $thm_url ?>" class="ImageBorder"
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	 /><?php } else { ?><img border=0 src="../gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],false) ?>" 
	<?php if ($infobox) { ?>onmouseover="InfoBoxSetResource(<?php echo $ref?>);" onmouseout="InfoBoxSetResource(0);"<?php } ?>
	/><?php } ?></a>
		</td>
		</tr></table>
<?php } ?> <!-- END HOOK Renderimagethumb-->	
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
<?php if (!hook("rendertitlethumb")) { ?>	

<?php } ?> <!-- END HOOK Rendertitlethumb -->			
		
		<?php
		# thumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			#value filter plugin -tbd	
			$value=@$result[$n]['field'.$df[$x]['ref']];
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
			if ( in_array($df[$x]['ref'],$thumbs_display_extended_fields) &&
			( (isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) ){ ?>
			<?php if (!hook("replaceresourcepanelinfo")){?>
			<div class="ResourcePanelInfo"><div class="extended">
			<?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link
			echo format_display_field($value);
			?><?php if ($show_extension_in_search) { ?><?php echo " " . str_replace_formatted_placeholder("%extension", $result[$n]["file_extension"], $lang["fileextension"])?><?php } ?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div></div>
			<?php } /* end hook replaceresourcepanelinfo */?>
			<?php 

			// normal behavior
			} else if  ( (isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) {?> 
			<div class="ResourcePanelInfo"><?php if ($x==0){ // add link if necessary ?><a href="<?php echo $url?>"  onClick="return CentralSpaceLoad(this,true);" <?php if (!$infobox) { ?>title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"<?php } //end if infobox ?>><?php } //end link?><?php echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),$search_results_title_trim),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed'])?><?php if ($x==0){ // add link if necessary ?></a><?php } //end link?>&nbsp;</div><div class="clearer"></div>
			<?php } ?>
			<?php
			}
		?>
		
		<div class="ResourcePanelIcons"><?php if ($display_resource_id_in_thumbnail && $ref>0) { echo $ref; } else { ?>&nbsp;<?php } ?></div>	

		<?php if (!hook("replaceresourcetools")){?>
		<?php if (!hook("replacefullscreenpreviewicon")){?>
		<span class="IconPreview"><a href="preview.php?from=search&ref=<?php echo $ref?>&ext=<?php echo $result[$n]["preview_extension"]?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"  onClick="return CentralSpaceLoad(this,true);" title="<?php echo $lang["fullscreenpreview"]?>"><img src="../gfx/interface/sp.gif" alt="<?php echo $lang["fullscreenpreview"]?>" width="22" height="12" /></a></span>
		<?php } /* end hook replacefullscreenpreviewicon */?>
		
		<?php if(!hook("iconcollect")){?>
		<?php if (!checkperm("b") && $k=="" && !$use_checkboxes_for_selection) { ?>
		<span class="IconCollect"><?php echo add_to_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12"/></a></span>
		<?php } ?>
		<?php } # end hook iconcollect ?>

		<?php if (!checkperm("b") && substr($search,0,11)=="!collection" && $k=="" && !$use_checkboxes_for_selection) { ?>
		<?php if ($search=="!collection".$usercollection){?><span class="IconCollectOut"><?php echo remove_from_collection_link($ref,$search)?><img src="../gfx/interface/sp.gif" alt="" width="22" height="12" /></a></span>
		<?php } ?>
		<?php } ?>
		
		<?php if ($allow_share && $k=="") { ?><span class="IconEmail"><a href="resource_email.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&k=<?php echo $k?>"  onClick="return CentralSpaceLoad(this,true);" title="<?php echo $lang["emailresource"]?>"><img src="../gfx/interface/sp.gif" alt="" width="16" height="12" /></a></span><?php } ?>
		<?php if (isset($result[$n][$rating]) && 
		$result[$n][$rating]>0) { ?><div class="IconStar"></div><?php } ?>
		<?php if ($k==""){?><?php if ($collection_reorder_caption && $allow_reorder) { ?>
		<span class="IconComment"><a href="collection_comment.php?ref=<?php echo $ref?>&collection=<?php echo substr($search,11)?>"  onClick="return CentralSpaceLoad(this,true);" title="<?php echo $lang["addorviewcomments"]?>"><img src="../gfx/interface/sp.gif" alt="" width="14" height="12" /></a></span>			
		<?php if ($order_by=="relevance"){?><div class="IconReorder" onmousedown="InfoBoxWaiting=false;"> </div><?php } ?><?php } ?>
		<?php } 
		hook("largesearchicon");?><div class="clearer"></div>
		<?php if(!hook("thumbscheckboxes")){?>
		<?php if ($use_checkboxes_for_selection){?><input type="checkbox" id="check<?php echo $ref?>" class="checkselect" <?php if (in_array($ref,$collectionresources)){ ?>checked<?php } ?> onclick="if (jQuery('#check<?php echo $ref?>').attr('checked')=='checked'){ <?php if ($frameless_collections){?>AddResourceToCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?add=<?php echo $ref?>';<?php }?> } else if (jQuery('#check<?php echo $ref?>').attr('checked')!='checked'){<?php if ($frameless_collections){?>RemoveResourceFromCollection(<?php echo $ref?>);<?php }else {?>parent.collections.location.href='collections.php?remove=<?php echo $ref?>';<?php }?> <?php if ($frameless_collections && isset($collection)){?>document.location.href='?search=<?php echo urlencode($search)?>&order_by=<?php echo urlencode($order_by)?>&sort=<?php echo $revsort?>&archive=<?php echo $archive?>&offset=<?php echo $offset?>';<?php } ?> }"><?php } ?>
		<?php } # end hook thumbscheckboxes?>
		<?php } // end hook replaceresourcetools ?>

	</div>
<div class="PanelShadow"></div>
</div>
<?php if ($allow_reorder && $display!="list") { 
# Javascript drag/drop enabling.
?>
<script type="text/javascript">
new Draggable('ResourceShell<?php echo $ref?>',{handle: 'IconReorder', revert: true});
Droppables.add('ResourceShell<?php echo $ref?>',{accept: 'ResourcePanelShell', onDrop: function(element) {ReorderResources(element.id,<?php echo $ref?>);}, hoverclass: 'ReorderHover'});
</script>
<?php } ?> 
<?php } ?>

		<?php 
		